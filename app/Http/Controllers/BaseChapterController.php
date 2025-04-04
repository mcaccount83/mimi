<?php

namespace App\Http\Controllers;

use App\Models\Chapters;
use App\Models\Conference;
use App\Models\Coordinators;
use App\Models\FinancialReportAwards;
use App\Models\Month;
use App\Models\Probation;
use App\Models\Region;
use App\Models\State;
use App\Models\Status;
use App\Models\Website;

class BaseChapterController extends Controller
{
    protected $userController;

    protected $baseConditionsController;

    public function __construct(UserController $userController, BaseConditionsController $baseConditionsController)
    {
        $this->userController = $userController;
        $this->baseConditionsController = $baseConditionsController;
    }

    /* /Custom Helpers/ */
    // $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);
    // $displayEOY = getEOYDisplay();

    /**
     * Apply checkbox filters to the query
     */
    private function applyCheckboxFilters($baseQuery, $coorId)
    {
        $checkboxStatus = ['checkBoxStatus' => '', 'checkBox2Status' => '', 'checkBox3Status' => '', 'checkBox4Status' => ''];

        if (isset($_GET['check']) && $_GET['check'] == 'yes') {
            $checkboxStatus['checkBoxStatus'] = 'checked';
            $baseQuery->where('primary_coordinator_id', '=', $coorId);
        }

        if (isset($_GET['check2']) && $_GET['check2'] == 'yes') {
            $checkboxStatus['checkBox2Status'] = 'checked';
            $baseQuery->whereHas('financialReport', function ($query) use ($coorId) {
                $query->where('reviewer_id', '=', $coorId);
            });
        }

        if (isset($_GET['check4']) && $_GET['check4'] == 'yes') {
            $checkboxStatus['checkBox4Status'] = 'checked';
            $baseQuery->where('status_id', '!=', '1');
        }

        return ['query' => $baseQuery, 'status' => $checkboxStatus];
    }

    /**
     * Get base query with common relations
     */
    private function getBaseQueryWithRelations($isActive = 1)
    {
        return Chapters::with(['state', 'conference', 'region', 'webLink', 'president', 'avp', 'mvp', 'treasurer', 'secretary', 'startMonth', 'primaryCoordinator'])
            ->where('is_active', $isActive);
    }

    /**
     * Apply sorting based on query type and page
     */
    private function applySorting($baseQuery, $queryType)
    {
        $isReregPage = request()->route()->getName() === 'chapters.chapreregistration';

        if ($queryType === 'zapped') {
            return ['query' => $baseQuery->orderByDesc('chapters.zap_date'), 'checkBox3Status' => ''];
        }

        if ($isReregPage) {
            if (isset($_GET['check3']) && $_GET['check3'] == 'yes') {
                $baseQuery->orderBy(State::select('state_short_name')
                    ->whereColumn('state.id', 'chapters.state_id'), 'asc')
                    ->orderBy('chapters.name');

                return ['query' => $baseQuery, 'checkBox3Status' => 'checked'];
            }

            $baseQuery->orderByDesc('next_renewal_year')
                ->orderByDesc('start_month_id');

            return ['query' => $baseQuery, 'checkBox3Status' => ''];
        }

        return ['query' => $baseQuery->orderBy(Conference::select('short_name')
            ->whereColumn('conference.id', 'chapters.conference_id'))
            ->orderBy(Region::select('short_name')
                ->whereColumn('region.id', 'chapters.region_id'))
            ->orderBy(State::select('state_short_name')
                ->whereColumn('state.id', 'chapters.state_id'), 'asc')
            ->orderBy('chapters.name'),
            'checkBox3Status' => ''];
    }

    /**
     * Build chapter query based on type and parameters
     */
    private function buildChapterQuery($params)
    {
        $baseQuery = $this->getBaseQueryWithRelations($params['isActive']);

        if (isset($params['coorId'])) {
            // Only apply position conditions if this is not an international query
            if (isset($params['conditions']) && $params['conditions']) {
                $conditionsData = $this->baseConditionsController->getConditions(
                    $params['coorId'],
                    $params['positionId'],
                    $params['secPositionId']
                );

                $positionMethod = $params['queryType'] === 'inquiries'
                    ? 'applyInquiriesPositionConditions'
                    : 'applyPositionConditions';

                $baseQuery = $this->baseConditionsController->$positionMethod(
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

        $sortingResults = $this->applySorting($baseQuery, $params['isActive'] ? 'active' : 'zapped');

        return ['query' => $sortingResults['query'], 'checkBoxStatus' => $checkboxStatus['checkBoxStatus'] ?? '', 'checkBox2Status' => $checkboxStatus['checkBox2Status'] ?? '',
            'checkBox3Status' => $sortingResults['checkBox3Status'], 'checkBox4Status' => $checkboxStatus['checkBox4Status'] ?? '',
        ];
    }

    /**
     * Public methods for different query types
     */
    public function getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildChapterQuery([
            'isActive' => 1,
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'conditions' => true,
            'queryType' => 'regular',
        ]);
    }

    public function getZappedBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildChapterQuery([
            'isActive' => 0,
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'conditions' => true,
            'queryType' => 'regular',
        ]);
    }

    public function getActiveInquiriesBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildChapterQuery([
            'isActive' => 1,
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'conditions' => true,
            'queryType' => 'inquiries',
        ]);
    }

    public function getZappedInquiriesBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildChapterQuery([
            'isActive' => 0,
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'conditions' => true,
            'queryType' => 'inquiries',
        ]);
    }

    public function getActiveInternationalBaseQuery($coorId)
    {
        return $this->buildChapterQuery([
            'isActive' => 1,
            'coorId' => $coorId,
            'conditions' => false,
            'queryType' => 'international',
        ]);
    }

    public function getZappedInternationalBaseQuery($coorId)
    {
        return $this->buildChapterQuery([
            'isActive' => 0,
            'coorId' => $coorId,
            'conditions' => false,
            'queryType' => 'international',
        ]);
    }

    /**
     * Chapter Details Base Query for all (active and zapped) chapters
     */
    public function getChapterDetails($chId)
    {
        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'documents', 'financialReport', 'startMonth', 'boards', 'primaryCoordinator',
            'payments', 'disbandCheck', 'probation', 'boardsDisbanded']) ->find($chId);
        $chIsActive = $chDetails->is_active;
        $stateShortName = $chDetails->state->state_short_name;
        $regionLongName = $chDetails->region?->long_name;
        $conferenceDescription = $chDetails->conference?->conference_description;
        $chConfId = $chDetails->conference_id;
        $chRegId = $chDetails->region_id;
        $chPcId = $chDetails->primary_coordinator_id;

        $startMonthName = $chDetails->startMonth?->month_long_name;
        $chapterStatus = $chDetails->status?->chapter_status;
        $probationReason = $chDetails->probation?->probation_reason;
        $websiteLink = $chDetails->webLink?->link_status ?? null;

        $chPayments = $chDetails->payments;
        $chDocuments = $chDetails->documents;
        $reviewComplete = $chDetails->documents?->review_complete ?? null;
        $chFinancialReport = $chDetails->financialReport;
        $displayEOY = getEOYDisplay();  // Conditions to Show EOY Items

        $allStatuses = Status::all();  // Full List for Dropdown Menu
        $allProbation = Probation::all();  // Full List for Dropdown Menu
        $allAwards = FinancialReportAwards::all();  // Full List for Dropdown Menu
        $allWebLinks = Website::all();  // Full List for Dropdown Menu
        $allStates = State::all();  // Full List for Dropdown Menu
        $allMonths = Month::all();  // Full List for Dropdown Menu

        $boards = $chDetails->boards()->with('stateName')->get();
        $bdDetails = $boards->groupBy('board_position_id');
        $defaultBoardMember = (object) ['id' => null, 'first_name' => '', 'last_name' => '', 'email' => '', 'street_address' => '', 'city' => '', 'zip' => '', 'phone' => '', 'state' => '', 'user_id' => ''];

        // Fetch board details or fallback to default
        $PresDetails = $bdDetails->get(1, collect([$defaultBoardMember]))->first(); // President
        $AVPDetails = $bdDetails->get(2, collect([$defaultBoardMember]))->first(); // AVP
        $MVPDetails = $bdDetails->get(3, collect([$defaultBoardMember]))->first(); // MVP
        $TRSDetails = $bdDetails->get(4, collect([$defaultBoardMember]))->first(); // Treasurer
        $SECDetails = $bdDetails->get(5, collect([$defaultBoardMember]))->first(); // Secretary

        $chDisbanded = $chDetails->disbandCheck;
        $bdDisbanded = $chDetails->boardsDisbanded()->with('stateName')->get();
        $bdDisbandedDetails = $bdDisbanded->groupBy('board_position_id');
        $defaultDisbandedBoardMember = (object) ['id' => null, 'first_name' => '', 'last_name' => '', 'email' => '', 'street_address' => '', 'city' => '', 'zip' => '', 'phone' => '', 'state' => '', 'user_id' => ''];

        // Fetch board details or fallback to default
        $PresDisbandedDetails = $bdDisbandedDetails->get(1, collect([$defaultDisbandedBoardMember]))->first(); // President
        $AVPDisbandedDetails = $bdDisbandedDetails->get(2, collect([$defaultDisbandedBoardMember]))->first(); // AVP
        $MVPDisbandedDetails = $bdDisbandedDetails->get(3, collect([$defaultDisbandedBoardMember]))->first(); // MVP
        $TRSDisbandedDetails = $bdDisbandedDetails->get(4, collect([$defaultDisbandedBoardMember]))->first(); // Treasurer
        $SECDisbandedDetails = $bdDisbandedDetails->get(5, collect([$defaultDisbandedBoardMember]))->first(); // Secretary

        // Load Board and Coordinators for Sending Email
        $emailData = $this->userController->loadEmailDetails($chId);
        $emailListChap = $emailData['emailListChap'];
        $emailListCoord = $emailData['emailListCoord'];

        // Load Conference Coordinators for Sending Email
        $emailCCData = $this->userController->loadConferenceCoord($chPcId);
        $cc_id = $emailCCData['cc_id'];
        $emailCC = $emailCCData['cc_email'];

        // Load Primary Coordinator Inforamtion //
        $pcDetails = Coordinators::find($chPcId);
        $emailPC = $pcDetails?->email;
        $pcName = $pcDetails?->first_name.' '.$pcDetails?->last_name;

        // Load Primary Coordinator Dropdown List
        $pcList = $this->userController->loadPrimaryList($chRegId, $chConfId) ?? null;
        // Load Report Reviewer Coordinator Dropdown List
        $rrList = $this->userController->loadReviewerList($chRegId, $chConfId) ?? null;

        return ['chDetails' => $chDetails, 'chIsActive' => $chIsActive, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription, 'chConfId' => $chConfId, 'chRegId' => $chRegId, 'chPcId' => $chPcId, 'chId' => $chId,
            'chDocuments' => $chDocuments, 'reviewComplete' => $reviewComplete, 'chFinancialReport' => $chFinancialReport, 'allAwards' => $allAwards, 'chPayments' => $chPayments,
            'PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
            'emailListChap' => $emailListChap, 'emailListCoord' => $emailListCoord, 'pcList' => $pcList, 'rrList' => $rrList, 'emailCCData' => $emailCCData,
            'allWebLinks' => $allWebLinks, 'allStatuses' => $allStatuses, 'allStates' => $allStates, 'emailCC' => $emailCC, 'emailPC' => $emailPC, 'cc_id' => $cc_id,
            'startMonthName' => $startMonthName, 'chapterStatus' => $chapterStatus, 'websiteLink' => $websiteLink, 'pcName' => $pcName, 'displayEOY' => $displayEOY, 'probationReason' => $probationReason,
            'allMonths' => $allMonths, 'pcDetails' => $pcDetails, 'chDisbanded' => $chDisbanded, 'PresDisbandedDetails' => $PresDisbandedDetails, 'allProbation' => $allProbation,
            'AVPDisbandedDetails' => $AVPDisbandedDetails, 'MVPDisbandedDetails' => $MVPDisbandedDetails, 'TRSDisbandedDetails' => $TRSDisbandedDetails, 'SECDisbandedDetails' => $SECDisbandedDetails,
        ];
    }
}
