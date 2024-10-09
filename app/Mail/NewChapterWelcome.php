<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class NewChapterWelcome extends Mailable implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, IsMonitored, SerializesModels;

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
        $this->pdfPath = $pdfPath2;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject('Congratulations on your New Chapter!')
            ->replyTo($this->mailData['userEmail'])
            ->markdown('emails.chapterupdate.newchapterwelcome')
            ->attach($this->pdfPath, [
                'as' => $this->mailData['state'].'_'.$this->mailData['chapter'].'_ChapterInGoodStanding.pdf',
                'mime' => 'application/pdf',
            ])
            ->attach($this->pdfPath2, [
                'as' => 'GroupExemptionLetter.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
