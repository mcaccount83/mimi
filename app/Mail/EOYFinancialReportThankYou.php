<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EOYFinancialReportThankYou extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;
    public $coordinator_array;
    protected $attachmentPath;
    protected $attachmentName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData, $coordinator_array, $attachmentPath = null, $attachmentName = null)
    {
        $this->mailData = $mailData;
        $this->coordinator_array = $coordinator_array;
        $this->attachmentPath = $attachmentPath;
        $this->attachmentName = $attachmentName;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject('Financial Report Submitted')
            ->markdown('emails.endofyear.financialreportthankyou')
            ->attach($this->attachmentPath, [
                'as' => $this->attachmentName,
                'mime' => mime_content_type($this->attachmentPath),
            ]);
    }

}
