<?php

namespace App\Mail;

class PaymentsM2MPublicThankYou extends BaseMailable
    // class DonationM2MPublicThankYou extends BaseMailable
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
            ->subject('Thank You for Your Mother-to-Mother Fund Donation')
            ->markdown('emails.payments.m2mpublicthankyou');
        // ->markdown('emails.public.donationm2mpublicthankyou');
    }
}
