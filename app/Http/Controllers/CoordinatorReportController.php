<?php

namespace App\Http\Controllers;

use App\Mail\CoordinatorRetireAdmin;
use App\Models\CoordinatorPosition;
use App\Models\Coordinators;
use App\Models\CoordinatorTree;
use App\Models\Chapters;
use App\Models\User;
use App\Models\State;
use App\Models\Region;
use App\Models\Month;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CoordinatorReportController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->middleware('auth')->except('logout');
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class);
        $this->userController = $userController;
    }

    /**
     *  Coordinator List Base Query
     */
    public function getActiveBaseQuery($userConfId, $userRegId, $userCdId, $userPositionid, $userSecPositionid)
    {
        $conditions = getPositionConditions($userPositionid, $userSecPositionid);
        if ($conditions['coordinatorCondition']) {
            $coordinatorData = $this->userController->loadReportingTree($userCdId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = Coordinators::with(['state', 'conference', 'region', 'displayPosition', 'mimiPosition', 'secondaryPosition', 'birthdayMonth'])
            // ->where('id', '!=', $userCdId)
            ->where('is_active', 1);

        if ($conditions['founderCondition']) {
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('conference_id', '=', $userConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('region_id', '=', $userRegId);
        } else {
            $baseQuery->whereIn('report_id', $inQryArr);
        }

        if (isset($_GET['check']) && $_GET['check'] == 'yes') {
            $checkBoxStatus = 'checked';
            $baseQuery->where('report_id', '=', $userCdId);
        } else {
            $checkBoxStatus = '';
        }

        $isBirthdayPage = request()->route()->getName() === 'coordreports.coordrptbirthdays';
        if ($isBirthdayPage) {
            $baseQuery->orderBy('birthday_month_id')
                ->orderBy('birthday_day');
        } else{
            $baseQuery->orderBy('coordinator_start_date');
        }

        return ['query' => $baseQuery, 'checkBoxStatus' => $checkBoxStatus, 'inQryArr' => $inQryArr];

    }

    /**
     * Active Coordinator Details Base Query
     */
    // public function getCoordinatorDetails($id)
    // {
    //     $cdDetails = Coordinators::with(['state', 'conference', 'region', 'displayPosition', 'mimiPosition', 'secondaryPosition', 'birthdayMonth',
    //         'reportsTo'])->find($id);
    //     $cdId = $cdDetails->id;
    //     $cdPositionid = $cdDetails->position_id;
    //     $cdIsActive = $cdDetails->is_active;
    //     $regionLongName = $cdDetails->region->long_name;
    //     $conferenceDescription = $cdDetails->conference->conference_description;
    //     $cdConfId = $cdDetails->conference_id;
    //     $cdRegId = $cdDetails->region_id;
    //     $cdRptId = $cdDetails->report_id;
    //     $RptFName = $cdDetails->reportsTo?->first_name;
    //     $RptLName = $cdDetails->reportsTo?->last_name;
    //     $displayPosition = $cdDetails->displayPosition;
    //     $mimiPosition = $cdDetails->mimiPosition;
    //     $secondaryPosition = $cdDetails->secondaryPosition;

    //     $allRegions = Region::with('conference')  // Full List for Dropdown Menu based on Conference
    //         ->where('conference_id', $cdConfId)
    //         ->orwhere('id', '0')
    //         ->get();
    //     $allStates = State::all();  // Full List for Dropdown Menu
    //     $allMonths = Month::all();  // Full List for Dropdown Menu
    //     $allPositions = CoordinatorPosition::all();  // Full List for Dropdown Menu
    //     $allCoordinators = Coordinators::with('conference')  // Full List for Dropdown Menu based on Conference
    //         ->where('conference_id', $cdConfId)
    //         ->where('is_active', 1)
    //         ->get();

    //      // Load ReportsTo Coordinator Dropdown List
    //      $rcDetails = $this->userController->loadReportsToList($cdId, $cdConfId, $cdPositionid);

    //     return ['cdDetails' => $cdDetails, 'cdId' => $cdId, 'cdIsActive' => $cdIsActive, 'regionLongName' => $regionLongName,
    //         'conferenceDescription' => $conferenceDescription, 'cdConfId' => $cdConfId, 'cdRegId' => $cdRegId, 'cdRptId' => $cdRptId,
    //         'RptFName' => $RptFName, 'RptLName' => $RptLName, 'displayPosition' => $displayPosition, 'mimiPosition' => $mimiPosition,
    //         'secondaryPosition' => $secondaryPosition, 'allRegions' => $allRegions, 'allStates' => $allStates, 'allMonths' => $allMonths,
    //         'rcDetails' => $rcDetails, 'allPositions' => $allPositions, 'allCoordinators' => $allCoordinators, 'cdPositionid' => $cdPositionid,
    //     ];
    // }

    /**
     * View the Volunteer Utilization list
     */
    public function showRptVolUtilization(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $coordinatorList = $baseQuery['query']->get();
        $inQryArr = $baseQuery['inQryArr'];

        foreach ($coordinatorList as $list) {
            $reportingData = $this->calculateReporting($list->id, $list->layer_id, $inQryArr);

            $list->direct_report = $reportingData['direct_report'];
            $list->indirect_report = $reportingData['indirect_report'];
            $list->total_report = $reportingData['total_report'];
        }

        $data = ['coordinatorList' => $coordinatorList];

        return view('coordreports.coordrptvolutilization')->with($data);
    }

    /**
     * Calculate Direct/Indirect Reports
     */
    private function calculateReporting($coordinatorId, $corlayerId, $inQryArr)
    {
        // Calculate direct chapter report
        $coordinator_options = Chapters::where('primary_coordinator_id', $coordinatorId)
            ->where('is_active', '1')
            ->get();
        $direct_report = count($coordinator_options);

        // Calculate indirect chapter report
        $sqlLayerId = 'layer'.$corlayerId;
        $reportIdList = CoordinatorTree::where($sqlLayerId, '=', $coordinatorId)->get();

        $inQryStr = '';
        foreach ($reportIdList as $key => $val) {
            $inQryStr .= $val->id.',';
        }
        $inQryStr = rtrim($inQryStr, ',');
        $inQryArr = explode(',', $inQryStr);

        $indirectChapterReport = Chapters::whereIn('primary_coordinator_id', $inQryArr)
            ->where('is_active', '1')
            ->get();

        $indirect_report = count($indirectChapterReport) - $direct_report;

        // Calculate Ttoal chapter report
        $total_report = $direct_report + $indirect_report;

        return ['direct_report' => $direct_report, 'indirect_report' => $indirect_report, 'total_report' => $total_report,];
    }

    /**
     * Coordiantor Appreciation List
     */
    public function showRptAppreciation(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $coordinatorList = $baseQuery['query']->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('coordreports.coordrptappreciation')->with($data);
    }

     /**
     * View the Volunteer Birthday list
     */
    public function showRptBirthdays(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $coordinatorList = $baseQuery['query']->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('coordreports.coordrptbirthdays')->with($data);
    }


    // /**
    //  * Coordiantor Appreciation Details
    //  */
    // public function showRptAppreciationView(Request $request, $id): View
    // {
    //     $user = User::find($request->user()->id);
    //     $userId = $user->id;

    //     $cdDetailsUser = $user->coordinator;
    //     $cdIdUser = $cdDetailsUser->id;
    //     $cdConfIdUser = $cdDetailsUser->conference_id;
    //     $cdRegIdUser = $cdDetailsUser->region_id;
    //     $cdPositionidUser = $cdDetailsUser->position_id;

    //     $baseQuery = $this->getCoordinatorDetails($id);
    //     $cdDetails = $baseQuery['cdDetails'];
    //     $cdId = $baseQuery['cdId'];
    //     $cdIsActive = $baseQuery['cdIsActive'];
    //     $regionLongName = $baseQuery['regionLongName'];
    //     $conferenceDescription = $baseQuery['conferenceDescription'];
    //     $cdConfId = $baseQuery['cdConfId'];
    //     $cdRptId = $baseQuery['cdRptId'];
    //     $RptFName = $baseQuery['RptFName'];
    //     $RptLName = $baseQuery['RptLName'];
    //     $ReportTo = $RptFName.' '.$RptLName;
    //     $displayPosition = $baseQuery['displayPosition'];
    //     $mimiPosition = $baseQuery['mimiPosition'];
    //     $secondaryPosition = $baseQuery['secondaryPosition'];
    //     $cdLeave = $baseQuery['cdDetails']->on_leave;

    //     $data = ['cdDetails' => $cdDetails, 'cdConfId' => $cdConfId, 'conferenceDescription' => $conferenceDescription, 'regionLongName' => $regionLongName,
    //         'cdIsActive' => $cdIsActive, 'cdConfIdUser' => $cdConfIdUser, 'userId' => $userId, 'cdLeave' => $cdLeave, 'ReportTo' => $ReportTo,
    //         'displayPosition' => $displayPosition, 'mimiPosition' => $mimiPosition,
    //         'secondaryPosition' => $secondaryPosition,
    //     ];

    //     return view('coordreports.coordrptappreciationview')->with($data);
    // }

    // /**
    //  * Update Coordiantor Appreciation Gifts (store)
    //  */
    // public function updateRptAppreciation(Request $request, $id): RedirectResponse
    // {
    //     $user = User::find($request->user()->id);
    //     $userId = $user->id;

    //     $cdDetailsUser = $user->coordinator;
    //     $cdIdUser = $cdDetailsUser->id;
    //     $lastUpdatedBy = $cdDetailsUser->first_name.' '.$cdDetailsUser->last_name;

    //     $coordinator = Coordinators::find($id);

    //     DB::beginTransaction();
    //     try {
    //             $coordinator->recognition_toptier = $request->input('cord_toptier');
    //             $coordinator->recognition_year0 = $request->input('cord_year0');
    //             $coordinator->recognition_year1 = $request->input('cord_year1');
    //             $coordinator->recognition_year2 = $request->input('cord_year2');
    //             $coordinator->recognition_year3 = $request->input('cord_year3');
    //             $coordinator->recognition_year4 = $request->input('cord_year4');
    //             $coordinator->recognition_year5 = $request->input('cord_year5');
    //             $coordinator->recognition_year6 = $request->input('cord_year6');
    //             $coordinator->recognition_year7 = $request->input('cord_year7');
    //             $coordinator->recognition_year8 = $request->input('cord_year8');
    //             $coordinator->recognition_year9 = $request->input('cord_year9');
    //             $coordinator->recognition_necklace = (int) $request->has('cord_necklace');
    //             $coordinator->last_updated_by = $lastUpdatedBy;
    //             $coordinator->last_updated_date = date('Y-m-d H:i:s');

    //             $coordinator->save();

    //         DB::commit();

    //         } catch (\Exception $e) {
    //             DB::rollback();
    //             Log::error($e);

    //             return redirect()->to('/coordreports/appreciation')->with('fail', 'Something went wrong, Please try again.');
    //         }

    //     return redirect()->to('/coordreports/appreciation')->with('success', 'Appreciation gifts updated successfully');

    // }


    /**
     * Coordiantor Birthdays Details
     */
    // public function showRptBirthdaysView(Request $request, $id): View
    // {
    //     $corDetails = User::find($request->user()->id)->coordinator;
    //     $corId = $corDetails['id'];
    //     $corConfId = $corDetails['conference_id'];
    //     $coordinatorDetails = DB::table('coordinators as cd')
    //         ->select('cd.id', 'cd.position_id', 'cd.birthday_month_id', 'cd.birthday_day', 'cd.card_sent', 'cd.first_name', 'cd.last_name', 'cd.address', 'cd.city', 'cd.state', 'cd.zip')
    //         ->where('cd.is_active', '=', '1')
    //         ->where('cd.id', '=', $id)
    //         ->get();
    //     $stateArr = DB::table('state')
    //         ->select('state.*')
    //         ->orderBy('id')
    //         ->get();
    //     $countryArr = DB::table('country')
    //         ->select('country.*')
    //         ->orderBy('id')
    //         ->get();
    //     $regionList = DB::table('region')
    //         ->select('id', 'long_name')
    //         ->orderBy('long_name')
    //         ->get();
    //     $confList = DB::table('conference')
    //         ->select('id', 'conference_name')
    //         ->orderBy('conference_name')
    //         ->get();
    //     $positionList = DB::table('coordinator_position')
    //         ->select('id', 'long_title')
    //         ->orderBy('long_title')
    //         ->get();

    //     $primaryCoordinatorList = DB::table('coordinators as cd')
    //         ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
    //         ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
    //         ->where('cd.is_active', '=', '1')
    //         ->orderBy('cd.first_name')
    //         ->get();
    //     $directReportTo = DB::table('coordinators as cd')
    //         ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
    //         ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
    //         ->where('cd.report_id', '=', $id)
    //         ->where('cd.is_active', '=', '1')
    //         ->get();

    //     $data = ['directReportTo' => $directReportTo, 'primaryCoordinatorList' => $primaryCoordinatorList, 'positionList' => $positionList, 'confList' => $confList, 'coordinatorDetails' => $coordinatorDetails, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr];

    //     return view('coordreports.coordrptbirthdaysview')->with($data);
    // }

    /**
     * Update Coordiantor Birthdays (store)
     */
    // public function updateRptBirthdays(Request $request, $id): RedirectResponse
    // {
    //     $corDetails = User::find($request->user()->id)->coordinator;
    //     $corId = $corDetails['id'];
    //     $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
    //     $cordinatorId = $id;

    //     if ($request->input('cord_fname') != '' && $request->input('cord_lname') != '') {
    //         $corDetails = DB::table('coordinators')
    //             ->select('id', 'user_id')
    //             ->where('id', '=', $cordinatorId)
    //             ->get();
    //         if (count($corDetails) != 0) {
    //             try {
    //                 $userId = $corDetails[0]->user_id;
    //                 $cordId = $corDetails[0]->id;

    //                 $user = User::find($userId);
    //                 $user->first_name = $request->input('cord_fname');
    //                 $user->last_name = $request->input('cord_lname');
    //                 $user->updated_at = date('Y-m-d H:i:s');
    //                 $user->save();

    //                 DB::table('coordinators')
    //                     ->where('id', $cordinatorId)
    //                     ->update(['card_sent' => $request->input('card_sent'),
    //                         'last_updated_by' => $lastUpdatedBy,
    //                         'last_updated_date' => date('Y-m-d H:i:s')]);
    //                 DB::commit();
    //             } catch (\Exception $e) {
    //                 // Rollback Transaction
    //                 DB::rollback();
    //                 // Log the error
    //                 Log::error($e);

    //                 return redirect()->to('/coordreports/birthdays')->with('fail', 'Something went wrong, Please try again.');
    //             }
    //         }
    //     }

    //     return redirect()->to('/coordreports/birthdays')->with('success', 'Appreciation gifts updated successfully');

    // }

    /**
     * View the Reporting Tree
     */
    public function showRptReportingTree(Request $request): View
    {
        $coordinator_array = [];
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $cord_pos_id = $request->session()->get('positionid');

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
            // Load Reporting Tree
            $coordinatorData = $this->userController->loadReportingTree($corId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = DB::table('coordinators')
            ->select('coordinators.id AS id', 'coordinators.first_name', 'coordinators.last_name', 'pos1.short_title AS position_title',
                'pos2.short_title AS sec_position_title', 'pos3.short_title AS display_position_title', 'coordinators.layer_id', 'coordinators.report_id',
                'coordinators.report_id AS tree_id', 'region.short_name AS region', 'conference.short_name as conference')
            ->join('coordinator_position as pos1', 'pos1.id', '=', 'coordinators.position_id')
            ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'coordinators.sec_position_id')
            ->leftJoin('coordinator_position as pos3', 'pos3.id', '=', 'coordinators.display_position_id')
            ->join('region', 'coordinators.region_id', '=', 'region.id')
            ->join('conference', 'coordinators.conference_id', '=', 'conference.id')
            ->where('coordinators.on_leave', 0)
            ->where('coordinators.is_active', 1)
            ->orderBy('coordinators.region_id')
            ->orderBy('coordinators.conference_id');

        if ($conditions['founderCondition']) {

        } else {
            $baseQuery->where('coordinators.conference_id', '=', $corConfId);
        }

        $coordinatorDetails = $baseQuery->get();

        foreach ($coordinatorDetails as $key => $value) {
            $coordinator_array[$key] = (array) $value;
        }

        return view('coordreports.coordrptreportingtree', [
            'coordinator_array' => $coordinator_array,
            'cord_pos_id' => $cord_pos_id,
        ]);
    }
}
