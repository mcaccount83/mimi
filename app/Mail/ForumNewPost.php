<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Category;

class ForumNewPost extends BaseMailable
{
    public function __construct(
        public Post $post,
        public Thread $thread,
        public Category $category,
        public string $authorNameWithPosition,
        public string $type = 'reply',  // 'thread' or 'reply'
    ) {}

    public function envelope(): Envelope
    {
        $subjectPrefix = $this->type === 'thread' ? '' : 'RE: ';

        return new Envelope(
            from: new Address('noreply@momsclub.org', 'MOMS Club'),
            subject: "{$this->category->title} | {$subjectPrefix}{$this->thread->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.forum.new-post',
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function headers(): Headers
    {
        return new Headers(
            text: ['X-Forum-Broadcast' => 'true'],
        );
    }
}
