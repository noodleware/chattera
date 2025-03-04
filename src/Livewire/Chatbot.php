<?php

namespace Noodleware\Chattera\Livewire;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Str;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Noodleware\Chattera\Models\Chat;

#[Lazy]
class Chatbot extends Component
{
    #[Locked]
    public ?Chat $chat = null;

    #[Locked]
    public ?int $newMessageId = null;

    #[Locked]
    public bool $started = false;

    #[Locked]
    public bool $waiting = false;

    public bool $show = false;

    public string $email = '';

    public string $message = '';

    public function mount(): void
    {
        // Retrieve a chat session.
        if ($chat = Chat::loadForSession()) {
            $this->started = true;

            $this->chat = $chat;
        }
    }

    public function start(): void
    {
        if (config('chattera.requires_email')) {
            $this->validate([
                'email' => 'required|email',
            ]);
        }

        // Retrieve or create a chat session for the current user.
        $this->chat = Chat::createForSession($this->email);

        // Send a greeting message
        $this->newMessageId = $this->chat->sendGreetingMessage()->id;

        // Mark chatbot as started
        $this->started = true;
    }

    public function send(): void
    {
        if ($this->containsInvalidContent()) {
            $this->reset('message');

            return;
        }

        $message = $this->chat->handleUserMessage($this->message);

        if ($message) {
            $this->newMessageId = $message->id;
            $this->waiting = true;
            // Dispatch an event to notify the frontend that a new message has been added.
            $this->dispatch('messageAdded');
        }

        $this->reset('message');
    }

    protected function containsInvalidContent(): bool
    {
        // Remove spaces and normalize case for a robust check.
        $normalizedMessage = strtolower(str_replace(' ', '', $this->message));

        return Str::contains($normalizedMessage, ['actlike']);
    }

    /**
     * Handles generating and sending the bot's response.
     *
     * @throws ConnectionException
     */
    public function getResponse(): void
    {
        try {
            $lastMessage = $this->chat->messages()->orderByDesc('created_at')->first();

            if ($lastMessage && $lastMessage->isFlagged()) {
                $this->chat->sendModerationResponse();
                $this->waiting = false;

                return;
            }

            $this->processMessagesForResponse();
        } catch (ConnectionException $e) {
            // Log the error or notify the user appropriately.
            $this->waiting = false;
            $this->dispatch('connectionError', ['message' => 'There was a connection error. Please try again.']);
        }
    }

    /**
     * @throws ConnectionException
     */
    protected function processMessagesForResponse(): void
    {
        $message = $this->chat->generateResponse();
        $this->newMessageId = $message->id;
        $this->waiting = false;
    }

    public function render()
    {
        $messages = $this->chat ? $this->chat->getFormattedMessages($this->newMessageId) : collect();
        // Reset the newMessageId so it's only used once.
        $this->newMessageId = null;

        return view('chattera::livewire.chatbot', [
            'messages' => $messages,
        ]);
    }
}
