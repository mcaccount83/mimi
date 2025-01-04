<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckCurrentPasswordBoardRequest;
use App\Http\Requests\UpdatePasswordBoardRequest;
use App\Mail\ChapersUpdateListAdmin;
use App\Mail\ChapersUpdateListAdminMember;
use App\Mail\ChapersUpdatePrimaryCoorMember;
use App\Mail\ChaptersUpdatePrimaryCoorPresident;
use App\Mail\EOYElectionReportSubmitted;
use App\Mail\EOYElectionReportThankYou;
use App\Mail\EOYFinancialReportThankYou;
use App\Mail\EOYFinancialSubmitted;
use App\Mail\WebsiteAddNoticeAdmin;
use App\Mail\WebsiteReviewNotice;
use App\Models\Admin;
use App\Models\Boards;
use App\Models\Chapters;
use App\Models\Website;
use App\Models\State;
use App\Models\Documents;
use App\Models\FinancialReport;
use App\Models\FinancialReportAwards;
use App\Models\FolderRecord;
use App\Models\User;
use App\Models\Resources;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PDFController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BoardController extends Controller
{
    protected $userController;
    protected $pdfController;

    public function __construct(UserController $userController, PDFController $pdfController)
    {
        $this->middleware('auth')->except('logout');
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndBoard::class);
        $this->userController = $userController;
        $this->pdfController = $pdfController;  // Note the consistent naming
    }

    /**
     * Reset Password
     */
    public function updatePassword(UpdatePasswordBoardRequest $request): JsonResponse
    {
        $user = $request->user();

        // Ensure the current password is correct
        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 400);
        }

        // Update the user's password
        $user->password = Hash::make($request->new_password);
        $user->remember_token = null; // Reset the remember token
        $user->save();

        return response()->json(['message' => 'Password updated successfully']);
    }

    /**
     * Verify Current Passwor for Reset
     */
    public function checkCurrentPassword(CheckCurrentPasswordBoardRequest $request): JsonResponse
    {
        $user = $request->user();
        $isValid = Hash::check($request->current_password, $user->password);

        return response()->json(['isValid' => $isValid]);
    }

     /**
     * Active Chapter Details Base Query
     */
    public function getChapterDetails($id)
    {
        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'startMonth', 'webLink', 'state', 'documents', 'financialReport', 'president', 'boards', 'reportReviewer'])->find($id);
        $chId = $chDetails->id;
        $chIsActive = $chDetails->is_active;
        $stateShortName = $chDetails->state->state_short_name;
        $regionLongName = $chDetails->region->long_name;
        $conferenceDescription = $chDetails->conference->conference_description;
        $chConfId = $chDetails->conference_id;
        $chRegId = $chDetails->region_id;
        $chPcId = $chDetails->primary_coordinator_id;
        $startMonthName = $chDetails->startMonth->month_long_name;
        $websiteLink = $chDetails->webLink->link_status ?? null;

        $allWebLinks = Website::all(); // Full List for Dropdown Menu
        $allStates = State::all();  // Full List for Dropdown Menu
        $allAwards = FinancialReportAwards::all();  // Full List for Dropdown Menu

        $chDocuments = $chDetails->documents;
        $submitted = $chDocuments->financial_report_received;
        $reviewComplete = $chDetails->documents->review_complete;
        $reviewerEmail = $chDetails->reportReviewer?->email;
        $chFinancialReport = $chDetails->financialReport;

        $awards = $chDetails->financialReport;

        $boards = $chDetails->boards()->with(['stateName', 'position'])->get();
        $bdDetails = $boards->groupBy('board_position_id');
        $defaultBoardMember = (object)['id' => null, 'first_name' => '', 'last_name' => '', 'email' => '', 'street_address' => '', 'city' => '', 'zip' => '', 'phone' => '', 'state' => '', 'user_id' => ''];

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

         // Load Conference Coordinators for Sending Email
         $ccEmailData = $this->userController->loadConferenceCoord($chPcId);
         $cc_id = $ccEmailData['cc_id'];
         $emailCC = $ccEmailData['cc_email'];

        return ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'cc_id' => $cc_id,
            'chFinancialReport' => $chFinancialReport, 'startMonthName' => $startMonthName, 'chDocuments' => $chDocuments, 'submitted' => $submitted,
            'PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
            'allWebLinks' => $allWebLinks, 'allStates' => $allStates, 'emailListChap' => $emailListChap, 'emailListCoord' => $emailListCoord, 'emailCC' => $emailCC,
            'reviewerEmail' => $reviewerEmail, 'awards' => $awards, 'allAwards' => $allAwards
        ];

    }

    /**
     * View Board Details President Login
     */
    public function showPresident(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;
        $bdPositionid = $bdDetails->board_position_id;
        $bdIsActive = $bdDetails->is_active;
        $id = $bdDetails->chapter_id;

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
     * View Board Details President Login
     */
    public function showMember(Request $request): View
    {
        $user = $request->user();
        $user_type = $user->user_type;
        $borDetails = $request->user()->board;
        $borPositionId = $borDetails['board_position_id'];
        $isActive = $borDetails['is_active'];
        $chapterId = $borDetails['chapter_id'];
        $chapterDetails = Chapters::find($chapterId);
        $request->session()->put('chapterid', $chapterId);

        $financial_report_array = FinancialReport::find($chapterId);

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

        $AVPDetails = DB::table('boards as bd')
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

        $MVPDetails = DB::table('boards as bd')
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

        $TRSDetails = DB::table('boards as bd')
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

        $SECDetails = DB::table('boards as bd')
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

        $data = ['financial_report_array' => $financial_report_array, 'chapterState' => $chapterState, 'chapterDetails' => $chapterDetails, 'boardPositionAbbreviation' => $boardPositionAbbreviation, 'currentMonthAbbreviation' => $currentMonthAbbreviation,
            'stateArr' => $stateArr, 'borPositionId' => $borPositionId, 'borDetails' => $borDetails,
            'startMonth' => $start_monthInWords, 'thisMonth' => $month, 'due_date' => $due_date, 'late_date' => $late_date, 'user_type' => $user_type,
            'webStatusArr' => $webStatusArr, 'boardreport_yes' => $boardreport_yes, 'financialreport_yes' => $financialreport_yes];

        return view('boards.member')->with($data);
    }

    /**
     * Update Board Details President Login
     */
    public function updatePresident(Request $request, $id): RedirectResponse
    {
        $chapterId = $id;
        $user = $request->user();
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;

        $chapterInfoPre = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'st.state_short_name as statename',
                'chapters.conference_id as conference', 'chapters.primary_coordinator_id as cor_id', 'bd.first_name as ch_pre_fname', 'bd.last_name as ch_pre_lname',
                'bd.email as ch_pre_email', 'cd.email as cor_email')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
            // ->where('chapters.is_Active', '=', '1')
            ->where('chapters.id', $id)
            ->orderByDesc('chapters.id')
            ->get();

        $chState = $chapterInfoPre[0]->statename;
        $chConfId = $chapterInfoPre[0]->conference;
        $chPCId = $chapterInfoPre[0]->cor_id;
        $pc_email = $chapterInfoPre[0]->cor_email;

        $ch_webstatus = $request->input('ch_webstatus') ?: $request->input('ch_hid_webstatus');
        if (empty(trim($ch_webstatus))) {
            $ch_webstatus = 0; // Set it to 0 if it's blank
        }

        $website = $request->input('ch_website');
        // Ensure it starts with "http://" or "https://"
        if (! str_starts_with($website, 'http://') && ! str_starts_with($website, 'https://')) {
            $website = 'http://'.$website;
        }

        $presInfoPre = DB::table('chapters')
            ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'bd.street_address as street',
                'bd.city as city', 'bd.zip as zip', 'st.state_short_name as state')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'bd.state', '=', 'st.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.id', $id)
            ->get();

        $AVPInfoPre = DB::table('chapters')
            ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '2')
            ->where('chapters.id', $id)
            ->get();

        $MVPInfoPre = DB::table('chapters')
            ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '3')
            ->where('chapters.id', $id)
            ->get();

        $tresInfoPre = DB::table('chapters')
            ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '4')
            ->where('chapters.id', $id)
            ->get();

        $secInfoPre = DB::table('chapters')
            ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '5')
            ->where('chapters.id', $id)
            ->get();

        $chapter = Chapters::find($chapterId);
        DB::beginTransaction();
        try {
            $chapter->website_url = $website;
            $chapter->website_status = $request->input('ch_webstatus');
            $chapter->email = $request->input('ch_email');
            $chapter->inquiries_contact = $request->input('ch_inqemailcontact');
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->po_box = $request->input('ch_pobox');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');

            $chapter->save();

            //President Info
            if ($request->input('ch_pre_fname') != '' && $request->input('ch_pre_lname') != '' && $request->input('ch_pre_email') != '') {
                $PREDetails = DB::table('boards')
                    ->select('id as board_id', 'user_id')
                    ->where('chapter_id', '=', $chapterId)
                    ->where('board_position_id', '=', '1')
                    ->get();
                if (count($PREDetails) != 0) {
                    $userId = $PREDetails[0]->user_id;
                    $boardId = $PREDetails[0]->board_id;

                    $user = User::find($userId);
                    $user->first_name = $request->input('ch_pre_fname');
                    $user->last_name = $request->input('ch_pre_lname');
                    $user->email = $request->input('ch_pre_email');
                    $user->updated_at = now();
                    $user->save();

                    $board = Boards::find($boardId);
                    $board->first_name = $request->input('ch_pre_fname');
                    $board->last_name = $request->input('ch_pre_lname');
                    $board->email = $request->input('ch_pre_email');
                    $board->street_address = $request->input('ch_pre_street');
                    $board->city = $request->input('ch_pre_city');
                    $board->state = $request->input('ch_pre_state');
                    $board->zip = $request->input('ch_pre_zip');
                    $board->country = 'USA';
                    $board->phone = $request->input('ch_pre_phone');
                    $board->last_updated_by = $lastUpdatedBy;
                    $board->last_updated_date = now();
                    $board->save();
                }
            }
            //AVP Info
            $AVPDetails = DB::table('boards')
                ->select('id as board_id', 'user_id')
                ->where('chapter_id', '=', $chapterId)
                ->where('board_position_id', '=', '2')
                ->get();
            if (count($AVPDetails) != 0) {
                $userId = $AVPDetails[0]->user_id;
                $boardId = $AVPDetails[0]->board_id;
                if ($request->input('AVPVacant') == 'on') {
                    //Delete Details of Board memebers
                    DB::table('boards')
                        ->where('id', $boardId)
                        ->delete();
                    //Delete Details of Board memebers from users table
                    DB::table('users')
                        ->where('id', $userId)
                        ->delete();
                } else {
                    $user = User::find($userId);
                    $user->first_name = $request->input('ch_avp_fname');
                    $user->last_name = $request->input('ch_avp_lname');
                    $user->email = $request->input('ch_avp_email');
                    $user->updated_at = now();
                    $user->save();

                    $board = Boards::find($boardId);
                    $board->first_name = $request->input('ch_avp_fname');
                    $board->last_name = $request->input('ch_avp_lname');
                    $board->email = $request->input('ch_avp_email');
                    $board->street_address = $request->input('ch_avp_street');
                    $board->city = $request->input('ch_avp_city');
                    $board->state = $request->input('ch_avp_state');
                    $board->zip = $request->input('ch_avp_zip');
                    $board->country = 'USA';
                    $board->phone = $request->input('ch_avp_phone');
                    $board->last_updated_by = $lastUpdatedBy;
                    $board->last_updated_date = now();
                    $board->save();
                }
            } else {
                if ($request->input('AVPVacant') != 'on') {
                    $userId = DB::table('users')->insertGetId(
                        ['first_name' => $request->input('ch_avp_fname'),
                            'last_name' => $request->input('ch_avp_lname'),
                            'email' => $request->input('ch_avp_email'),
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1]
                    );

                    $boardId = DB::table('boards')->insertGetId(
                        ['user_id' => $userId,
                            'first_name' => $request->input('ch_avp_fname'),
                            'last_name' => $request->input('ch_avp_lname'),
                            'email' => $request->input('ch_avp_email'),
                            'board_position_id' => 2,
                            'chapter_id' => $chapterId,
                            'street_address' => $request->input('ch_avp_street'),
                            'city' => $request->input('ch_avp_city'),
                            'state' => $request->input('ch_avp_state'),
                            'zip' => $request->input('ch_avp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_avp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                            'is_active' => 1]
                    );
                }
            }
            //MVP Info
            $MVPDetails = DB::table('boards')
                ->select('id as board_id', 'user_id')
                ->where('chapter_id', '=', $chapterId)
                ->where('board_position_id', '=', '3')
                ->get();
            if (count($MVPDetails) != 0) {
                $userId = $MVPDetails[0]->user_id;
                $boardId = $MVPDetails[0]->board_id;
                if ($request->input('MVPVacant') == 'on') {
                    //Delete Details of Board memebers
                    DB::table('boards')
                        ->where('id', $boardId)
                        ->delete();
                    //Delete Details of Board memebers from users table
                    DB::table('users')
                        ->where('id', $userId)
                        ->delete();
                } else {
                    $user = User::find($userId);
                    $user->first_name = $request->input('ch_mvp_fname');
                    $user->last_name = $request->input('ch_mvp_lname');
                    $user->email = $request->input('ch_mvp_email');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    $board = Boards::find($boardId);
                    $board->first_name = $request->input('ch_mvp_fname');
                    $board->last_name = $request->input('ch_mvp_lname');
                    $board->email = $request->input('ch_mvp_email');
                    $board->street_address = $request->input('ch_mvp_street');
                    $board->city = $request->input('ch_mvp_city');
                    $board->state = $request->input('ch_mvp_state');
                    $board->zip = $request->input('ch_mvp_zip');
                    $board->country = 'USA';
                    $board->phone = $request->input('ch_mvp_phone');
                    $board->last_updated_by = $lastUpdatedBy;
                    $board->last_updated_date = now();
                    $board->save();
                }
            } else {
                if ($request->input('MVPVacant') != 'on') {
                    $userId = DB::table('users')->insertGetId(
                        ['first_name' => $request->input('ch_mvp_fname'),
                            'last_name' => $request->input('ch_mvp_lname'),
                            'email' => $request->input('ch_mvp_email'),
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1]
                    );

                    $boardId = DB::table('boards')->insertGetId(
                        ['user_id' => $userId,
                            'first_name' => $request->input('ch_mvp_fname'),
                            'last_name' => $request->input('ch_mvp_lname'),
                            'email' => $request->input('ch_mvp_email'),
                            'board_position_id' => 3,
                            'chapter_id' => $chapterId,
                            'street_address' => $request->input('ch_mvp_street'),
                            'city' => $request->input('ch_mvp_city'),
                            'state' => $request->input('ch_mvp_state'),
                            'zip' => $request->input('ch_mvp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_mvp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                            'is_active' => 1]
                    );
                }
            }
            //TRS Info
            $TRSDetails = DB::table('boards')
                ->select('id as board_id', 'user_id')
                ->where('chapter_id', '=', $chapterId)
                ->where('board_position_id', '=', '4')
                ->get();
            if (count($TRSDetails) != 0) {
                $userId = $TRSDetails[0]->user_id;
                $boardId = $TRSDetails[0]->board_id;
                if ($request->input('TreasVacant') == 'on') {
                    //Delete Details of Board memebers
                    DB::table('boards')
                        ->where('id', $boardId)
                        ->delete();
                    //Delete Details of Board memebers from users table
                    DB::table('users')
                        ->where('id', $userId)
                        ->delete();
                } else {
                    $user = User::find($userId);
                    $user->first_name = $request->input('ch_trs_fname');
                    $user->last_name = $request->input('ch_trs_lname');
                    $user->email = $request->input('ch_trs_email');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    $board = Boards::find($boardId);
                    $board->first_name = $request->input('ch_trs_fname');
                    $board->last_name = $request->input('ch_trs_lname');
                    $board->email = $request->input('ch_trs_email');
                    $board->street_address = $request->input('ch_trs_street');
                    $board->city = $request->input('ch_trs_city');
                    $board->state = $request->input('ch_trs_state');
                    $board->zip = $request->input('ch_trs_zip');
                    $board->country = 'USA';
                    $board->phone = $request->input('ch_trs_phone');
                    $board->last_updated_by = $lastUpdatedBy;
                    $board->last_updated_date = now();
                    $board->save();
                }
            } else {
                if ($request->input('TreasVacant') != 'on') {
                    $userId = DB::table('users')->insertGetId(
                        ['first_name' => $request->input('ch_trs_fname'),
                            'last_name' => $request->input('ch_trs_lname'),
                            'email' => $request->input('ch_trs_email'),
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1]
                    );

                    $boardId = DB::table('boards')->insertGetId(
                        ['user_id' => $userId,
                            'first_name' => $request->input('ch_trs_fname'),
                            'last_name' => $request->input('ch_trs_lname'),
                            'email' => $request->input('ch_trs_email'),
                            'board_position_id' => 4,
                            'chapter_id' => $chapterId,
                            'street_address' => $request->input('ch_trs_street'),
                            'city' => $request->input('ch_trs_city'),
                            'state' => $request->input('ch_trs_state'),
                            'zip' => $request->input('ch_trs_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_trs_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                            'is_active' => 1]
                    );
                }
            }
            //SEC Info
            $SECDetails = DB::table('boards')
                ->select('id as board_id', 'user_id')
                ->where('chapter_id', '=', $chapterId)
                ->where('board_position_id', '=', '5')
                ->get();
            if (count($SECDetails) != 0) {
                $userId = $SECDetails[0]->user_id;
                $boardId = $SECDetails[0]->board_id;
                if ($request->input('SecVacant') == 'on') {
                    //Delete Details of Board memebers
                    DB::table('boards')
                        ->where('id', $boardId)
                        ->delete();
                    //Delete Details of Board memebers from users table
                    DB::table('users')
                        ->where('id', $userId)
                        ->delete();
                } else {
                    $user = User::find($userId);
                    $user->first_name = $request->input('ch_sec_fname');
                    $user->last_name = $request->input('ch_sec_lname');
                    $user->email = $request->input('ch_sec_email');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    $board = Boards::find($boardId);
                    $board->first_name = $request->input('ch_sec_fname');
                    $board->last_name = $request->input('ch_sec_lname');
                    $board->email = $request->input('ch_sec_email');
                    $board->street_address = $request->input('ch_sec_street');
                    $board->city = $request->input('ch_sec_city');
                    $board->state = $request->input('ch_sec_state');
                    $board->zip = $request->input('ch_sec_zip');
                    $board->country = 'USA';
                    $board->phone = $request->input('ch_sec_phone');
                    $board->last_updated_by = $lastUpdatedBy;
                    $board->last_updated_date = now();
                    $board->save();
                }
            } else {
                if ($request->input('SecVacant') != 'on') {
                    $userId = DB::table('users')->insertGetId(
                        ['first_name' => $request->input('ch_sec_fname'),
                            'last_name' => $request->input('ch_sec_lname'),
                            'email' => $request->input('ch_sec_email'),
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1]
                    );

                    $boardId = DB::table('boards')->insertGetId(
                        ['user_id' => $userId,
                            'first_name' => $request->input('ch_sec_fname'),
                            'last_name' => $request->input('ch_sec_lname'),
                            'email' => $request->input('ch_sec_email'),
                            'board_position_id' => 5,
                            'chapter_id' => $chapterId,
                            'street_address' => $request->input('ch_sec_street'),
                            'city' => $request->input('ch_sec_city'),
                            'state' => $request->input('ch_sec_state'),
                            'zip' => $request->input('ch_sec_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_sec_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                            'is_active' => 1]
                    );
                }
            }

            //Website Notifications//
            $chId = $chapter['id'];
            $chPcid = $chPCId;
            $chConf = $chConfId;

            $emailData = $this->userController->loadConferenceCoord($chPcid);
            $to_CCemail = $emailData['cc_email'];

            if ($request->input('ch_webstatus') != $request->input('ch_hid_webstatus')) {

                $mailData = [
                    'chapter_name' => $request->input('ch_name'),
                    'chapter_state' => $request->input('ch_state'),
                    'ch_website_url' => $website,
                ];

                if ($request->input('ch_webstatus') == 1) {
                    Mail::to($to_CCemail)
                        ->queue(new WebsiteAddNoticeAdmin($mailData));
                }

                if ($request->input('ch_webstatus') == 2) {
                    Mail::to($to_CCemail)
                        ->queue(new WebsiteReviewNotice($mailData));
                }
            }

            //Update Chapter MailData//
            $chapterInfoUpd = DB::table('chapters')
                ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'st.state_short_name as state',
                    'chapters.conference_id as conference', 'chapters.primary_coordinator_id as cor_id')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('chapters.id', $chapterId)
                ->orderByDesc('chapters.id')
                ->get();

            $presInfoUpd = DB::table('chapters')
                ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'bd.street_address as street',
                    'bd.city as city', 'bd.zip as zip', 'st.state_short_name as state')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'bd.state', '=', 'st.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.id', $chapterId)
                ->orderByDesc('chapters.id')
                ->get();

            $AVPInfoUpd = DB::table('chapters')
                ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '2')
                ->where('chapters.id', $chapterId)
                ->get();

            $MVPInfoUpd = DB::table('chapters')
                ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '3')
                ->where('chapters.id', $chapterId)
                ->get();

            $tresInfoUpd = DB::table('chapters')
                ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '4')
                ->where('chapters.id', $chapterId)
                ->get();

            $secInfoUpd = DB::table('chapters')
                ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '5')
                ->where('chapters.id', $chapterId)
                ->get();

            $mailDataPres = [
                'chapter_name' => $request->input('ch_name'),
                'chapter_state' => $request->input('ch_state'),
                'chapterNameUpd' => $chapterInfoUpd[0]->name,
                'chapterStateUpd' => $chapterInfoUpd[0]->state,
                'cor_fnameUpd' => $chapterInfoUpd[0]->cor_f_name,
                'cor_lnameUpd' => $chapterInfoUpd[0]->cor_l_name,
                'updated_byUpd' => $chapterInfoUpd[0]->last_updated_date,
                'chapfnameUpd' => $presInfoUpd[0]->bor_f_name,
                'chaplnameUpd' => $presInfoUpd[0]->bor_l_name,
                'chapteremailUpd' => $presInfoUpd[0]->bor_email,
                'streetUpd' => $presInfoUpd[0]->street,
                'cityUpd' => $presInfoUpd[0]->city,
                'stateUpd' => $presInfoUpd[0]->state,
                'zipUpd' => $presInfoUpd[0]->zip,
                'phoneUpd' => $presInfoUpd[0]->phone,
                'inConUpd' => $chapterInfoUpd[0]->inquiries_contact,
                'chapemailUpd' => $chapterInfoUpd[0]->email,
                'poBoxUpd' => $chapterInfoUpd[0]->po_box,
                'webUrlUpd' => $chapterInfoUpd[0]->website_url,
                'webStatusUpd' => $chapterInfoUpd[0]->website_status,
                'egroupUpd' => $chapterInfoUpd[0]->egroup,
                'chapfnamePre' => $presInfoPre[0]->bor_f_name,
                'chaplnamePre' => $presInfoPre[0]->bor_l_name,
                'chapteremailPre' => $presInfoPre[0]->bor_email,
                'streetPre' => $presInfoPre[0]->street,
                'cityPre' => $presInfoPre[0]->city,
                'statePre' => $presInfoPre[0]->state,
                'zipPre' => $presInfoPre[0]->zip,
                'phonePre' => $presInfoPre[0]->phone,
                'inConPre' => $chapterInfoPre[0]->inquiries_contact,
                'chapemailPre' => $chapterInfoPre[0]->email,
                'poBoxPre' => $chapterInfoPre[0]->po_box,
                'webUrlPre' => $chapterInfoPre[0]->website_url,
                'webStatusPre' => $chapterInfoPre[0]->website_status,
                'egroupPre' => $chapterInfoPre[0]->egroup,
            ];
            $mailData = array_merge($mailDataPres);
            if ($AVPInfoUpd !== null && count($AVPInfoUpd) > 0) {
                $mailDataAvp = ['avpfnameUpd' => $AVPInfoUpd[0]->bor_f_name,
                    'avplnameUpd' => $AVPInfoUpd[0]->bor_l_name,
                    'avpemailUpd' => $AVPInfoUpd[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDataAvp);
            } else {
                $mailDataAvp = ['avpfnameUpd' => '',
                    'avplnameUpd' => '',
                    'avpemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDataAvp);
            }
            if ($MVPInfoUpd !== null && count($MVPInfoUpd) > 0) {
                $mailDataMvp = ['mvpfnameUpd' => $MVPInfoUpd[0]->bor_f_name,
                    'mvplnameUpd' => $MVPInfoUpd[0]->bor_l_name,
                    'mvpemailUpd' => $MVPInfoUpd[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDataMvp);
            } else {
                $mailDataMvp = ['mvpfnameUpd' => '',
                    'mvplnameUpd' => '',
                    'mvpemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDataMvp);
            }
            if ($tresInfoUpd !== null && count($tresInfoUpd) > 0) {
                $mailDatatres = ['tresfnameUpd' => $tresInfoUpd[0]->bor_f_name,
                    'treslnameUpd' => $tresInfoUpd[0]->bor_l_name,
                    'tresemailUpd' => $tresInfoUpd[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDatatres);
            } else {
                $mailDatatres = ['tresfnameUpd' => '',
                    'treslnameUpd' => '',
                    'tresemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDatatres);
            }
            if ($secInfoUpd !== null && count($secInfoUpd) > 0) {
                $mailDataSec = ['secfnameUpd' => $secInfoUpd[0]->bor_f_name,
                    'seclnameUpd' => $secInfoUpd[0]->bor_l_name,
                    'secemailUpd' => $secInfoUpd[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDataSec);
            } else {
                $mailDataSec = ['secfnameUpd' => '',
                    'seclnameUpd' => '',
                    'secemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDataSec);
            }
            if ($AVPInfoPre !== null && count($AVPInfoPre) > 0) {
                $mailDataAvpp = ['avpfnamePre' => $AVPInfoPre[0]->bor_f_name,
                    'avplnamePre' => $AVPInfoPre[0]->bor_l_name,
                    'avpemailPre' => $AVPInfoPre[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDataAvpp);
            } else {
                $mailDataAvpp = ['avpfnamePre' => '',
                    'avplnamePre' => '',
                    'avpemailPre' => '', ];
                $mailData = array_merge($mailData, $mailDataAvpp);
            }
            if ($MVPInfoPre !== null && count($MVPInfoPre) > 0) {
                $mailDataMvpp = ['mvpfnamePre' => $MVPInfoPre[0]->bor_f_name,
                    'mvplnamePre' => $MVPInfoPre[0]->bor_l_name,
                    'mvpemailPre' => $MVPInfoPre[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDataMvpp);
            } else {
                $mailDataMvpp = ['mvpfnamePre' => '',
                    'mvplnamePre' => '',
                    'mvpemailPre' => '', ];
                $mailData = array_merge($mailData, $mailDataMvpp);
            }
            if ($tresInfoPre !== null && count($tresInfoPre) > 0) {
                $mailDatatresp = ['tresfnamePre' => $tresInfoPre[0]->bor_f_name,
                    'treslnamePre' => $tresInfoPre[0]->bor_l_name,
                    'tresemailPre' => $tresInfoPre[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDatatresp);
            } else {
                $mailDatatresp = ['tresfnamePre' => '',
                    'treslnamePre' => '',
                    'tresemailPre' => '', ];
                $mailData = array_merge($mailData, $mailDatatresp);
            }
            if ($secInfoPre !== null && count($secInfoPre) > 0) {
                $mailDataSecp = ['secfnamePre' => $secInfoPre[0]->bor_f_name,
                    'seclnamePre' => $secInfoPre[0]->bor_l_name,
                    'secemailPre' => $secInfoPre[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDataSecp);
            } else {
                $mailDataSecp = ['secfnamePre' => '',
                    'seclnamePre' => '',
                    'secemailPre' => '', ];
                $mailData = array_merge($mailData, $mailDataSecp);
            }

            //Primary Coordinator Notification//
            $to_email = $chapterInfoUpd[0]->cor_email;

            if ($presInfoUpd[0]->bor_email != $presInfoPre[0]->bor_email || $presInfoUpd[0]->street != $presInfoPre[0]->street || $presInfoUpd[0]->city != $presInfoPre[0]->city ||
                $presInfoUpd[0]->state != $presInfoPre[0]->state || $presInfoUpd[0]->bor_f_name != $presInfoPre[0]->bor_f_name || $presInfoUpd[0]->bor_l_name != $presInfoPre[0]->bor_l_name ||
                    $presInfoUpd[0]->zip != $presInfoPre[0]->zip || $presInfoUpd[0]->phone != $presInfoPre[0]->phone || $chapterInfoUpd[0]->inquiries_contact != $chapterInfoPre[0]->inquiries_contact ||
                    $chapterInfoUpd[0]->email != $chapterInfoPre[0]->email || $chapterInfoUpd[0]->po_box != $chapterInfoPre[0]->po_box || $chapterInfoUpd[0]->website_url != $chapterInfoPre[0]->website_url ||
                    $chapterInfoUpd[0]->website_status != $chapterInfoPre[0]->website_status || $chapterInfoUpd[0]->egroup != $chapterInfoPre[0]->egroup ||
                    $mailDataAvpp['avpfnamePre'] != $mailDataAvp['avpfnameUpd'] || $mailDataAvpp['avplnamePre'] != $mailDataAvp['avplnameUpd'] || $mailDataAvpp['avpemailPre'] != $mailDataAvp['avpemailUpd'] ||
                    $mailDataMvpp['mvpfnamePre'] != $mailDataMvp['mvpfnameUpd'] || $mailDataMvpp['mvplnamePre'] != $mailDataMvp['mvplnameUpd'] || $mailDataMvpp['mvpemailPre'] != $mailDataMvp['mvpemailUpd'] ||
                    $mailDatatresp['tresfnamePre'] != $mailDatatres['tresfnameUpd'] || $mailDatatresp['treslnamePre'] != $mailDatatres['treslnameUpd'] || $mailDatatresp['tresemailPre'] != $mailDatatres['tresemailUpd'] ||
                    $mailDataSecp['secfnamePre'] != $mailDataSec['secfnameUpd'] || $mailDataSecp['seclnamePre'] != $mailDataSec['seclnameUpd'] || $mailDataSecp['secemailPre'] != $mailDataSec['secemailUpd']) {

                Mail::to($to_email)
                    ->queue(new ChaptersUpdatePrimaryCoorPresident($mailData));
            }

            //List Admin Notification//
            $to_email2 = 'listadmin@momsclub.org';

            if ($chapterInfoUpd[0]->email != $chapterInfoPre[0]->email || $presInfoUpd[0]->bor_email != $presInfoPre[0]->bor_email ||
            $mailDataAvpp['avpemailPre'] != $mailDataAvp['avpemailUpd'] || $mailDataMvpp['mvpemailPre'] != $mailDataMvp['mvpemailUpd'] ||
            $mailDatatresp['tresemailPre'] != $mailDatatres['tresemailUpd'] || $mailDataSecp['secemailPre'] != $mailDataSec['secemailUpd']) {
                Mail::to($to_email2)
                    ->queue(new ChapersUpdateListAdmin($mailData));
            }

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            echo $e->getMessage();
            exit();
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->to('/home')->with('fail', 'Something went wrong, Please try again');
        }

        return redirect()->back()->with('success', 'Chapter has successfully updated');
    }

    /**
     * Update Board Details Board Member Login
     */
    public function updateMember(Request $request, $id): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $chapterId = $id;
            $posId = $request->input('bor_positionid');

            // Fetch User Details
            $user = $request->user();
            $lastUpdatedBy = $user->first_name.' '.$user->last_name;

            // Fetch Board Details
            $boardDetails = DB::table('boards')
                ->select('boards.id as board_id', 'boards.user_id', 'boards.first_name as bor_fname', 'boards.last_name as bor_lname', 'boards.email as bor_email', 'bp.position as bor_position')
                ->leftJoin('board_position as bp', 'boards.board_position_id', '=', 'bp.id')
                ->where('boards.chapter_id', '=', $chapterId)
                ->where('boards.board_position_id', '=', $posId)
                ->get();

            // Fetch Chapter Info
            $chapterInfo = DB::table('chapters')
                ->select('chapters.id as chapter_id', 'chapters.name', 'chapters.state_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'st.state_short_name as state')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
                ->where('chapters.id', '=', $chapterId)
                ->get();

            $ch_webstatus = $request->input('ch_webstatus') ?: $request->input('ch_hid_webstatus');
            if (empty(trim($ch_webstatus))) {
                $ch_webstatus = 0; // Set it to 0 if it's blank
            }

            $website = $request->input('ch_website');
            // Ensure it starts with "http://" or "https://"
            if (! str_starts_with($website, 'http://') && ! str_starts_with($website, 'https://')) {
                $website = 'http://'.$website;
            }

            if (count($boardDetails) != 0) {
                $userId = $boardDetails[0]->user_id;
                $boardId = $boardDetails[0]->board_id;

                // Update User Details
                $user = User::find($userId);
                $user->first_name = $request->input('bor_fname');
                $user->last_name = $request->input('bor_lname');
                $user->email = $request->input('bor_email');
                $user->updated_at = now();
                $user->save();

                // Update Board Details
                $board = Boards::find($boardId);
                $board->first_name = $request->input('bor_fname');
                $board->last_name = $request->input('bor_lname');
                $board->email = $request->input('bor_email');
                $board->phone = $request->input('bor_phone');
                $board->street_address = $request->input('bor_addr');
                $board->city = $request->input('bor_city');
                $board->state = $request->input('bor_state');
                $board->zip = $request->input('bor_zip');
                $board->country = 'USA';
                $board->last_updated_by = $lastUpdatedBy;
                $board->last_updated_date = now();
                $board->save();

                // Update Chapter Details
                $chapter = Chapters::find($chapterId);
                $chapter->website_url = $website;
                $chapter->website_status = $request->input('ch_webstatus');
                $chapter->email = $request->input('ch_email');
                $chapter->inquiries_contact = $request->input('ch_inqemailcontact');
                $chapter->egroup = $request->input('ch_onlinediss');
                $chapter->social1 = $request->input('ch_social1');
                $chapter->social2 = $request->input('ch_social2');
                $chapter->social3 = $request->input('ch_social3');
                $chapter->po_box = $request->input('ch_pobox');
                $chapter->last_updated_by = $lastUpdatedBy;
                $chapter->last_updated_date = date('Y-m-d H:i:s');

                $chapter->save();
            }

            // Fetch Updated Board Details
            $boardDetailsUpd = DB::table('boards')
                ->select('boards.id as board_id', 'boards.user_id', 'boards.first_name as bor_fname', 'boards.last_name as bor_lname', 'boards.email as bor_email')
                ->where('boards.chapter_id', '=', $chapterId)
                ->where('boards.board_position_id', '=', $posId)
                ->get();

            $mailData = [
                'cor_fname' => $chapterInfo[0]->cor_fname,
                'chapter_name' => $chapterInfo[0]->name,
                'chapter_state' => $chapterInfo[0]->state,
                'borposition' => $boardDetails[0]->bor_position,
                'borfnameUpd' => $boardDetailsUpd[0]->bor_fname,
                'borlnameUpd' => $boardDetailsUpd[0]->bor_lname,
                'boremailUpd' => $boardDetailsUpd[0]->bor_email,
                'borfname' => $boardDetails[0]->bor_fname,
                'borlname' => $boardDetails[0]->bor_lname,
                'boremail' => $boardDetails[0]->bor_email,
            ];

            // PC Admin Notification
            $to_email = $chapterInfo[0]->cor_email;
            if ($boardDetailsUpd[0]->bor_email != $boardDetails[0]->bor_email || $boardDetailsUpd[0]->bor_fname != $boardDetails[0]->bor_fname ||
                $boardDetailsUpd[0]->bor_lname != $boardDetails[0]->bor_lname) {

                Mail::to($to_email)
                    ->queue(new ChapersUpdatePrimaryCoorMember($mailData));
            }

            // List Admin Notification
            $to_email2 = 'listadmin@momsclub.org';
            if ($boardDetailsUpd[0]->bor_email != $boardDetails[0]->bor_email) {
                Mail::to($to_email2)
                    ->queue(new ChapersUpdateListAdminMember($mailData));
            }

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->to('/home')->with('fail', 'Something went wrong, Please try again');
        }

        return redirect()->back()->with('success', 'Chapter has successfully updated');
    }

    /**
     * Show Re-Registrstion Payment Form All Board Members
     */
    public function showReregistrationPaymentForm(Request $request)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;
        $id = $bdDetails->chapter_id;

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
     * Show M2M Donation Form All Board Members
     */
    public function showM2MDonationForm(Request $request)
    {
        $user = $request->user();

        $borDetails = $user->board;
        // Check if BoardDetails is not found for the user
        if (! $borDetails) {
            return to_route('home');
        }

        $borPositionId = $borDetails['board_position_id'];
        $chapterId = $borDetails['chapter_id'];
        $isActive = $borDetails['is_active'];

        $chapterDetails = Chapters::find($chapterId);
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();

        $chapterState = DB::table('state')
            ->select('state_short_name')
            ->where('id', '=', $chapterDetails->state)
            ->get();
        $chapterState = $chapterState[0]->state_short_name;

        $boardPosition = ['1' => 'President', '2' => 'AVP', '3' => 'MVP', '4' => 'Treasurer', '5' => 'Secretary'];
        $boardPositionCode = $borPositionId;
        $boardPositionAbbreviation = isset($boardPosition[$boardPositionCode]) ? $boardPosition[$boardPositionCode] : '';

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address',
                'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '1')
            ->get();

        $data = ['chapterState' => $chapterState, 'stateArr' => $stateArr, 'chapterList' => $chapterList, 'boardPositionAbbreviation' => $boardPositionAbbreviation];

        return view('boards.donation')->with($data);
    }

    /**
     * Show Chater Resources
     */
    public function showResources(Request $request)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;
        $id = $bdDetails->chapter_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];

        $resources = Resources::with('categoryName')->get();

        $data = ['stateShortName' => $stateShortName, 'chDetails' => $chDetails, 'resources' => $resources];

        return view('boards.resources')->with($data);
    }

    /**
     * Show EOY BoardInfo All Board Members
     */
    public function showBoardInfo(Request $request)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;
        $id = $bdDetails->chapter_id;

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
     * Update EOY BoardInfo All Board Members
     */
    public function createBoardInfo(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];

        $stateShortName = $baseQuery['stateShortName'];
        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];
        $emailCC = $baseQuery['emailCC'];

        $boundaryStatus = $request->input('BoundaryStatus');
        $issue_note = $request->input('BoundaryIssue');
        //Boundary Issues Correct 0 | Not Correct 1
        if ($boundaryStatus == 0) {
            $issue_note = '';
        }

        $ch_webstatus = $request->input('ch_webstatus') ?: $request->input('ch_hid_webstatus');
        if (empty(trim($ch_webstatus))) {
            $ch_webstatus = 0; // Set it to 0 if it's blank
        }

        $website = $request->input('ch_website');
        // Ensure it starts with "http://" or "https://"
        if (! str_starts_with($website, 'http://') && ! str_starts_with($website, 'https://')) {
            $website = 'http://'.$website;
        }

        $chapter = Chapters::find($id);
        $documents = Documents::find($id);

        DB::beginTransaction();
        try {
            $chDetails->inquiries_contact = $request->input('InquiriesContact');
            $chDetails->boundary_issues = $request->input('BoundaryStatus');
            $chDetails->boundary_issue_notes = $issue_note;
            $chDetails->website_url = $website;
            $chDetails->website_status = $ch_webstatus;
            $chDetails->egroup = $request->input('ch_onlinediss');
            $chDetails->social1 = $request->input('ch_social1');
            $chDetails->social2 = $request->input('ch_social2');
            $chDetails->social3 = $request->input('ch_social3');
            $chDetails->last_updated_by = $lastUpdatedBy;
            $chDetails->last_updated_date = date('Y-m-d H:i:s');
            $chDetails->save();

            $documents->new_board_submitted = 1;
            $documents->save();

            //President Info
            if ($request->input('ch_pre_fname') != '' && $request->input('ch_pre_lname') != '' && $request->input('ch_pre_email') != '') {
                $PREDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $id)
                    ->where('board_position_id', '=', '1')
                    ->get();
                $id = $request->input('presID');
                if (count($PREDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $bdId)
                        ->update(['first_name' => $request->input('ch_pre_fname'),
                            'last_name' => $request->input('ch_pre_lname'),
                            'email' => $request->input('ch_pre_email'),
                            'street_address' => $request->input('ch_pre_street'),
                            'city' => $request->input('ch_pre_city'),
                            'state' => $request->input('ch_pre_state'),
                            'zip' => $request->input('ch_pre_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_pre_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                } else {
                    $board = DB::table('incoming_board_member')->insert(
                        ['first_name' => $request->input('ch_pre_fname'),
                            'last_name' => $request->input('ch_pre_lname'),
                            'email' => $request->input('ch_pre_email'),
                            'board_position_id' => 1,
                            'chapter_id' => $id,
                            'street_address' => $request->input('ch_pre_street'),
                            'city' => $request->input('ch_pre_city'),
                            'state' => $request->input('ch_pre_state'),
                            'zip' => $request->input('ch_pre_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_pre_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                }

            }
            //AVP Info
            if ($request->input('AVPVacant') == 'on') {
                $bdId = $request->input('avpID');
                DB::table('incoming_board_member')
                    ->where('id', $bdId)
                    ->delete();
            }
            if ($request->input('ch_avp_fname') != '' && $request->input('ch_avp_lname') != '' && $request->input('ch_avp_email') != '') {
                $AVPDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $id)
                    ->where('board_position_id', '=', '2')
                    ->get();
                $id = $request->input('avpID');
                if (count($AVPDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $bdId)
                        ->update(['first_name' => $request->input('ch_avp_fname'),
                            'last_name' => $request->input('ch_avp_lname'),
                            'email' => $request->input('ch_avp_email'),
                            'street_address' => $request->input('ch_avp_street'),
                            'city' => $request->input('ch_avp_city'),
                            'state' => $request->input('ch_avp_state'),
                            'zip' => $request->input('ch_avp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_avp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                } else {

                    $board = DB::table('incoming_board_member')->insert(
                        [
                            'first_name' => $request->input('ch_avp_fname'),
                            'last_name' => $request->input('ch_avp_lname'),
                            'email' => $request->input('ch_avp_email'),
                            'board_position_id' => 2,
                            'chapter_id' => $id,
                            'street_address' => $request->input('ch_avp_street'),
                            'city' => $request->input('ch_avp_city'),
                            'state' => $request->input('ch_avp_state'),
                            'zip' => $request->input('ch_avp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_avp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }
            //MVP Info
            if ($request->input('MVPVacant') == 'on') {
                $bdId = $request->input('mvpID');
                DB::table('incoming_board_member')
                    ->where('id', $bdId)
                    ->delete();
            }
            if ($request->input('ch_mvp_fname') != '' && $request->input('ch_mvp_lname') != '' && $request->input('ch_mvp_email') != '') {
                $MVPDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $id)
                    ->where('board_position_id', '=', '3')
                    ->get();
                $id = $request->input('mvpID');
                if (count($MVPDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $bdId)
                        ->update(['first_name' => $request->input('ch_mvp_fname'),
                            'last_name' => $request->input('ch_mvp_lname'),
                            'email' => $request->input('ch_mvp_email'),
                            'street_address' => $request->input('ch_mvp_street'),
                            'city' => $request->input('ch_mvp_city'),
                            'state' => $request->input('ch_mvp_state'),
                            'zip' => $request->input('ch_mvp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_mvp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                } else {

                    $board = DB::table('incoming_board_member')->insert(
                        [
                            'first_name' => $request->input('ch_mvp_fname'),
                            'last_name' => $request->input('ch_mvp_lname'),
                            'email' => $request->input('ch_mvp_email'),
                            'board_position_id' => 3,
                            'chapter_id' => $id,
                            'street_address' => $request->input('ch_mvp_street'),
                            'city' => $request->input('ch_mvp_city'),
                            'state' => $request->input('ch_mvp_state'),
                            'zip' => $request->input('ch_mvp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_mvp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }
            //TRS Info
            if ($request->input('TreasVacant') == 'on') {
                $bdId = $request->input('trsID');
                DB::table('incoming_board_member')
                    ->where('id', $bdId)
                    ->delete();
            }
            if ($request->input('ch_trs_fname') != '' && $request->input('ch_trs_lname') != '' && $request->input('ch_trs_email') != '') {
                $TRSDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $id)
                    ->where('board_position_id', '=', '4')
                    ->get();
                $id = $request->input('trsID');
                if (count($TRSDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $bdId)
                        ->update(['first_name' => $request->input('ch_trs_fname'),
                            'last_name' => $request->input('ch_trs_lname'),
                            'email' => $request->input('ch_trs_email'),
                            'street_address' => $request->input('ch_trs_street'),
                            'city' => $request->input('ch_trs_city'),
                            'state' => $request->input('ch_trs_state'),
                            'zip' => $request->input('ch_trs_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_trs_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);

                } else {

                    $board = DB::table('incoming_board_member')->insert(
                        [
                            'first_name' => $request->input('ch_trs_fname'),
                            'last_name' => $request->input('ch_trs_lname'),
                            'email' => $request->input('ch_trs_email'),
                            'board_position_id' => 4,
                            'chapter_id' => $id,
                            'street_address' => $request->input('ch_trs_street'),
                            'city' => $request->input('ch_trs_city'),
                            'state' => $request->input('ch_trs_state'),
                            'zip' => $request->input('ch_trs_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_trs_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }
            //SEC Info
            if ($request->input('SecVacant') == 'on') {
                $bdId = $request->input('secID');
                DB::table('incoming_board_member')
                    ->where('id', $bdId)
                    ->delete();
            }
            if ($request->input('ch_sec_fname') != '' && $request->input('ch_sec_lname') != '' && $request->input('ch_sec_email') != '') {
                $SECDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $id)
                    ->where('board_position_id', '=', '5')
                    ->get();
                $id = $request->input('secID');
                if (count($SECDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $bdId)
                        ->update(['first_name' => $request->input('ch_sec_fname'),
                            'last_name' => $request->input('ch_sec_lname'),
                            'email' => $request->input('ch_sec_email'),
                            'street_address' => $request->input('ch_sec_street'),
                            'city' => $request->input('ch_sec_city'),
                            'state' => $request->input('ch_sec_state'),
                            'zip' => $request->input('ch_sec_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_sec_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);

                } else {
                    $board = DB::table('incoming_board_member')->insert(
                        [
                            'first_name' => $request->input('ch_sec_fname'),
                            'last_name' => $request->input('ch_sec_lname'),
                            'email' => $request->input('ch_sec_email'),
                            'board_position_id' => 5,
                            'chapter_id' => $id,
                            'street_address' => $request->input('ch_sec_street'),
                            'city' => $request->input('ch_sec_city'),
                            'state' => $request->input('ch_sec_state'),
                            'zip' => $request->input('ch_sec_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_sec_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                        ]
                    );

                }
            }

            // Send email to Conference Coordinator
            $to_email = $emailCC;

            // Send email to full Board
            $to_email2 = $emailListChap;

            $mailData = [
                'chapterid' => $id,
                'chapter_name' => $chDetails->name,
                'chapter_state' => $stateShortName,
            ];

            Mail::to($to_email)
                ->queue(new EOYElectionReportSubmitted($mailData));

            Mail::to($to_email2)
                ->queue(new EOYElectionReportThankYou($mailData));

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->back()->with('success', 'Board Info has been Submitted');
    }

    /**
     * Show EOY Financial Report All Board Members
     */
    public function showFinancialReport(Request $request, $chapterId)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;
        $userName = $user->first_name.' '.$user->last_name;
        $userEmail = $user->email;
        $loggedInName = $user->first_name.' '.$user->last_name;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;

        $id = $bdDetails->chapter_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        $submitted = $baseQuery['submitted'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $awards = $baseQuery['awards'];
        $allAwards = $baseQuery['allAwards'];

        $resources = Resources::with('categoryName')->get();

        $data = ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'submitted' => $submitted, 'chDetails' => $chDetails, 'user_type' => $user_type,
            'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName,
            'awards' => $awards, 'allAwards' => $allAwards,
        ];

        return view('boards.financial')->with($data);

    }

    /**
     * Save EOY Financial Report All Board Members
     */
    public function storeFinancialReport(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;
        $userName = $user->first_name.' '.$user->last_name;
        $userEmail = $user->email;
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;

        $id = $bdDetails->chapter_id;

        $input = $request->all();
        $farthest_step_visited = $input['FurthestStep'];
        $reportReceived = $input['submitted'];

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];
        $emailCC = $baseQuery['emailCC'];
        $cc_id = $baseQuery['cc_id'];
        $reviewerEmail = $baseQuery['reviewerEmail'];

        $roster_path = $chDocuments->roster_path;
        $irs_path = $chDocuments->irs_path;
        $statement_1_path = $chDocuments->statement_1_path;
        $statement_2_path = $chDocuments->statement_2_path;
        $financial_pdf_path = $chDocuments->financial_pdf_path;

        // CHAPTER DUES
        $changed_dues = isset($input['optChangeDues']) ? $input['optChangeDues'] : null;
        $different_dues = isset($input['optNewOldDifferent']) ? $input['optNewOldDifferent'] : null;
        $not_all_full_dues = isset($input['optNoFullDues']) ? $input['optNoFullDues'] : null;
        $total_new_members = $input['TotalNewMembers'];
        $total_renewed_members = $input['TotalRenewedMembers'];
        $dues_per_member = $input['MemberDues'];
        $total_new_members_changed_dues = $input['TotalNewMembersNewFee'];
        $total_renewed_members_changed_dues = $input['TotalRenewedMembersNewFee'];
        $dues_per_member_renewal = $input['MemberDuesRenewal'];
        $dues_per_member_new_changed = $input['NewMemberDues'];
        $dues_per_member_renewal_changed = $input['NewMemberDuesRenewal'];
        $members_who_paid_no_dues = $input['MembersNoDues'];
        $members_who_paid_partial_dues = $input['TotalPartialDuesMembers'];
        $total_partial_fees_collected = $input['PartialDuesMemberDues'];
        $total_associate_members = $input['TotalAssociateMembers'];
        $associate_member_fee = $input['AssociateMemberDues'];

        // MONTHLY MEETING EXPENSES
        $manditory_meeting_fees_paid = $input['ManditoryMeetingFeesPaid'];
        $voluntary_donations_paid = $input['VoluntaryDonationsPaid'];
        $meeting_speakers = isset($input['MeetingSpeakers']) ? $input['MeetingSpeakers'] : null;
        $meeting_speakers_array = isset($input['Speakers']) ? $input['Speakers'] : null;
        $discussion_topic_frequency = isset($input['SpeakerFrequency']) ? $input['SpeakerFrequency'] : null;
        $childrens_room_sitters = isset($input['ChildrensRoom']) ? $input['ChildrensRoom'] : null;
        $paid_baby_sitters = $input['PaidBabySitters'];

        $ChildrenRoomArray = null;
        $FieldCount = $input['ChildrensExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $ChildrenRoomArray[$i]['childrens_room_desc'] = $input['ChildrensRoomDesc'.$i] ?? null;
            $ChildrenRoomArray[$i]['childrens_room_supplies'] = $input['ChildrensRoomSupplies'.$i] ?? null;
            $ChildrenRoomArray[$i]['childrens_room_other'] = $input['ChildrensRoomOther'.$i] ?? null;
        }
        $childrens_room_expenses = base64_encode(serialize($ChildrenRoomArray));

        // SERVICE PROJECTS
        $at_least_one_service_project = isset($input['PerformServiceProject']) ? $input['PerformServiceProject'] : null;
        $at_least_one_service_project_explanation = $input['PerformServiceProjectExplanation'];
        $contributions_not_registered_charity = isset($input['ContributionsNotRegNP']) ? $input['ContributionsNotRegNP'] : null;
        $contributions_not_registered_charity_explanation = $input['ContributionsNotRegNPExplanation'];

        $ServiceProjectFields = null;
        $FieldCount = $input['ServiceProjectRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $ServiceProjectFields[$i]['service_project_desc'] = $input['ServiceProjectDesc'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_income'] = $input['ServiceProjectIncome'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_supplies'] = $input['ServiceProjectSupplies'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_charity'] = $input['ServiceProjectDonatedCharity'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_m2m'] = $input['ServiceProjectDonatedM2M'.$i] ?? null;
        }
        $service_project_array = base64_encode(serialize($ServiceProjectFields));

        // PARTIES & MEMBER BENEFITS
        $PartyExpenseFields = null;
        $FieldCount = $input['PartyExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $PartyExpenseFields[$i]['party_expense_desc'] = $input['PartyDesc'.$i] ?? null;
            $PartyExpenseFields[$i]['party_expense_income'] = $input['PartyIncome'.$i] ?? null;
            $PartyExpenseFields[$i]['party_expense_expenses'] = $input['PartyExpenses'.$i] ?? null;
        }
        $party_expense_array = base64_encode(serialize($PartyExpenseFields));

        // OFFICE & OPERATING EXPENSES
        $office_printing_costs = $input['PrintingCosts'];
        $office_postage_costs = $input['PostageCosts'];
        $office_membership_pins_cost = $input['MembershipPins'];

        $OfficeOtherArray = null;
        $FieldCount = $input['OfficeExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $OfficeOtherArray[$i]['office_other_desc'] = $input['OfficeDesc'.$i] ?? null;
            $OfficeOtherArray[$i]['office_other_expense'] = $input['OfficeExpenses'.$i] ?? null;
        }
        $office_other_expenses = base64_encode(serialize($OfficeOtherArray));

        // INTERNATIONAL EVENTS & RE-REGISTRATION
        $annual_registration_fee = $input['AnnualRegistrationFee'];
        $international_event = isset($input['InternationalEvent']) ? $input['InternationalEvent'] : null;

        $InternationalEventArray = null;
        $FieldCount = $input['InternationalEventRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $InternationalEventArray[$i]['intl_event_desc'] = $input['InternationalEventDesc'.$i] ?? null;
            $InternationalEventArray[$i]['intl_event_income'] = $input['InternationalEventIncome'.$i] ?? null;
            $InternationalEventArray[$i]['intl_event_expenses'] = $input['InternationalEventExpense'.$i] ?? null;
        }
        $international_event_array = base64_encode(serialize($InternationalEventArray));

        // DONATIONS TO CHAPTER
        $MonetaryDonation = null;
        $FieldCount = $input['MonDonationRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $MonetaryDonation[$i]['mon_donation_desc'] = $input['DonationDesc'.$i] ?? null;
            $MonetaryDonation[$i]['mon_donation_info'] = $input['DonorInfo'.$i] ?? null;
            $MonetaryDonation[$i]['mon_donation_date'] = $input['MonDonationDate'.$i] ?? null;
            $MonetaryDonation[$i]['mon_donation_amount'] = $input['DonationAmount'.$i] ?? null;
        }
        $monetary_donations_to_chapter = base64_encode(serialize($MonetaryDonation));

        $NonMonetaryDonation = null;
        $FieldCount = $input['NonMonDonationRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $NonMonetaryDonation[$i]['nonmon_donation_desc'] = $input['NonMonDonationDesc'.$i] ?? null;
            $NonMonetaryDonation[$i]['nonmon_donation_info'] = $input['NonMonDonorInfo'.$i] ?? null;
            $NonMonetaryDonation[$i]['nonmon_donation_date'] = $input['NonMonDonationDate'.$i] ?? null;
        }
        $non_monetary_donations_to_chapter = base64_encode(serialize($NonMonetaryDonation));

        // OTHER INCOME & EXPENSES
        $OtherOffice = null;
        $FieldCount = $input['OtherOfficeExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $OtherOffice[$i]['other_desc'] = $input['OtherOfficeDesc'.$i] ?? null;
            $OtherOffice[$i]['other_expenses'] = $input['OtherOfficeExpenses'.$i] ?? null;
            $OtherOffice[$i]['other_income'] = $input['OtherOfficeIncome'.$i] ?? null;
        }
        $other_income_and_expenses_array = base64_encode(serialize($OtherOffice));

        // BANK RECONCILLIATION
        $bank_statement_included = isset($input['BankStatementIncluded']) ? $input['BankStatementIncluded'] : null;
        $bank_statement_included_explanation = $input['BankStatementIncludedExplanation'];
        $wheres_the_money = $input['WheresTheMoney'];
        $amount_reserved_from_previous_year = $input['AmountReservedFromLastYear'];
        $amount_reserved_from_previous_year = str_replace(',', '', $amount_reserved_from_previous_year);
        $amount_reserved_from_previous_year = $amount_reserved_from_previous_year === '' ? null : $amount_reserved_from_previous_year;
        $bank_balance_now = $input['BankBalanceNow'];
        $bank_balance_now = str_replace(',', '', $bank_balance_now);
        $bank_balance_now = $bank_balance_now === '' ? null : $bank_balance_now;
        $BankRecArray = null;
        $FieldCount = $input['BankRecRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $BankRecArray[$i]['bank_rec_date'] = $input['BankRecDate'.$i] ?? null;
            $BankRecArray[$i]['bank_rec_check_no'] = $input['BankRecCheckNo'.$i] ?? null;
            $BankRecArray[$i]['bank_rec_desc'] = $input['BankRecDesc'.$i] ?? null;
            $BankRecArray[$i]['bank_rec_payment_amount'] = $input['BankRecPaymentAmount'.$i] ?? null;
            $BankRecArray[$i]['bank_rec_desposit_amount'] = $input['BankRecDepositAmount'.$i] ?? null;
        }
        $bank_reconciliation_array = base64_encode(serialize($BankRecArray));

        // 990 IRS FILING
        $file_irs = isset($input['FileIRS']) ? $input['FileIRS'] : null;
        $file_irs_explanation = $input['FileIRSExplanation'];

        // CHPATER QUESTIONS
        //Question 1
        $bylaws_available = isset($input['ByLawsAvailable']) ? $input['ByLawsAvailable'] : null;
        $bylaws_available_explanation = $input['ByLawsAvailableExplanation'];
        //Question 4
        $vote_all_activities = isset($input['VoteAllActivities']) ? $input['VoteAllActivities'] : null;
        $vote_all_activities_explanation = $input['VoteAllActivitiesExplanation'];
        //Question 11
        $child_outings = isset($input['ChildOutings']) ? $input['ChildOutings'] : null;
        $child_outings_explanation = $input['ChildOutingsExplanation'];
        //Question 10
        $playgroups = isset($input['Playgroups']) ? $input['Playgroups'] : null;
        $had_playgroups_explanation = $input['PlaygroupsExplanation'];
        //Question 16
        $park_day_frequency = isset($input['ParkDays']) ? $input['ParkDays'] : null;
        $park_day_frequency_explanation = $input['ParkDaysExplanation'];
        //Question 12
        $mother_outings = isset($input['MotherOutings']) ? $input['MotherOutings'] : null;
        $mother_outings_explanation = $input['MotherOutingsExplanation'];
        //Question 17
        $activity_array = isset($input['Activity']) ? $input['Activity'] : null;
        $activity_other_explanation = $input['ActivityOtherExplanation'];
        //Question 7
        $offered_merch = isset($input['OfferedMerch']) ? $input['OfferedMerch'] : null;
        $offered_merch_explanation = $input['OfferedMerchExplanation'];
        //Question 6
        $bought_merch = isset($input['BoughtMerch']) ? $input['BoughtMerch'] : null;
        $bought_merch_explanation = $input['BoughtMerchExplanation'];
        //Question 5
        $purchase_pins = isset($input['BoughtPins']) ? $input['BoughtPins'] : null;
        $purchase_pins_explanation = $input['BoughtPinsExplanation'];
        //Question 8
        $receive_compensation = isset($input['ReceiveCompensation']) ? $input['ReceiveCompensation'] : null;
        $receive_compensation_explanation = $input['ReceiveCompensationExplanation'];
        //Question 2
        $financial_benefit = isset($input['FinancialBenefit']) ? $input['FinancialBenefit'] : null;
        $financial_benefit_explanation = $input['FinancialBenefitExplanation'];
        //Question 3
        $influence_political = isset($input['InfluencePolitical']) ? $input['InfluencePolitical'] : null;
        $influence_political_explanation = $input['InfluencePoliticalExplanation'];
        //Question 20
        $sister_chapter = isset($input['SisterChapter']) ? $input['SisterChapter'] : null;
        $sister_chapter_explanation = $input['SisterChapterExplanation'];

        // AWARDS
        // $award_nominations = $input['TotalAwardNominations'];
        $ChapterAwards = null;
        $FieldCount = $input['ChapterAwardsRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $ChapterAwards[$i]['awards_type'] = $input['ChapterAwardsType'.$i] ?? null;
            $ChapterAwards[$i]['awards_desc'] = $input['ChapterAwardsDesc'.$i] ?? null;
        }
        $chapter_awards = base64_encode(serialize($ChapterAwards));

//         $awards_data = [];
// for ($i = 0; $i < $request->input('award_count'); $i++) {
//     $awards_data[] = [
//         'awards_type' => $request->input("ChaperAwardsType{$i}"), // This will now be the award ID
//         'awards_desc' => $request->input("ChaperAwardsDesc{$i}")
//     ];
// }

// $chapter_awards = base64_encode(serialize($awards_data));

        // //Award Nomination 1
        // if (isset($input['NominationType1'])) {$award_1_nomination_type = $input['NominationType1'];}
        // else {$award_1_nomination_type = null;}
        // $award_1_outstanding_follow_bylaws = isset($input['OutstandingFollowByLaws1']) ? $input['OutstandingFollowByLaws1'] : null;
        // $award_1_outstanding_well_rounded = isset($input['OutstandingWellRounded1']) ? $input['OutstandingWellRounded1'] : null;
        // $award_1_outstanding_communicated = isset($input['OutstandingCommunicated1']) ? $input['OutstandingCommunicated1'] : null;
        // $award_1_outstanding_support_international = isset($input['OutstandingSupportMomsClub1']) ? $input['OutstandingSupportMomsClub1'] : null;
        // $award_1_outstanding_project_desc = $input['AwardDesc1'];

        // //Award Nomination 2
        // if (isset($input['NominationType2'])) {$award_2_nomination_type = $input['NominationType2'];}
        // else {$award_2_nomination_type = null;}
        // $award_2_outstanding_follow_bylaws = isset($input['OutstandingFollowByLaws2']) ? $input['OutstandingFollowByLaws2'] : null;
        // $award_2_outstanding_well_rounded = isset($input['OutstandingWellRounded2']) ? $input['OutstandingWellRounded2'] : null;
        // $award_2_outstanding_communicated = isset($input['OutstandingCommunicated2']) ? $input['OutstandingCommunicated2'] : null;
        // $award_2_outstanding_support_international = isset($input['OutstandingSupportMomsClub2']) ? $input['OutstandingSupportMomsClub2'] : null;
        // $award_2_outstanding_project_desc = $input['AwardDesc2'];

        // //Award Nomination 3
        // if (isset($input['NominationType3'])) {$award_3_nomination_type = $input['NominationType3'];}
        // else {$award_3_nomination_type = null;}
        // $award_3_outstanding_follow_bylaws = isset($input['OutstandingFollowByLaws3']) ? $input['OutstandingFollowByLaws3'] : null;
        // $award_3_outstanding_well_rounded = isset($input['OutstandingWellRounded3']) ? $input['OutstandingWellRounded3'] : null;
        // $award_3_outstanding_communicated = isset($input['OutstandingCommunicated3']) ? $input['OutstandingCommunicated3'] : null;
        // $award_3_outstanding_support_international = isset($input['OutstandingSupportMomsClub3']) ? $input['OutstandingSupportMomsClub3'] : null;
        // $award_3_outstanding_project_desc = $input['AwardDesc3'];

        // //Award Nomination 4
        // if (isset($input['NominationType4'])) {$award_4_nomination_type = $input['NominationType4'];}
        // else {$award_4_nomination_type = null;}
        // $award_4_outstanding_follow_bylaws = isset($input['OutstandingFollowByLaws4']) ? $input['OutstandingFollowByLaws4'] : null;
        // $award_4_outstanding_well_rounded = isset($input['OutstandingWellRounded4']) ? $input['OutstandingWellRounded4'] : null;
        // $award_4_outstanding_communicated = isset($input['OutstandingCommunicated4']) ? $input['OutstandingCommunicated4'] : null;
        // $award_4_outstanding_support_international = isset($input['OutstandingSupportMomsClub4']) ? $input['OutstandingSupportMomsClub4'] : null;
        // $award_4_outstanding_project_desc = $input['AwardDesc4'];

        // //Award Nomination 5
        // if (isset($input['NominationType5'])) {$award_5_nomination_type = $input['NominationType5'];}
        // else {$award_5_nomination_type = null;}
        // $award_5_outstanding_follow_bylaws = isset($input['OutstandingFollowByLaws5']) ? $input['OutstandingFollowByLaws5'] : null;
        // $award_5_outstanding_well_rounded = isset($input['OutstandingWellRounded5']) ? $input['OutstandingWellRounded5'] : null;
        // $award_5_outstanding_communicated = isset($input['OutstandingCommunicated5']) ? $input['OutstandingCommunicated5'] : null;
        // $award_5_outstanding_support_international = isset($input['OutstandingSupportMomsClub5']) ? $input['OutstandingSupportMomsClub5'] : null;
        // $award_5_outstanding_project_desc = $input['AwardDesc5'];

        if (isset($input['AwardsAgree']) && $input['AwardsAgree'] == false) {$award_agree = 0;}
        elseif (isset($input['AwardsAgree'])) {$award_agree = 1;}
        else {$award_agree = null;}

        $report = FinancialReport::find($id);
        $documents = Documents::find($id);
        $chapter = Chapters::find($id);

        DB::beginTransaction();
        try {
                $report->changed_dues = $changed_dues;
                $report->different_dues = $different_dues;
                $report->not_all_full_dues = $not_all_full_dues;
                $report->total_new_members = $total_new_members;
                $report->total_renewed_members = $total_renewed_members;
                $report->dues_per_member = $dues_per_member;
                $report->total_new_members_changed_dues = $total_new_members_changed_dues;
                $report->total_renewed_members_changed_dues = $total_renewed_members_changed_dues;
                $report->dues_per_member_renewal = $dues_per_member_renewal;
                $report->dues_per_member_new_changed = $dues_per_member_new_changed;
                $report->dues_per_member_renewal_changed = $dues_per_member_renewal_changed;
                $report->members_who_paid_no_dues = $members_who_paid_no_dues;
                $report->members_who_paid_partial_dues = $members_who_paid_partial_dues;
                $report->total_partial_fees_collected = $total_partial_fees_collected;
                $report->total_associate_members = $total_associate_members;
                $report->associate_member_fee = $associate_member_fee;
                $report->manditory_meeting_fees_paid = $manditory_meeting_fees_paid;
                $report->voluntary_donations_paid = $voluntary_donations_paid;
                $report->paid_baby_sitters = $paid_baby_sitters;
                $report->childrens_room_expenses = $childrens_room_expenses;
                $report->service_project_array = $service_project_array;
                $report->party_expense_array = $party_expense_array;
                $report->office_printing_costs = $office_printing_costs;
                $report->office_postage_costs = $office_postage_costs;
                $report->office_membership_pins_cost = $office_membership_pins_cost;
                $report->office_other_expenses = $office_other_expenses;
                $report->international_event_array = $international_event_array;
                $report->annual_registration_fee = $annual_registration_fee;
                $report->monetary_donations_to_chapter = $monetary_donations_to_chapter;
                $report->non_monetary_donations_to_chapter = $non_monetary_donations_to_chapter;
                $report->other_income_and_expenses_array = $other_income_and_expenses_array;
                $report->amount_reserved_from_previous_year = $amount_reserved_from_previous_year;
                $report->bank_balance_now = $bank_balance_now;
                $report->bank_reconciliation_array = $bank_reconciliation_array;
                $report->receive_compensation = $receive_compensation;
                $report->receive_compensation_explanation = $receive_compensation_explanation;
                $report->financial_benefit = $financial_benefit;
                $report->financial_benefit_explanation = $financial_benefit_explanation;
                $report->influence_political = $influence_political;
                $report->influence_political_explanation = $influence_political_explanation;
                $report->vote_all_activities = $vote_all_activities;
                $report->vote_all_activities_explanation = $vote_all_activities_explanation;
                $report->purchase_pins = $purchase_pins;
                $report->purchase_pins_explanation = $purchase_pins_explanation;
                $report->bought_merch = $bought_merch;
                $report->bought_merch_explanation = $bought_merch_explanation;
                $report->offered_merch = $offered_merch;
                $report->offered_merch_explanation = $offered_merch_explanation;
                $report->bylaws_available = $bylaws_available;
                $report->bylaws_available_explanation = $bylaws_available_explanation;
                $report->childrens_room_sitters = $childrens_room_sitters;
                $report->playgroups = $playgroups;
                $report->had_playgroups_explanation = $had_playgroups_explanation;
                $report->child_outings = $child_outings;
                $report->child_outings_explanation = $child_outings_explanation;
                $report->mother_outings = $mother_outings;
                $report->mother_outings_explanation = $mother_outings_explanation;
                $report->meeting_speakers = $meeting_speakers;
                $report->meeting_speakers_array = $meeting_speakers_array;
                $report->discussion_topic_frequency = $discussion_topic_frequency;
                $report->park_day_frequency = $park_day_frequency;
                $report->park_day_frequency_explanation = $park_day_frequency_explanation;
                $report->activity_array = $activity_array;
                $report->activity_other_explanation = $activity_other_explanation;
                $report->contributions_not_registered_charity = $contributions_not_registered_charity;
                $report->contributions_not_registered_charity_explanation = $contributions_not_registered_charity_explanation;
                $report->at_least_one_service_project = $at_least_one_service_project;
                $report->at_least_one_service_project_explanation = $at_least_one_service_project_explanation;
                $report->sister_chapter = $sister_chapter;
                $report->sister_chapter_explanation = $sister_chapter_explanation;
                $report->international_event = $international_event;
                $report->file_irs = $file_irs;
                $report->file_irs_explanation = $file_irs_explanation;
                $report->bank_statement_included = $bank_statement_included;
                $report->bank_statement_included_explanation = $bank_statement_included_explanation;
                $report->wheres_the_money = $wheres_the_money;
                // $report->award_nominations = $award_nominations;
                $report->chapter_awards = $chapter_awards;
                // $report->award_1_nomination_type = $award_1_nomination_type;
                // $report->award_1_outstanding_follow_bylaws = $award_1_outstanding_follow_bylaws;
                // $report->award_1_outstanding_well_rounded = $award_1_outstanding_well_rounded;
                // $report->award_1_outstanding_communicated = $award_1_outstanding_communicated;
                // $report->award_1_outstanding_support_international = $award_1_outstanding_support_international;
                // $report->award_1_outstanding_project_desc = $award_1_outstanding_project_desc;
                // $report->award_2_nomination_type = $award_2_nomination_type;
                // $report->award_2_outstanding_follow_bylaws = $award_2_outstanding_follow_bylaws;
                // $report->award_2_outstanding_well_rounded = $award_2_outstanding_well_rounded;
                // $report->award_2_outstanding_communicated = $award_2_outstanding_communicated;
                // $report->award_2_outstanding_support_international = $award_2_outstanding_support_international;
                // $report->award_2_outstanding_project_desc = $award_2_outstanding_project_desc;
                // $report->award_3_nomination_type = $award_3_nomination_type;
                // $report->award_3_outstanding_follow_bylaws = $award_3_outstanding_follow_bylaws;
                // $report->award_3_outstanding_well_rounded = $award_3_outstanding_well_rounded;
                // $report->award_3_outstanding_communicated = $award_3_outstanding_communicated;
                // $report->award_3_outstanding_support_international = $award_3_outstanding_support_international;
                // $report->award_3_outstanding_project_desc = $award_3_outstanding_project_desc;
                // $report->award_4_nomination_type = $award_4_nomination_type;
                // $report->award_4_outstanding_follow_bylaws = $award_4_outstanding_follow_bylaws;
                // $report->award_4_outstanding_well_rounded = $award_4_outstanding_well_rounded;
                // $report->award_4_outstanding_communicated = $award_4_outstanding_communicated;
                // $report->award_4_outstanding_support_international = $award_4_outstanding_support_international;
                // $report->award_4_outstanding_project_desc = $award_4_outstanding_project_desc;
                // $report->award_5_nomination_type = $award_5_nomination_type;
                // $report->award_5_outstanding_follow_bylaws = $award_5_outstanding_follow_bylaws;
                // $report->award_5_outstanding_well_rounded = $award_5_outstanding_well_rounded;
                // $report->award_5_outstanding_communicated = $award_5_outstanding_communicated;
                // $report->award_5_outstanding_support_international = $award_5_outstanding_support_international;
                // $report->award_5_outstanding_project_desc = $award_5_outstanding_project_desc;
                $report->award_agree = $award_agree;
                $report->farthest_step_visited = $farthest_step_visited;
                $report->completed_name = $userName;
                $report->completed_email = $userEmail;
                $report->submitted = date('Y-m-d H:i:s');

                $mailData = [
                    'chapterid' => $id,
                    'chapter_name' => $chDetails->name,
                    'chapter_state' => $stateShortName,
                    'completed_name' => $userName,
                    'completed_email' => $userEmail,
                    'roster_path' => $roster_path,
                    'file_irs_path' => $irs_path,
                    'bank_statement_included_path' => $statement_1_path,
                    'bank_statement_2_included_path' => $statement_2_path,
                    // 'financial_pdf_path' => $financial_pdf_path,
                ];

                 // Send emails
                $to_email = $emailCC;
                $to_email3 = $reviewerEmail;
                $to_email2 = $userEmail;
                $to_email4 = $emailListChap;

                if ($reportReceived == 1) {
                    $pdfPath = $this->pdfController->generateAndSavePdf($id, $userId);   // Generate and save the PDF
                    // $pdfPath = $this->generateAndSavePdf($id, $userId);   // Generate and save the PDF
                    Mail::to($to_email2)
                        ->cc($to_email4)
                        ->queue(new EOYFinancialReportThankYou($mailData, $pdfPath));

                    if ($chFinancialReport->reviewer_id == null) {
                        DB::update('UPDATE financial_report SET reviewer_id = ? where chapter_id = ?', [$cc_id, $id]);
                        Mail::to($to_email)
                            ->queue(new EOYFinancialSubmitted($mailData, $pdfPath));
                    }

                    if ($chFinancialReport->reviewer_id != null) {
                        Mail::to($to_email3)
                            ->queue(new EOYFinancialSubmitted($mailData, $pdfPath));
                    }
                }

                $report->save();

                if ($reportReceived == 1) {
                    $documents->financial_report_received = 1;
                    $documents->report_received = date('Y-m-d H:i:s');
                }

                $documents->save();

                $chapter->last_updated_by = $lastUpdatedBy;
                $chapter->last_updated_date = date('Y-m-d H:i:s');
                $chapter->save();


                DB::commit();
                if ($reportReceived == 1) {
                    return redirect()->back()->with('success', 'Report has been successfully Submitted');
                } else {
                    return redirect()->back()->with('success', 'Report has been successfully updated');
                }

            } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong Please try again.');
        }
    }

    // public function generateAndSavePdf($id)
    // {
    //     $baseQuery = $this->getChapterDetails($id);
    //     $chDetails = $baseQuery['chDetails'];
    //     $stateShortName = $baseQuery['stateShortName'];
    //     $chDocuments = $baseQuery['chDocuments'];
    //     $chFinancialReport = $baseQuery['chFinancialReport'];

    //     $pdfData = [
    //         'conf' => $chDetails->conference_id,
    //         'chapter_name' => $chDetails->name,
    //         'state' => $stateShortName,
    //         'ein' => $chDetails->ein,
    //         'boundaries' => $chDetails->territory,
    //         'changed_dues' => $chFinancialReport->changed_dues,
    //         'different_dues' => $chFinancialReport->different_dues,
    //         'not_all_full_dues' => $chFinancialReport->not_all_full_dues,
    //         'total_new_members' => $chFinancialReport->total_new_members,
    //         'total_renewed_members' => $chFinancialReport->total_renewed_members,
    //         'dues_per_member' => $chFinancialReport->dues_per_member,
    //         'total_new_members_changed_dues' => $chFinancialReport->total_new_members_changed_dues,
    //         'total_renewed_members_changed_dues' => $chFinancialReport->total_renewed_members_changed_dues,
    //         'dues_per_member_renewal' => $chFinancialReport->dues_per_member_renewal,
    //         'dues_per_member_new_changed' => $chFinancialReport->dues_per_member_new_changed,
    //         'dues_per_member_renewal_changed' => $chFinancialReport->dues_per_member_renewal_changed,
    //         'members_who_paid_no_dues' => $chFinancialReport->members_who_paid_no_dues,
    //         'members_who_paid_partial_dues' => $chFinancialReport->members_who_paid_partial_dues,
    //         'total_partial_fees_collected' => $chFinancialReport->total_partial_fees_collected,
    //         'total_associate_members' => $chFinancialReport->total_associate_members,
    //         'associate_member_fee' => $chFinancialReport->associate_member_fee,
    //         'manditory_meeting_fees_paid' => $chFinancialReport->manditory_meeting_fees_paid,
    //         'voluntary_donations_paid' => $chFinancialReport->voluntary_donations_paid,
    //         'paid_baby_sitters' => $chFinancialReport->paid_baby_sitters,
    //         'childrens_room_expenses' => $chFinancialReport->childrens_room_expenses,
    //         'service_project_array' => $chFinancialReport->service_project_array,
    //         'party_expense_array' => $chFinancialReport->party_expense_array,
    //         'office_printing_costs' => $chFinancialReport->office_printing_costs,
    //         'office_postage_costs' => $chFinancialReport->office_postage_costs,
    //         'office_membership_pins_cost' => $chFinancialReport->office_membership_pins_cost,
    //         'office_other_expenses' => $chFinancialReport->office_other_expenses,
    //         'international_event_array' => $chFinancialReport->international_event_array,
    //         'annual_registration_fee' => $chFinancialReport->annual_registration_fee,
    //         'monetary_donations_to_chapter' => $chFinancialReport->monetary_donations_to_chapter,
    //         'non_monetary_donations_to_chapter' => $chFinancialReport->non_monetary_donations_to_chapter,
    //         'other_income_and_expenses_array' => $chFinancialReport->other_income_and_expenses_array,
    //         'amount_reserved_from_previous_year' => $chFinancialReport->amount_reserved_from_previous_year,
    //         'bank_balance_now' => $chFinancialReport->bank_balance_now,
    //         'bank_reconciliation_array' => $chFinancialReport->bank_reconciliation_array,
    //         'receive_compensation' => $chFinancialReport->receive_compensation,
    //         'receive_compensation_explanation' => $chFinancialReport->receive_compensation_explanation,
    //         'financial_benefit' => $chFinancialReport->financial_benefit,
    //         'financial_benefit_explanation' => $chFinancialReport->financial_benefit_explanation,
    //         'influence_political' => $chFinancialReport->influence_political,
    //         'influence_political_explanation' => $chFinancialReport->influence_political_explanation,
    //         'vote_all_activities' => $chFinancialReport->vote_all_activities,
    //         'vote_all_activities_explanation' => $chFinancialReport->vote_all_activities_explanation,
    //         'purchase_pins' => $chFinancialReport->purchase_pins,
    //         'purchase_pins_explanation' => $chFinancialReport->purchase_pins_explanation,
    //         'bought_merch' => $chFinancialReport->bought_merch,
    //         'bought_merch_explanation' => $chFinancialReport->bought_merch_explanation,
    //         'offered_merch' => $chFinancialReport->offered_merch,
    //         'offered_merch_explanation' => $chFinancialReport->offered_merch_explanation,
    //         'bylaws_available' => $chFinancialReport->bylaws_available,
    //         'bylaws_available_explanation' => $chFinancialReport->bylaws_available_explanation,
    //         'childrens_room_sitters' => $chFinancialReport->childrens_room_sitters,
    //         'childrens_room_sitters_explanation' => $chFinancialReport->childrens_room_sitters_explanation,
    //         'had_playgroups' => $chFinancialReport->had_playgroups,
    //         'playgroups' => $chFinancialReport->playgroups,
    //         'had_playgroups_explanation' => $chFinancialReport->had_playgroups_explanation,
    //         'child_outings' => $chFinancialReport->child_outings,
    //         'child_outings_explanation' => $chFinancialReport->child_outings_explanation,
    //         'mother_outings' => $chFinancialReport->mother_outings,
    //         'mother_outings_explanation' => $chFinancialReport->mother_outings_explanation,
    //         'meeting_speakers' => $chFinancialReport->meeting_speakers,
    //         'meeting_speakers_explanation' => $chFinancialReport->meeting_speakers_explanation,
    //         'meeting_speakers_array' => $chFinancialReport->meeting_speakers_array,
    //         'discussion_topic_frequency' => $chFinancialReport->discussion_topic_frequency,
    //         'park_day_frequency' => $chFinancialReport->park_day_frequency,
    //         'activity_array' => $chFinancialReport->activity_array,
    //         'contributions_not_registered_charity' => $chFinancialReport->contributions_not_registered_charity,
    //         'contributions_not_registered_charity_explanation' => $chFinancialReport->contributions_not_registered_charity_explanation,
    //         'at_least_one_service_project' => $chFinancialReport->at_least_one_service_project,
    //         'at_least_one_service_project_explanation' => $chFinancialReport->at_least_one_service_project_explanation,
    //         'sister_chapter' => $chFinancialReport->sister_chapter,
    //         'international_event' => $chFinancialReport->international_event,
    //         'file_irs' => $chFinancialReport->file_irs,
    //         'file_irs_explanation' => $chFinancialReport->file_irs_explanation,
    //         'bank_statement_included' => $chFinancialReport->bank_statement_included,
    //         'bank_statement_included_explanation' => $chFinancialReport->bank_statement_included_explanation,
    //         'wheres_the_money' => $chFinancialReport->wheres_the_money,
    //         'completed_name' => $chFinancialReport->completed_name,
    //         'completed_email' => $chFinancialReport->completed_email,
    //         'submitted' => $chFinancialReport->submitted,
    //     ];

    //     $pdf = Pdf::loadView('pdf.financialreport', compact('pdfData'));

    //     $chapterName = str_replace('/', '', $pdfData['chapter_name']); // Remove any slashes from chapter name
    //     $filename = (date('Y') - 1).'-'.date('Y').'_'.$pdfData['state'].'_'.$chapterName.'_FinancialReport.pdf'; // Use sanitized chapter name

    //     $pdfPath = storage_path('app/pdf_reports/'.$filename);
    //     $pdf->save($pdfPath);

    //     $googleClient = new Client;
    //     $client_id = \config('services.google.client_id');
    //     $client_secret = \config('services.google.client_secret');
    //     $refresh_token = \config('services.google.refresh_token');
    //     $response = Http::post('https://oauth2.googleapis.com/token', [
    //         'client_id' => $client_id,
    //         'client_secret' => $client_secret,
    //         'refresh_token' => $refresh_token,
    //         'grant_type' => 'refresh_token',
    //     ]);

    //     $conf = $pdfData['conf'];
    //     $state = $pdfData['state'];
    //     $chapterName = str_replace('/', '', $pdfData['chapter_name']); // Remove any slashes from chapter name
    //     $accessToken = json_decode((string) $response->getBody(), true)['access_token'];

    //     $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';   //Shared Drive -> EOY Uploads -> 2024

    //     // Create conference folder if it doesn't exist in the shared drive
    //     $chapterFolderId = $this->createFolderIfNotExists($conf, $state, $chapterName, $accessToken, $sharedDriveId);

    //     // Set parent IDs for the file
    //     $fileMetadata = [
    //         'name' => $filename,
    //         'mimeType' => 'application/pdf',
    //         'parents' => [$chapterFolderId],
    //         'driveId' => $sharedDriveId,
    //     ];

    //     // Upload the file
    //     $fileContent = file_get_contents($pdfPath);
    //     $fileContentBase64 = base64_encode($fileContent);
    //     $metadataJson = json_encode($fileMetadata);

    //     $response = $googleClient->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
    //         'headers' => [
    //             'Authorization' => 'Bearer '.$accessToken,
    //             'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
    //         ],
    //         'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
    //     ]);

    //     if ($response->getStatusCode() === 200) { // Check for a successful status code
    //         $pdf_file_id = json_decode($response->getBody()->getContents(), true)['id'];
    //         $documents = Documents::find($id);
    //         $documents->financial_pdf_path = $pdf_file_id;
    //         $documents->save();

    //         return $pdfPath;  // Return the full local stored path
    //     }
    // }

    // private function createFolderIfNotExists($conf, $state, $chapterName, $accessToken, $sharedDriveId)
    // {
    //     // Check if the conference folder exists, create it if not
    //     $confFolderId = $this->getOrCreateConfFolder($conf, $accessToken, $sharedDriveId);

    //     // Check if the state folder exists, create it if not
    //     $stateFolderId = $this->getOrCreateStateFolder($conf, $state, $confFolderId, $accessToken, $sharedDriveId);

    //     // Check if the chapter folder exists, create it if not
    //     $chapterFolderId = $this->getOrCreateChapterFolder($conf, $state, $chapterName, $stateFolderId, $accessToken, $sharedDriveId);

    //     return $chapterFolderId;
    // }

    // private function getOrCreateConfFolder($conf, $accessToken, $sharedDriveId)
    // {
    //     // Check if the conference folder exists in the records
    //     $confRecord = FolderRecord::where('conf', $conf)->first();

    //     if ($confRecord) {
    //         // Conference folder exists, return its ID
    //         return $confRecord->folder_id;
    //     } else {
    //         // Conference folder doesn't exist, create it
    //         $client = new Client;
    //         $folderMetadata = [
    //             'name' => "Conference $conf",
    //             'parents' => [$sharedDriveId],
    //             'driveId' => $sharedDriveId,
    //             'mimeType' => 'application/vnd.google-apps.folder',
    //         ];
    //         $response = $client->request('POST', 'https://www.googleapis.com/drive/v3/files?supportsAllDrives=true', [
    //             'headers' => [
    //                 'Authorization' => 'Bearer '.$accessToken,
    //                 'Content-Type' => 'application/json',
    //             ],
    //             'json' => $folderMetadata,
    //         ]);
    //         $folderId = json_decode($response->getBody()->getContents(), true)['id'];

    //         // Record the created folder ID for future reference
    //         FolderRecord::create([
    //             'conf' => $conf,
    //             'folder_id' => $folderId,
    //         ]);

    //         return $folderId;
    //     }
    // }

    // private function getOrCreateStateFolder($conf, $state, $confFolderId, $accessToken, $sharedDriveId)
    // {
    //     // Check if the state folder exists in the records
    //     $stateRecord = FolderRecord::where('state', $state)->first();

    //     if ($stateRecord) {
    //         // State folder exists, return its ID
    //         return $stateRecord->folder_id;
    //     } else {
    //         // State folder doesn't exist, create it
    //         $client = new Client;
    //         $folderMetadata = [
    //             'name' => $state,
    //             'parents' => [$confFolderId],
    //             'driveId' => $sharedDriveId,
    //             'mimeType' => 'application/vnd.google-apps.folder',
    //         ];
    //         $response = $client->request('POST', 'https://www.googleapis.com/drive/v3/files?supportsAllDrives=true', [
    //             'headers' => [
    //                 'Authorization' => 'Bearer '.$accessToken,
    //                 'Content-Type' => 'application/json',
    //             ],
    //             'json' => $folderMetadata,
    //         ]);
    //         $folderId = json_decode($response->getBody()->getContents(), true)['id'];

    //         // Record the created folder ID for future reference
    //         FolderRecord::create([
    //             // 'conf' => "Conference $conf",
    //             'state' => $state,
    //             'folder_id' => $folderId,
    //         ]);

    //         return $folderId;
    //     }
    // }

    // private function getOrCreateChapterFolder($conf, $state, $chapterName, $stateFolderId, $accessToken, $sharedDriveId)
    // {
    //     // Check if the chapter folder exists in the records
    //     $chapterRecord = FolderRecord::where('chapter_name', $chapterName)->first();

    //     if ($chapterRecord) {
    //         // Chapter folder exists, return its ID
    //         return $chapterRecord->folder_id;
    //     } else {
    //         // Chapter folder doesn't exist, create it
    //         $client = new Client;
    //         $folderMetadata = [
    //             'name' => $chapterName,
    //             'parents' => [$stateFolderId],
    //             'driveId' => $sharedDriveId,
    //             'mimeType' => 'application/vnd.google-apps.folder',
    //         ];
    //         $response = $client->request('POST', 'https://www.googleapis.com/drive/v3/files?supportsAllDrives=true', [
    //             'headers' => [
    //                 'Authorization' => 'Bearer '.$accessToken,
    //                 'Content-Type' => 'application/json',
    //             ],
    //             'json' => $folderMetadata,
    //         ]);
    //         $folderId = json_decode($response->getBody()->getContents(), true)['id'];

    //         // Record the created folder ID for future reference
    //         FolderRecord::create([
    //             // 'conf' => "Conference $conf",
    //             // 'state' => $state,
    //             'chapter_name' => $chapterName,
    //             'folder_id' => $folderId,
    //         ]);

    //         return $folderId;
    //     }
    // }

    public function getRosterfile(): BinaryFileResponse
    {
        $filename = 'roster_template.xlsx';

        $file_path = '/home/momsclub/public_html/mimi/storage/app/public';

        return Response::download($file_path, $filename, [
            'Content-Length: '.filesize($file_path),
        ]);
    }

}
