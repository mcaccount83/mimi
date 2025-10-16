<?php

namespace App\Http\Controllers;

use App\Enums\BoardPosition;
use App\Models\ActiveStatus;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\Country;
use App\Models\FinancialReportAwards;
use App\Models\Probation;
use App\Models\State;
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
        $chActiveId = $chDetails->active_status;
        $chActiveStatus = $chDetails->activeStatus->active_status;

        if ($chDetails->state_id < 52) {
            $stateShortName = $chDetails->state->state_short_name;
        } else {
            $stateShortName = $chDetails->country->short_name;
        }

        $chConfId = $chDetails->conference_id;
        $chPcId = $chDetails->primary_coordinator_id;
        $probationReason = $chDetails->probation?->probation_reason;
        $startMonthName = $chDetails->startMonth->month_long_name;

        $allActive = ActiveStatus::all();
        $allProbation = Probation::all();
        $allWebLinks = Website::all();
        $allStates = State::all();
        $allCountries = Country::all();
        $allAwards = FinancialReportAwards::all();

        $chPayments = $chDetails->payments;
        $chDocuments = $chDetails->documents;
        $reviewerEmail = $chDetails->reportReviewer?->email;
        $chFinancialReport = $chDetails->financialReport;
        $chFinancialReportFinal = $chDetails->financialReportFinal;
        $displayEOY = $this->positionConditionsService->getEOYDisplay();

        $awards = $chDetails->financialReport;
        $chDisbanded = $chDetails->disbandCheck;

        if ($chActiveId == '1') {
            $boards = $chDetails->boards()->with(['state', 'country'])->get();
            $bdDetails = $boards->groupBy('board_position_id');
            $defaultBoardMember = (object) ['id' => null, 'first_name' => '', 'last_name' => '', 'email' => '', 'street_address' => '', 'city' => '', 'zip' => '', 'phone' => '', 'state_id' => '', 'country_id' => '', 'user_id' => ''];

            // Fetch board details using BoardPosition constants
            $PresDetails = $bdDetails->get(BoardPosition::PRES, collect([$defaultBoardMember]))->first();
            $AVPDetails = $bdDetails->get(BoardPosition::AVP, collect([$defaultBoardMember]))->first();
            $MVPDetails = $bdDetails->get(BoardPosition::MVP, collect([$defaultBoardMember]))->first();
            $TRSDetails = $bdDetails->get(BoardPosition::TRS, collect([$defaultBoardMember]))->first();
            $SECDetails = $bdDetails->get(BoardPosition::SEC, collect([$defaultBoardMember]))->first();
        }

        if ($chActiveId == '0') {
            $bdDisbanded = $chDetails->boardsDisbanded()->with(['state', 'country'])->get();
            $bdDisbandedDetails = $bdDisbanded->groupBy('board_position_id');
            $defaultDisbandedBoardMember = (object) ['id' => null, 'first_name' => '', 'last_name' => '', 'email' => '', 'street_address' => '', 'city' => '', 'zip' => '', 'phone' => '', 'state_id' => '', 'country_id' => '', 'user_id' => ''];

            // Fetch board details using BoardPosition constants
            $PresDetails = $bdDisbandedDetails->get(BoardPosition::PRES, collect([$defaultDisbandedBoardMember]))->first();
            $AVPDetails = $bdDisbandedDetails->get(BoardPosition::AVP, collect([$defaultDisbandedBoardMember]))->first();
            $MVPDetails = $bdDisbandedDetails->get(BoardPosition::MVP, collect([$defaultDisbandedBoardMember]))->first();
            $TRSDetails = $bdDisbandedDetails->get(BoardPosition::TRS, collect([$defaultDisbandedBoardMember]))->first();
            $SECDetails = $bdDisbandedDetails->get(BoardPosition::SEC, collect([$defaultDisbandedBoardMember]))->first();
        }

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
            'pcDetails' => $pcDetails, 'chDisbanded' => $chDisbanded, 'allProbation' => $allProbation, 'probationReason' => $probationReason,
        ];
    }
}
