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

        /**
         * Coordinator Login with dashboard view
         */
        if ($user_type == 'coordinator') {
            //Get Coordinators Details
            $corDetails = User::find($request->user()->id)->CoordinatorDetails;
            $corId = $corDetails['coordinator_id'];
            $corConfId = $corDetails['conference_id'];

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

        /**
         * Board Login with Chapter profile Screen
         */
        if ($user_type == 'board') {
            $borDetails = User::find($request->user()->id)->BoardDetails;
            $borPositionId = $borDetails['board_position_id'];
            $isActive = $borDetails['is_active'];
            $chapterId = $borDetails['chapter_id'];
            $chapterDetails = Chapter::find($chapterId);
            $request->session()->put('chapterid', $chapterId);

            $stateArr = DB::table('state')
                ->select('state.*')
                ->orderBy('id')
                ->get();

            $chapterState = DB::table('state')
                ->select('state_short_name')
                ->where('id', '=', $chapterDetails->state)
                ->get();
            $chapterState = $chapterState[0]->state_short_name;

            $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN',
                '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
            $currentMonthCode = $chapterDetails->start_month_id;
            $currentMonthAbbreviation = isset($foundedMonth[$currentMonthCode]) ? $foundedMonth[$currentMonthCode] : '';

            $boardPosition = ['1' => 'President', '2' => 'AVP', '3' => 'MVP', '4' => 'Treasurer', '5' => 'Secretary'];
            $boardPositionCode = $borPositionId;
            $boardPositionAbbreviation = isset($boardPosition[$boardPositionCode]) ? $boardPosition[$boardPositionCode] : '';

            $year = date('Y');
            $month = date('m');

            $next_renewal_year = $chapterDetails['next_renewal_year'];
            $start_month = $chapterDetails['start_month_id'];
            $late_month = $start_month + 1;

            $due_date = Carbon::create($next_renewal_year, $start_month, 1);
            $late_date = Carbon::create($next_renewal_year, $late_month, 1);

            // Convert $start_month to words
            // $start_monthInWords = strftime('%B', strtotime("2000-$start_month-01"));
            $start_monthInWords = Carbon::createFromFormat('m', $start_month)->format('F');

            // Determine the range start and end months correctly
            $monthRangeStart = $start_month;
            $monthRangeEnd = $start_month - 1;

            // Adjust range for January
            if ($start_month == 1) {
                $monthRangeStart = 12;
            } else {
                $monthRangeEnd = $start_month - 1;
            }

            // Adjust range for January
            if ($month == 1) {
                $monthRangeEnd = 12;
            }

            // Create Carbon instances for start and end dates
            $rangeStartDate = Carbon::create($year, $monthRangeStart, 1);
            $rangeEndDate = Carbon::create($year, $monthRangeEnd, 1)->endOfMonth();

            // Format the dates as words
            $rangeStartDateFormatted = $rangeStartDate->format('F jS');
            $rangeEndDateFormatted = $rangeEndDate->format('F jS');

            if ($borPositionId == 1 && $isActive == 1) {
                $chapterList = DB::table('chapters as ch')
                    ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip',
                        'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id', 'bd.password as pswd')
                    ->leftJoin('board_details as bd', 'ch.id', '=', 'bd.chapter_id')
                    ->where('ch.is_active', '=', '1')
                    ->where('ch.id', '=', $chapterId)
                    ->where('bd.board_position_id', '=', '1')
                            //->orderBy('bd.board_position_id','ASC')
                    ->get();

                $AVPDetails = DB::table('board_details as bd')
                    ->select('bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id', 'bd.street_address as avp_addr',
                        'bd.city as avp_city', 'bd.zip as avp_zip', 'bd.phone as avp_phone', 'bd.state as avp_state', 'bd.user_id as user_id')
                    ->where('bd.chapter_id', '=', $chapterId)
                    ->where('bd.board_position_id', '=', '2')
                    ->get();
                if (count($AVPDetails) == 0) {
                    $AVPDetails[0] = ['avp_fname' => '', 'avp_lname' => '', 'avp_email' => '', 'avp_addr' => '', 'avp_city' => '', 'avp_zip' => '', 'avp_phone' => '',
                        'avp_state' => '', 'user_id' => ''];
                    $AVPDetails = json_decode(json_encode($AVPDetails));
                }

                $MVPDetails = DB::table('board_details as bd')
                    ->select('bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id', 'bd.street_address as mvp_addr',
                        'bd.city as mvp_city', 'bd.zip as mvp_zip', 'bd.phone as mvp_phone', 'bd.state as mvp_state', 'bd.user_id as user_id')
                    ->where('bd.chapter_id', '=', $chapterId)
                    ->where('bd.board_position_id', '=', '3')
                    ->get();
                if (count($MVPDetails) == 0) {
                    $MVPDetails[0] = ['mvp_fname' => '', 'mvp_lname' => '', 'mvp_email' => '', 'mvp_addr' => '', 'mvp_city' => '', 'mvp_zip' => '', 'mvp_phone' => '',
                        'mvp_state' => '', 'user_id' => ''];
                    $MVPDetails = json_decode(json_encode($MVPDetails));
                }

                $TRSDetails = DB::table('board_details as bd')
                    ->select('bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id', 'bd.street_address as trs_addr',
                        'bd.city as trs_city', 'bd.zip as trs_zip', 'bd.phone as trs_phone', 'bd.state as trs_state', 'bd.user_id as user_id')
                    ->where('bd.chapter_id', '=', $chapterId)
                    ->where('bd.board_position_id', '=', '4')
                    ->get();
                if (count($TRSDetails) == 0) {
                    $TRSDetails[0] = ['trs_fname' => '', 'trs_lname' => '', 'trs_email' => '', 'trs_addr' => '', 'trs_city' => '', 'trs_zip' => '', 'trs_phone' => '',
                        'trs_state' => '', 'user_id' => ''];
                    $TRSDetails = json_decode(json_encode($TRSDetails));
                }

                $SECDetails = DB::table('board_details as bd')
                    ->select('bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.board_position_id', 'bd.street_address as sec_addr',
                        'bd.city as sec_city', 'bd.zip as sec_zip', 'bd.phone as sec_phone', 'bd.state as sec_state', 'bd.user_id as user_id')
                    ->where('bd.chapter_id', '=', $chapterId)
                    ->where('bd.board_position_id', '=', '5')
                    ->get();
                if (count($SECDetails) == 0) {
                    $SECDetails[0] = ['sec_fname' => '', 'sec_lname' => '', 'sec_email' => '', 'sec_addr' => '', 'sec_city' => '', 'sec_zip' => '', 'sec_phone' => '',
                        'sec_state' => '', 'user_id' => ''];
                    $SECDetails = json_decode(json_encode($SECDetails));
                }
                $data = ['chapterState' => $chapterState, 'stateArr' => $stateArr, 'boardPositionAbbreviation' => $boardPositionAbbreviation, 'currentMonthAbbreviation' => $currentMonthAbbreviation,
                    'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'chapterList' => $chapterList,
                    'startMonth' => $start_monthInWords, 'thisMonth' => $month, 'due_date' => $due_date, 'late_date' => $late_date];

                return view('boards.president')->with($data);
            } elseif ($borPositionId != 1 && $isActive == 1) {
                $data = ['chapterState' => $chapterState, 'chapterDetails' => $chapterDetails, 'boardPositionAbbreviation' => $boardPositionAbbreviation, 'currentMonthAbbreviation' => $currentMonthAbbreviation,
                    'stateArr' => $stateArr, 'borPositionId' => $borPositionId, 'borDetails' => $borDetails,
                    'startMonth' => $start_monthInWords, 'thisMonth' => $month, 'due_date' => $due_date, 'late_date' => $late_date];

                return view('boards.members')->with($data);
            } else {
                Auth::logout(); // logout user
                $request->session()->flush();

                return redirect()->to('/login');
            }
        }

        /**
         * Outgoing Board Login with Fiancial Report Only
         */
        if ($user_type == 'outgoing') {
            //  $borDetails = User::find($request->user()->id)->OutgoingBoardMember;

            $user = User::with('OutgoingDetails')->find($request->user()->id);
            $borDetails = $user->OutgoingDetails;

            $isActive = $borDetails['is_active'];
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

            $data = ['financial_report_array' => $financial_report_array, 'submitted' => $submitted, 'loggedInName' => $loggedInName, 'chapterDetails' => $chapterDetails, 'user_type' => $user_type];

            return view('boards.financial')->with($data);

        } else {
            Auth::logout(); // logout user
            $request->session()->flush();

            return redirect()->to('/login');
        }

    }
}
