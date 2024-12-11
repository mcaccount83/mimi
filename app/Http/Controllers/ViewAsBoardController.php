<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\FinancialReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
     * View the President Profile View
     */
    public function showChapterView(Request $request, $id): View
    {
        $user = $request->user();
        $user_type = $user->user_type;

        $financial_report_array = FinancialReport::find($id);

        $admin = DB::table('admin')
            ->select('admin.*',
                DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'), )
            ->leftJoin('coordinators as cd', 'admin.updated_id', '=', 'cd.id')
            ->orderByDesc('admin.id') // Assuming 'id' represents the order of insertion
            ->first();

        $eoy_boardreport = $admin->eoy_boardreport;
        $eoy_financialreport = $admin->eoy_financialreport;
        $boardreport_yes = ($eoy_boardreport == 1);
        $financialreport_yes = ($eoy_financialreport == 1);

        $chapterDetails = Chapter::find($id);
        $request->session()->put('chapterid', $id);

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

        $webStatusArr = ['0' => 'Website Not Linked', '1' => 'Website Linked', '2' => 'Add Link Requested', '3' => 'Do Not Link'];

        $boardPosition = ['1' => 'President', '2' => 'AVP', '3' => 'MVP', '4' => 'Treasurer', '5' => 'Secretary'];
        $boardPositionCode = 1;
        $boardPositionAbbreviation = isset($boardPosition[$boardPositionCode]) ? $boardPosition[$boardPositionCode] : '';

        $year = date('Y');
        $month = date('m');

        $next_renewal_year = $chapterDetails['next_renewal_year'];
        $start_month = $chapterDetails['start_month_id'];
        $late_month = $start_month + 1;

        $due_date = Carbon::create($next_renewal_year, $start_month, 1);
        $late_date = Carbon::create($next_renewal_year, $late_month, 1);

        // Convert $start_month to words
        $start_monthInWords = Carbon::createFromFormat('m', $start_month)->format('F');

        // Determine the range start and end months correctly
        $monthRangeStart = $start_month;
        $monthRangeEnd = $start_month - 1;

        // Adjust range for January
        if ($start_month == 1) {
            $monthRangeStart = 1;
            $monthRangeEnd = 12;
        }

        // Create Carbon instances for start and end dates
        $rangeStartDate = Carbon::create($year, $monthRangeStart, 1);
        $rangeEndDate = Carbon::create($year, $monthRangeEnd, 1)->endOfMonth();

        // Format the dates as words
        $rangeStartDateFormatted = $rangeStartDate->format('F jS');
        $rangeEndDateFormatted = $rangeEndDate->format('F jS');

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip',
                'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();

        $AVPDetails = DB::table('boards as bd')
            ->select('bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id', 'bd.street_address as avp_addr',
                'bd.city as avp_city', 'bd.zip as avp_zip', 'bd.phone as avp_phone', 'bd.state as avp_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '2')
            ->get();
        if (count($AVPDetails) == 0) {
            $AVPDetails[0] = ['avp_fname' => '', 'avp_lname' => '', 'avp_email' => '', 'avp_addr' => '', 'avp_city' => '', 'avp_zip' => '', 'avp_phone' => '',
                'avp_state' => '', 'user_id' => ''];
            $AVPDetails = json_decode(json_encode($AVPDetails));
        }

        $MVPDetails = DB::table('boards as bd')
            ->select('bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id', 'bd.street_address as mvp_addr',
                'bd.city as mvp_city', 'bd.zip as mvp_zip', 'bd.phone as mvp_phone', 'bd.state as mvp_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '3')
            ->get();
        if (count($MVPDetails) == 0) {
            $MVPDetails[0] = ['mvp_fname' => '', 'mvp_lname' => '', 'mvp_email' => '', 'mvp_addr' => '', 'mvp_city' => '', 'mvp_zip' => '', 'mvp_phone' => '',
                'mvp_state' => '', 'user_id' => ''];
            $MVPDetails = json_decode(json_encode($MVPDetails));
        }

        $TRSDetails = DB::table('boards as bd')
            ->select('bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id', 'bd.street_address as trs_addr',
                'bd.city as trs_city', 'bd.zip as trs_zip', 'bd.phone as trs_phone', 'bd.state as trs_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '4')
            ->get();
        if (count($TRSDetails) == 0) {
            $TRSDetails[0] = ['trs_fname' => '', 'trs_lname' => '', 'trs_email' => '', 'trs_addr' => '', 'trs_city' => '', 'trs_zip' => '', 'trs_phone' => '',
                'trs_state' => '', 'user_id' => ''];
            $TRSDetails = json_decode(json_encode($TRSDetails));
        }

        $SECDetails = DB::table('boards as bd')
            ->select('bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.board_position_id', 'bd.street_address as sec_addr',
                'bd.city as sec_city', 'bd.zip as sec_zip', 'bd.phone as sec_phone', 'bd.state as sec_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '5')
            ->get();
        if (count($SECDetails) == 0) {
            $SECDetails[0] = ['sec_fname' => '', 'sec_lname' => '', 'sec_email' => '', 'sec_addr' => '', 'sec_city' => '', 'sec_zip' => '', 'sec_phone' => '',
                'sec_state' => '', 'user_id' => ''];
            $SECDetails = json_decode(json_encode($SECDetails));
        }
        $data = ['financial_report_array' => $financial_report_array, 'chapterState' => $chapterState, 'stateArr' => $stateArr, 'boardPositionAbbreviation' => $boardPositionAbbreviation, 'currentMonthAbbreviation' => $currentMonthAbbreviation,
            'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'chapterList' => $chapterList,
            'startMonth' => $start_monthInWords, 'thisMonth' => $month, 'due_date' => $due_date, 'late_date' => $late_date, 'user_type' => $user_type,
            'webStatusArr' => $webStatusArr, 'boardreport_yes' => $boardreport_yes, 'financialreport_yes' => $financialreport_yes];

        return view('boards.president')->with($data);

    }

    /**
     * View the Chapter Financial Report View
     */
    public function showChapterFinancialView(Request $request, $id): View
    {
        $user = $request->user();
        $user_type = $user->user_type;

        $financial_report_array = FinancialReport::find($id);
        $userName = $financial_report_array['completed_name'];
        $userEmail = $financial_report_array['completed_email'];

        $request->session()->put('chapterid', $id);

        $chapterDetails = DB::table('chapters')
            ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.financial_report_received as financial_report_received', 'st.state_short_name as state', 'chapters.conference as conf', 'chapters.primary_coordinator_id as pcid')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('chapters.id', '=', $id)
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
        $data = ['financial_report_array' => $financial_report_array, 'submitted' => $submitted, 'chapterDetails' => $chapterDetails, 'user_type' => $user_type,
            'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources];

        DB::commit();

        return view('boards.financial')->with($data);
    }

    /**
     * View the Chapter Board Info Report View
     */
    public function showChapterBoardInfoView(Request $request, $id): View
    {
        $user = $request->user();
        $user_type = $user->user_type;

        $financial_report_array = FinancialReport::find($id);

        $chapterDetails = Chapter::find($id);
        $request->session()->put('chapterid', $id);

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
        $currentMonth = $chapterDetails->start_month_id;

        $webStatusArr = ['0' => 'Website Not Linked', '1' => 'Website Linked', '2' => 'Add Link Requested', '3' => 'Do Not Link'];

        $boardPosition = ['1' => 'President', '2' => 'AVP', '3' => 'MVP', '4' => 'Treasurer', '5' => 'Secretary'];
        $boardPositionCode = 1;
        $boardPositionAbbreviation = isset($boardPosition[$boardPositionCode]) ? $boardPosition[$boardPositionCode] : '';

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address',
                'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();

        $PREDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.board_position_id',
                'bd.street_address as pre_addr', 'bd.city as pre_city', 'bd.zip as pre_zip', 'bd.phone as pre_phone', 'bd.state as pre_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        if (count($PREDetails) == 0) {
            $PREDetails[0] = ['pre_fname' => '', 'pre_lname' => '', 'pre_email' => '', 'pre_addr' => '', 'pre_city' => '', 'pre_zip' => '', 'pre_phone' => '',
                'pre_state' => '', 'ibd_id' => ''];
            $PREDetails = json_decode(json_encode($PREDetails));
        }

        $AVPDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id', 'bd.street_address as avp_addr',
                'bd.city as avp_city', 'bd.zip as avp_zip', 'bd.phone as avp_phone', 'bd.state as avp_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '2')
            ->get();
        if (count($AVPDetails) == 0) {
            $AVPDetails[0] = ['avp_fname' => '', 'avp_lname' => '', 'avp_email' => '', 'avp_addr' => '', 'avp_city' => '', 'avp_zip' => '', 'avp_phone' => '',
                'avp_state' => '', 'ibd_id' => ''];
            $AVPDetails = json_decode(json_encode($AVPDetails));
        }

        $MVPDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id', 'bd.street_address as mvp_addr',
                'bd.city as mvp_city', 'bd.zip as mvp_zip', 'bd.phone as mvp_phone', 'bd.state as mvp_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '3')
            ->get();
        if (count($MVPDetails) == 0) {
            $MVPDetails[0] = ['mvp_fname' => '', 'mvp_lname' => '', 'mvp_email' => '', 'mvp_addr' => '', 'mvp_city' => '', 'mvp_zip' => '', 'mvp_phone' => '',
                'mvp_state' => '', 'ibd_id' => ''];
            $MVPDetails = json_decode(json_encode($MVPDetails));
        }

        $TRSDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id', 'bd.street_address as trs_addr',
                'bd.city as trs_city', 'bd.zip as trs_zip', 'bd.phone as trs_phone', 'bd.state as trs_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '4')
            ->get();
        if (count($TRSDetails) == 0) {
            $TRSDetails[0] = ['trs_fname' => '', 'trs_lname' => '', 'trs_email' => '', 'trs_addr' => '', 'trs_city' => '', 'trs_zip' => '', 'trs_phone' => '',
                'trs_state' => '', 'ibd_id' => ''];
            $TRSDetails = json_decode(json_encode($TRSDetails));
        }

        $SECDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.board_position_id', 'bd.street_address as sec_addr',
                'bd.city as sec_city', 'bd.zip as sec_zip', 'bd.phone as sec_phone', 'bd.state as sec_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '5')
            ->get();
        if (count($SECDetails) == 0) {
            $SECDetails[0] = ['sec_fname' => '', 'sec_lname' => '', 'sec_email' => '', 'sec_addr' => '', 'sec_city' => '', 'sec_zip' => '', 'sec_phone' => '',
                'sec_state' => '', 'ibd_id' => ''];
            $SECDetails = json_decode(json_encode($SECDetails));
        }

        $data = ['chapterState' => $chapterState, 'currentMonth' => $currentMonth, 'foundedMonth' => $foundedMonth, 'stateArr' => $stateArr, 'SECDetails' => $SECDetails,
            'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'PREDetails' => $PREDetails, 'chapterList' => $chapterList, 'user_type' => $user_type,
            'webStatusArr' => $webStatusArr];

        DB::commit();

        return view('boards.boardinfo')->with($data);
    }

    /**
     * View the Chapter Board Info Report View
     */
    public function showChapterReregistrationView(Request $request, $id): View
    {
        $user = $request->user();
        $user_type = $user->user_type;

        $chapterDetails = Chapter::find($id);
        $request->session()->put('chapterid', $id);

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
        $boardPositionCode = 1;
        $boardPositionAbbreviation = isset($boardPosition[$boardPositionCode]) ? $boardPosition[$boardPositionCode] : '';

        $year = date('Y');
        $month = date('m');

        $next_renewal_year = $chapterDetails->next_renewal_year;
        $start_month = $chapterDetails->start_month_id;
        $late_month = $start_month + 1;

        $due_date = Carbon::create($next_renewal_year, $start_month, 1);
        $late_date = Carbon::create($next_renewal_year, $late_month, 1);

        // Convert $start_month to words
        $start_monthInWords = Carbon::createFromFormat('m', $start_month)->format('F');

        // Determine the range start and end months correctly
        $monthRangeStart = $start_month;
        $monthRangeEnd = $start_month - 1;

        // Adjust range for January
        if ($start_month == 1) {
            $monthRangeStart = 1;
            $monthRangeEnd = 12;
        }

        // Create Carbon instances for start and end dates
        $rangeStartDate = Carbon::create($year, $monthRangeStart, 1);
        $rangeEndDate = Carbon::create($year, $monthRangeEnd, 1)->endOfMonth();

        // Format the dates as words
        $rangeStartDateFormatted = $rangeStartDate->format('F jS');
        $rangeEndDateFormatted = $rangeEndDate->format('F jS');

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address',
                'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();

        $data = ['chapterState' => $chapterState, 'stateArr' => $stateArr, 'chapterList' => $chapterList, 'boardPositionAbbreviation' => $boardPositionAbbreviation, 'renewyear' => $next_renewal_year,
            'currentMonthAbbreviation' => $currentMonthAbbreviation, 'startMonth' => $start_monthInWords, 'endRange' => $rangeEndDateFormatted, 'startRange' => $rangeStartDateFormatted,
            'thisMonth' => $month, 'due_date' => $due_date, 'late_date' => $late_date, 'user_type' => $user_type];

        return view('boards.payment')->with($data);
    }
}
