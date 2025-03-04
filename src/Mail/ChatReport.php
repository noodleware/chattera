<?php

namespace Noodleware\Chattera\Mail;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Barryvdh\DomPDF\Facade\Pdf;

class ChatReport extends Mailable
{
    /**
     * Create a new message instance.
     */
    public function __construct(public Collection $chats)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('chattera.report.branding', 'Chatbot') . ' Report',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'chattera::mail.chat-report',
            with: [
                'chats' => $this->chats,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->chats as $chat) {
            $pdf = Pdf::loadView('chattera::mail.chat-transcript', ['chat' => $chat]);

            $attachments[] = Attachment::fromData(
                fn () => $pdf->output(),
                'transcript-' . $chat->id . '.pdf'
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
