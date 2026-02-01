<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class YesToChapterInquiries extends BaseMailable
{
    public $mailData;


    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->mailData['inquiriesCoordEmail'], 'MOMS Club Inquiries'),
            replyTo: [
                new Address($this->mailData['inquiriesCoordEmail'], 'MOMS Club Inquiries'),
            ],
            subject: "New Inquiry for Your Chapter",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.inquiries.yestochapter',
        );
    }

     public function attachments(): array
    {
        return [];
    }
}
