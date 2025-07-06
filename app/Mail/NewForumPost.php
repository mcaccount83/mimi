<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewForumPost extends BaseMailable
{

    public $post;

    public $thread;

    public $category;

    public $authorNameWithPosition;

    public function __construct($post, $thread, $category, $authorNameWithPosition)
    {
        $this->post = $post;
        $this->thread = $thread;
        $this->category = $category;
        $this->authorNameWithPosition = $authorNameWithPosition;
    }

    public function build()
    {
        return $this->markdown('emails.forum.new-post')
                    // ->subject("New Reply in: {$this->thread->title}");
            ->subject("{$this->category->title} | RE:{$this->thread->title}");
    }
}
