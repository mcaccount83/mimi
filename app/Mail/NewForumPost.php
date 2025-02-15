<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class NewForumPost extends Mailable
{
    use Queueable, SerializesModels;

    public $post;
    public $thread;
    public $authorNameWithPosition;


    public function __construct($post, $thread, $authorNameWithPosition)
    {
        $this->post = $post;
        $this->thread = $thread;
        $this->authorNameWithPosition = $authorNameWithPosition;
    }

    public function build()
    {
        return $this->markdown('emails.forum.new-post')
                    ->subject("New Reply in: {$this->thread->title}");
    }
}
