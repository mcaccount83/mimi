<?php

namespace App\Http\Controllers;

use App\Models\ActiveStatus;
use App\Models\Chapters;
use App\Models\Conference;
use App\Models\Coordinators;
use App\Models\FinancialReportAwards;
use App\Models\Month;
use App\Models\Probation;
use App\Models\Region;
use App\Models\State;
use App\Models\Country;
use App\Models\Status;
use App\Models\Website;
use App\Services\PositionConditionsService;

class BaseChapterController extends Controller
{
    protected $positionConditionsService;

    protected $userController;

    protected $baseConditionsController;

    public function __construct(PositionConditionsService $positionConditionsService, UserController $userController, BaseConditionsController $baseConditionsController)
    {
        $this->positionConditionsService = $positionConditionsService;
        $this->userController = $userController;
        $this->baseConditionsController = $baseConditionsController;
    }

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
    private function getBaseQueryWithRelations($activeStatus, $zapDateAfter = null)
    {
        $query = Chapters::query()->where('active_status', $activeStatus);

        // Add zap_date filter if provided (only for zapped chapters)
        if ($zapDateAfter && $activeStatus == 0) {
            $query->where('chapters.zap_date', '>', $zapDateAfter);
        }

        // For pending (2) or not approved (3) status, we need to use relations from the BoardsPending table
        if ($activeStatus == 2 || $activeStatus == 3) {
            return $query->with([
                'country', 'state', 'conference', 'region', 'webLink',
                'pendingPresident', 'pendingAvp', 'pendingMvp', 'pendingTreasurer', 'pendingSecretary',
                'payments', 'startMonth', 'primaryCoordinator',
            ]);
        } else {
            // For active (1) or zapped (0), use the regular Boards table
            return $query->with([
                'country', 'state', 'conference', 'region', 'webLink',
                'president', 'avp', 'mvp', 'treasurer', 'secretary',
                'payments', 'startMonth', 'primaryCoordinator',
            ]);
        }
    }

    /**
     * Apply sorting based on query type and page
     */
    private function applySorting($baseQuery, $queryType)
    {
        $isReregPage = request()->route()->getName() === 'chapters.chapreregistration';
        $isIntReregPage = request()->route()->getName() === 'international.intregistration';

        if ($queryType === 'zapped') {
            return ['query' => $baseQuery->orderByDesc('chapters.zap_date'), 'checkBox3Status' => ''];
        }

        if ($isIntReregPage) {
            $baseQuery->orderByDesc('next_renewal_year')
                ->orderByDesc('start_month_id');

            return ['query' => $baseQuery, 'checkBox3Status' => ''];
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
        $zapDateAfter = $params['zapDateAfter'] ?? null;
        $baseQuery = $this->getBaseQueryWithRelations($params['activeStatus'], $zapDateAfter);
        $checkboxStatus = [];
        $isPending = ($params['activeStatus'] == 2 || $params['activeStatus'] == 3);

        if (isset($params['coorId'])) {
            // Only apply position conditions if this is not an international or inquiries
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
                    : $this->baseConditionsController->applyPositionConditions(
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

            if (isset($params['inquiriesConditions']) && $params['inquiriesConditions']) {
                $secPositionId = isset($params['secPositionId']) ? $params['secPositionId'] : [];
                $secPositionId = is_array($secPositionId) ? array_map('intval', $secPositionId) : [intval($secPositionId)];

                $conditionsData = $this->baseConditionsController->getConditions(
                    $params['coorId'],
                    $params['positionId'],
                    $secPositionId  // Use the formatted variable here instead of $params['secPositionId']
                );

                // Use the appropriate method based on active status
                $baseQuery = $isPending
                    ? $this->baseConditionsController->applyPendingInquiriesPositionConditions(
                        $baseQuery,
                        $conditionsData['conditions'],
                        $params['confId'] ?? null,
                        $params['regId'] ?? null,
                        $conditionsData['inQryArr']
                    )
                    : $this->baseConditionsController->applyInquiriesPositionConditions(
                        $baseQuery,
                        $conditionsData['conditions'],
                        $params['confId'] ?? null,
                        $params['regId'] ?? null,
                        $conditionsData['inQryArr']
                    );
            }
        }

        $sortingResults = $this->applySorting($baseQuery, $params['queryType']);

        return [
            'query' => $sortingResults['query'],
            'checkBoxStatus' => $checkboxStatus['checkBoxStatus'] ?? '',
            'checkBox2Status' => $checkboxStatus['checkBox2Status'] ?? '',
            'checkBox3Status' => $sortingResults['checkBox3Status'],
            'checkBox4Status' => $checkboxStatus['checkBox4Status'] ?? '',
        ];
    }

    // Add new method for zapped with date filter
public function getZappedInternationalBaseQuerySinceDate($coorId, $zapDateAfter)
{
    return $this->buildChapterQuery([
        'activeStatus' => 0, // 0 = zapped
        'coorId' => $coorId,
        'inquiriesConditions' => false,
        'conditions' => false,
        'queryType' => 'international',
        'zapDateAfter' => $zapDateAfter,
    ]);
}

    /**
     * Public methods for different query types
     */
    public function getPendingBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildChapterQuery([
            'activeStatus' => 2, // 2 = pending
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'inquiriesConditions' => false,
            'conditions' => true,
            'queryType' => 'pending',
        ]);
    }

    public function getNotApprovedBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildChapterQuery([
            'activeStatus' => 3, // 3 = not approved
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'inquiriesConditions' => false,
            'conditions' => true,
            'queryType' => 'not_approved',
        ]);
    }

    public function getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildChapterQuery([
            'activeStatus' => 1, // 1 = active
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'inquiriesConditions' => false,
            'conditions' => true,
            'queryType' => 'regular',
        ]);
    }

    public function getZappedBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildChapterQuery([
            'activeStatus' => 0, // 0 = zapped
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'inquiriesConditions' => false,
            'conditions' => true,
            'queryType' => 'zapped',
        ]);
    }

    public function getActiveInquiriesBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildChapterQuery([
            'activeStatus' => 1, // 1 = active
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'inquiriesConditions' => true,
            'conditions' => false,
            'queryType' => 'inquiries',
        ]);
    }

    public function getZappedInquiriesBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildChapterQuery([
            'activeStatus' => 0, // 0 = zapped
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'inquiriesConditions' => true,
            'conditions' => false,
            'queryType' => 'inquiries',
        ]);
    }

    public function getPendingInquiriesBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildChapterQuery([
            'activeStatus' => 2, // 2 = pending
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'inquiriesConditions' => true,
            'conditions' => false,
            'queryType' => 'pending_inquiries',
        ]);
    }

    public function getNotApprovedInquiriesBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildChapterQuery([
            'activeStatus' => 3, // 3 = not approved
            'coorId' => $coorId,
            'confId' => $confId,
            'regId' => $regId,
            'positionId' => $positionId,
            'secPositionId' => $secPositionId,
            'inquiriesConditions' => true,
            'conditions' => false,
            'queryType' => 'not_approved_inquiries',
        ]);
    }

    public function getActiveInternationalBaseQuery($coorId)
    {
        return $this->buildChapterQuery([
            'activeStatus' => 1, // 1 = active
            'coorId' => $coorId,
            'inquiriesConditions' => false,
            'conditions' => false,
            'queryType' => 'international',
        ]);
    }

    public function getZappedInternationalBaseQuery($coorId)
    {
        return $this->buildChapterQuery([
            'activeStatus' => 0, // 0 = zapped
            'coorId' => $coorId,
            'inquiriesConditions' => false,
            'conditions' => false,
            'queryType' => 'international',
        ]);
    }

    public function getPendingInternationalBaseQuery($coorId)
    {
        return $this->buildChapterQuery([
            'activeStatus' => 2, // 2 = pending
            'coorId' => $coorId,
            'inquiriesConditions' => false,
            'conditions' => false,
            'queryType' => 'pending_international',
        ]);
    }

    public function getNotApprovedInternationalBaseQuery($coorId)
    {
        return $this->buildChapterQuery([
            'activeStatus' => 3, // 3 = not approved
            'coorId' => $coorId,
            'inquiriesConditions' => false,
            'conditions' => false,
            'queryType' => 'not_approved_international',
        ]);
    }

    /**
     * Chapter Details Base Query for all chapters
     */
    public function getChapterDetails($chId)
    {
        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'documents', 'financialReport', 'startMonth', 'primaryCoordinator',
            'payments', 'probation'])->find($chId);
        $chActiveId = $chDetails->active_status;
        $chActiveStatus = $chDetails->activeStatus->active_status;

        if ($chDetails->state_id < 52){
            $stateShortName = $chDetails->state->state_short_name;
        }
        else{
            $stateShortName = $chDetails->country->short_name;
        }

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
        $chFinancialReportFinal = $chDetails->financialReportFinal;
        $displayEOY = $this->positionConditionsService->getEOYDisplay();  // Conditions to Show EOY Items

        $allActive = ActiveStatus::all();  // Full List for Dropdown Menu
        $allStatuses = Status::all();  // Full List for Dropdown Menu
        $allProbation = Probation::all();  // Full List for Dropdown Menu
        $allAwards = FinancialReportAwards::all();  // Full List for Dropdown Menu
        $allWebLinks = Website::all();  // Full List for Dropdown Menu
        $allStates = State::all();  // Full List for Dropdown Menu
        $allCountries = Country::all();  // Full List for Dropdown Menu
        $allMonths = Month::all();  // Full List for Dropdown Menu
        $allRegions = Region::with('conference')  // Full List for Dropdown Menu based on Conference
            ->where('conference_id', $chConfId)
            ->get();

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

        return ['chDetails' => $chDetails, 'chActiveId' => $chActiveId, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'allActive' => $allActive,
            'conferenceDescription' => $conferenceDescription, 'chConfId' => $chConfId, 'chRegId' => $chRegId, 'chPcId' => $chPcId, 'chId' => $chId, 'chFinancialReportFinal' => $chFinancialReportFinal,
            'chDocuments' => $chDocuments, 'reviewComplete' => $reviewComplete, 'chFinancialReport' => $chFinancialReport, 'allAwards' => $allAwards, 'chPayments' => $chPayments,
            'allRegions' => $allRegions, 'allCountries' => $allCountries,
            'emailListChap' => $emailListChap, 'emailListCoord' => $emailListCoord, 'pcList' => $pcList, 'rrList' => $rrList, 'emailCCData' => $emailCCData, 'chActiveStatus' => $chActiveStatus,
            'allWebLinks' => $allWebLinks, 'allStatuses' => $allStatuses, 'allStates' => $allStates, 'emailCC' => $emailCC, 'emailPC' => $emailPC, 'cc_id' => $cc_id,
            'startMonthName' => $startMonthName, 'chapterStatus' => $chapterStatus, 'websiteLink' => $websiteLink, 'pcName' => $pcName, 'displayEOY' => $displayEOY, 'probationReason' => $probationReason,
            'allMonths' => $allMonths, 'pcDetails' => $pcDetails, 'allProbation' => $allProbation,
        ];
    }

     /**
     * Board Details Base Query for all active chapters
     */
    public function getActiveBoardDetails($chId)
    {
        $chDetails = Chapters::with(['boards'])->find($chId);

        $boards = $chDetails->boards()->with(['state', 'country'])->get();
        $bdDetails = $boards->groupBy('board_position_id');
        $defaultBoardMember = (object) ['id' => null, 'first_name' => '', 'last_name' => '', 'email' => '', 'street_address' => '', 'city' => '', 'zip' => '', 'phone' => '', 'state_id' => '', 'country_id' => '', 'user_id' => ''];

        // Fetch board details or fallback to default
        $PresDetails = $bdDetails->get(1, collect([$defaultBoardMember]))->first(); // President
        $AVPDetails = $bdDetails->get(2, collect([$defaultBoardMember]))->first(); // AVP
        $MVPDetails = $bdDetails->get(3, collect([$defaultBoardMember]))->first(); // MVP
        $TRSDetails = $bdDetails->get(4, collect([$defaultBoardMember]))->first(); // Treasurer
        $SECDetails = $bdDetails->get(5, collect([$defaultBoardMember]))->first(); // Secretary

        return ['PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
         ];
    }

     /**
     * Board Details Base Query for all disbanded chapters
     */
    public function getDisbandedBoardDetails($chId)
    {
        $chDetails = Chapters::with(['disbandCheck', 'boardsDisbanded'])->find($chId);

        $chDisbanded = $chDetails->disbandCheck;
        $bdDisbanded = $chDetails->boardsDisbanded()->with(['state', 'country'])->get();
        $bdDisbandedDetails = $bdDisbanded->groupBy('board_position_id');
        $defaultDisbandedBoardMember = (object) ['id' => null, 'first_name' => '', 'last_name' => '', 'email' => '', 'street_address' => '', 'city' => '', 'zip' => '', 'phone' => '', 'state_id' => '', 'country_id' => '', 'user_id' => ''];

        // Fetch board details or fallback to default
        $PresDisbandedDetails = $bdDisbandedDetails->get(1, collect([$defaultDisbandedBoardMember]))->first(); // President
        $AVPDisbandedDetails = $bdDisbandedDetails->get(2, collect([$defaultDisbandedBoardMember]))->first(); // AVP
        $MVPDisbandedDetails = $bdDisbandedDetails->get(3, collect([$defaultDisbandedBoardMember]))->first(); // MVP
        $TRSDisbandedDetails = $bdDisbandedDetails->get(4, collect([$defaultDisbandedBoardMember]))->first(); // Treasurer
        $SECDisbandedDetails = $bdDisbandedDetails->get(5, collect([$defaultDisbandedBoardMember]))->first(); // Secretary

        return ['chDisbanded' => $chDisbanded, 'PresDisbandedDetails' => $PresDisbandedDetails,
            'AVPDisbandedDetails' => $AVPDisbandedDetails, 'MVPDisbandedDetails' => $MVPDisbandedDetails,
            'TRSDisbandedDetails' => $TRSDisbandedDetails, 'SECDisbandedDetails' => $SECDisbandedDetails,
         ];
    }

     /**
     * Board Details Base Query for all incoming chapters
     */
    public function getIncomingBoardDetails($chId)
    {
        $chDetails = Chapters::with(['boardsIncoming'])->find($chId);

        $bdIncoming = $chDetails->boardsIncoming()->with('state', 'country')->get();
        $bdIncomingDetails = $bdIncoming->groupBy('board_position_id');
        $defaultIncomingBoardMember = (object) ['id' => null, 'first_name' => '', 'last_name' => '', 'email' => '', 'street_address' => '', 'city' => '', 'zip' => '', 'phone' => '', 'state_id' => '', 'country_id' => '', 'user_id' => ''];

        // Fetch board details or fallback to default
        $PresIncomingDetails = $bdIncomingDetails->get(1, collect([$defaultIncomingBoardMember]))->first(); // President
        $AVPIncomingDetails = $bdIncomingDetails->get(2, collect([$defaultIncomingBoardMember]))->first(); // AVP
        $MVPIncomingDetails = $bdIncomingDetails->get(3, collect([$defaultIncomingBoardMember]))->first(); // MVP
        $TRSIncomingDetails = $bdIncomingDetails->get(4, collect([$defaultIncomingBoardMember]))->first(); // Treasurer
        $SECIncomingDetails = $bdIncomingDetails->get(5, collect([$defaultIncomingBoardMember]))->first(); // Secretary

        return ['PresIncomingDetails' => $PresIncomingDetails, 'AVPIncomingDetails' => $AVPIncomingDetails, 'MVPIncomingDetails' => $MVPIncomingDetails,
            'TRSIncomingDetails' => $TRSIncomingDetails, 'SECIncomingDetails' => $SECIncomingDetails,
         ];
    }

     /**
     * Board Details Base Query for all pending chapters
     */
    public function getPendingBoardDetails($chId)
    {
        $chDetails = Chapters::with(['boardsPending'])->find($chId);

        $bdPending = $chDetails->boardsPending()->with('state', 'country')->get();
        $bdPendingDetails = $bdPending->groupBy('board_position_id');
        $defaultPendingBoardMember = (object) ['id' => null, 'first_name' => '', 'last_name' => '', 'email' => '', 'street_address' => '', 'city' => '', 'zip' => '', 'phone' => '', 'state_id' => '', 'country_id' => '', 'user_id' => ''];

        // Fetch board details or fallback to default
        $PresPendingDetails = $bdPendingDetails->get(1, collect([$defaultPendingBoardMember]))->first(); // President
        $AVPPendingDetails = $bdPendingDetails->get(2, collect([$defaultPendingBoardMember]))->first(); // AVP
        $MVPPendingDetails = $bdPendingDetails->get(3, collect([$defaultPendingBoardMember]))->first(); // MVP
        $TRSPendingDetails = $bdPendingDetails->get(4, collect([$defaultPendingBoardMember]))->first(); // Treasurer
        $SECPendingDetails = $bdPendingDetails->get(5, collect([$defaultPendingBoardMember]))->first(); // Secretary

        return ['PresPendingDetails' => $PresPendingDetails, 'AVPPendingDetails' => $AVPPendingDetails, 'MVPPendingDetails' => $MVPPendingDetails,
            'TRSPendingDetails' => $TRSPendingDetails, 'SECPendingDetails' => $SECPendingDetails,
         ];
    }

}
