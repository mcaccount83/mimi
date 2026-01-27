<?php

namespace App\Http\Controllers;

use App\Enums\CoordinatorCheckbox;
use App\Models\AdminRole;
use App\Models\Conference;
use App\Models\CoordinatorPosition;
use App\Models\Coordinators;
use App\Models\Country;
use App\Models\Month;
use App\Models\RecognitionGifts;
use App\Models\Region;
use App\Models\State;
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
    private function applyCheckboxFilters($baseQuery, $coorId, $conditions = null, $confId = null, $regId = null)
    {
        $checkboxStatus = [
            CoordinatorCheckbox::CHECK_DIRECT => '',
            CoordinatorCheckbox::CHECK_CONFERENCE_REGION => '',
            CoordinatorCheckbox::CHECK_INTERNATIONAL => '',
        ];

        // Checkbox
        if (isset($_GET[CoordinatorCheckbox::DIRECT_REPORT]) && $_GET[CoordinatorCheckbox::DIRECT_REPORT] == 'yes') {
            $checkboxStatus[CoordinatorCheckbox::CHECK_DIRECT] = 'checked';
            $baseQuery->where('report_id', '=', $coorId);
        }

        // Checkbox3
        if (isset($_GET[CoordinatorCheckbox::CONFERENCE_REGION]) && $_GET[CoordinatorCheckbox::CONFERENCE_REGION] == 'yes') {
            $checkboxStatus[CoordinatorCheckbox::CHECK_CONFERENCE_REGION] = 'checked';
            // Position conditions already applied in buildChapterQuery
        }

        // Checkbox5
        if (isset($_GET[CoordinatorCheckbox::INTERNATIONAL]) && $_GET[CoordinatorCheckbox::INTERNATIONAL] == 'yes') {
            $checkboxStatus[CoordinatorCheckbox::CHECK_INTERNATIONAL] = 'checked';
            // Position conditions were skipped in buildChapterQuery
            if ($conditions && (
                ! $conditions['inquiriesInternationalCondition'] &&
                ! $conditions['ITCondition'] &&
                ! $conditions['einCondition'])) {
                // User doesn't have international permissions, show nothing
                $baseQuery->whereRaw('1 = 0');
            }
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
                'secondaryPosition', 'birthdayMonth', 'recognition', 'application',
            ]);
        } else {
            // For active (1) or zapped (0), use the regular Boards table
            return $query->with([
                'state', 'conference', 'region', 'displayPosition', 'mimiPosition',
                'secondaryPosition', 'birthdayMonth', 'recognition',
            ]);
        }

    }

    /**
     * Apply sorting based on query type and page
     */
    private function applySorting($baseQuery)
    {
        $isRetiredPage = request()->route()->getName() == 'coordinators.coordretired';
        $isBirthdayPage = request()->route()->getName() == 'coordreports.coordrptbirthdays';
        $isUtilizationPage = request()->route()->getName() == 'coordreports.coordrptvolutilization';

        if ($isRetiredPage) {
            $baseQuery->orderByDesc('zapped_date');

            return ['query' => $baseQuery];
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
            ->orderBy('coordinator_start_date'),
        ];
    }

    /**
     * Build coordinator query based on type and parameters
     */
    private function buildCoordinatorQuery($params)
    {
        $baseQuery = $this->getBaseQueryWithRelations($params['activeStatus']);
        $checkboxStatus = [];

        if (isset($params['coorId'])) {
            $secPositionId = is_array($params['secPositionId']) ? array_map('intval', $params['secPositionId']) : [intval($params['secPositionId'])];

            $conditionsData = $this->baseConditionsController->getConditions(
                $params['coorId'],
                $params['positionId'],
                $secPositionId
            );

            // Only apply position conditions if check5 is NOT selected
            if (! isset($_GET[CoordinatorCheckbox::INTERNATIONAL]) || $_GET[CoordinatorCheckbox::INTERNATIONAL] !== 'yes') {
                $baseQuery = $this->baseConditionsController->applyCordPositionConditions(
                    $baseQuery,
                    $conditionsData['conditions'],
                    $params['confId'] ?? null,
                    $params['regId'] ?? null,
                    $conditionsData['inQryArr']
                );
            }

            // Apply checkbox filters with conditions
            $checkboxResults = $this->applyCheckboxFilters(
                $baseQuery,
                $params['coorId'],
                $conditionsData['conditions'],
                $params['confId'] ?? null,
                $params['regId'] ?? null
            );

            $baseQuery = $checkboxResults['query'];
            $checkboxStatus = $checkboxResults['status'];
        }

        $sortingResults = $this->applySorting($baseQuery);

        return [
            'query' => $sortingResults['query'],
            CoordinatorCheckbox::CHECK_DIRECT => $checkboxStatus[CoordinatorCheckbox::CHECK_DIRECT] ?? '',
            CoordinatorCheckbox::CHECK_CONFERENCE_REGION => $checkboxStatus[CoordinatorCheckbox::CHECK_CONFERENCE_REGION] ?? '',
            CoordinatorCheckbox::CHECK_INTERNATIONAL => $checkboxStatus[CoordinatorCheckbox::CHECK_INTERNATIONAL] ?? '',
        ];
    }

    /**
     * Public methods for different query types
     */
    public function getBaseQuery($activeStatus, $coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildCoordinatorQuery([
            'activeStatus' => $activeStatus,
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'queryType' => $this->getQueryType($activeStatus),
        ]);
    }

    /**
     * Helper to determine query type from active status
     */
    private function getQueryType($activeStatus)
    {
        return match ($activeStatus) {
            0 => 'zapped',
            1 => 'active',
            2 => 'pending',
            3 => 'not_approved',
            default => 'active',
        };
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

        if ($cdDetails->state_id < 52) {
            $cdstateShortName = $cdDetails->state->state_short_name;
        } else {
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

        // Load Conference Coordinators for Sending Email
        $emailCCData = $this->userController->loadConferenceCoordConf($cdConfId);
        $cc_id = $emailCCData['cc_id'];
        $emailCC = $emailCCData['cc_email'];

        // Load ReportsTo Coordinator Dropdown List
        $rcDetails = $this->userController->loadReportsToList($cdId, $cdConfId, $cdPositionid);

        $rcDetailsInfo = $this->userController->loadReportToCoord($cdId);
        $rc_id = $rcDetailsInfo['rc_id'];
        $rc_name = $rcDetailsInfo['rc_fname'].' '.$rcDetailsInfo['rc_lname'];
        $rc_email = $rcDetailsInfo['rc_email'];
        $rc_pos = $rcDetailsInfo['rc_pos'];

        return ['cdDetails' => $cdDetails, 'cdId' => $cdId, 'cdActiveId' => $cdActiveId, 'regionLongName' => $regionLongName, 'cdUserAdmin' => $cdUserAdmin, 'emailCC' => $emailCC,
            'conferenceDescription' => $conferenceDescription, 'cdConfId' => $cdConfId, 'cdRegId' => $cdRegId, 'cdRptId' => $cdRptId, 'allAdminRoles' => $allAdminRoles,
            'RptFName' => $RptFName, 'RptLName' => $RptLName, 'displayPosition' => $displayPosition, 'mimiPosition' => $mimiPosition, 'cdAdminRole' => $cdAdminRole, 'rc_email' => $rc_email,
            'secondaryPosition' => $secondaryPosition, 'allRegions' => $allRegions, 'allStates' => $allStates, 'allMonths' => $allMonths, 'secondaryPositionId' => $secondaryPositionId,
            'rcDetails' => $rcDetails, 'allPositions' => $allPositions, 'allCoordinators' => $allCoordinators, 'cdPositionid' => $cdPositionid, 'secondaryPositionShort' => $secondaryPositionShort,
            'allRecognitionGifts' => $allRecognitionGifts, 'allCountries' => $allCountries, 'cdstateShortName' => $cdstateShortName, 'cdApp' => $cdApp, 'emailCCData' => $emailCCData,
        ];
    }
}
