<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EOYFinancialSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public $coordinator_array;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData, $coordinator_array)
    {
        $this->mailData = $mailData;
        $this->coordinator_array = $coordinator_array;
    }

   /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
        ->subject('Financial Report Submitted')
        ->markdown('emails.endofyear.financialsubmitted');
    }
}
