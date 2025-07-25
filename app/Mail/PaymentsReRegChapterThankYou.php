<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;;
use Illuminate\Support\Carbon;

class PaymentsReRegChapterThankYou extends BaseMailable
// class ReRegChapterThankYou extends BaseMailable
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

        // Check if 'chapterDate' is set and valid
        if (! isset($this->mailData['chapterDate']) || ! strtotime($this->mailData['chapterDate'])) {
            // If 'chapterDate' is not set or invalid, default it to today's date
            $this->mailData['chapterDate'] = Carbon::today()->format('m-d-Y');
        } elseif (strpos($this->mailData['chapterDate'], '-') !== false) {
            // If 'chapterDate' contains '-', assume it's in 'yyyy-mm-dd' format and format it
            $this->mailData['chapterDate'] = date('m-d-Y', strtotime($this->mailData['chapterDate']));
        }
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->from('support@momsclub.org', 'MOMS Club')
            ->subject('Thank You for Your Re-Registration Payment')
            ->markdown('emails.payments.reregchapterthankyou');
            // ->markdown('emails.chapter.reregchapterthankyou');
        // ->with('mailData', $this->mailData);
    }
}
