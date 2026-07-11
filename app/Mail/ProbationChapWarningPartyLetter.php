<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ProbationChapWarningPartyLetter extends BaseMailable
{
    public array $mailData;

    protected string $pdfPath;

    protected string $pdfPath2;

    public function __construct(array $mailData, string $pdfPath, string $pdfPath2)
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
            subject: "Warning Party Expense Letter | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chapter.probationchapwarningpartyletter',
        );
    }

    public function attachments(): array
    {
        $pdfContent2 = file_get_contents($this->pdfPath2);

        return [
            Attachment::fromPath($this->pdfPath)
                ->as($this->mailData['chapterState'].'_'.$this->mailData['chapterNameSanitized'].'_Warning_Party.pdf')
                ->withMime('application/pdf'),
            Attachment::fromData(fn () => $pdfContent2, 'FSChapterPartyExpense.pdf'
                )->withMime('application/pdf'),
        ];
    }
}
