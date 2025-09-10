<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewChapPCNotice extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Chapter Add Notification',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chapter.newchappcnotice',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
