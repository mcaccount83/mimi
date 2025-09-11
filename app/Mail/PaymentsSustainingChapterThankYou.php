<?php

namespace App\Mail;

class PaymentsSustainingChapterThankYou extends BaseMailable
    // class DonationSustainingChapterThankYou extends BaseMailable
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
            ->subject('Thank You for Your Sustaining Chapter Donation')
            ->markdown('emails.payments.sustainingchapterthankyou');
        // ->markdown('emails.chapter.donationsustainingchapterthankyou');
    }
}
