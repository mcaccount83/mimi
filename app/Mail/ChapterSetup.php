<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ChapterSetup extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, IsMonitored, Queueable, SerializesModels;

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
            ->markdown('emails.chapter.chaptersetup')
            ->attachData($content, 'EINApplication.pdf', [
                'mime' => 'application/pdf',
            ])
            ->attachData($content2, 'EINInstructions.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
