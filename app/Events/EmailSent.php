<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailSent
{
    use Dispatchable, SerializesModels;

    /**
     * The email address the email was sent to.
     *
     * @var string
     */
    public $recipient;

    /**
     * The subject of the email.
     *
     * @var string
     */
    public $subject;

    /**
     * Create a new event instance.
     *
     * @param string $recipient
     * @param string $subject
     */
    public function __construct($recipient, $subject)
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
    }
}
