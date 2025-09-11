<?php

namespace App\Mail;

class PaymentsNewChapOnline extends BaseMailable
    // class NewChapterAdminNotice extends BaseMailable
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
            ->subject("New Chapter Application Received | Conference {$this->mailData['chapterConf']}")
            ->markdown('emails.payments.newchaponline');
        // ->markdown('emails.chapter.newchapteradminnotice');
    }
}
