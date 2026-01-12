<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EOYFinancialReportThankYou extends BaseMailable
{
    public $mailData;

    protected $pdfPath;

    protected $fiscalYear;

    public function __construct($mailData, $pdfPath, $fiscalYear)
    {
        $this->mailData = $mailData;
        $this->pdfPath = $pdfPath;
        $this->fiscalYear = $fiscalYear;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('support@momsclub.org', 'MOMS Club'),
            replyTo: [
                new Address('support@momsclub.org', 'MOMS Club'),
            ],
            subject: 'Financial Report Submitted',
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
                ->as($this->fiscalYear.'_'.$this->mailData['chapterState'].'_'.$this->mailData['chapterNameSanitized'].'_FinancialReport.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
