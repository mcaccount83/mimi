<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ProbationReportSubmitted extends BaseMailable
// class ProbationRptSubmittedCCNotice extends BaseMailable
{
    public $mailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject("Quarterly Financial Report Submitted | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}")
            ->markdown('emails.chapter.probationreportsubmitted');
            // ->markdown('emails.chapter.probationrptsubmittedccnotice');
    }
}
