<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Carbon;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PaymentsReRegChapterThankYou extends BaseMailable
{
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;

        // Check if 'chapterDate' is set and valid
        if (! isset($this->mailData['chapterDate']) || ! strtotime($this->mailData['chapterDate'])) {
            // If 'chapterDate' is not set or invalid, default it to today's date
            $this->mailData['chapterDate'] = Carbon::today()->format('m-d-Y');
        } elseif (strpos($this->mailData['chapterDate'], '-') != false) {
            // If 'chapterDate' contains '-', assume it's in 'yyyy-mm-dd' format and format it
            $this->mailData['chapterDate'] = date('m-d-Y', strtotime($this->mailData['chapterDate']));
        }
    }

      public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('support@momsclub.org', 'MOMS Club'),
            replyTo: [
                new Address('support@momsclub.org', 'MOMS Club'),
            ],
            subject: "Thank You for Your Re-Registration Payment",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payments.reregchapterthankyou',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
