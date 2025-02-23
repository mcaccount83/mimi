<?php

namespace App\Http\Controllers;

use App\Models\Chapters;
use App\Models\FinancialReportAwards;
use App\Models\State;
use App\Models\Website;

class BaseBoardController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->userController = $userController;
    }

    /*/Custom Helpers/*/
    // $displayEOY = getEOYDisplay();

    /*/User Controller/*/
    // $this->userController->loadEmailDetails($chId);
    // $this->userController->loadConferenceCoord($chPcId);

    /*/ Active Chapter Details Base Query /*/
    public function getChapterDetails($id)
    {
        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'startMonth', 'webLink', 'state', 'documents', 'financialReport', 'president',
            'boards', 'reportReviewer', 'primaryCoordinator'])->find($id);
        $chId = $chDetails->id;
        $chIsActive = $chDetails->is_active;
        $stateShortName = $chDetails->state->state_short_name;
        $regionLongName = $chDetails->region->long_name;
        $conferenceDescription = $chDetails->conference->conference_description;
        $chConfId = $chDetails->conference_id;
        $chRegId = $chDetails->region_id;
        $chPcId = $chDetails->primary_coordinator_id;
        $startMonthName = $chDetails->startMonth->month_long_name;
        $websiteLink = $chDetails->webLink->link_status ?? null;

        $allWebLinks = Website::all(); // Full List for Dropdown Menu
        $allStates = State::all();  // Full List for Dropdown Menu
        $allAwards = FinancialReportAwards::all();  // Full List for Dropdown Menu

        $chDocuments = $chDetails->documents;
        $submitted = $chDocuments->financial_report_received;
        $reviewComplete = $chDetails->documents->review_complete;
        $reviewerEmail = $chDetails->reportReviewer?->email;  // Could be null -- no reviewer assigned
        $chFinancialReport = $chDetails->financialReport;
        $displayEOY = getEOYDisplay();

        $awards = $chDetails->financialReport;

        $boards = $chDetails->boards()->with(['stateName', 'position'])->get();
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

        // PC Email for Sending Email
        $pcEmail = $chDetails->primaryCoordinator->email;

        // Load Conference Coordinators for Sending Email
        $ccEmailData = $this->userController->loadConferenceCoord($chPcId);
        $cc_id = $ccEmailData['cc_id'];
        $emailCC = $ccEmailData['cc_email'];

        return ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'cc_id' => $cc_id,
            'chFinancialReport' => $chFinancialReport, 'startMonthName' => $startMonthName, 'chDocuments' => $chDocuments, 'submitted' => $submitted,
            'PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
            'allWebLinks' => $allWebLinks, 'allStates' => $allStates, 'emailListChap' => $emailListChap, 'emailListCoord' => $emailListCoord, 'emailCC' => $emailCC,
            'reviewerEmail' => $reviewerEmail, 'awards' => $awards, 'allAwards' => $allAwards, 'pcEmail' => $pcEmail, 'displayEOY' => $displayEOY,
        ];
    }

}
