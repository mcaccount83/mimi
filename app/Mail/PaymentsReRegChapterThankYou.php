<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Illuminate\Support\Carbon;

class PaymentsReRegChapterThankYou extends Mailable implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, IsMonitored, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
        // $this->mailData['chapterDate'] = date('m-d-Y', strtotime($this->mailData['chapterDate']));

        // Check if 'chapterDate' is set and valid
        if (! isset($this->mailData['chapterDate']) || ! strtotime($this->mailData['chapterDate'])) {
            // If 'chapterDate' is not set or invalid, default it to today's date
            $this->mailData['chapterDate'] = Carbon::today()->format('m-d-Y');
        } elseif (strpos($this->mailData['chapterDate'], '-') !== false) {
            // If 'chapterDate' contains '-', assume it's in 'yyyy-mm-dd' format and format it
            $this->mailData['chapterDate'] = date('m-d-Y', strtotime($this->mailData['chapterDate']));
        }
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->from('support@momsclub.org', 'MOMS Club')
            ->subject('Thank You for Your Re-Registration Payment')
            ->markdown('emails.payments.reregchapterthankyou')
            ->with('mailData', $this->mailData);
    }
}
