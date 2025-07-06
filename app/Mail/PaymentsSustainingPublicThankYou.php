<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PaymentsSustainingPublicThankYou extends BaseMailable
// class DonationSustainingPublicThankYou extends BaseMailable
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
            ->markdown('emails.payments.sustainingpublicthankyou');
            // ->markdown('emails.public.donationsustainingpublicthankyou');
    }
}
