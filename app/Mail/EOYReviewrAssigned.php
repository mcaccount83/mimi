<?php

namespace App\Mail;

class EOYReviewrAssigned extends BaseMailable
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
            ->subject("Financial Reviewer Assigned | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}")
            ->markdown('emails.endofyear.reviewerassigned');
    }
}
