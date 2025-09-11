<?php

namespace App\Mail;

class PaymentsManualOrderReceipt extends BaseMailable
    // class ManualOrderChapterReceipt extends BaseMailable
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
            ->from('support@momsclub.org', 'MOMS Club')
            ->subject('Chapter Manual Replacement Order')
            ->markdown('emails.payments.manualorderreceipt');
        // ->markdown('emails.chapter.manualorderchapterreceipt');
    }
}
