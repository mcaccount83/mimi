<?php

namespace App\Mail;

class PaymentsManualOnline extends BaseMailable
    // class ManualOrderAdminNotice extends BaseMailable
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
            ->subject("Donation Received | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}")
            ->markdown('emails.payments.manualonline');
        // ->markdown('emails.chapter.manualorderadminnotice');
    }
}
