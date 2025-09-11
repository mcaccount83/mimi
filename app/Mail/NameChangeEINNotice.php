<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NameChangeEINNotice extends BaseMailable
{
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
            subject: "Chapter Name Change Notification | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.chapter.namechangeeinnotice',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as($this->mailData['chapterState'].'_'.$this->mailData['chapterNameSanitized'].'_ChapterNameChange.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
