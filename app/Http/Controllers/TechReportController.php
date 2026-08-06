<?php

namespace App\Http\Controllers;

use App\Enums\CheckboxFilterEnum;
use App\Enums\CoordinatorPosition;
use App\Enums\ProbationReasonEnum;
use App\Enums\ForumCategoryEnum;
use App\Enums\UserStatusEnum;
use App\Enums\UserTypeEnum;
use App\Http\Requests\AddAdminEmailTechReportRequest;
use App\Http\Requests\AddGoogleDriveTechReportRequest;
use App\Http\Requests\UpdateAdminEmailTechReportRequest;
use App\Http\Requests\UpdateGoogleDriveTechReportRequest;
use App\Models\Admin;
use App\Models\AdminEmail;
use App\Models\AdminIRS;
use App\Models\AdminReport;
use App\Models\AdminYear;
use App\Models\Boards;
use App\Models\BoardsDisbanded;
use App\Models\BoardsIncoming;
use App\Models\BoardsOutgoing;
use App\Models\BoardsPending;
use App\Models\ChapterAwardHistory;
use App\Models\Chapters;
use App\Models\Conference;
use App\Models\CoordinatorApplication;
use App\Models\CoordinatorRecognition;
use App\Models\Coordinators;
use App\Models\CoordinatorTree;
use App\Models\Documents;
use App\Models\DocumentsEOY;
use App\Models\DocumentsIRS;
use App\Models\DocumentsReport;
use App\Models\FinancialReport;
use App\Models\FinancialReportQuestions;
use App\Models\FinancialReportReview;
use App\Models\FiscalYear;
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

class TechReportController extends Controller implements HasMiddleware
{
    public function __construct(
        protected UserController $userController,
        protected PositionConditionsService $positionConditionsService,
        protected BaseChapterController $baseChapterController,
        protected BaseCoordinatorController $baseCoordinatorController,
        protected BaseBoardController $baseBoardController,
        protected ForumSubscriptionController $forumSubscriptionController,
    ) {}

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
        $userId = $user['userId'];
        $userTypeId = $user['userTypeId'];
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $chapterBdData = [];
        foreach ($chapterList as $chapter) {
            $chDetails = $this->baseBoardController->getChapterDetails($chapter->id);
            $PresDetails = $chDetails['PresDetails'] ?? null;
            $chId = $chapter->id;

            if ($PresDetails === null) {
                $chapterBdData[$chapter->id] = null; // or some default

                continue;
            }

            $ownBoardDetails = Boards::with(['state', 'country', 'user'])
                ->where('chapter_id', $chId)
                ->where('user_id', $userId)
                ->first();

            $bdData = $this->positionConditionsService->getViewAs($userTypeId, $userId, $PresDetails, $ownBoardDetails);
            $chapterBdData[$chapter->id] = $bdData;
        }

        $countList = $chapterList->count();
        $data = ['countList' => $countList, 'chapterList' => $chapterList,
            'checkBox51Status' => $checkBox51Status, 'chapterBdData' => $chapterBdData, 'userTypeId' => $userTypeId,
        ];

        $countList = $chapterList->count();

        return view('coordinators.techreports.viewasactivechapter')->with($data);
    }

    public function viewAsDisbandedChapter(Request $request): View
    {
        $_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL] = 'yes';

        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];
        $userTypeId = $user['userTypeId'];
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $dateOptions = $this->positionConditionsService->getDateOptions();
        $oneYearAgo = $dateOptions['oneYearAgo'];

        $baseQuery = $this->baseChapterController->getBaseQuery(0, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where('zap_date', '>=', $oneYearAgo)
            ->get();
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $chapterBdData = [];
        foreach ($chapterList as $chapter) {
            $chDetails = $this->baseBoardController->getChapterDetails($chapter->id);
            $PresDetails = $chDetails['PresDetails'] ?? null;
            $chId = $chapter->id;

            if ($PresDetails === null) {
                $chapterBdData[$chapter->id] = null; // or some default

                continue;
            }

            $ownBoardDetails = BoardsDisbanded::with(['state', 'country', 'user'])
                ->where('chapter_id', $chId)
                ->where('user_id', $userId)
                ->first();

            $bdData = $this->positionConditionsService->getViewAs($userTypeId, $userId, $PresDetails, $ownBoardDetails);
            $chapterBdData[$chapter->id] = $bdData;
        }

        $countList = $chapterList->count();
        $data = ['countList' => $countList, 'chapterList' => $chapterList,
            'checkBox51Status' => $checkBox51Status, 'chapterBdData' => $chapterBdData, 'userTypeId' => $userTypeId,
        ];

        $countList = $chapterList->count();

        return view('coordinators.techreports.viewasdisbandedchapter')->with($data);
    }

    public function viewAsPendingChapter(Request $request): View
    {
        $_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL] = 'yes';

        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];
        $userTypeId = $user['userTypeId'];
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(2, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $chapterBdData = [];
        foreach ($chapterList as $chapter) {
            $chDetails = $this->baseBoardController->getChapterDetails($chapter->id);
            $PresDetails = $chDetails['PresDetails'] ?? null;
            $chId = $chapter->id;

            if ($PresDetails === null) {
                $chapterBdData[$chapter->id] = null; // or some default

                continue;
            }

            $ownBoardDetails = BoardsPending::with(['state', 'country', 'user'])
                ->where('chapter_id', $chId)
                ->where('user_id', $userId)
                ->first();

            $bdData = $this->positionConditionsService->getViewAs($userTypeId, $userId, $PresDetails, $ownBoardDetails);
            $chapterBdData[$chapter->id] = $bdData;
        }

        $countList = $chapterList->count();
        $data = ['countList' => $countList, 'chapterList' => $chapterList,
            'checkBox51Status' => $checkBox51Status, 'chapterBdData' => $chapterBdData, 'userTypeId' => $userTypeId,
        ];

        $countList = $chapterList->count();

        return view('coordinators.techreports.viewaspendingchapter')->with($data);
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

        $chapterIdsWithOutgoing = BoardsOutgoing::pluck('chapter_id')->unique();

    $chapterList = $baseQuery['query']
            ->whereIn('chapters.id', $chapterIdsWithOutgoing)
            ->get();
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $chapterBdData = [];
        foreach ($chapterList as $chapter) {
            $outgoingDetails = BoardsOutgoing::with(['state', 'country', 'user'])
                ->where('chapter_id', $chapter->id)
                ->first();

            if ($outgoingDetails === null) {
                $chapterBdData[$chapter->id] = null;
                continue;
            }

            $chapterBdData[$chapter->id] = [
                'bdPositionId' => $outgoingDetails->position_id ?? null,
                'bdDetails' => $outgoingDetails,
                'bdTypeId' => $outgoingDetails?->user?->type_id ?? null,
            ];
        }

        $countList = $chapterList->count();
        $data = ['countList' => $countList, 'chapterList' => $chapterList,
            'checkBox51Status' => $checkBox51Status, 'chapterBdData' => $chapterBdData, 'userTypeId' => $userTypeId,
        ];

        return view('coordinators.techreports.viewasoutgoingchapter')->with($data);
    }

    public function viewAsActiveCoordinator(Request $request): View
    {
        $_GET[\App\Enums\CheckboxFilterEnum::INTERNATIONAL] = 'yes';

        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];
        $userTypeId = $user['userTypeId'];
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseCoordinatorController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $coordinatorList = $baseQuery['query']->get();
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $coordinatorData = [];
        foreach ($coordinatorList as $coordinator) {
            $cdDetails = $this->baseCoordinatorController->getCoordinatorDetails($coordinator->id);
            $coordinatorData[$coordinator->id] = $cdDetails;
        }

        $countList = $coordinatorList->count();

        $data = ['countList' => $countList, 'coordinatorList' => $coordinatorList,
            'checkBox51Status' => $checkBox51Status, 'coordinatorData' => $coordinatorData, 'userTypeId' => $userTypeId,
        ];

        return view('coordinators.techreports.viewasactivecoordinator')->with($data);
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
                ->where('probation_id', ProbationReasonEnum::EXCESSPARTY)
                // ->where('probation_id', 3)
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
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

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

        $fiscalYearOptions = $this->positionConditionsService->getFiscalYearOptions();
        $adminYear = $fiscalYearOptions['adminYear'];
        $irsYear = $fiscalYearOptions['irsYear'];

        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $reportYear = $reportYearOptions['reportYear'];

        // ADMIN PROCEDURES
        $subscribeListItems = [
            'Subscribe Board Members to BoardList',
            'Subscribe Board Members to Public Announcements',
        ];

        $updateUserTablesItems = [
            'Copy/Rename Chapters Table',
            'Copy/Rename Boards Table',
            'Copy/Rename Coordinators Table',
            'Copy/Rename Users Table',
        ];

        $unSubscribeListItems = [
            'Remove Board Members from BoardList',
            'Remove Board Members from Publc Announcements',
            'Copy/Reset BoardList Forum Category',
        ];

        // IRS PROCEDURES
        $irsCorrectsionsListItems = [
            'Chapters with Wrong Fiscal Year Dates',
            'Chapters Not Found in IRS Database',
            'Chapters who FILED with the Wrong Fiscal Year Dates',
        ];

        $irsSubordinateListItems = [
            'All Chapters Currently Active',
            'Chapters Added this Fiscal Year',
            'Chapters Removed this Fiscal Year',
        ];

        $irsUpdateListItems1 = [
            'Chapters Added since Subordinate Filing',
            'Chapters Removed since Subordinate Filing',
        ];

        $irsUpdateListItems2 = [
            'Chapters Added since Last Update',
            'Chapters Removed since Last Update',
        ];

        // REPORT PROCEDURES
        $resetEOYTableItems = [
            'Add New EOY PDF Column to Table',
            'Copy/Rename Financial Report & Review Tables',
            'Set all Outgoing Users to Inactive',
            'Approved Awards Added to Awards History',
            'Clear Outgoing & Incoming Board Member Table',
            'Clear Financial Report, Questions, Documents & Review Tables',
            'Reset Financial Report Fields & Prepopulate Last Years Balance & Party Percentage',
            'Archive IRS Data in Table',
            'Reset EOY Fields in Chapter, IRS & Report Tables',
            'Update Google Shared Drive to new year for Attachmnet Uploads',
        ];

        $displayTestingItemsItems = [
            'Display EOY Dashboard Menu Items for testers',
            'Display Board Election Report Button  for testers',
            'Display Financal Report Button for testers',
        ];

        $resetAFTERtestingItems = [
            'Clear Outgoing & Incoming Board Member Table',
            'Clear Financial Report, Questions, Documents & Review Tables',
            'Reset Financial Report Fields & Prepopulate Last Years Balance & Party Percentage',
            'Reset EOY Fields in Chapter, IRS & Report Tables',
        ];

        $displayLiveItemsItems = [
            'Display EOY Dashboard Menu Items for all Coordinators',
            'Display Board Election Report Button for Board Members after May 1st',
            'Display Financal Report Button for Board Members after June 1st',
        ];

        $data = ['adminYear' => $adminYear, 'canEditFiles' => $canEditFiles, 'irsYear' => $irsYear, 'reportYear' => $reportYear, 'irsCorrectsionsListItems' => $irsCorrectsionsListItems,
            'resetEOYTableItems' => $resetEOYTableItems, 'displayTestingItemsItems' => $displayTestingItemsItems, 'displayLiveItemsItems' => $displayLiveItemsItems, 'unSubscribeListItems' => $unSubscribeListItems,
            'resetAFTERtestingItems' => $resetAFTERtestingItems, 'updateUserTablesItems' => $updateUserTablesItems, 'subscribeListItems' => $subscribeListItems,
            'irsUpdateListItems1' => $irsUpdateListItems1, 'irsUpdateListItems2' => $irsUpdateListItems2, 'irsSubordinateListItems' => $irsSubordinateListItems,
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
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
    // public function resetYear(): JsonResponse
    // {
    //     // Resolve the CURRENT fiscal year before creating anything new
    //     $currentFiscalYearOptions = $this->positionConditionsService->getFiscalYearOptions();
    //     $currentFiscalYearId = $currentFiscalYearOptions['fiscalYearId'];

    //     // Calculate the new fiscal year (current year - next year)
    //     $dateOptions = $this->positionConditionsService->getDateOptions();
    //     $fiscal_start = $dateOptions['currentYear'];
    //     $fiscal_end = $dateOptions['nextYear'];
    //     $fiscal_year = $fiscal_start.'-'.$fiscal_end;

    //     DB::beginTransaction();
    //     try {
    //         $fiscalYear = new FiscalYear;
    //         $fiscalYear->fiscal_year = $fiscal_year;
    //         $fiscalYear->fiscal_start = $fiscal_start;
    //         $fiscalYear->fiscal_end = $fiscal_end;
    //         $fiscalYear->save();

    //         $fiscalYearId = $fiscalYear->id;

    //         $adminYear = new AdminYear;
    //         $adminYear->fiscal_year_id = $fiscalYearId;
    //         $adminYear->save();

    //         // Grab the previous AdminIRS row using the resolved "old" fiscal year id
    //         $previousAdminIRS = AdminIRS::where('fiscal_year_id', $currentFiscalYearId)->first();

    //         $adminIRS = new AdminIRS;
    //         $adminIRS->fiscal_year_id = $fiscalYearId;
    //         $adminIRS->previous_file_date = $previousAdminIRS?->june_file_date;
    //         $adminIRS->save();

    //         DB::commit(); // Commit transaction

    //         return response()->json(['success' => 'Fiscal year reset successfully.']);
    //     } catch (\Exception $e) {
    //         DB::rollback(); // Rollback Transaction
    //         Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

    //         return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
    //     } finally {
    //         // This ensures DB connections are released even if exceptions occur
    //         DB::disconnect();
    //     }
    // }

    /**
     * Subscribe Users to ForumLists  // Step 2
     */
    public function updateSubscribeLists(Request $request): JsonResponse
    {
        $fiscalYearOptions = $this->positionConditionsService->getFiscalYearOptions();
        $fiscalYearId = $fiscalYearOptions['fiscalYearId'];

        DB::beginTransaction();
        try {
            $boardListAll = $this->forumSubscriptionController->bulkAddBoardBoardList();
            $publicListBoard = $this->forumSubscriptionController->bulkAddBoardPublicAnnouncements();

            $adminYear = AdminYear::where('fiscal_year_id', $fiscalYearId)->firstOrFail();
            $adminYear->update([
                'subscribe_list' => 1,
                'boardlist_active' => 1,
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Successfully subscribed to lists.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    /**
     * Record 990N Corrections Update  // Step 2
     */
    public function updateFilingCorrections(Request $request): JsonResponse
    {
        $fiscalYearOptions = $this->positionConditionsService->getFiscalYearOptions();
        $fiscalYearId = $fiscalYearOptions['fiscalYearId'];

        DB::beginTransaction();
        try {
            $adminIRS = AdminIRS::where('fiscal_year_id', $fiscalYearId)->firstOrFail();
            $adminIRS->update([
                'file_corrections' => 1,
                'file_corrections_date' => now(),
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Successfully recorded filing corrections.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    public function updateFilingCorrections2(Request $request): JsonResponse
    {
        $fiscalYearOptions = $this->positionConditionsService->getFiscalYearOptions();
        $fiscalYearId = $fiscalYearOptions['fiscalYearId'];

        DB::beginTransaction();
        try {
            $adminIRS = AdminIRS::where('fiscal_year_id', $fiscalYearId)->firstOrFail();
            $adminIRS->update([
                'file_corrections_2' => 1,
                'file_corrections_2_date' => now(),
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Successfully recorded filing corrections.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    /**
     * Record IRS September Update  // Step 2
     */
    public function updateFilingSept(Request $request): JsonResponse
    {
        $fiscalYearOptions = $this->positionConditionsService->getFiscalYearOptions();
        $fiscalYearId = $fiscalYearOptions['fiscalYearId'];

        DB::beginTransaction();
        try {
            $adminIRS = AdminIRS::where('fiscal_year_id', $fiscalYearId)->firstOrFail();
            $adminIRS->update([
                'file_sept' => 1,
                'sept_file_date' => now(),
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Successfully recorded filing update.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    /**
     * Record IRS December Update  // Step 2
     */
    public function updateFilingDec(Request $request): JsonResponse
    {
        $fiscalYearOptions = $this->positionConditionsService->getFiscalYearOptions();
        $fiscalYearId = $fiscalYearOptions['fiscalYearId'];

        DB::beginTransaction();
        try {
            $adminIRS = AdminIRS::where('fiscal_year_id', $fiscalYearId)->firstOrFail();
            $adminIRS->update([
                'file_dec' => 1,
                'dec_file_date' => now(),
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Successfully recorded filing update.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    /**
     * Record IRS Subordinate Filing  // Step 2
     */
    public function updateSubordinateFiling(Request $request): JsonResponse
    {
        $fiscalYearOptions = $this->positionConditionsService->getFiscalYearOptions();
        $fiscalYearId = $fiscalYearOptions['fiscalYearId'];

        DB::beginTransaction();
        try {
            $adminIRS = AdminIRS::where('fiscal_year_id', $fiscalYearId)->firstOrFail();
            $adminIRS->update([
                'file_subordinate' => 1,
                'sub_file_date' => now(),
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Successfully recorded subordinate filing.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    /**
     * Udate User Database Tables  // Step 7
     */
    public function updateDataDatabase(Request $request): JsonResponse
    {
        $fiscalYearOptions = $this->positionConditionsService->getFiscalYearOptions();
        $fiscalYearId = $fiscalYearOptions['fiscalYearId'];

        // Get the current month and year for table renaming
        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentYear = $dateOptions['currentYear'];
        $currentMonth = str_pad($dateOptions['currentMonth'], 2, '0', STR_PAD_LEFT);

        $this->copyTablesFINAL($currentMonth, $currentYear);

        DB::beginTransaction();
        try {
            // $this->resetBoardList($currentYear);

            $adminYear = AdminYear::where('fiscal_year_id', $fiscalYearId)->firstOrFail();
            $adminYear->update([
                'update_user_tables' => 1,
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'User data tables successfully updated, copied, and renamed.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    /**
     * Record IRS June Update  // Step 2
     */
    public function updateFilingJune(Request $request): JsonResponse
    {
        $fiscalYearOptions = $this->positionConditionsService->getFiscalYearOptions();
        $fiscalYearId = $fiscalYearOptions['fiscalYearId'];

        DB::beginTransaction();
        try {
            $adminIRS = AdminIRS::where('fiscal_year_id', $fiscalYearId)->firstOrFail();
            $adminIRS->update([
                'file_june' => 1,
                'june_file_date' => now(),
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Successfully recorded filing update.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    /**
     * Unsubscribe Users from ForumLists  // Step 8
     */
    public function updateUnsubscribeLists(Request $request): JsonResponse
    {
        // Get the current month and year for table renaming
        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentYear = $dateOptions['currentYear'];

        $fiscalYearOptions = $this->positionConditionsService->getFiscalYearOptions();
        $fiscalYearId = $fiscalYearOptions['fiscalYearId'];

        DB::beginTransaction();
        try {
            $this->resetBoardList($currentYear);

            $boardListAll = $this->forumSubscriptionController->bulkRemoveBoardBoardList();
            $publicListBoard = $this->forumSubscriptionController->bulkRemoveBoardPublicAnnouncements();

            $adminYear = AdminYear::where('fiscal_year_id', $fiscalYearId)->firstOrFail();
            $adminYear->update([
                'unsubscribe_list' => 1,
                'boardlist_active' => null,
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Successfully unsubscribed to lists.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    /**
     * Reset Fiscal Year EOY Files/Docs/Etc  // Step 3
     */
    // public function resetYearEOY(): JsonResponse
    // {
    //     $fiscalYearOptions = $this->positionConditionsService->getFiscalYearOptions();
    //     $fiscalYearId = $fiscalYearOptions['fiscalYearId'];
    //     $report_start = $fiscalYearOptions['reportYearStart'];
    //     $report_end = $fiscalYearOptions['reportYearEnd'];
    //     $report_year = $fiscalYearOptions['reportYearEnd'];

    //     DB::beginTransaction();
    //     try {
    //         $fiscalYear = FiscalYear::findOrFail($fiscalYearId);
    //         $fiscalYear->fiscal_year = $report_year;
    //         $fiscalYear->fiscal_start = $report_start;
    //         $fiscalYear->fiscal_end = $report_end;
    //         $fiscalYear->save();

    //         $adminReport = new AdminReport;
    //         $adminReport->report_year_id = $fiscalYearId;
    //         $adminReport->reset_report_year = '1';
    //         $adminReport->save();

    //         DB::commit();

    //         return response()->json(['success' => 'EOY Fiscal year reset successfully.']);
    //     } catch (\Exception $e) {
    //         DB::rollback(); // Rollback Transaction
    //         Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

    //         return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
    //     } finally {
    //         // This ensures DB connections are released even if exceptions occur
    //         DB::disconnect();
    //     }
    // }

    /**
     * Udate EOY Database Tables  // Step 4
     */
    public function updateEOYDatabaseTESTING(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['updatedBy'];

        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $reportYearId = $reportYearOptions['reportYearId'];
        $yearColumnName = $reportYearOptions['yearColumnName'];

        $fiscalYearOptions = $this->positionConditionsService->getFiscalYearOptions();

        $this->addFinancialPdfColumn($reportYearOptions);
        $this->archiveFinancialTables($fiscalYearOptions);

        DB::beginTransaction();
        try {
            $this->deactivateOutgoingUsers();
            $this->copyAwardsToHistory($reportYearOptions, $updatedId, $updatedBy);
            $this->clearTables();
            $this->resetFinancialReports($reportYearOptions, $fiscalYearOptions);
            $this->archiveIRSData();
            $this->resetEOYData($yearColumnName);
            $this->updateGoogleDriveYear($reportYearOptions);

            $adminReport = AdminReport::where('report_year_id', $reportYearId)->firstOrFail();
            $adminReport->update([
                'reset_eoy_tables' => 1,
            ]);

            DB::commit();

            return response()->json(['success' => 'Data tables successfully updated, copied, and renamed.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    private function addFinancialPdfColumn(array $fiscalYearOptions): void
    {
        $newColumnName = $fiscalYearOptions['fiscalYearEnd'].'_financial_pdf_path';
        $afterColumnName = $fiscalYearOptions['fiscalYearStart'].'_financial_pdf_path';

        Schema::table('documents_eoy', function (Blueprint $table) use ($newColumnName, $afterColumnName) {
            $table->string($newColumnName, 255)->nullable()->after($afterColumnName);
        });
    }

    private function archiveFinancialTables(array $fiscalYearOptions): void
    {
        $fiscalYearStart = $fiscalYearOptions['fiscalYearStart'];

        $tablesToArchive = [
            'financial_report',
            'financial_report_questions',
            'financial_report_review',
        ];

        foreach ($tablesToArchive as $table) {
            $archiveTable = "zzz_{$table}_12_$fiscalYearStart";

            DB::statement("DROP TABLE IF EXISTS $archiveTable");
            DB::statement("CREATE TABLE $archiveTable LIKE $table");
            DB::statement("INSERT INTO $archiveTable SELECT * FROM $table");
        }
    }

    private function deactivateOutgoingUsers(): void
    {
        User::where('type_id', UserTypeEnum::OUTGOING)
            ->where('is_active', UserStatusEnum::ACTIVE)
            ->update(['is_active' => UserStatusEnum::INACTIVE]);
    }

    private function copyAwardsToHistory(array $reportYearOptions, int $updatedId, string $updatedBy): void
    {
        $reportYearId = $reportYearOptions['reportYearId'];

        $allChapters = FinancialReportQuestions::whereNotNull('chapter_awards')->get();
        foreach ($allChapters as $report) {
            $awards = unserialize(base64_decode($report->chapter_awards));
            if (! is_array($awards)) {
                continue;
            }

            foreach ($awards as $award) {
                if (! empty($award['awards_approved'])) {
                    ChapterAwardHistory::firstOrCreate(
                        [
                            'chapter_id' => $report->chapter_id,
                            'awards_type' => $award['awards_type'],
                            'report_year_id' => $reportYearId,
                            'notified' => $report->chapter_awards_notified,
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

    private function clearTables(): void
    {
        // Clear/Delete data from the following tables
        $models = [
            BoardsOutgoing::class,
            BoardsIncoming::class,
            FinancialReport::class,
            FinancialReportQuestions::class,
            FinancialReportReview::class,
            DocumentsEOY::class,
        ];

        foreach ($models as $model) {
            $model::query()->delete();
        }
    }

    private function resetFinancialReports(array $reportYearOptions, array $fiscalYearOptions): void
    {
        $fiscalYearStart = $fiscalYearOptions['fiscalYearStart'];

        // Pull original values from the archive, not the live table
        $lastYearData = DB::table("zzz_financial_report_12_$fiscalYearStart")
            ->get(['chapter_id', 'ending_balance', 'party_percentage'])
            ->keyBy('chapter_id');

        // Create new entries in the tables below
        $activeChapters = Chapters::where('active_status', 1)->get();
        foreach ($activeChapters as $chapter) {
            $priorYear = $lastYearData->get($chapter->id);

            FinancialReport::create([
                'chapter_id' => $chapter->id,
                'ending_balance_last_year' => $priorYear->ending_balance ?? 0,
                'beginning_balance' => $priorYear->ending_balance ?? 0,
                'party_percentage_last_year' => $priorYear->party_percentage ?? 0,
            ]);

            foreach ([
                FinancialReportQuestions::class, FinancialReportReview::class, DocumentsEOY::class
            ] as $model) {
                $model::create(['chapter_id' => $chapter->id]);
            }
        }
    }

    private function archiveIRSData(): void
    {
        DocumentsIRS::query()->update([
            'irs_notes_previous' => DB::raw('irs_notes'),
            'irs_verified_previous' => DB::raw('irs_verified'),
            'irs_issues_previous' => DB::raw('irs_issues'),
            'irs_wrongdate_previous' => DB::raw('irs_wrongdate'),
            'irs_notfound_previous' => DB::raw('irs_notfound'),
            'irs_filedwrong_previous' => DB::raw('irs_filedwrong'),
            'irs_notified_previous' => DB::raw('irs_notified'),
            'irs_path_previous' => DB::raw('irs_path'),
        ]);
    }

    private function resetEOYData(string $yearColumnName): void
    {
       $tablesToReset = [
            Chapters::class => [
                'boundary_issues',
                'boundary_issue_notes',
                'boundary_issue_resolved',
            ],
            DocumentsIRS::class => [
                'irs_notes',
                'irs_verified',
                'irs_issues',
                'irs_wrongdate',
                'irs_notfound',
                'irs_filedwrong',
                'irs_notified',
                'irs_path',
            ],
            DocumentsReport::class => [
                $yearColumnName,
            ],
        ];

        foreach ($tablesToReset as $model => $columns) {
            $nulls = array_fill_keys($columns, null);
            $model::query()->update($nulls);
        }
    }

    private function updateGoogleDriveYear(array $reportYearOptions): void
    {
        GoogleDrive::where('name', 'eoy_uploads')
            ->update(['version' => $reportYearOptions['reportYearEnd']]);
    }

    /**
     * View EOY Teating Items  // Step 5
     */
    public function updateEOYTesting(Request $request): JsonResponse
    {
        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $reportYearId = $reportYearOptions['reportYearId'];

        DB::beginTransaction();
        try {
            $adminReport = AdminReport::where('report_year_id', $reportYearId)->firstOrFail();
            $adminReport->update([
                'display_testing' => 1,
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Testing items successfully set to view.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    /**
     * Udate EOY Database Tables AFTER Testing  // Step 6
     */
    public function updateEOYDatabaseLIVE(Request $request): JsonResponse
    {
        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $reportYearId = $reportYearOptions['reportYearId'];
        $yearColumnName = $reportYearOptions['yearColumnName'];

        $fiscalYearOptions = $this->positionConditionsService->getFiscalYearOptions();

        DB::beginTransaction();
        try {
            $this->clearTables();
            $this->resetFinancialReports($reportYearOptions, $fiscalYearOptions);
            $this->resetEOYData($yearColumnName);

            $adminReport = AdminReport::where('report_year_id', $reportYearId)->firstOrFail();
            $adminReport->update([
                'reset_AFTER_testing' => 1,
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Data sucessfully reset.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    private function copyTablesFINAL(int $currentMonth, int $currentYear): void
    {
        $tablesToArchive = [
            'chapters',
            'boards',
            'coordinators',
            'users',
        ];

        foreach ($tablesToArchive as $table) {
            $archiveTable = "zzz_{$table}_{$currentMonth}_{$currentYear}";

            DB::statement("DROP TABLE IF EXISTS $archiveTable");
            DB::statement("CREATE TABLE $archiveTable LIKE $table");
            DB::statement("INSERT INTO $archiveTable SELECT * FROM $table");
        }
    }

    public function resetBoardList(int $currentYear): void
    {
        $lastYear = $currentYear - 1;
        $boardListId = ForumCategoryEnum::BOARDLIST;

        $category = DB::table('forum_categories')->where('id', $boardListId)->first();
        $maxRgt = DB::table('forum_categories')->max('_rgt');

        $newCategoryId = DB::table('forum_categories')->insertGetId([
            'title' => "{$lastYear}-{$currentYear} BoardList",
            'description' => $category->description,
            'accepts_threads' => $category->accepts_threads,
            'newest_thread_id' => null,
            'latest_active_thread_id' => null,
            'thread_count' => $category->thread_count,
            'post_count' => $category->post_count,
            'is_private' => $category->is_private,
            'thread_approval_enabled' => $category->thread_approval_enabled,
            'post_approval_enabled' => $category->post_approval_enabled,
            'created_at' => now(),
            'updated_at' => now(),
            '_lft' => $maxRgt + 1,
            '_rgt' => $maxRgt + 2,
            'parent_id' => $category->parent_id,
            'color_light_mode' => $category->color_light_mode,
            'color_dark_mode' => $category->color_dark_mode,
        ]);

        DB::table('forum_threads')
            ->where('category_id', $boardListId)
            ->update(['category_id' => $newCategoryId]);

        DB::table('forum_categories')
            ->where('id', $boardListId)
            ->update([
                'newest_thread_id' => null,
                'latest_active_thread_id' => null,
                'thread_count' => 0,
                'post_count' => 0,
                'updated_at' => now(),
            ]);
    }

    /**
     * View EOY Live Items  // Step 9
     */
    public function updateEOYLive(Request $request): JsonResponse
    {
        $fiscalYearOptions = $this->positionConditionsService->getFiscalYearOptions();
        $fiscalYearId = $fiscalYearOptions['fiscalYearId'];
        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $reportYearId = $reportYearOptions['reportYearId'];

        DB::beginTransaction();
        try {
            $adminReport = AdminReport::where('report_year_id', $reportYearId)->firstOrFail();
            $adminReport->update([
                'display_live' => 1,
            ]);

            $adminYear = AdminYear::where('fiscal_year_id', $fiscalYearId)->firstOrFail();
            $adminYear->update([
                'test_eoy' => 1,
            ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Live items successfully set to view.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

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

    public function addGoogleDrive(AddGoogleDriveTechReportRequest $request): JsonResponse
    {
        try {
            $drive = new GoogleDrive;
            $drive->name = $request->name;
            $drive->description = $request->description;
            $drive->version = $request->version;
            $drive->folder_id = $request->folder_id;
            $drive->save();

            return response()->json([
                'success' => true,
                'message' => 'Google Drive folder added successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Google Drive folder creation error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error adding folder. Please try again.',
            ], 500);
        }
    }

    public function updateGoogleDrive(UpdateGoogleDriveTechReportRequest $request, int $id): JsonResponse
    {
        try {
            $drive = GoogleDrive::findOrFail($id);
            $drive->name = $request->name;
            $drive->description = $request->description;
            $drive->version = $request->version;
            $drive->folder_id = $request->folder_id;
            $drive->save();

            return response()->json([
                'success' => true,
                'message' => 'Google Drive folder updated successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Google Drive folder update error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating folder. Please try again.',
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
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

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

    public function addAdminEmail(AddAdminEmailTechReportRequest $request): JsonResponse
    {
        try {
            $admin = new AdminEmail;
            $admin->name = $request->name;
            $admin->description = $request->description;
            $admin->email = $request->email;
            $admin->save();

            return response()->json([
                'success' => true,
                'message' => 'System email added successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('System email creation error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error adding email. Please try again.',
            ], 500);
        }
    }

    public function updateAdminEmail(UpdateAdminEmailTechReportRequest $request, int $id): JsonResponse
    {
        try {
            $admin = AdminEmail::findOrFail($id);
            $admin->name = $request->name;
            $admin->description = $request->description;
            $admin->email = $request->email;
            $admin->save();

            return response()->json([
                'success' => true,
                'message' => 'System email updated successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('System email update error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating email. Please try again.',
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
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

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
            },
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
            },
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
            'conferenceList' => $conferenceList,
        ];

        return view('coordinators.techreports.regionlist')->with($data);
    }

    public function updateRegion(Request $request, int $id): JsonResponse
    {
        try {
            $region = Region::findOrFail($id);
            $region->conference_id = $request->conference_id;
            $region->save();

            $conference = Conference::find($request->conference_id);

            return response()->json([
                'success' => true,
                'message' => 'Region conference updated successfully!',
                'conference_name' => $conference->short_name,
            ]);
        } catch (\Exception $e) {
            Log::error('Region conference update error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating region conference. Please try again.',
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
            'regionList' => $regionList,
        ];

        return view('coordinators.techreports.statelist')->with($data);
    }

    public function updateState(Request $request, int $id): JsonResponse
    {
        try {
            $state = State::findOrFail($id);

            // Verify that the region belongs to the selected conference
            $region = Region::where('id', $request->region_id)
                ->where('conference_id', $request->conference_id)
                ->first();

            if (! $region) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected region does not belong to the selected conference.',
                ], 400);
            }

            $state->conference_id = $request->conference_id;
            $state->region_id = $request->region_id;
            $state->save();

            return response()->json([
                'success' => true,
                'message' => 'State assignment updated successfully!',
                'conference_name' => $region->conference->short_name,
                'region_name' => $region->long_name,
            ]);
        } catch (\Exception $e) {
            Log::error('State assignment update error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }
}
