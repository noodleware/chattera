<?php

namespace Noodleware\Chattera\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Noodleware\Chattera\Services\MessageSanitizerService;
use Noodleware\Chattera\Services\OpenAIService;

/**
 * Chat model representing a chat session.
 *
 * @property mixed $messages
 * @property mixed $rating
 * @property mixed $improvement
 *
 * @method static create(array $array)
 * @method static firstOrCreate(array $array)
 * @method static where(string $string, string $getSessionToken)
 */
class Chat extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Define the relationship to ChatMessage.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Retrieve chat session based on the session token.
     */
    public static function loadForSession(): ?self
    {
        return self::where('session_token', self::getSessionToken())->first();
    }

    /**
     * Create a chat session based on the session token.
     */
    public static function createForSession($email = null): self
    {
        return self::create([
            'session_token' => self::getSessionToken(),
            'email' => $email,
        ]);
    }

    /**
     * Generate or retrieve a session token.
     */
    public static function getSessionToken(): string
    {
        if (! Session::has('chatbot_session_token')) {
            Session::put('chatbot_session_token', Str::uuid()->toString());
        }

        return Session::get('chatbot_session_token');
    }

    /**
     * Create and save a new message for this chat.
     */
    public function addMessage(string $role, string $content, int $tokens = 0): ChatMessage
    {
        return $this->messages()->create([
            'role' => $role,
            'content' => $content,
            'total_tokens' => $tokens,
        ]);
    }

    /**
     * Send a greeting message from the assistant.
     */
    public function sendGreetingMessage(): ChatMessage
    {
        return $this->addMessage('assistant', config('chattera.ui.messages.greeting'));
    }

    /**
     * Send a moderation response if the user message violates policies.
     */
    public function sendModerationResponse(): ChatMessage
    {
        return $this->addMessage('assistant', config('chattera.ui.messages.moderation'));
    }

    /**
     * Generate a response using the OpenAI service.
     *
     * @throws ConnectionException
     */
    public function generateResponse(): ChatMessage
    {
        // Retrieve messages in descending order (most recent first).
        $messages = $this->messages()->orderByDesc('created_at')->get();

        // Get rules from config and create a single string.
        $rules = implode(". \n", config('chattera.rules', []));

        // Get relevant context from the latest 3 user messages.
        $context = ChatContext::getRelevantContext(
            $messages->where('role', 'user')->take(3)->implode('content', '. \n')
        );

        // Build the payload: include system message with rules & context,
        // then add the last 6 messages (reversed to chronological order).
        $payload = collect([
            ['role' => 'system', 'content' => "Rules: \n$rules. \n \nContext: \n$context."],
        ])->merge(
            $messages->take(6)->reverse()->map(function ($message) {
                return [
                    'role' => $message->role,
                    'content' => $message->content,
                ];
            })
        );

        // Call the OpenAI service to generate a response.
        $data = app(OpenAIService::class)->chat($payload->toArray());

        // Save and return the assistant's generated message.
        return $this->addMessage(
            'assistant',
            trim($data['choices'][0]['message']['content']),
            $data['usage']['total_tokens']
        );
    }

    /**
     * Process a user message: rate limit, validate, sanitize, and save it.
     */
    public function handleUserMessage(string $message): ?ChatMessage
    {
        // Rate limiting: allow up to 3 attempts.
        if (RateLimiter::tooManyAttempts('send-message:'.$this->id, 3)) {
            return null;
        }

        // Validate that the message is not empty.
        if (empty(trim($message))) {
            return null;
        }

        // Sanitize the message content.
        $cleanMessage = MessageSanitizerService::sanitize($message);

        // Save and return the new user message.
        return $this->addMessage('user', $cleanMessage);
    }

    /**
     * Format messages for presentation, marking the new assistant message if applicable.
     */
    public function getFormattedMessages(?int $newMessageId = null): Collection
    {
        return $this->messages->map(function ($message) use ($newMessageId) {
            $message->is_new = ($message->id === $newMessageId && $message->role === 'assistant');

            return $message;
        });
    }
}
