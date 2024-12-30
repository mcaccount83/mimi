<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Chapters;
use App\Models\User;
use App\Models\Website;
use App\Models\State;
use App\Models\Resources;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ViewAsBoardController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->middleware('auth')->except('logout');
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class);
        $this->userController = $userController;
    }

     /**
     * Active Chapter Details Base Query
     */
    public function getChapterDetails($id)
    {
        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'startMonth', 'webLink', 'state', 'documents', 'financialReport', 'reportReviewer', 'boards'])->find($id);
        $stateShortName = $chDetails->state->state_short_name;
        $startMonthName = $chDetails->startMonth->month_long_name;

        $allWebLinks = Website::all(); // Full List for Dropdown Menu
        $allStates = State::all();  // Full List for Dropdown Menu

        $chDocuments = $chDetails->documents;
        $submitted = $chDetails->documents->financial_report_received;
        $chFinancialReport = $chDetails->financialReport;

        $boards = $chDetails->boards()->with(['stateName', 'position'])->get();
        $bdDetails = $boards->groupBy('board_position_id');
        $defaultBoardMember = (object)['id' => null, 'first_name' => '', 'last_name' => '', 'email' => '', 'street_address' => '', 'city' => '', 'zip' => '', 'phone' => '', 'state' => '', 'user_id' => ''];

        // Fetch board details or fallback to default
        $PresDetails = $bdDetails->get(1, collect([$defaultBoardMember]))->first(); // President
        $AVPDetails = $bdDetails->get(2, collect([$defaultBoardMember]))->first(); // AVP
        $MVPDetails = $bdDetails->get(3, collect([$defaultBoardMember]))->first(); // MVP
        $TRSDetails = $bdDetails->get(4, collect([$defaultBoardMember]))->first(); // Treasurer
        $SECDetails = $bdDetails->get(5, collect([$defaultBoardMember]))->first(); // Secretary

        return [ 'chDetails' => $chDetails,'chFinancialReport' => $chFinancialReport, 'stateShortName' => $stateShortName, 'allStates' => $allStates, 'allWebLinks' => $allWebLinks,
            'PresDetails' => $PresDetails, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails,
            'startMonthName' => $startMonthName, 'chDocuments' => $chDocuments, 'submitted' => $submitted
        ];

    }

    /**
     * View the President Profile View
     */
    public function showChapterView(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

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

        $eoyStatus = Admin::first();
        $boardreport_yes = ($eoyStatus->eoy_boardreport == 1);
        $financialreport_yes = ($eoyStatus->eoy_financialreport == 1);

        $data = ['chDetails' => $chDetails,'chFinancialReport' => $chFinancialReport, 'stateShortName' => $stateShortName, 'allStates' => $allStates, 'allWebLinks' => $allWebLinks,
            'PresDetails' => $PresDetails, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails,
            'startMonthName' => $startMonthName, 'thisMonth' => $month, 'due_date' => $due_date, 'user_type' => $user_type,
            'boardreport_yes' => $boardreport_yes, 'financialreport_yes' => $financialreport_yes];

        return view('boards.president')->with($data);

    }

     /**
     * View the Chapter Re-Reg Payment Info Report View
     */
    public function showChapterReregistrationView(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
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
            'thisMonth' => $month, 'due_date' => $due_date, 'user_type' => $user_type
        ];

        return view('boards.payment')->with($data);
    }

    /**
     * View the Chapter Board Info Report View
     */
    public function showChapterBoardInfoView(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $baseQuery = $this->getChapterDetails($id);
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
            'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'PresDetails' => $PresDetails, 'chDetails' => $chDetails, 'user_type' => $user_type,
            'allWebLinks' => $allWebLinks
        ];

        return view('boards.boardinfo')->with($data);
    }

    /**
     * View the Chapter Financial Report View
     */
    public function showChapterFinancialView(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;
        $loggedInName = $user->first_name.' '.$user->last_name;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        $submitted = $baseQuery['submitted'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $resources = Resources::with('categoryName')->get();

        $data = ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'submitted' => $submitted, 'chDetails' => $chDetails, 'user_type' => $user_type,
             'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName
        ];

        return view('boards.financial')->with($data);
    }

}
