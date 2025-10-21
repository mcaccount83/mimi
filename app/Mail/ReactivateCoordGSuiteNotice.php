<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ReactivateCoordGSuiteNotice extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Coordinator Reactivate Admin Notice',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.coordinator.reactivatecoordgsuitenotice',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
