<?php

namespace App\Console\Commands;

use App\Services\PaymentReminderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendChapterReRegistrationLateReminders extends Command
{
    protected $signature = 'reminders:reregistration-late';

    protected $description = 'Send re-registration late notice emails to chapters overdue as of last month';

    public function handle(PaymentReminderService $service): int
    {
        try {
            $result = $service->sendLateReRegistrationReminders();
            $this->info($result['message']);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->error('Failed to send reminders: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
