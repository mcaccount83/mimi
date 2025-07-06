<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ProbationChapPartyLetter extends BaseMailable
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
            ->subject("Probation Party Expense Letter | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}")
            ->replyTo($this->mailData['userEmail'])
            ->markdown('emails.chapter.probationchappartyletter')
            ->attach($this->pdfPath, [
                'as' => $this->mailData['chapterState'].'_'.$this->mailData['chapterNameSanitized'].'_Probation_Party.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
