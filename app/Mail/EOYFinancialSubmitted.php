<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EOYFinancialSubmitted extends BaseMailable
{
    public array $mailData;

    protected string $pdfPath;

    protected string $reportYearRange;

    public function __construct(array $mailData, string $pdfPath, string $reportYearRange)
    {
        $this->mailData = $mailData;
        $this->pdfPath = $pdfPath;
        $this->reportYearRange = $reportYearRange;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Financial Report Submitted | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.endofyear.financialsubmitted',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as($this->reportYearRange.'_'.$this->mailData['chapterState'].'_'.$this->mailData['chapterNameSanitized'].'_FinancialReport.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
