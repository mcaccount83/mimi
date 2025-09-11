<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewCoordApproveRCNotice extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
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
            markdown: 'emails.coordinator.newcoordapprovercnotice',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
