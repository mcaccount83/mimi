<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ForumDailyDigest extends BaseMailable
{
    public function __construct(
        public $threadsByCategory,
        public User $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@momsclub.org', 'MOMS Club'),
            subject: 'Your MOMS Club Forum Daily Digest',
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.forum.daily-digest');
    }

    public function attachments(): array
    {
        return [];
    }
}
