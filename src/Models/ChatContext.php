<?php

namespace Noodleware\Chattera\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Noodleware\Chattera\Services\OpenAIService;

/**
 * ChatContext model for retrieving and calculating context similarity.
 *
 * @property mixed $embedding
 *
 * @method static whereNotNull(string $column)
 * @method static create(array $attributes)
 */
class ChatContext extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'embedding' => 'array',
    ];

    /**
     * Retrieve the most relevant context based on the provided message.
     *
     * @throws ConnectionException
     */
    public static function getRelevantContext(string $message): string
    {
        // Get embeddings for the input message using the OpenAI service.
        $embeddings = app(OpenAIService::class)->embeddings($message);

        // Ensure that the expected data is present.
        if (empty($embeddings['data'][0]['embedding'])) {
            return '';
        }

        // Cache the chat contexts with embeddings using a descriptive key.
        $chatContext = Cache::rememberForever('chat_context_embeddings', function () {
            return self::whereNotNull('embedding')->get();
        });

        return self::calculateSimilarContext($chatContext, $embeddings['data'][0]['embedding']);
    }

    /**
     * Calculate and concatenate similar context entries based on cosine similarity.
     */
    protected static function calculateSimilarContext(Collection $chatContext, array $embedding): string
    {
        // Map each context to its cosine similarity score.
        $results = $chatContext->map(function ($context) use ($embedding) {
            return [
                'similarity' => cosineSimilarity($context->embedding, $embedding),
                'context' => $context->value,
            ];
        });

        $totalTokens = 0;
        $concatenatedContexts = '';

        // Sort contexts by descending similarity.
        foreach ($results->sortByDesc('similarity') as $contextInfo) {
            // Count tokens using PHP's built-in function for clarity.
            $contextTokens = str_word_count($contextInfo['context']);

            if ($totalTokens + $contextTokens <= config('chattera.token_limit')) {
                $concatenatedContexts .= $contextInfo['context'].' ';
                $totalTokens += $contextTokens;
            } else {
                break;
            }
        }

        return rtrim($concatenatedContexts);
    }
}
