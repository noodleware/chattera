<?php

namespace Noodleware\Chattera\Console\Commands;

use Exception;
use Noodleware\Chattera\Mail\ChatReport;
use Noodleware\Chattera\Models\Chat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Noodleware\Chattera\Services\OpenAIService;

class SendChatteraReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chattera:send-report {frequency}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch chat report jobs based on account settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $start = match ($this->argument('frequency')) {
            'daily' => today()->subDay(),
            'weekly' => today()->subWeek(),
            'monthly' => today()->subMonth(),
        };

        $end = today()->subDay()->endOfDay();

        // load all chats
        $chats = Chat::query()
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->each(function (Chat $chat) {
                if (!$chat->rating) {
                    $rules = implode(". \n", [
                        'You are a customer satisfaction determination bot',
                        'Analyse the following conversation and give it a "rating" between 0 and 100.',
                        'Analyse the following conversion and suggest one possible "improvement" from the assistant',
                        'You will return the statements in a raw JSON format without any code block or markdown formatting.',
                    ]);

                    $payload = $chat->messages
                        ->map(function ($message) {
                            return [
                                'role' => $message->role,
                                'content' => $message->content,
                            ];
                        })
                        ->values()
                        ->toArray();

                    // Call the OpenAI service to generate a response.
                    $data = app(OpenAIService::class)->responses("Rules: \n$rules", $payload);

                    // store response
                    try {
                        $object = json_decode(extractAssistantText($data));
                        $chat->rating = $object->rating;
                        $chat->improvement = $object->improvement;
                        $chat->save();
                    } catch (Exception $exception) {}

                    $chat->refresh();
                }
            });

        // if no chats in that period then do not send
        if ($chats->isEmpty()) {
            return;
        }

        Mail::to(config('chattera.report.recipients'))
            ->send(new ChatReport($chats));
    }
}
