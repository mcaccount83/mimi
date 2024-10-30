<?php

namespace App\Http\Controllers;

use App\Mail\BigSisterWelcome;
use App\Mail\CoordinatorRetireAdmin;
use App\Models\CoordinatorPosition;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CoordinatorController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->middleware('auth')->except('logout');
        $this->userController = $userController;
    }

    // /**
    //  * Reset Password
    //  */
    // public function updatePassword(Request $request)
    // {
    //     $request->validate([
    //         'current_password' => ['required'],
    //         'new_password' => ['required', 'string', 'min:8', 'confirmed'],
    //     ]);

    //     $user = $request->user();

    //     // Ensure the current password is correct
    //     if (!Hash::check($request->current_password, $user->password)) {
    //         return response()->json(['error' => 'Current password is incorrect'], 400);
    //     }

    //     // Update the user's password
    //     $user->password = Hash::make($request->new_password);
    //     $user->remember_token = null; // Reset the remember token
    //     $user->save();

    //     return response()->json(['message' => 'Password updated successfully']);
    // }

    // /**
    //  * Verify Current Password for Reset
    //  */
    // public function checkCurrentPassword(Request $request)
    // {
    //     $request->validate([
    //         'current_password' => 'required',
    //     ]);

    //     $user = $request->user();
    //     $isValid = Hash::check($request->current_password, $user->password);

    //     return response()->json(['isValid' => $isValid]);
    // }

     /**
     * Load Conference Coordinators For Each Conference
     */
    // public function load_cc_coordinators($chConf, $chPcid)
    // {
    //     $reportingList = DB::table('coordinator_reporting_tree')
    //         ->select('*')
    //         ->where('id', '=', $chPcid)
    //         ->get();

    //     foreach ($reportingList as $key => $value) {
    //         $reportingList[$key] = (array) $value;
    //     }
    //     $filterReportingList = array_filter($reportingList[0]);
    //     unset($filterReportingList['id']);
    //     unset($filterReportingList['layer0']);
    //     $filterReportingList = array_reverse($filterReportingList);
    //     $str = '';
    //     $array_rows = count($filterReportingList);
    //     $i = 0;
    //     $coordinator_array = [];
    //     foreach ($filterReportingList as $key => $val) {
    //         $corList = DB::table('coordinators as cd')
    //             ->select('cd.id as cid', 'cd.first_name as fname', 'cd.last_name as lname', 'cp.long_title as pos')
    //             ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
    //             ->where('cd.id', '=', $val)
    //             ->get();
    //         $coordinator_array[$i] = ['id' => $corList[0]->cid,
    //             'first_name' => $corList[0]->fname,
    //             'last_name' => $corList[0]->lname,
    //             'pos' => $corList[0]->pos];

    //         $i++;
    //     }
    //     $coordinator_count = count($coordinator_array);

    //     for ($i = 0; $i < $coordinator_count; $i++) {
    //         $cc_fname = $coordinator_array[$i]['first_name'];
    //         $cc_lname = $coordinator_array[$i]['last_name'];
    //         $cc_pos = $coordinator_array[$i]['pos'];

    //     }

    //     switch ($chConf) {
    //         case 1:
    //             $cc_fname = $cc_fname;
    //             $cc_lname = $cc_lname;
    //             $cc_pos = $cc_pos;
    //             break;
    //         case 2:
    //             $cc_fname = $cc_fname;
    //             $cc_lname = $cc_lname;
    //             $cc_pos = $cc_pos;
    //             break;
    //         case 3:
    //             $cc_fname = $cc_fname;
    //             $cc_lname = $cc_lname;
    //             $cc_pos = $cc_pos;
    //             break;
    //         case 4:
    //             $cc_fname = $cc_fname;
    //             $cc_lname = $cc_lname;
    //             $cc_pos = $cc_pos;
    //             break;
    //         case 5:
    //             $cc_fname = $cc_fname;
    //             $cc_lname = $cc_lname;
    //             $cc_pos = $cc_pos;
    //             break;
    //     }

    //     return [
    //         'cc_fname' => $cc_fname,
    //         'cc_lname' => $cc_lname,
    //         'cc_pos' => $cc_pos,
    //         'coordinator_array' => $coordinator_array,
    //     ];
    // }

    /**
     * get Coordinator Email -- Mail for full Coordinator Downline
     */
    // public function getCoordMail($pcid)
    // {
    //     $reportingList = DB::table('coordinator_reporting_tree')
    //         ->select('*')
    //         ->where('id', '=', $pcid)
    //         ->get();
    //     foreach ($reportingList as $key => $value) {
    //         $reportingList[$key] = (array) $value;
    //     }
    //     $filterReportingList = array_filter($reportingList[0]);
    //     unset($filterReportingList['id']);
    //     unset($filterReportingList['layer0']);
    //     $filterReportingList = array_reverse($filterReportingList);
    //     $str = '';
    //     $array_rows = count($filterReportingList);
    //     $down_line_email = [];
    //     foreach ($filterReportingList as $key => $val) {
    //         //if($corId != $val && $val >1){
    //         if ($val > 1) {
    //             $corList = DB::table('coordinators as cd')
    //                 ->select('cd.email as cord_email')
    //                 ->where('cd.id', '=', $val)
    //                 ->where('cd.is_active', '=', 1)
    //                 ->get();
    //             if (count($corList) > 0) {
    //                 if ($down_line_email == '') {
    //                     $down_line_email[] = $corList[0]->cord_email;
    //                 } else {
    //                     $down_line_email[] = $corList[0]->cord_email;
    //                 }
    //             }
    //         }
    //     }

    //     return $down_line_email;
    // }

    /**
     * Coordiantor Dashboard - Login Main Screen
     */
    public function showCoordinatorDashboard(Request $request): View
    {
        $corDetails = $request->user()->Coordinators;
        $corId = $corDetails->id;
        $corConfId = $corDetails->conference_id;
        $corReportId = $corDetails->report_id;
        $positionid = $corDetails->position_id;
        $secpositionid = $corDetails->sec_position_id;
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $corId)
            ->get();
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id')
            ->get();

        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->orderBy('long_name')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->orderBy('conference_name')
            ->get();
        $positionList = DB::table('coordinator_position')
            ->select('id', 'long_title')
            ->orderBy('long_title')
            ->get();

        $primaryCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'cd.phone as cor_phone', 'cd.alt_phone as cor_altphone')
            ->where('cd.id', '=', $corReportId)
            ->where('cd.is_active', '=', '1')
            ->get();
        if (count($primaryCoordinatorList) == 0) {
            $primaryCoordinatorList[0] = ['cor_f_name' => '', 'cor_l_name' => '', 'cor_email' => '', 'cor_phone' => '', 'cor_altphone' => ''];
            $primaryCoordinatorList = json_decode(json_encode($primaryCoordinatorList));
        }
        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $coordinatorDetails[0]->birthday_month_id;

        $data = ['primaryCoordinatorList' => $primaryCoordinatorList, 'positionid' => $positionid, 'secpositionid' => $secpositionid, 'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('coordinators.coorddashboard')->with($data);
    }

    /**
     * Update Coordiantor Dashboard - Login Main Screen
     */
    public function updateCoordinatorDashboard(Request $request): RedirectResponse
    {
        $corDetails = $request->user()->Coordinators;
        $corId = $corDetails->id;
        $lastUpdatedBy = $corDetails->first_name . ' ' . $corDetails->last_name;

        $corDetails = DB::table('coordinators')
        ->where('id', '=', $corId)
        ->first();  // Use first() instead of get() to get a single record

        // Convert checkboxes to 1 if checked, 0 if not
        $updateData = [
            'todo_check_chapters' => $request->has('todo_check_chapters') ? 1 : 0,
            'todo_send_rereg' => $request->has('todo_send_rereg') ? 1 : 0,
            'todo_send_late' => $request->has('todo_send_late') ? 1 : 0,
            'todo_record_rereg' => $request->has('todo_record_rereg') ? 1 : 0,
            'todo_record_m2m' => $request->has('todo_record_m2m') ? 1 : 0,
            'todo_export_reports' => $request->has('todo_export_reports') ? 1 : 0,
            'todo_export_int_reports' => $request->has('todo_export_int_reports') ? 1 : 0,
            'todo_election_faq' => $request->has('todo_election_faq') ? 1 : 0,
            'todo_election_due' => $request->has('todo_election_due') ? 1 : 0,
            'todo_financial_due' => $request->has('todo_financial_due') ? 1 : 0,
            'todo_990_due' => $request->has('todo_990_due') ? 1 : 0,
            'todo_welcome' => $request->has('todo_welcome') ? 1 : 0,
            'dashboard_updated' => now(),
            'last_updated_by' => $lastUpdatedBy,
            'last_updated_date' => now(),
        ];

        DB::beginTransaction();
        try {
            DB::table('coordinators')
                ->where('id', $corId)
                ->update($updateData);

                DB::commit();

            } catch (\Exception $e) {
                // Rollback Transaction
                DB::rollback();
                // Log the error
                Log::error($e);
                return redirect()->to('/coordinator/dashboard')->with('fail', 'Something went wrong, Please try again.');
        }
        return redirect()->to('/coordinator/dashboard')->with('success', 'Coordinator dashboard updated successfully');
    }

    /**
     * Coordiantor Listing
     */
    public function showCoordinators(Request $request): View
    {
        //Get Coordinator Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

         // Get the conditions
         $conditions = getPositionConditions($positionId, $secPositionId);

         if ($conditions['coordinatorCondition']) {
            // Load Reporting Tree
            $coordinatorData = $this->userController->loadReportingTree($corId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = DB::table('coordinators as cd')
        ->select('cd.id as cor_id', 'cd.home_chapter as cor_chapter', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email',
            'cd.phone as cor_phone', 'cd.report_id as report_id', 'cp.short_title as position', 'pos2.long_title as sec_pos', 'pos3.long_title as display_pos',
            'cd.conference_id as conf', 'cd.coordinator_start_date as coordinator_start_date', 'rg.short_name as reg', 'report.first_name as report_fname',
            'report.last_name as report_lname', 'cf.short_name as conf')
        ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
        ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
        ->leftJoin('coordinator_position as pos3', 'pos3.id', '=', 'cd.display_position_id')
        ->join('region as rg', 'rg.id', '=', 'cd.region_id')
        ->join('conference as cf', 'cf.id', '=', 'cd.conference_id')
        ->leftJoin('coordinators as report', 'report.id', '=', 'cd.report_id')
        ->where('cd.is_active', '=', '1');

        if ($conditions['founderCondition']) {
            $baseQuery;
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('cd.conference_id', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('cd.region_id', '=', $corRegId);
        } else {
            $baseQuery->whereIn('cd.report_id', $inQryArr);
        }

        if (isset($_GET['check']) && $_GET['check'] == 'yes') {
            $checkBoxStatus = 'checked';
            $baseQuery->where('cd.report_id', $corId)
                ->where('cd.id', '!=', $corId)
                ->orderBy('cd.coordinator_start_date');
        } else {
            $checkBoxStatus = '';
            $baseQuery->where('cd.id', '!=', $corId)
            ->orderBy('cd.coordinator_start_date');
        }

        $coordinatorList = $baseQuery->get();

        //Get the e-mail addresses for all the listed coordinators
        $emailListCord = '';
        $row_count = count($coordinatorList);
        for ($row = 0; $row < $row_count; $row++) {
            $email = $coordinatorList[$row]->cor_email;
            $escaped_email = str_replace("'", "\\'", $email);

            if ($emailListCord == '') {
                $emailListCord = $escaped_email;
            } else {
                $emailListCord .= ';'.$escaped_email;
            }
        }

        $countList = count($coordinatorList);
        $data = ['countList' => $countList, 'corId' => $corId, 'coordinatorList' => $coordinatorList, 'checkBoxStatus' => $checkBoxStatus, 'emailListCord' => $emailListCord];

        return view('coordinators.coordlist')->with($data);
    }

    /**
     * Coordiantor New Create
     */
    public function showCoordinatorNew(Request $request): View
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];

        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];

        $data = ['regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('coordinators.coordnew')->with($data);
    }

    /**
     * Create New Coordiantor (store)
     */
    public function updateCoordinatorNew(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $corRegId = $corDetails['region_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $new_layer_id = $corlayerId + 1;
        $input = $request->all();

        DB::beginTransaction();
        try {
            $userId = DB::table('users')->insertGetId(
                ['first_name' => $input['cord_fname'],
                    'last_name' => $input['cord_lname'],
                    'email' => $input['cord_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'coordinator',
                    'is_active' => 1]
            );

            $cordId = DB::table('coordinators')->insertGetId(
                ['user_id' => $userId,
                    'conference_id' => $corConfId,
                    'region_id' => $corRegId,
                    'layer_id' => $new_layer_id,
                    'first_name' => $input['cord_fname'],
                    'last_name' => $input['cord_lname'],
                    'position_id' => 1,
                    'display_position_id' => 1,
                    'email' => $input['cord_email'],
                    'sec_email' => $input['sec_email'],
                    'report_id' => $corId,
                    'address' => $input['cord_addr'],
                    'city' => $input['cord_city'],
                    'state' => $input['cord_state'],
                    'zip' => $input['cord_zip'],
                    'country' => 'USA',
                    'phone' => $input['cord_phone'],
                    'alt_phone' => $input['cord_altphone'],
                    'birthday_month_id' => $input['cord_month'],
                    'birthday_day' => $input['cord_day'],
                    'home_chapter' => $input['cord_chapter'],
                    'coordinator_start_date' => date('Y-m-d H:i:s'),
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1]
            );

            $cordReportingTree = DB::table('coordinator_reporting_tree')
                ->select('layer0', 'layer1', 'layer2', 'layer3', 'layer4', 'layer5', 'layer6', 'layer7', 'layer8')
                ->where('id', '=', $cordId)
                ->limit(1)
                ->get();
            $layer0 = $cordReportingTree[0]->layer0;
            $layer1 = $cordReportingTree[0]->layer1;
            $layer2 = $cordReportingTree[0]->layer2;
            $layer3 = $cordReportingTree[0]->layer3;
            $layer4 = $cordReportingTree[0]->layer4;
            $layer5 = $cordReportingTree[0]->layer5;
            $layer6 = $cordReportingTree[0]->layer6;
            $layer7 = $cordReportingTree[0]->layer7;
            $layer8 = $cordReportingTree[0]->layer8;
            $coordinator_id = $cordId;
            switch ($new_layer_id) {
                case 0:
                    $layer0 = $coordinator_id;
                    $layer1 = null;
                    $layer2 = null;
                    $layer3 = null;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 1:
                    $layer1 = $coordinator_id;
                    $layer2 = null;
                    $layer3 = null;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 2:
                    $layer2 = $coordinator_id;
                    $layer3 = null;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 3:
                    $layer3 = $coordinator_id;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 4:
                    $layer4 = $coordinator_id;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 5:
                    $layer5 = $coordinator_id;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 6:
                    $layer6 = $coordinator_id;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 7:
                    $layer7 = $coordinator_id;
                    $layer8 = null;
                    break;
                case 7:
                    $layer8 = $coordinator_id;
                    break;
            }
            $coord = DB::table('coordinator_reporting_tree')->insert(
                [
                    'layer0' => $layer0,
                    'layer1' => $layer1,
                    'layer2' => $layer2,
                    'layer3' => $layer3,
                    'layer4' => $layer4,
                    'layer5' => $layer5,
                    'layer6' => $layer6,
                    'layer7' => $layer7,
                    'layer8' => $layer8,
                ]
            );
            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            echo $e->getMessage();
            exit;
            // Log the error
            Log::error($e);

            return redirect()->to('/')->with('fail', 'Something went wrong, Please try again..');
        }

        return redirect()->to('/coordinator/coordlist')->with('success', 'Coordinator created successfully.');
    }

    /**
     * Edit Coordiantor
     */
    public function showCoordinatorView(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $id)
            ->get();
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->orderBy('long_name')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->orderBy('conference_name')
            ->get();
        $positionList = DB::table('coordinator_position')
            ->select('id', 'long_title')
            ->orderBy('long_title')
            ->get();

        $primaryCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();
        $directReportTo = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.report_id', '=', $id)
            ->where('cd.is_active', '=', '1')
            ->get();

        $directChapterTo = DB::table('chapters as ch')
            ->select('ch.id as ch_id', 'ch.name as ch_name', 'st.state_short_name as st_name')
            ->join('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.primary_coordinator_id', '=', $id)
            ->where('ch.is_active', '=', '1')
            ->get();
        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG',
            '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $coordinatorDetails[0]->birthday_month_id;

        $data = ['directChapterTo' => $directChapterTo, 'directReportTo' => $directReportTo, 'primaryCoordinatorList' => $primaryCoordinatorList,
            'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails,
            'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth, 'cor_id' => $corId];

        return view('coordinators.coordview')->with($data);
    }

    /**
     * Update Coordinator (store)
     */
    public function updateCoordinator(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $cordinatorId = $id;

        if ($request->input('cord_fname') != '' && $request->input('cord_lname') != '' && $request->input('cord_email') != '') {
            $corDetails = DB::table('coordinators')
                ->select('id', 'user_id')
                ->where('id', '=', $cordinatorId)
                ->get();
            if (count($corDetails) != 0) {
                $userId = $corDetails[0]->user_id;
                $cordId = $corDetails[0]->id;

                $user = User::find($userId);
                $user->first_name = $request->input('cord_fname');
                $user->last_name = $request->input('cord_lname');
                $user->email = $request->input('cord_email');
                if ($request->input('cord_pswd_chg') == '1') {
                    $user->password = Hash::make($request->input('cord_pswd_cnf'));
                }
                $user->updated_at = date('Y-m-d H:i:s');
                $user->save();

                DB::table('coordinators')
                    ->where('id', $cordinatorId)
                    ->update(['first_name' => $request->input('cord_fname'),
                        'last_name' => $request->input('cord_lname'),
                        'email' => $request->input('cord_email'),
                        'sec_email' => $request->input('cord_sec_email'),
                        'address' => $request->input('cord_addr'),
                        'city' => $request->input('cord_city'),
                        'state' => $request->input('cord_state'),
                        'zip' => $request->input('cord_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('cord_phone'),
                        'alt_phone' => $request->input('cord_altphone'),
                        'home_chapter' => $request->input('cord_chapter'),
                        'birthday_month_id' => $request->input('cord_month'),
                        'birthday_day' => $request->input('cord_day'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s')]);
            }
        }

        return redirect()->to('/coordinator/coordlist')->with('success', 'Coordinator updated successfully.');
    }

    /**
     * Change Coordinator Role
     */
    public function showCoordinatorRoleView(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corPosId = $corDetails['position_id'];
        $corRegId = $corDetails['region_id'];

        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $id)
            ->get();

        $conference_id = $coordinatorDetails[0]->conference_id;
        $position_id = $coordinatorDetails[0]->position_id;
        $region_id = $coordinatorDetails[0]->region_id;

        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id')
            ->get();
        // $regionList = DB::table('region')
        //     ->select('id', 'long_name')
        //     ->where('conference_id', '=', $corConfId)
        //     ->orderBy('long_name')
        //     ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $conference_id)
            ->orderBy('long_name')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            // ->where('id', '>=', 0)
            ->orderBy('conference_name')
            ->get();
        $positionList = DB::table('coordinator_position')
            ->select('id', 'long_title', 'level_id')
            ->orderBy('id')
            ->get();

        /***Query For Report To in Frst Section */
            $primaryCoordinatorList = DB::table('coordinators as cd')
                ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                ->where('cd.conference_id', $conference_id)
                ->where('cd.position_id', '>', $position_id)
                ->where('cd.position_id', '>', 1)
                ->where(function($query) use ($region_id) {
                    $query->where('cd.region_id', $region_id)
                        ->orWhereIn('cd.position_id', [6, 7]);
                })
                ->where('cd.is_active', 1)
                ->orderBy('cd.position_id')
                ->orderBy('cd.first_name')
                ->orderBy('cd.last_name')
                ->get();

        /***Query For Direct Report Dropdown in Bottom Section */
            $directReportTo = DB::table('coordinators as cd')
                ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                ->where('cd.conference_id', $conference_id)
                ->where('cd.position_id', '<', $position_id)
                ->where(function($query) use ($region_id) {
                    $query->where('cd.region_id', $region_id)
                        ->orWhereIn('cd.position_id', [6, 7]);
                })
                ->where('cd.is_active', 1)
                ->orderBy('cd.position_id')
                ->orderBy('cd.first_name')
                ->orderBy('cd.last_name')
                ->get();

        /***Query For Primary For Dropdown in Bottom Section */
        if ($region_id == 0) {
            $primaryChapterList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'st.state_short_name as state')
                ->join('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.conference', '=', $conference_id)
                ->where('chapters.is_active', '=', '1')
                ->orderBy('st.state_short_name')
                ->get();
        } else {
            $primaryChapterList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'st.state_short_name as state')
                ->join('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.region', '=', $region_id)
                ->where('chapters.is_active', '=', '1')
                ->orderBy('st.state_short_name')
                ->orderBy('chapters.name')
                ->get();
        }

        $directReportToHTML = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->join('region as rg', 'cd.region_id', '=', 'rg.id')
            ->where('cd.report_id', '=', $id)
            ->where('cd.is_active', '=', '1')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $coordinatorDetails[0]->birthday_month_id;
        if ($coordinatorDetails[0]->last_promoted == '0000-00-00') {
            $lastPromoted = null;
        } else {
            $lastPromoted = $coordinatorDetails[0]->last_promoted;
        }
        $data = ['lastPromoted' => $lastPromoted, 'directReportToHTML' => $directReportToHTML, 'primaryChapterList' => $primaryChapterList, 'directReportTo' => $directReportTo, 'primaryCoordinatorList' => $primaryCoordinatorList, 'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth, 'position_id' => $position_id];

        return view('coordinators.coordroleview')->with($data);

    }

    /**
     * Get Region List -- auto updates dropdown menu in top section of update role screen when conference changes.
     */
    public function getRegionList($corConfId): JsonResponse
    {
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name')
            ->get();

        $html = '<option value="">Select Region</option><option value="0">None</option>';
        foreach ($regionList as $list) {
            $html .= '<option value="'.$list->id.'">'.$list->long_name.'</option>';
        }

        return response()->json(['html' => $html]);

    }

    /**
     * Get Reporting List -- auto updates dropdown menu in top section of update role screen when region changes.
     */
    public function getReportingList(): JsonResponse
    {
        $conference_id = $_GET['conf_id'];
        $position_id = $_GET['pos_id'];
        $region_id = $_GET['reg_id'];

        $reportCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', $conference_id)
            ->where('cd.position_id', '>', $position_id)
            ->where('cd.position_id', '>', 1)
            ->where(function($query) use ($region_id) {
                $query->where('cd.region_id', $region_id)
                    ->orWhereIn('cd.position_id', [6, 7]);
            })
            ->where('cd.is_active', 1)
            ->orderBy('cd.position_id')
            ->orderBy('cd.first_name')
            ->orderBy('cd.last_name')
            ->get();

        $html = '<option value=""></option>';
        foreach ($reportCoordinatorList as $list) {
            $html .= '<option value="'.$list->cid.'">'.$list->cor_f_name.' '.$list->cor_l_name.' ('.$list->pos.')</option>';
        }

        return response()->json(['html' => $html]);
    }

    /**
     * Get Direct Report -- auto updates dropdown menu in bottom section of update role screen when region changes.
     */
    public function getDirectReportingList(): JsonResponse
    {
        $conference_id = $_GET['conf_id'];
        $position_id = $_GET['pos_id'];
        $region_id = $_GET['reg_id'];

        $directReportTo = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', $conference_id)
            ->where('cd.position_id', '<', $position_id)
            ->where(function($query) use ($region_id) {
                $query->where('cd.region_id', $region_id)
                    ->orWhereIn('cd.position_id', [6, 7]);
            })
            ->where('cd.is_active', 1)
            ->orderBy('cd.position_id')
            ->orderBy('cd.first_name')
            ->orderBy('cd.last_name')
            ->get();

            $html = '<option value=""></option>';
            foreach ($directReportTo as $list) {
                $html .= '<option value="'.$list->cid.'">'.$list->cor_f_name.' '.$list->cor_l_name.' ('.$list->pos.')</option>';
            }

            return response()->json(['html' => $html]);
        }

    /**
     * Get Primary Coordinator -- auto updates dropdown menu in bottom section of update role screen when region changes.
     */
    public function getChapterPrimaryFor(): JsonResponse
    {
        $conference_id = $_GET['conf_id'];
        $position_id = $_GET['pos_id'];
        $region_id = $_GET['reg_id'];


        if ($region_id == 0) {
            $primaryChapterList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'st.state_short_name as state')
                ->join('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.conference', '=', $conference_id)
                ->where('chapters.is_active', '=', '1')
                ->orderBy('st.state_short_name')
                ->get();
        } else {
            $primaryChapterList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'st.state_short_name as state')
                ->join('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.region', '=', $region_id)
                ->where('chapters.is_active', '=', '1')
                ->orderBy('st.state_short_name')
                ->orderBy('chapters.name')
                ->get();
        }

        $html = '<option value=""></option>';
        foreach ($primaryChapterList as $list) {
            $html .= '<option value="'.$list->id.'">'.$list->state.' - '.$list->chapter_name.'</option>';
        }

        return response()->json(['html' => $html]);
    }

    /**
     * Update Coordiantor Role
     * Including LOA & Retiring Cooardinator
     */
    public function updateCoordinatorRole(Request $request, $id)
    {
        // Find the coordinator details for the current user
        $corDetails = User::find($request->user()->id)->Coordinators;

        // Get the necessary details from the coordinator details
        $corId = $corDetails['id'];
        $userName = $corDetails['first_name'].' '.$corDetails['last_name'];
        $userEmail = $corDetails['email'];
        $positionId = $corDetails['position_id'];
        $position = CoordinatorPosition::find($positionId);
        $positionTitle = $position['long_title'];

        $lastUpdatedBy = $userName;
        $cordinatorId = $id;
        $onleave = false;
        $leavedate = null;

        $submit_type = $_POST['submit_type'];

        if ($submit_type == 'Leave') {
            $onleave = true;
            $leavedate = date('Y-m-d');
        }
        if ($submit_type == 'Retire') {
            $reason = $_POST['RetireReason'];
            $userid = $_POST['userid'];
            $coordName = $_POST['coordName'];
            $coordConf = $_POST['coordConf'];
            $email = $_POST['email'];
            DB::beginTransaction();
            try {
                DB::table('coordinators')
                    ->where('id', $cordinatorId)
                    ->update(['reason_retired' => $reason,
                        'zapped_date' => date('Y-m-d'),
                        'is_active' => 0,
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s')]);
                DB::update('UPDATE users SET is_active = ? where id = ?', [0, $userid]);
                DB::commit();
                $webmstremail = DB::table('coordinators')
                    ->select('email')
                    ->where('conference_id', $coordConf)
                    ->where('position_id', 13)
                    ->where('is_active', 1)
                    ->get();
                $mailData = [
                    'coordName' => $coordName,
                    'confNumber' => $coordConf,
                    'email' => $email,
                ];

                $to_email = 'jackie.mchenry@momsclub.org';

                Mail::to($to_email, 'MOMS Club')
                    ->queue(new CoordinatorRetireAdmin($mailData));

                return redirect()->to('/coordinator/retired')->with('success', 'Coordinator retired successfully.');
                exit;
            } catch (\Exception $e) {
                // Rollback Transaction
                DB::rollback();
                // Log the error
                Log::error($e);

                return redirect()->to('/coordinator/coordlist')->with('fail', 'Something went wrong, Please try again.');
            }
        }

        if ($submit_type == 'Leave' || $submit_type == 'RemoveLeave') {
            DB::beginTransaction();
            try {
                DB::table('coordinators')
                    ->where('id', $cordinatorId)
                    ->update(['on_leave' => $onleave,
                        'leave_date' => $leavedate,
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s')]);

                DB::commit();
                if ($submit_type == 'Leave') {
                    return redirect()->back()->with('success', 'Coordinator has been successfully put on Leave');
                } else {
                    return redirect()->back()->with('success', 'Coordinator has been successfully remove on Leave');
                }
                exit;
                // return true;
            } catch (\Exception $e) {
                // Rollback Transaction
                DB::rollback();

                return false;
            }
        }

        if ($submit_type == 'Letter') {
            DB::beginTransaction();
            try {
                $coordinatorDetails = DB::table('coordinators as cd')
                    ->select('cd.*', 'cf.conference_description as conf_name', 'rg.long_name as reg_name', 'cd2.first_name as cor_fname', 'cd2.last_name as cor_lname', 'cd2.email as cor_email',
                        'cd2.phone as cor_phone', 'cd2.conference_id as conf', 'cd2.id as pcid')
                    ->leftJoin('coordinators as cd2', 'cd.report_id', '=', 'cd2.id')
                    ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
                    ->leftJoin('region as rg', 'cd.region_id', '=', 'rg.id')
                    ->where('cd.id', $cordinatorId)
                    ->get();

                $chapters = DB::table('chapters as ch')
                    ->select('ch.name as chapter', 'st.state_short_name as state')
                    ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
                    ->leftJoin('state as st', 'ch.state', '=', 'st.id')
                    ->where('ch.is_Active', '=', '1')
                    ->where('primary_coordinator_id', $cordinatorId)
                    ->orderBy('st.state_short_name')
                    ->orderBy('ch.name')
                    ->get();

                $firstName = $coordinatorDetails[0]->first_name;
                $lastName = $coordinatorDetails[0]->last_name;
                $email = $coordinatorDetails[0]->email;
                $cor_fname = $coordinatorDetails[0]->cor_fname;
                $cor_lname = $coordinatorDetails[0]->cor_lname;
                $cor_email = $coordinatorDetails[0]->cor_email;
                $cor_phone = $coordinatorDetails[0]->cor_phone;
                $conf_name = $coordinatorDetails[0]->conf_name;
                $reg_name = $coordinatorDetails[0]->reg_name;
                $conf = $coordinatorDetails[0]->conf;

                // Call the getCCMemail function
                $corId = $cordinatorId;
                $emailData = $this->userController->loadCoordEmail($corId);
                $coordEmail = $emailData['coordEmail'];
                $coordSecEmail = $emailData['coordSecEmail'];
                $emailListCoord = $emailData['emailListCoord'];

                if ($coordSecEmail !== null) {
                    $to_email = [$coordEmail, $coordSecEmail];
                } else {
                    $to_email = [$coordEmail];
                }
                $cc_email = $emailListCoord;

                $mailData = [
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'email' => $email,
                    'cor_fname' => $cor_fname,
                    'cor_lname' => $cor_lname,
                    'cor_email' => $cor_email,
                    'cor_phone' => $cor_phone,
                    'chapters' => $chapters,
                    'conf_name' => $conf_name,
                    'reg_name' => $reg_name,
                    'userName' => $userName,
                    'userEmail' => $userEmail,
                    'positionTitle' => $positionTitle,
                    'conf' => $conf,
                ];

                Mail::to($to_email)
                    ->cc($cc_email)
                    ->queue(new BigSisterWelcome($mailData));

                DB::commit();

                return redirect()->back()->with('success', 'Welcome letter has been successfully sent');
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error sending welcome letter:', ['error' => $e->getMessage()]);

                return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
            }
        }

        ///Reassign Direct Report Coordinators that Changed
        $rowcountCord = $_POST['CoordinatorCount'];
        for ($i = 0; $i < $rowcountCord; $i++) {
            $new_coordinator_field = 'Report'.$i;
            $new_coordinator_id = $_POST[$new_coordinator_field];

            $coordinator_field = 'CoordinatorIDRow'.$i;
            $coordinator_id = $_POST[$coordinator_field];

            $this->ReassignCoordinator($request, $coordinator_id, $new_coordinator_id, true);
        }

        //Reassign Primary Coordinatory Chapters that Changed
        $rowcountChapter = $_POST['ChapterCount'];
        for ($i = 0; $i < $rowcountChapter; $i++) {
            $coordinator_field = 'PCID'.$i;
            $coordinator_id = $_POST[$coordinator_field];

            $chapter_field = 'ChapterIDRow'.$i;
            $chapter_id = $_POST[$chapter_field];

            $this->ReassignChapter($request, $chapter_id, $coordinator_id, true);
        }

        if ($rowcountCord == 0 && $rowcountChapter == 0) {

        }

        //Reassign Report To / Direct Supervisor that Changed
        $coordinator_id = $request->input('coordinator_id');
        $new_coordinator_id = $request->input('cord_report_pc');
        $this->ReassignCoordinator($request, $coordinator_id, $new_coordinator_id, true);

        //Save other changes
        $position_id = $request->input('cord_pri_pos');
        $display_position_id = $request->input('cord_disp_pos');
        $sec_position_id = $request->input('cord_sec_pos');
        $old_position_id = $request->input('OldPrimaryPosition');
        $old_display_position_id = $request->input('OldDisplayPosition');
        $old_sec_position_id = $request->input('OldSecPosition');
        $promote_date = $request->input('CoordinatorPromoteDate');
        //$report_id = $request->get('cord_report');
        if ($promote_date == '0000-00-00') {
            $promote_date = null;
        }
        if ($position_id != $old_position_id || $display_position_id != $old_display_position_id || $sec_position_id != $old_sec_position_id) {
            $promote_date = $request->input('CoordinatorPromoteDateNew');
        }

        DB::beginTransaction();
        try {
            DB::table('coordinators')
                ->where('id', $cordinatorId)
                ->update([
                    'position_id' => $request->input('cord_pri_pos'),
                    'display_position_id' => $request->input('cord_disp_pos'),
                    'sec_position_id' => $request->input('cord_sec_pos'),
                    'region_id' => $request->input('cord_region'),
                    'conference_id' => $request->input('cord_conf'),
                    // 'home_chapter' => $request->input('cord_chapter'),
                    //'report_id' => $report_id,
                    'last_promoted' => $promote_date,
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                ]);
            DB::commit();

            return redirect()->back()->with('success', 'Coordinator Role has been changed successfully.');
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }
    }

    /**
     * Reassign Chapter
     */
    public function ReassignChapter(Request $request, $chapter_id, $coordinator_id, $check_changed = false)
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        if ($check_changed) {
            $checkPrimaryIdArr = DB::table('chapters as ch')
                ->select('ch.primary_coordinator_id as chpid')
                ->where('ch.id', '=', $chapter_id)
                ->where('ch.is_active', '=', '1')
                ->get();
            $current_primary = $checkPrimaryIdArr[0]->chpid;
            if ($current_primary == $coordinator_id) {
                return true;
            }
        }
        DB::beginTransaction();
        try {
            DB::table('chapters')
                ->where('id', $chapter_id)
                ->update(['primary_coordinator_id' => $coordinator_id,
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s')]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            return false;
        }

    }

    /**
     * Reassign Coordinator
     */
    public function ReassignCoordinator(Request $request, $coordinator_id, $new_coordinator_id, $check_changed = false)
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'] = $coordinator_id;
        $corConfId = $corDetails['conference_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        if ($check_changed) {
                $checkReportIdArr = DB::table('coordinators as cd')
                ->select('cd.report_id as repid')
                ->where('cd.id', '=', $coordinator_id)
                ->where('cd.is_active', '=', '1')
                ->get();
            $current_report = $checkReportIdArr[0]->repid;
            if ($current_report == $new_coordinator_id) {
                return true;
            }
        }

        DB::beginTransaction();
        try {
            //Find new layer
            $query = $layerId = DB::table('coordinators')
                ->select('layer_id')
                ->where('id', $new_coordinator_id)
                ->limit(1)
                ->get();
            $new_layer_id = $query[0]->layer_id + 1;

            //Update their main report ID & layer
            DB::table('coordinators')
                ->where('id', $coordinator_id)
                ->update(['report_id' => $new_coordinator_id,
                    'layer_id' => $new_layer_id,
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s')]);

            //Update the coordinator tree with their new tree relationship
            //Get the current report array
            $cordReportingTree = DB::table('coordinator_reporting_tree')
                ->select('layer0', 'layer1', 'layer2', 'layer3', 'layer4', 'layer5', 'layer6', 'layer7', 'layer8')
                ->where('id', '=', $new_coordinator_id)
                ->limit(1)
                ->get();
            $layer0 = $cordReportingTree[0]->layer0;
            $layer1 = $cordReportingTree[0]->layer1;
            $layer2 = $cordReportingTree[0]->layer2;
            $layer3 = $cordReportingTree[0]->layer3;
            $layer4 = $cordReportingTree[0]->layer4;
            $layer5 = $cordReportingTree[0]->layer5;
            $layer6 = $cordReportingTree[0]->layer6;
            $layer7 = $cordReportingTree[0]->layer7;
            $layer8 = $cordReportingTree[0]->layer8;
            switch ($new_layer_id) {
                case 0:
                    $layer0 = $coordinator_id;
                    $layer1 = null;
                    $layer2 = null;
                    $layer3 = null;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 1:
                    $layer1 = $coordinator_id;
                    $layer2 = null;
                    $layer3 = null;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 2:
                    $layer2 = $coordinator_id;
                    $layer3 = null;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 3:
                    $layer3 = $coordinator_id;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 4:
                    $layer4 = $coordinator_id;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 5:
                    $layer5 = $coordinator_id;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 6:
                    $layer6 = $coordinator_id;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 7:
                    $layer7 = $coordinator_id;
                    $layer8 = null;
                    break;
                case 7:
                    $layer8 = $coordinator_id;
                    break;
            }
            DB::table('coordinator_reporting_tree')
                ->where('id', $coordinator_id)
                ->update(['layer0' => $layer0,
                    'layer1' => $layer1,
                    'layer2' => $layer2,
                    'layer3' => $layer3,
                    'layer4' => $layer4,
                    'layer5' => $layer5,
                    'layer6' => $layer6,
                    'layer7' => $layer7,
                    'layer8' => $layer8,
                ]);
            DB::commit();

            return true;
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            return false;
        }
    }

    /**
     * Retired Coorinators
     */
    public function showRetiredCoordinator(Request $request): View
    {
         //Get Coordinator Details
         $corDetails = User::find($request->user()->id)->Coordinators;
         $corConfId = $corDetails['conference_id'];
         $corRegId = $corDetails['region_id'];
         $positionId = $corDetails['position_id'];
         $secPositionId = $corDetails['sec_position_id'];

          // Get the conditions
          $conditions = getPositionConditions($positionId, $secPositionId);

         $baseQuery = DB::table('coordinators as cd')
            ->select('cd.id as cor_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.reason_retired as cor_reason', 'cd.zapped_date as cor_zapdate',
                'cp.long_title as position', 'rg.short_name as reg', 'cf.short_name as conf')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
            ->leftJoin('coordinator_position as pos3', 'pos3.id', '=', 'cd.display_position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->join('conference as cf', 'cf.id', '=', 'cd.conference_id')
            ->where('cd.is_active', '=', '0')
            ->orderByDesc('cd.zapped_date');

            if ($conditions['founderCondition']) {
                $baseQuery;
            } elseif ($conditions['assistConferenceCoordinatorCondition']) {
                $baseQuery->where('cd.conference_id', '=', $corConfId);
         } elseif ($conditions['regionalCoordinatorCondition']) {
             $baseQuery->where('cd.region_id', '=', $corRegId);
         } else {
             $baseQuery;
         }

         $retiredCoordinatorList = $baseQuery->get();

        //Get Coordinator List mapped with login coordinator
        $data = ['retiredCoordinatorList' => $retiredCoordinatorList];

        return view('coordinators.coordretired')->with($data);
    }

    /**
     * Retired Coordinator Details
     */
    public function showRetiredCoordinatorView(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*')
            ->where('cd.id', '=', $id)
            ->get();
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->orderBy('long_name')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->orderBy('conference_name')
            ->get();
        $positionList = DB::table('coordinator_position')
            ->select('id', 'long_title')
            ->orderBy('long_title')
            ->get();

        $primaryCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();
        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $coordinatorDetails[0]->birthday_month_id;

        $data = ['primaryCoordinatorList' => $primaryCoordinatorList, 'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('coordinators.coordretiredview')->with($data);
    }

    /**
     * Unretire Coordinator
     */
    public function updateUnretireCoordinator($id): RedirectResponse
    {
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*')
            ->where('cd.id', '=', $id)
            ->get();
        if (! empty($coordinatorDetails[0]->user_id)) {
            DB::table('coordinators')
                ->where('id', $id)
                ->update(['reason_retired' => '',
                    'zapped_date' => date('Y-m-d'),
                    'is_active' => 1,
                    'last_updated_by' => date('Y-m-d H:i:s'),
                    'last_updated_date' => date('Y-m-d H:i:s')]);
            DB::update('UPDATE users SET is_active = ? where id = ?', [1, $coordinatorDetails[0]->user_id]);

            DB::commit();
        }

        return redirect()->to('/coordinator/coordlist')->with('success', 'Coordinator successfully unretired');
    }

    /**
     * Coordiantor Profile
     */
    public function showCoordinatorProfile(Request $request): View
    {
        $corDetails = $request->user()->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corReportId = $corDetails['report_id'];
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $corId)
            ->get();
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id')
            ->get();

        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->orderBy('long_name')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->orderBy('conference_name')
            ->get();
        $positionList = DB::table('coordinator_position')
            ->select('id', 'long_title')
            ->orderBy('long_title')
            ->get();

        $primaryCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'cd.phone as cor_phone', 'cd.alt_phone as cor_altphone')
            ->where('cd.id', '=', $corReportId)
            ->where('cd.is_active', '=', '1')
            ->get();
        if (count($primaryCoordinatorList) == 0) {
            $primaryCoordinatorList[0] = ['cor_f_name' => '', 'cor_l_name' => '', 'cor_email' => '', 'cor_phone' => '', 'cor_altphone' => ''];
            $primaryCoordinatorList = json_decode(json_encode($primaryCoordinatorList));
        }
        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $coordinatorDetails[0]->birthday_month_id;

        $data = ['primaryCoordinatorList' => $primaryCoordinatorList, 'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('coordinators.coordprofile')->with($data);
    }

    /**
     * CUpdate Profile (Store)
     */
    public function updateCoordinatorProfile(Request $request): RedirectResponse
    {
        $corDetails = $request->user()->Coordinators;
        $corId = $corDetails->id;
        $lastUpdatedBy = $corDetails->first_name . ' ' . $corDetails->last_name;

        if ($request->input('cord_fname') != '' && $request->input('cord_lname') != '' && $request->input('cord_email') != '') {
            $corDetail = DB::table('coordinators')
                ->select('id', 'user_id')
                ->where('id', '=', $corId)
                ->first(); // Use first() to get a single result

                try {
                    $userId = $corDetail->user_id;

                    $user = User::find($userId);
                        $user->first_name = $request->input('cord_fname');
                        $user->last_name = $request->input('cord_lname');
                        $user->email = $request->input('cord_email');
                        $user->updated_at = now();
                        $user->save();

                    DB::table('coordinators')
                        ->where('id', $corId)
                        ->update([
                            'first_name' => $request->input('cord_fname'),
                            'last_name' => $request->input('cord_lname'),
                            'email' => $request->input('cord_email'),
                            'sec_email' => $request->input('cord_sec_email'),
                            'address' => $request->input('cord_addr'),
                            'city' => $request->input('cord_city'),
                            'state' => $request->input('cord_state'),
                            'zip' => $request->input('cord_zip'),
                            'country' => $request->input('cord_country'),
                            'phone' => $request->input('cord_phone'),
                            'alt_phone' => $request->input('cord_altphone'),
                            'birthday_month_id' => $request->input('cord_month'),
                            'birthday_day' => $request->input('cord_day'),
                            'home_chapter' => $request->input('cord_chapter'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => now()
                        ]);

                    // Commit transaction
                    DB::commit();
                } catch (\Exception $e) {
                    // Rollback Transaction
                    DB::rollback();
                    // Log the error
                    Log::error($e);

                    return redirect()->to('/coordinator/profile')->with('fail', 'Something went wrong, Please try again.');
                }
        }
        return redirect()->to('/coordinator/profile')->with('success', 'Coordinator profile updated successfully');
    }



     /**
     * Edit Coordiantor
     */
    public function viewCoordDetails(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $userId = $corDetails['id'];
        $userConfId = $corDetails['conference_id'];
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*','st.state_short_name as statename', 'cf.conference_description as confname', 'rg.long_name as regname', 'cp.long_title as position',
                'cp3.long_title as display_position', 'cp2.long_title as sec_position', 'cd2.first_name as report_fname', 'cd2.last_name as report_lname')
            ->leftJoin('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')  // Primary Position
            ->leftJoin('coordinator_position as cp2', 'cd.sec_position_id', '=', 'cp2.id')  //Secondary Position
            ->leftJoin('coordinator_position as cp3', 'cd.display_position_id', '=', 'cp3.id')  //Display Position
            ->leftJoin('coordinators as cd2', 'cd.report_id', '=', 'cd2.id') //Supervising Coordinator
            ->leftJoin('state as st', 'cd.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
            ->leftJoin('region as rg', 'cd.region_id', '=', 'rg.id')
            // ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $id)
            ->get();

        $corIsActive = $coordinatorDetails[0]->is_active;
        $corConfId = $coordinatorDetails[0]->conference_id;

        $directReportTo = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.report_id', '=', $id)
            ->where('cd.is_active', '=', '1')
            ->get();

        $directChapterTo = DB::table('chapters as ch')
            ->select('ch.id as ch_id', 'ch.name as ch_name', 'st.state_short_name as st_name')
            ->join('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.primary_coordinator_id', '=', $id)
            ->where('ch.is_active', '=', '1')
            ->get();

        $month = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $birthMonth = $coordinatorDetails[0]->birthday_month_id;
        $birthMonthWords = $month[$birthMonth] ?? 'Status Unknown';

        $data = ['coordinatorDetails' => $coordinatorDetails, 'directReportTo' => $directReportTo, 'directChapterTo' => $directChapterTo, 'corConfId' => $corConfId,
        'corIsActive' => $corIsActive, 'userConfId' => $userConfId, 'userId' => $userId , 'birthMonthWords' => $birthMonthWords];

        return view('coordinators.view')->with($data);
    }

}
