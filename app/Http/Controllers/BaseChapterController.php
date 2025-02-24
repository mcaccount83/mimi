<?php

namespace App\Http\Controllers;

use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\FinancialReportAwards;
use App\Models\Conference;
use App\Models\Region;
use App\Models\State;
use App\Models\Status;
use App\Models\Website;
use Illuminate\Support\Facades\Log;

class BaseChapterController extends Controller
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
    // $displayEOY = getEOYDisplay();

    /*/User Controller/*/
    // $this->userController->loadReportingTree($cdId);
    // $this->userController->loadEmailDetails($chId);
    // $this->userController->loadConferenceCoord($chPcId);
    // $this->userController->loadPrimaryList($chRegId, $chConfId);

    /*/Base Coditions Controller/*/
    // $this->baseConditionsController->getConditions($cdId, $cdPositionid, $cdSecPositionid);
    // $this->baseConditionsController->applyPositionConditions($baseQuery, $conditions, $cdConfId, $cdRegId, $inQryArr)
    // $this->baseConditionsController->applyInquiriesPositionConditions($baseQuery, $conditions, $cdConfId, $cdRegId, $inQryArrrr)

    /**
     * Apply checkbox filters to the query
     */
    private function applyCheckboxFilters($baseQuery, $cdId)
    {
        $checkboxStatus = ['checkBoxStatus' => '', 'checkBox2Status' => '', 'checkBox3Status' => '', 'checkBox4Status' => ''];

        if (isset($_GET['check']) && $_GET['check'] == 'yes') {
            $checkboxStatus['checkBoxStatus'] = 'checked';
            $baseQuery->where('primary_coordinator_id', '=', $cdId);
        }

        if (isset($_GET['check2']) && $_GET['check2'] == 'yes') {
            $checkboxStatus['checkBox2Status'] = 'checked';
            $baseQuery->whereHas('financialReport', function ($query) use ($cdId) {
                $query->where('reviewer_id', '=', $cdId);
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
    private function applySorting($baseQuery, $queryType) {
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

        if (isset($params['cdId'])) {
            // Only apply position conditions if this is not an international query
            if (isset($params['conditions']) && $params['conditions']) {
                $conditionsData = $this->baseConditionsController->getConditions(
                    $params['cdId'],
                    $params['cdPositionid'],
                    $params['cdSecPositionid']
                );

                $positionMethod = $params['queryType'] === 'inquiries'
                    ? 'applyInquiriesPositionConditions'
                    : 'applyPositionConditions';

                $baseQuery = $this->baseConditionsController->$positionMethod(
                    $baseQuery,
                    $conditionsData['conditions'],
                    $params['cdConfId'] ?? null,
                    $params['cdRegId'] ?? null,
                    $conditionsData['inQryArr']
                );
            }

            $checkboxResults = $this->applyCheckboxFilters($baseQuery, $params['cdId']);
            $baseQuery = $checkboxResults['query'];
            $checkboxStatus = $checkboxResults['status'];
        }

        $sortingResults = $this->applySorting($baseQuery, $params['isActive'] ? 'active' : 'zapped');

        return ['query' => $sortingResults['query'], 'checkBoxStatus' => $checkboxStatus['checkBoxStatus'] ?? '', 'checkBox2Status' => $checkboxStatus['checkBox2Status'] ?? '',
            'checkBox3Status' => $sortingResults['checkBox3Status'], 'checkBox4Status' => $checkboxStatus['checkBox4Status'] ?? ''
        ];
    }

    /**
     * Public methods for different query types
     */
    public function getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    {
        return $this->buildChapterQuery([
            'isActive' => 1,
            'cdId' => $cdId,
            'cdConfId' => $cdConfId,
            'cdRegId' => $cdRegId,
            'cdPositionid' => $cdPositionid,
            'cdSecPositionid' => $cdSecPositionid,
            'conditions' => true,
            'queryType' => 'regular'
        ]);
    }

    public function getZappedBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    {
        return $this->buildChapterQuery([
            'isActive' => 0,
            'cdId' => $cdId,
            'cdConfId' => $cdConfId,
            'cdRegId' => $cdRegId,
            'cdPositionid' => $cdPositionid,
            'cdSecPositionid' => $cdSecPositionid,
            'conditions' => true,
            'queryType' => 'regular'
        ]);
    }

    public function getActiveInquiriesBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    {
        return $this->buildChapterQuery([
            'isActive' => 1,
            'cdId' => $cdId,
            'cdConfId' => $cdConfId,
            'cdRegId' => $cdRegId,
            'cdPositionid' => $cdPositionid,
            'cdSecPositionid' => $cdSecPositionid,
            'conditions' => true,
            'queryType' => 'inquiries'
        ]);
    }

    public function getZappedInquiriesBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    {
        return $this->buildChapterQuery([
            'isActive' => 0,
            'cdId' => $cdId,
            'cdConfId' => $cdConfId,
            'cdRegId' => $cdRegId,
            'cdPositionid' => $cdPositionid,
            'cdSecPositionid' => $cdSecPositionid,
            'conditions' => true,
            'queryType' => 'inquiries'
        ]);
    }

    public function getActiveInternationalBaseQuery($cdId)
    {
        return $this->buildChapterQuery([
            'isActive' => 1,
            'cdId' => $cdId,
            'conditions' => false,
            'queryType' => 'international'
        ]);
    }

    public function getZappedInternationalBaseQuery($cdId) {
        return $this->buildChapterQuery([
            'isActive' => 0,
            'cdId' => $cdId,
            'conditions' => false,
            'queryType' => 'international'
        ]);
    }

    /**
     * Chapter Details Base Query for all (active and zapped) chapters
    */
    public function getChapterDetails($chId)
    {
        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'documents', 'financialReport', 'startMonth', 'boards', 'primaryCoordinator'])->find($chId);
        $chIsActive = $chDetails->is_active;
        $stateShortName = $chDetails->state->state_short_name;
        $regionLongName = $chDetails->region->long_name;
        $conferenceDescription = $chDetails->conference->conference_description;
        $chConfId = $chDetails->conference_id;
        $chRegId = $chDetails->region_id;
        $chPcId = $chDetails->primary_coordinator_id;

        // Log::info("Chapter ID: " . $chId);
        // Log::info("Primary Coordinator ID: " . $chPcId);

        $startMonthName = $chDetails->startMonth->month_long_name;
        $chapterStatus = $chDetails->status->chapter_status;
        $websiteLink = $chDetails->webLink->link_status ?? null;

        $chDocuments = $chDetails->documents;
        $submitted = $chDetails->documents->financial_report_received ?? null;
        $reviewComplete = $chDetails->documents->review_complete ?? null;
        $chFinancialReport = $chDetails->financialReport;
        $displayEOY = getEOYDisplay();

        $allStatuses = Status::all();  // Full List for Dropdown Menu
        $allAwards = FinancialReportAwards::all();  // Full List for Dropdown Menu
        $allWebLinks = Website::all();  // Full List for Dropdown Menu
        $allStates = State::all();  // Full List for Dropdown Menu

        $boards = $chDetails->boards()->with('stateName')->get();
        $bdDetails = $boards->groupBy('board_position_id');
        $defaultBoardMember = (object) ['id' => null, 'first_name' => '', 'last_name' => '', 'email' => '', 'street_address' => '', 'city' => '', 'zip' => '', 'phone' => '', 'state' => '', 'user_id' => ''];

        // Fetch board details or fallback to default
        $PresDetails = $bdDetails->get(1, collect([$defaultBoardMember]))->first(); // President
        $AVPDetails = $bdDetails->get(2, collect([$defaultBoardMember]))->first(); // AVP
        $MVPDetails = $bdDetails->get(3, collect([$defaultBoardMember]))->first(); // MVP
        $TRSDetails = $bdDetails->get(4, collect([$defaultBoardMember]))->first(); // Treasurer
        $SECDetails = $bdDetails->get(5, collect([$defaultBoardMember]))->first(); // Secretary

        // Load Board and Coordinators for Sending Email
        $emailData = $this->userController->loadEmailDetails($chId);
        $emailListChap = $emailData['emailListChap'];
        $emailListCoord = $emailData['emailListCoord'];

        // Load Conference Coordinators for Sending Email
        $emailCCData = $this->userController->loadConferenceCoord($chPcId);
        $emailCC = $emailCCData['cc_email'];
        $cc_fname = $emailCCData['cc_fname'];
        $cc_lname = $emailCCData['cc_lname'];
        $cc_pos = $emailCCData['cc_pos'];
        $cc_conf_name = $emailCCData['cc_conf_name'];
        $cc_conf_desc = $emailCCData['cc_conf_desc'];

        // //Primary Coordinator Notification//
        $pcDetails = Coordinators::find($chPcId);
        $emailPC = $pcDetails->email;
        $pcName = $pcDetails->first_name.' '.$pcDetails->last_name;

        // Load Report Reviewer Coordinator Dropdown List
        $pcDetails = $this->userController->loadPrimaryList($chRegId, $chConfId)  ?? null;;

        // Log::info("PC Details: " . json_encode($pcDetails));

        // Load Report Reviewer Coordinator Dropdown List
        $rrDetails = $this->userController->loadReviewerList($chRegId, $chConfId)  ?? null;;

        return ['chDetails' => $chDetails, 'chIsActive' => $chIsActive, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription, 'chConfId' => $chConfId, 'chRegId' => $chRegId, 'chPcId' => $chPcId, 'chId' => $chId,
            'chDocuments' => $chDocuments, 'reviewComplete' => $reviewComplete, 'chFinancialReport' => $chFinancialReport, 'allAwards' => $allAwards,
            'PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
            'emailListChap' => $emailListChap, 'emailListCoord' => $emailListCoord, 'pcDetails' => $pcDetails, 'submitted' => $submitted, 'rrDetails' => $rrDetails,
            'allWebLinks' => $allWebLinks, 'allStatuses' => $allStatuses, 'allStates' => $allStates, 'emailCC' => $emailCC, 'emailPC' => $emailPC,
            'startMonthName' => $startMonthName, 'chapterStatus' => $chapterStatus, 'websiteLink' => $websiteLink, 'pcName' => $pcName, 'displayEOY' => $displayEOY,
            'cc_fname' => $cc_fname, 'cc_lname' => $cc_lname, 'cc_pos' => $cc_pos, 'cc_conf_desc' => $cc_conf_desc, 'cc_conf_name' => $cc_conf_name
        ];
    }

}
