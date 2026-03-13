<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

    class ForumBroadcastSummary extends BaseMailable
{
    public function __construct(
        public string $broadcastSubject,
        public int $recipientCount,
        public $post,
        public $thread,
        public $category,
        public string $authorNameWithPosition,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Forum Broadcast] {$this->broadcastSubject}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.forum.broadcast-summary',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
