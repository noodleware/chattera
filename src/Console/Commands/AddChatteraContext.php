<?php

namespace Noodleware\Chattera\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Noodleware\Chattera\Models\ChatContext;
use Noodleware\Chattera\Services\OpenAIService;

class AddChatteraContext extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chattera:add-context {context}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a new chatbot context entry based on provided text';

    /**
     * The OpenAI service instance.
     */
    protected OpenAIService $openAIService;

    /**
     * Create a new command instance.
     */
    public function __construct(OpenAIService $openAIService)
    {
        parent::__construct();
        $this->openAIService = $openAIService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $context = $this->argument('context');

        try {
            $embeddingsResponse = $this->openAIService->embeddings($context);

            // Validate the response structure before proceeding.
            if (! isset($embeddingsResponse['data'][0]['embedding'])) {
                $this->error('Embedding data not found in the response.');

                return;
            }

            ChatContext::create([
                'value' => $context,
                'embedding' => $embeddingsResponse['data'][0]['embedding'],
                'tokens' => $embeddingsResponse['usage']['prompt_tokens'] ?? 0,
            ]);

            // Clear the cached context data.
            Cache::forget('chat-context');

            $this->info('Chat context added successfully.');
        } catch (ConnectionException $e) {
            $this->error('Failed to connect to the OpenAI API: '.$e->getMessage());
        } catch (Exception $e) {
            $this->error('An error occurred: '.$e->getMessage());
        }
    }

    /**
     * Enhance the given text by splitting it into self-contained statements.
     *
     * @throws ConnectionException|RequestException
     */
    private function enhance(string $text): array
    {
        $rules = implode(". \n", [
            'You are a text-organizing assistant',
            'You will be given large bodies of text about various topics (for example, instructions for using an account)',
            'Your task is to split the text into multiple statements in such a way that each statement is entirely self-contained and does not rely on the previous statements for context',
            'Maintain important keywords (e.g., "Legacy Secure account," "password," "email address," etc.) in each statement when relevant so the meaning is clear on its own',
            'You may include multiple sentences in one statement if needed; do not split a sentence in a way that loses context',
            'Be detailed and preserve logical completeness in each statement so it can be understood in isolation',
            'Return the statements as a raw JSON array (for example ["statement 1", "statement 2"]) with no code blocks or markdown formatting',
        ]);

        $data = $this->openAIService->chat([
            [
                'role' => 'system',
                'content' => "Rules: \n{$rules}.",
            ],
            [
                'role' => 'user',
                'content' => $text,
            ],
        ]);

        return json_decode($data['choices'][0]['message']['content'], true);
    }
}
