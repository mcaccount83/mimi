<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ChapersUpdateListAdmin extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, IsMonitored, Queueable, SerializesModels;

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
     * Build the message.  From MIMI to ListAdmin
     */
    public function build(): static
    {
        return $this
            ->subject("Chapter Update ListAdmin Notice | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}")
            ->markdown('emails.chapterupdate.listadmin');
    }
}
