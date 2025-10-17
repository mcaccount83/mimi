<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AdminNewMIMIBugWish extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New MIMI Bugs & Wishes Submitted',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.techreports.newmimibugwish',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
