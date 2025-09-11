<?php

namespace App\Mail;

class EOYElectionReportThankYou extends BaseMailable
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
            ->subject('Election Report Submitted')
            ->markdown('emails.endofyear.electionreportthankyou');
    }
}
