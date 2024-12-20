<?php

namespace App\Http\Controllers;

use App\Models\User;
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
     * View the Volunteer Utilization list
     */
    public function showRptVolUtilization(Request $request): View
    {
        //Get Coordinator Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
            // Load Reporting Tree
            $coordinatorData = $this->userController->loadReportingTree($corId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = DB::table('coordinators as cd')
            ->select('cd.id as cor_id', 'cd.layer_id as layer_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.report_id as report_id',
                'cp.long_title as position', 'pos2.long_title as sec_pos', 'cd.conference_id as cor_conf', 'rg.short_name as reg', 'cf.short_name as conf')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.region_id')
            ->orderByDesc('cp.id');

        if ($conditions['founderCondition']) {

        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('cd.conference_id', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('cd.region_id', '=', $corRegId);
        } else {
            $baseQuery->whereIn('cd.id', $inQryArr);
        }

        $coordinatorList = $baseQuery->get();

        foreach ($coordinatorList as $list) {
            $reportingData = $this->calculateReporting($list->cor_id, $list->layer_id, $inQryArr);

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
        $coordinator_options = DB::table('chapters')
            ->select('name')
            ->where('primary_coordinator_id', $coordinatorId)
            ->where('is_active', '1')
            ->get();
        $direct_report = count($coordinator_options);

        // Calculate indirect chapter report
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $reportIdList = DB::table('coordinator_reporting_tree as crt')
            ->select('crt.id')
            ->where($sqlLayerId, '=', $coordinatorId)
            ->get();

        $inQryStr = '';
        foreach ($reportIdList as $key => $val) {
            $inQryStr .= $val->id.',';
        }
        $inQryStr = rtrim($inQryStr, ',');
        $inQryArr = explode(',', $inQryStr);

        $indirectChapterReport = DB::table('chapters')
            ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.inquiries_contact as inq_con', 'chapters.territory as terry', 'chapters.status_id as status', 'chapters.inquiries_note as inq_note')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->get();

        $indirect_report = count($indirectChapterReport) - $direct_report;
        $total_report = $direct_report + $indirect_report;

        return [
            'direct_report' => $direct_report,
            'indirect_report' => $indirect_report,
            'total_report' => $total_report,
        ];
    }

    /**
     * View the Coordinator ToDo List
     */
    public function showRptCoordToDo(Request $request): View
    {
        //Get Coordinator Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
            // Load Reporting Tree
            $coordinatorData = $this->userController->loadReportingTree($corId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = DB::table('coordinators as cd')
            ->select('cd.id as cor_id', 'cd.layer_id as layer_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.report_id as report_id', 'cd.sec_position_id as sec_position_id', 'cp.long_title as position', 'rg.short_name as reg', 'cd.conference_id as cor_conf',
                'cd.todo_month as todo_month', 'cd.todo_check_chapters as todo_check_chapters', 'cd.todo_election_faq as todo_election_faq', 'cd.dashboard_updated as dashboard_updated')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('rg.short_name')
            ->orderByDesc('cp.id');

        if ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('cd.conference_id', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('cd.region_id', '=', $corRegId);
        } else {
            $baseQuery->whereIn('cd.id', $inQryArr);
        }

        $coordinatorList = $baseQuery->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('coordreports.coordrpttodo')->with($data);

    }

    /**
     * Coordiantor Appreciation List
     */
    public function showRptAppreciation(Request $request): View
    {
        //Get Coordinator Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
            // Load Reporting Tree
            $coordinatorData = $this->userController->loadReportingTree($corId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = DB::table('coordinators as cd')
            ->select('cd.id as cor_id', 'cd.layer_id as layer_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email',
                'cd.report_id as report_id', 'cd.sec_position_id as sec_position_id', 'cp.long_title as position', 'rg.short_name as reg', 'cd.conference_id as cor_conf',
                'cd.recognition_year0 as yr_0', 'cd.recognition_year1 as yr_1', 'cd.recognition_year2 as yr_2', 'cd.recognition_year3 as yr_3', 'cd.recognition_year4 as yr_4',
                'cd.recognition_year5 as yr_5', 'cd.recognition_year6 as yr_6', 'cd.recognition_year7 as yr_7', 'cd.recognition_year8 as yr_8', 'cd.recognition_year9 as yr_9',
                'cd.recognition_toptier as toptier', 'cd.coordinator_start_date as start_date', 'cd.recognition_necklace as necklace', 'cf.short_name as conf')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.coordinator_start_date');

        if ($conditions['founderCondition']) {

        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('cd.conference_id', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('cd.region_id', '=', $corRegId);
        } else {
            $baseQuery->whereIn('cd.id', $inQryArr);
        }

        $coordinatorList = $baseQuery->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('coordreports.coordrptappreciation')->with($data);
    }

    /**
     * Coordiantor Appreciation Details
     */
    public function showRptAppreciationView(Request $request, $id): View
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

        $data = ['directReportTo' => $directReportTo, 'primaryCoordinatorList' => $primaryCoordinatorList, 'positionList' => $positionList, 'confList' => $confList, 'coordinatorDetails' => $coordinatorDetails, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr];

        return view('coordreports.coordrptappreciationview')->with($data);
    }

    /**
     * Update Coordiantor Appreciation Gifts (store)
     */
    public function updateRptAppreciation(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $cordinatorId = $id;

        if ($request->input('cord_fname') != '' && $request->input('cord_lname') != '') {
            $corDetails = DB::table('coordinators')
                ->select('id', 'user_id')
                ->where('id', '=', $cordinatorId)
                ->get();
            if (count($corDetails) != 0) {
                try {
                    $userId = $corDetails[0]->user_id;
                    $cordId = $corDetails[0]->id;

                    $user = User::find($userId);
                    $user->first_name = $request->input('cord_fname');
                    $user->last_name = $request->input('cord_lname');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    DB::table('coordinators')
                        ->where('id', $cordinatorId)
                        ->update(['recognition_toptier' => $request->input('cord_toptier'),
                            'recognition_year0' => $request->input('cord_year0'),
                            'recognition_year1' => $request->input('cord_year1'),
                            'recognition_year2' => $request->input('cord_year2'),
                            'recognition_year3' => $request->input('cord_year3'),
                            'recognition_year4' => $request->input('cord_year4'),
                            'recognition_year5' => $request->input('cord_year5'),
                            'recognition_year6' => $request->input('cord_year6'),
                            'recognition_year7' => $request->input('cord_year7'),
                            'recognition_year8' => $request->input('cord_year8'),
                            'recognition_year9' => $request->input('cord_year9'),
                            'recognition_necklace' => (int) $request->has('cord_necklace'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                    DB::commit();
                } catch (\Exception $e) {
                    // Rollback Transaction
                    DB::rollback();
                    // Log the error
                    Log::error($e);

                    return redirect()->to('/coordreports/appreciation')->with('fail', 'Something went wrong, Please try again.');
                }
            }
        }

        return redirect()->to('/coordreports/appreciation')->with('success', 'Appreciation gifts updated successfully');

    }

    /**
     * View the Volunteer Birthday list
     */
    public function showRptBirthdays(Request $request): View
    {
        //Get Coordinator Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
            // Load Reporting Tree
            $coordinatorData = $this->userController->loadReportingTree($corId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = DB::table('coordinators as cd')
            ->select('cd.id as cor_id', 'cd.layer_id as layer_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.report_id as report_id', 'cd.sec_position_id as sec_position_id', 'cd.card_sent as card_sent',
                'cp.long_title as position', 'rg.short_name as reg', 'cf.short_name as conf', 'cd.birthday_month_id as b_month', 'cd.birthday_day as b_day', 'db.month_long_name as month')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
            ->join('month as db', 'cd.birthday_month_id', '=', 'db.id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.birthday_month_id')
            ->orderBy('cd.birthday_day');

        if ($conditions['founderCondition']) {

        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('cd.conference_id', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('cd.region_id', '=', $corRegId);
        } else {
            $baseQuery->whereIn('cd.id', $inQryArr);
        }

        $coordinatorList = $baseQuery->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('coordreports.coordrptbirthdays')->with($data);
    }

    /**
     * Coordiantor Birthdays Details
     */
    public function showRptBirthdaysView(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.id', 'cd.position_id', 'cd.birthday_month_id', 'cd.birthday_day', 'cd.card_sent', 'cd.first_name', 'cd.last_name', 'cd.address', 'cd.city', 'cd.state', 'cd.zip')
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

        $data = ['directReportTo' => $directReportTo, 'primaryCoordinatorList' => $primaryCoordinatorList, 'positionList' => $positionList, 'confList' => $confList, 'coordinatorDetails' => $coordinatorDetails, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr];

        return view('coordreports.coordrptbirthdaysview')->with($data);
    }

    /**
     * Update Coordiantor Birthdays (store)
     */
    public function updateRptBirthdays(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $cordinatorId = $id;

        if ($request->input('cord_fname') != '' && $request->input('cord_lname') != '') {
            $corDetails = DB::table('coordinators')
                ->select('id', 'user_id')
                ->where('id', '=', $cordinatorId)
                ->get();
            if (count($corDetails) != 0) {
                try {
                    $userId = $corDetails[0]->user_id;
                    $cordId = $corDetails[0]->id;

                    $user = User::find($userId);
                    $user->first_name = $request->input('cord_fname');
                    $user->last_name = $request->input('cord_lname');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    DB::table('coordinators')
                        ->where('id', $cordinatorId)
                        ->update(['card_sent' => $request->input('card_sent'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                    DB::commit();
                } catch (\Exception $e) {
                    // Rollback Transaction
                    DB::rollback();
                    // Log the error
                    Log::error($e);

                    return redirect()->to('/coordreports/birthdays')->with('fail', 'Something went wrong, Please try again.');
                }
            }
        }

        return redirect()->to('/coordreports/birthdays')->with('success', 'Appreciation gifts updated successfully');

    }

    /**
     * View the Reporting Tree
     */
    public function showRptReportingTree(Request $request): View
    {
        $coordinator_array = [];
        $corDetails = User::find($request->user()->id)->Coordinators;
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
