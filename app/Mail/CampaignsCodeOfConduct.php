<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CampaignsCodeOfConduct extends BaseMailable
{
    public array $mailData;

    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->mailData['ccEmail'], $this->mailData['ccName']),
            replyTo: [
                new Address($this->mailData['ccEmail'], $this->mailData['ccName']),
            ],
            subject: 'Thank You!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.campaigns.codeofconduct',
        );
    }


}
