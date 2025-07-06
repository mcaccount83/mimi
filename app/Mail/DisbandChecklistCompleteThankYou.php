<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class DisbandChecklistCompleteThankYou extends BaseMailable
{
    public $mailData;

      public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('support@momsclub.org', 'MOMS Club'),
            replyTo: [
                new Address('support@momsclub.org', 'MOMS Club')
            ],
            subject: "Disband Checklist Submitted",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chapter.disbandchecklistcompletethankyou',
        );
    }

    public function attachments(): array
    {
        return [];
    }

}
