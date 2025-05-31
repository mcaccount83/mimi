<?php

namespace App\Http\Controllers;

use App\Models\AdminRole;
use App\Models\Conference;
use App\Models\CoordinatorPosition;
use App\Models\Coordinators;
use App\Models\Month;
use App\Models\RecognitionGifts;
use App\Models\Region;
use App\Models\State;
use App\Models\Country;
use App\Models\User;
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
    private function getBaseQueryWithRelations($activeStatus)
    {
        $query = Coordinators::query()->where('active_status', $activeStatus);

        // For pending (2) or not approved (3) status, we need to use relations from the BoardsPending table
        if ($activeStatus == 2 || $activeStatus == 3) {
            return $query->with([
                'state', 'conference', 'region', 'displayPosition', 'mimiPosition',
                'secondaryPosition', 'birthdayMonth', 'recognition'
            ]);
        } else {
            // For active (1) or zapped (0), use the regular Boards table
            return $query->with([
                'state', 'conference', 'region', 'displayPosition', 'mimiPosition',
                'secondaryPosition', 'birthdayMonth', 'recognition'
            ]);
        }

        // return Coordinators::with(['state', 'conference', 'region', 'displayPosition', 'mimiPosition', 'secondaryPosition', 'birthdayMonth', 'recognition'])
        //     ->where('active_status', $cdIsActive);
    }

    /**
     * Apply sorting based on query type and page
     */
    private function applySorting($baseQuery, $queryType)
    {
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
        $baseQuery = $this->getBaseQueryWithRelations($params['activeStatus']);
        $checkboxStatus = [];
        $isPending = ($params['activeStatus'] == 2 || $params['activeStatus'] == 3);


        if (isset($params['coorId'])) {
            // Only apply position conditions if this is not an international or tree query
            if (isset($params['conditions']) && $params['conditions']) {
                $secPositionId = is_array($params['secPositionId']) ? array_map('intval', $params['secPositionId']) : [intval($params['secPositionId'])];

                $conditionsData = $this->baseConditionsController->getConditions(
                    $params['coorId'],
                    $params['positionId'],
                    $secPositionId  // Use the formatted variable here instead of $params['secPositionId']
                );

                  // Use the appropriate method based on active status
                $baseQuery = $isPending
                    ? $this->baseConditionsController->applyPendingPositionConditions(
                        $baseQuery,
                        $conditionsData['conditions'],
                        $params['confId'] ?? null,
                        $params['regId'] ?? null,
                        $conditionsData['inQryArr']
                    )
                    : $this->baseConditionsController->applyPositionCoordConditions(
                        $baseQuery,
                        $conditionsData['conditions'],
                        $params['confId'] ?? null,
                        $params['regId'] ?? null,
                        $conditionsData['inQryArr']
                    );

                $checkboxResults = $this->applyCheckboxFilters($baseQuery, $params['coorId']);
                $baseQuery = $checkboxResults['query'];
                $checkboxStatus = $checkboxResults['status'];

            }

                // $baseQuery = $this->baseConditionsController->applyPositionCoordConditions(
                //     $baseQuery,
                //     $conditionsData['conditions'],
                //     $params['confId'] ?? null,
                //     $params['regId'] ?? null,
                //     $conditionsData['inQryArr']
                // );

                // $checkboxResults = $this->applyCheckboxFilters($baseQuery, $params['coorId']);
                // $baseQuery = $checkboxResults['query'];
                // $checkboxStatus = $checkboxResults['status'];

            // }

            // Only apply position conditions if this is tree query
            if (isset($params['treeConditions']) && $params['treeConditions']) {
                $conditionsData = $conditionsData ?? $this->baseConditionsController->getConditions(
                    $params['coorId'],
                    $params['positionId'],
                    $params['secPositionId']
                );

                $baseQuery->where('on_leave', '!=', '1');
                $conditions = $conditionsData['conditions'] ?? [];

                if (! ($conditions['founderCondition'] ?? false)) {
                    $baseQuery->where('conference_id', $params['confId']);
                }
            }
        }

        // $sortingResults = $this->applySorting($baseQuery, $params['cdIsActive'] ? 'active' : 'retired');
        $sortingResults = $this->applySorting($baseQuery, $params['queryType']);

        return [
            'query' => $sortingResults['query'],
            'checkBoxStatus' => $checkboxStatus['checkBoxStatus'] ?? '',
        ];
    }

    /**
     * Public methods for different query types
     */
    public function getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildCoordinatorQuery([
            'activeStatus' => 1, // 1 = active
            'cdIsActive' => 1,
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'treeConditions' => false,
            'conditions' => true,
            'queryType' => 'regular',
        ]);
    }

    public function getPendingBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildCoordinatorQuery([
            'activeStatus' => 2, // 2 = pending
            'cdIsActive' => 1,
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'treeConditions' => false,
            'conditions' => true,
            'queryType' => 'pending',
        ]);
    }

     public function getNotApprovedBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildCoordinatorQuery([
            'activeStatus' => 3, // 3 = not approved
            'cdIsActive' => 0,
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'treeConditions' => false,
            'conditions' => true,
            'queryType' => 'not_approved',
        ]);
    }

    public function getRetiredBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildCoordinatorQuery([
            'activeStatus' => 0, // 0 = zapped
            'cdIsActive' => 0,
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'treeConditions' => false,
            'conditions' => true,
            'queryType' => 'retired',
        ]);
    }

    public function getActiveInternationalBaseQuery($coorId)
    {
        return $this->buildCoordinatorQuery([
            'activeStatus' => 1,
            'cdIsActive' => 1,
            'coorId' => $coorId,
            'treeConditions' => false,
            'conditions' => false,
            'queryType' => 'international',
        ]);
    }

     public function getPendingInternationalBaseQuery($coorId)
    {
        return $this->buildCoordinatorQuery([
            'activeStatus' => 2,
            'cdIsActive' => 0,
            'coorId' => $coorId,
            'treeConditions' => false,
            'conditions' => false,
            'queryType' => 'pending_international',
        ]);
    }

     public function getNotApprovedInternationalBaseQuery($coorId)
    {
        return $this->buildCoordinatorQuery([
            'activeStatus' => 3,
            'cdIsActive' => 1,
            'coorId' => $coorId,
            'treeConditions' => false,
            'conditions' => false,
            'queryType' => 'not_approved_international',
        ]);
    }

    public function getRetiredInternationalBaseQuery($coorId)
    {
        return $this->buildCoordinatorQuery([
            'activeStatus' => 0,
            'cdIsActive' => 0,
            'coorId' => $coorId,
            'treeConditions' => false,
            'conditions' => false,
            'queryType' => 'international',
        ]);
    }

    public function getReportingTreeBaseQuery($coorId, $confId, $positionId, $secPositionId)
    {
        return $this->buildCoordinatorQuery([
            'activeStatus' => 1,
            'cdIsActive' => 1,
            'coorId' => $coorId,
            'confId' => $confId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'treeConditions' => true,
            'conditions' => false,
            'queryType' => 'conference',
        ]);
    }

    /**
     * Active Coordinator Details Base Query
     */
    public function getCoordinatorDetails($cdId)
    {
        $cdDetails = Coordinators::with(['country', 'state', 'conference', 'region', 'displayPosition', 'mimiPosition', 'secondaryPosition', 'birthdayMonth',
            'reportsTo', 'recognition', 'application'])->find($cdId);
        $cdActiveId = $cdDetails->active_status;
        $cdApp = $cdDetails->application;
        $cdPositionid = $cdDetails->position_id;
        $regionLongName = $cdDetails->region?->long_name;
        $conferenceDescription = $cdDetails->conference?->conference_description;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdRptId = $cdDetails->report_id;
        $RptFName = $cdDetails->reportsTo?->first_name;
        $RptLName = $cdDetails->reportsTo?->last_name;
        $displayPosition = $cdDetails->displayPosition;
        $mimiPosition = $cdDetails->mimiPosition;

         if ($cdDetails->state_id < 52){
            $cdstateShortName = $cdDetails->state->state_short_name;
        }
        else{
            $cdstateShortName = $cdDetails->country->short_name;
        }

        $secondaryPosition = [];
        $secondaryPositionShort = [];
        $secondaryPositionId = [];
        if ($cdDetails->secondaryPosition && $cdDetails->secondaryPosition->count() > 0) {
            $secondaryPosition = $cdDetails->secondaryPosition->pluck('long_title')->toArray();
            $secondaryPositionShort = $cdDetails->secondaryPosition->pluck('short_title')->toArray();
            $secondaryPositionId = $cdDetails->secondaryPosition->pluck('id')->toArray();
        }

        $cdUserId = $cdDetails->user_id;
        $cdUser = User::with(['adminRole'])
            ->find($cdUserId);
        $cdUserAdmin = $cdUser->is_admin ?? null;
        $cdAdminRole = $cdUser->adminRole ?? null;

        $allRegions = Region::with('conference')  // Full List for Dropdown Menu based on Conference
            ->where('conference_id', $cdConfId)
            ->orwhere('id', '0')
            ->get();
        $allStates = State::all();  // Full List for Dropdown Menu
        $allCountries = Country::all();  // Full List for Dropdown Menu
        $allMonths = Month::all();  // Full List for Dropdown Menu
        $allRecognitionGifts = RecognitionGifts::all();  // Full List for Dropdown Menu
        $allAdminRoles = AdminRole::all();  // Full List for Dropdown Menu
        $allPositions = CoordinatorPosition::all();  // Full List for Dropdown Menu
        $allCoordinators = Coordinators::with('conference')  // Full List for Dropdown Menu based on Conference
            ->where('conference_id', $cdConfId)
            ->where('active_status', 1)
            ->get();

        // Load ReportsTo Coordinator Dropdown List
        $rcDetails = $this->userController->loadReportsToList($cdId, $cdConfId, $cdPositionid);

        return ['cdDetails' => $cdDetails, 'cdId' => $cdId, 'cdActiveId' => $cdActiveId, 'regionLongName' => $regionLongName, 'cdUserAdmin' => $cdUserAdmin,
            'conferenceDescription' => $conferenceDescription, 'cdConfId' => $cdConfId, 'cdRegId' => $cdRegId, 'cdRptId' => $cdRptId, 'allAdminRoles' => $allAdminRoles,
            'RptFName' => $RptFName, 'RptLName' => $RptLName, 'displayPosition' => $displayPosition, 'mimiPosition' => $mimiPosition, 'cdAdminRole' => $cdAdminRole,
            'secondaryPosition' => $secondaryPosition, 'allRegions' => $allRegions, 'allStates' => $allStates, 'allMonths' => $allMonths, 'secondaryPositionId' => $secondaryPositionId,
            'rcDetails' => $rcDetails, 'allPositions' => $allPositions, 'allCoordinators' => $allCoordinators, 'cdPositionid' => $cdPositionid, 'secondaryPositionShort' => $secondaryPositionShort,
            'allRecognitionGifts' => $allRecognitionGifts, 'allCountries' => $allCountries, 'cdstateShortName' => $cdstateShortName, 'cdApp' => $cdApp,
        ];
    }
}
