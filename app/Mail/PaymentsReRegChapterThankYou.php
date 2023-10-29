<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentsReRegChapterThankYou extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
        $this->mailData['chapterDate'] = date('m-d-Y', strtotime($this->mailData['chapterDate']));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        return $this
            ->subject('Thank You for Your Re-Registration Payment')
            ->markdown('emails.payments.reregchapterthankyou')
            ->with('mailData', $this->mailData);
    }
}
