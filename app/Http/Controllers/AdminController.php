<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBugsAdminRequest;
use App\Http\Requests\AddResourcesAdminRequest;
use App\Http\Requests\AddToolkitAdminRequest;
use App\Http\Requests\UpdateBugsAdminRequest;
use App\Http\Requests\UpdateEOYRequest;
use App\Http\Requests\UpdateResourcesAdminRequest;
use App\Http\Requests\UpdateToolkitAdminRequest;
use App\Mail\AdminNewMIMIBugWish;
use App\Models\Admin;
use App\Models\OutgoingBoard;
use App\Models\IncomingBoard;
use App\Models\Boards;
use App\Models\Bugs;
use App\Models\Chapters;
use App\Models\State;
use App\Models\FinancialReport;
use App\Models\GoogleDrive;
use App\Models\Month;
use App\Models\Resources;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->middleware('auth')->except('logout');
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class);
        $this->userController = $userController;
    }

    /**
     * View Tasks on Bugs & Enhancements List
     */
    public function showBugs(Request $request): View
    {
        $user = User::find($request->user()->id);

        $corDetails = $user->coordinator;
        // Check if CordDetails is not found for the user
        if (! $corDetails) {
            return to_route('home');
        }

        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $corId)
            ->get();

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

        // Determine if the user is allowed to edit notes and status
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $canEditDetails = ($positionId == 13 || $secPositionId == 13);  //IT Coordinator

        $data = ['admin' => $admin, 'canEditDetails' => $canEditDetails, 'coordinatorDetails' => $coordinatorDetails];

        return view('admin.bugs')->with($data);
    }

    /**
     * Add New Task to Bugs & Enhancements List
     */
    public function addBugs(AddBugsAdminRequest $request)
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        // Fetch coordinator details
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $corId)
            ->first(); // Fetch only one record

        // Fetch admin details
        $admin = DB::table('bugs')
            ->select('bugs.*', DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS reported_by'))
            ->leftJoin('coordinators as cd', 'bugs.reported_id', '=', 'cd.id')
            ->orderByDesc('priority')
            ->first(); // Fetch only one record

        $validatedData = $request->validated();

        $task = new Bugs;
        $task->task = $validatedData['taskNameNew'];
        $task->details = $validatedData['taskDetailsNew'];
        $task->priority = $validatedData['taskPriorityNew'];
        $task->reported_id = $corId;
        $task->reported_date = Carbon::today();

        $mailData = [
            'taskNameNew' => $task->task,
            'taskDetailsNew' => $task->details,
            'ReportedId' => $admin->reported_by,
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
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
            //Get Coordinator Reporting Tree
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
                ->where($sqlLayerId, '=', $corId)
                ->get();

            $inQryStr = '';
            foreach ($reportIdList as $key => $val) {
                $inQryStr .= $val->id.',';
            }
            $inQryStr = rtrim($inQryStr, ',');
            $inQryArr = explode(',', $inQryStr);
        }

        //Get Chapter List mapped with login coordinator
        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'db.month_long_name as start_month')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
            ->leftJoin('month as db', 'chapters.start_month_id', '=', 'db.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1');

        if ($conditions['founderCondition']) {

        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('chapters.conference_id', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('chapters.region_id', '=', $corRegId);
        } else {
            $baseQuery->whereIn('chapters.primary_coordinator_id', $inQryArr);
        }
        $chapterList = $baseQuery->get();

        $data = ['chapterList' => $chapterList, 'corId' => $corId, 'positionId' => $positionId, 'secPositionId' => $secPositionId];

        return view('admin.downloads')->with($data);
    }

    /**
     * View Resources List
     */
    public function showResources(Request $request): View
    {
        $user = User::find($request->user()->id);

        $corDetails = $user->coordinator;
        // Check if CordDetails is not found for the user
        if (! $corDetails) {
            return to_route('home');
        }

        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $corId)
            ->get();

        $resources = DB::table('resources')
            ->select('resources.*', 'resources.id as id',
                DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'),
                DB::raw('CASE
                    WHEN category = 1 THEN "BYLAWS"
                    WHEN category = 2 THEN "FACT SHEETS"
                    WHEN category = 3 THEN "COPY READY MATERIAL"
                    WHEN category = 4 THEN "IDEAS AND INSPIRATION"
                    WHEN category = 5 THEN "CHAPTER RESOURCES"
                    WHEN category = 6 THEN "SAMPLE CHPATER FILES"
                    WHEN category = 7 THEN "END OF YEAR"
                    ELSE "Unknown"
                END as priority_word'))
            ->leftJoin('coordinators as cd', 'resources.updated_id', '=', 'cd.id')
            ->orderBy('name')
            ->get();

        // Assuming you want to access the 'id' property of each resource, you need to iterate through $resources
        foreach ($resources as $resource) {
            $id = $resource->id;
            // Do something with $id
        }

        // Determine if the user is allowed to add and update resources
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $canEditFiles = ($positionId == 7 || $secPositionId == 7);  //CC Coordinator

        $data = ['resources' => $resources, 'canEditFiles' => $canEditFiles, 'coordinatorDetails' => $coordinatorDetails, 'id' => $id];

        return view('admin.resources')->with($data);
    }

    /**
     * Add New Files or Links to the Resources List
     */
    public function addResources(AddResourcesAdminRequest $request): JsonResponse
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        // Fetch coordinator details
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $corId)
            ->first(); // Fetch only one record

        $validatedData = $request->validated();

        $file = new Resources;
        $file->category = $validatedData['fileCategoryNew'];
        $file->name = $validatedData['fileNameNew'];
        $file->description = $validatedData['fileDescriptionNew'];
        $file->file_type = $validatedData['fileTypeNew'];
        $file->version = $validatedData['fileVersionNew'] ?? null;
        $file->link = $validatedData['LinkNew'] ?? null;
        $file->file_path = $validatedData['filePathNew'] ?? null;
        $file->updated_id = $corId;
        $file->updated_date = Carbon::today();

        $file->save();

        // After adding the resource, retrieve its id
        $id = $file->id;
        $fileType = $file->file_type;

        // Return the id and file_type in the response
        return response()->json(['id' => $id, 'file_type' => $fileType]);
    }

    /**
     * Update Files or Links on the Resources List
     */
    public function updateResources(UpdateResourcesAdminRequest $request, $id)
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        // Fetch coordinator details
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $corId)
            ->first(); // Fetch only one record

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

        $file->updated_id = $corId;
        $file->updated_date = Carbon::today();

        $file->save();
    }

    /**
     * View Toolkit List
     */
    public function showToolkit(Request $request): View
    {
        $user = User::find($request->user()->id);

        $corDetails = $user->coordinator;
        // Check if CordDetails is not found for the user
        if (! $corDetails) {
            return to_route('home');
        }

        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $corId)
            ->get();

        $resources = DB::table('resources')
            ->select('resources.*',
                DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'),
                DB::raw('CASE
                    WHEN category = 8 THEN "NEED BASED FACT SHEET"
                    WHEN category = 9 THEN "JOB DESCRIPTION"
                    WHEN category = 10 THEN "RESOURCE FOR COORDINATORS"
                    WHEN category = 11 THEN "RESOURCE FOR CHAPTERS"
                    ELSE "Unknown"
                END as priority_word'))
            ->leftJoin('coordinators as cd', 'resources.updated_id', '=', 'cd.id')
            ->orderBy('name')
            ->get();

        // Determine if the user is allowed to edit notes and status
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $canEditFiles = ($positionId == 13 || $secPositionId == 13);  //IT Coordinator

        $data = ['resources' => $resources, 'canEditFiles' => $canEditFiles, 'coordinatorDetails' => $coordinatorDetails];

        return view('admin.toolkit')->with($data);
    }

    /**
     * Add New Files or Links to the Toolkit List
     */
    public function addToolkit(AddToolkitAdminRequest $request): JsonResponse
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];

        // Fetch coordinator details
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $corId)
            ->first();

        $validatedData = $request->validated();

        // Create new file resource
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

        $file->updated_id = $corId;
        $file->updated_date = Carbon::today();

        $file->save();

        // Return response
        return response()->json(['id' => $file->id, 'file_type' => $file->file_type]);
    }

    /**
     * Update Files or Links on the Toolkit List
     */
    public function updateToolkit(UpdateToolkitAdminRequest $request, $id)
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        // Fetch coordinator details
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $corId)
            ->first(); // Fetch only one record

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

        $file->updated_id = $corId;
        $file->updated_date = Carbon::today();

        $file->save();
    }

    public function showReRegDate(Request $request)
    {
        $reChapterList = Chapters::with(['state', 'conference', 'region', 'startMonth', 'president', ])
            ->where('is_active', 1)
            ->orderBy(State::select('state_short_name')
            ->whereColumn('state.id', 'chapters.state_id'), 'asc')
            ->orderBy('chapters.name')
            ->get();

        $data = ['reChapterList' => $reChapterList];

        return view('admin.reregdate')->with($data);
    }

    public function editReRegDate(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $coordId = $cdDetails->id;

        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'startMonth', 'webLink', 'status', 'documents', 'financialReport', 'boards'])->find($id);

        $allMonths = Month::all();

        $data = ['id' => $id, 'chDetails' => $chDetails, 'allMonths' => $allMonths];

        return view('admin.editreregdate')->with($data);
    }

    public function updateReRegDate(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $chapter = Chapters::find($id);
        DB::beginTransaction();
        try {
            $chapter->start_month_id = $request->input('ch_founddate');
            $chapter->next_renewal_year = $request->input('ch_renewyear');
            $chapter->dues_last_paid = $request->input('ch_duespaid');
            $chapter->members_paid_for = $request->input('ch_members');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');

            $chapter->save();

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

    // /**
    //  * error logs
    //  */
    // public function showMailQueue(): View
    // {
    //     $Queue = DB::table('jobs')
    //         ->get();

    //     $data = ['Queue' => $Queue];

    //     return view('admin.mailqueue')->with($data);
    // }

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
        $userData = Boards::where('is_active', '=', '1')
            ->groupBy('email')
            ->having(DB::raw('count(email)'), '>', 1)
            ->pluck('email');

        $userList = Boards::where('is_active', '=', '1')
            ->whereIn('email', $userData)
            ->get();

        $data = ['userList' => $userList];

        return view('admin.duplicateboardid')->with($data);
    }

    /**
     * List of users on multiple boards
     */
    // public function showMultiple(): View
    // {

    //     $userData = DB::table('boards')
    //         ->where('is_active', '=', '1')
    //         ->groupBy('email')
    //         ->having(DB::raw('count(email)'), '>', 1)
    //         ->pluck('email');

    //     $userList = DB::table('boards')
    //         ->where('is_active', '=', '1')
    //         ->whereIn('email', $userData)
    //         ->get();

    //     $data = ['userList' => $userList];

    //     return view('admin.multipleboard')->with($data);
    // }

    /**
     * boards with no president
     */
    public function showNoPresident(): View
    {
        $PresId = DB::table('boards')
            ->where('is_active', '=', '1')
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
        $OutgoingBoard = DB::table('outgoing_board_member as ob')
            ->select('ob.chapter_id', 'ob.first_name', 'ob.last_name', 'ob.email', 'users.user_type', 'chapters.name as chapter_name', 'state.state_short_name as chapter_state')
            ->leftJoin('users', 'ob.email', '=', 'users.email')
            ->leftJoin('chapters', 'ob.chapter_id', '=', 'chapters.id')
            ->leftJoin('state', 'chapters.state_id', '=', 'state.id')
            ->where('users.user_type', 'outgoing')
            ->where('users.is_active', '1')
            ->orderBy('chapters.name')
            ->get();

        $countList = count($OutgoingBoard);

        $data = ['OutgoingBoard' => $OutgoingBoard, 'countList' => $countList];
        // $data = ['OutgoingBoard' => $OutgoingBoard, 'countList' => $countList, 'checkBoxStatus' => $checkBoxStatus];

        return view('admin.outgoingboard')->with($data);
    }

    /**
     * Show EOY Procedures
     */
    public function showEOY(Request $request): View
    {
        $user = User::find($request->user()->id);

        $corDetails = $user->coordinator;
        // Check if CordDetails is not found for the user
        if (! $corDetails) {
            return to_route('home');
        }

        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $corId)
            ->get();

        $admin = DB::table('admin')
            ->select('admin.*',
                DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'), )
            ->leftJoin('coordinators as cd', 'admin.updated_id', '=', 'cd.id')
            ->orderByDesc('admin.id') // Assuming 'id' represents the order of insertion
            ->first();

        // Fetch distinct fiscal years
        $fiscalYears = DB::table('admin')->distinct()->pluck('fiscal_year');

        $resetAFTERtestingItems = [
            'Outgoing Board Member Database Reset',
            'Incoming Board Member Database Reset',
            'Financial Report Database Reset',
            'Pre-Balance Added to Financial Report Database'
        ];

        // Determine if the user is allowed to edit notes and status
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $canEditFiles = ($positionId == 13 || $secPositionId == 13);  //IT Coordinator

        $data = ['admin' => $admin, 'canEditFiles' => $canEditFiles, 'coordinatorDetails' => $coordinatorDetails, 'fiscalYears' => $fiscalYears,
            'resetAFTERtestingItems' => $resetAFTERtestingItems
        ];

        return view('admin.eoy')->with($data);
    }

    /**
     * Reset EOY Procedurles for New year
     */
    public function resetYear(): RedirectResponse
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

            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/admin')->with('success', 'Fiscal year reset successfully.');
         }

        return redirect()->to('/admin')->with('fail', 'An error occurred when restting the fiscal year.');
    }

    /**
     * Udate EOY Database Tables
     */
    public function updateEOYDatabase(Request $request): RedirectResponse
    {
        try {
            $corDetails = User::find($request->user()->id)->coordinator;
            $corId = $corDetails['id'];

            // Get the current year +/- 1 for table renaming
            $currentYear = Carbon::now()->year;
            $nextYear = $currentYear + 1;
            $lastyear = $currentYear - 1;

            // Fetch all outgoing board members
            // $outgoingBoardMembers = DB::table('outgoing_board_member')->get();
            // $outgoingBoardMembers = OutgoingBoard::get();

            // Update the `is_active` column in the `users` table
            // foreach ($outgoingBoardMembers as $outgoingMember) {
            //     DB::table('users')->where('id', $outgoingMember->user_id)->update([
            //         'is_active' => 0,
            //         'updated_at' => now(),
            //     ]);
            // }

            // Remove Data from the `outgoing_board_member` and `incoming_board_member` tables
            // DB::table('outgoing_board_member')->truncate();
            // DB::table('incoming_board_member')->truncate();
            OutgoingBoard::query()->delete();
            IncomingBoard::query()->delete();

            // Fetch all chapters with their financial reports and update the balance BEFORE removing data from table
            $chapters = Chapters::with('financialReport', 'documents')->get();
            foreach ($chapters as $chapter) {
                if ($chapter->financialReport) {
                    $chapter->documents->balance = $chapter->financialReport->post_balance;
                    $chapter->save();
                }
            }

            // Copy and rename the `financial_report` table
            DB::statement("CREATE TABLE financial_report_12_$lastyear LIKE financial_report");
            DB::statement("INSERT INTO financial_report_12_$lastyear SELECT * FROM financial_report");

            // Remove Data from the `financial_report` table
            // DB::table('financial_report')->truncate();
            FinancialReport::query()->delete();

            // Fetch all active chapters
            $activeChapters = Chapters::with('documents')->where('is_active', 1)->get();

            // Insert each chapter's balance into financial_report
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

            // Get board details where board members are active
            $boardDetails = Boards::where('is_active', 1)->get();

            // Loop through each board detail and insert into outgoing_boards
            foreach ($boardDetails as $boardDetail) {
                OutgoingBoard::create([
                    'board_id' => $boardDetail->id,
                    'user_id' => $boardDetail->user_id,
                    'chapter_id' => $boardDetail->chapter_id,
                    'board_position_id' => $boardDetail->board_position_id,
                    'first_name' => $boardDetail->first_name,
                    'last_name' => $boardDetail->last_name,
                    'email' => $boardDetail->email,
                ]);
            }

            // Change Year for Google Drive Financial Report Attachmnets
            DB::table('google_drive')->update([
                'eoy_uploads_year' => $nextYear,
            ]);

            // Update admin table: Set specified columns to 1
            DB::table('admin')->update([
                'reset_AFTER_testing' => '1',
                'truncate_outgoing' => '1',
                'copy_FRtoCH' => '1',
                'copy_financial' => '1',
                'copy_CHtoFR' => '1',
                'copy_BDtoOUT' => '1',
                'update_googleID' => '1',
                'updated_id' => $corId,
                'updated_at' => Carbon::today(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/admin')->with('success', 'Financial data tables successfully updated, copied, and renamed.');
           }

        return redirect()->to('/admin')->with('fail', 'An error occurred while updating the financial data tables.');
    }

    /**
     * Udate EOY Database Tables AFTER Testing
     */
    public function updateEOYDatabaseAFTERTesting(Request $request): RedirectResponse
    {
        try {
            $corDetails = User::find($request->user()->id)->coordinator;
            $corId = $corDetails['id'];

             // Fetch all chapters with their financial reports and update the balance BEFORE removing data from table
             $chapters = Chapters::with('financialReportLastYear', 'documents')->get();
             foreach ($chapters as $chapter) {
                 if ($chapter->financialReportLastYear) {
                     $chapter->documents->balance = $chapter->financialReportLastYear->post_balance;
                     $chapter->save();
                 }
             }

            OutgoingBoard::query()->delete();
            IncomingBoard::query()->delete();
            FinancialReport::query()->delete();

            // Fetch all active chapters
            $activeChapters = Chapters::with('documents')->where('is_active', 1)->get();

            // Insert each chapter's balance into financial_report
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

            // Get board details where board members are active
            $boardDetails = Boards::where('is_active', 1)->get();

            // Loop through each board detail and insert into outgoing_boards
            foreach ($boardDetails as $boardDetail) {
                OutgoingBoard::create([
                    'board_id' => $boardDetail->id,
                    'user_id' => $boardDetail->user_id,
                    'chapter_id' => $boardDetail->chapter_id,
                    'board_position_id' => $boardDetail->board_position_id,
                    'first_name' => $boardDetail->first_name,
                    'last_name' => $boardDetail->last_name,
                    'email' => $boardDetail->email,
                ]);
            }

            // Update admin table: Set specified columns to 1
            DB::table('admin')->update([
                'reset_AFTER_testing' => '1',
                'updated_id' => $corId,
                'updated_at' => Carbon::today(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/admin')->with('success', 'Data sucessfully reset.');
          }

        return redirect()->to('/admin')->with('fail', 'An error occurred while updating the data.');
    }


    /**
     * Udate User Database Tables
     */
    public function updateDataDatabase(Request $request): RedirectResponse
    {
        try {
            $corDetails = User::find($request->user()->id)->coordinator;
            $corId = $corDetails['id'];

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

            // Delete all board members from 'outgoing_boards' table
            // OutgoingBoard::query()->delete();
            // DB::table('boards')
            //     ->truncate();

            // Update all outgoing board members in 'users' table to be inactive
            DB::table('users')
                ->where(function ($query) {
                    $query->where('user_type', 'outgoing');
                })
                ->update([
                    'is_active' => '0',
                ]);

            // Update admin table: Set specified columns to 1
            DB::table('admin')->update([
                'copy_chapters' => '1',
                'copy_users' => '1',
                'copy_boarddetails' => '1',
                'copy_Coordinators' => '1',
                // 'delete_outgoing' => '1',
                // 'outgoing_inactive' => '1',
                'updated_id' => $corId,
                'updated_at' => Carbon::today(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/admin')->with('success', 'User data tables successfully updated, copied, and renamed..');
            }

        return redirect()->to('/admin')->with('fail', 'An error occurred while updating the user data tables.');
    }

    /**
     * Udate Coordinator EOY Menu Items
     */
    public function updateEOYCoordinator(Request $request): RedirectResponse
    {
        try {
            $corDetails = User::find($request->user()->id)->coordinator;
            $corId = $corDetails['id'];

            // Update admin table: Set specified columns to 1
            DB::table('admin')->update([
                'eoy_testers' => '1',
                'eoy_coordinators' => '1',
                'updated_id' => $corId,
                'updated_at' => Carbon::today(),
            ]);

            // Return success message
            return redirect()->to('/admin')->with('success', 'Coordinator Menus have been activated.');
        } catch (Exception $e) {
            // Log the error message
            Log::error('An error occurred while activating coordinator menus: '.$e->getMessage());

            // Return error message, this is where the error should be flashed
            return redirect()->to('/admin')->with('fail', 'An error occurred while activating coordinator menus.');
        }
    }

    /**
     * Udate Chapter EOY Buttons
     */
    public function updateEOYChapter(Request $request): RedirectResponse
    {
        try {
            $corDetails = User::find($request->user()->id)->coordinator;
            $corId = $corDetails['id'];

            // Update admin table: Set specified columns to 1
            DB::table('admin')->update([
                'eoy_boardreport' => '1',
                'eoy_financialreport' => '1',
                'updated_id' => $corId,
                'updated_at' => Carbon::today(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/admin')->with('success', 'Chapter Buttons have been activated.');
            }

        return redirect()->to('/admin')->with('fail', 'An error occurred while activating chapter buttons.');
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
        // $einLetterDrive = $request->input('einLetterDrive');
        // $eoyDrive = $request->input('eoyDrive');
        // $eoyDriveYear = $request->input('eoyDriveYear');
        // $resourcesDrive = $request->input('resourcesDrive');
        // $disbandDrive = $request->input('disbandDrive');
        // $goodStandingDrive = $request->input('goodStandingDrive');
        // $probationDrive = $request->input('probationDrive');
        try {
            $drive = GoogleDrive::firstOrFail();
            $drive->ein_letter_uploads = $request->input('einLetterDrive');
            $drive->eoy_uploads = $request->input('eoyDrive');
            $drive->eoy_uploads_year = $request->input('eoyDriveYear');
            $drive->resources_uploads = $request->input('resourcesDrive');
            $drive->disband_letter = $request->input('disbandDrive');
            $drive->good_standing_letter = $$request->input('goodStandingDrive');
            $drive->probation_letter = $request->input('probationDrive');

            $drive->save();

            DB::commit();

            $message = 'Google Drive ID updated successfully';

            return response()->json(['status' => 'success', 'message' => $message, 'redirect' => route('admin.googledrive'),]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback transaction on exception
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            return response()->json(['status' => 'error', 'message' => $message, 'redirect' => route('admin.googledrive'),]);
        }
    }

}
