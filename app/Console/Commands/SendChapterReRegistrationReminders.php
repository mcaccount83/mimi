<?php

namespace App\Console\Commands;

use App\Services\PaymentReminderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendChapterReRegistrationReminders extends Command
{
    protected $signature = 'reminders:reregistration';

    protected $description = 'Send re-registration reminder emails to chapters due this month';

    public function handle(PaymentReminderService $service): int
    {
        try {
            $result = $service->sendReRegistrationReminders();
            $this->info($result['message']);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->error('Failed to send reminders: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
