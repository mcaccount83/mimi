<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class RCChangeCoordNotice extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mentoring Coordinator Change',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.coordinator.rcchangecoordnotice',
        );
    }

    public function attachments(): array
    {
        return [];
    }

}
