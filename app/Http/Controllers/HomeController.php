<?php

namespace App\Http\Controllers;

use App\Models\Chapters;
use App\Models\FinancialReport;
use App\Models\FinancialReportAwards;
use App\Models\Resources;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->middleware('auth')->except('logout');
        $this->userController = $userController;
    }

    /**
     * Home page for Coordinators & Board Members - logic for login redirect
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $user_type = $user->user_type;
        $userStatus = $user->is_active;
        if ($userStatus != 1) {
            Auth::logout();  // logout inactive user
            $request->session()->flush();

            return redirect()->to('/login');
        }

        if ($user_type == 'coordinator') {
            //Send to Coordinator Dashboard
            return redirect()->to('coordviewprofile');
        }

        if ($user_type == 'board') {
            //Send to President or Board Profile Screen
            $borDetails = $request->user()->board;
            $borPositionId = $borDetails['board_position_id'];

            if ($borPositionId == 1) {
                return redirect()->to('board/president');
            } else {
                return redirect()->to('board/member');
            }
        }

        if ($user_type == 'outgoing') {
            //Send to Financial Report without Menus
            $user = User::with('outgoing')->find($request->user()->id);
            $userName = $user->first_name.' '.$user->last_name;
            $userEmail = $user->email;
            $loggedInName = $user->first_name.' '.$user->last_name;

            $bdDetails = $user->outgoing;
            $chId = $bdDetails->chapter_id;
            $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'startMonth', 'webLink', 'status', 'documents', 'financialReport', 'boards',])->find($chId);

            $stateShortName = $chDetails->state->state_short_name;
            $chDocuments = $chDetails->documents;
            $submitted = $chDocuments->financial_report_received;
            $chFinancialReport = $chDetails->financialReport;
            $awards = $chDetails->financialReport;
            $allAwards = FinancialReportAwards::all();  // Full List for Dropdown Menu

            $resources = Resources::with('categoryName')->get();

            $data = ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'submitted' => $submitted, 'chDetails' => $chDetails, 'user_type' => $user_type,
                'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName,
                'awards' => $awards, 'allAwards' => $allAwards,
            ];

            return view('boards.financial')->with($data);

        } else {
            Auth::logout(); // logout non-user
            $request->session()->flush();

            return redirect()->to('/login');
        }
    }
}
