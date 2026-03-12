<?php

namespace App\Http\Controllers;

use App\Enums\CoordinatorPosition;
use App\Enums\ForumCategoryEnum;
use App\Enums\UserStatusEnum;
use App\Enums\UserTypeEnum;
use App\Models\Admin;
use App\Models\AdminEmail;
use App\Models\AdminYear;
use App\Models\Boards;
use App\Models\BoardsDisbanded;
use App\Models\BoardsIncoming;
use App\Models\BoardsOutgoing;
use App\Models\BoardsPending;
use App\Models\Chapters;
use App\Models\ChapterAwardHistory;
use App\Models\Conference;
use App\Models\CoordinatorApplication;
use App\Models\CoordinatorRecognition;
use App\Models\Coordinators;
use App\Models\CoordinatorTree;
use App\Models\Documents;
use App\Models\DocumentsEOY;
use App\Models\FinancialReport;
use App\Models\FinancialReportReview;
use App\Models\ForumCategorySubscription;
use App\Models\GoogleDrive;
use App\Models\ProbationSubmission;
use App\Models\Region;
use App\Models\State;
use App\Models\User;
use App\Services\PositionConditionsService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use TeamTeaTime\Forum\Models\Category as ForumCategory;

class TechReportController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseChapterController;

    protected $baseBoardController;

    protected $baseCoordinatorController;

    protected $positionConditionsService;

    protected $forumSubscriptionController;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, BaseCoordinatorController $baseCoordinatorController,
        PositionConditionsService $positionConditionsService, BaseBoardController $baseBoardController, ForumSubscriptionController $forumSubscriptionController)
    {
        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->baseCoordinatorController = $baseCoordinatorController;
        $this->positionConditionsService = $positionConditionsService;
        $this->baseBoardController = $baseBoardController;
        $this->forumSubscriptionController = $forumSubscriptionController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
                        \App\Http\Middleware\SetViewAsSession::class,

        ];
    }

    /**
     * Admin Choose Active Chapter for Viewing
     */
    public function listActiveChapters(Request $request): View
    {
        // Simulate the check5=yes parameter to get international chapters
        $_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL] = 'yes';

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);

        $chapters = $baseQuery['query']->get();

        // Clean up the simulated parameter
        unset($_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL]);

        $countList = count($chapters);
        $data = ['countList' => $countList, 'chapters' => $chapters];

        return view('coordinators.techreports.chapterlist')->with($data);
    }

    public function viewAsActiveChapter(Request $request): View
{
    $_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL] = 'yes';

    $user = $this->userController->loadUserInformation($request);
    $userTypeId = $user['userTypeId'];
    $coorId = $user['cdId'];
    $confId = $user['confId'];
    $regId = $user['regId'];
    $positionId = $user['cdPositionId'];
    $secPositionId = $user['cdSecPositionId'];

    $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
    $chapters = $baseQuery['query']->get();

    $chapterBdData = [];
    foreach ($chapters as $chapter) {
        $chDetails = $this->baseBoardController->getChapterDetails($chapter->id);
        $bdData = $this->positionConditionsService->getViewAs($userTypeId, $chDetails['PresDetails']);
        $chapterBdData[$chapter->id] = $bdData;
    }

    unset($_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL]);

    return view('coordinators.techreports.viewasactivechapter')->with([
        'countList'     => count($chapters),
        'chapters'      => $chapters,
        'chapterBdData' => $chapterBdData,
        'userTypeId'    => $userTypeId,
    ]);
}

   public function viewAsDisbandedChapter(Request $request): View
{
    $_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL] = 'yes';

    $user = $this->userController->loadUserInformation($request);
    $userTypeId = $user['userTypeId'];
    $coorId = $user['cdId'];
    $confId = $user['confId'];
    $regId = $user['regId'];
    $positionId = $user['cdPositionId'];
    $secPositionId = $user['cdSecPositionId'];

    $baseQuery = $this->baseChapterController->getBaseQuery(0, $coorId, $confId, $regId, $positionId, $secPositionId);
    $chapters = $baseQuery['query']->get();

    $chapterBdData = [];
    foreach ($chapters as $chapter) {
        $chDetails = $this->baseBoardController->getChapterDetails($chapter->id);
        $bdData = $this->positionConditionsService->getViewAs($userTypeId, $chDetails['PresDetails']);
        $chapterBdData[$chapter->id] = $bdData;
    }

    unset($_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL]);

    return view('coordinators.techreports.viewasdisbandedchapter')->with([
        'countList'     => count($chapters),
        'chapters'      => $chapters,
        'chapterBdData' => $chapterBdData,
        'userTypeId'    => $userTypeId,
    ]);
}
   public function viewAsPendingChapter(Request $request): View
{
    $_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL] = 'yes';

    $user = $this->userController->loadUserInformation($request);
    $userTypeId = $user['userTypeId'];
    $coorId = $user['cdId'];
    $confId = $user['confId'];
    $regId = $user['regId'];
    $positionId = $user['cdPositionId'];
    $secPositionId = $user['cdSecPositionId'];

    $baseQuery = $this->baseChapterController->getBaseQuery(2, $coorId, $confId, $regId, $positionId, $secPositionId);
    $chapters = $baseQuery['query']->get();

    $chapterBdData = [];
    foreach ($chapters as $chapter) {
        $chDetails = $this->baseBoardController->getChapterDetails($chapter->id);
        $bdData = $this->positionConditionsService->getViewAs($userTypeId, $chDetails['PresDetails']);
        $chapterBdData[$chapter->id] = $bdData;
    }

    unset($_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL]);

    return view('coordinators.techreports.viewaspendingchapter')->with([
        'countList'     => count($chapters),
        'chapters'      => $chapters,
        'chapterBdData' => $chapterBdData,
        'userTypeId'    => $userTypeId,
    ]);
}
   public function viewAsOutgoingChapter(Request $request): View
{
    $_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL] = 'yes';

    $user = $this->userController->loadUserInformation($request);
    $userTypeId = $user['userTypeId'];
    $coorId = $user['cdId'];
    $confId = $user['confId'];
    $regId = $user['regId'];
    $positionId = $user['cdPositionId'];
    $secPositionId = $user['cdSecPositionId'];

    $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
    $chapters = $baseQuery['query']->get();

    $chapterBdData = [];
    foreach ($chapters as $chapter) {
        $chDetails = $this->baseBoardController->getChapterDetails($chapter->id);
        $bdData = $this->positionConditionsService->getViewAs($userTypeId, $chDetails['PresDetails']);
        $chapterBdData[$chapter->id] = $bdData;
    }

    unset($_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL]);

    return view('coordinators.techreports.viewasoutgoingchapter')->with([
        'countList'     => count($chapters),
        'chapters'      => $chapters,
        'chapterBdData' => $chapterBdData,
        'userTypeId'    => $userTypeId,
    ]);
}
    /**
     * Admin Choose Zapped Chapter for Viewing
     */
    public function listZappedChapters(Request $request): View
    {
        // Simulate the check5=yes parameter to get international chapters
        $_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL] = 'yes';

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(0, $coorId, $confId, $regId, $positionId, $secPositionId);

        $chapters = $baseQuery['query']->get();

        // Clean up the simulated parameter
        unset($_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL]);

        $countList = count($chapters);
        $data = ['countList' => $countList, 'chapters' => $chapters];

        return view('coordinators.techreports.chapterlistzapped')->with($data);
    }

    /**
     * Admin Choose Pending Chapter for Viewing
     */
    public function listPendingChapters(Request $request): View
    {
        // Simulate the check5=yes parameter to get international chapters
        $_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL] = 'yes';

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(2, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapters = $baseQuery['query']->get();

        // Clean up the simulated parameter
        unset($_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL]);

        $countList = count($chapters);
        $data = ['countList' => $countList, 'chapters' => $chapters];

        return view('coordinators.techreports.chapterlistpending')->with($data);
    }

    /**
     * Reset Quarterly Report Data
     */
    public function resetProbationSubmission(): JsonResponse
    {
        DB::beginTransaction();
        try {
            ProbationSubmission::query()->delete();

            $probationPartyChapters = Chapters::with('probation')
                ->where('active_status', 1)
                ->where('probation_id', 3)
                ->get();
            foreach ($probationPartyChapters as $chapter) {
                ProbationSubmission::create([
                    'chapter_id' => $chapter->id,
                ]);
            }

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Quarterly Report Data reset successfully.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Show EOY Procedures
     */
    public function showEOY(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];
        $canEditFiles = ($positionId == CoordinatorPosition::IT || in_array(CoordinatorPosition::IT, $secPositionId));

        // $admin = DB::table('admin')
        //     ->select('admin.*',
        //         DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'), )
        //     ->leftJoin('coordinators as cd', 'admin.updated_id', '=', 'cd.id')
        //     ->orderByDesc('admin.id') // Assuming 'id' represents the order of insertion
        //     ->first();

        // Fetch distinct fiscal years
        // $fiscalYears = DB::table('admin_year')->distinct()->pluck('year_fiscal');
        // $fiscalYearsEOY = DB::table('admin')->distinct()->pluck('fiscal_year_eoy');

        $adminYear = AdminYear::latest('id')->firstOrFail();
        $fiscalYear = $adminYear->year_fiscal;  // "2025-2026"

        $admin = Admin::latest('id')->firstOrFail();
        $fiscalYearEOY = $admin->fiscal_year_eoy;  // "2024-2025"

        $subscribeListItems = [
            // 'Subscribe Coordinators to BoardList',
            'Subscribe Board Members to BoardList',
            'Subscribe Board Members to Public Announcements',
        ];

        $resetEOYTableItems = [
            'Set all Outgoing Users to Inactive',
            'Clear Outgoing Board Member Table',
            'Clear Incoming Board Member Table',
            'Ending Balance Added to Documents from Financial Reports',
            'Approved Awards Added to Awards History from Financial Reports',
            'Copy/Rename Financial Report & Review Tables',
            'Clear Financial Report & Review Tables',
            'Pre-Balance Added to Financial Report & Review from Documents',
            'EOY Fields Reset in Chapters Table',
            'EOY Fields Reset in Documents Table',
            'Add Outgoing Board Members from Board Details',
            'Update Google Shared Drive to new year for Attachmnet Uploads',
        ];

        $displayTestingItemsItems = [
            'Display EOY Dashboard Menu Items for testers',
            'Display Board Election Report Button  for testers',
            'Display Financal Report Button for testers',
        ];

        $displayLiveItemsItems = [
            'Display EOY Dashboard Menu Items for all Coordinators',
            'Display Board Election Report Button for Board Members after May 1st',
            'Display Financal Report Button for Board Members after June 1st',
        ];

        $displayCoorindatorMenuItems = [
            'Display EOY Dashboard Menu Items for testers',
            'Display EOY Chapter Profile Buttons for testers',
            'Financial Report for Chapters - button will be available after June 1st.',
        ];

        $displayCoorindatorMenuItems = [
            'Display EOY Dashboard Menu Items for testers',
            'Display EOY Chapter Profile Buttons for testers',
            'Financial Report for Chapters - button will be available after June 1st.',
        ];

        $displayChapterButtonItems = [
            'Board Election Report for Chapters - button will be available after May 1st.',
            'Report Menu for Coordinators - menu will be available after May 1st.',
        ];

        $resetAFTERtestingItems = [
            'Ending Balance Added to Documents from Last Year Financial Reports',
            'Clear Outgoing Board Member Table',
            'Clear Incoming Board Member Table',
            'Clear Financial Reports Table',
            'Pre-Balance Added to Financial Report from Documents',
            'EOY Fields Reset in Chapters Table',
            'EOY Fields Reset in Documents Table',
            'Update Outgoing Board Members from Board Details',
        ];

        $updateUserTablesItems = [
            'Copy/Rename Chapters Table',
            'Copy/Rename Boards Table',
            'Copy/Rename Coordinators Table',
            'Copy/Rename Users Table',
            'Copy/Reset BoardList Forum Category',
        ];

        $unSubscribeListItems = [
            'Remove Board Members from BoardList',
            'Remove Board Members from Publc Announcements',
            // 'Remove Coordinators from BoardList',
        ];

        $data = ['admin' => $admin, 'adminYear' => $adminYear, 'canEditFiles' => $canEditFiles, 'fiscalYearEOY' => $fiscalYearEOY, 'fiscalYear' => $fiscalYear,
            'resetEOYTableItems' => $resetEOYTableItems, 'displayCoorindatorMenuItems' => $displayCoorindatorMenuItems, 'displayChapterButtonItems' => $displayChapterButtonItems,
            'displayTestingItemsItems' => $displayTestingItemsItems, 'displayLiveItemsItems' => $displayLiveItemsItems, 'unSubscribeListItems' => $unSubscribeListItems,
            'resetAFTERtestingItems' => $resetAFTERtestingItems, 'updateUserTablesItems' => $updateUserTablesItems, 'subscribeListItems' => $subscribeListItems,
        ];

        return view('coordinators.techreports.eoy')->with($data);
    }

    /**
     * Reset Disbanded Users to NOT active
     */
    public function resetDisbandedUsers(): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Make disbanded users inactive
            DB::table('users')
                ->where('type_id', UserTypeEnum::DISBANDED)
                ->where('active_status', UserStatusEnum::ACTIVE)
                ->update([
                    'active_status' => UserStatusEnum::INACTIVE,
                ]);

            DB::commit();

            $message = 'Disbanded users successfully updated';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('techreports.disbandedboard'),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('techreports.disbandedboard'),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Reset Outgoing Users to NOT active
     */
    public function resetOutgoingUsers(): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Make outgoing users inactive
            DB::table('users')
                ->where('type_id', UserTypeEnum::OUTGOING)
                ->where('active_status', UserStatusEnum::ACTIVE)
                ->update([
                    'active_status' => UserStatusEnum::INACTIVE,
                ]);

            DB::commit();

            $message = 'Outgoing users successfully updated';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('techreports.outgoingboard'),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('userreports.outgoingboard'),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Reset Fiscal Year in Jul for Subscription Lists  // Step 1
     */
    public function resetYear(): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Create a new Admin instance
            $adminYear = new AdminYear;

            // Calculate the fiscal year (current year - next year)
            $dateOptions = $this->positionConditionsService->getDateOptions();
            $currentYear = $dateOptions['currentYear'];
            $nextYear = $dateOptions['nextYear'];
            $fiscalYear = $currentYear.'-'.$nextYear;

            // Set the fiscal year field
            $adminYear->year_fiscal = $fiscalYear;
            $adminYear->year_start = $currentYear;
            $adminYear->year_end = $nextYear;

            // Save the new entry
            $adminYear->save();

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Fiscal year reset successfully.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Subscribe Users to ForumLists  // Step 2
     */
     public function updateSubscribeLists(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];

        $EOYOptions = $this->positionConditionsService->getEOYOptions();
        $fiscalYear = $EOYOptions['fiscalYear'];

        DB::beginTransaction();
        try {
            $boardListAll = $this->forumSubscriptionController->bulkAddBoardBoardList();
            $publicListBoard = $this->forumSubscriptionController->bulkAddBoardPublicAnnouncements();

            $adminYear = AdminYear::where('year_fiscal', $fiscalYear)->firstOrFail();
            $adminYear->update([
                'subscribe_list' => 1,
                'updated_id'       => $updatedId,
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Successfully subscribed to lists.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }


            // // Get BoardList category
            // $categoryBoardList = ForumCategory::where('title', 'BoardList')
            //     ->first();
            // $categoryIdBoardList = $categoryBoardList->id;

            // // Get Public Announcement category
            // $categoryPublic = ForumCategory::where('title', 'Public Announcements')
            //     ->first();
            // $categoryIdPublic = $categoryPublic->id;

            // // Get active coordinators
            // $coordinatorUserIds = Coordinators::where('active_status', '1')
            //     ->where('on_leave', '0')
            //     ->get()
            //     ->pluck('user_id')
            //     ->unique();
            // $activeCoordinators = User::whereIn('id', $coordinatorUserIds)->get();

            // // Get board members from active chapters using with()
            // $boardUserIds = Chapters::with('boards')
            //     ->where('active_status', 1)
            //     ->get()
            //     ->pluck('boards')
            //     ->flatten()
            //     ->pluck('user_id')
            //     ->unique();
            // $activeBoards = User::whereIn('id', $boardUserIds)->get();

            // // Combine the collections
            // $allUsers = $activeCoordinators->concat($activeBoards);

            // // Remove duplicates if a user is both coordinator and board member
            // $uniqueUsers = $allUsers->unique('id');

            // foreach ($uniqueUsers as $user) {
            //     // Check if subscription already exists
            //     $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
            //         ->where('category_id', ForumCategoryEnum::BOARDLIST)
            //         ->first();

            //     if (! $existingSubscription) {
            //         ForumCategorySubscription::create([
            //             'user_id' => $user->id,
            //             'category_id' => ForumCategoryEnum::BOARDLIST,
            //         ]);
            //     }
            // }

            // foreach ($activeBoards as $user) {
            //     // Check if subscription already exists
            //     $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
            //         ->where('category_id', ForumCategoryEnum::PUBLICLIST)
            //         ->first();

            //     if (! $existingSubscription) {
            //         ForumCategorySubscription::create([
            //             'user_id' => $user->id,
            //             'category_id' => ForumCategoryEnum::PUBLICLIST,
            //         ]);
            //     }
            // }


    /**
     * Reset Fiscal Year EOY Files/Docs/Etc  // Step 3
     */
    public function resetYearEOY(): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Create a new Admin instance
            $admin = new Admin;

            // Calculate the fiscal year EOY
            $dateOptions = $this->positionConditionsService->getDateOptions();
            $lastYear = $dateOptions['lastYear'];
            $currentYear = $dateOptions['currentYear'];
            $fiscalYearEOY = $lastYear.'-'.$currentYear;

            // Update the fiscal_year_eoy field on the existing entry
            $admin->fiscal_year_eoy = $fiscalYearEOY;
            $admin->save();

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'EOY Fiscal year reset successfully.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Udate EOY Database Tables  // Step 4
     */
    public function updateEOYDatabase(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $EOYOptions = $this->positionConditionsService->getEOYOptions();
        $fiscalYearEOY = $EOYOptions['fiscalYearEOY'];

        $this->addFinancialPdfColumn($EOYOptions);
        $this->archiveFinancialTables($EOYOptions);

        DB::beginTransaction();
        try {
            $this->deactivateOutgoingUsers();
            $this->clearBoardTables();
            $this->updateChapterPreBalances();
            $this->copyAwardsToHistory($EOYOptions, $updatedId, $updatedBy);
            $this->resetFinancialReports($EOYOptions);
            $this->resetChapterFlags();
            $this->resetDocumentsEOY();
            $this->updateGoogleDriveYear($EOYOptions);
            // $this->markAdminEOYComplete($EOYOptions, $updatedId);

            $admin = Admin::where('fiscal_year_eoy', $fiscalYearEOY)->firstOrFail();
            $admin->update([
                'reset_eoy_tables' => 1,
                'updated_id'       => $updatedId,
            ]);

            DB::commit();

            return response()->json(['success' => 'Data tables successfully updated, copied, and renamed.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    private function addFinancialPdfColumn(array $EOYOptions): void
    {
        $newColumnName   = $EOYOptions['thisYearEOY'] . '_financial_pdf_path';
        $afterColumnName = $EOYOptions['lastYearEOY'] . '_financial_pdf_path';

        Schema::table('documents_eoy', function (Blueprint $table) use ($newColumnName, $afterColumnName) {
            $table->string($newColumnName, 255)->nullable()->after($afterColumnName);
        });
    }

     private function archiveFinancialTables(array $EOYOptions): void
    {
        $lastYearEOY = $EOYOptions['lastYearEOY'];

        DB::statement("CREATE TABLE zzz_financial_report_12_$lastYearEOY LIKE financial_report");
        DB::statement("INSERT INTO zzz_financial_report_12_$lastYearEOY SELECT * FROM financial_report");
        DB::statement("CREATE TABLE zzz_financial_report_review_12_$lastYearEOY LIKE financial_report_review");
        DB::statement("INSERT INTO zzz_financial_report_review_12_$lastYearEOY SELECT * FROM financial_report_review");
    }

    private function deactivateOutgoingUsers(): void
    {
        User::where('type_id', UserTypeEnum::OUTGOING)
        ->where('is_active', UserStatusEnum::ACTIVE)
        ->update(['is_active' => UserStatusEnum::INACTIVE]);
    }

    private function clearBoardTables(): void
    {
        BoardsOutgoing::query()->delete();
        BoardsIncoming::query()->delete();
    }

    private function updateChapterPreBalances(): void
    {
        $chapters = Chapters::with('financialReportReview', 'documentsEOY')->get();
        foreach ($chapters as $chapter) {
            if ($chapter->financialReportReview && $chapter->documentsEOY) {
                $chapter->documentsEOY->pre_balance = $chapter->financialReportReview->post_balance;
                $chapter->documentsEOY->save();
            }
        }
    }

    private function copyAwardsToHistory(array $EOYOptions, int $updatedId, string $updatedBy): void
    {
        $fiscalYearEOY = $EOYOptions['fiscalYearEOY'];

        $allChapters = FinancialReport::whereNotNull('chapter_awards')->get();
        foreach ($allChapters as $report) {
            $awards = unserialize(base64_decode($report->chapter_awards));
            if (!is_array($awards)) continue;

            foreach ($awards as $award) {
                if (!empty($award['awards_approved'])) {
                    ChapterAwardHistory::firstOrCreate(
                        [
                            'chapter_id'  => $report->chapter_id,
                            'awards_type' => $award['awards_type'],
                            'award_year'  => $fiscalYearEOY,
                        ],
                        [
                            'awards_desc' => $award['awards_desc'],
                            'approved_at' => now(),
                            'approved_by' => $updatedBy,
                            'approved_id' => $updatedId,
                        ]
                    );
                }
            }
        }
    }

    private function resetFinancialReports(array $EOYOptions): void
    {
        FinancialReport::query()->delete();
        FinancialReportReview::query()->delete();

        $activeChapters = Chapters::with('documentsEOY')->where('active_status', 1)->get();
        foreach ($activeChapters as $chapter) {
            FinancialReport::create([
                'chapter_id'                         => $chapter->id,
                'pre_balance'                        => $chapter->documentsEOY->pre_balance,
                'amount_reserved_from_previous_year' => $chapter->documentsEOY->pre_balance,
            ]);
            FinancialReportReview::create([
                'chapter_id'  => $chapter->id,
                'pre_balance' => $chapter->documentsEOY->pre_balance,
            ]);
        }
    }

    private function resetChapterFlags(): void
    {
        Chapters::query()->update([
            'boundary_issues'         => null,
            'boundary_issue_notes'    => null,
            'boundary_issue_resolved' => null,
        ]);
    }

    private function resetDocumentsEOY(): void
    {
        DocumentsEoy::query()->update([
            'irs_verified'              => null,
            'irs_issues'                => null,
            'irs_wrongdate'             => null,
            'irs_notfound'              => null,
            'irs_filedwrong'            => null,
            'irs_notified'              => null,
            'new_board_submitted'       => null,
            'new_board_active'          => null,
            'financial_report_received' => null,
            'report_received'           => null,
            'financial_review_complete' => null,
            'review_complete'           => null,
            'report_notes'              => null,
            'report_extension'          => null,
            'extension_notes'           => null,
            'roster_path'               => null,
            'irs_path'                  => null,
            'statement_1_path'          => null,
            'statement_2_path'          => null,
            'award_path'                => null,
        ]);
    }

    private function updateGoogleDriveYear(array $EOYOptions): void
    {
        GoogleDrive::where('name', 'eoy_uploads')
            ->update(['version' => $EOYOptions['thisYearEOY']]);
    }

    /**
     * View EOY Teating Items  // Step 5
     */
    public function updateEOYTesting(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];

        $EOYOptions = $this->positionConditionsService->getEOYOptions();
        $fiscalYearEOY = $EOYOptions['fiscalYearEOY'];

        DB::beginTransaction();
        try {
            $admin = Admin::where('fiscal_year_eoy', $fiscalYearEOY)->firstOrFail();
            $admin->update([
                'display_testing' => 1,
                'updated_id'       => $updatedId,
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Testing items successfully set to view.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    /**
     * Udate EOY Database Tables AFTER Testing  // Step 6
     */
    public function updateEOYDatabaseAFTERTesting(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];

        $EOYOptions = $this->positionConditionsService->getEOYOptions();
        $fiscalYearEOY = $EOYOptions['fiscalYearEOY'];
        $yearColumnName = $EOYOptions['yearColumnName'];

        DB::beginTransaction();
        try {
            $this->updateChapterPostBalancesLIVE();
            $this->clearTablesLIVE();
            $this->updateChapterPreBalancesLIVE();
            $this->resetDocumentsEOYLIVE($yearColumnName);

         $admin = Admin::where('fiscal_year_eoy', $fiscalYearEOY)->firstOrFail();
            $admin->update([
                'reset_AFTER_testing' => 1,
                'updated_id'       => $updatedId,
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Data sucessfully reset.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    private function updateChapterPostBalancesLIVE(): void
    {
        // Fetch all chapters with their financial reports and update the balance BEFORE removing data from table
        $chapters = Chapters::with('financialReportLastYear', 'documentsEOY')->get();
        foreach ($chapters as $chapter) {
            if ($chapter->financialReportLastYear && $chapter->documentsEOY) {
                $documentsEOY = $chapter->documentsEOY;
                $documentsEOY->pre_balance = $chapter->financialReportLastYear->post_balance;
                $documentsEOY->save();
            }
        }
    }

    private function clearTablesLIVE(): void
    {
        BoardsOutgoing::query()->delete();
        BoardsIncoming::query()->delete();
        FinancialReport::query()->delete();
    }

    private function updateChapterPreBalancesLIVE(): void
    {
        // Fetch all active chapters and add balance into financial_report
        $activeChapters = Chapters::with('documentsEOY')->where('active_status', 1)->get();
        foreach ($activeChapters as $chapter) {
            FinancialReport::create([
                'chapter_id' => $chapter->id,  // Ensure chapter_id is provided
                'pre_balance' => $chapter->documentsEOY->pre_balance,
                'amount_reserved_from_previous_year' => $chapter->documentsEOY->pre_balance,
            ]);
        }
    }

    private function resetDocumentsEOYLIVE(string $yearColumnName): void
    {
        // Update chapters table: Set specified columns to NULL
        DB::table('chapters')->update([
            'boundary_issues' => null,
            'boundary_issue_notes' => null,
            'boundary_issue_resolved' => null,
        ]);

        // Update documents table: Set specified columns to NULL
        DB::table('documents_eoy')->update([
            'new_board_submitted' => null,
            'new_board_active' => null,
            'financial_report_received' => null,
            'report_received' => null,
            'financial_review_complete' => null,
            'review_complete' => null,
            'report_notes' => null,
            'report_extension' => null,
            'extension_notes' => null,
            $yearColumnName => null,
            'roster_path' => null,
            'irs_path' => null,
            'statement_1_path' => null,
            'statement_2_path' => null,
            'award_path' => null,
        ]);
    }

    /**
     * Udate User Database Tables  // Step 7
     */
    public function updateDataDatabase(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];

        // Get the current month and year for table renaming
        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentYear = $dateOptions['currentYear'];
        $currentMonth = str_pad($dateOptions['currentMonth'], 2, '0', STR_PAD_LEFT);

        $EOYOptions = $this->positionConditionsService->getEOYOptions();
        $fiscalYearEOY = $EOYOptions['fiscalYearEOY'];

        $this->copyTablesFINAL($currentMonth, $currentYear);

        DB::beginTransaction();
        try {
            $this->resetBoardList($currentYear);

            $admin = Admin::where('fiscal_year_eoy', $fiscalYearEOY)->firstOrFail();
            $admin->update([
                'update_user_tables' => 1,
                'updated_id'       => $updatedId,
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'User data tables successfully updated, copied, and renamed.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    private function copyTablesFINAL(int $currentMonth, int $currentYear): void
    {
        // Copy and rename the `chapters` table
        DB::statement("CREATE TABLE zzz_chapters_{$currentMonth}_{$currentYear} LIKE chapters");
        DB::statement("INSERT INTO zzz_chapters_{$currentMonth}_{$currentYear} SELECT * FROM chapters");

        // Copy and rename the `boards` table
        DB::statement("CREATE TABLE zzz_boards_{$currentMonth}_{$currentYear} LIKE boards");
        DB::statement("INSERT INTO zzz_boards_{$currentMonth}_{$currentYear} SELECT * FROM boards");

        // Copy and rename the `coordinators` table
        DB::statement("CREATE TABLE zzz_coordinators_{$currentMonth}_{$currentYear} LIKE coordinators");
        DB::statement("INSERT INTO zzz_coordinators_{$currentMonth}_{$currentYear} SELECT * FROM coordinators");

        // Copy and rename the `users` table
        DB::statement("CREATE TABLE zzz_users_{$currentMonth}_{$currentYear} LIKE users");
        DB::statement("INSERT INTO zzz_users_{$currentMonth}_{$currentYear} SELECT * FROM users");
    }

      private function resetBoardList(int $currentYear): void
    {
        // Archive forum category 2 threads to a new year-labeled category
        $lastYear = $currentYear - 1;

        // Fetch category 2 to copy its settings
        $category2 = DB::table('forum_categories')->where('id', 2)->first();
        // Get max _lft/_rgt for positioning the new category at the end
        $maxRgt = DB::table('forum_categories')->max('_rgt');

        $newCategoryId = DB::table('forum_categories')->insertGetId([
            'title'                    => "{$lastYear}-{$currentYear} BoardList",
            'description'              => $category2->description,
            'accepts_threads'          => $category2->accepts_threads,
            'newest_thread_id'         => null,
            'latest_active_thread_id'  => null,
            'thread_count'             => $category2->thread_count,
            'post_count'               => $category2->post_count,
            'is_private'               => $category2->is_private,
            'thread_approval_enabled'  => $category2->thread_approval_enabled,
            'post_approval_enabled'    => $category2->post_approval_enabled,
            'created_at'               => now(),
            'updated_at'               => now(),
            '_lft'                     => $maxRgt + 1,
            '_rgt'                     => $maxRgt + 2,
            'parent_id'                => $category2->parent_id,
            'color_light_mode'         => $category2->color_light_mode,
            'color_dark_mode'          => $category2->color_dark_mode,
        ]);

        // Move all threads from category 2 to the new archived category
        DB::table('forum_threads')
            ->where('category_id', 2)
            ->update(['category_id' => $newCategoryId]);

        // Reset category 2 counts (threads moved out, ready for new year)
        DB::table('forum_categories')
            ->where('id', 2)
            ->update([
                'newest_thread_id'        => null,
                'latest_active_thread_id' => null,
                'thread_count'            => 0,
                'post_count'              => 0,
                'updated_at'              => now(),
            ]);
    }

    /**
     * Unsubscribe Users from ForumLists  // Step 8
     */
    public function updateUnsubscribeLists(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];

        $EOYOptions = $this->positionConditionsService->getEOYOptions();
        $fiscalYear = $EOYOptions['fiscalYear'];

        DB::beginTransaction();
        try {
            $boardListAll = $this->forumSubscriptionController->bulkRemoveBoardBoardList();
            $publicListBoard = $this->forumSubscriptionController->bulkRemoveBoardPublicAnnouncements();

            // $categoryBoardList = ForumCategory::where('title', 'BoardList')
            //     ->first();

            // $categoryPublic = ForumCategory::where('title', 'Public Announcements')
            //     ->first();

            // // Delete all subscriptions for this category
            // $unsubscribeBoardList = ForumCategorySubscription::where('category_id', $categoryBoardList->id)
            //     ->delete();

            // // Delete board members for this category
            // $unsubscribePublic = ForumCategorySubscription::where('category_id', $categoryPublic->id)
            //     ->whereHas('user', function ($query) {
            //         $query->where('type_id', UserTypeEnum::BOARD);
            //     })
            //     ->delete();

            $adminYear = AdminYear::where('year_fiscal', $fiscalYear)->firstOrFail();
            $adminYear->update([
                'unsubscribe_list' => 1,
                'updated_id'       => $updatedId,
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Successfully unsubscribed to lists.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    /**
     * View EOY Live Items  // Step 9
     */
    public function updateEOYLive(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $this->userController->loadUserInformation($request);
            $updatedId = $user['userId'];

            // Update admin table: Set specified columns to 1
            DB::table('admin')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'display_live' => '1',
                    'updated_id' => $updatedId,
                ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Live items successfully set to view.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * view Google Drive Shared Folder Ids
     */
    public function googleDriveList(): View
    {
        $driveList = GoogleDrive::get();

        $data = ['driveList' => $driveList];

        return view('coordinators.techreports.googledrive')->with($data);

    }

    public function addGoogleDrive(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'version' => 'nullable|string|max:50',
            'folder_id' => 'required|string|max:255'
        ]);

        try {
            $drive = new GoogleDrive();
            $drive->name = $request->name;
            $drive->description = $request->description;
            $drive->version = $request->version;
            $drive->folder_id = $request->folder_id;
            $drive->save();

            return response()->json([
                'success' => true,
                'message' => 'Google Drive folder added successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Google Drive folder creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error adding folder. Please try again.'
            ], 500);
        }
    }

    public function updateGoogleDrive(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'version' => 'nullable|string|max:50',
            'folder_id' => 'required|string|max:255'
        ]);

        try {
            $drive = GoogleDrive::findOrFail($id);
            $drive->name = $request->name;
            $drive->description = $request->description;
            $drive->version = $request->version;
            $drive->folder_id = $request->folder_id;
            $drive->save();

            return response()->json([
                'success' => true,
                'message' => 'Google Drive folder updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Google Drive folder update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating folder. Please try again.'
            ], 500);
        }
    }

    public function deleteGoogleDrive(Request $request): JsonResponse
    {
        $driveId = $request->input('driveId');

        try {
            DB::beginTransaction();

            // Delete the Google Drive record (update this to your actual table name)
            DB::table('google_drive_new')->where('id', $driveId)->delete();

            DB::commit();

            return response()->json(['success' => 'Drive successfully deleted.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return response()->json(['fail' => 'Something went wrong, Please try again.'], 500);
        }
    }

    /**
     * view Email Addresses not assigned by positionId
     */
    public function adminEmailList(): View
    {
        $emailList = AdminEmail::get();

        $data = ['emailList' => $emailList];

        return view('coordinators.techreports.adminemail')->with($data);

    }

  public function addAdminEmail(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:255',
        'email' => 'required|string|max:255' // Fixed: emial -> email
    ]);

    try {
        $admin = new AdminEmail();
        $admin->name = $request->name;
        $admin->description = $request->description;
        $admin->email = $request->email;
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'System email added successfully!'
        ]);
    } catch (\Exception $e) {
        Log::error('System email creation error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error adding email. Please try again.' // Fixed message
        ], 500);
    }
}

   public function updateAdminEmail(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:255',
        'email' => 'required|string|max:255' // Fixed: emial -> email
    ]);

    try {
        $admin = AdminEmail::findOrFail($id);
        $admin->name = $request->name;
        $admin->description = $request->description;
        $admin->email = $request->email;
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'System email updated successfully!'
        ]);
    } catch (\Exception $e) {
        Log::error('System email update error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error updating email. Please try again.' // Fixed message
        ], 500);
    }
}

    public function deleteAdminEmail(Request $request): JsonResponse
    {
        $emailId = $request->input('emailId');

        try {
            DB::beginTransaction();

            // Delete the Admin Email record
            DB::table('admin_email_new')->where('id', $emailId)->delete();

            DB::commit();

            return response()->json(['success' => 'System email successfully deleted.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return response()->json(['fail' => 'Something went wrong, Please try again.'], 500);
        }
    }

    /**
     * Delete Chapter/Board from Database -- cannot be undone!
     */
    public function updateChapterDelete(Request $request): JsonResponse
    {
        $input = $request->all();
        $chapterid = $input['chapterid'];
        $activeStatus = $input['activeStatus'];

        try {
            DB::beginTransaction();

            // Delete chapter and related data
            Chapters::where('id', $chapterid)->delete();
            Documents::where('chapter_id', $chapterid)->delete();
            DocumentsEOY::where('chapter_id', $chapterid)->delete();
            FinancialReport::where('chapter_id', $chapterid)->delete();

            // Get board details based on status
            $boardDetails = collect(); // Initialize empty collection

            if ($activeStatus == 'Active') {
                $boardDetails = Boards::where('chapter_id', $chapterid)->get();
                Boards::where('chapter_id', $chapterid)->delete(); // Delete from Boards table
            } elseif ($activeStatus == 'Zapped') {
                $boardDetails = BoardsDisbanded::where('chapter_id', $chapterid)->get();
                BoardsDisbanded::where('chapter_id', $chapterid)->delete(); // Delete from BoardsDisbanding table
            } elseif ($activeStatus == 'Pending' || $activeStatus == 'Not Approved') {
                $boardDetails = BoardsPending::where('chapter_id', $chapterid)->get();
                BoardsPending::where('chapter_id', $chapterid)->delete(); // Delete from BoardsPending table
            }

            // Delete users and subscriptions if board members exist
            if ($boardDetails->isNotEmpty()) {
                $userIds = $boardDetails->pluck('user_id')->toArray();
                User::whereIn('id', $userIds)->delete();
                ForumCategorySubscription::whereIn('user_id', $userIds)->delete();
            }

            DB::commit();

            return response()->json(['success' => 'Chapter successfully deleted.']);
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return response()->json(['fail' => 'Something went wrong, Please try again.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Delete Coordinator from Database -- cannot be undone!
     */
    public function updateCoordinatorDelete(Request $request): JsonResponse
    {
        $input = $request->all();
        $coordid = $input['coordid'];
        // Note: You're not using activeStatus in this function, so you might not need it

        try {
            DB::beginTransaction();

            // Delete coordinator and related data
            CoordinatorApplication::where('coordinator_id', $coordid)->delete();
            CoordinatorRecognition::where('coordinator_id', $coordid)->delete();
            CoordinatorTree::where('coordinator_id', $coordid)->delete();

            // Get coordinator details BEFORE deleting the coordinator record
            $coordDetails = Coordinators::where('id', $coordid)->first(); // Use first() instead of get()

            // Get the user_id before deleting the coordinator
            $userId = $coordDetails->user_id;

            // Delete the coordinator record
            Coordinators::where('id', $coordid)->delete();

            // Delete user and related subscriptions
            if ($userId) {
                User::where('id', $userId)->delete(); // Use where() instead of whereIn() for single ID
                ForumCategorySubscription::where('user_id', $userId)->delete(); // Use where() instead of whereIn()
            }

            DB::commit();

            return response()->json(['success' => 'Coordinator successfully deleted.']); // Fixed message
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return response()->json(['fail' => 'Something went wrong, Please try again.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

public function conferenceList(Request $request): View
    {
        $confList = Conference::with([
                'regions' => function ($query) {
                    $query->orderBy('long_name');
                },
                'states' => function ($query) {
                    $query->orderBy('state_short_name');
                }
            ])
            ->orderBy('short_name')
            ->get();

        $data = ['confList' => $confList];

        return view('coordinators.techreports.conferencelist')->with($data);
    }

    public function regionList(Request $request): View
    {
        $regList = Region::with([
                'conference',
                'states' => function ($query) {
                    $query->orderBy('state_short_name');
                }
            ])
            ->join('conference', 'region.conference_id', '=', 'conference.id')
            ->orderBy('conference.short_name')
            ->orderBy('region.long_name')
            ->select('region.*')
            ->get();

        // Get all conferences for dropdown
        $conferenceList = Conference::orderBy('short_name')->get();

        $data = [
            'regList' => $regList,
            'conferenceList' => $conferenceList
        ];

        return view('coordinators.techreports.regionlist')->with($data);
    }

    public function updateRegion(Request $request, $id)
    {
        try {
            $region = Region::findOrFail($id);
            $region->conference_id = $request->conference_id;
            $region->save();

            $conference = Conference::find($request->conference_id);

            return response()->json([
                'success' => true,
                'message' => 'Region conference updated successfully!',
                'conference_name' => $conference->short_name
            ]);
        } catch (\Exception $e) {
                Log::error('Region conference update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating region conference. Please try again.'
            ], 500);
        }
    }

    public function stateList(Request $request): View
    {
        $stateList = State::with('conference', 'region')
            ->orderBy('state_short_name')
            ->get();

        // Get all conferences and regions for dropdowns
        $conferenceList = Conference::orderBy('short_name')->get();
        $regionList = Region::orderBy('long_name')->get();

        $data = [
            'stateList' => $stateList,
            'conferenceList' => $conferenceList,
            'regionList' => $regionList
        ];

        return view('coordinators.techreports.statelist')->with($data);
    }

    public function updateState(Request $request, $id)
    {
        try {
            $state = State::findOrFail($id);

            // Verify that the region belongs to the selected conference
            $region = Region::where('id', $request->region_id)
                ->where('conference_id', $request->conference_id)
                ->first();

            if (!$region) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected region does not belong to the selected conference.'
                ], 400);
            }

            $state->conference_id = $request->conference_id;
            $state->region_id = $request->region_id;
            $state->save();

            return response()->json([
                'success' => true,
                'message' => 'State assignment updated successfully!',
                'conference_name' => $region->conference->short_name,
                'region_name' => $region->long_name
            ]);
        } catch (\Exception $e) {
                Log::error('State assignment update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()  // Return actual error for debugging
            ], 500);
        }
    }

}
