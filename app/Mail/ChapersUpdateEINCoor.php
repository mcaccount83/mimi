<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ChapersUpdateEINCoor extends Mailable implements ShouldQueue
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
     * Build the message.  From MIMI to EIN Coordinator
     */
    public function build(): static
    {
        return $this
            ->subject("Chapter Name Change Notification | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}")
            ->markdown('emails.chapterupdate.eincoor')
            ->attach($this->pdfPath, [
                'as' => $this->mailData['chapterState'].'_'.$this->mailData['chapterNameSanitized'].'_ChapterNameChange.pdf',
                'mime' => 'application/pdf',
            ]);;
    }
}
