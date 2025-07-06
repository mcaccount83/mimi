<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewCoordinatorThankYou extends BaseMailable
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
        // $this->mailData['chapterDate'] = date('m-d-Y', strtotime($this->mailData['chapterDate']));

    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->from('support@momsclub.org', 'MOMS Club')
            ->subject('Thank You for Your Coordinator Application')
            ->markdown('emails.coordinator.newcoordthankyou');
        // ->with('mailData', $this->mailData);
    }
}
