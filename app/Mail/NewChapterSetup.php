<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewChapterSetup extends BaseMailable
{
    public $mailData;

    protected $pdfPath;

    protected $pdfPath2;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData, $pdfPath, $pdfPath2)
    {
        $this->mailData = $mailData;
        $this->pdfPath = $pdfPath;
        $this->pdfPath2 = $pdfPath2;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        // Download the Google Drive file first
        $content = file_get_contents($this->pdfPath);
        $content2 = file_get_contents($this->pdfPath2);

        return $this
            ->subject('Chapter Setup')
            ->replyTo($this->mailData['userEmail'])
            ->markdown('emails.chapter.newchaptersetup')
            ->attachData($content, 'EINApplication.pdf', [
                'mime' => 'application/pdf',
            ])
            ->attachData($content2, 'EINInstructions.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
