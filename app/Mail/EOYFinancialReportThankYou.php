<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EOYFinancialReportThankYou extends BaseMailable
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
            subject: "Financial Report Submitted",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.endofyear.financialreportthankyou',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as(date('Y') - 1 .'-'.date('Y').'_'.$this->mailData['chapterState'].'_'.$this->mailData['chapterNameSanitized'].'_FinancialReport.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
