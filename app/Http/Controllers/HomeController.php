<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\FinancialReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        //$this->middleware('preventBackHistory');
        $this->middleware('auth');
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
            Auth::logout();
            $request->session()->flush();

            return redirect()->to('/login');
        }

        if ($user_type == 'coordinator') {
            //Get Coordinators Details
            $corDetails = $request->user()->Coordinators;
            $corId = $corDetails->id;
            $corConfId = $corDetails->conference_id;

            $corlayerId = $corDetails['layer_id'];
            $sqlLayerId = 'crt.layer'.$corlayerId;
            $positionId = $corDetails['position_id'];
            $secPositionId = $corDetails['sec_position_id'];
            $request->session()->put('positionid', $positionId);
            $request->session()->put('secpositionid', $secPositionId);
            $request->session()->put('corconfid', $corConfId);

            if ($positionId == 25) {
                //Get Coordinator Reporting Tree
                $reportIdList = DB::table('coordinator_reporting_tree as crt')
                    ->select('crt.id')
                    ->where('crt.layer1', '=', '6')
                    ->get();

            } else {
                //Get Coordinator Reporting Tree
                $reportIdList = DB::table('coordinator_reporting_tree as crt')
                    ->select('crt.id')
                    ->where($sqlLayerId, '=', $corId)
                    ->get();

            }
            $inQryStr = '';
            foreach ($reportIdList as $key => $val) {
                $inQryStr .= $val->id.',';
            }
            $inQryStr = rtrim($inQryStr, ',');
            $inQryArr = explode(',', $inQryStr);

            return redirect()->to('coordinator/dashboard');
        }

        if ($user_type == 'board') {
            //Get Board Details
            $borDetails = $request->user()->BoardDetails;
            $borPositionId = $borDetails['board_position_id'];

            if ($borPositionId == 1) {
                return redirect()->to('board/president');
            } else {
                return redirect()->to('board/member');
            }
        }

        if ($user_type == 'outgoing') {
            //Get Outgoing Board Details
            $user = User::with('OutgoingDetails')->find($request->user()->id);
            $userName = $user['first_name'].' '.$user['last_name'];
            $userEmail = $user['email'];
            $borDetails = $user->OutgoingDetails;

            $chapterId = $borDetails['chapter_id'];
            $chapterDetails = Chapter::find($chapterId);
            $request->session()->put('chapterid', $chapterId);

            $loggedInName = $borDetails->first_name.' '.$borDetails->last_name;
            $financial_report_array = FinancialReport::find($chapterId);

            $chapterDetails = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.financial_report_received as financial_report_received', 'st.state_short_name as state', 'chapters.conference as conf', 'chapters.primary_coordinator_id as pcid')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.is_active', '=', '1')
                ->where('chapters.id', '=', $chapterId)
                ->get();

            $submitted = $chapterDetails[0]->financial_report_received;

            $data = ['financial_report_array' => $financial_report_array, 'submitted' => $submitted, 'loggedInName' => $loggedInName, 'chapterDetails' => $chapterDetails, 'user_type' => $user_type,
                'userName' => $userName, 'userEmail' => $userEmail];

            return view('boards.financial')->with($data);

        } else {
            Auth::logout(); // logout user
            $request->session()->flush();

            return redirect()->to('/login');
        }

    }
}
