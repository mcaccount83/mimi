<?php

namespace App\Http\Controllers;

use App\Enums\BoardPosition;
use App\Enums\ChapterCheckbox;
use App\Models\ActiveStatus;
use App\Models\Chapters;
use App\Models\Conference;
use App\Models\Coordinators;
use App\Models\Country;
use App\Models\FinancialReportAwards;
use App\Models\Month;
use App\Models\Probation;
use App\Models\Region;
use App\Models\State;
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
    private function applyCheckboxFilters($baseQuery, $coorId, $conditions = null, $confId = null, $regId = null)
    {
        $checkboxStatus = [
            ChapterCheckbox::CHECK_PRIMARY => '',
            ChapterCheckbox::CHECK_REVIEWER => '',
            ChapterCheckbox::CHECK_CONFERENCE_REGION => '',
            ChapterCheckbox::CHECK_PROBATION => '',
            ChapterCheckbox::CHECK_INTERNATIONAL => '',
            ChapterCheckbox::CHECK_INTERNATIONALREREG => '',
        ];

        // Checkbox
        if (isset($_GET[ChapterCheckbox::PRIMARY_COORDINATOR]) && $_GET[ChapterCheckbox::PRIMARY_COORDINATOR] == 'yes') {
            $checkboxStatus[ChapterCheckbox::CHECK_PRIMARY] = 'checked';
            $baseQuery->where('primary_coordinator_id', '=', $coorId);
        }

        // Checkbox2
        if (isset($_GET[ChapterCheckbox::REVIEWER]) && $_GET[ChapterCheckbox::REVIEWER] == 'yes') {
            $checkboxStatus[ChapterCheckbox::CHECK_REVIEWER] = 'checked';
            $baseQuery->whereHas('financialReport', function ($query) use ($coorId) {
                $query->where('reviewer_id', '=', $coorId);
            });
        }

        // Checkbox3
        if (isset($_GET[ChapterCheckbox::CONFERENCE_REGION]) && $_GET[ChapterCheckbox::CONFERENCE_REGION] == 'yes') {
            $checkboxStatus[ChapterCheckbox::CHECK_CONFERENCE_REGION] = 'checked';
            // Position conditions already applied in buildChapterQuery
            if ($conditions && $conditions['inquiriesConferenceCondition']) {
                if ($regId && $regId > 0) {
                    $baseQuery->where('region_id', '=', $regId);
                } else {
                    $baseQuery->where('conference_id', '=', $confId);
                }
            }
        }

        // Checkbox4
        if (isset($_GET[ChapterCheckbox::PROBATION]) && $_GET[ChapterCheckbox::PROBATION] == 'yes') {
            $checkboxStatus[ChapterCheckbox::CHECK_PROBATION] = 'checked';
            // Position conditions already applied in buildChapterQuery
            $baseQuery->where('status_id', '!=', '1');
        }

        // Checkbox5
        if (isset($_GET[ChapterCheckbox::INTERNATIONAL]) && $_GET[ChapterCheckbox::INTERNATIONAL] == 'yes') {
            $checkboxStatus[ChapterCheckbox::CHECK_INTERNATIONAL] = 'checked';
            // Position conditions were skipped in buildChapterQuery
            if ($conditions && (
                ! $conditions['inquiriesInternationalCondition'] &&
                ! $conditions['ITCondition'] &&
                ! $conditions['einCondition'])) {
                // User doesn't have international permissions, show nothing
                $baseQuery->whereRaw('1 = 0');
            }
        }

        // Checkbox6
        if (isset($_GET[ChapterCheckbox::INTERNATIONALREREG]) && $_GET[ChapterCheckbox::INTERNATIONALREREG] == 'yes') {
            $checkboxStatus[ChapterCheckbox::CHECK_INTERNATIONALREREG] = 'checked';
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
        $query = Chapters::query()->where('active_status', $activeStatus);

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
    private function applySorting($baseQuery)
    {
        $isPendingPage = request()->route()->getName() == 'chapters.chappending';
        $isNotApprovedPage = request()->route()->getName() == 'chapters.chapnotapproved';
        $isZappedPage = request()->route()->getName() == 'chapters.chapzapped';
        $isReregPage = request()->route()->getName() == 'payment.chapreregistration';

        if ($isPendingPage || $isNotApprovedPage) {
            $baseQuery->orderByDesc('chapters.created_at');

            return ['query' => $baseQuery];
        }

        if ($isZappedPage) {
            $baseQuery->orderByDesc('chapters.zap_date');

            return ['query' => $baseQuery];
        }

        if ($isReregPage && ! ((isset($_GET['check3']) && $_GET['check3'] == 'yes') || (isset($_GET['check5']) && $_GET['check5'] == 'yes'))) {
            $baseQuery->orderByDesc('next_renewal_year')
                ->orderByDesc('start_month_id');

            return ['query' => $baseQuery];
        }

        return ['query' => $baseQuery->orderBy(Conference::select('short_name')
            ->whereColumn('conference.id', 'chapters.conference_id'))
            ->orderBy(Region::select('short_name')
                ->whereColumn('region.id', 'chapters.region_id'))
            ->orderBy(State::select('state_short_name')
                ->whereColumn('state.id', 'chapters.state_id'), 'asc')
            ->orderBy('chapters.name'),
        ];
    }

    /**
     * Build chapter query based on type and parameters
     */
    private function buildChapterQuery($params)
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

            // Only apply position conditions if NEITHER International nor InternationalReReg is selected
            if ((! isset($_GET[ChapterCheckbox::INTERNATIONAL]) || $_GET[ChapterCheckbox::INTERNATIONAL] !== 'yes') &&
                (! isset($_GET[ChapterCheckbox::INTERNATIONALREREG]) || $_GET[ChapterCheckbox::INTERNATIONALREREG] !== 'yes') &&
                (! isset($_GET[ChapterCheckbox::CONFERENCE_REGION]) || $_GET[ChapterCheckbox::CONFERENCE_REGION] !== 'yes')) {

                $baseQuery = $this->baseConditionsController->applyPositionConditions(
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
            ChapterCheckbox::CHECK_PRIMARY => $checkboxStatus[ChapterCheckbox::CHECK_PRIMARY] ?? '',
            ChapterCheckbox::CHECK_REVIEWER => $checkboxStatus[ChapterCheckbox::CHECK_REVIEWER] ?? '',
            ChapterCheckbox::CHECK_CONFERENCE_REGION => $checkboxStatus[ChapterCheckbox::CHECK_CONFERENCE_REGION] ?? '',
            ChapterCheckbox::CHECK_PROBATION => $checkboxStatus[ChapterCheckbox::CHECK_PROBATION] ?? '',
            ChapterCheckbox::CHECK_INTERNATIONAL => $checkboxStatus[ChapterCheckbox::CHECK_INTERNATIONAL] ?? '',
            ChapterCheckbox::CHECK_INTERNATIONALREREG => $checkboxStatus[ChapterCheckbox::CHECK_INTERNATIONALREREG] ?? '',
        ];
    }

    /**
     * Get base query for chapters with any active status
     */
    public function getBaseQuery($activeStatus, $coorId, $confId, $regId, $positionId, $secPositionId)
    {
        return $this->buildChapterQuery([
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
     * Chapter Details Base Query for all chapters
     */
    public function getChapterDetails($chId)
    {
        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'documents', 'financialReport', 'startMonth', 'primaryCoordinator',
            'payments', 'probation', 'financialReportFinal'])->find($chId);
        $chActiveId = $chDetails->active_status;
        $chActiveStatus = $chDetails->activeStatus->active_status;

        if ($chDetails->state_id < 52) {
            $stateShortName = $chDetails->state->state_short_name;
        } else {
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

        // Fetch board details using BoardPosition constants
        $PresDetails = $bdDetails->get(BoardPosition::PRES, collect([$defaultBoardMember]))->first();
        $AVPDetails = $bdDetails->get(BoardPosition::AVP, collect([$defaultBoardMember]))->first();
        $MVPDetails = $bdDetails->get(BoardPosition::MVP, collect([$defaultBoardMember]))->first();
        $TRSDetails = $bdDetails->get(BoardPosition::TRS, collect([$defaultBoardMember]))->first();
        $SECDetails = $bdDetails->get(BoardPosition::SEC, collect([$defaultBoardMember]))->first();

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

        // Fetch board details using BoardPosition constants
        $PresDetails = $bdDisbandedDetails->get(BoardPosition::PRES, collect([$defaultDisbandedBoardMember]))->first();
        $AVPDetails = $bdDisbandedDetails->get(BoardPosition::AVP, collect([$defaultDisbandedBoardMember]))->first();
        $MVPDetails = $bdDisbandedDetails->get(BoardPosition::MVP, collect([$defaultDisbandedBoardMember]))->first();
        $TRSDetails = $bdDisbandedDetails->get(BoardPosition::TRS, collect([$defaultDisbandedBoardMember]))->first();
        $SECDetails = $bdDisbandedDetails->get(BoardPosition::SEC, collect([$defaultDisbandedBoardMember]))->first();

        return ['chDisbanded' => $chDisbanded, 'PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
        ];

        // $PresDisbandedDetails = $bdDisbandedDetails->get(BoardPosition::PRES, collect([$defaultDisbandedBoardMember]))->first();
        // $AVPDisbandedDetails = $bdDisbandedDetails->get(BoardPosition::AVP, collect([$defaultDisbandedBoardMember]))->first();
        // $MVPDisbandedDetails = $bdDisbandedDetails->get(BoardPosition::MVP, collect([$defaultDisbandedBoardMember]))->first();
        // $TRSDisbandedDetails = $bdDisbandedDetails->get(BoardPosition::TRS, collect([$defaultDisbandedBoardMember]))->first();
        // $SECDisbandedDetails = $bdDisbandedDetails->get(BoardPosition::SEC, collect([$defaultDisbandedBoardMember]))->first();

        // return ['chDisbanded' => $chDisbanded, 'PresDisbandedDetails' => $PresDisbandedDetails,
        //     'AVPDisbandedDetails' => $AVPDisbandedDetails, 'MVPDisbandedDetails' => $MVPDisbandedDetails,
        //     'TRSDisbandedDetails' => $TRSDisbandedDetails, 'SECDisbandedDetails' => $SECDisbandedDetails,
        // ];
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

        // Fetch board details using BoardPosition constants
        $PresDetails = $bdIncomingDetails->get(BoardPosition::PRES, collect([$defaultIncomingBoardMember]))->first();
        $AVPDetails = $bdIncomingDetails->get(BoardPosition::AVP, collect([$defaultIncomingBoardMember]))->first();
        $MVPDetails = $bdIncomingDetails->get(BoardPosition::MVP, collect([$defaultIncomingBoardMember]))->first();
        $TRSDetails = $bdIncomingDetails->get(BoardPosition::TRS, collect([$defaultIncomingBoardMember]))->first();
        $SECDetails = $bdIncomingDetails->get(BoardPosition::SEC, collect([$defaultIncomingBoardMember]))->first();

        // $PresIncomingDetails = $bdIncomingDetails->get(BoardPosition::PRES, collect([$defaultIncomingBoardMember]))->first();
        // $AVPIncomingDetails = $bdIncomingDetails->get(BoardPosition::AVP, collect([$defaultIncomingBoardMember]))->first();
        // $MVPIncomingDetails = $bdIncomingDetails->get(BoardPosition::MVP, collect([$defaultIncomingBoardMember]))->first();
        // $TRSIncomingDetails = $bdIncomingDetails->get(BoardPosition::TRS, collect([$defaultIncomingBoardMember]))->first();
        // $SECIncomingDetails = $bdIncomingDetails->get(BoardPosition::SEC, collect([$defaultIncomingBoardMember]))->first();

        return ['PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
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

        // Fetch board details using BoardPosition constants
        $PresDetails = $bdPendingDetails->get(BoardPosition::PRES, collect([$defaultPendingBoardMember]))->first();
        $AVPDetails = $bdPendingDetails->get(BoardPosition::AVP, collect([$defaultPendingBoardMember]))->first();
        $MVPDetails = $bdPendingDetails->get(BoardPosition::MVP, collect([$defaultPendingBoardMember]))->first();
        $TRSDetails = $bdPendingDetails->get(BoardPosition::TRS, collect([$defaultPendingBoardMember]))->first();
        $SECDetails = $bdPendingDetails->get(BoardPosition::SEC, collect([$defaultPendingBoardMember]))->first();

        // $PresPendingDetails = $bdPendingDetails->get(BoardPosition::PRES, collect([$defaultPendingBoardMember]))->first();
        // $AVPPendingDetails = $bdPendingDetails->get(BoardPosition::AVP, collect([$defaultPendingBoardMember]))->first();
        // $MVPPendingDetails = $bdPendingDetails->get(BoardPosition::MVP, collect([$defaultPendingBoardMember]))->first();
        // $TRSPendingDetails = $bdPendingDetails->get(BoardPosition::TRS, collect([$defaultPendingBoardMember]))->first();
        // $SECPendingDetails = $bdPendingDetails->get(BoardPosition::SEC, collect([$defaultPendingBoardMember]))->first();

        return ['PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
        ];
    }
}
