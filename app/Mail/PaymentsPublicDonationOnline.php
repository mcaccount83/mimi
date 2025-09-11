<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PaymentsPublicDonationOnline extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        $donationType = $this->mailData['donationType'] ?? 'Donation';

        return new Envelope(
            subject: "Public {$donationType} Received",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payments.donationpubliconline',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

// ->markdown('emails.public.donationpublicadminnotice');
