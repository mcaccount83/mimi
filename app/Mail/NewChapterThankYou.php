<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewChapterThankYou extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('support@momsclub.org', 'MOMS Club'),
            replyTo: [
                new Address('support@momsclub.org', 'MOMS Club'),
            ],
            subject: 'Thank You for Your New Chapter Application!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payments.newchapthankyou',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
