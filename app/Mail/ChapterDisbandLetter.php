<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChapterDisbandLetter extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    protected $pdfPath;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData, $pdfPath)
    {
        $this->mailData = $mailData;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject('Chapter Disband Letter')
            ->replyTo($this->mailData['cc_email'])
            ->markdown('emails.chapterupdate.chapterdisbandletter')
            ->attach($this->pdfPath, [
                'as' => $this->mailData['chapterState'].'_'.$this->mailData['chapterName'].'_Disband_Letter.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
