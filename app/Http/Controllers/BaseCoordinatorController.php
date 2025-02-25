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
    protected $baseConditionsController;

    public function __construct(UserController $userController, BaseConditionsController $baseConditionsController)
    {
        $this->userController = $userController;
        $this->baseConditionsController = $baseConditionsController;
    }

    /*/Custom Helpers/*/
    // $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);

    /*/User Controller/*/
    // $this->userController->loadReportingTree($cdId);

    /*/Base Coditions Controller/*/
    // $this->baseConditionsController->getConditions($cdId, $cdPositionid, $cdSecPositionid);
    // $this->baseConditionsController->applyPositionConditions($baseQuery, $conditions, $cdConfId, $cdRegId, $inQryArr)

    /**
     * Apply checkbox filters to the query
     */
    private function applyCheckboxFilters($baseQuery, $coorId)
    {
        $checkboxStatus = ['checkBoxStatus' => ''];

        if (isset($_GET['check']) && $_GET['check'] == 'yes') {
            $checkboxStatus['checkBoxStatus'] = 'checked';
            $baseQuery->where('report_id', '=', $coorId);
        }

        return ['query' => $baseQuery, 'status' => $checkboxStatus];
    }

    /**
     * Get base query with common relations
     */
    private function getBaseQueryWithRelations($cdIsActive = 1)
    {
        return Coordinators::with(['state', 'conference', 'region', 'displayPosition', 'mimiPosition', 'secondaryPosition', 'birthdayMonth'])
            ->where('is_active', $cdIsActive);
    }

     /**
     * Apply sorting based on query type and page
     */
    private function applySorting($baseQuery, $queryType) {
        $isBirthdayPage = request()->route()->getName() === 'coordreports.coordrptbirthdays';
        $isUtilizationPage = request()->route()->getName() === 'coordreports.coordrptvolutilization';

        if ($queryType === 'retired') {
            return ['query' => $baseQuery->orderByDesc('coordinators.zapped_date'), 'checkBoxStatus' => ''];
            }

        if ($isBirthdayPage) {
            $baseQuery->orderBy(Conference::select(DB::raw("CASE WHEN short_name = 'Intl' THEN '' ELSE short_name END"))
                ->whereColumn('conference.id', 'coordinators.conference_id')
                ->limit(1))
                ->orderBy('birthday_month_id')
                ->orderBy('birthday_day');
                return ['query' => $baseQuery, 'checkBoxStatus' => ''];
            }

        if ($isUtilizationPage) {
            $baseQuery->orderBy(Conference::select(DB::raw("CASE WHEN short_name = 'Intl' THEN '' ELSE short_name END"))
                ->whereColumn('conference.id', 'coordinators.conference_id')
                ->limit(1))
                ->orderBy(Region::select(DB::raw("CASE WHEN short_name = 'None' THEN '' ELSE short_name END"))
                    ->whereColumn('region.id', 'coordinators.region_id')
                    ->limit(1))
                ->orderBy('coordinator_start_date');
                return ['query' => $baseQuery, 'checkBoxStatus' => ''];
            }

        return ['query' => $baseQuery->orderBy(Conference::select(DB::raw("CASE WHEN short_name = 'Intl' THEN '' ELSE short_name END"))
            ->whereColumn('conference.id', 'coordinators.conference_id')
            ->limit(1))
            ->orderBy('coordinator_start_date'), 'checkBoxStatus' => ''];
    }

    /**
     * Build coordinator query based on type and parameters
     */
    private function buildCoordinatorQuery($params)
    {
        $baseQuery = $this->getBaseQueryWithRelations($params['cdIsActive']);

        if (isset($params['coorId'])) {
            // Only apply position conditions if this is not an international query
            if (isset($params['conditions']) && $params['conditions']) {
                $conditionsData = $this->baseConditionsController->getConditions(
                    $params['coorId'],
                    $params['positionId'],
                    $params['secPositionId']
                );

                $baseQuery = $this->baseConditionsController->applyPositionConditions(
                    $baseQuery,
                    $conditionsData['conditions'],
                    $params['confId'] ?? null,
                    $params['regId'] ?? null,
                    $conditionsData['inQryArr']
                );
            }

            $checkboxResults = $this->applyCheckboxFilters($baseQuery, $params['coorId']);
            $baseQuery = $checkboxResults['query'];
            $checkboxStatus = $checkboxResults['status'];
        }

        $sortingResults = $this->applySorting($baseQuery, $params['cdIsActive'] ? 'active' : 'retired');

        return [
            'query' => $sortingResults['query'],
            'checkBoxStatus' => $checkboxStatus['checkBoxStatus'] ?? ''
        ];
    }

     /**
     * Public methods for different query types
     */
    public function getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildCoordinatorQuery([
            'cdIsActive' => 1,
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'conditions' => true,
            'queryType' => 'regular'
        ]);
    }

    public function getRetiredBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildCoordinatorQuery([
            'cdIsActive' => 0,
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'conditions' => true,
            'queryType' => 'regular'
        ]);
    }

    public function getActiveInternationalBaseQuery($coorId)
    {
        return $this->buildCoordinatorQuery([
            'cdIsActive' => 1,
            'coorId' => $coorId,
            'conditions' => false,
            'queryType' => 'international'
        ]);
    }

    public function getRetiredInternationalBaseQuery($coorId) {
        return $this->buildCoordinatorQuery([
            'cdIsActive' => 0,
            'coorId' => $coorId,
            'conditions' => false,
            'queryType' => 'international'
        ]);
    }

    /**
     * Active Coordinator Details Base Query
     */
    public function getCoordinatorDetails($cdId)
    {
        $cdDetails = Coordinators::with(['state', 'conference', 'region', 'displayPosition', 'mimiPosition', 'secondaryPosition', 'birthdayMonth',
            'reportsTo'])->find($cdId);
        $cdIsActive = $cdDetails->is_active;
        $cdPositionid = $cdDetails->position_id;
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
}
