<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewChapEIN extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->mailData['userEmail'], $this->mailData['userName']),
            replyTo: [
                new Address($this->mailData['userEmail'], $this->mailData['userName'])
            ],
            subject: 'EIN Number Received',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chapter.newchapein',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
