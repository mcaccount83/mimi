<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewInquiryThankYou extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->mailData['emailInquiriesCoord'], 'MOMS Club Inquiries'),
            replyTo: [
                new Address($this->mailData['emailInquiriesCoord'], 'MOMS Club Inquiries'),
            ],
            subject: 'Thank You for Your New Chapter Inquiry!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.inquiries.newinquirythankyou',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
