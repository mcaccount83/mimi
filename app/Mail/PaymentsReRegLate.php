<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PaymentsReRegLate extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

      public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Re-Registration Late Payment Reminder | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payments.rereglate',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
