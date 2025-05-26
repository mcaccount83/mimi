<?php

namespace App\Http\Controllers;

use App\Models\ResourceCategory;
use App\Models\Resources;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ViewAsBoardController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseBoardController;

    public function __construct(UserController $userController, BaseBoardController $baseBoardController)
    {

        $this->userController = $userController;
        $this->baseBoardController = $baseBoardController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /**
     * View the President Profile View
     */
    public function showChapterView(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $chPayments = $baseQuery['chPayments'];
        $chDocuments = $baseQuery['chDocuments'];
        $probationReason = $baseQuery['probationReason'];

        $allWebLinks = $baseQuery['allWebLinks'];
        $allStates = $baseQuery['allStates'];

        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        $year = date('Y');
        $month = date('m');
        $start_month = $chDetails->start_month_id;
        $next_renewal_year = $chDetails->next_renewal_year;
        $due_date = Carbon::create($next_renewal_year, $start_month, 1);

        $displayEOY = $baseQuery['displayEOY'];
        $displayTESTING = $displayEOY['displayTESTING'];
        $displayLIVE = $displayEOY['displayLIVE'];

        $data = ['chDetails' => $chDetails, 'chFinancialReport' => $chFinancialReport, 'stateShortName' => $stateShortName, 'allStates' => $allStates, 'allWebLinks' => $allWebLinks,
            'PresDetails' => $PresDetails, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails,
            'startMonthName' => $startMonthName, 'thisMonth' => $month, 'due_date' => $due_date, 'userType' => $userType, 'probationReason' => $probationReason,
            'displayTESTING' => $displayTESTING, 'displayLIVE' => $displayLIVE, 'chDocuments' => $chDocuments, 'chPayments' => $chPayments, 'userAdmin' => $userAdmin,
        ];

        return view('boards.president')->with($data);

    }

    /**
     * View the Chapter Re-Reg Payment Info Report View
     */
    public function showChapterReregistrationView(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chIsActive = $baseQuery['chIsActive'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];

        $year = date('Y');
        $month = date('m');
        $start_month = $chDetails->start_month_id;
        $next_renewal_year = $chDetails->next_renewal_year;
        $due_date = Carbon::create($next_renewal_year, $start_month, 1);

        // Determine the range start and end months correctly
        $monthRangeStart = $start_month;
        $monthRangeEnd = $start_month - 1;

        if ($start_month == 1) {    // Adjust range for January
            $monthRangeStart = 1;
            $monthRangeEnd = 12;
        }

        $rangeStartDate = Carbon::create($year, $monthRangeStart, 1);
        $rangeEndDate = Carbon::create($year, $monthRangeEnd, 1)->endOfMonth();
        $rangeStartDateFormatted = $rangeStartDate->format('F jS');
        $rangeEndDateFormatted = $rangeEndDate->format('F jS');

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName,
            'startMonthName' => $startMonthName, 'endRange' => $rangeEndDateFormatted, 'startRange' => $rangeStartDateFormatted,
            'thisMonth' => $month, 'due_date' => $due_date, 'userType' => $userType, 'userAdmin' => $userAdmin, 'chIsActive' => $chIsActive
        ];

        return view('boards.payment')->with($data);
    }

    /**
     * View the Chapter M2M & Sustaining Donation Info Report View
     */
    public function showChapterM2MDonationView(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chIsActive = $baseQuery['chIsActive'];
        $stateShortName = $baseQuery['stateShortName'];
        $allStates = $baseQuery['allStates'];
        $PresDetails = $baseQuery['PresDetails'];

        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'userType' => $userType, 'userAdmin' => $userAdmin, 'chIsActive' => $chIsActive,
            'PresDetails' => $PresDetails, 'allStates' => $allStates,
        ];

        return view('boards.donation')->with($data);
    }

    /**
     * View the Chapter Manual Order Form
     */
    public function showChapterManualOrderView(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chActiveId = $baseQuery['chActiveId'];
        $stateShortName = $baseQuery['stateShortName'];
        $allStates = $baseQuery['allStates'];

        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'userType' => $userType, 'userAdmin' => $userAdmin, 'chActiveId' => $chActiveId,
            'allStates' => $allStates,
        ];

        return view('boards.manualorder')->with($data);
    }

    /**
     * View the Chapter Probation Submission View
     */
    public function showProbationSubmissionView(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];

        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;
        $start_month = $chDetails->start_month_id;
        $next_renewal_year = $chDetails->next_renewal_year;
        $due_date = Carbon::create($next_renewal_year, $start_month, 1);
        $rangeEndDate = $due_date->copy()->subMonth()->endOfMonth();
        $rangeStartDate = $rangeEndDate->copy()->startOfMonth()->subYear()->addMonth();

        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName,
            'startMonthName' => $startMonthName, 'endRange' => $rangeEndDateFormatted, 'startRange' => $rangeStartDateFormatted,
            'thisMonth' => $month, 'due_date' => $due_date, 'userType' => $userType, 'userAdmin' => $userAdmin,
        ];

        return view('boards.probation')->with($data);
    }

    /**
     * View the Chapter Board Info Report View
     */
    public function showChapterBoardInfoView(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];

        $allWebLinks = $baseQuery['allWebLinks'];
        $allStates = $baseQuery['allStates'];

        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        $data = ['stateShortName' => $stateShortName, 'startMonthName' => $startMonthName, 'allStates' => $allStates, 'SECDetails' => $SECDetails,
            'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'PresDetails' => $PresDetails, 'chDetails' => $chDetails, 'userType' => $userType,
            'allWebLinks' => $allWebLinks, 'userAdmin' => $userAdmin,
        ];

        return view('boards.boardinfo')->with($data);
    }

    /**
     * View the Chapter Financial Report View
     */
    public function showChapterFinancialView(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userAdmin = $user['userAdmin'];
        $userName = $user['user_name'];
        $userEmail = $user['user_email'];
        $loggedInName = $user['user_name'];

        $baseQuery = $this->baseBoardController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chActiveId = $baseQuery['chActiveId'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $allAwards = $baseQuery['allAwards'];

        $resources = Resources::with('resourceCategory')->get();
        $resourceCategories = ResourceCategory::all();

        $data = ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userType' => $userType,
            'userName' => $userName, 'userEmail' => $userEmail,  'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName,
            'allAwards' => $allAwards, 'chActiveId' => $chActiveId, 'resourceCategories' => $resourceCategories, 'userAdmin' => $userAdmin,
        ];

        return view('boards.financial')->with($data);
    }

    /**
     * View the Disband Checklis & Report Report View
     */
    public function showDisbandChecklistView(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userAdmin = $user['userAdmin'];
        $userName = $user['user_name'];
        $userEmail = $user['user_email'];
        $loggedInName = $user['user_name'];

        $baseQuery = $this->baseBoardController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chActiveId = $baseQuery['chActiveId'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $allAwards = $baseQuery['allAwards'];

        $chDisbanded = $baseQuery['chDisbanded'];

        $resources = Resources::with('resourceCategory')->get();
        $resourceCategories = ResourceCategory::all();

        $data = ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userType' => $userType,
            'userName' => $userName, 'userEmail' => $userEmail,  'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName,
            'allAwards' => $allAwards, 'chDisbanded' => $chDisbanded, 'chActiveId' => $chActiveId, 'resourceCategories' => $resourceCategories, 'userAdmin' => $userAdmin,
        ];

        return view('boards.disband')->with($data);
    }
}
