<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class DisbandChapListNotice extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Chapter Removal ListAdmin Notice',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chapter.disbandchaplistnotice',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
