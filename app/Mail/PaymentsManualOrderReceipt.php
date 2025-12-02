<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PaymentsManualOrderReceipt extends BaseMailable
{
    public $mailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

      public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Chapter Manual Replacement Order",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payments.manualorderreceipt',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
