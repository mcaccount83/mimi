<?php

namespace App\Http\Controllers;

use App\Models\Chapters;
use App\Models\FinancialReport;
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
            $userName = $user['first_name'].' '.$user['last_name'];
            $userEmail = $user['email'];
            $borDetails = $user->outgoing;

            $chapterId = $borDetails['chapter_id'];
            $chapterDetails = Chapters::find($chapterId);
            $request->session()->put('chapterid', $chapterId);

            $loggedInName = $borDetails->first_name.' '.$borDetails->last_name;
            $financial_report_array = FinancialReport::find($chapterId);

            $chapterDetails = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.financial_report_received as financial_report_received', 'st.state_short_name as state', 'chapters.conference as conf', 'chapters.primary_coordinator_id as pcid')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.is_active', '=', '1')
                ->where('chapters.id', '=', $chapterId)
                ->get();

            $resources = DB::table('resources')
                ->select('resources.*',
                    DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'),
                    DB::raw('CASE
                    WHEN category = 1 THEN "BYLAWS"
                    WHEN category = 2 THEN "FACT SHEETS"
                    WHEN category = 3 THEN "COPY READY MATERIAL"
                    WHEN category = 4 THEN "IDEAS AND INSPIRATION"
                    WHEN category = 5 THEN "CHAPTER RESOURCES"
                    WHEN category = 6 THEN "SAMPLE CHPATER FILES"
                    WHEN category = 7 THEN "END OF YEAR"
                    ELSE "Unknown"
                END as priority_word'))
                ->leftJoin('coordinators as cd', 'resources.updated_id', '=', 'cd.id')
                ->orderBy('name')
                ->get();

            $submitted = $chapterDetails[0]->financial_report_received;

            $data = ['financial_report_array' => $financial_report_array, 'submitted' => $submitted, 'loggedInName' => $loggedInName, 'chapterDetails' => $chapterDetails, 'user_type' => $user_type,
                'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources];

            return view('boards.financial')->with($data);

        } else {
            Auth::logout(); // logout non-user
            $request->session()->flush();

            return redirect()->to('/login');
        }
    }
}
