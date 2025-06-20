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

class NewChapterWelcome extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, IsMonitored, Queueable, SerializesModels;

    public $mailData;

    protected $pdfPath;

    protected $pdfPath2;

    public function __construct($mailData, $pdfPath, $pdfPath2)
    {
        $this->mailData = $mailData;
        $this->pdfPath = $pdfPath;
        $this->pdfPath2 = $pdfPath2;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->mailData['userEmail'], $this->mailData['userName']),
            replyTo: [
                new Address($this->mailData['userEmail'], $this->mailData['userName'])
            ],
            subject: 'Congratulations on your New Chapter!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chapter.newchapterwelcome',
        );
    }

    public function attachments(): array
    {
        $attachments = [
            Attachment::fromPath($this->pdfPath)
                ->as($this->mailData['chapterState'].'_'.$this->mailData['chapterName'].'_ChapterInGoodStanding.pdf')
                ->withMime('application/pdf'),
        ];

        if ($this->pdfPath2) {
            $pdfContent = file_get_contents($this->pdfPath2);
            if ($pdfContent !== false) {
                $attachments[] = Attachment::fromData(
                    fn() => $pdfContent,
                    'GroupExemptionLetter.pdf'
                )->withMime('application/pdf');
            }
        }

        return $attachments;
    }

}
