<?php

namespace App\Http\Controllers;

use App\Mail\CoordinatorRetireAdmin;
use App\Models\FinancialReport;
use App\Models\User;
use App\Models\Admin;
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

        return view('admin')->with($data);
    }

    /**
     * Add New Task to Bugs & Enhancements List
     */
    public function addProgression(Request $request)
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];

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

            $mailData = [
                'taskName' => $task->task,
                'taskDetails' => $task->details,
                'ReportedId' => $task->reported_id,
                'ReportedDate' => $task->reported_date,
            ];

            // Send the email only if the status is 3
            $to_email = 'jackie.mchenry@momsclub.org';
            Mail::to($to_email)->send(new AdminNewMIMIBugWish($mailData));
        }

        $task->save();
    }

}
