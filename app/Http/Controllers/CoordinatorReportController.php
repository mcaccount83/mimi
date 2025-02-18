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
use App\Models\Conference;
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
        $isUtilizationPage = request()->route()->getName() === 'coordreports.coordrptvolutilization';
        if ($isUtilizationPage){
            $baseQuery->orderBy(Conference::select(DB::raw("CASE WHEN short_name = 'Intl' THEN '' ELSE short_name END"))
                ->whereColumn('conference.id', 'coordinators.conference_id')
                ->limit(1)
            )
            ->orderBy(
                Region::select(DB::raw("CASE WHEN short_name = 'None' THEN '' ELSE short_name END"))
                        ->whereColumn('region.id', 'coordinators.region_id')
                        ->limit(1)
            )
            ->orderBy('coordinator_start_date');
        } elseif ($isBirthdayPage) {
            $baseQuery->orderBy(Conference::select(DB::raw("CASE WHEN short_name = 'Intl' THEN '' ELSE short_name END"))
            ->whereColumn('conference.id', 'coordinators.conference_id')
            ->limit(1)
            )
            ->orderBy('birthday_month_id')
                ->orderBy('birthday_day');
        } else{
            $baseQuery->orderBy(Conference::select(DB::raw("CASE WHEN short_name = 'Intl' THEN '' ELSE short_name END"))
            ->whereColumn('conference.id', 'coordinators.conference_id')
            ->limit(1)
            )
            ->orderBy('coordinator_start_date');        }

        return ['query' => $baseQuery, 'checkBoxStatus' => $checkBoxStatus, 'inQryArr' => $inQryArr];

    }

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
