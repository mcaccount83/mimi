<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class EOYFinancialReportThankYou extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, IsMonitored, Queueable, SerializesModels;

    public $mailData;

    public $coordinator_array;

    protected $pdfPath;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData, $coordinator_array, $pdfPath)
    {
        $this->mailData = $mailData;
        $this->coordinator_array = $coordinator_array;
        $this->pdfPath = $pdfPath;

    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject('Financial Report Submitted')
            ->markdown('emails.endofyear.financialreportthankyou')
            ->attach($this->pdfPath, [
                'as' => date('Y') - 1 .'-'.date('Y').'_'.$this->mailData['chapter_state'].'_'.$this->mailData['chapter_name'].'_FinancialReport.pdf',
                'mime' => 'application/pdf',
            ]);

    }
}
