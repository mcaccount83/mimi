<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CoordinatorReportController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseChapterController;

    protected $baseCoordinatorController;

    public function __construct(UserController $userController, BaseCoordinatorController $baseCoordinatorController, BaseChapterController $baseChapterController)
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
     * View the Volunteer Utilization list
     */
    public function showRptVolUtilization(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $coordinatorList = $baseQuery['query']->get();

        foreach ($coordinatorList as $list) {
            $cdCoorId = $list->id;
            $cdConfId = $list->conference_id;
            $cdRegId = $list->region_id;
            $cdPositionId = $list->position_id;
            $cdSecPositionId = $list->sec_position_id;

            $reportingData = $this->calculateReporting($cdCoorId, $cdConfId, $cdRegId, $cdPositionId, $cdSecPositionId);

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
    private function calculateReporting($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        $baseDirectQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $directReportList = $baseDirectQuery['query']->where('primary_coordinator_id', $coorId)->get();
        $direct_report = count($directReportList);

        $coordinatorData = $this->userController->loadReportingTree($coorId);
        $inQryArr = $coordinatorData['inQryArr'];
        $inQryArr = array_filter($inQryArr, fn ($id) => $id != $coorId);

        $baseIndirectQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $indirectReportList = $baseIndirectQuery['query']->whereIn('primary_coordinator_id', $inQryArr)->get();

        $indirect_report = count($indirectReportList);

        $total_report = $direct_report + $indirect_report;

        return ['direct_report' => $direct_report, 'indirect_report' => $indirect_report, 'total_report' => $total_report];
    }

    /**
     * Coordiantor Appreciation List
     */
    public function showRptAppreciation(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $coordinatorList = $baseQuery['query']->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('coordreports.coordrptappreciation')->with($data);
    }

    /**
     * View the Volunteer Birthday list
     */
    public function showRptBirthdays(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
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
