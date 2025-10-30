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

    /**
     * Get chapter details with appropriate board members based on active status
     * Simplified version for board member access (no lists, filters, or sorting needed)
     */
    public function getChapterDetails($id)
    {
        // Load chapter with common relations
        $chDetails = Chapters::with([
            'country', 'state', 'conference', 'region', 'startMonth', 'webLink', 'documents', 'financialReport', 'financialReportFinal', 'payments',
            'reportReviewer', 'primaryCoordinator', 'probation', 'disbandCheck', 'activeStatus',
        ])->find($id);

        $chId = $chDetails->id;
        $chActiveId = $chDetails->active_status;
        $chActiveStatus = $chDetails->activeStatus->active_status;

        // Get board details from the appropriate table based on active status
        $boardDetails = $this->getBoardDetailsByStatus($chId, $chActiveId);

        // State/Country logic
        if ($chDetails->state_id < 52) {
            $stateShortName = $chDetails->state->state_short_name;
        } else {
            $stateShortName = $chDetails->country->short_name;
        }

        $chConfId = $chDetails->conference_id;
        $chPcId = $chDetails->primary_coordinator_id;
        $probationReason = $chDetails->probation?->probation_reason;
        $startMonthName = $chDetails->startMonth->month_long_name;

        // Full lists for dropdown menus
        $allActive = ActiveStatus::all();
        $allProbation = Probation::all();
        $allWebLinks = Website::all();
        $allStates = State::all();
        $allCountries = Country::all();
        $allAwards = FinancialReportAwards::all();

        // Chapter data
        $chPayments = $chDetails->payments;
        $chDocuments = $chDetails->documents;
        $reviewerEmail = $chDetails->reportReviewer?->email;
        $chFinancialReport = $chDetails->financialReport;
        $chFinancialReportFinal = $chDetails->financialReportFinal;
        $displayEOY = $this->positionConditionsService->getEOYDisplay();
        $chDisbanded = $chDetails->disbandCheck;

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

        // Merge everything together
        return array_merge([
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'cc_id' => $cc_id, 'chFinancialReportFinal' => $chFinancialReportFinal,
            'chFinancialReport' => $chFinancialReport, 'startMonthName' => $startMonthName, 'chDocuments' => $chDocuments, 'chPayments' => $chPayments, 'allActive' => $allActive,
            'chActiveId' => $chActiveId, 'allWebLinks' => $allWebLinks, 'allStates' => $allStates, 'emailListChap' => $emailListChap, 'emailListCoord' => $emailListCoord,
            'emailCC' => $emailCC, 'chActiveStatus' => $chActiveStatus, 'reviewerEmail' => $reviewerEmail, 'awards' => $chFinancialReport, 'allAwards' => $allAwards, 'pcEmail' => $pcEmail,
            'displayEOY' => $displayEOY, 'allCountries' => $allCountries, 'pcDetails' => $pcDetails, 'chDisbanded' => $chDisbanded, 'allProbation' => $allProbation,
            'probationReason' => $probationReason,
        ], $boardDetails); // Add board member details from appropriate table
    }

    /**
     * Route to correct board details table based on active status
     */
    private function getBoardDetailsByStatus($chId, $activeStatus)
    {
        switch ($activeStatus) {
            case 0: // Zapped/Disbanded
                return $this->getDisbandedBoardDetails($chId);

            case 1: // Active
                return $this->getActiveBoardDetails($chId);

            case 2: // Pending
            case 3: // Not Approved
                return $this->getPendingBoardDetails($chId);

            default:
                // Fallback to active board details
                return $this->getActiveBoardDetails($chId);
        }
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

        return ['PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,

        // $PresDisbandedDetails = $bdDisbandedDetails->get(BoardPosition::PRES, collect([$defaultDisbandedBoardMember]))->first();
        // $AVPDisbandedDetails = $bdDisbandedDetails->get(BoardPosition::AVP, collect([$defaultDisbandedBoardMember]))->first();
        // $MVPDisbandedDetails = $bdDisbandedDetails->get(BoardPosition::MVP, collect([$defaultDisbandedBoardMember]))->first();
        // $TRSDisbandedDetails = $bdDisbandedDetails->get(BoardPosition::TRS, collect([$defaultDisbandedBoardMember]))->first();
        // $SECDisbandedDetails = $bdDisbandedDetails->get(BoardPosition::SEC, collect([$defaultDisbandedBoardMember]))->first();

        // return ['chDisbanded' => $chDisbanded, 'PresDisbandedDetails' => $PresDisbandedDetails,
        //     'AVPDisbandedDetails' => $AVPDisbandedDetails, 'MVPDisbandedDetails' => $MVPDisbandedDetails,
        //     'TRSDisbandedDetails' => $TRSDisbandedDetails, 'SECDisbandedDetails' => $SECDisbandedDetails,
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

        // Fetch board details using BoardPosition constants
        $PresDetails = $bdIncomingDetails->get(BoardPosition::PRES, collect([$defaultIncomingBoardMember]))->first();
        $AVPDetails = $bdIncomingDetails->get(BoardPosition::AVP, collect([$defaultIncomingBoardMember]))->first();
        $MVPDetails = $bdIncomingDetails->get(BoardPosition::MVP, collect([$defaultIncomingBoardMember]))->first();
        $TRSDetails = $bdIncomingDetails->get(BoardPosition::TRS, collect([$defaultIncomingBoardMember]))->first();
        $SECDetails = $bdIncomingDetails->get(BoardPosition::SEC, collect([$defaultIncomingBoardMember]))->first();

                return ['PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,

        ];
        // $PresIncomingDetails = $bdIncomingDetails->get(BoardPosition::PRES, collect([$defaultIncomingBoardMember]))->first();
        // $AVPIncomingDetails = $bdIncomingDetails->get(BoardPosition::AVP, collect([$defaultIncomingBoardMember]))->first();
        // $MVPIncomingDetails = $bdIncomingDetails->get(BoardPosition::MVP, collect([$defaultIncomingBoardMember]))->first();
        // $TRSIncomingDetails = $bdIncomingDetails->get(BoardPosition::TRS, collect([$defaultIncomingBoardMember]))->first();
        // $SECIncomingDetails = $bdIncomingDetails->get(BoardPosition::SEC, collect([$defaultIncomingBoardMember]))->first();

        // return ['PresIncomingDetails' => $PresIncomingDetails, 'AVPIncomingDetails' => $AVPIncomingDetails, 'MVPIncomingDetails' => $MVPIncomingDetails,
        //     'TRSIncomingDetails' => $TRSIncomingDetails, 'SECIncomingDetails' => $SECIncomingDetails,
        // ];
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

                        return ['PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,

        ];
    }
}
