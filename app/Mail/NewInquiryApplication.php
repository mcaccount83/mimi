<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewInquiryApplication extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Inquiry Received for {$this->mailData['state']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.inquiries.newinquiryapplication',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
