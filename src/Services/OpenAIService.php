<?php

namespace Noodleware\Chattera\Services;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    public function __construct(
        protected string $url,
        protected string $token,
    ) {
        //
    }

    /**
     * Generic method to send a POST request.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    private function sendRequest(string $endpoint, array $payload, int $retry, int $timeout): array
    {
        try {
            $response = Http::withToken($this->token)
                ->retry($retry)
                ->timeout($timeout)
                ->post($this->url.$endpoint, $payload);

            // Automatically throw an exception for non-successful responses.
            $response->throw();

            return $response->json();
        } catch (ConnectionException $e) {
            Log::error('OpenAIService request failed (Connection): '.$e->getMessage(), [
                'endpoint' => $endpoint,
                'payload' => $payload,
            ]);
            throw $e;
        } catch (Exception $e) {
            Log::error('OpenAIService request failed (General): '.$e->getMessage(), [
                'endpoint' => $endpoint,
                'payload' => $payload,
            ]);
            throw $e;
        }
    }

    /**
     * Send a chat request to generate a response.
     *
     * @throws ConnectionException|RequestException
     */
    public function chat(array $messages): array
    {
        // Retrieve OpenAI configuration from the config file.
        $openaiConfig = config('chattera.openai');

        $payload = [
            'model' => $openaiConfig['chat_model'] ?? 'gpt-4o-mini',
            'messages' => $messages,
            'temperature' => $openaiConfig['temperature'] ?? 0.7,
            'frequency_penalty' => $openaiConfig['frequency_penalty'] ?? 0.5,
        ];

        return $this->sendRequest('chat/completions', $payload, 2, 120);
    }

    /**
     * Send a moderation request to check content.
     *
     * @throws ConnectionException|RequestException
     */
    public function moderation(string $input): array
    {
        $payload = ['input' => $input];

        return $this->sendRequest('moderations', $payload, 3, 20);
    }

    /**
     * Send an embeddings request.
     *
     * @throws ConnectionException|RequestException
     */
    public function embeddings(string $input): array
    {
        // Retrieve OpenAI configuration for embeddings.
        $openaiConfig = config('chattera.openai');

        $payload = [
            'model' => $openaiConfig['embedding_model'] ?? 'text-embedding-3-small',
            'input' => $input,
        ];

        return $this->sendRequest('embeddings', $payload, 3, 20);
    }
}
