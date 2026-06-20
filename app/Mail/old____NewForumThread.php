<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;

class NewForumThread extends BaseMailable
{
    public function __construct(
        public $post,
        public $thread,
        public $category,
        public $authorNameWithPosition
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@momsclub.org', 'MOMS Club'),
            subject: "{$this->category->title} | {$this->thread->title}",
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
