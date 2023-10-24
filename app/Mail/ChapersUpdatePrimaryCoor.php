<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChapersUpdatePrimaryCoor extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData; // Create a property to store the mail data

    // Add properties for other variables...

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData; // Assign the mail data to the property
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.chapterupdate.primarycoor');
    }
}
