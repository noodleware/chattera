<?php

namespace Noodleware\Chattera\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Noodleware\Chattera\Services\OpenAIService;

/**
 * ChatMessage model for storing individual chat messages.
 *
 * @property mixed $total_tokens
 * @property mixed $id
 * @property mixed $content
 */
class ChatMessage extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Check if this message has been flagged by the moderation service.
     */
    public function isFlagged(): bool
    {
        try {
            // Call the moderation service with the current message content.
            $moderation = app(OpenAIService::class)
                ->moderation($this->content);

            // Ensure that the expected structure exists before returning the flagged status.
            if (isset($moderation['results'][0]['flagged'])) {
                return (bool) $moderation['results'][0]['flagged'];
            }
        } catch (\Exception $e) {
        }

        // Default to false if moderation fails or the structure is unexpected.
        return false;
    }
}
