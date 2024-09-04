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
use App\Models\Bugs;
use App\Models\Chapter;
use App\Models\Resources;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('logout');
    }

    /**
     * View Tasks on Bugs & Enhancements List
     */
    public function showBugs(Request $request): View
    {
        $user = User::find($request->user()->id);
        // Check if user is not found
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->CoordinatorDetails;
        // Check if CordDetails is not found for the user
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $corId)
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
            ->leftJoin('coordinator_details as cd', 'bugs.reported_id', '=', 'cd.coordinator_id')
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
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        // Fetch coordinator details
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $corId)
            ->first(); // Fetch only one record

        // Fetch admin details
        $admin = DB::table('bugs')
            ->select('bugs.*', DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS reported_by'))
            ->leftJoin('coordinator_details as cd', 'bugs.reported_id', '=', 'cd.coordinator_id')
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
     * View Resources List
     */
    public function showResources(Request $request): View
    {
        $user = User::find($request->user()->id);
        // Check if user is not found
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->CoordinatorDetails;
        // Check if CordDetails is not found for the user
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $corId)
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
            ->leftJoin('coordinator_details as cd', 'resources.updated_id', '=', 'cd.coordinator_id')
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
        $canEditFiles = ($positionId == 6 || $secPositionId == 6);  //CC Coordinator

        $data = ['resources' => $resources, 'canEditFiles' => $canEditFiles, 'coordinatorDetails' => $coordinatorDetails, 'id' => $id];

        return view('admin.resources')->with($data);
    }

    /**
     * Add New Files or Links to the Resources List
     */
    public function addResources(AddResourcesAdminRequest $request): JsonResponse
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        // Fetch coordinator details
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $corId)
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
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        // Fetch coordinator details
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $corId)
            ->first(); // Fetch only one record

        // Fetch admin details
        $file = DB::table('resources')
            ->select('resources.*', DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'))
            ->leftJoin('coordinator_details as cd', 'resources.updated_id', '=', 'cd.coordinator_id')
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
        // Check if user is not found
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->CoordinatorDetails;
        // Check if CordDetails is not found for the user
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $corId)
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
            ->leftJoin('coordinator_details as cd', 'resources.updated_id', '=', 'cd.coordinator_id')
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
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        // Fetch coordinator details
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $corId)
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
     * Update Files or Links on the Toolkit List
     */
    public function updateToolkit(UpdateToolkitAdminRequest $request, $id)
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        // Fetch coordinator details
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $corId)
            ->first(); // Fetch only one record

        // Fetch admin details
        $file = DB::table('resources')
            ->select('resources.*', DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'))
            ->leftJoin('coordinator_details as cd', 'resources.updated_id', '=', 'cd.coordinator_id')
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

    public function showEOY(Request $request): View
    {
        $user = User::find($request->user()->id);
        // Check if user is not found
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->CoordinatorDetails;
        // Check if CordDetails is not found for the user
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $corId)
            ->get();

        $admin = DB::table('admin')
            ->select('admin.*',
                DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'), )
            ->leftJoin('coordinator_details as cd', 'admin.updated_id', '=', 'cd.coordinator_id')
            ->orderByDesc('admin.id') // Assuming 'id' represents the order of insertion
            ->first();

        // Fetch distinct fiscal years
        $fiscalYears = DB::table('admin')->distinct()->pluck('fiscal_year');

        // Determine if the user is allowed to edit notes and status
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $canEditFiles = ($positionId == 13 || $secPositionId == 13);  //IT Coordinator

        $data = ['admin' => $admin, 'canEditFiles' => $canEditFiles, 'coordinatorDetails' => $coordinatorDetails, 'fiscalYears' => $fiscalYears];

        return view('admin.eoy')->with($data);
    }

    public function updateEOY(UpdateEOYRequest $request, $id): RedirectResponse
    {
        try {
            $admin = Admin::findOrFail($id);
            $validatedData = $request->validated();

            $corDetails = User::find($request->user()->id)->CoordinatorDetails;
            $corId = $corDetails['coordinator_id'];

            // Convert checkbox values to 1 or null
            $admin->eoy_testers = isset($validatedData['eoy_testers']) ? 1 : null;
            $admin->eoy_coordinators = isset($validatedData['eoy_coordinators']) ? 1 : null;
            $admin->eoy_boardreport = isset($validatedData['eoy_boardreport']) ? 1 : null;
            $admin->eoy_financialreport = isset($validatedData['eoy_financialreport']) ? 1 : null;
            $admin->truncate_incoming = isset($validatedData['truncate_incoming']) ? 1 : null;
            $admin->truncate_outgoing = isset($validatedData['truncate_outgoing']) ? 1 : null;
            $admin->copy_FRtoCH = isset($validatedData['copy_FRtoCH']) ? 1 : null;
            $admin->copy_CHtoFR = isset($validatedData['copy_CHtoFR']) ? 1 : null;
            $admin->copy_financial = isset($validatedData['copy_financial']) ? 1 : null;
            $admin->copy_chapters = isset($validatedData['copy_chapters']) ? 1 : null;
            $admin->copy_users = isset($validatedData['copy_users']) ? 1 : null;
            $admin->copy_boarddetails = isset($validatedData['copy_boarddetails']) ? 1 : null;
            $admin->copy_coordinatordetails = isset($validatedData['copy_coordinatordetails']) ? 1 : null;
            $admin->updated_id = $corId;
            $admin->updated_at = Carbon::today();

            $admin->save();

            // Return a success response to the client
            return redirect()->to('/admin/eoy')->with('success', 'Admin data updated successfully.');

        } catch (Exception $e) {
            // Log the error message
            Log::error('Failed to update admin data: '.$e->getMessage());

            return redirect()->to('/admin/eoy')->with('success', 'Admin data failed to update.');
        }
    }

    public function resetYear(Request $request): RedirectResponse
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

            // Return a success response to the client
            return redirect()->to('/admin')->with('success', 'Fiscal year reset successfully.');
        } catch (Exception $e) {
            // Log the error message
            Log::error('Failed to reset fiscal year: '.$e->getMessage());

            return redirect()->to('/admin')->with('error', 'Failed to reset fiscal year.');
        }
    }

    public function showReRegDate(Request $request)
    {
        $user = User::find($request->user()->id);
        // Check if user is not found
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->CoordinatorDetails;
        // Check if CordDetails is not found for the user
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $reChapterList = DB::table('chapters as ch')
            ->select('ch.id', 'ch.notes', 'ch.name', 'ch.state', 'ch.reg_notes', 'ch.next_renewal_year', 'ch.dues_last_paid', 'ch.start_month_id',
                'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name', 'db.month_short_name')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'ch.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'ch.id')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->leftJoin('db_month as db', 'ch.start_month_id', '=', 'db.id')
            ->where('ch.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('ch.name')
            ->get();

        $data = ['reChapterList' => $reChapterList];

        return view('admin.reregdate')->with($data);
    }

    public function editReRegDate(Request $request, $id)
    {
        //$corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $user = User::find($request->user()->id);
        // Check if user is not found
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->CoordinatorDetails;
        // Check if BoardDetails is not found for the user
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->get();

        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $chapterList[0]->start_month_id;

        $data = ['id' => $id, 'currentMonth' => $currentMonth, 'chapterList' => $chapterList, 'stateArr' => $stateArr, 'foundedMonth' => $foundedMonth];

        return view('admin.editreregdate')->with($data);
    }

    public function updateReRegDate(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $chapter = Chapter::find($id);
        DB::beginTransaction();
        try {
            $chapter->start_month_id = $request->input('ch_founddate');
            $chapter->next_renewal_year = $request->input('ch_renewyear');
            $chapter->dues_last_paid = $request->input('ch_duespaid');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');

            $chapter->save();

            DB::commit();

            // Return a success response to the client
            return redirect()->to('/admin/reregdate')->with('success', 'Re-Reg Date updated successfully.');
        } catch (Exception $e) {
            // Log the error message
            Log::error('Failed to reset fiscal year: '.$e->getMessage());

            return redirect()->to('/admin/reregdate')->with('error', 'Failed to update Re-Reg Date.');
        }
    }

    /**
     * mail in queue
     */
    public function showMailQueue(): View
    {
        $Queue = DB::table('jobs')
            ->get();

        $data = ['Queue' => $Queue];

        return view('admin.mailqueue')->with($data);
    }

    /**
     * List of Duplicate Users
     */
    public function showDuplicate(): View
    {

        $userData = DB::table('users')
            ->where('is_active', '=', '1')
            ->groupBy('email')
            ->having(DB::raw('count(email)'), '>', 1)
            ->pluck('email');

        $userList = DB::table('users')
            ->where('is_active', '=', '1')
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

        $userData = DB::table('board_details')
            ->where('is_active', '=', '1')
            ->groupBy('board_id')
            ->having(DB::raw('count(board_id)'), '>', 1)
            ->pluck('board_id');

        $userList = DB::table('board_details')
            ->where('is_active', '=', '1')
            ->whereIn('board_id', $userData)
            ->orderBy('board_id')
            ->get();

        $data = ['userList' => $userList];

        return view('admin.duplicateboardid')->with($data);
    }

    /**
     * VList of users on multiple boards
     */
    public function showMultiple(): View
    {

        $userData = DB::table('board_details')
            ->where('is_active', '=', '1')
            ->groupBy('email')
            ->having(DB::raw('count(email)'), '>', 1)
            ->pluck('email');

        $userList = DB::table('board_details')
            ->where('is_active', '=', '1')
            ->whereIn('email', $userData)
            ->get();

        $data = ['userList' => $userList];

        return view('admin.multipleboard')->with($data);
    }

    /**
     * boards with no president
     */
    public function showNoPresident(): View
    {
        $PresId = DB::table('board_details')
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
        $OutgoingBoard = DB::table('outgoing_board_member')
            ->leftJoin('users', 'outgoing_board_member.email', '=', 'users.email')
            ->leftJoin('chapters', 'outgoing_board_member.chapter_id', '=', 'chapters.id')
            ->select(
                'outgoing_board_member.chapter_id as chapter_id',
                'outgoing_board_member.first_name as first_name',
                'outgoing_board_member.last_name as last_name',
                'outgoing_board_member.email as email',
                'users.user_type as user_type',  // This column will be null if there's no match
                'chapters.name as chapter_name'
            )
            ->orderBy('outgoing_board_member.chapter_id')
            ->get();

        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $OutgoingBoard = DB::table('outgoing_board_member')
                    ->leftJoin('users', 'outgoing_board_member.email', '=', 'users.email')
                    ->leftJoin('chapters', 'outgoing_board_member.chapter_id', '=', 'chapters.id')
                    ->whereNull('users.user_type')  // Only select entries where user_type is null
                    ->select(
                        'outgoing_board_member.chapter_id as chapter_id',
                        'outgoing_board_member.first_name as first_name',
                        'outgoing_board_member.last_name as last_name',
                        'outgoing_board_member.email as email',
                        'users.user_type as user_type',  // This column will be null for unmatched entries
                        'chapters.name as chapter_name'
                    )
                    ->orderBy('outgoing_board_member.chapter_id')
                    ->get();
            }
        } else {
            $checkBoxStatus = '';
        }

        $countList = count($OutgoingBoard);

        $data = ['OutgoingBoard' => $OutgoingBoard, 'countList' => $countList, 'checkBoxStatus' => $checkBoxStatus];

        return view('admin.outgoingboard')->with($data);
    }

    /**
     * Remove Outgoing Board
     */
    public function updateOutgoingBoard()
    {
        // Fetch all outgoing board members
        $outgoingBoardMembers = DB::table('outgoing_board_member')->get();

        // Update the `is_active` column in the `users` table
        foreach ($outgoingBoardMembers as $outgoingMember) {
            // Ensure to update the `is_active` column for users in the `users` table
            DB::table('users')->where('id', $outgoingMember->user_id)->update([
                'is_active' => 0,
                'last_updated_date' => now(),
            ]);
        }

        // Truncate the `outgoing_board_member` table
        DB::table('outgoing_board_member')->truncate();
    }

}
