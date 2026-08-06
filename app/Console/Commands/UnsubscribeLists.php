<?php

namespace App\Console\Commands;

use App\Models\AdminYear;
use App\Http\Controllers\ForumSubscriptionController;
use App\Http\Controllers\TechReportController;
use App\Services\PositionConditionsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnsubscribeLists extends Command
{
    protected $signature = 'moms:unsubscribe-lists';
    protected $description = 'Unsubscribes the outgoing board from the board list and public announcements.';

    public function handle(
        PositionConditionsService $positionConditionsService,
        ForumSubscriptionController $forumSubscriptionController,
        TechReportController $techReportController
    ): int {
        $dateOptions = $positionConditionsService->getDateOptions();
        $currentYear = $dateOptions['currentYear'];

        $fiscalYearOptions = $positionConditionsService->getFiscalYearOptions();
        $fiscalYearId = $fiscalYearOptions['fiscalYearId'];

        DB::beginTransaction();
        try {
            $techReportController->resetBoardList($currentYear);

            // $forumSubscriptionController->bulkRemoveBoardBoardList();
            $forumSubscriptionController->bulkRemoveBoardPublicAnnouncements();
            // $forumSubscriptionController->bulkRemoveCoordinatorsBoardList();
            // $forumSubscriptionController->bulkRemoveCoordinatorsPublicAnnounceements();
            $forumSubscriptionController->bulkRemoveBoardList();

            $adminYear = AdminYear::where('fiscal_year_id', $fiscalYearId)->firstOrFail();
            $adminYear->update([
                'unsubscribe_list' => 1,
                'boardlist_active' => null,
            ]);

            DB::commit();
            $this->info('Successfully unsubscribed from lists.');

            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->error('An error occurred while unsubscribing from lists.');

            return self::FAILURE;
        }
    }
}
