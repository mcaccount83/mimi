<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PaymentsReRegOnline extends BaseMailable
// class ReRegAdminNotice extends BaseMailable
{
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
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject("Re-Registration Payment Received | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}")
            ->markdown('emails.payments.reregonline');
            // ->markdown('emails.chapter.reregadminnotice');
    }
}
