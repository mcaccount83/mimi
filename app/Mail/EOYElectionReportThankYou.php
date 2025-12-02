<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EOYElectionReportThankYou extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Election Report Submitted",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.endofyear.electionreportthankyou',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
