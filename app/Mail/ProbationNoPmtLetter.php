<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ProbationNoPmtLetter extends Mailable implements ShouldQueue
// class ProbationChapNoPmtLetter extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, IsMonitored, Queueable, SerializesModels;

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
            ->subject("Probation No Payment Letter | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}")
            ->replyTo($this->mailData['userEmail'])
            ->markdown('emails.chapter.probationpmtletter')
            // ->markdown('emails.chapter.probationchapnopmtletter')
            ->attach($this->pdfPath, [
                'as' => $this->mailData['chapterState'].'_'.$this->mailData['chapterNameSanitized'].'_Probation_No_Payment.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
