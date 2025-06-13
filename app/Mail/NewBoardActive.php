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

class NewBoardActive extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, IsMonitored, Queueable, SerializesModels;

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
        // Download the Google Drive file first
        $content = file_get_contents($this->pdfPath);

   // For local files, use the filesystem path directly
    $logoPath = ltrim(config('settings.base_url'), '/') . 'images/logo-mc.png';

    // Check if it's a local environment
    if (app()->environment('local') || str_contains(config('app.url'), 'localhost')) {
        // Use direct file access for local
        if (file_exists($logoPath)) {
            $logoContent = file_get_contents($logoPath);
        } else {
            throw new \Exception("Logo file not found at: " . $logoPath);
        }
    } else {
        // Use HTTP request for live/production
        $logoUrl = config('app.url') . config('settings.base_url') . 'images/logo-mc.png';
        $logoContent = file_get_contents($logoUrl);
    }




        return $this
            ->subject('Welcome to the Executive Board!')
            ->markdown('emails.chapter.newboardactive')
            ->attachData($content, 'OfficerPacket.pdf', [
                'mime' => 'application/pdf',
            ])
            ->attachData($logoContent, 'logo-mc.png', [
                'mime' => 'image/png',
            ]);

    }
}
