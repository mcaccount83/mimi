<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PaymentsPublicDonationOnline extends BaseMailable
// class DonationPublicAdminNotice extends BaseMailable
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
            ->subject("Public Donation Received")
            ->markdown('emails.payments.donationpubliconline');
            // ->markdown('emails.public.donationpublicadminnotice');
    }
}
