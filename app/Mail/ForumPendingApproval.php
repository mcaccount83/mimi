<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;

class ForumPendingApproval extends BaseMailable
{
    public function __construct(
        public string $type,
        public Thread $thread,
        public ?Post $post,
        public string $authorNameWithPosition,
    ) {}

    public function envelope(): Envelope
    {
        $subjectPrefix = $this->type === 'thread' ? 'New Thread' : 'New Reply';

        return new Envelope(
            subject: "{$this->thread->category->title} | {$subjectPrefix} Pending Approval: {$this->thread->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.forum.pending-approval',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
