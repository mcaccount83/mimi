<?php

namespace App\Mail;

class PaymentsReRegLate extends BaseMailable
    // class ReRegChapterReminderLate extends BaseMailable
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
            ->subject("Re-Registration Late Payment Reminder | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}")
            ->markdown('emails.payments.rereglate')
            // ->markdown('emails.chapter.reregchapterreminderlate')
            ->with('mailData', $this->mailData);
    }
}
