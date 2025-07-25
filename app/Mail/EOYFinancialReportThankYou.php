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

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData, $pdfPath)
    {
        $this->mailData = $mailData;
        $this->pdfPath = $pdfPath;

    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject('Financial Report Submitted')
            ->markdown('emails.endofyear.financialreportthankyou')
            ->attach($this->pdfPath, [
                'as' => date('Y') - 1 .'-'.date('Y').'_'.$this->mailData['chapterState'].'_'.$this->mailData['chapterNameSanitized'].'_FinancialReport.pdf',
                'mime' => 'application/pdf',
            ]);

    }
}
