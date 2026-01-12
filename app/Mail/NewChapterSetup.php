<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewChapterSetup extends BaseMailable
{
    public $mailData;

    protected $pdfPath;

    protected $pdfPath2;

    public function __construct($mailData, $pdfPath, $pdfPath2)
    {
        $this->mailData = $mailData;
        $this->pdfPath = $pdfPath;
        $this->pdfPath2 = $pdfPath2;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->mailData['userEmail'], $this->mailData['userName']),
            replyTo: [
                new Address($this->mailData['userEmail'], $this->mailData['userName']),
            ],
            subject: 'New Chapter Setup',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chapter.newchaptersetup',
        );
    }

    public function attachments(): array
    {
        // Download the Google Drive file first
        $content = file_get_contents($this->pdfPath);
        $content2 = file_get_contents($this->pdfPath2);

        return [
            Attachment::fromData(fn () => $content, 'EINApplication.pdf')
                ->withMime('application/pdf'),
            Attachment::fromData(fn () => $content2, 'EINInstructions.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
