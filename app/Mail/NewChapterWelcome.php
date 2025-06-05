<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class NewChapterWelcome extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, IsMonitored, Queueable, SerializesModels;

    public $mailData;

    protected $pdfPath;

    protected $pdfPath2;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData, $pdfPath, $pdfPath2)
    {
        $this->mailData = $mailData;
        $this->pdfPath = $pdfPath;
        $this->pdfPath2 = $pdfPath2;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        // Download the Google Drive file first
        $content = file_get_contents($this->pdfPath2);

        return $this
            ->subject('Congratulations on your New Chapter!')
            ->replyTo($this->mailData['userEmail'])
            ->markdown('emails.chapter.newchapterwelcome')
            ->attach($this->pdfPath, [
                'as' => $this->mailData['chapterState'].'_'.$this->mailData['chapterName'].'_ChapterInGoodStanding.pdf',
                'mime' => 'application/pdf',
            ])
            ->attachData($content, 'GroupExemptionLetter.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
