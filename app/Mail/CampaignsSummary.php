<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CampaignsSummary extends BaseMailable
{
    public array $mailData;

    protected ?string $pdfPath;

    public function __construct(array $mailData, ?string $pdfPath = null)
    {
        $this->mailData = $mailData;
        $this->pdfPath = $pdfPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Email Campaign Sent | {$this->mailData['campaignLabel']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.campaigns.summary',
        );
    }

    public function attachments(): array
    {
        if (!$this->pdfPath) {
            return [];
        }

        $pdfContent = @file_get_contents($this->pdfPath);
        if ($pdfContent != false) {
            return [
                Attachment::fromData(
                    fn () => $pdfContent,
                    'Attachment.pdf'
                )->withMime('application/pdf')
            ];
        }

        return [];
    }
}
