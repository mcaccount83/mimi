<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PaymentsManualOnline extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

      public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Manual Replacement Order | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payments.manualonline',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
