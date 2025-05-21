<?php

namespace App\Http\Controllers;

use App\Models\ActiveStatus;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\FinancialReportAwards;
use App\Models\Probation;
use App\Models\State;
use App\Models\Country;
use App\Models\Website;
use App\Services\PositionConditionsService;

class BaseBoardController extends Controller
{
    protected $positionConditionsService;

    protected $userController;

    public function __construct(PositionConditionsService $positionConditionsService, UserController $userController)
    {
        $this->positionConditionsService = $positionConditionsService;
        $this->userController = $userController;
    }

    /* /Custom Helpers/ */
    // $displayEOY = getEOYDisplay();

    /* / Active Chapter Details Base Query / */
    public function getChapterDetails($id)
    {
        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'startMonth', 'webLink', 'state', 'documents', 'financialReport', 'president',
            'payments', 'boards', 'reportReviewer', 'primaryCoordinator', 'probation', 'disbandCheck'])->find($id);
        $chId = $chDetails->id;
        $chIsActive = $chDetails->active_status;
        $chActiveId = $chDetails->active_status;
        $chActiveStatus = $chDetails->activeStatus->active_status;
        $stateShortName = $chDetails->state->state_short_name;
        $chConfId = $chDetails->conference_id;
        $chPcId = $chDetails->primary_coordinator_id;
        $probationReason = $chDetails->probation?->probation_reason;
        $startMonthName = $chDetails->startMonth->month_long_name;

        $allActive = ActiveStatus::all();  // Full List for Dropdown Menu
        $allProbation = Probation::all();  // Full List for Dropdown Menu
        $allWebLinks = Website::all(); // Full List for Dropdown Menu
        $allStates = State::all();  // Full List for Dropdown Menu
        $allCountries = Country::all();  // Full List for Dropdown Menu
        $allAwards = FinancialReportAwards::all();  // Full List for Dropdown Menu

        $chPayments = $chDetails->payments;
        $chDocuments = $chDetails->documents;
        $reviewerEmail = $chDetails->reportReviewer?->email;  // Could be null -- no reviewer assigned
        $chFinancialReport = $chDetails->financialReport;
        $chFinancialReportFinal = $chDetails->financialReportFinal;
        $displayEOY = $this->positionConditionsService->getEOYDisplay();

        $awards = $chDetails->financialReport;

        $chDisbanded = $chDetails->disbandCheck;

        $boards = $chDetails->boards()->with(['state', 'country'])->get();
        $bdDetails = $boards->groupBy('board_position_id');
        $defaultBoardMember = (object) ['id' => null, 'first_name' => '', 'last_name' => '', 'email' => '', 'street_address' => '', 'city' => '', 'zip' => '', 'phone' => '', 'state' => '', 'country' => '', 'user_id' => ''];

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

        // PC Details for Sending Email
        $pcDetails = Coordinators::find($chPcId);
        $pcEmail = $pcDetails->email;
        $pcName = $pcDetails->first_name.' '.$pcDetails->last_name;

        // Load Conference Coordinators for Sending Email
        $ccEmailData = $this->userController->loadConferenceCoord($chPcId);
        $cc_id = $ccEmailData['cc_id'];
        $emailCC = $ccEmailData['cc_email'];

        return ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'cc_id' => $cc_id, 'chFinancialReportFinal' => $chFinancialReportFinal,
            'chFinancialReport' => $chFinancialReport, 'startMonthName' => $startMonthName, 'chDocuments' => $chDocuments, 'chPayments' => $chPayments, 'allActive' => $allActive,
            'PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails, 'chActiveId' => $chActiveId,
            'allWebLinks' => $allWebLinks, 'allStates' => $allStates, 'emailListChap' => $emailListChap, 'emailListCoord' => $emailListCoord, 'emailCC' => $emailCC, 'chActiveStatus' => $chActiveStatus,
            'reviewerEmail' => $reviewerEmail, 'awards' => $awards, 'allAwards' => $allAwards, 'pcEmail' => $pcEmail, 'displayEOY' => $displayEOY, 'allCountries' => $allCountries,
            'pcDetails' => $pcDetails, 'chDisbanded' => $chDisbanded, 'chIsActive' => $chIsActive, 'allProbation' => $allProbation, 'probationReason' => $probationReason,
        ];
    }
}
