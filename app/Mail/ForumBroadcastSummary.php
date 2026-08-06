<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Category;

class ForumBroadcastSummary extends BaseMailable
{
    public function __construct(
        public string $broadcastSubject,
        public int $recipientCount,
        public Post $post,
        public Thread $thread,
        public Category $category,
        public string $authorNameWithPosition,
        public string $type = 'reply',
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
