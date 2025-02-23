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

    public function __construct(UserController $userController )
    {
        $this->userController = $userController;
    }

    /*/Custom Helpers/*/
    // $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);
    // $displayEOY = getEOYDisplay();

    /*/User Controller/*/
    // $this->userController->loadReportingTree($cdId);
    // $this->userController->loadEmailDetails($chId);
    // $this->userController->loadConferenceCoord($chPcId);
    // $this->userController->loadPrimaryList($chRegId, $chConfId);

    /**
     * Get conditions and reporting tree data based on coordinator position
     */
    public function getConditions($cdId, $cdPositionid, $cdSecPositionid)
    {
        $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);
        $inQryArr = [];

        if ($conditions['coordinatorCondition']) {
            $coordinatorData = $this->userController->loadReportingTree($cdId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        return ['conditions' => $conditions, 'inQryArr' => $inQryArr];
    }

    /**
     * Apply position-based conditions to the query
     */
    public function applyPositionConditions($baseQuery, $conditions, $cdConfId, $cdRegId, $inQryArr)
    {
        if ($conditions['founderCondition']) {
            // No restrictions for founder
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('conference_id', '=', $cdConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('region_id', '=', $cdRegId);
        } else {
            $baseQuery->whereIn('primary_coordinator_id', $inQryArr);
        }

        return $baseQuery;
    }

    /**
     * Apply position-based inquiries conditions to the query
     */
    public function applyInquiriesPositionConditions($baseQuery, $conditions, $cdConfId, $cdRegId, $inQryArr)
    {
        if ($conditions['founderCondition'] || $conditions['inquiriesInternationalCondition']) {
        } elseif ($conditions['assistConferenceCoordinatorCondition'] || $conditions['inquiriesConferneceCondition']) {
            $baseQuery->where('conference_id', '=', $cdConfId);
        } else {
            $baseQuery->where('region_id', '=', $cdRegId);
        }

        return $baseQuery;
    }

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
     * Apply sorting based on page type
     */
    private function applySorting($baseQuery, $isReregPage)
    {
        if ($isReregPage) {
            if (isset($_GET['check3']) && $_GET['check3'] == 'yes') {
                $baseQuery->orderBy(State::select('state_short_name')
                    ->whereColumn('state.id', 'chapters.state_id'), 'asc')
                    ->orderBy('chapters.name');
                return ['query' => $baseQuery, 'checkBox3Status' => 'checked'];
            } else {
                $baseQuery->orderByDesc('next_renewal_year')
                         ->orderByDesc('start_month_id');
                return ['query' => $baseQuery, 'checkBox3Status' => ''];
            }
        }

        // Default sorting for non-rereg pages
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
     * Apply sorting for zapped chapters
     */
    private function applyZappedSorting($baseQuery) {
        return ['query' => $baseQuery->orderByDesc('chapters.zap_date'), 'checkBox3Status' => ''];
    }

    /**
     * Get the base query for active chapters with all necessary relations and filters
     */
    public function getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    {
        $conditionsData = $this->getConditions($cdId, $cdPositionid, $cdSecPositionid);

        $baseQuery = Chapters::with(['state', 'conference', 'region', 'webLink','president', 'avp', 'mvp', 'treasurer', 'secretary','startMonth', 'primaryCoordinator'])
            ->where('is_active', 1);

        $baseQuery = $this->applyPositionConditions($baseQuery, $conditionsData['conditions'], $cdConfId, $cdRegId, $conditionsData['inQryArr']);

        $checkboxResults = $this->applyCheckboxFilters($baseQuery, $cdId);
        $baseQuery = $checkboxResults['query'];
        $checkboxStatus = $checkboxResults['status'];

        // Apply sorting based on page type
        $isReregPage = request()->route()->getName() === 'chapters.chapreregistration';
        $sortingResults = $this->applySorting($baseQuery, $isReregPage);

        return [
            'query' => $sortingResults['query'], 'checkBoxStatus' => $checkboxStatus['checkBoxStatus'], 'checkBox2Status' => $checkboxStatus['checkBox2Status'],
            'checkBox3Status' => $sortingResults['checkBox3Status'], 'checkBox4Status' => $checkboxStatus['checkBox4Status']
        ];
    }

    /**
     * Get the base query for zapped chapters with all necessary relations and filters
     */
    public function getZappedBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    {
        $conditionsData = $this->getConditions($cdId, $cdPositionid, $cdSecPositionid);

        $baseQuery = Chapters::with(['state', 'conference', 'region', 'webLink', 'president', 'avp', 'mvp', 'treasurer', 'secretary', 'startMonth', 'primaryCoordinator'])
            ->where('is_active', 0);

        $baseQuery = $this->applyPositionConditions($baseQuery, $conditionsData['conditions'], $cdConfId, $cdRegId, $conditionsData['inQryArr']);

        $checkboxResults = $this->applyCheckboxFilters($baseQuery, $cdId);
        $baseQuery = $checkboxResults['query'];
        $checkboxStatus = $checkboxResults['status'];

        // Apply zapped sorting
        $sortingResults = $this->applyZappedSorting($baseQuery);

        return ['query' => $sortingResults['query'], 'checkBoxStatus' => $checkboxStatus['checkBoxStatus'], 'checkBox2Status' => $checkboxStatus['checkBox2Status'],
            'checkBox3Status' => $sortingResults['checkBox3Status'], 'checkBox4Status' => $checkboxStatus['checkBox4Status']
        ];
    }

    /**
     * Get the base query for active inquiries chapters with all necessary relations and filters
     */
    public function getActiveInquiriesBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    {
        $conditionsData = $this->getConditions($cdId, $cdPositionid, $cdSecPositionid);

        $baseQuery = Chapters::with(['state', 'conference', 'region', 'webLink','president', 'avp', 'mvp', 'treasurer', 'secretary','startMonth', 'primaryCoordinator'])
            ->where('is_active', 1);

        $baseQuery = $this->applyInquiriesPositionConditions($baseQuery, $conditionsData['conditions'], $cdConfId, $cdRegId, $conditionsData['inQryArr']);

        $checkboxResults = $this->applyCheckboxFilters($baseQuery, $cdId);
        $baseQuery = $checkboxResults['query'];
        $checkboxStatus = $checkboxResults['status'];

        // Apply sorting based on page type
        $isReregPage = request()->route()->getName() === 'chapters.chapreregistration';
        $sortingResults = $this->applySorting($baseQuery, $isReregPage);

        return [
            'query' => $sortingResults['query'], 'checkBoxStatus' => $checkboxStatus['checkBoxStatus'], 'checkBox2Status' => $checkboxStatus['checkBox2Status'],
            'checkBox3Status' => $sortingResults['checkBox3Status'], 'checkBox4Status' => $checkboxStatus['checkBox4Status']
        ];
    }

    /**
     * Get the base query for zapped inquiries chapters with all necessary relations and filters
     */
    public function getZappedInquiriesBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    {
        $conditionsData = $this->getConditions($cdId, $cdPositionid, $cdSecPositionid);

        $baseQuery = Chapters::with(['state', 'conference', 'region', 'webLink', 'president', 'avp', 'mvp', 'treasurer', 'secretary', 'startMonth', 'primaryCoordinator'])
            ->where('is_active', 0);

        $baseQuery = $this->applyInquiriesPositionConditions($baseQuery, $conditionsData['conditions'], $cdConfId, $cdRegId, $conditionsData['inQryArr']);

        $checkboxResults = $this->applyCheckboxFilters($baseQuery, $cdId);
        $baseQuery = $checkboxResults['query'];
        $checkboxStatus = $checkboxResults['status'];

        // Apply zapped sorting
        $sortingResults = $this->applyZappedSorting($baseQuery);

        return ['query' => $sortingResults['query'], 'checkBoxStatus' => $checkboxStatus['checkBoxStatus'], 'checkBox2Status' => $checkboxStatus['checkBox2Status'],
            'checkBox3Status' => $sortingResults['checkBox3Status'], 'checkBox4Status' => $checkboxStatus['checkBox4Status']
        ];
    }


    /**
     * Get the base query for active international chapters with all necessary relations and filters
     */
    public function getActiveInternationalBaseQuery($cdId)
    {
        $baseQuery = Chapters::with(['state', 'conference', 'region', 'webLink','president', 'avp', 'mvp', 'treasurer', 'secretary','startMonth', 'primaryCoordinator'])
            ->where('is_active', 1);

        $checkboxResults = $this->applyCheckboxFilters($baseQuery, $cdId);
        $baseQuery = $checkboxResults['query'];
        $checkboxStatus = $checkboxResults['status'];

        // Apply sorting based on page type
        $isReregPage = request()->route()->getName() === 'chapters.chapreregistration';
        $sortingResults = $this->applySorting($baseQuery, $isReregPage);

        return [
            'query' => $sortingResults['query'], 'checkBoxStatus' => $checkboxStatus['checkBoxStatus'], 'checkBox2Status' => $checkboxStatus['checkBox2Status'],
            'checkBox3Status' => $sortingResults['checkBox3Status'], 'checkBox4Status' => $checkboxStatus['checkBox4Status']
        ];
    }

    /**
     * Get the base query for zapped international chapters with all necessary relations and filters
     */
    public function getZappedInternationalBaseQuery($cdId)
    {
        $baseQuery = Chapters::with(['state', 'conference', 'region', 'webLink', 'president', 'avp', 'mvp', 'treasurer', 'secretary', 'startMonth', 'primaryCoordinator'])
            ->where('is_active', 0);

        $checkboxResults = $this->applyCheckboxFilters($baseQuery, $cdId);
        $baseQuery = $checkboxResults['query'];
        $checkboxStatus = $checkboxResults['status'];

        // Apply zapped sorting
        $sortingResults = $this->applyZappedSorting($baseQuery);

        return ['query' => $sortingResults['query'], 'checkBoxStatus' => $checkboxStatus['checkBoxStatus'], 'checkBox2Status' => $checkboxStatus['checkBox2Status'],
            'checkBox3Status' => $sortingResults['checkBox3Status'], 'checkBox4Status' => $checkboxStatus['checkBox4Status']
        ];
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
