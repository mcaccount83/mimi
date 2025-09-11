<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class RCChangeRCNotice extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Menoring Coordinator Change | {$this->mailData['cdName']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.coordinator.rcchangercnotice',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
