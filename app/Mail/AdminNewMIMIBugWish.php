<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class AdminNewMIMIBugWish extends Mailable implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, IsMonitored, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
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
            ->subject('New MIMI Bugs & Wishes Submitted')
            ->markdown('emails.admin.newmimibugwish');
    }
}
