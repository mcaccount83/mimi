<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class PaymentsReRegReceipt extends Mailable implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, IsMonitored, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->from('support@momsclub.org', 'MOMS Club')
            ->subject('Thank You for Your Re-Registration Payment')
            ->markdown('emails.payments.reregreceipt');
    }
}
