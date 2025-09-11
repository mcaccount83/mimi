<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PaymentsDonationOnline extends BaseMailable
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
            subject: "{$donationType} Received | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payments.donationonline',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

// ->markdown('emails.chapter.donationchapadminnotice');
