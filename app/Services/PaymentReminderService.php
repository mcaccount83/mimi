<?php

namespace App\Services;

use App\Mail\PaymentsReRegLate;
use App\Mail\PaymentsReRegReminder;
use App\Models\Chapters;
use App\Http\Controllers\UserController;
use App\Services\PositionConditionsService;
use Illuminate\Support\Facades\Mail;

class PaymentReminderService
{
    public function __construct(
        protected UserController $userController,
        protected PositionConditionsService $positionConditionsService,
    ) {}

    /**
     * Send re-registration reminders to chapters due this month, across all conferences.
     */
    public function sendReRegistrationReminders(): array
    {
        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentDate = $dateOptions['currentDate'];
        $currentYear = $dateOptions['currentYear'];
        $currentMonth = $dateOptions['currentMonth'];
        $currentMonthWords = $dateOptions['currentMonthWords'];

        $rangeEndDate = $currentDate->copy()->subMonth()->endOfMonth();
        $rangeStartDate = $rangeEndDate->copy()->startOfMonth()->subYear()->addMonth();

        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        $chapters = Chapters::with(['state.conference'])
            ->where('start_month_id', $currentMonth)
            ->where('next_renewal_year', $currentYear)
            ->where('active_status', 1)
            ->get();

        if ($chapters->isEmpty()) {
            return ['status' => 'info', 'message' => 'There are no Chapters with Registrations Due.'];
        }

        $sentCount = 0;

        foreach ($chapters as $chapter) {
            $chapterName = $chapter->name;
            if (! $chapterName) {
                continue;
            }

            $confId = $chapter->state->conference->id ?? null;
            $stateShortName = $chapter->state->state_short_name;

            $emailData = $this->userController->loadEmailDetails($chapter->id);
            $to_email = $emailData['emailListChap'] ?? [];
            $cc_email = $emailData['emailListCoord'] ?? [];

            if (empty($to_email)) {
                continue;
            }

            $data = [
                'chapterName' => $chapterName,
                'chapterState' => $stateShortName,
                'confId' => $confId,
                'startRange' => $rangeStartDateFormatted,
                'endRange' => $rangeEndDateFormatted,
                'startMonth' => $currentMonthWords,
            ];

            Mail::to($to_email)
                ->cc($cc_email)
                ->queue(new PaymentsReRegReminder($data));

            $sentCount++;
        }

        return ['status' => 'success', 'message' => "Re-Registration Reminders sent to {$sentCount} chapter(s)."];
    }

    /**
     * Send re-registration LATE reminders to chapters overdue as of last month, across all conferences.
     */
    public function sendLateReRegistrationReminders(): array
    {
        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentDate = $dateOptions['currentDate'];
        $currentYear = $dateOptions['currentYear'];
        $currentMonth = $dateOptions['currentMonth'];
        $lastMonth = $dateOptions['lastMonth'];
        $currentMonthWords = $dateOptions['currentMonthWords'];
        $lastMonthWords = $dateOptions['lastMonthWords'];

        if ($currentMonth == '01' && $lastMonth == '12') {
            $currentYear = $currentYear - 1;
        }

        $rangeEndDate = $currentDate->copy()->subMonths(2)->endOfMonth();
        $rangeStartDate = $rangeEndDate->copy()->startOfMonth()->subYear()->addMonth();

        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        $chapters = Chapters::with(['state.conference'])
            ->where('start_month_id', $lastMonth)
            ->where('next_renewal_year', $currentYear)
            ->where('active_status', 1)
            ->get();

        if ($chapters->isEmpty()) {
            return ['status' => 'info', 'message' => 'There are no Chapters with Registrations Due.'];
        }

        $sentCount = 0;

        foreach ($chapters as $chapter) {
            $chapterName = $chapter->name;
            if (! $chapterName) {
                continue;
            }

            $confId = $chapter->state->conference->id ?? null;
            $stateShortName = $chapter->state->state_short_name;

            $emailData = $this->userController->loadEmailDetails($chapter->id);
            $to_email = $emailData['emailListChap'] ?? [];
            $cc_email = $emailData['emailListCoord'] ?? [];

            if (empty($to_email)) {
                continue;
            }

            $data = [
                'chapterName' => $chapterName,
                'chapterState' => $stateShortName,
                'confId' => $confId,
                'startRange' => $rangeStartDateFormatted,
                'endRange' => $rangeEndDateFormatted,
                'startMonth' => $lastMonthWords,
                'dueMonth' => $currentMonthWords,
            ];

            Mail::to($to_email)
                ->cc($cc_email)
                ->queue(new PaymentsReRegLate($data));

            $sentCount++;
        }

        return ['status' => 'success', 'message' => "Re-Registration Late Reminders sent to {$sentCount} chapter(s)."];
    }
}
