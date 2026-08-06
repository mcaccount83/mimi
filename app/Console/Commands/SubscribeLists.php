<?php

namespace App\Console\Commands;

use App\Models\AdminYear;
use App\Http\Controllers\ForumSubscriptionController;
use App\Services\PositionConditionsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscribeLists extends Command
{
    protected $signature = 'moms:subscribe-lists';
    protected $description = 'Subscribes the new board to the board list and public announcements.';

    public function handle(
        PositionConditionsService $positionConditionsService,
        ForumSubscriptionController $forumSubscriptionController
    ): int {
        $fiscalYearOptions = $positionConditionsService->getFiscalYearOptions();
        $fiscalYearId = $fiscalYearOptions['fiscalYearId'];

        DB::beginTransaction();
        try {
            // $forumSubscriptionController->bulkAddBoardBoardList();
            // $forumSubscriptionController->bulkAddBoardPublicAnnouncements();
            // $forumSubscriptionController->bulkAddCoordinatorsBoardList();
            // $forumSubscriptionController->bulkAddCoordinatorsPublicAnnounceements();
            $forumSubscriptionController->bulkAddBoardList();
            $forumSubscriptionController->bulkAddPublicAnnouncements();

            $adminYear = AdminYear::where('fiscal_year_id', $fiscalYearId)->firstOrFail();
            $adminYear->update([
                'subscribe_list' => 1,
                'boardlist_active' => 1,
            ]);

            DB::commit();
            $this->info('Successfully subscribed to lists.');

            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->error('An error occurred while subscribing to lists.');

            return self::FAILURE;
        }
    }
}
