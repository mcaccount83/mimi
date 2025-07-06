<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewCoordApplication extends BaseMailable
{
    public $mailData;


    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Coordinator Application Received | Conference {$this->mailData['conference_id']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.coordinator.newcoordapplication',
        );
    }

    public function attachments(): array
    {
        return [];
    }

}
