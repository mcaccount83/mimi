<?php

namespace App\Http\Controllers;

use App\Mail\CoordinatorRetireAdmin;
use App\Models\FinancialReport;
use App\Models\User;
use App\Models\Admin;
use App\Models\Resources;
use App\Mail\AdminNewMIMIBugWish;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('logout');
    }

    /**
     * View Tasks on Bugs & Enhancements List
     */
    public function showProgression(Request $request): View
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
                DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS reported_by'),
                DB::raw('CASE
                    WHEN priority = 1 THEN "LOW"
                    WHEN priority = 2 THEN "NORMAL"
                    WHEN priority = 3 THEN "HIGH"
                    ELSE "Unknown"
                END as priority_word'))
            ->leftJoin('coordinator_details as cd', 'admin.reported_id', '=', 'cd.coordinator_id')
            ->orderBy('priority', 'desc')
            ->get();

        // Determine if the user is allowed to edit notes and status
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $canEditDetails = ($positionId == 13 || $secPositionId == 13);  //IT Coordinator

        $data = ['admin' => $admin, 'canEditDetails' => $canEditDetails, 'coordinatorDetails' => $coordinatorDetails];

        return view('admin.progression')->with($data);
    }

    /**
     * Add New Task to Bugs & Enhancements List
     */
    public function addProgression(Request $request)
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
        $admin = DB::table('admin')
            ->select('admin.*', DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS reported_by'))
            ->leftJoin('coordinator_details as cd', 'admin.reported_id', '=', 'cd.coordinator_id')
            ->orderBy('priority', 'desc')
            ->first(); // Fetch only one record

        $validatedData = $request->validate([
            'taskNameNew' => 'required|string|max:255',
            'taskDetailsNew' => 'required|string',
            'taskPriorityNew' => 'required',
        ]);

        $task = new Admin;
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
    public function updateProgression(Request $request, $id)
    {
        $validatedData = $request->validate([
            'taskDetails' => 'required|string',
            'taskNotes' => 'nullable|string',
            'taskStatus' => 'required',
            'taskPriority' => 'required',
        ]);

        $task = Admin::findOrFail($id);
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
            ->orderBy('name', 'asc')
            ->get();

        // Determine if the user is allowed to edit notes and status
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $canEditFiles = ($positionId == 13 || $secPositionId == 13);  //IT Coordinator

        $data = ['resources' => $resources, 'canEditFiles' => $canEditFiles, 'coordinatorDetails' => $coordinatorDetails];

        return view('admin.resources')->with($data);
    }

    /**
     * Add New Files or Links to the Resources List
     */
    public function addResources(Request $request)
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

        $validatedData = $request->validate([
            'fileCategoryNew' => 'required',
            'fileNameNew' => 'required|string|max:50',
            'fileDescriptionNew' => 'required|string|max:255',
            'fileTypeNew' => 'required',
            'fileVersionNew' => 'nullable|string|max:25',
            'LinkNew' => 'nullable|string|max:255',
            'filePathNew' => 'nullable|string|max:255',
        ]);

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
     * Update Files or Links on the Resources List
     */
    public function updateResources(Request $request, $id)
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
        $validatedData = $request->validate([
            'fileDescription' => 'required|string|max:255',
            'fileType' => 'required',
            'fileVersion' => 'nullable|string|max:25',
            'link' => 'nullable|string|max:255',
            'filePath' => 'nullable|string|max:255',
        ]);

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
            ->orderBy('name', 'asc')
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
    public function addToolkit(Request $request)
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

        $validatedData = $request->validate([
            'fileCategoryNew' => 'required',
            'fileNameNew' => 'required|string|max:50',
            'fileDescriptionNew' => 'required|string|max:255',
            'fileTypeNew' => 'required',
            'fileVersionNew' => 'nullable|string|max:25',
            'LinkNew' => 'nullable|string|max:255',
            'filePathNew' => 'nullable|string|max:255',
        ]);

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
    public function updateToolkit(Request $request, $id)
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
        $validatedData = $request->validate([
            'fileDescription' => 'required|string|max:255',
            'fileType' => 'required',
            'fileVersion' => 'nullable|string|max:25',
            'link' => 'nullable|string|max:255',
            'filePath' => 'nullable|string|max:255',
        ]);

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


}
