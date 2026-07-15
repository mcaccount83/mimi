<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewCoordApproveCCNotice extends BaseMailable
{
    public array $mailData;

    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Coordinator Approved | {$this->mailData['first_name']}, {$this->mailData['last_name']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.coordinator.newcoordapproveccnotice',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
