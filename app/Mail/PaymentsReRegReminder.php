<?php

namespace App\Mail;

class PaymentsReRegReminder extends BaseMailable
    // class ReRegChapterReminder extends BaseMailable
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
            ->subject("Re-Registration Payment Reminder | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}")
            ->markdown('emails.payments.reregreminder')
            // ->markdown('emails.chapter.reregchapterreminder')
            ->with('mailData', $this->mailData);
    }
}
