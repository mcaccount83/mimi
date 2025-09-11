<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WebsiteUpdatePCNotice extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Website Update Notification',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chapter.websiteupdatepcnotice',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
