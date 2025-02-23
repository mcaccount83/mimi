<?php

namespace App\Http\Controllers;

use App\Models\CoordinatorPosition;
use App\Models\Coordinators;
use App\Models\State;
use App\Models\Region;
use App\Models\Conference;
use App\Models\Month;
use Illuminate\Support\Facades\DB;

class BaseCoordinatorController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->userController = $userController;
    }

    /*/Custom Helpers/*/
    // $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);

    /*/User Controller/*/
    // $this->userController->loadReportingTree($cdId);


    /*/ Active Coordinator List Base Query /*/
    public function getActiveBaseQuery($userConfId, $userRegId, $userCdId, $userPositionid, $userSecPositionid)
    {
        $conditions = getPositionConditions($userPositionid, $userSecPositionid);
        if ($conditions['coordinatorCondition']) {
            $coordinatorData = $this->userController->loadReportingTree($userCdId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = Coordinators::with(['state', 'conference', 'region', 'displayPosition', 'mimiPosition', 'secondaryPosition', 'birthdayMonth'])
            ->where('id', '!=', $userCdId)
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
            ->orderBy('coordinator_start_date');
        }

        return ['query' => $baseQuery, 'checkBoxStatus' => $checkBoxStatus, 'inQryArr' => $inQryArr];
    }

    /*/ Retired Coordinator List Base Query /*/
    public function getRetiredBaseQuery($userConfId, $userRegId, $userCdId, $userPositionid, $userSecPositionid)
    {
        $conditions = getPositionConditions($userPositionid, $userSecPositionid);
        if ($conditions['coordinatorCondition']) {
            $coordinatorData = $this->userController->loadReportingTree($userCdId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = Coordinators::with(['state', 'conference', 'region', 'displayPosition', 'mimiPosition', 'secondaryPosition', 'birthdayMonth'])
            ->where('is_active', 0);

        if ($conditions['founderCondition']) {
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('conference_id', '=', $userConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('region_id', '=', $userRegId);
        } else {
            $baseQuery->whereIn('report_id', $inQryArr);
        }

        $baseQuery->orderByDesc('zapped_date');

        return ['query' => $baseQuery];
    }

    /*/ Active Coordinator Details Base Query /*/
    public function getCoordinatorDetails($id)
    {
        $cdDetails = Coordinators::with(['state', 'conference', 'region', 'displayPosition', 'mimiPosition', 'secondaryPosition', 'birthdayMonth',
            'reportsTo'])->find($id);
        $cdId = $cdDetails->id;
        $cdPositionid = $cdDetails->position_id;
        $cdIsActive = $cdDetails->is_active;
        $regionLongName = $cdDetails->region->long_name;
        $conferenceDescription = $cdDetails->conference->conference_description;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdRptId = $cdDetails->report_id;
        $RptFName = $cdDetails->reportsTo?->first_name;
        $RptLName = $cdDetails->reportsTo?->last_name;
        $displayPosition = $cdDetails->displayPosition;
        $mimiPosition = $cdDetails->mimiPosition;
        $secondaryPosition = $cdDetails->secondaryPosition;

        $allRegions = Region::with('conference')  // Full List for Dropdown Menu based on Conference
            ->where('conference_id', $cdConfId)
            ->orwhere('id', '0')
            ->get();
        $allStates = State::all();  // Full List for Dropdown Menu
        $allMonths = Month::all();  // Full List for Dropdown Menu
        $allPositions = CoordinatorPosition::all();  // Full List for Dropdown Menu
        $allCoordinators = Coordinators::with('conference')  // Full List for Dropdown Menu based on Conference
            ->where('conference_id', $cdConfId)
            ->where('is_active', 1)
            ->get();

         // Load ReportsTo Coordinator Dropdown List
         $rcDetails = $this->userController->loadReportsToList($cdId, $cdConfId, $cdPositionid);

        return ['cdDetails' => $cdDetails, 'cdId' => $cdId, 'cdIsActive' => $cdIsActive, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription, 'cdConfId' => $cdConfId, 'cdRegId' => $cdRegId, 'cdRptId' => $cdRptId,
            'RptFName' => $RptFName, 'RptLName' => $RptLName, 'displayPosition' => $displayPosition, 'mimiPosition' => $mimiPosition,
            'secondaryPosition' => $secondaryPosition, 'allRegions' => $allRegions, 'allStates' => $allStates, 'allMonths' => $allMonths,
            'rcDetails' => $rcDetails, 'allPositions' => $allPositions, 'allCoordinators' => $allCoordinators, 'cdPositionid' => $cdPositionid,
        ];
    }

     /*/ Active International Coordinator List Base Query /*/
     public function getActiveInternationalBaseQuery($userConfId, $userRegId, $userCdId, $userPositionid, $userSecPositionid)
     {
     }


}
