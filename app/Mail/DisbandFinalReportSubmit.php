<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class DisbandFinalReportSubmit extends Mailable implements ShouldQueue
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
        return $this
        ->subject("Final Financial Report Submitted | {$this->mailData['chapterName']}, {$this->mailData['chapterState']}")
        ->markdown('emails.disband.finalreportsubmitted')
            ->attach($this->pdfPath, [
                'as' => $this->mailData['chapterState'].'_'.$this->mailData['chapterNameSanitized'].'_Final_FinancialReport.pdf',
                'mime' => 'application/pdf',
            ]);

    }
}
