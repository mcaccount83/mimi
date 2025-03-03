<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ChapterDisbandLetter extends Mailable implements ShouldQueue
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
            ->subject("Chapter Disband Letter | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}")
            ->replyTo($this->mailData['userEmail'])
            ->markdown('emails.chapterupdate.chapterdisbandletter')
            ->attach($this->pdfPath, [
                'as' => $this->mailData['chapterState'].'_'.$this->mailData['chapterName'].'_Disband_Letter.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
