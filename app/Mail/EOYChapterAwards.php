<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EOYChapterAwards extends BaseMailable
{
    public array $mailData;

    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('support@momsclub.org', 'MOMS Club'),
            replyTo: [
                new Address('support@momsclub.org', 'MOMS Club'),
            ],
            subject: "Chapter Awards | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.endofyear.chapterawards',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->mailData['badgeAttachments'] ?? [] as $badge) {
            $attachments[] = Attachment::fromData(
                fn() => base64_decode($badge['content']),
                $badge['name']
            )->withMime($badge['mime']);
        }

        return $attachments;
    }
}
