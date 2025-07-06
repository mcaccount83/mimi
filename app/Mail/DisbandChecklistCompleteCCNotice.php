<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class DisbandChecklistCompleteCCNotice extends BaseMailable

{
    public $mailData;

     public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

     public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Disband Checklist Submitted | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chapter.disbandchecklistcompleteccnotice',
        );
    }

    public function attachments(): array
    {
        return [];
    }

}
