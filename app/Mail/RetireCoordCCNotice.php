<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class RetireCoordCCNotice extends BaseMailable
{
    public array $mailData;

    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Coordinator Retired',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.coordinator.retirecoordccnotice',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
