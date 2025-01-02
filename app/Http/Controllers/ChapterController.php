<?php

namespace App\Http\Controllers;

use App\Mail\ChapersUpdateEINCoor;
use App\Mail\ChapersUpdateListAdmin;
use App\Mail\ChapterAddListAdmin;
use App\Mail\ChapterAddPrimaryCoor;
use App\Mail\ChapterDisbandLetter;
use App\Mail\ChapterReAddListAdmin;
use App\Mail\ChapterRemoveListAdmin;
use App\Mail\ChaptersPrimaryCoordinatorChange;
use App\Mail\ChaptersPrimaryCoordinatorChangePCNotice;
use App\Mail\ChaptersUpdatePrimaryCoorBoard;
use App\Mail\ChaptersUpdatePrimaryCoorChapter;
use App\Mail\WebsiteAddNoticeAdmin;
use App\Mail\WebsiteAddNoticeChapter;
use App\Mail\WebsiteReviewNotice;
use App\Mail\WebsiteUpdatePrimaryCoor;
use App\Mail\PaymentsReRegLate;
use App\Mail\PaymentsReRegReminder;
use App\Mail\PaymentsM2MChapterThankYou;
use App\Mail\PaymentsReRegChapterThankYou;
use App\Mail\PaymentsSustainingChapterThankYou;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\State;
use App\Models\FinancialReport;
use App\Models\FinancialReportAwards;
use App\Models\User;
use App\Models\Status;
use App\Models\Website;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ChapterController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->middleware('auth')->except('logout');
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class);
        $this->userController = $userController;
    }


     /**
     * Active Chapter List Base Query
     */
    public function getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    {
        $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);
        if ($conditions['coordinatorCondition']) {
            $coordinatorData = $this->userController->loadReportingTree($cdId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = Chapters::with(['state', 'conference', 'region', 'webLink', 'president', 'avp', 'mvp', 'treasurer', 'secretary', 'primaryCoordinator'])
            ->where('is_active', 1);

        if ($conditions['founderCondition']) {
            } elseif ($conditions['assistConferenceCoordinatorCondition']) {
                $baseQuery->where('conference_id', '=', $cdConfId);
            } elseif ($conditions['regionalCoordinatorCondition']) {
                $baseQuery->where('region_id', '=', $cdRegId);
            } else {
                $baseQuery->whereIn('primary_coordinator_id', $inQryArr);
        }

        if (isset($_GET['check']) && $_GET['check'] == 'yes') {
            $checkBoxStatus = 'checked';
            $baseQuery->where('primary_coordinator_id', '=', $cdId);
        } else {
            $checkBoxStatus = '';
        }

        $baseQuery->orderBy(State::select('state_short_name')
            ->whereColumn('state.id', 'chapters.state_id'), 'asc')
            ->orderBy('chapters.name', 'asc');

        return ['query' => $baseQuery, 'checkBoxStatus' => $checkBoxStatus];

    }

     /**
     * Active Chapter Details Base Query
     */
    public function getChapterDetails($id)
    {
        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'documents', 'financialReport', 'startMonth', 'boards'])->find($id);
        $chId = $chDetails->id;
        $chIsActive = $chDetails->is_active;
        $stateShortName = $chDetails->state->state_short_name;
        $regionLongName = $chDetails->region->long_name;
        $conferenceDescription = $chDetails->conference->conference_description;
        $chConfId = $chDetails->conference_id;
        $chRegId = $chDetails->region_id;
        $chPcId = $chDetails->primary_coordinator_id;

        $startMonthName = $chDetails->startMonth->month_long_name;
        $chapterStatus = $chDetails->status->chapter_status;
        $websiteLink = $chDetails->webLink->link_status ?? null;

        $chDocuments = $chDetails->documents;
        $submitted = $chDetails->documents->financial_report_received;
        $reviewComplete = $chDetails->documents->review_complete;
        $chFinancialReport = $chDetails->financialReport;

        $allStatuses = Status::all();  // Full List for Dropdown Menu
        $allAwards = FinancialReportAwards::all();  // Full List for Dropdown Menu
        $allWebLinks = Website::all();  // Full List for Dropdown Menu
        $allStates = State::all();  // Full List for Dropdown Menu

        $boards = $chDetails->boards()->with('stateName')->get();
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

        // Load Report Reviewer Coordinator Dropdown List
        $pcDetails = $this->userController->loadPrimaryList($chRegId, $chConfId);
        // $rrDetails = $this->userController->loadReviewerList($chRegId, $chConfId);

        return ['chDetails' => $chDetails, 'chIsActive' => $chIsActive, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription, 'chConfId' => $chConfId, 'chRegId' => $chRegId, 'chPcId' => $chPcId, 'chId' => $chId,
            'chDocuments' => $chDocuments, 'reviewComplete' => $reviewComplete, 'chFinancialReport' => $chFinancialReport, 'allAwards' => $allAwards,
            'PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
            'emailListChap' => $emailListChap, 'emailListCoord' => $emailListCoord, 'pcDetails' => $pcDetails, 'submitted' => $submitted,
            'allWebLinks' => $allWebLinks, 'allStatuses' => $allStatuses, 'allStates' => $allStates,
            'startMonthName' => $startMonthName, 'chapterStatus' => $chapterStatus, 'websiteLink' => $websiteLink
        ];

    }

     /**
     * Zapped Chapter List Base Query
     */
    public function getZappedBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    {
        $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);
        if ($conditions['coordinatorCondition']) {
            $coordinatorData = $this->userController->loadReportingTree($cdId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = Chapters::with(['state', 'conference', 'region', 'president', 'primaryCoordinator'])
        ->where('is_active', 0)
        ->orderBy('chapters.zap_date', 'desc');

        if ($conditions['founderCondition']) {
            } elseif ($conditions['assistConferenceCoordinatorCondition']) {
                $baseQuery->where('conference_id', '=', $cdConfId);
            } elseif ($conditions['regionalCoordinatorCondition']) {
                $baseQuery->where('region_id', '=', $cdRegId);
            } else {
                $baseQuery->whereIn('primary_coordinator_id', $inQryArr);
        }

        return ['query' => $baseQuery ];

    }

    /**
     * Display the Active chapter list mapped with login coordinator
     */
    public function showChapters(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $countList = $chapterList->count();
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $cdId ];

        return view('chapters.chaplist')->with($data);
    }

    /**
     * Display the Zapped chapter list mapped with Conference Region
     */
    public function showZappedChapter(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getZappedBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('chapters.chapzapped')->with($data);
    }

    /**
     * Display the Inquiries Chapter list
     */
    public function showChapterInquiries(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);

        $baseQuery = Chapters::with(['state', 'conference', 'region'])
            ->where('is_active', 1);

        if ($conditions['founderCondition'] || $conditions['inquiriesInternationalCondition']) {
            } elseif ($conditions['assistConferenceCoordinatorCondition'] || $conditions['inquiriesConferneceCondition']) {
                $baseQuery->where('conference_id', '=', $cdConfId);
            } else {
                $baseQuery->where('region_id', '=', $cdRegId);
            }

        $baseQuery->orderBy(State::select('state_short_name')
            ->whereColumn('state.id', 'chapters.state_id'), 'asc')
            ->orderBy('chapters.name', 'asc');

        $inquiriesList = $baseQuery->get();

        $data = ['inquiriesList' => $inquiriesList, 'corConfId' => $cdConfId, 'corRegId' => $cdRegId];

        return view('chapters.chapinquiries')->with($data);
    }

    /**
     * Display the Zapped Inquiries list
     */
    public function showZappedChapterInquiries(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);

        $baseQuery = Chapters::with(['state', 'conference', 'region'])
        ->where('is_active', 0);

        if ($conditions['founderCondition'] || $conditions['inquiriesInternationalCondition']) {
            } elseif ($conditions['assistConferenceCoordinatorCondition'] || $conditions['inquiriesConferneceCondition']) {
                $baseQuery->where('conference_id', '=', $cdConfId);
            } else {
                $baseQuery->where('region_id', '=', $cdRegId);
            }

            $baseQuery->orderBy(State::select('state_short_name')
            ->whereColumn('state.id', 'chapters.state_id'), 'asc')
            ->orderBy('chapters.name', 'asc');

        $inquiriesList = $baseQuery->get();

        $data = ['inquiriesList' => $inquiriesList, 'corConfId' => $cdConfId];

        return view('chapters.chapinquirieszapped')->with($data);
    }

    /**
     * Display the International chapter list
     */
    public function showIntChapter(Request $request)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $intChapterList = Chapters::with(['state', 'conference', 'region', 'president', 'primaryCoordinator'])
            ->where('is_active', 1)
            ->orderBy(State::select('state_short_name')
                ->whereColumn('state.id', 'chapters.state_id'), 'asc')
            ->orderBy('chapters.name', 'asc')
            ->get();

        $countList = count($intChapterList);
        $data = ['countList' => $countList, 'intChapterList' => $intChapterList];

        return view('international.intchapter')->with($data);
    }

    /**
     * Display the International Zapped chapter list
     */
    public function showIntZappedChapter(Request $request)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $chapterList = Chapters::with(['state', 'conference', 'region'])
            ->where('is_active', 0)
            ->orderBy('chapters.zap_date', 'desc')
            ->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('international.intchapterzapped')->with($data);
    }

    /**
     * Display the Chapter Details for ALL lists - Active, Zapped, Inquiries, International
     */
    public function viewChapterDetails(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chId = $baseQuery['chId'];
        $chIsActive = $baseQuery['chIsActive'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chDocuments = $baseQuery['chDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $reviewComplete = $baseQuery['reviewComplete'];

        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $baseQuery['chapterStatus'];
        $websiteLink = $baseQuery['websiteLink'];

        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'cdPositionid' => $cdPositionid, 'cdId' => $cdId, 'reviewComplete' => $reviewComplete,
            'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'PresDetails' => $PresDetails, 'chDetails' => $chDetails, 'websiteLink' => $websiteLink,
            'startMonthName' => $startMonthName, 'cdConfId' => $cdConfId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chapterStatus' => $chapterStatus,
            'chFinancialReport' => $chFinancialReport, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription,
        ];

        return view('chapters.view')->with($data);
    }

    public function checkEIN(Request $request): JsonResponse
    {
        $chapterId = $request->input('chapter_id');
        $chapter = DB::table('chapters')->where('id', $chapterId)->first();

        return response()->json([
            'ein' => $chapter->ein ?? null,
        ]);
    }

    public function updateEIN(Request $request): JsonResponse
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $ein = $request->input('ein');
        $chapterId = $request->input('chapter_id');

        try {
            DB::beginTransaction();

            DB::table('chapters')
                ->where('id', $chapterId)
                ->update(['ein' => $ein,
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d'),
                ]);

            // Commit the transaction
            DB::commit();

            $message = 'Chapter EIN successfully updated';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterId]),
            ]);

        } catch (\Exception $e) {
            // Rollback transaction on exception
            DB::rollback();
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterId]),
            ]);
        }
    }

    /**
     * Function for Zapping a Chapter (store)
     */
    public function updateChapterDisband(Request $request): JsonResponse
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];

        $input = $request->all();
        $chapterid = $input['chapterid'];
        $disbandReason = $input['reason'];
        $disbandLetter = $input['letter'];

        try {
            DB::beginTransaction();

            DB::table('chapters')
                ->where('id', $chapterid)
                ->update(['is_active' => 0, 'disband_reason' => $disbandReason, 'disband_letter' => $disbandLetter, 'zap_date' => date('Y-m-d')]);

            $userRelatedChpaterList = DB::table('boards as bd')
                ->select('bd.user_id')
                ->where('bd.chapter_id', '=', $chapterid)
                ->get();
            if (count($userRelatedChpaterList) > 0) {
                foreach ($userRelatedChpaterList as $list) {
                    $userId = $list->user_id;
                    DB::table('users')
                        ->where('id', $userId)
                        ->update(['is_active' => 0]);
                }
            }
            DB::table('boards')
                ->where('chapter_id', $chapterid)
                ->update(['is_active' => 0]);

            $chapterList = DB::table('chapters')
                ->select('chapters.*', 'chapters.primary_coordinator_id as pcid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                    'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'chapters.conference_id as conf')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
                ->where('chapters.is_Active', '=', '0')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.id', $chapterid)
                ->orderByDesc('chapters.id')
                ->get();

            $chPcid = $chapterList[0]->pcid;

            $chapterName = $chapterList[0]->name;
            $chapterState = $chapterList[0]->state;
            $chapterEmail = $chapterList[0]->email;
            $chapterStatus = $chapterList[0]->status_id;
            //President Info
            $preinfo = DB::table('boards')
                ->select('first_name', 'last_name', 'email')
                ->where('chapter_id', $chapterid)
                ->where('board_position_id', '=', '1')
                ->get();

            if (count($preinfo) > 0) {
                $prefirst = $preinfo[0]->first_name;
                $presecond = $preinfo[0]->last_name;
                $preemail = $preinfo[0]->email;
            } else {
                $prefirst = '';
                $presecond = '';
                $preemail = '';
            }
            //Avp info
            $avpinfo = DB::table('boards')
                ->select('first_name', 'last_name', 'email')
                ->where('chapter_id', $chapterid)
                ->where('board_position_id', '=', '2')
                ->get();
            if (count($avpinfo) > 0) {
                $avpfirst = $avpinfo[0]->first_name;
                $avpsecond = $avpinfo[0]->last_name;
                $avpemail = $avpinfo[0]->email;
            } else {
                $avpfirst = '';
                $avpsecond = '';
                $avpemail = '';
            }
            //Mvp info

            $mvpinfo = DB::table('boards')
                ->select('first_name', 'last_name', 'email')
                ->where('chapter_id', $chapterid)
                ->where('board_position_id', '=', '3')
                ->get();
            if (count($mvpinfo) > 0) {
                $mvpfirst = $mvpinfo[0]->first_name;
                $mvpsecond = $mvpinfo[0]->last_name;
                $mvpemail = $mvpinfo[0]->email;
            } else {
                $mvpfirst = '';
                $mvpsecond = '';
                $mvpemail = '';
            }
            //Treasurere info
            $triinfo = DB::table('boards')
                ->select('first_name', 'last_name', 'email')
                ->where('chapter_id', $chapterid)
                ->where('board_position_id', '=', '4')
                ->get();
            if (count($triinfo) > 0) {
                $trifirst = $triinfo[0]->first_name;
                $trisecond = $triinfo[0]->last_name;
                $triemail = $triinfo[0]->email;
            } else {
                $trifirst = '';
                $trisecond = '';
                $triemail = '';
            }
            //secretary info
            $secinfo = DB::table('boards')
                ->select('first_name', 'last_name', 'email')
                ->where('chapter_id', $chapterid)
                ->where('board_position_id', '=', '5')
                ->get();
            if (count($secinfo) > 0) {
                $secfirst = $secinfo[0]->first_name;
                $secscond = $secinfo[0]->last_name;
                $secemail = $secinfo[0]->email;
            } else {
                $secfirst = '';
                $secscond = '';
                $secemail = '';
            }
            //conference info
            $coninfo = DB::table('chapters')
                ->select('chapters.*', 'conference')
                ->where('id', $chapterid)
                ->get();
            $conf = $coninfo[0]->conference;

            // Load Board and Coordinators for Sending Email
            $chId = $chapterList[0]->id;
            $emailData = $this->userController->loadEmailDetails($chId);
            $emailListChap = $emailData['emailListChap'];
            $emailListCoord = $emailData['emailListCoord'];

            $chapterEmails = $emailListChap;
            $coordEmails = $emailListCoord;

            // Load Conference Coordinators information for signing email
            $chConf = $chapterList[0]->conf;
            $chPcid = $chapterList[0]->pcid;

            $coordinatorData = $this->userController->loadConferenceCoord($chPcid);
            $cc_fname = $coordinatorData['cc_fname'];
            $cc_lname = $coordinatorData['cc_lname'];
            $cc_pos = $coordinatorData['cc_pos'];
            $cc_conf = $coordinatorData['cc_conf'];
            $cc_conf_desc = $coordinatorData['cc_conf_desc'];
            $cc_email = $coordinatorData['cc_email'];

            $mailData = [
                'chapterName' => $chapterName,
                'chapterEmail' => $chapterEmail,
                'chapterState' => $chapterState,
                'pfirst' => $prefirst,
                'plast' => $presecond,
                'pemail' => $preemail,
                'afirst' => $avpfirst,
                'alast' => $avpsecond,
                'aemail' => $avpemail,
                'mfirst' => $mvpfirst,
                'mlast' => $mvpsecond,
                'memail' => $mvpemail,
                'tfirst' => $trifirst,
                'tlast' => $trisecond,
                'temail' => $triemail,
                'sfirst' => $secfirst,
                'slast' => $secscond,
                'semail' => $secemail,
                'conf' => $conf,
                'cc_fname' => $cc_fname,
                'cc_lname' => $cc_lname,
                'cc_pos' => $cc_pos,
                'cc_conf' => $cc_conf,
                'cc_conf_desc' => $cc_conf_desc,
                'cc_email' => $cc_email,
            ];

            //Primary Coordinator Notification//
            $to_email = 'listadmin@momsclub.org';
            Mail::to($to_email)
                ->queue(new ChapterRemoveListAdmin($mailData));

            //Standard Disbanding Letter Send to Board & Coordinators//
            if ($disbandLetter == 1) {
                $pdfPath = $this->generateAndSaveDisbandLetter($chapterid);   // Generate and save the PDF
                Mail::to($chapterEmails)
                    ->cc($coordEmails)
                    ->queue(new ChapterDisbandLetter($mailData, $pdfPath));
            }

            // Commit the transaction
            DB::commit();

            $message = 'Chapter successfully unzapped';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);

        } catch (\Exception $e) {
            // Rollback transaction on exception
            DB::rollback();
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);
        }
    }

    public function generateAndSaveDisbandLetter($chapterid)
    {
        $chapterDetails = DB::table('chapters')
            ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.ein as ein', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name',
                'st.state_short_name as state', 'bd.first_name as pres_fname', 'bd.last_name as pres_lname', 'bd.street_address as pres_addr', 'bd.city as pres_city', 'bd.state as pres_state',
                'bd.zip as pres_zip', 'chapters.conference_id as conf', 'cf.conference_name as conf_name', 'cf.conference_description as conf_desc', 'chapters.primary_coordinator_id as pcid')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('conference as cf', 'chapters.conference_id', '=', 'cf.id')
            ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
            // ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.id', '=', $chapterid)
            ->get();

        // Load Conference Coordinators information for signing letter
        $chName = $chapterDetails[0]->chapter_name;
        $chState = $chapterDetails[0]->state;
        $chConf = $chapterDetails[0]->conf;
        $chPcid = $chapterDetails[0]->pcid;

        $coordinatorData = $this->userController->loadConferenceCoord($chPcid);
        $cc_fname = $coordinatorData['cc_fname'];
        $cc_lname = $coordinatorData['cc_lname'];
        $cc_pos = $coordinatorData['cc_pos'];

        $pdfData = [
            'chapter_name' => $chapterDetails[0]->chapter_name,
            'state' => $chapterDetails[0]->state,
            'conf' => $chapterDetails[0]->conf,
            'conf_name' => $chapterDetails[0]->conf_name,
            'conf_desc' => $chapterDetails[0]->conf_desc,
            'ein' => $chapterDetails[0]->ein,
            'pres_fname' => $chapterDetails[0]->pres_fname,
            'pres_lname' => $chapterDetails[0]->pres_lname,
            'pres_addr' => $chapterDetails[0]->pres_addr,
            'pres_city' => $chapterDetails[0]->pres_city,
            'pres_state' => $chapterDetails[0]->pres_state,
            'pres_zip' => $chapterDetails[0]->pres_zip,
            'cc_fname' => $cc_fname,
            'cc_lname' => $cc_lname,
            'cc_pos' => $cc_pos,
        ];

        $pdf = Pdf::loadView('pdf.disbandletter', compact('pdfData'));

        $chapterName = str_replace('/', '', $pdfData['chapter_name']); // Remove any slashes from chapter name
        $filename = $pdfData['state'].'_'.$chapterName.'_Disband_Letter.pdf'; // Use sanitized chapter name

        $pdfPath = storage_path('app/pdf_reports/'.$filename);
        $pdf->save($pdfPath);

        $googleClient = new Client;
        $client_id = \config('services.google.client_id');
        $client_secret = \config('services.google.client_secret');
        $refresh_token = \config('services.google.refresh_token');
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        $chapterName = str_replace('/', '', $pdfData['chapter_name']); // Remove any slashes from chapter name
        $accessToken = json_decode((string) $response->getBody(), true)['access_token'];

        $sharedDriveId = '1PlBi8BE2ESqUbLPTkQXzt1dKhwonyU_9';   //Shared Drive -> Disband Letters

        // Set parent IDs for the file
        $fileMetadata = [
            'name' => $filename,
            'mimeType' => 'application/pdf',
            'parents' => [$sharedDriveId],
        ];

        // Upload the file
        $fileContent = file_get_contents($pdfPath);
        $fileContentBase64 = base64_encode($fileContent);
        $metadataJson = json_encode($fileMetadata);

        $response = $googleClient->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
            ],
            'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
        ]);

        if ($response->getStatusCode() === 200) { // Check for a successful status code
            $pdf_file_id = json_decode($response->getBody()->getContents(), true)['id'];
            $chapter = Chapters::find($chapterid);
            $chapter->disband_letter_path = $pdf_file_id;
            $chapter->save();

            return $pdfPath;  // Return the full local stored path
        }
    }

    /**
     * Function for unZapping a Chapter (store)
     */
    public function updateChapterUnZap(Request $request): JsonResponse
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];

        $input = $request->all();
        $chapterid = $input['chapterid'];

        try {
            DB::beginTransaction();

            DB::table('chapters')
                ->where('id', $chapterid)
                ->update(['is_active' => 1, 'disband_reason' => null, 'zap_date' => null]);

            $userRelatedChpaterList = DB::table('boards as bd')
                ->select('bd.user_id')
                ->where('bd.chapter_id', '=', $chapterid)
                ->get();
            if (count($userRelatedChpaterList) > 0) {
                foreach ($userRelatedChpaterList as $list) {
                    $userId = $list->user_id;
                    DB::table('users')
                        ->where('id', $userId)
                        ->update(['is_active' => 1]);
                }
            }
            DB::table('boards')
                ->where('chapter_id', $chapterid)
                ->update(['is_active' => 1]);

            $chapterList = DB::table('chapters')
                ->select('chapters.*', 'chapters.primary_coordinator_id as pcid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                    'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'chapters.conference_id as conf')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.id', $chapterid)
                ->orderByDesc('chapters.id')
                ->get();

            $chPcid = $chapterList[0]->pcid;

            $chapterName = $chapterList[0]->name;
            $chapterState = $chapterList[0]->state;
            $chapterEmail = $chapterList[0]->email;
            $chapterStatus = $chapterList[0]->status_id;
            //President Info
            $preinfo = DB::table('boards')
                ->select('first_name', 'last_name', 'email')
                ->where('chapter_id', $chapterid)
                ->where('board_position_id', '=', '1')
                ->get();

            if (count($preinfo) > 0) {
                $prefirst = $preinfo[0]->first_name;
                $presecond = $preinfo[0]->last_name;
                $preemail = $preinfo[0]->email;
            } else {
                $prefirst = '';
                $presecond = '';
                $preemail = '';
            }
            //Avp info
            $avpinfo = DB::table('boards')
                ->select('first_name', 'last_name', 'email')
                ->where('chapter_id', $chapterid)
                ->where('board_position_id', '=', '2')
                ->get();
            if (count($avpinfo) > 0) {
                $avpfirst = $avpinfo[0]->first_name;
                $avpsecond = $avpinfo[0]->last_name;
                $avpemail = $avpinfo[0]->email;
            } else {
                $avpfirst = '';
                $avpsecond = '';
                $avpemail = '';
            }
            //Mvp info

            $mvpinfo = DB::table('boards')
                ->select('first_name', 'last_name', 'email')
                ->where('chapter_id', $chapterid)
                ->where('board_position_id', '=', '3')
                ->get();
            if (count($mvpinfo) > 0) {
                $mvpfirst = $mvpinfo[0]->first_name;
                $mvpsecond = $mvpinfo[0]->last_name;
                $mvpemail = $mvpinfo[0]->email;
            } else {
                $mvpfirst = '';
                $mvpsecond = '';
                $mvpemail = '';
            }
            //Treasurere info
            $triinfo = DB::table('boards')
                ->select('first_name', 'last_name', 'email')
                ->where('chapter_id', $chapterid)
                ->where('board_position_id', '=', '4')
                ->get();
            if (count($triinfo) > 0) {
                $trifirst = $triinfo[0]->first_name;
                $trisecond = $triinfo[0]->last_name;
                $triemail = $triinfo[0]->email;
            } else {
                $trifirst = '';
                $trisecond = '';
                $triemail = '';
            }
            //secretary info
            $secinfo = DB::table('boards')
                ->select('first_name', 'last_name', 'email')
                ->where('chapter_id', $chapterid)
                ->where('board_position_id', '=', '5')
                ->get();
            if (count($secinfo) > 0) {
                $secfirst = $secinfo[0]->first_name;
                $secscond = $secinfo[0]->last_name;
                $secemail = $secinfo[0]->email;
            } else {
                $secfirst = '';
                $secscond = '';
                $secemail = '';
            }
            //conference info
            $coninfo = DB::table('chapters')
                ->select('chapters.*', 'conference')
                ->where('id', $chapterid)
                ->get();
            $conf = $coninfo[0]->conference;

            // Load Board and Coordinators for Sending Email
            $chId = $chapterList[0]->id;
            $emailData = $this->userController->loadEmailDetails($chId);
            $emailListChap = $emailData['emailListChap'];
            $emailListCoord = $emailData['emailListCoord'];

            // Load Conference Coordinators information for signing email
            $chConf = $chapterList[0]->conf;
            $chPcid = $chapterList[0]->pcid;

            $coordinatorData = $this->userController->loadConferenceCoord($chPcid);
            $cc_fname = $coordinatorData['cc_fname'];
            $cc_lname = $coordinatorData['cc_lname'];
            $cc_pos = $coordinatorData['cc_pos'];
            $cc_conf = $coordinatorData['cc_conf'];
            $cc_conf_desc = $coordinatorData['cc_conf_desc'];
            $cc_email = $coordinatorData['cc_email'];

            $mailData = [
                'chapterName' => $chapterName,
                'chapterEmail' => $chapterEmail,
                'chapterState' => $chapterState,
                'pfirst' => $prefirst,
                'plast' => $presecond,
                'pemail' => $preemail,
                'afirst' => $avpfirst,
                'alast' => $avpsecond,
                'aemail' => $avpemail,
                'mfirst' => $mvpfirst,
                'mlast' => $mvpsecond,
                'memail' => $mvpemail,
                'tfirst' => $trifirst,
                'tlast' => $trisecond,
                'temail' => $triemail,
                'sfirst' => $secfirst,
                'slast' => $secscond,
                'semail' => $secemail,
                'conf' => $conf,
                'cc_fname' => $cc_fname,
                'cc_lname' => $cc_lname,
                'cc_pos' => $cc_pos,
                'cc_conf' => $cc_conf,
                'cc_conf_desc' => $cc_conf_desc,
                'cc_email' => $cc_email,
            ];

            //Primary Coordinator Notification//
            $to_email = 'listadmin@momsclub.org';
            Mail::to($to_email)
                ->queue(new ChapterReAddListAdmin($mailData));

            // Commit the transaction
            DB::commit();

            $message = 'Chapter successfully unzapped';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);

        } catch (\Exception $e) {
            // Rollback transaction on exception
            DB::rollback();
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);
        }

    }

    /**
     *Add New Chapter
     */
    public function editChapterNew(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        // $corDetails = User::find($request->user()->id)->coordinator;
        $corDetails = DB::table('coordinators as cd')
            ->select('cd.id', 'cd.conference_id', 'cd.region_id', 'cd.position_id')
            ->where('cd.user_id', '=', $userId)
            ->get();

        $coordId = $corDetails[0]->id;
        $corConfId = $corDetails[0]->conference_id;
        $corRegId = $corDetails[0]->region_id;
        $positionid = $corDetails[0]->position_id;

        $primaryCoordinatorList = DB::table('chapters as ch')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos', 'pos2.short_title as sec_pos')
            ->join('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->join('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
            ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
            ->where('cd.conference_id', '=', $corConfId)
            ->where('cd.position_id', '<=', '7')
            ->where('cd.position_id', '>=', '1')
            ->where('cd.is_active', '=', '1')
            ->groupBy('cd.id', 'cd.first_name', 'cd.last_name', 'cp.short_title', 'pos2.short_title')
            ->orderBy('cd.position_id')
            ->orderBy('cd.first_name')
            ->get();

        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();

        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name')
            ->get();

        $webStatusArr = ['0' => 'Website Not Linked', '1' => 'Website Linked', '2' => 'Add Link Requested', '3' => 'Do Not Link'];
        $chapterStatusArr = ['1' => 'Operating OK', '4' => 'On Hold Do not Refer', '5' => 'Probation', '6' => 'Probation Do Not Refer'];

        $data = ['positionid' => $positionid, 'coordId' => $coordId, 'regionList' => $regionList,
            'webStatusArr' => $webStatusArr, 'chapterStatusArr' => $chapterStatusArr,
            'primaryCoordinatorList' => $primaryCoordinatorList, 'corConfId' => $corConfId, 'stateArr' => $stateArr, ];

        return view('chapters.editnew')->with($data);
    }

    public function updateChapterNew(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $input = $request->all();

        $conference = $corConfId;
        $country = 'USA';
        $currentMonth = date('m');
        $currentYear = date('Y');

        DB::beginTransaction();
        try {
            $chapterId = DB::table('chapters')->insertGetId(
                [
                    'name' => $input['ch_name'],
                    'state' => $input['ch_state'],
                    'country' => $country,
                    'conference' => $conference,
                    'region' => $input['ch_region'],
                    'ein' => $input['ch_ein'],
                    'status' => $input['ch_status'],
                    'territory' => $input['ch_boundariesterry'],
                    'inquiries_contact' => $input['ch_inqemailcontact'],
                    'start_month_id' => $currentMonth,
                    'start_year' => $currentYear,
                    'next_renewal_year' => $currentYear + 1,
                    'primary_coordinator_id' => $input['ch_primarycor'],
                    'founders_name' => $input['ch_pre_fname'].' '.$input['ch_pre_lname'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'is_active' => 1]
            );

            $financial = DB::table('financial_report')->insert(
                ['chapter_id' => $chapterId]
            );

            //President Info
            if (isset($input['ch_pre_fname']) && isset($input['ch_pre_lname']) && isset($input['ch_pre_email'])) {
                $userId = DB::table('users')->insertGetId(
                    ['first_name' => $input['ch_pre_fname'],
                        'last_name' => $input['ch_pre_lname'],
                        'email' => $input['ch_pre_email'],
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1]
                );

                $boardId = DB::table('boards')->insertGetId(
                    ['user_id' => $userId,
                        'first_name' => $input['ch_pre_fname'],
                        'last_name' => $input['ch_pre_lname'],
                        'email' => $input['ch_pre_email'],
                        'board_position_id' => 1,
                        'chapter_id' => $chapterId,
                        'street_address' => $input['ch_pre_street'],
                        'city' => $input['ch_pre_city'],
                        'state' => $input['ch_pre_state'],
                        'zip' => $input['ch_pre_zip'],
                        'country' => $country,
                        'phone' => $input['ch_pre_phone'],
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s'),
                        'is_active' => 1]
                );
            }

            //AVP Info
            if (isset($input['ch_avp_fname']) && isset($input['ch_avp_lname']) && isset($input['ch_avp_email'])) {
                $userId = DB::table('users')->insertGetId(
                    ['first_name' => $input['ch_avp_fname'],
                        'last_name' => $input['ch_avp_lname'],
                        'email' => $input['required|ch_avp_email'],
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1]
                );

                $boardId = DB::table('boards')->insertGetId(
                    ['user_id' => $userId,
                        'first_name' => $input['ch_avp_fname'],
                        'last_name' => $input['ch_avp_lname'],
                        'email' => $input['required|ch_avp_email'],
                        'board_position_id' => 2,
                        'chapter_id' => $chapterId,
                        'street_address' => $input['ch_avp_street'],
                        'city' => $input['ch_avp_city'],
                        'state' => $input['ch_avp_state'],
                        'zip' => $input['ch_avp_zip'],
                        'country' => $country,
                        'phone' => $input['ch_avp_phone'],
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s'),
                        'is_active' => 1]
                );
            }
            //MVP Info
            if (isset($input['ch_mvp_fname']) && isset($input['ch_mvp_lname']) && isset($input['ch_mvp_email'])) {
                $userId = DB::table('users')->insertGetId(
                    ['first_name' => $input['ch_mvp_fname'],
                        'last_name' => $input['ch_mvp_lname'],
                        'email' => $input['ch_mvp_email'],
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1]
                );

                $boardId = DB::table('boards')->insertGetId(
                    ['user_id' => $userId,
                        'first_name' => $input['ch_mvp_fname'],
                        'last_name' => $input['ch_mvp_lname'],
                        'email' => $input['ch_mvp_email'],
                        'board_position_id' => 3,
                        'chapter_id' => $chapterId,
                        'street_address' => $input['ch_mvp_street'],
                        'city' => $input['ch_mvp_city'],
                        'state' => $input['ch_mvp_state'],
                        'zip' => $input['ch_mvp_zip'],
                        'country' => $country,
                        'phone' => $input['ch_mvp_phone'],
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s'),
                        'is_active' => 1]
                );
            }
            //TREASURER Info
            if (isset($input['ch_trs_fname']) && isset($input['ch_trs_lname']) && isset($input['ch_trs_email'])) {
                $userId = DB::table('users')->insertGetId(
                    ['first_name' => $input['ch_trs_fname'],
                        'last_name' => $input['ch_trs_lname'],
                        'email' => $input['ch_trs_email'],
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1]
                );

                $boardId = DB::table('boards')->insertGetId(
                    ['user_id' => $userId,
                        'first_name' => $input['ch_trs_fname'],
                        'last_name' => $input['ch_trs_lname'],
                        'email' => $input['ch_trs_email'],
                        'board_position_id' => 4,
                        'chapter_id' => $chapterId,
                        'street_address' => $input['ch_trs_street'],
                        'city' => $input['ch_trs_city'],
                        'state' => $input['ch_trs_state'],
                        'zip' => $input['ch_trs_zip'],
                        'country' => $country,
                        'phone' => $input['ch_trs_phone'],
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s'),
                        'is_active' => 1]
                );
            }
            //Secretary Info
            if (isset($input['ch_sec_fname']) && isset($input['ch_sec_lname']) && isset($input['ch_sec_email'])) {
                $userId = DB::table('users')->insertGetId(
                    ['first_name' => $input['ch_sec_fname'],
                        'last_name' => $input['ch_sec_lname'],
                        'email' => $input['ch_sec_email'],
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1]
                );

                $boardId = DB::table('boards')->insertGetId(
                    ['user_id' => $userId,
                        'first_name' => $input['ch_sec_fname'],
                        'last_name' => $input['ch_sec_lname'],
                        'email' => $input['ch_sec_email'],
                        'board_position_id' => 5,
                        'chapter_id' => $chapterId,
                        'street_address' => $input['ch_sec_street'],
                        'city' => $input['ch_sec_city'],
                        'state' => $input['ch_sec_state'],
                        'zip' => $input['ch_sec_zip'],
                        'country' => $country,
                        'phone' => $input['ch_sec_phone'],
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s'),
                        'is_active' => 1]
                );

            }

            $cordInfo = DB::table('coordinators')
                ->select('first_name', 'last_name', 'email')
                ->where('is_active', '=', '1')
                ->where('id', $input['ch_primarycor'])
                ->get();
            $state = DB::table('state')
                ->select('state_short_name')
                ->where('id', $input['ch_state'])
                ->get();

            $mailData = [
                'chapter_name' => $input['ch_name'],
                'chapter_state' => $state[0]->state_short_name,
                'cor_fname' => $cordInfo[0]->first_name,
                'cor_lname' => $cordInfo[0]->last_name,
                'updated_by' => date('Y-m-d H:i:s'),
                // 'email' => $input['ch_email'],
                'pfirst' => $input['ch_pre_fname'],
                'plast' => $input['ch_pre_lname'],
                'pemail' => $input['ch_pre_email'],
                'afirst' => $input['ch_avp_fname'],
                'alast' => $input['ch_avp_lname'],
                'aemail' => $input['ch_avp_email'],
                'mfirst' => $input['ch_mvp_fname'],
                'mlast' => $input['ch_mvp_lname'],
                'memail' => $input['ch_mvp_email'],
                'tfirst' => $input['ch_trs_fname'],
                'tlast' => $input['ch_trs_lname'],
                'temail' => $input['ch_trs_email'],
                'sfirst' => $input['ch_sec_fname'],
                'slast' => $input['ch_sec_lname'],
                'semail' => $input['ch_sec_email'],
                'conf' => $conference,
            ];

            //Primary Coordinator Notification//
            $to_email = $cordInfo[0]->email;

            Mail::to($to_email)
                ->queue(new ChapterAddPrimaryCoor($mailData));

            //List Admin Notification//
            $to_email2 = 'listadmin@momsclub.org';

            Mail::to($to_email2)
                ->queue(new ChapterAddListAdmin($mailData));

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->to('/chapter/chapterlist')->with('fail', 'Something went wrong, Please try again...');
        }

        return redirect()->to('/chapter/chapterlist')->with('success', 'Chapter created successfully');
    }

    /**
     *Edit Chapter Information
     */
    public function editChapterDetails(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chId = $baseQuery['chId'];
        $chIsActive = $baseQuery['chIsActive'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chConfId = $baseQuery['chConfId'];
        $chRegId = $baseQuery['chRegId'];
        $chPcId = $baseQuery['chPcId'];
        $chDocuments = $baseQuery['chDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $reviewComplete = $baseQuery['reviewComplete'];

        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $baseQuery['chapterStatus'];
        $websiteLink = $baseQuery['websiteLink'];

        $allStatuses = $baseQuery['allStatuses'];
        $allWebLinks = $baseQuery['allWebLinks'];

        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];

        $pcDetails = $baseQuery['pcDetails'];

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'cdPositionid' => $cdPositionid, 'cdId' => $cdId, 'reviewComplete' => $reviewComplete,
            'emailListCoord' => $emailListCoord, 'emailListChap' => $emailListChap, 'chDetails' => $chDetails, 'websiteLink' => $websiteLink,
            'startMonthName' => $startMonthName, 'cdConfId' => $cdConfId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chapterStatus' => $chapterStatus,
            'chFinancialReport' => $chFinancialReport, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription, 'allStatuses' => $allStatuses, 'allWebLinks' =>$allWebLinks,
            'pcDetails' => $pcDetails,
        ];

        return view('chapters.edit')->with($data);
    }

    /**
     *Update Chapter Information
     */
    public function updateChapterDetails(Request $request, $id): RedirectResponse
    {
        $chapterId = $id;
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $chapterInfoPre = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'st.state_short_name as statename',
                'chapters.conference_id as conference', 'chapters.primary_coordinator_id as cor_id', 'bd.first_name as ch_pre_fname', 'bd.last_name as ch_pre_lname',
                'bd.email as ch_pre_email')
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

        $chapter = Chapters::find($chapterId);
        DB::beginTransaction();
        try {
            $chapter->name = $request->filled('ch_name') ? $request->input('ch_name') : $request->input('ch_hid_name');
            $chapter->notes = $request->input('ch_einnotes');
            $chapter->former_name = $request->filled('ch_preknown') ? $request->input('ch_preknown') : $request->input('ch_hid_preknown');
            $chapter->sistered_by = $request->filled('ch_sistered') ? $request->input('ch_sistered') : $request->input('ch_hid_sistered');
            $chapter->territory = $request->filled('ch_boundariesterry') ? $request->input('ch_boundariesterry') : $request->input('ch_hid_boundariesterry');
            $chapter->status_id = $request->filled('ch_status') ? $request->input('ch_status') : $request->input('ch_hid_status');
            $chapter->notes = $request->input('ch_notes');
            $chapter->inquiries_contact = $request->input('ch_inqemailcontact');
            $chapter->inquiries_note = $request->input('ch_inqnote');
            $chapter->email = $request->input('ch_email');
            $chapter->po_box = $request->input('ch_pobox');
            $chapter->additional_info = $request->input('ch_addinfo');
            $chapter->website_url = $website;
            $chapter->website_status = $request->input('ch_webstatus');
            $chapter->website_notes = $request->input('ch_webnotes');
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->primary_coordinator_id = $request->filled('ch_primarycor') ? $request->input('ch_primarycor') : $request->input('ch_hid_primarycor');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');

            $chapter->save();

            //Change Primary Coordinator Notifications//
            $chId = $chapter['id'];
            $emailData = $this->userController->loadEmailDetails($chId);
            $emailListChap = $emailData['emailListChap'];  // Full Board
            $emailListCoord = $emailData['emailListCoord'];  // Full Coordinaor List

            $pcDetails = DB::table('coordinators')
                ->select('email', 'first_name', 'last_name')
                ->where('is_active', '=', '1')
                ->where('id', $request->input('ch_primarycor'))
                ->get();

            $pcEmail = $pcDetails[0]->email;  //Primary Coordinator Email

            if ($request->input('ch_primarycor') != $request->input('ch_hid_primarycor')) {
                $mailData = [
                    'chapter_name' => $chapterInfoPre[0]->name,
                    'chapter_state' => $chState,
                    'ch_pre_fname' => $chapterInfoPre[0]->ch_pre_fname,
                    'ch_pre_lname' => $chapterInfoPre[0]->ch_pre_lname,
                    'ch_pre_email' => $chapterInfoPre[0]->ch_pre_email,
                    'name1' => $pcDetails[0]->first_name,
                    'name2' => $pcDetails[0]->last_name,
                    'email1' => $pcDetails[0]->email,
                ];

                //Chapter Notification//
                $to_email5 = $emailListChap;
                Mail::to($to_email5)
                    ->queue(new ChaptersPrimaryCoordinatorChange($mailData));

                //Primary Coordinator Notification//
                $to_email6 = $pcEmail;
                Mail::to($to_email6)
                    ->queue(new ChaptersPrimaryCoordinatorChangePCNotice($mailData));
            }

            //Website Notifications//
            $chId = $chapter['id'];
            $chPcid = $chPCId;
            $chConf = $chConfId;

            $emailData = $this->userController->loadConferenceCoord($chPcid);
            $to_CCemail = $emailData['cc_email'];

            if ($request->input('ch_webstatus') != $request->input('ch_hid_webstatus')) {

                $mailData = [
                    'chapter_name' => $chapterInfoPre[0]->name,
                    'chapter_state' => $chState,
                    'ch_website_url' => $website,
                ];

                if ($request->input('ch_webstatus') == 1) {
                    Mail::to($to_CCemail)
                        ->queue(new WebsiteAddNoticeAdmin($mailData));

                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new WebsiteAddNoticeChapter($mailData));
                }

                if ($request->input('ch_webstatus') == 2) {
                    Mail::to($to_CCemail)
                        ->queue(new WebsiteReviewNotice($mailData));
                }
            }

            //Update Chapter MailData//
            $chaperInfoUpd = DB::table('chapters')
                ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'st.state_short_name as state',
                    'chapters.conference_id as conference', 'chapters.primary_coordinator_id as cor_id')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('chapters.id', $chapterId)
                ->orderByDesc('chapters.id')
                ->get();

            $mailData = [
                'chapter_name' => $chaperInfoUpd[0]->name,
                'chapter_state' => $chaperInfoUpd[0]->state,
                'conference' => $corConfId,
                'chapterNameUpd' => $chaperInfoUpd[0]->name,
                'boundUpd' => $chaperInfoUpd[0]->territory,
                'chapstatusUpd' => $chaperInfoUpd[0]->status_id,
                'chapNoteUpd' => $chaperInfoUpd[0]->notes,
                'inConUpd' => $chaperInfoUpd[0]->inquiries_contact,
                'inNoteUpd' => $chaperInfoUpd[0]->inquiries_note,
                'chapemailUpd' => $chaperInfoUpd[0]->email,
                'poBoxUpd' => $chaperInfoUpd[0]->po_box,
                'addInfoUpd' => $chaperInfoUpd[0]->additional_info,
                'webUrlUpd' => $chaperInfoUpd[0]->website_url,
                'webStatusUpd' => $chaperInfoUpd[0]->website_status,
                'egroupUpd' => $chaperInfoUpd[0]->egroup,
                'cor_fnameUpd' => $chaperInfoUpd[0]->cor_f_name,
                'cor_lnameUpd' => $chaperInfoUpd[0]->cor_l_name,
                'chapterNamePre' => $chapterInfoPre[0]->name,
                'boundPre' => $chapterInfoPre[0]->territory,
                'chapstatusPre' => $chapterInfoPre[0]->status_id,
                'chapNotePre' => $chapterInfoPre[0]->notes,
                'inConPre' => $chapterInfoPre[0]->inquiries_contact,
                'inNotePre' => $chapterInfoPre[0]->inquiries_note,
                'chapemailPre' => $chapterInfoPre[0]->email,
                'poBoxPre' => $chapterInfoPre[0]->po_box,
                'addInfoPre' => $chapterInfoPre[0]->additional_info,
                'webUrlPre' => $chapterInfoPre[0]->website_url,
                'webStatusPre' => $chapterInfoPre[0]->website_status,
                'egroupPre' => $chapterInfoPre[0]->egroup,
                'cor_fnamePre' => $chapterInfoPre[0]->cor_f_name,
                'cor_lnamePre' => $chapterInfoPre[0]->cor_l_name,
                'updated_byUpd' => $chaperInfoUpd[0]->last_updated_date,
            ];

            //Primary Coordinator Notification//
            $to_email = $pc_email;

            if ($chaperInfoUpd[0]->name != $chapterInfoPre[0]->name || $chaperInfoUpd[0]->inquiries_contact != $chapterInfoPre[0]->inquiries_contact || $chaperInfoUpd[0]->inquiries_note != $chapterInfoPre[0]->inquiries_note ||
                    $chaperInfoUpd[0]->email != $chapterInfoPre[0]->email || $chaperInfoUpd[0]->po_box != $chapterInfoPre[0]->po_box || $chaperInfoUpd[0]->website_url != $chapterInfoPre[0]->website_url ||
                    $chaperInfoUpd[0]->website_status != $chapterInfoPre[0]->website_status || $chaperInfoUpd[0]->egroup != $chapterInfoPre[0]->egroup || $chaperInfoUpd[0]->territory != $chapterInfoPre[0]->territory ||
                    $chaperInfoUpd[0]->additional_info != $chapterInfoPre[0]->additional_info || $chaperInfoUpd[0]->status_id != $chapterInfoPre[0]->status_id || $chaperInfoUpd[0]->notes != $chapterInfoPre[0]->notes) {
                Mail::to($to_email)
                    ->queue(new ChaptersUpdatePrimaryCoorChapter($mailData));
            }

            //EIN Coor Notification//
            $to_email3 = 'jackie.mchenry@momsclub.org';

            if ($chaperInfoUpd[0]->name != $chapterInfoPre[0]->name) {

                Mail::to($to_email3)
                    ->queue(new ChapersUpdateEINCoor($mailData));
            }

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            echo $e->getMessage();
            exit();
            DB::rollback();
            // Log the error
            Log::error($e);

            return to_route('chapters.view', ['id' => $id])->with('fail', 'Something went wrong, Please try again..');
        }

        return to_route('chapters.view', ['id' => $id])->with('success', 'Chapter Details have been updated');
    }

    /**
     *Edit Chapter Board Information
     */
    public function editChapterBoard(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chId = $baseQuery['chId'];
        $chIsActive = $baseQuery['chIsActive'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chDocuments = $baseQuery['chDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $reviewComplete = $baseQuery['reviewComplete'];

        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $baseQuery['chapterStatus'];
        $websiteLink = $baseQuery['websiteLink'];

        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        $allStates = $baseQuery['allStates'];
        $websiteLink = $baseQuery['websiteLink'];

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'cdId' => $cdId, 'reviewComplete' => $reviewComplete, 'stateShortName' => $stateShortName,
            'chDetails' => $chDetails, 'websiteLink' => $websiteLink, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails,
            'cdConfId' => $cdConfId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'allStates' => $allStates, 'PresDetails' => $PresDetails,
            'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription
        ];

        return view('chapters.editboard')->with($data);
    }

    /**
     *Update Chapter Board Information
     */
    public function updateChapterBoard(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $userName =  $user->first_name.' '.$user->last_name;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;
        $lastupdatedDate = date('Y-m-d H:i:s');

        $baseQueryPre = $this->getChapterDetails($id);
        $chDetailsPre = $baseQueryPre['chDetails'];
        $PresDetailsPre = $baseQueryPre['PresDetails'];
        $AVPDetailsPre = $baseQueryPre['AVPDetails'];
        $MVPDetailsPre = $baseQueryPre['MVPDetails'];
        $TRSDetailsPre = $baseQueryPre['TRSDetails'];
        $SECDetailsPre = $baseQueryPre['SECDetails'];

        $chapter = Chapters::find($id);

        DB::beginTransaction();
        try {
                    $chapter->last_updated_by = $lastUpdatedBy;
                    $chapter->last_updated_date = date('Y-m-d H:i:s');
                    $chapter->save();

                    //President Info
                    if ($request->input('ch_pre_fname') != '' && $request->input('ch_pre_lname') != '' && $request->input('ch_pre_email') != '') {
                        $PREDetails = DB::table('boards')
                            ->select('id', 'user_id')
                            ->where('chapter_id', '=', $id)
                            ->where('board_position_id', '=', '1')
                            ->get();
                        if (count($PREDetails) != 0) {
                            $userId = $PREDetails[0]->user_id;
                            $boardId = $PREDetails[0]->id;

                            $user = User::find($userId);
                            $user->first_name = $request->input('ch_pre_fname');
                            $user->last_name = $request->input('ch_pre_lname');
                            $user->email = $request->input('ch_pre_email');
                            $user->updated_at = date('Y-m-d H:i:s');
                            $user->save();

                            DB::table('boards')
                                ->where('id', $boardId)
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
                        }
                    }
                    //AVP Info
                    $AVPDetails = DB::table('boards')
                        ->select('id', 'user_id')
                        ->where('chapter_id', '=', $id)
                        ->where('board_position_id', '=', '2')
                        ->get();
                    if (count($AVPDetails) != 0) {
                        $userId = $AVPDetails[0]->user_id;
                        $boardId = $AVPDetails[0]->id;
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
                            $user->updated_at = date('Y-m-d H:i:s');
                            $user->save();

                            DB::table('boards')
                                ->where('id', $boardId)
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
                                    'chapter_id' => $id,
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
                        ->select('id', 'user_id')
                        ->where('chapter_id', '=', $id)
                        ->where('board_position_id', '=', '3')
                        ->get();
                    if (count($MVPDetails) != 0) {
                        $userId = $MVPDetails[0]->user_id;
                        $boardId = $MVPDetails[0]->id;
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

                            DB::table('boards')
                                ->where('id', $boardId)
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
                                    'chapter_id' => $id,
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
                        ->select('id', 'user_id')
                        ->where('chapter_id', '=', $id)
                        ->where('board_position_id', '=', '4')
                        ->get();
                    if (count($TRSDetails) != 0) {
                        $userId = $TRSDetails[0]->user_id;
                        $boardId = $TRSDetails[0]->id;
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

                            DB::table('boards')
                                ->where('id', $boardId)
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
                                    'chapter_id' => $id,
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
                        ->select('id', 'user_id')
                        ->where('chapter_id', '=', $id)
                        ->where('board_position_id', '=', '5')
                        ->get();
                    if (count($SECDetails) != 0) {
                        $userId = $SECDetails[0]->user_id;
                        $boardId = $SECDetails[0]->id;
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

                            DB::table('boards')
                                ->where('id', $boardId)
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
                                    'chapter_id' => $id,
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

                    //Update Chapter MailData//
                    $baseQueryUpd = $this->getChapterDetails($id);
                    $chDetailsUpd = $baseQueryUpd['chDetails'];
                    $stateShortName = $baseQueryUpd['stateShortName'];
                    $chConfId = $baseQueryUpd['chConfId'];
                    $chPcId = $baseQueryUpd['chPcId'];
                    $PresDetailsUpd = $baseQueryUpd['PresDetails'];
                    $AVPDetailsUpd = $baseQueryUpd['AVPDetails'];
                    $MVPDetailsUpd = $baseQueryUpd['MVPDetails'];
                    $TRSDetailsUpd = $baseQueryUpd['TRSDetails'];
                    $SECDetailsUpd = $baseQueryUpd['SECDetails'];

                    $mailDataPres = [
                            'chapter_name' => $chDetailsUpd->name,
                            'chapter_state' => $stateShortName,
                            'conference' => $chConfId,
                            'updated_byUpd' => $lastUpdatedBy,
                            'updated_byPre' => $lastupdatedDate,

                            'chapfnamePre' => $PresDetailsPre->first_name,
                            'chaplnamePre' => $PresDetailsPre->last_name,
                            'chapteremailPre' => $PresDetailsPre->email,
                            'phonePre' => $PresDetailsPre->phone,
                            'streetPre' => $PresDetailsPre->street,
                            'cityPre' => $PresDetailsPre->city,
                            'statePre' => $PresDetailsPre->state,
                            'zipPre' => $PresDetailsPre->zip,

                            'chapfnameUpd' => $PresDetailsUpd->first_name,
                            'chaplnameUpd' => $PresDetailsUpd->last_name,
                            'chapteremailUpd' => $PresDetailsUpd->email,
                            'phoneUpd' => $PresDetailsUpd->phone,
                            'streetUpd' => $PresDetailsUpd->street,
                            'cityUpd' => $PresDetailsUpd->city,
                            'stateUpd' => $PresDetailsUpd->state,
                            'zipUpd' => $PresDetailsUpd->zip,
                        ];

                         $mailData = array_merge($mailDataPres);
                        if ($AVPDetailsUpd !== null) {
                            $mailDataAvp = ['avpfnameUpd' => $AVPDetailsUpd->first_name,
                                'avplnameUpd' => $AVPDetailsUpd->last_name,
                                'avpemailUpd' => $AVPDetailsUpd->email, ];
                            $mailData = array_merge($mailData, $mailDataAvp);
                        } else {
                            $mailDataAvp = ['avpfnameUpd' => '',
                                'avplnameUpd' => '',
                                'avpemailUpd' => '', ];
                            $mailData = array_merge($mailData, $mailDataAvp);
                        }
                        if ($MVPDetailsUpd !== null) {
                            $mailDataMvp = ['mvpfnameUpd' => $MVPDetailsUpd->first_name,
                                'mvplnameUpd' => $MVPDetailsUpd->last_name,
                                'mvpemailUpd' => $MVPDetailsUpd->email, ];
                            $mailData = array_merge($mailData, $mailDataMvp);
                        } else {
                            $mailDataMvp = ['mvpfnameUpd' => '',
                                'mvplnameUpd' => '',
                                'mvpemailUpd' => '', ];
                            $mailData = array_merge($mailData, $mailDataMvp);
                        }
                        if ($TRSDetailsUpd !== null) {
                            $mailDatatres = ['tresfnameUpd' => $TRSDetailsUpd->first_name,
                                'treslnameUpd' => $TRSDetailsUpd->last_name,
                                'tresemailUpd' => $TRSDetailsUpd->email, ];
                            $mailData = array_merge($mailData, $mailDatatres);
                        } else {
                            $mailDatatres = ['tresfnameUpd' => '',
                                'treslnameUpd' => '',
                                'tresemailUpd' => '', ];
                            $mailData = array_merge($mailData, $mailDatatres);
                        }
                        if ($SECDetailsUpd !== null) {
                            $mailDataSec = ['secfnameUpd' => $SECDetailsUpd->first_name,
                                'seclnameUpd' => $SECDetailsUpd->last_name,
                                'secemailUpd' => $SECDetailsUpd->email, ];
                            $mailData = array_merge($mailData, $mailDataSec);
                        } else {
                            $mailDataSec = ['secfnameUpd' => '',
                                'seclnameUpd' => '',
                                'secemailUpd' => '', ];
                            $mailData = array_merge($mailData, $mailDataSec);
                        }

                        if ($AVPDetailsPre !== null) {
                            $mailDataAvpp = ['avpfnamePre' => $AVPDetailsPre->first_name,
                                'avplnamePre' => $AVPDetailsPre->last_name,
                                'avpemailPre' => $AVPDetailsPre->email, ];
                            $mailData = array_merge($mailData, $mailDataAvpp);
                        } else {
                            $mailDataAvpp = ['avpfnamePre' => '',
                                'avplnamePre' => '',
                                'avpemailPre' => '', ];
                            $mailData = array_merge($mailData, $mailDataAvpp);
                        }
                        if ($MVPDetailsPre !== null) {
                            $mailDataMvpp = ['mvpfnamePre' => $MVPDetailsPre->first_name,
                                'mvplnamePre' => $MVPDetailsPre->last_name,
                                'mvpemailPre' => $MVPDetailsPre->email, ];
                            $mailData = array_merge($mailData, $mailDataMvpp);
                        } else {
                            $mailDataMvpp = ['mvpfnamePre' => '',
                                'mvplnamePre' => '',
                                'mvpemailPre' => '', ];
                            $mailData = array_merge($mailData, $mailDataMvpp);
                        }
                        if ($TRSDetailsPre !== null) {
                            $mailDatatresp = ['tresfnamePre' => $TRSDetailsPre->first_name,
                                'treslnamePre' => $TRSDetailsPre->last_name,
                                'tresemailPre' => $TRSDetailsPre->email, ];
                            $mailData = array_merge($mailData, $mailDatatresp);
                        } else {
                            $mailDatatresp = ['tresfnamePre' => '',
                                'treslnamePre' => '',
                                'tresemailPre' => '', ];
                            $mailData = array_merge($mailData, $mailDatatresp);
                        }
                        if ($SECDetailsPre !== null) {
                            $mailDataSecp = ['secfnamePre' => $SECDetailsPre->first_name,
                                'seclnamePre' => $SECDetailsPre->last_name,
                                'secemailPre' => $SECDetailsPre->email, ];
                            $mailData = array_merge($mailData, $mailDataSecp);
                        } else {
                            $mailDataSecp = ['secfnamePre' => '',
                                'seclnamePre' => '',
                                'secemailPre' => '', ];
                            $mailData = array_merge($mailData, $mailDataSecp);
                        }

                    // //Primary Coordinator Notification//
                    $pc_email = Coordinators::find($chPcId);
                    $to_email = $pc_email;

                    if ($chDetailsUpd->name != $chDetailsPre->name || $PresDetailsUpd->bor_email != $PresDetailsPre->bor_email || $PresDetailsUpd->street_address != $PresDetailsPre->street_address || $PresDetailsUpd->city != $PresDetailsPre->city ||
                            $PresDetailsUpd->state != $PresDetailsPre->state || $PresDetailsUpd->first_name != $PresDetailsPre->first_name || $PresDetailsUpd->last_name != $PresDetailsPre->last_name ||
                            $PresDetailsUpd->zip != $PresDetailsPre->zip || $PresDetailsUpd->phone != $PresDetailsPre->phone || $PresDetailsUpd->inquiries_contact != $PresDetailsPre->inquiries_contact ||
                            $PresDetailsUpd->ein != $chDetailsPre->ein || $chDetailsUpd->ein_letter_path != $chDetailsPre->ein_letter_path || $PresDetailsUpd->inquiries_note != $PresDetailsPre->inquiries_note ||
                            $chDetailsUpd->email != $chDetailsPre->email || $chDetailsUpd->po_box != $chDetailsPre->po_box || $chDetailsUpd->website_url != $chDetailsPre->website_url ||
                            $chDetailsUpd->website_status != $chDetailsPre->website_status || $chDetailsUpd->egroup != $chDetailsPre->egroup || $chDetailsUpd->territory != $chDetailsPre->territory ||
                            $chDetailsUpd->additional_info != $chDetailsPre->additional_info || $chDetailsUpd->status_id != $chDetailsPre->status_id || $chDetailsUpd->notes != $chDetailsPre->notes ||
                            $mailDataAvpp['avpfnamePre'] != $mailDataAvp['avpfnameUpd'] || $mailDataAvpp['avplnamePre'] != $mailDataAvp['avplnameUpd'] || $mailDataAvpp['avpemailPre'] != $mailDataAvp['avpemailUpd'] ||
                            $mailDataMvpp['mvpfnamePre'] != $mailDataMvp['mvpfnameUpd'] || $mailDataMvpp['mvplnamePre'] != $mailDataMvp['mvplnameUpd'] || $mailDataMvpp['mvpemailPre'] != $mailDataMvp['mvpemailUpd'] ||
                            $mailDatatresp['tresfnamePre'] != $mailDatatres['tresfnameUpd'] || $mailDatatresp['treslnamePre'] != $mailDatatres['treslnameUpd'] || $mailDatatresp['tresemailPre'] != $mailDatatres['tresemailUpd'] ||
                            $mailDataSecp['secfnamePre'] != $mailDataSec['secfnameUpd'] || $mailDataSecp['seclnamePre'] != $mailDataSec['seclnameUpd'] || $mailDataSecp['secemailPre'] != $mailDataSec['secemailUpd']) {

                        Mail::to($to_email)
                            ->queue(new ChaptersUpdatePrimaryCoorBoard($mailData));
                    }

                    // //List Admin Notification//
                    $to_email2 = 'listadmin@momsclub.org';

                    if ($PresDetailsUpd->email != $PresDetailsPre->email || $PresDetailsUpd->email != $PresDetailsPre->email || $mailDataAvpp['avpemailPre'] != $mailDataAvp['avpemailUpd'] ||
                                $mailDataMvpp['mvpemailPre'] != $mailDataMvp['mvpemailUpd'] || $mailDatatresp['tresemailPre'] != $mailDatatres['tresemailUpd'] ||
                                $mailDataSecp['secemailPre'] != $mailDataSec['secemailUpd']) {

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

                    return to_route('chapters.view', ['id' => $id])->with('fail', 'Something went wrong, Please try again..');
                }

                return to_route('chapters.view', ['id' => $id])->with('success', 'Chapter Details have been updated');
            }

    /**
     *Edit Chapter EIN Notes
     */
    public function editChapterIRS(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        // $corDetails = User::find($request->user()->id)->coordinator;
        $corDetails = DB::table('coordinators as cd')
            ->select('cd.id', 'cd.conference_id', 'cd.region_id', 'cd.position_id')
            ->where('cd.user_id', '=', $userId)
            ->get();

        $coordId = $corDetails[0]->id;
        $corConfId = $corDetails[0]->conference_id;
        $corRegId = $corDetails[0]->region_id;
        $positionid = $corDetails[0]->position_id;

        $financial_report_array = FinancialReport::find($id);

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id',
                'ct.name as countryname', 'st.state_short_name as statename', 'cf.conference_description as confname', 'rg.long_name as regname', 'mo.month_long_name as startmonth')
            ->join('country as ct', 'ch.country_short_name', '=', 'ct.short_name')
            ->join('state as st', 'ch.state_id', '=', 'st.id')
            ->join('conference as cf', 'ch.conference_id', '=', 'cf.id')
            ->join('region as rg', 'ch.region_id', '=', 'rg.id')
            ->leftJoin('month as mo', 'ch.start_month_id', '=', 'mo.id')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            // ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();

        $chConfId = $chapterList[0]->conference_id;
        $chRegId = $chapterList[0]->region_id;
        $chPCid = $chapterList[0]->primary_coordinator_id;

        // Load Active Status for Active/Zapped Visibility
        $chIsActive = $chapterList[0]->is_active;

        $primaryCoordinatorList = DB::table('chapters as ch')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos', 'pos2.short_title as sec_pos')
            ->join('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->join('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
            ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
            ->where(function ($query) use ($chRegId, $chConfId) {
                $query->where('cd.region_id', '=', $chRegId)
                    ->orWhere(function ($subQuery) use ($chConfId) {
                        $subQuery->where('cd.region_id', '=', 0)
                            ->where('cd.conference_id', $chConfId);
                    });
            })
            ->where('cd.position_id', '<=', '7')
            ->where('cd.position_id', '>=', '1')
            ->where('cd.is_active', '=', '1')
            ->groupBy('cd.id', 'cd.first_name', 'cd.last_name', 'cp.short_title', 'pos2.short_title')
            ->orderBy('cd.position_id')
            ->orderBy('cd.first_name')
            ->get();

        $chConfId = $chapterList[0]->conference_id;
        $chRegId = $chapterList[0]->region_id;
        $chPCid = $chapterList[0]->primary_coordinator_id;

        $webStatusArr = ['0' => 'Website Not Linked', '1' => 'Website Linked', '2' => 'Add Link Requested', '3' => 'Do Not Link'];
        $chapterStatusArr = ['1' => 'Operating OK', '4' => 'On Hold Do not Refer', '5' => 'Probation', '6' => 'Probation Do Not Refer'];

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'positionid' => $positionid, 'coordId' => $coordId, 'financial_report_array' => $financial_report_array,
            'chapterList' => $chapterList, 'webStatusArr' => $webStatusArr, 'chapterStatusArr' => $chapterStatusArr,
            'primaryCoordinatorList' => $primaryCoordinatorList, 'corConfId' => $corConfId, 'chConfId' => $chConfId, 'chPCid' => $chPCid];

        return view('chapters.editirs')->with($data);
    }

    /**
     *Update Chapter EIN Notes
     */
    public function updateChapterIRS(Request $request, $id): RedirectResponse
    {
        $chapterId = $id;
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $chapter = Chapters::find($chapterId);
        DB::beginTransaction();
        try {
            $chapter->ein_letter = $request->has('ch_ein_letter') ? 1 : 0;
            $chapter->ein_notes = $request->input('ch_einnotes');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');

            $chapter->save();

            $financial = FinancialReport::find($chapterId);
            $financial->check_current_990N_verified_IRS = $request->has('check_current_990N_verified_IRS') ? 1 : 0;
            $financial->check_current_990N_notes = $request->input('check_current_990N_notes');

            $financial->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            echo $e->getMessage();
            exit();
            DB::rollback();
            // Log the error
            Log::error($e);

            return to_route('chapters.editirs', ['id' => $id])->with('fail', 'Something went wrong, Please try again..');
        }

        return to_route('chapters.editirs', ['id' => $id])->with('success', 'Chapter IRS Information has been updated');
    }

    /**
     * Display the Website Details
     */
    public function showChapterWebsite(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $websiteList = $baseQuery['query']->get();

        $data = ['websiteList' => $websiteList];

        return view('chapters.chapwebsite')->with($data);
    }


    /**
     * Display the Social Media Information
     */
    public function showRptSocialMedia(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();

        $data = ['chapterList' => $chapterList, 'corId' => $cdId];

        return view('chapreports.chaprptsocialmedia')->with($data);
    }


    /**
     *Edit Website & Social Information
     */
    public function editChapterWebsite(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chId = $baseQuery['chId'];
        $chIsActive = $baseQuery['chIsActive'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chDocuments = $baseQuery['chDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $reviewComplete = $baseQuery['reviewComplete'];

        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $baseQuery['chapterStatus'];
        $websiteLink = $baseQuery['websiteLink'];

        $allWebLinks = Website::all();  // Full List for Dropdown Menu

        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];



        // $user = User::find($request->user()->id);
        // $userId = $user->id;

        // // $corDetails = User::find($request->user()->id)->coordinator;
        // $corDetails = DB::table('coordinators as cd')
        //     ->select('cd.id', 'cd.conference_id', 'cd.region_id', 'cd.position_id')
        //     ->where('cd.user_id', '=', $userId)
        //     ->get();

        // $coordId = $corDetails[0]->id;
        // $corConfId = $corDetails[0]->conference_id;
        // $corRegId = $corDetails[0]->region_id;
        // $positionid = $corDetails[0]->position_id;

        // $financial_report_array = FinancialReport::find($id);
        // if ($financial_report_array) {
        //     $reviewComplete = $financial_report_array['review_complete'];
        // } else {
        //     $reviewComplete = null;
        // }

        // $chapterList = DB::table('chapters as ch')
        //     ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id',
        //         'ct.name as countryname', 'st.state_short_name as statename', 'cf.conference_description as confname', 'rg.long_name as regname', 'mo.month_long_name as startmonth')
        //     ->join('country as ct', 'ch.country_short_name', '=', 'ct.short_name')
        //     ->join('state as st', 'ch.state_id', '=', 'st.id')
        //     ->join('conference as cf', 'ch.conference_id', '=', 'cf.id')
        //     ->join('region as rg', 'ch.region_id', '=', 'rg.id')
        //     ->leftJoin('month as mo', 'ch.start_month_id', '=', 'mo.id')
        //     ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
        //     // ->where('ch.is_active', '=', '1')
        //     ->where('ch.id', '=', $id)
        //     ->where('bd.board_position_id', '=', '1')
        //     ->get();

        // $chConfId = $chapterList[0]->conference_id;
        // $chRegId = $chapterList[0]->region_id;
        // $chPCid = $chapterList[0]->primary_coordinator_id;

        // Load Active Status for Active/Zapped Visibility
        // $chIsActive = $chapterList[0]->is_active;

        // Load Board and Coordinators for Sending Email
        // $chId = $chapterList[0]->id;
        // $emailData = $this->userController->loadEmailDetails($chId);
        // $emailListChap = $emailData['emailListChap'];
        //     $emailListCoord = $emailData['emailListCoord'];

        // $primaryCoordinatorList = DB::table('chapters as ch')
        //     ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos', 'pos2.short_title as sec_pos')
        //     ->join('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
        //     ->join('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
        //     ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
        //     ->where(function ($query) use ($chRegId, $chConfId) {
        //         $query->where('cd.region_id', '=', $chRegId)
        //             ->orWhere(function ($subQuery) use ($chConfId) {
        //                 $subQuery->where('cd.region_id', '=', 0)
        //                     ->where('cd.conference_id', $chConfId);
        //             });
        //     })
        //     ->where('cd.position_id', '<=', '7')
        //     ->where('cd.position_id', '>=', '1')
        //     ->where('cd.is_active', '=', '1')
        //     ->groupBy('cd.id', 'cd.first_name', 'cd.last_name', 'cp.short_title', 'pos2.short_title')
        //     ->orderBy('cd.position_id')
        //     ->orderBy('cd.first_name')
        //     ->get();

        // $chConfId = $chapterList[0]->conference_id;
        // $chRegId = $chapterList[0]->region_id;
        // $chPCid = $chapterList[0]->primary_coordinator_id;

        // $webStatusArr = ['0' => 'Website Not Linked', '1' => 'Website Linked', '2' => 'Add Link Requested', '3' => 'Do Not Link'];
        // $chapterStatusArr = ['1' => 'Operating OK', '4' => 'On Hold Do not Refer', '5' => 'Probation', '6' => 'Probation Do Not Refer'];

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'cdId' => $cdId, 'stateShortName' => $stateShortName, 'conferenceDescription' => $conferenceDescription,
            'chDetails' => $chDetails, 'allWebLinks' => $allWebLinks,  'emailListChap' => $emailListChap, 'emailListCoord' => $emailListCoord,
            'cdConfId' => $cdConfId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'regionLongName' => $regionLongName
        ];

        return view('chapters.editwebsite')->with($data);
    }

    /**
     *Update Website & Social Media Information
     */
    public function updateChapterWebsite(Request $request, $id): RedirectResponse
    {
        $chapterId = $id;
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $chapterInfoPre = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'st.state_short_name as statename',
                'chapters.conference_id as conference', 'chapters.primary_coordinator_id as cor_id', 'bd.first_name as ch_pre_fname', 'bd.last_name as ch_pre_lname',
                'bd.email as ch_pre_email')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
            ->where('chapters.is_Active', '=', '1')
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

        $chapter = Chapters::find($chapterId);
        DB::beginTransaction();
        try {
            $chapter->website_url = $website;
            $chapter->website_status = $request->input('ch_webstatus');
            $chapter->website_notes = $request->input('ch_webnotes');
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');

            $chapter->save();

            //Change Primary Coordinator Notifications//
            $chId = $chapter['id'];
            $emailData = $this->userController->loadEmailDetails($chId);
            $emailListChap = $emailData['emailListChap'];  // Full Board
            $emailListCoord = $emailData['emailListCoord'];  // Full Coordinaor List

            //Website Notifications//
            $chId = $chapter['id'];
            $chPcid = $chPCId;
            $chConf = $chConfId;

            $emailData = $this->userController->loadConferenceCoord($chPcid);
            $to_CCemail = $emailData['cc_email'];

            //Update Chapter MailData//
            $chaperInfoUpd = DB::table('chapters')
                ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'st.state_short_name as state',
                    'chapters.conference_id as conference', 'chapters.primary_coordinator_id as cor_id')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('chapters.id', $chapterId)
                ->orderByDesc('chapters.id')
                ->get();

            if ($request->input('ch_webstatus') != $request->input('ch_hid_webstatus')) {
                $mailData = [
                    'chapter_name' => $chaperInfoUpd[0]->name,
                    'chapter_state' => $chaperInfoUpd[0]->state,
                    'ch_website_url' => $website,
                ];

                if ($request->input('ch_webstatus') == 1) {
                    Mail::to($to_CCemail)
                        ->queue(new WebsiteAddNoticeAdmin($mailData));

                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new WebsiteAddNoticeChapter($mailData));
                }

                if ($request->input('ch_webstatus') == 2) {
                    Mail::to($to_CCemail)
                        ->queue(new WebsiteReviewNotice($mailData));
                }
            }

            $mailData = [
                'chapter_name' => $chaperInfoUpd[0]->name,
                'chapter_state' => $chaperInfoUpd[0]->state,
                'webUrlUpd' => $chaperInfoUpd[0]->website_url,
                'webStatusUpd' => $chaperInfoUpd[0]->website_status,
                'webUrlPre' => $chapterInfoPre[0]->website_url,
                'webStatusPre' => $chapterInfoPre[0]->website_status,
                'updated_byUpd' => $chaperInfoUpd[0]->last_updated_date,
            ];

            //Primary Coordinator Notification//
            $to_email = $pc_email;

            if ($chaperInfoUpd[0]->website_url != $chapterInfoPre[0]->website_url || $chaperInfoUpd[0]->website_status != $chapterInfoPre[0]->website_status) {
                Mail::to($to_email)
                    ->queue(new WebsiteUpdatePrimaryCoor($mailData));
            }

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            echo $e->getMessage();
            exit();
            DB::rollback();
            // Log the error
            Log::error($e);

            return to_route('chapters.editwebsite', ['id' => $id])->with('fail', 'Something went wrong, Please try again..');
        }

        return to_route('chapters.editwebsite', ['id' => $id])->with('success', 'Chapter Website & Social Meida has been updated');
    }

    /**
     * BoardList
     */
    public function showChapterBoardlist(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $activeChapterList = $baseQuery['query']->get();

        $countList = count($activeChapterList);
        $data = ['countList' => $countList, 'activeChapterList' => $activeChapterList];

        return view('chapters.chapboardlist')->with($data);
    }

      /**
     * ReRegistration List
     */
    public function showChapterReRegistration(Request $request): View
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $currentYear = date('Y');
        $currentMonth = date('m');
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        $request->session()->put('corconfid', $corConfId);
        $request->session()->put('corregid', $corRegId);

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
            // Load Reporting Tree
            $coordinatorData = $this->userController->loadReportingTree($corId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = DB::table('chapters as ch')
            ->select(
                'ch.id', 'ch.members_paid_for', 'ch.notes', 'ch.name', 'ch.state_id', 'ch.reg_notes', 'ch.next_renewal_year', 'ch.dues_last_paid', 'ch.start_month_id',
                'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name', 'db.month_short_name', 'cf.short_name as conf', 'rg.short_name as reg',
                'ct.name as countryname', 'st.state_long_name as statename', 'cf.conference_description as confname', 'rg.long_name as regname')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'ch.id')
            ->join('country as ct', 'ch.country_short_name', '=', 'ct.short_name')
            ->join('state as st', 'ch.state_id', '=', 'st.id')
            ->join('conference as cf', 'ch.conference_id', '=', 'cf.id')
            ->join('region as rg', 'ch.region_id', '=', 'rg.id')
            ->leftJoin('month as db', 'ch.start_month_id', '=', 'db.id')
            ->where('ch.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1');

        if ($conditions['founderCondition']) {

        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('ch.conference_id', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('ch.region_id', '=', $corRegId);
        } else {
            $baseQuery->whereIn('ch.primary_coordinator_id', $inQryArr);
        }

        // If checkbox is not checked, apply the additional year and month filtering
        if (! isset($_GET['check']) || $_GET['check'] !== 'yes') {
            $baseQuery->where(function ($query) use ($currentYear, $currentMonth) {
                $query->where('ch.next_renewal_year', '<', $currentYear)
                    ->orWhere(function ($query) use ($currentYear, $currentMonth) {
                        $query->where('ch.next_renewal_year', '=', $currentYear)
                            ->where('ch.start_month_id', '<=', $currentMonth);
                    });
            });
        }

        // Apply sorting based on checkbox status -- show All Chapters
        if (isset($_GET['check']) && $_GET['check'] == 'yes') {
            $checkBoxStatus = 'checked';
            $baseQuery
                ->orderBy('ch.conference_id')
                ->orderBy('st.state_short_name')
                ->orderBy('ch.name');
        } else {
            $checkBoxStatus = '';
            $baseQuery
                ->orderByDesc('ch.next_renewal_year')
                ->orderByDesc('ch.start_month_id')
                ->orderBy('ch.conference_id')
                ->orderBy('st.state_short_name')
                ->orderBy('ch.name');
        }

        $reChapterList = $baseQuery->get();

        $countList = count($reChapterList);

        $data = ['countList' => $countList, 'reChapterList' => $reChapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

        return view('chapters.chapreregistration')->with($data);
    }

    /**
     * ReRegistration Reminders Auto Send
     */
    public function createChapterReRegistrationReminder(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corName = $corDetails['first_name'].' '.$corDetails['last_name'];

        $month = date('m');
        $year = date('Y');
        $monthRangeStart = $month;
        $monthRangeEnd = $month - 1;
        $lastYear = $year - 1;
        $thisyear = $year;

        if ($month == 1) {
            $monthRangeStart = 12;
            $lastYear = $lastYear - 1;
        }
        if ($month == 1) {
            $monthRangeEnd = 12;
            $thisyear = $year - 1;
        }

        $rangeStartDate = Carbon::create($lastYear, $monthRangeStart, 1);
        $rangeEndDate = Carbon::create($thisyear, $monthRangeEnd, 1)->endOfMonth();

        // Convert $month to words
        $monthInWords = Carbon::createFromFormat('m', $month)->format('F');

        // Format dates as "mm-dd-yyyy"
        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        $chapters = Chapters::select('chapters.*', 'chapters.name as chapter_name', 'state.state_short_name as chapter_state',
            'chapters.primary_coordinator_id as pcid', 'chapters.email as ch_email', 'chapters.start_month_id as start_month', )
            ->join('state', 'chapters.state_id', '=', 'state.id')
            ->where('chapters.conference_id', $corConfId)
            ->where('chapters.start_month_id', $month)
            ->where('chapters.next_renewal_year', $year)
            ->where('chapters.is_active', 1)
            ->get();

        if ($chapters->isEmpty()) {
            return redirect()->back()->with('info', 'There are no Chapters with Registrations Due.');
        }

        $chapterIds = [];
        $chapterEmails = [];
        $coordinatorEmails = [];
        $mailData = [];

        foreach ($chapters as $chapter) {
            $chapterIds[] = $chapter->id;

            if ($chapter->name) {
                $emailData = $this->userController->loadEmailDetails($chapter->id);
                $emailListChap = $emailData['emailListChap'];
                $emailListCoord = $emailData['emailListCoord'];

                $chapterEmails[$chapter->name] = $emailListChap;
                $coordinatorEmails[$chapter->name] = $emailListCoord;
            }

            $chapterState = $chapter->chapter_state;

            $mailData[$chapter->chapter_name] = [
                'chapterName' => $chapter->chapter_name,
                'chapterState' => $chapterState,
                'startRange' => $rangeStartDateFormatted,
                'endRange' => $rangeEndDateFormatted,
                'startMonth' => $monthInWords,
            ];

        }

        foreach ($mailData as $chapterName => $data) {
            $to_email = $chapterEmails[$chapterName] ?? [];
            $cc_email = $coordinatorEmails[$chapterName] ?? [];

            if (! empty($to_email)) {
                Mail::to($to_email)
                    ->cc($cc_email)
                    ->queue(new PaymentsReRegReminder($data));
            }
        }

        try {
            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            echo $e->getMessage();
            exit();
            // Log the error
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/chapter/reregistration')->with('success', 'Re-Registration Reminders have been successfully sent.');
    }

    /**
     * ReRegistration Late Notices Auto Send
     */
    public function createChapterReRegistrationLateReminder(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corName = $corDetails['first_name'].' '.$corDetails['last_name'];

        $month = date('m');
        $year = date('Y');
        $lastMonth = $month - 1;
        $monthRangeStart = $month - 1;
        $monthRangeEnd = $month - 2;
        $lastYear = $year - 1;
        $thisyear = $year;

        if ($month == 1) {
            $monthRangeStart = 11;
            $lastYear = $lastYear - 1;
        } elseif ($month == 2) {
            $monthRangeStart = 12;
            $lastYear = $lastYear - 1;
        }

        if ($month == 1) {
            $monthRangeEnd = 11;
            $thisyear = $year - 1;
        } elseif ($month == 2) {
            $monthRangeEnd = 12;
            $thisyear = $year - 1;
        }

        $rangeStartDate = Carbon::create($lastYear, $monthRangeStart, 1);
        $rangeEndDate = Carbon::create($thisyear, $monthRangeEnd, 1)->endOfMonth();

        // Convert $month to words
        $monthInWords = Carbon::createFromFormat('m', $month)->format('F');
        $lastMonthInWords = Carbon::createFromFormat('m', $lastMonth)->format('F');

        // Format dates as "mm-dd-yyyy"
        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        $chapters = Chapters::select('chapters.*', 'chapters.name as chapter_name', 'state.state_short_name as chapter_state', 'boards.email as bor_email',
            'chapters.primary_coordinator_id as pcid', 'chapters.email as ch_email', 'chapters.start_month_id as start_month',
            'boards.board_position_id')
            ->join('state', 'chapters.state_id', '=', 'state.id')
            ->join('boards', 'chapters.id', '=', 'boards.chapter_id')
            ->whereIn('boards.board_position_id', [1, 2, 3, 4, 5])
            ->where('chapters.conference_id', $corConfId)
            ->where(function ($query) use ($month, $year) {
                if ($month == 1) {
                    // January, so get chapters with December start_month_id
                    $query->where('chapters.start_month_id', 12)
                        ->where('chapters.next_renewal_year', $year - 1);
                } else {
                    // Any other month, get chapters with $month - 1 start_month_id
                    $query->where('chapters.start_month_id', $month - 1)
                        ->where('chapters.next_renewal_year', $year);
                }
            })
            ->where('chapters.is_active', 1)
            ->get();

        if ($chapters->isEmpty()) {
            return redirect()->back()->with('info', 'There are no Chapters with Late Registrations Due.');
        }

        $chapterIds = [];
        $chapterEmails = [];
        $coordinatorEmails = [];
        $mailData = [];

        foreach ($chapters as $chapter) {
            $chapterIds[] = $chapter->id;

            if ($chapter->name) {
                $emailData = $this->userController->loadEmailDetails($chapter->id);
                $emailListChap = $emailData['emailListChap'];
                $emailListCoord = $emailData['emailListCoord'];

                $chapterEmails[$chapter->name] = $emailListChap;
                $coordinatorEmails[$chapter->name] = $emailListCoord;
            }

            $chapterState = $chapter->chapter_state;

            $mailData[$chapter->chapter_name] = [
                'chapterName' => $chapter->chapter_name,
                'chapterState' => $chapterState,
                'startRange' => $rangeStartDateFormatted,
                'endRange' => $rangeEndDateFormatted,
                'startMonth' => $lastMonthInWords,
                'dueMonth' => $monthInWords,
            ];

        }

        foreach ($mailData as $chapterName => $data) {
            $to_email = $chapterEmails[$chapterName] ?? [];
            $cc_email = $coordinatorEmails[$chapterName] ?? [];

            if (! empty($to_email)) {
                Mail::to($to_email)
                    ->cc($cc_email)
                    ->queue(new PaymentsReRegLate($data));
            }
        }

        try {
            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            echo $e->getMessage();
            exit();
            // Log the error
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/chapter/reregistration')->with('success', 'Re-Registration Late Reminders have been successfully sent.');
    }

    /**
     * View Doantions List
     */
    public function showRptDonations(Request $request): View
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
            // Load Reporting Tree
            $coordinatorData = $this->userController->loadReportingTree($corId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
            ->leftJoin('conference as cf', 'chapters.conference_id', '=', 'cf.id')
            ->leftJoin('region as rg', 'chapters.region_id', '=', 'rg.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1');

        if ($conditions['founderCondition']) {

        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('chapters.conference_id', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('chapters.region_id', '=', $corRegId);
        } else {
            $baseQuery->whereIn('chapters.primary_coordinator_id', $inQryArr);
        }

        if (isset($_GET['check']) && $_GET['check'] == 'yes') {
            $checkBoxStatus = 'checked';
            $baseQuery->where('chapters.primary_coordinator_id', '=', $corId)
                ->orderBy('st.state_short_name')
                ->orderBy('chapters.name');
        } else {
            $checkBoxStatus = '';
            $baseQuery->orderBy('st.state_short_name')
                ->orderBy('chapters.name');
        }

        $chapterList = $baseQuery->get();

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

        return view('chapreports.chaprptdonations')->with($data);
    }

    /**
     * View the International M2M Doantions
     */
    public function showIntdonation(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);

        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
            ->leftJoin('conference as cf', 'chapters.conference_id', '=', 'cf.id')
            ->leftJoin('region as rg', 'chapters.region_id', '=', 'rg.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        $data = ['chapterList' => $chapterList];

        return view('international.intdonation')->with($data);
    }


     /**
     *Edit Chapter Information
     */
    public function editChapterPayment(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        // $corDetails = User::find($request->user()->id)->coordinator;
        $corDetails = DB::table('coordinators as cd')
            ->select('cd.id', 'cd.conference_id', 'cd.region_id', 'cd.position_id')
            ->where('cd.user_id', '=', $userId)
            ->get();

        $coordId = $corDetails[0]->id;
        $corConfId = $corDetails[0]->conference_id;
        $corRegId = $corDetails[0]->region_id;
        $positionid = $corDetails[0]->position_id;

        $financial_report_array = FinancialReport::find($id);
        if ($financial_report_array) {
            $reviewComplete = $financial_report_array['review_complete'];
        } else {
            $reviewComplete = null;
        }

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id',
                'ct.name as countryname', 'st.state_short_name as statename', 'cf.conference_description as confname', 'rg.long_name as regname', 'mo.month_long_name as startmonth',
                'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.conference_id as cor_confid')
            ->join('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->join('country as ct', 'ch.country_short_name', '=', 'ct.short_name')
            ->join('state as st', 'ch.state_id', '=', 'st.id')
            ->join('conference as cf', 'ch.conference_id', '=', 'cf.id')
            ->join('region as rg', 'ch.region_id', '=', 'rg.id')
            ->leftJoin('month as mo', 'ch.start_month_id', '=', 'mo.id')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            // ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();

        $chConfId = $chapterList[0]->conference_id;
        $chRegId = $chapterList[0]->region_id;
        $chPCid = $chapterList[0]->primary_coordinator_id;

        // Load Active Status for Active/Zapped Visibility
        $chIsActive = $chapterList[0]->is_active;

        $primaryCoordinatorList = DB::table('chapters as ch')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos', 'pos2.short_title as sec_pos')
            ->join('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->join('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
            ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
            ->where(function ($query) use ($chRegId, $chConfId) {
                $query->where('cd.region_id', '=', $chRegId)
                    ->orWhere(function ($subQuery) use ($chConfId) {
                        $subQuery->where('cd.region_id', '=', 0)
                            ->where('cd.conference_id', $chConfId);
                    });
            })
            ->where('cd.position_id', '<=', '7')
            ->where('cd.position_id', '>=', '1')
            ->where('cd.is_active', '=', '1')
            ->groupBy('cd.id', 'cd.first_name', 'cd.last_name', 'cp.short_title', 'pos2.short_title')
            ->orderBy('cd.position_id')
            ->orderBy('cd.first_name')
            ->get();

        $chConfId = $chapterList[0]->conference_id;
        $chRegId = $chapterList[0]->region_id;
        $chPCid = $chapterList[0]->primary_coordinator_id;

        $statusbWords = ['1' => 'Operating OK', '4' => 'On Hold Do not Refer', '5' => 'Probation', '6' => 'Probation Do Not Refer'];
        $chapterStatus = $chapterList[0]->status_id;
        $chapterStatusinWords = $statusbWords[$chapterStatus] ?? 'Status Unknown';

        $webStatusArr = ['0' => 'Website Not Linked', '1' => 'Website Linked', '2' => 'Add Link Requested', '3' => 'Do Not Link'];
        $chapterStatusArr = ['1' => 'Operating OK', '4' => 'On Hold Do not Refer', '5' => 'Probation', '6' => 'Probation Do Not Refer'];

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'positionid' => $positionid, 'coordId' => $coordId, 'reviewComplete' => $reviewComplete,
            'chapterList' => $chapterList, 'webStatusArr' => $webStatusArr, 'chapterStatusinWords' => $chapterStatusinWords,
            'primaryCoordinatorList' => $primaryCoordinatorList, 'corConfId' => $corConfId, 'chConfId' => $chConfId, 'chPCid' => $chPCid];

        return view('chapters.editpayment')->with($data);
    }

    /**
     *Update Chapter Information
     */
    public function updateChapterPayment(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $userName =  $user->first_name.' '.$user->last_name;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;

        $nextRenewalYear = $request->input('ch_nxt_renewalyear');
        $primaryCordEmail = $request->input('ch_pc_email');
        $boardPresEmail = $request->input('ch_pre_email');

        $chapter = Chapters::find($id);
        $chId = $chapter['id'];
        $emailData = $this->userController->loadEmailDetails($chId);
        $emailListChap = $emailData['emailListChap'];
        $emailListCoord = $emailData['emailListCoord'];

        $chapterEmails = $emailListChap;

        $to_email = $chapterEmails;
        $cc_email = $primaryCordEmail;

        $chapter = Chapters::find($id);

        DB::beginTransaction();
        try {
            if ($request->input('PaymentDate') != null) {
                $chapter->dues_last_paid = $request->input('PaymentDate');
                $chapter->members_paid_for = $request->input('MembersPaidFor');
                $chapter->reg_notes = $request->input('ch_regnotes');
                $chapter->next_renewal_year = $nextRenewalYear + 1;
                $chapter->last_updated_by = $lastUpdatedBy;
                $chapter->last_updated_date = date('Y-m-d H:i:s');
                $chapter->save();

                if ($request->input('ch_notify') == 'on') {
                    $mailData = [
                        'chapterName' => $request->input('ch_name'),
                        'chapterState' => $request->input('ch_state'),
                        'chapterPreEmail' => $request->input('ch_pre_email'),
                        'chapterDate' => $request->input('PaymentDate'),
                        'chapterMembers' => $request->input('MembersPaidFor'),
                        'cordFname' => $request->input('ch_pc_fname'),
                        'cordLname' => $request->input('ch_pc_lname'),
                        'cordConf' => $request->input('ch_pc_confid'),
                    ];

                    // Payment Thank You Email
                    Mail::to($to_email)
                        ->cc($cc_email)
                        ->queue(new PaymentsReRegChapterThankYou($mailData));
                }
            }

            if ($request->input('M2MPaymentDate') != null) {
                $chapter->m2m_date = $request->input('M2MPaymentDate');
                $chapter->members_paid_for = $request->input('M2MPayment');
                $chapter->last_updated_by = $lastUpdatedBy;
                $chapter->last_updated_date = date('Y-m-d H:i:s');
                $chapter->save();

                if ($request->input('ch_thanks') == 'on') {
                    $mailData = [
                        'chapterName' => $request->input('ch_name'),
                        'chapterState' => $request->input('ch_state'),
                        'chapterPreEmail' => $request->input('ch_pre_email'),
                        'chapterAmount' => $request->input('M2MPayment'),
                        'cordFname' => $request->input('ch_pc_fname'),
                        'cordLname' => $request->input('ch_pc_lname'),
                        'cordConf' => $request->input('ch_pc_confid'),
                    ];

                    //M2M Donation Thank You Email//
                    Mail::to($to_email)
                        ->cc($cc_email)
                        ->queue(new PaymentsM2MChapterThankYou($mailData));
                }
            }

            if ($request->input('SustainingPaymentDate') != null) {
                $chapter->sustaining_date = $request->input('SustainingPaymentDate');
                $chapter->sustaining_donation = $request->input('SustainingPayment');
                $chapter->last_updated_by = $lastUpdatedBy;
                $chapter->last_updated_date = date('Y-m-d H:i:s');
                $chapter->save();

                if ($request->input('ch_sustaining') == 'on') {
                    $mailData = [
                        'chapterName' => $request->input('ch_name'),
                        'chapterState' => $request->input('ch_state'),
                        'chapterPreEmail' => $request->input('ch_pre_email'),
                        'chapterTotal' => $request->input('SustainingPayment'),
                        'cordFname' => $request->input('ch_pc_fname'),
                        'cordLname' => $request->input('ch_pc_lname'),
                        'cordConf' => $request->input('ch_pc_confid'),
                    ];

                    //Sustaining Chapter Thank You Email//
                    Mail::to($to_email)
                        ->cc($cc_email)
                        ->queue(new PaymentsSustainingChapterThankYou($mailData));
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('chapters.editpayment', ['id' => $id])->with('fail', 'Something went wrong, Please try again..');
        }

        return to_route('chapters.editpayment', ['id' => $id])->with('success', 'Chapter Payments/Donations have been updated');
    }
}
