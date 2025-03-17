<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class WarningPartyLetter extends Mailable implements ShouldQueue
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
            ->subject("Warning Party Expense Letter | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}")
            ->replyTo($this->mailData['userEmail'])
            ->markdown('emails.chapterupdate.warningpartyletter')
            ->attach($this->pdfPath, [
                'as' => $this->mailData['chapterState'].'_'.$this->mailData['chapterNameSanitized'].'_Warning_Party.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
