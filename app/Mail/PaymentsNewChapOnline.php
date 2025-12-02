<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PaymentsNewChapOnline extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

      public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Chapter Application Received | Conference {$this->mailData['chapterConf']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payments.newchaponline',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
