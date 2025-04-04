<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBugsAdminRequest;
use App\Http\Requests\AddResourcesAdminRequest;
use App\Http\Requests\AddToolkitAdminRequest;
use App\Http\Requests\UpdateBugsAdminRequest;
use App\Http\Requests\UpdateResourcesAdminRequest;
use App\Http\Requests\UpdateToolkitAdminRequest;
use App\Mail\AdminNewMIMIBugWish;
use App\Models\Admin;
use App\Models\Boards;
use App\Models\Bugs;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\FinancialReport;
use App\Models\ForumCategorySubscription;
use App\Models\GoogleDrive;
use App\Models\IncomingBoard;
use App\Models\Payments;
use App\Models\BoardsOutgoing;
use App\Models\Resources;
use App\Models\ResourceCategory;
use App\Models\ToolkitCategory;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use TeamTeaTime\Forum\Models\Category as ForumCategory;

class AdminController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseChapterController;

    protected $baseCoordinatorController;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, BaseCoordinatorController $baseCoordinatorController)
    {
        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->baseCoordinatorController = $baseCoordinatorController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /* /Custom Helpers/ */
    // $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);

    /**
     * View the EOY Report Title
     */
    public function getPageTitle(Request $request)
    {
        $titles = [
            'admin_reports' => 'Admin Tasks/Reports',
            'admin_details' => 'Chapter Details',
            'resource_reports' => 'Resources',
            'resource_details' => 'Resource Details',
        ];

        return $titles;
    }

    /**
     * View Tasks on Bugs & Enhancements List
     */
    public function showBugs(Request $request): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['resource_reports'];
        $breadcrumb = 'MIMI Bugs & Wishes';

        $user = $this->userController->loadUserInformation($request);
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];
        $canEditDetails = ($positionId == 13 || $secPositionId == 13);  // IT Coordinator

        $admin = DB::table('bugs')
            ->select('bugs.*',
                DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS reported_by'),
                DB::raw('CASE
                    WHEN priority = 1 THEN "LOW"
                    WHEN priority = 2 THEN "NORMAL"
                    WHEN priority = 3 THEN "HIGH"
                    ELSE "Unknown"
                END as priority_word'))
            ->leftJoin('coordinators as cd', 'bugs.reported_id', '=', 'cd.id')
            ->orderByDesc('priority')
            ->get();

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'admin' => $admin, 'canEditDetails' => $canEditDetails];

        return view('admin.bugs')->with($data);
    }

    /**
     * Add New Task to Bugs & Enhancements List
     */
    public function addBugs(AddBugsAdminRequest $request)
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $validatedData = $request->validated();

        $task = new Bugs;
        $task->task = $validatedData['taskNameNew'];
        $task->details = $validatedData['taskDetailsNew'];
        $task->priority = $validatedData['taskPriorityNew'];
        $task->reported_id = $coorId;
        $task->reported_date = $lastupdatedDate;

        $mailData = [
            'taskNameNew' => $task->task,
            'taskDetailsNew' => $task->details,
            'ReportedId' => $coorId,
            'ReportedDate' => $task->reported_date,
        ];

        $to_email = 'jackie.mchenry@momsclub.org';
        Mail::to($to_email)->queue(new AdminNewMIMIBugWish($mailData));

        $task->save();
    }

    /**
     * Update Task on Bugs & Enhancements List
     */
    public function updateBugs(UpdateBugsAdminRequest $request, $id)
    {
        $validatedData = $request->validated();

        $task = Bugs::findOrFail($id);
        $task->details = $validatedData['taskDetails'];
        $task->notes = $validatedData['taskNotes'];
        $task->status = $validatedData['taskStatus'];
        $task->priority = $validatedData['taskPriority'];

        if ($validatedData['taskStatus'] == 3) {
            $task->completed_date = Carbon::today();
        }

        $task->save();
    }

    /**
     * View the Downloads List
     */
    public function showDownloads(Request $request): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['resource_reports'];
        $breadcrumb = 'Download Reports';

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb];

        return view('admin.downloads')->with($data);
    }

    /**
     * View Resources List
     */
    public function showResources(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];
        $canEditFiles = ($positionId == 7 || $secPositionId == 7);  // CC Coordinator

        $resources = Resources::with('resourceCategory')->get();
        $resourceCategories = ResourceCategory::all();

        foreach ($resources as $resource) {
            $id = $resource->id;
        }

        $data = ['resources' => $resources, 'resourceCategories' => $resourceCategories, 'canEditFiles' => $canEditFiles, 'id' => $id];

        return view('admin.resources')->with($data);
    }

    /**
     * Add New Files or Links to the Resources List
     */
    public function addResources(AddResourcesAdminRequest $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $validatedData = $request->validated();

        $file = new Resources;
        $file->category = $validatedData['fileCategoryNew'];
        $file->name = $validatedData['fileNameNew'];
        $file->description = $validatedData['fileDescriptionNew'];
        $file->file_type = $validatedData['fileTypeNew'];
        $file->version = $validatedData['fileVersionNew'] ?? null;
        $file->link = $validatedData['LinkNew'] ?? null;
        $file->file_path = $validatedData['filePathNew'] ?? null;
        $file->updated_id = $coorId;
        $file->updated_date = $lastupdatedDate;

        $file->save();

        $id = $file->id;
        $fileType = $file->file_type;

        return response()->json(['id' => $id, 'file_type' => $fileType]);
    }

    /**
     * Update Files or Links on the Resources List
     */
    public function updateResources(UpdateResourcesAdminRequest $request, $id)
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        // Fetch admin details
        $file = DB::table('resources')
            ->select('resources.*', DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'))
            ->leftJoin('coordinators as cd', 'resources.updated_id', '=', 'cd.id')
            ->first(); // Fetch only one record
        $validatedData = $request->validated();

        $file = Resources::findOrFail($id);
        $file->description = $validatedData['fileDescription'];
        $file->file_type = $validatedData['fileType'];

        // Check file_type value and set version and link accordingly
        if ($validatedData['fileType'] == 1) {
            $file->link = null;
            $file->version = $validatedData['fileVersion'] ?? null;
        } elseif ($validatedData['fileType'] == 2) {
            $file->version = null;
            $file->file_path = null;
            $file->link = $validatedData['link'] ?? null;
        }

        $file->updated_id = $coorId;
        $file->updated_date = $lastupdatedDate;

        $file->save();
    }

    /**
     * View Toolkit List
     */
    public function showToolkit(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];
        $canEditFiles = ($positionId == 13 || $secPositionId == 13);  // IT Coordinator

        $resources = Resources::with('toolkitCategory')->get();
        $toolkitCategories = ToolkitCategory::all();

        $data = ['resources' => $resources, 'canEditFiles' => $canEditFiles, 'toolkitCategories' => $toolkitCategories];

        return view('admin.toolkit')->with($data);
    }

    /**
     * Add New Files or Links to the Toolkit List
     */
    public function addToolkit(AddToolkitAdminRequest $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $validatedData = $request->validated();

        $file = new Resources;
        $file->category = $request->fileCategoryNew;
        $file->name = $request->fileNameNew;
        $file->description = $request->fileDescriptionNew;
        $file->file_type = $request->fileTypeNew;

        if ($request->fileTypeNew == 1) {
            $file->link = null;
            $file->version = $request->fileVersionNew ?? null;
        } elseif ($request->fileTypeNew == 2) {
            $file->version = null;
            $file->file_path = null;
            $file->link = $request->linkNew ?? null;
        }

        $file->updated_id = $coorId;
        $file->updated_date = $lastupdatedDate;

        $file->save();

        return response()->json(['id' => $file->id, 'file_type' => $file->file_type]);
    }

    /**
     * Update Files or Links on the Toolkit List
     */
    public function updateToolkit(UpdateToolkitAdminRequest $request, $id)
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        // Fetch admin details
        $file = DB::table('resources')
            ->select('resources.*', DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'))
            ->leftJoin('coordinators as cd', 'resources.updated_id', '=', 'cd.id')
            ->first(); // Fetch only one record
        $validatedData = $request->validated();

        $file = Resources::findOrFail($id);
        $file->description = $validatedData['fileDescription'];
        $file->file_type = $validatedData['fileType'];

        // Check file_type value and set version and link accordingly
        if ($validatedData['fileType'] == 1) {
            $file->link = null;
            $file->version = $validatedData['fileVersion'] ?? null;
        } elseif ($validatedData['fileType'] == 2) {
            $file->version = null;
            $file->file_path = null;
            $file->link = $validatedData['link'] ?? null;
        }

        $file->updated_id = $coorId;
        $file->updated_date = $lastupdatedDate;

        $file->save();
    }

    /**
     * View List of ReReg Payments if Dates Need to be Udpated
     */
    public function showReRegDate(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);
        $chapterList = $baseQuery['query']->get();

        $data = ['chapterList' => $chapterList];

        return view('admin.reregdate')->with($data);
    }

    public function editReRegDate(Request $request, $id): View
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chPayments = $baseQuery['chPayments'];
        $allMonths = $baseQuery['allMonths'];

        $data = ['id' => $id, 'chDetails' => $chDetails, 'chPayments' => $chPayments, 'allMonths' => $allMonths];

        return view('admin.editreregdate')->with($data);
    }

    public function updateReRegDate(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $chapter = Chapters::find($id);
        $payments = Payments::find($id);

        DB::beginTransaction();
        try {
            $chapter->start_month_id = $request->input('ch_founddate');
            $chapter->next_renewal_year = $request->input('ch_renewyear');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;

            $chapter->save();

            $payments->rereg_date = $request->input('ch_duespaid');
            $payments->rereg_members = $request->input('ch_members');

            $payments->save();

            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/admin/reregdate')->with('success', 'Re-Reg Date updated successfully.');
        }

        return redirect()->to('/admin/reregdate')->with('error', 'Failed to update Re-Reg Date.');
    }

    /**
     * List of Duplicate Users
     */
    public function showDuplicate(): View
    {
        $userData = User::where('is_active', '=', '1')
            ->groupBy('email')
            ->having(DB::raw('count(email)'), '>', 1)
            ->pluck('email');

        $userList = User::where('is_active', '=', '1')
            ->whereIn('email', $userData)
            ->get();

        $data = ['userList' => $userList];

        return view('admin.duplicateuser')->with($data);
    }

    /**
     *List of duplicate Board IDs
     */
    public function showDuplicateId(): View
    {
        $userData = Boards::groupBy('email')
            ->having(DB::raw('count(email)'), '>', 1)
            ->pluck('email');

        $userList = Boards::whereIn('email', $userData)
            ->get();

        $data = ['userList' => $userList];

        return view('admin.duplicateboardid')->with($data);
    }

    /**
     * boards with no president
     */
    public function showNoPresident(): View
    {
        $PresId = DB::table('boards')
            ->where('board_position_id', '=', '1')
            ->pluck('chapter_id');

        $ChapterPres = DB::table('chapters')
            ->where('is_active', '=', '1')
            ->whereNotIn('id', $PresId)
            ->get();

        $data = ['ChapterPres' => $ChapterPres];

        return view('admin.nopresident')->with($data);
    }

    /**
     * Outgoing Board Members
     */
    public function showOutgoingBoard(): View
    {
        $outgoingList = User::with(['outgoing', 'board.chapters'])
            ->where('user_type', 'outgoing')
            ->where('is_active', '1')
            ->get();

        $countList = count($outgoingList);
        $data = ['countList' => $countList, 'outgoingList' => $outgoingList];

        return view('admin.outgoingboard')->with($data);
    }

    /**
     * Disbanded Board Members
     */
    public function showDisbandedBoard(): View
    {
        $disbandedList = User::with(['boardDisbanded', 'boardDisbanded.chapters'])
            ->where('user_type', 'disbanded')
            ->where('is_active', '1')
            ->get();

        $countList = count($disbandedList);
        $data = ['countList' => $countList, 'disbandedList' => $disbandedList];

        return view('admin.disbandedboard')->with($data);
    }

    /**
     * Show EOY Procedures
     */
    public function showEOY(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];
        $canEditFiles = ($positionId == 13 || $secPositionId == 13);  // IT Coordinator

        $admin = DB::table('admin')
            ->select('admin.*',
                DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'), )
            ->leftJoin('coordinators as cd', 'admin.updated_id', '=', 'cd.id')
            ->orderByDesc('admin.id') // Assuming 'id' represents the order of insertion
            ->first();

        // Fetch distinct fiscal years
        $fiscalYears = DB::table('admin')->distinct()->pluck('fiscal_year');

        $resetEOYTableItems = [
            'Set all Outgoing Users to Inactive',
            'Clear Outgoing Board Member Table',
            'Clear Incoming Board Member Table',
            'Ending Balance Added to Documents from Financial Reports',
            'Copy/Rename Financial Reports Table',
            'Clear Financial Reports Table',
            'Pre-Balance Added to Financial Report from Documents',
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
        ];

        $unSubscribeListItems = [
            'Remove Board Members from BoardList',
            'Remove Board Members from Publc Announcements',
            'Remove Coordinators from BoardList',
        ];

        $subscribeListItems = [
            'Subscribe Coordinators to BoardList',
            'Subscribe Board Members to BoardList',
            'Subscribe Board Members to Public Announcements',
        ];

        $data = ['admin' => $admin, 'canEditFiles' => $canEditFiles, 'fiscalYears' => $fiscalYears,
            'resetEOYTableItems' => $resetEOYTableItems, 'displayCoorindatorMenuItems' => $displayCoorindatorMenuItems, 'displayChapterButtonItems' => $displayChapterButtonItems,
            'displayTestingItemsItems' => $displayTestingItemsItems, 'displayLiveItemsItems' => $displayLiveItemsItems, 'unSubscribeListItems' => $unSubscribeListItems,
            'resetAFTERtestingItems' => $resetAFTERtestingItems, 'updateUserTablesItems' => $updateUserTablesItems, 'subscribeListItems' => $subscribeListItems,
        ];

        return view('admin.eoy')->with($data);
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
            ->where('user_type', 'disbanded')
            ->where('is_active', '1')
            ->update([
                'is_active' => '0',
            ]);

        DB::commit();

            $message = 'Disbanded users successfully updated';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('admin.disbandedboard'),
            ]);

        } catch (\Exception $e) {
            // Rollback transaction on exception
            DB::rollback();
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('admin.disbandedboard'),
            ]);
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
            ->where('user_type', 'outgoing')
            ->where('is_active', '1')
            ->update([
                'is_active' => '0',
            ]);

            DB::commit();

            $message = 'Outgoing users successfully updated';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('admin.outgoingboard'),
            ]);

        } catch (\Exception $e) {
            // Rollback transaction on exception
            DB::rollback();
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('admin.outgoingboard'),
            ]);
        }
    }
    /**
     * Reset EOY Procedurles for New year
     */
    public function resetYear(): JsonResponse
    {
        try {
            // Create a new Admin instance
            $admin = new Admin;

            // Calculate the fiscal year (current year - next year)
            $currentYear = Carbon::now()->year;
            $nextYear = $currentYear + 1;
            $fiscalYear = $currentYear.'-'.$nextYear;

            // Set the fiscal year field
            $admin->fiscal_year = $fiscalYear;

            // Save the new entry
            $admin->save();

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Fiscal year reset successfully.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    /**
     * Udate EOY Database Tables
     */
    public function updateEOYDatabase(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['user_coorId'];
            $lastUpdatedBy = $user['user_name'];
            $lastupdatedDate = date('Y-m-d H:i:s');

            // Get the current year +/- 1 for table renaming
            $currentYear = Carbon::now()->year;
            $nextYear = $currentYear + 1;
            $lastyear = $currentYear - 1;

            // Make outgoing users inactive
            DB::table('users')
                ->where('user_type', 'outgoing')
                ->where('is_active', '1')
                ->update([
                    'is_active' => '0',
                ]);

            // Remove Data from the `outgoing_board_member` and `incoming_board_member` tables
            BoardsOutgoing::query()->delete();
            IncomingBoard::query()->delete();

            // Fetch all chapters with their financial reports and update the balance BEFORE removing data from table
            $chapters = Chapters::with('financialReport', 'documents')->get();
            foreach ($chapters as $chapter) {
                if ($chapter->financialReport && $chapter->documents) {
                    $document = $chapter->documents;
                    $document->balance = $chapter->financialReport->post_balance;
                    $document->save();
                }
            }

            // Copy and rename the `financial_report` table
            DB::statement("CREATE TABLE financial_report_12_$lastyear LIKE financial_report");
            DB::statement("INSERT INTO financial_report_12_$lastyear SELECT * FROM financial_report");

            // Remove Data from the `financial_report` table
            FinancialReport::query()->delete();

            // Fetch all active chapters and insert each chapter's balance into financial_report
            $activeChapters = Chapters::with('documents')->where('is_active', 1)->get();
            foreach ($activeChapters as $chapter) {
                FinancialReport::create([
                    'chapter_id' => $chapter->id,  // Ensure chapter_id is provided
                    'pre_balance' => $chapter->documents->balance,
                    'amount_reserved_from_previous_year' => $chapter->documents->balance,
                ]);
            }

            // Update chapters table: Set specified columns to NULL
            DB::table('chapters')->update([
                'boundary_issues' => null,
                'boundary_issue_notes' => null,
                'boundary_issue_resolved' => null,
            ]);

            // Update documents table: Set specified columns to NULL
            DB::table('documents')->update([
                'new_board_submitted' => null,
                'new_board_active' => null,
                'financial_report_received' => null,
                'report_received' => null,
                'financial_review_complete' => null,
                'review_complete' => null,
                'report_notes' => null,
                'report_extension' => null,
                'extension_notes' => null,
                'financial_pdf_path' => null,
                'roster_path' => null,
                'irs_path' => null,
                'statement_1_path' => null,
                'statement_2_path' => null,
                'award_path' => null,
            ]);

            // Change Year for Google Drive Financial Report Attachmnets
            DB::table('google_drive')->update([
                'eoy_uploads_year' => $nextYear,
            ]);

            // Update admin table: Set specified columns to 1
            DB::table('admin')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'reset_eoy_tables' => '1',
                    'updated_id' => $coorId,
                    'updated_at' => $lastupdatedDate,
                ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Data tables successfully updated, copied, and renamed.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    /**
     * Udate EOY Database Tables AFTER Testing
     */
    public function updateEOYDatabaseAFTERTesting(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['user_coorId'];
            $lastUpdatedBy = $user['user_name'];
            $lastupdatedDate = date('Y-m-d H:i:s');

            // Fetch all chapters with their financial reports and update the balance BEFORE removing data from table
            $chapters = Chapters::with('financialReportLastYear', 'documents')->get();
            foreach ($chapters as $chapter) {
                if ($chapter->financialReportLastYear && $chapter->documents) {
                    $document = $chapter->documents;
                    $document->balance = $chapter->financialReportLastYear->post_balance;
                    $document->save();
                }
            }

            BoardsOutgoing::query()->delete();
            IncomingBoard::query()->delete();
            FinancialReport::query()->delete();

            // Fetch all active chapters and add balance into financial_report
            $activeChapters = Chapters::with('documents')->where('is_active', 1)->get();
            foreach ($activeChapters as $chapter) {
                FinancialReport::create([
                    'chapter_id' => $chapter->id,  // Ensure chapter_id is provided
                    'pre_balance' => $chapter->documents->balance,
                    'amount_reserved_from_previous_year' => $chapter->documents->balance,
                ]);
            }

            // Update chapters table: Set specified columns to NULL
            DB::table('chapters')->update([
                'boundary_issues' => null,
                'boundary_issue_notes' => null,
                'boundary_issue_resolved' => null,
            ]);

            // Update documents table: Set specified columns to NULL
            DB::table('documents')->update([
                'new_board_submitted' => null,
                'new_board_active' => null,
                'financial_report_received' => null,
                'report_received' => null,
                'financial_review_complete' => null,
                'review_complete' => null,
                'report_notes' => null,
                'report_extension' => null,
                'extension_notes' => null,
                'financial_pdf_path' => null,
                'roster_path' => null,
                'irs_path' => null,
                'statement_1_path' => null,
                'statement_2_path' => null,
                'award_path' => null,
            ]);

            // Update admin table: Set specified columns to 1
            DB::table('admin')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'reset_AFTER_testing' => '1',
                    'updated_id' => $coorId,
                    'updated_at' => $lastupdatedDate,
                ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Data sucessfully reset.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    /**
     * Udate User Database Tables
     */
    public function updateDataDatabase(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['user_coorId'];
            $lastUpdatedBy = $user['user_name'];
            $lastupdatedDate = date('Y-m-d H:i:s');

            // Get the current month and year for table renaming
            $currentYear = Carbon::now()->year;
            $currentMonth = Carbon::now()->month;

            // Copy and rename the `chapters` table
            DB::statement("CREATE TABLE chapters_{$currentMonth}_{$currentYear} LIKE chapters");
            DB::statement("INSERT INTO chapters_{$currentMonth}_{$currentYear} SELECT * FROM chapters");

            // Copy and rename the `boards` table
            DB::statement("CREATE TABLE boards_{$currentMonth}_{$currentYear} LIKE boards");
            DB::statement("INSERT INTO boards_{$currentMonth}_{$currentYear} SELECT * FROM boards");

            // Copy and rename the `coordinators` table
            DB::statement("CREATE TABLE coordinators_{$currentMonth}_{$currentYear} LIKE coordinators");
            DB::statement("INSERT INTO coordinators_{$currentMonth}_{$currentYear} SELECT * FROM coordinators");

            // Copy and rename the `users` table
            DB::statement("CREATE TABLE users_{$currentMonth}_{$currentYear} LIKE users");
            DB::statement("INSERT INTO users_{$currentMonth}_{$currentYear} SELECT * FROM users");

            // Update admin table: Set specified columns to 1
            DB::table('admin')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'update_user_tables' => '1',
                    'updated_id' => $coorId,
                    'updated_at' => $lastupdatedDate,
                ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'User data tables successfully updated, copied, and renamed.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    /**
     * View EOY Teating Items
     */
    public function updateEOYTesting(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['user_coorId'];
            $lastUpdatedBy = $user['user_name'];
            $lastupdatedDate = date('Y-m-d H:i:s');

            // Update admin table: Set specified columns to 1
            DB::table('admin')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'display_testing' => '1',
                    'updated_id' => $coorId,
                    'updated_at' => $lastupdatedDate,
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
     * View EOY Live Items
     */
    public function updateEOYLive(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['user_coorId'];
            $lastUpdatedBy = $user['user_name'];
            $lastupdatedDate = date('Y-m-d H:i:s');

            // Update admin table: Set specified columns to 1
            DB::table('admin')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'display_live' => '1',
                    'updated_id' => $coorId,
                    'updated_at' => $lastupdatedDate,
                ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Live items successfully set to view.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    public function updateSubscribeLists(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['user_coorId'];
            $lastUpdatedBy = $user['user_name'];
            $lastupdatedDate = date('Y-m-d H:i:s');

            // Get BoardList category
            $categoryBoardList = ForumCategory::where('title', 'BoardList')
                ->first();
            $categoryIdBoardList = $categoryBoardList->id;

            // Get Public Announcement category
            $categoryPublic = ForumCategory::where('title', 'Public Announcements')
                ->first();
            $categoryIdPublic = $categoryPublic->id;

            // Get active coordinators
            $coordinatorUserIds = Coordinators::where('is_active', '1')
                ->where('on_leave', '0')
                ->get()
                ->pluck('user_id')
                ->unique();
            $activeCoordinators = User::whereIn('id', $coordinatorUserIds)->get();

            // Get board members from active chapters using with()
            $boardUserIds = Chapters::with('boards')
                ->where('is_active', true)
                ->get()
                ->pluck('boards')
                ->flatten()
                ->pluck('user_id')
                ->unique();
            $activeBoards = User::whereIn('id', $boardUserIds)->get();

            // Combine the collections
            $allUsers = $activeCoordinators->concat($activeBoards);

            // Remove duplicates if a user is both coordinator and board member
            $uniqueUsers = $allUsers->unique('id');

            foreach ($uniqueUsers as $user) {
                // Check if subscription already exists
                $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                    ->where('category_id', $categoryIdBoardList)
                    ->first();

                if (! $existingSubscription) {
                    ForumCategorySubscription::create([
                        'user_id' => $user->id,
                        'category_id' => $categoryIdBoardList,
                    ]);
                }
            }

            foreach ($activeBoards as $user) {
                // Check if subscription already exists
                $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                    ->where('category_id', $categoryIdPublic)
                    ->first();

                if (! $existingSubscription) {
                    ForumCategorySubscription::create([
                        'user_id' => $user->id,
                        'category_id' => $categoryIdPublic,
                    ]);
                }
            }

            // Update admin table: Set specified columns to 1
            DB::table('admin')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'subscribe_list' => '1',
                    'updated_id' => $coorId,
                    'updated_at' => $lastupdatedDate,
                ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Successfully subscribed to lists.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        }
    }

    public function updateUnsubscribeLists(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['user_coorId'];
            $lastUpdatedBy = $user['user_name'];
            $lastupdatedDate = date('Y-m-d H:i:s');

            $categoryBoardList = ForumCategory::where('title', 'BoardList')
                ->first();

            $categoryPublic = ForumCategory::where('title', 'Public Announcements')
                ->first();

            // Delete all subscriptions for this category
            $unsubscribeBoardList = ForumCategorySubscription::where('category_id', $categoryBoardList->id)
                ->delete();

            // Delete board members for this category
            $unsubscribePublic = ForumCategorySubscription::where('category_id', $categoryPublic->id)
                ->whereHas('user', function ($query) {
                    $query->where('user_type', 'board');
                })
                ->delete();

            // Update admin table: Set specified columns to 1
            DB::table('admin')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'unsubscribe_list' => '1',
                    'updated_id' => $coorId,
                    'updated_at' => $lastupdatedDate,
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
     * view Google Drive Shared Folder Ids
     */
    public function showGoogleDrive(): View
    {
        $googleDrive = GoogleDrive::get();

        $data = ['googleDrive' => $googleDrive];

        return view('admin.googledrive')->with($data);
    }

    /**
     * Update Google Drive Shared Folder Ids
     */
    public function updateGoogleDrive(Request $request): JsonResponse
    {
        try {
            $drive = GoogleDrive::firstOrFail();
            $drive->ein_letter_uploads = $request->input('einLetterDrive');
            $drive->eoy_uploads = $request->input('eoyDrive');
            $drive->eoy_uploads_year = $request->input('eoyDriveYear');
            $drive->resources_uploads = $request->input('resourcesDrive');
            $drive->disband_letter = $request->input('disbandDrive');
            $drive->final_financial_report = $request->input('finalReportDrive');
            $drive->good_standing_letter = $request->input('goodStandingDrive');
            $drive->probation_letter = $request->input('probationDrive');

            $drive->save();

            DB::commit();

            $message = 'Google Drive ID updated successfully';

            return response()->json(['status' => 'success', 'message' => $message, 'redirect' => route('admin.googledrive')]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback transaction on exception
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            return response()->json(['status' => 'error', 'message' => $message, 'redirect' => route('admin.googledrive')]);
        }
    }
}
