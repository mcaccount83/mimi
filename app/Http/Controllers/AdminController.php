<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBugsAdminRequest;
use App\Http\Requests\AddResourcesAdminRequest;
use App\Http\Requests\AddToolkitAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Requests\UpdateBugsAdminRequest;
use App\Http\Requests\UpdateResourcesAdminRequest;
use App\Http\Requests\UpdateToolkitAdminRequest;
use App\Mail\AdminNewMIMIBugWish;
use App\Models\Admin;
use App\Models\Bugs;
use App\Models\Resources;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Exception;


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
        Mail::to($to_email)->send(new AdminNewMIMIBugWish($mailData));

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

        // // Fetch admin details
        // $file = DB::table('resources')
        //     ->select('resources.*', DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'))
        //     ->leftJoin('coordinator_details as cd', 'resources.updated_id', '=', 'cd.coordinator_id')
        //     ->first(); // Fetch only one record

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
    public function addToolkit(AddToolkitAdminRequest $request)
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
        $file->version = $validatedData['fileVersion'] ?? null;
        $file->link = $validatedData['link'] ?? null;
        $file->file_path = $validatedData['filePath'] ?? null;
        $file->updated_id = $corId;
        $file->updated_date = Carbon::today();

        $file->save();
    }

    public function showAdmin(Request $request): View
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $corId)
            ->get();

         $admin = DB::table('admin')
            ->select('admin.*',
                DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'),)
            ->leftJoin('coordinator_details as cd', 'admin.updated_id', '=', 'cd.coordinator_id')
            ->orderBy('admin.id', 'desc') // Assuming 'id' represents the order of insertion
            ->first();





         // Fetch distinct fiscal years
        $fiscalYears = DB::table('admin')->distinct()->pluck('fiscal_year');

        // Determine if the user is allowed to edit notes and status
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $canEditFiles = ($positionId == 13 || $secPositionId == 13);  //IT Coordinator

        $data = ['admin' => $admin, 'canEditFiles' => $canEditFiles, 'coordinatorDetails' => $coordinatorDetails, 'fiscalYears' => $fiscalYears];

        return view('admin.index')->with($data);
    }

    public function updateAdmin(UpdateAdminRequest $request, $id)
    {
        try {
            $admin = Admin::findOrFail($id);
            $validatedData = $request->validated();

            $corDetails = User::find($request->user()->id)->CoordinatorDetails;
            $corId = $corDetails['coordinator_id'];

            // Convert checkbox values to 1 or null
        $admin->eoy_testers = isset($validatedData['eoy_testers']) ? 1 : null;
        $admin->eoy_coordinators = isset($validatedData['eoy_coordinators']) ? 1 : null;
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
           return redirect()->to('/admin')->with('success', 'Admin data updated successfully.');

        } catch (Exception $e) {
            // Log the error message
            Log::error('Failed to update admin data: ' . $e->getMessage());
            return redirect()->to('/admin')->with('success', 'Admin data failed to update.');
        }
    }

    public function resetYear(Request $request)
    {
        try {
            // Create a new Admin instance
            $admin = new Admin();

            // Calculate the fiscal year (current year - next year)
            $currentYear = Carbon::now()->year;
            $nextYear = $currentYear + 1;
            $fiscalYear = $currentYear . '-' . $nextYear;

            // Set the fiscal year field
            $admin->fiscal_year = $fiscalYear;

            // Save the new entry
            $admin->save();

            // Return a success response to the client
            return redirect()->to('/admin')->with('success', 'Fiscal year reset successfully.');
        } catch (Exception $e) {
            // Log the error message
            Log::error('Failed to reset fiscal year: ' . $e->getMessage());
            return redirect()->to('/admin')->with('error', 'Failed to reset fiscal year.');
        }
    }
}
