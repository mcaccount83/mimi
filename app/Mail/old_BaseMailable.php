<?php

namespace App\Mail;

use App\Events\EmailSent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable as BaseMailable;
use Illuminate\Queue\SerializesModels;

class Mailable extends BaseMailable
{
    use Queueable, SerializesModels;

    public function sendMail()
    {
        // Logic to send email

        // Dispatch the EmailSent event
        event(new EmailSent($this->to, $this->subject));
    }
}
