<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EOYFinancialSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public $financial_report_array;

    public $coordinator_array;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData, $financial_report_array, $coordinator_array)
    {
        $this->mailData = $mailData;
        $this->financial_report_array = $financial_report_array;
        $this->coordinator_array = $coordinator_array;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->markdown('emails.endofyear.financialsubmitted');
    }
}
