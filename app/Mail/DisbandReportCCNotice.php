<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class DisbandReportCCNotice extends BaseMailable
{
    public $mailData;

    protected $pdfPath;

    public function __construct($mailData, $pdfPath)
    {
        $this->mailData = $mailData;
        $this->pdfPath = $pdfPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Final Financial Report Submitted | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chapter.disbandreportccnotice',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as($this->mailData['chapterState'].'_'.$this->mailData['chapterNameSanitized'].'_Final_FinancialReport.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
