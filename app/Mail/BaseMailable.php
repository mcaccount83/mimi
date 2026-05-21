<?php

// app/Mail/BaseMailable.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

#[Tries(3)]
#[Backoff([60, 300, 900])]
abstract class BaseMailable extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, IsMonitored, Queueable, SerializesModels;

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Email permanently failed after all retries', [
            'email_class' => static::class,
            'exception' => $exception->getMessage(),
            'attempts' => $this->attempts(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
