<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewWebsiteApproveChapNotice extends BaseMailable
{
    public $mailData;

     public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Website Link Notification | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chapter.newwebsiteapprovechapnotice',
        );
    }

    public function attachments(): array
    {
        return [];
    }

}
