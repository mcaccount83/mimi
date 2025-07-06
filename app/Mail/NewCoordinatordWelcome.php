<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewCoordinatordWelcome extends BaseMailable

{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->mailData['userEmail'], $this->mailData['userName']),
            replyTo: [
                new Address($this->mailData['userEmail'], $this->mailData['userName'])
            ],
            subject: 'Welcome to Our Team!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.coordinator.newcoordwelcome',
        );
    }

    public function attachments(): array
    {
        return [];
    }

}
