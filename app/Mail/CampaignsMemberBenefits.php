<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CampaignsMemberBenefits extends BaseMailable
{
    public array $mailData;

    protected string $pdfPath;

    public function __construct(array $mailData, string $pdfPath)
    {
        $this->mailData = $mailData;
        $this->pdfPath = $pdfPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('support@momsclub.org', 'MOMS Club'),
            replyTo: [
                new Address('support@momsclub.org', 'MOMS Club'),
            ],
            subject: "Nonprofit Status & Member Benefits | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.campaigns.memberbenefits',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        $pdfContent = @file_get_contents($this->pdfPath);
        if ($pdfContent != false) {
            $attachments[] = Attachment::fromData(
                fn () => $pdfContent,
                'MemberBenefit.pdf'
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
