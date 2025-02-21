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

class BaseChapterController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController )
    {
        $this->userController = $userController;
    }

    /*/ Active Chapter List Base Query /*/
    public function getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    {
        $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);
        if ($conditions['coordinatorCondition']) {
            $coordinatorData = $this->userController->loadReportingTree($cdId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = Chapters::with(['state', 'conference', 'region', 'webLink', 'president', 'avp', 'mvp', 'treasurer', 'secretary', 'startMonth',
            'state', 'primaryCoordinator'])
            ->where('is_active', 1);

        if ($conditions['founderCondition']) {
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('conference_id', '=', $cdConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('region_id', '=', $cdRegId);
        } else {
            $baseQuery->whereIn('primary_coordinator_id', $inQryArr);
        }

        if (isset($_GET['check']) && $_GET['check'] == 'yes') {
            $checkBoxStatus = 'checked';
            $baseQuery->where('primary_coordinator_id', '=', $cdId);
        } else {
            $checkBoxStatus = '';
        }

        $checkBox3Status = '';

        if (isset($_GET['check4']) && $_GET['check4'] == 'yes') {
            $checkBox4Status = 'checked';
            $baseQuery->where('status_id', '!=', '1');
        } else {
            $checkBox4Status = '';
        }

        $isReregPage = request()->route()->getName() === 'chapters.chapreregistration';  // Check if we're on the re-registration page
        if ($isReregPage) {
            if (isset($_GET['check3']) && $_GET['check3'] == 'yes') {
                $checkBox3Status = 'checked';
                $baseQuery->orderBy(State::select('state_short_name')  // All chapter Re-Reg list sort by state and name
                    ->whereColumn('state.id', 'chapters.state_id'), 'asc')
                    ->orderBy('chapters.name');
            } else {
                $baseQuery->orderByDesc('next_renewal_year')  // Re-Reg sort by next renewal date
                         ->orderByDesc('start_month_id');
            }
            } else {
                $baseQuery->orderBy(Conference::select('short_name')
                    ->whereColumn('conference.id', 'chapters.conference_id')
                )
                    ->orderBy(
                        Region::select('short_name')
                                ->whereColumn('region.id', 'chapters.region_id')
                    )
                    ->orderBy(State::select('state_short_name')
                            ->whereColumn('state.id', 'chapters.state_id'), 'asc')

                    ->orderBy('chapters.name');
            }

        return ['query' => $baseQuery, 'checkBoxStatus' => $checkBoxStatus, 'checkBox3Status' => $checkBox3Status, 'checkBox4Status' => $checkBox4Status];
    }

    /*/ Zapped Chapter List Base Query /*/
    public function getZappedBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    {
        $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);
        if ($conditions['coordinatorCondition']) {
            $coordinatorData = $this->userController->loadReportingTree($cdId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = Chapters::with(['state', 'conference', 'region', 'president', 'primaryCoordinator'])
            ->where('is_active', 0)
            ->orderByDesc('chapters.zap_date');

        if ($conditions['founderCondition']) {
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('conference_id', '=', $cdConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('region_id', '=', $cdRegId);
        } else {
            $baseQuery->whereIn('primary_coordinator_id', $inQryArr);
        }

        return ['query' => $baseQuery];
    }

    /*/ Active Chapter Details Base Query /*/
    public function getChapterDetails($chId)
    {
        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'documents', 'financialReport', 'startMonth', 'boards', 'primaryCoordinator'])->find($chId);
        $chId = $chDetails->id;
        $chIsActive = $chDetails->is_active;
        $stateShortName = $chDetails->state->state_short_name;
        $regionLongName = $chDetails->region->long_name;
        $conferenceDescription = $chDetails->conference->conference_description;
        $chConfId = $chDetails->conference_id;
        $chRegId = $chDetails->region_id;
        $chPcId = $chDetails->primary_coordinator_id;

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
        $emailData = $this->userController->loadConferenceCoord($chPcId);
        $emailCC = $emailData['cc_email'];
        $cc_fname = $emailData['cc_fname'];
        $cc_lname = $emailData['cc_lname'];
        $cc_pos = $emailData['cc_pos'];
        $cc_conf_name = $emailData['cc_conf_name'];
        $cc_conf_desc = $emailData['cc_conf_desc'];

        // //Primary Coordinator Notification//
        $pcDetails = Coordinators::find($chPcId);
        $emailPC = $pcDetails->email;
        $pcName = $pcDetails->first_name.' '.$pcDetails->last_name;

        // Load Report Reviewer Coordinator Dropdown List
        $pcDetails = $this->userController->loadPrimaryList($chRegId, $chConfId);

        return ['chDetails' => $chDetails, 'chIsActive' => $chIsActive, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription, 'chConfId' => $chConfId, 'chRegId' => $chRegId, 'chPcId' => $chPcId, 'chId' => $chId,
            'chDocuments' => $chDocuments, 'reviewComplete' => $reviewComplete, 'chFinancialReport' => $chFinancialReport, 'allAwards' => $allAwards,
            'PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
            'emailListChap' => $emailListChap, 'emailListCoord' => $emailListCoord, 'pcDetails' => $pcDetails, 'submitted' => $submitted,
            'allWebLinks' => $allWebLinks, 'allStatuses' => $allStatuses, 'allStates' => $allStates, 'emailCC' => $emailCC, 'emailPC' => $emailPC,
            'startMonthName' => $startMonthName, 'chapterStatus' => $chapterStatus, 'websiteLink' => $websiteLink, 'pcName' => $pcName, 'displayEOY' => $displayEOY,
            'cc_fname' => $cc_fname, 'cc_lname' => $cc_lname, 'cc_pos' => $cc_pos, 'cc_conf_desc' => $cc_conf_desc, 'cc_conf_name' => $cc_conf_name
        ];

    }

}
