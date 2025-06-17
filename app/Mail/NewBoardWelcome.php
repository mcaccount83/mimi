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

class NewBoardWelcome extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, IsMonitored, Queueable, SerializesModels;

     public $mailData;

    protected $pdfPath;

    public function __construct($mailData, $pdfPath)
    {
        $this->mailData = $mailData;
        $this->pdfPath = $pdfPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->mailData['userEmail'], $this->mailData['userName']),
            replyTo: [
                new Address($this->mailData['userEmail'], $this->mailData['userName'])
            ],
            subject: 'Welcome to the Executive Board!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chapter.newboardwelcome',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        $pdfContent = @file_get_contents($this->pdfPath);
        if ($pdfContent !== false) {
            $attachments[] = Attachment::fromData(
                fn() => $pdfContent,
                $this->mailData['fiscalYear'].'_OfficerPacket.pdf'
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
