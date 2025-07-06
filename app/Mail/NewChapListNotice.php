<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewChapListNotice extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Chapter Add ListAdmin Notice",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chapter.newchaplsitnotice',
        );
    }

    public function attachments(): array
    {
        return [];
    }

}
