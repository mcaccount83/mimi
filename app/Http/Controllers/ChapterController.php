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
use App\Models\Boards;
use App\Models\Documents;
use App\Models\State;
use App\Models\FinancialReport;
use App\Models\FinancialReportAwards;
use App\Models\User;
use App\Models\Status;
use App\Models\Region;
use App\Models\Month;
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

        $baseQuery = Chapters::with(['state', 'conference', 'region', 'webLink', 'president', 'avp', 'mvp', 'treasurer', 'secretary', 'startMonth',
            'state', 'primaryCoordinator'])
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

        if (isset($_GET['check3']) && $_GET['check3'] == 'yes') {
            $checkBox3Status = 'checked';
            $baseQuery;
        } else {
            $checkBox3Status = '';
            $baseQuery
                ->orderByDesc('next_renewal_year')
                ->orderByDesc('start_month_id');
        }

        $baseQuery->orderBy(State::select('state_short_name')
            ->whereColumn('state.id', 'chapters.state_id'), 'asc')
            ->orderBy('chapters.name', 'asc');

        return ['query' => $baseQuery, 'checkBoxStatus' => $checkBoxStatus, 'checkBox3Status' => $checkBox3Status];

    }

     /**
     * Active Chapter Details Base Query
     */
    public function getChapterDetails($id)
    {
        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'documents', 'financialReport', 'startMonth', 'boards', 'primaryCoordinator'])->find($id);
        $chId = $chDetails->id;
        $chIsActive = $chDetails->is_active;
        $stateShortName = $chDetails->state?->state_short_name;
        $regionLongName = $chDetails->region?->long_name;
        $conferenceDescription = $chDetails->conference->conference_description;
        $chConfId = $chDetails->conference_id;
        $chRegId = $chDetails->region_id;
        $chPcId = $chDetails->primary_coordinator_id;

        $startMonthName = $chDetails->startMonth->month_long_name;
        $chapterStatus = $chDetails->status->chapter_status;
        $websiteLink = $chDetails->webLink->link_status ?? null;

        $chDocuments = $chDetails->documents;
        $submitted = $chDetails->documents->financial_report_received ?? null;
        $reviewComplete = $chDetails->documents->review_complete  ?? null;
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

        // Load Conference Coordinators for Sending Email
        $emailData = $this->userController->loadConferenceCoord($chPcId);
        $emailCC = $emailData['cc_email'];

        // //Primary Coordinator Notification//
        $pcDetails = Coordinators::find($chPcId);
        $emailPC = $pcDetails->email;

        // Load Report Reviewer Coordinator Dropdown List
        $pcDetails = $this->userController->loadPrimaryList($chRegId, $chConfId);

        return ['chDetails' => $chDetails, 'chIsActive' => $chIsActive, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription, 'chConfId' => $chConfId, 'chRegId' => $chRegId, 'chPcId' => $chPcId, 'chId' => $chId,
            'chDocuments' => $chDocuments, 'reviewComplete' => $reviewComplete, 'chFinancialReport' => $chFinancialReport, 'allAwards' => $allAwards,
            'PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
            'emailListChap' => $emailListChap, 'emailListCoord' => $emailListCoord, 'pcDetails' => $pcDetails, 'submitted' => $submitted,
            'allWebLinks' => $allWebLinks, 'allStatuses' => $allStatuses, 'allStates' => $allStates, 'emailCC' => $emailCC, 'emailPC' => $emailPC,
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
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, ];

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

        $data = ['inquiriesList' => $inquiriesList,];

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

        $data = ['inquiriesList' => $inquiriesList];

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
    public function addChapterNew(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;

        $allStates = State::all();  // Full List for Dropdown Menu
        $allRegions = Region::with('conference')  // Full List for Dropdown Menu based on Conference
            ->where('conference_id', $cdConfId)
            ->get();
        $allStatuses = Status::all();  // Full List for Dropdown Menu
        $chConfId = $cdConfId;

        $pcList = Coordinators::with(['displayPosition', 'secondaryPosition'])
                ->where('conference_id', $chConfId)
                ->whereBetween('position_id', [1, 7])
                ->where('is_active', 1)
                ->where('on_leave', '!=', '1')
                ->get();

        $pcDetails = $pcList->map(function ($coordinator) {
            return [
                'cid' => $coordinator->id,
                'cname' => "{$coordinator->first_name} {$coordinator->last_name}",
                'cpos' => $coordinator->displayPosition->short_title ?? 'No Position',
                'regid' => $coordinator->region_id,
            ];
        });

        $pcDetails = $pcDetails->unique('cid');  // Remove duplicates based on the 'cid' field

        $data = ['allRegions' => $allRegions, 'allStatuses' => $allStatuses, 'pcDetails' => $pcDetails, 'allStates' => $allStates

        ];

        return view('chapters.addnew')->with($data);
    }

    /**
     *Save New Chapter
     */
    public function updateChapterNew(Request $request): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;

        $conference = $cdConfId;
        $country = 'USA';
        $currentMonth = date('m');
        $currentYear = date('Y');

        $input = $request->all();

        DB::beginTransaction();
        try {
                $chapterId = Chapters::create([
                    'name' => $input['ch_name'],
                    'state_id' => $input['ch_state'],
                    'country_short_name' => $country,
                    'conference_id' => $conference,
                    'region_id' => $input['ch_region'],
                    'ein' => $input['ch_ein'],
                    'status_id' => $input['ch_status'],
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
                    'is_active' => 1,
                ])->id;

                $financialReport = FinancialReport::create([
                    'chapter_id' => $chapterId,
                ]);

                $documents = Documents::create([
                    'chapter_id' => $chapterId,
                ]);

            //President Info
            if (isset($input['ch_pre_fname']) && isset($input['ch_pre_lname']) && isset($input['ch_pre_email'])) {
                $userId = User::create([
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1,
                ])->id;

                $boardId = Boards::create([
                    'user_id' => $userId,
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
                    'is_active' => 1,
                ])->id;
            }

            //AVP Info
            if (isset($input['ch_avp_fname']) && isset($input['ch_avp_lname']) && isset($input['ch_avp_email'])) {
                $userId = User::create([
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1,
                ])->id;

                $boardId = Boards::create([
                    'user_id' => $userId,
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
                    'is_active' => 1,
                ])->id;
            }

            //MVP Info
            if (isset($input['ch_mvp_fname']) && isset($input['ch_mvp_lname']) && isset($input['ch_mvp_email'])) {
                $userId = User::create([
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1,
                ])->id;

                $boardId = Boards::create([
                    'user_id' => $userId,
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
                    'is_active' => 1,
                ])->id;
            }

            //TREASURER Info
            if (isset($input['ch_trs_fname']) && isset($input['ch_trs_lname']) && isset($input['ch_trs_email'])) {
                $userId = User::create([
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1,
                ])->id;

                $boardId = Boards::create([
                    'user_id' => $userId,
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
                    'is_active' => 1,
                ])->id;
            }

            //Secretary Info
            if (isset($input['ch_sec_fname']) && isset($input['ch_sec_lname']) && isset($input['ch_sec_email'])) {
                $userId = User::create([
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1,
                ])->id;

                $boardId = Boards::create([
                    'user_id' => $userId,
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
                    'is_active' => 1,
                ])->id;
            }

            $chDetails = Chapters::with(['state', 'primaryCoordinator'])->find($chapterId);
            $stateShortName = $chDetails->state->state_short_name;
            $chConfId = $chDetails->conference->id;
            $pcFName = $chDetails->primaryCoordinator->first_name;
            $pcLName = $chDetails->primaryCoordinator->last_name;
            $pcEmail = $chDetails->primaryCoordinator->email;

            $mailData = [
                'chapter_name' => $chDetails->name,
                'chapter_state' => $stateShortName,
                'conf' => $chConfId,
                'cor_fname' => $pcFName,
                'cor_lname' => $pcLName,
                'updated_by' => date('Y-m-d H:i:s'),
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
            ];

            //Primary Coordinator Notification//
            Mail::to($pcEmail)
                ->queue(new ChapterAddPrimaryCoor($mailData));

            //List Admin Notification//
            $listAdminEmail = 'listadmin@momsclub.org';
            Mail::to($listAdminEmail)
                ->queue(new ChapterAddListAdmin($mailData));

            DB::commit();
            } catch (\Exception $e) {
                DB::rollback();  // Rollback Transaction
                Log::error($e);  // Log the error

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

        $input = $request->all();
        $chPcIdPre = $input['ch_hid_primarycor'];
        $chPcIdUpd = $input['ch_primarycor'];
        $webStatusPre = $input['ch_hid_webstatus'];
        $webStatusUpd = $input['ch_webstatus'];

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
                $chapter->last_updated_date = $lastupdatedDate;

                $chapter->save();

                //Update Chapter MailData//
                $baseQueryUpd = $this->getChapterDetails($id);
                $chDetailsUpd = $baseQueryUpd['chDetails'];
                $stateShortName = $baseQueryUpd['stateShortName'];
                $chConfId = $baseQueryUpd['chConfId'];
                $chPcId = $baseQueryUpd['chPcId'];
                $PresDetailsUpd = $baseQueryUpd['PresDetails'];
                $emailListChap = $baseQueryUpd['emailListChap'];  // Full Board
                $emailListCoord = $baseQueryUpd['emailListCoord'];  // Full Coordinaor List
                $emailCC = $baseQueryUpd['emailCC'];  // CC Email
                $pcDetails = $baseQueryUpd['chDetails']->primaryCoordinator;
                $pcEmail = $pcDetails->email;  // PC Email
                $EINCordEmail = 'jackie.mchenry@momsclub.org';  // EIN Coor Email

                $mailData = [
                    'chapterNameUpd' => $chDetailsUpd->name,
                    'boundUpd' => $chDetailsUpd->territory,
                    'chapstatusUpd' => $chDetailsUpd->status_id,
                    'chapNoteUpd' => $chDetailsUpd->notes,
                    'inConUpd' => $chDetailsUpd->inquiries_contact,
                    'inNoteUpd' => $chDetailsUpd->inquiries_note,
                    'chapemailUpd' => $chDetailsUpd->email,
                    'poBoxUpd' => $chDetailsUpd->po_box,
                    'addInfoUpd' => $chDetailsUpd->additional_info,
                    'webUrlUpd' => $chDetailsUpd->website_url,
                    'webStatusUpd' => $chDetailsUpd->website_status,
                    'egroupUpd' => $chDetailsUpd->egroup,
                    'cor_fnameUpd' => $PresDetailsUpd->cor_f_name,
                    'cor_lnameUpd' => $PresDetailsUpd->cor_l_name,

                    'chapterNamePre' => $chDetailsPre->name,
                    'boundPre' => $chDetailsPre->territory,
                    'chapstatusPre' => $chDetailsPre->status_id,
                    'chapNotePre' => $chDetailsPre->notes,
                    'inConPre' => $chDetailsPre->inquiries_contact,
                    'inNotePre' => $chDetailsPre->inquiries_note,
                    'chapemailPre' => $chDetailsPre->email,
                    'poBoxPre' => $chDetailsPre->po_box,
                    'addInfoPre' => $chDetailsPre->additional_info,
                    'webUrlPre' => $chDetailsPre->website_url,
                    'webStatusPre' => $chDetailsPre->website_status,
                    'egroupPre' => $chDetailsPre->egroup,
                    'cor_fnamePre' => $PresDetailsPre->cor_f_name,
                    'cor_lnamePre' => $PresDetailsPre->cor_l_name,

                    'chapter_name' => $chDetailsUpd->name,
                    'chapter_state' => $stateShortName,
                    'conference' => $chConfId,
                    'updated_byUpd' => $lastupdatedDate,

                    'ch_pre_fname' => $PresDetailsPre->first_name,
                    'ch_pre_lname' => $PresDetailsPre->last_name,
                    'ch_pre_email' => $PresDetailsPre->email,
                    'name1' => $pcDetails->first_name,
                    'name2' => $pcDetails->last_name,
                    'email1' => $pcDetails->email,

                    'ch_website_url' => $website,
                ];

                //Primary Coordinator Notification//
                if ($chDetailsUpd->name != $chDetailsPre->name || $chDetailsUpd->inquiries_contact != $chDetailsPre->inquiries_contact || $chDetailsUpd->inquiries_note != $chDetailsPre->inquiries_note ||
                        $chDetailsUpd->email != $chDetailsPre->email || $chDetailsUpd->po_box != $chDetailsPre->po_box || $chDetailsUpd->website_url != $chDetailsPre->website_url ||
                        $chDetailsUpd->website_status != $chDetailsPre->website_status || $chDetailsUpd->egroup != $chDetailsPre->egroup || $chDetailsUpd->territory != $chDetailsPre->territory ||
                        $chDetailsUpd->additional_info != $chDetailsPre->additional_info || $chDetailsUpd->status_id != $chDetailsPre->status_id || $chDetailsUpd->notes != $chDetailsPre->notes) {
                    Mail::to($pcEmail)
                        ->queue(new ChaptersUpdatePrimaryCoorChapter($mailData));
                }

                //Name Change Notification//
                if ($chDetailsUpd->name != $chDetailsPre->name) {
                    Mail::to($EINCordEmail)
                        ->queue(new ChapersUpdateEINCoor($mailData));
                }

                //PC Change Notification//
                if ($chPcIdUpd != $chPcIdPre) {
                    Mail::to($emailListChap)
                    ->queue(new ChaptersPrimaryCoordinatorChange($mailData));

                    Mail::to($pcEmail)
                    ->queue(new ChaptersPrimaryCoordinatorChangePCNotice($mailData));
                }

                //Website URL Change Notification//
                if ($webStatusUpd != $webStatusPre) {
                    if ($webStatusUpd == 1) {
                        Mail::to($emailCC)
                            ->queue(new WebsiteAddNoticeAdmin($mailData));

                        Mail::to($emailListChap)
                            ->cc($emailListCoord)
                            ->queue(new WebsiteAddNoticeChapter($mailData));
                    }

                    if ($webStatusUpd == 2) {
                        Mail::to($emailCC)
                            ->queue(new WebsiteReviewNotice($mailData));
                    }
                }

            DB::commit();
            } catch (\Exception $e) {
                DB::rollback();  // Rollback Transaction
                Log::error($e);  // Log the error

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
                    $chapter = Chapters::with('president')->find($id);
                    $president = $chapter->president;
                    $user = $president->user;

                    $user->update([   // Update user details
                        'first_name' => $request->input('ch_pre_fname'),
                        'last_name' => $request->input('ch_pre_lname'),
                        'email' => $request->input('ch_pre_email'),
                        'updated_at' => now(),
                    ]);
                    $president->update([   // Update board details
                        'first_name' => $request->input('ch_pre_fname'),
                        'last_name' => $request->input('ch_pre_lname'),
                        'email' => $request->input('ch_pre_email'),
                        'street_address' => $request->input('ch_pre_street'),
                        'city' => $request->input('ch_pre_city'),
                        'state' => $request->input('ch_pre_state'),
                        'zip' => $request->input('ch_pre_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_pre_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                    ]);
                }

                //AVP Info
                $chapter = Chapters::with('avp')->find($id);
                $avp = $chapter->avp;
                    if ($avp) {
                        $user = $avp->user;
                        if ($request->input('AVPVacant') == 'on') {
                            $avp->delete();  // Delete board member and associated user if now Vacant
                            $user->delete();
                        } else {
                            $user->update([   // Update user details if alrady exists
                                'first_name' => $request->input('ch_avp_fname'),
                                'last_name' => $request->input('ch_avp_lname'),
                                'email' => $request->input('ch_avp_email'),
                                'updated_at' => now(),
                            ]);
                            $avp->update([   // Update board details if alrady exists
                                'first_name' => $request->input('ch_avp_fname'),
                                'last_name' => $request->input('ch_avp_lname'),
                                'email' => $request->input('ch_avp_email'),
                                'street_address' => $request->input('ch_avp_street'),
                                'city' => $request->input('ch_avp_city'),
                                'state' => $request->input('ch_avp_state'),
                                'zip' => $request->input('ch_avp_zip'),
                                'country' => 'USA',
                                'phone' => $request->input('ch_avp_phone'),
                                'last_updated_by' => $lastUpdatedBy,
                                'last_updated_date' => now(),
                            ]);
                        }
                    } else {
                        if ($request->input('AVPVacant') != 'on') {
                            $user = User::create([  // Create user details if new
                                'first_name' => $request->input('ch_avp_fname'),
                                'last_name' => $request->input('ch_avp_lname'),
                                'email' => $request->input('ch_avp_email'),
                                'password' => Hash::make('TempPass4You'),
                                'user_type' => 'board',
                                'is_active' => 1,
                            ]);
                            $chapter->avp()->create([  // Create board details if new
                                'user_id' => $user->id,
                                'first_name' => $request->input('ch_avp_fname'),
                                'last_name' => $request->input('ch_avp_lname'),
                                'email' => $request->input('ch_avp_email'),
                                'board_position_id' => 2,
                                'street_address' => $request->input('ch_avp_street'),
                                'city' => $request->input('ch_avp_city'),
                                'state' => $request->input('ch_avp_state'),
                                'zip' => $request->input('ch_avp_zip'),
                                'country' => 'USA',
                                'phone' => $request->input('ch_avp_phone'),
                                'last_updated_by' => $lastUpdatedBy,
                                'last_updated_date' => now(),
                                'is_active' => 1,
                            ]);
                        }
                    }

                //MVP Info
                $chapter = Chapters::with('mvp')->find($id);
                $mvp = $chapter->mvp;
                    if ($mvp) {
                        $user = $mvp->user;
                        if ($request->input('MVPVacant') == 'on') {
                            $mvp->delete();  // Delete board member and associated user if now Vacant
                            $user->delete();
                        } else {
                            $user->update([   // Update user details if alrady exists
                                'first_name' => $request->input('ch_mvp_fname'),
                                'last_name' => $request->input('ch_mvp_lname'),
                                'email' => $request->input('ch_mvp_email'),
                                'updated_at' => now(),
                            ]);
                            $mvp->update([   // Update board details if alrady exists
                                'first_name' => $request->input('ch_mvp_fname'),
                                'last_name' => $request->input('ch_mvp_lname'),
                                'email' => $request->input('ch_mvp_email'),
                                'street_address' => $request->input('ch_mvp_street'),
                                'city' => $request->input('ch_mvp_city'),
                                'state' => $request->input('ch_mvp_state'),
                                'zip' => $request->input('ch_mvp_zip'),
                                'country' => 'USA',
                                'phone' => $request->input('ch_mvp_phone'),
                                'last_updated_by' => $lastUpdatedBy,
                                'last_updated_date' => now(),
                            ]);
                        }
                    } else {
                        if ($request->input('MVPVacant') != 'on') {
                            $user = User::create([  // Create user details if new
                                'first_name' => $request->input('ch_mvp_fname'),
                                'last_name' => $request->input('ch_mvp_lname'),
                                'email' => $request->input('ch_mvp_email'),
                                'password' => Hash::make('TempPass4You'),
                                'user_type' => 'board',
                                'is_active' => 1,
                            ]);
                            $chapter->mvp()->create([  // Create board details if new
                                'user_id' => $user->id,
                                'first_name' => $request->input('ch_mvp_fname'),
                                'last_name' => $request->input('ch_mvp_lname'),
                                'email' => $request->input('ch_mvp_email'),
                                'board_position_id' => 3,
                                'street_address' => $request->input('ch_mvp_street'),
                                'city' => $request->input('ch_mvp_city'),
                                'state' => $request->input('ch_mvp_state'),
                                'zip' => $request->input('ch_mvp_zip'),
                                'country' => 'USA',
                                'phone' => $request->input('ch_mvp_phone'),
                                'last_updated_by' => $lastUpdatedBy,
                                'last_updated_date' => now(),
                                'is_active' => 1,
                            ]);
                        }
                    }

                //TRS Info
                $chapter = Chapters::with('treasurer')->find($id);
                $treasurer = $chapter->treasurer;
                    if ($treasurer) {
                        $user = $treasurer->user;
                        if ($request->input('TreasVacant') == 'on') {
                            $treasurer->delete();  // Delete board member and associated user if now Vacant
                            $user->delete();
                        } else {
                            $user->update([   // Update user details if alrady exists
                                'first_name' => $request->input('ch_trs_fname'),
                                'last_name' => $request->input('ch_trs_lname'),
                                'email' => $request->input('ch_trs_email'),
                                'updated_at' => now(),
                            ]);
                            $treasurer->update([   // Update board details if alrady exists
                                'first_name' => $request->input('ch_trs_fname'),
                                'last_name' => $request->input('ch_trs_lname'),
                                'email' => $request->input('ch_trs_email'),
                                'street_address' => $request->input('ch_trs_street'),
                                'city' => $request->input('ch_trs_city'),
                                'state' => $request->input('ch_trs_state'),
                                'zip' => $request->input('ch_trs_zip'),
                                'country' => 'USA',
                                'phone' => $request->input('ch_trs_phone'),
                                'last_updated_by' => $lastUpdatedBy,
                                'last_updated_date' => now(),
                            ]);
                        }
                    } else {
                        if ($request->input('TreasVacant') != 'on') {
                            $user = User::create([  // Create user details if new
                                'first_name' => $request->input('ch_trs_fname'),
                                'last_name' => $request->input('ch_trs_lname'),
                                'email' => $request->input('ch_trs_email'),
                                'password' => Hash::make('TempPass4You'),
                                'user_type' => 'board',
                                'is_active' => 1,
                            ]);
                            $chapter->treasurer()->create([  // Create board details if new
                                'user_id' => $user->id,
                                'first_name' => $request->input('ch_trs_fname'),
                                'last_name' => $request->input('ch_trs_lname'),
                                'email' => $request->input('ch_trs_email'),
                                'board_position_id' => 4,
                                'street_address' => $request->input('ch_trs_street'),
                                'city' => $request->input('ch_trs_city'),
                                'state' => $request->input('ch_trs_state'),
                                'zip' => $request->input('ch_trs_zip'),
                                'country' => 'USA',
                                'phone' => $request->input('ch_trs_phone'),
                                'last_updated_by' => $lastUpdatedBy,
                                'last_updated_date' => now(),
                                'is_active' => 1,
                            ]);
                        }
                    }

                //SEC Info
                $chapter = Chapters::with('secretary')->find($id);
                $secretary = $chapter->secretary;
                    if ($secretary) {
                        $user = $secretary->user;
                        if ($request->input('SecVacant') == 'on') {
                            $secretary->delete();  // Delete board member and associated user if now Vacant
                            $user->delete();
                        } else {
                            $user->update([   // Update user details if alrady exists
                                'first_name' => $request->input('ch_sec_fname'),
                                'last_name' => $request->input('ch_sec_lname'),
                                'email' => $request->input('ch_sec_email'),
                                'updated_at' => now(),
                            ]);
                            $secretary->update([   // Update board details if alrady exists
                                'first_name' => $request->input('ch_sec_fname'),
                                'last_name' => $request->input('ch_sec_lname'),
                                'email' => $request->input('ch_sec_email'),
                                'street_address' => $request->input('ch_sec_street'),
                                'city' => $request->input('ch_sec_city'),
                                'state' => $request->input('ch_sec_state'),
                                'zip' => $request->input('ch_sec_zip'),
                                'country' => 'USA',
                                'phone' => $request->input('ch_sec_phone'),
                                'last_updated_by' => $lastUpdatedBy,
                                'last_updated_date' => now(),
                            ]);
                        }
                    } else {
                        if ($request->input('SecVacant') != 'on') {
                            $user = User::create([  // Create user details if new
                                'first_name' => $request->input('ch_sec_fname'),
                                'last_name' => $request->input('ch_sec_lname'),
                                'email' => $request->input('ch_sec_email'),
                                'password' => Hash::make('TempPass4You'),
                                'user_type' => 'board',
                                'is_active' => 1,
                            ]);
                            $chapter->secretary()->create([  // Create board details if new
                                'user_id' => $user->id,
                                'first_name' => $request->input('ch_sec_fname'),
                                'last_name' => $request->input('ch_sec_lname'),
                                'email' => $request->input('ch_sec_email'),
                                'board_position_id' => 5,
                                'street_address' => $request->input('ch_sec_street'),
                                'city' => $request->input('ch_sec_city'),
                                'state' => $request->input('ch_sec_state'),
                                'zip' => $request->input('ch_sec_zip'),
                                'country' => 'USA',
                                'phone' => $request->input('ch_sec_phone'),
                                'last_updated_by' => $lastUpdatedBy,
                                'last_updated_date' => now(),
                                'is_active' => 1,
                            ]);
                        }
                    }

                //Update Chapter MailData//
                $baseQueryUpd = $this->getChapterDetails($id);
                $chDetailsUpd = $baseQueryUpd['chDetails'];
                $stateShortName = $baseQueryUpd['stateShortName'];
                $chConfId = $baseQueryUpd['chConfId'];
                $chPcId = $baseQueryUpd['chPcId'];
                $emailPC = $baseQueryUpd['emailPC'];
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

                    Mail::to($emailPC)
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
                DB::rollback();  // Rollback Transaction
                Log::error($e);  // Log the error

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

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $startMonthName = $baseQuery['startMonthName'];
        $chIsActive = $baseQuery['chIsActive'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chDocuments = $baseQuery['chDocuments'];

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'cdId' => $cdId, 'conferenceDescription' => $conferenceDescription,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'startMonthName' => $startMonthName,
            'cdConfId' => $cdConfId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chDocuments' => $chDocuments
            ];

        return view('chapters.editirs')->with($data);
    }

    /**
     *Update Chapter EIN Notes
     */
    public function updateChapterIRS(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;

        $chapter = Chapters::find($id);
        $documents = Documents::find($id);

        DB::beginTransaction();
        try {
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();

            $documents->ein_letter = $request->has('ch_ein_letter') ? 1 : 0;
            $documents->irs_verified = $request->has('irs_verified') ? 1 : 0;
            $documents->irs_notes = $request->input('irs_notes');
            $documents->save();

            DB::commit();
            } catch (\Exception $e) {
                DB::rollback();  // Rollback Transaction
                Log::error($e);  // Log the error

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

        $allWebLinks = Website::all();  // Full List for Dropdown Menu

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'cdId' => $cdId, 'stateShortName' => $stateShortName, 'conferenceDescription' => $conferenceDescription,
            'chDetails' => $chDetails, 'allWebLinks' => $allWebLinks,
            'cdConfId' => $cdConfId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'regionLongName' => $regionLongName
        ];

        return view('chapters.editwebsite')->with($data);
    }

    /**
     *Update Website & Social Media Information
     */
    public function updateChapterWebsite(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;

        $baseQueryPre = $this->getChapterDetails($id);
        $chDetailsPre = $baseQueryPre['chDetails'];

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

            //Update Chapter MailData//
            $baseQueryUpd = $this->getChapterDetails($id);
            $chDetailsUpd = $baseQueryUpd['chDetails'];
            $stateShortName = $baseQueryUpd['stateShortName'];
            $chPcId = $baseQueryUpd['chPcId'];
            $emailListChap = $baseQueryUpd['emailListChap'];  // Full Board
            $emailListCoord = $baseQueryUpd['emailListCoord']; // Full Coord List
            $emailCC = $baseQueryUpd['emailCC'];  // CC Email
            $emailPC = $baseQueryUpd['emailPC'];

            if ($request->input('ch_webstatus') != $request->input('ch_hid_webstatus')) {
                $mailData = [
                    'chapter_name' => $chDetailsUpd->name,
                    'chapter_state' => $stateShortName,
                    'ch_website_url' => $website,
                ];

                if ($request->input('ch_webstatus') == 1) {
                    Mail::to($emailCC)
                        ->queue(new WebsiteAddNoticeAdmin($mailData));

                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new WebsiteAddNoticeChapter($mailData));
                }

                if ($request->input('ch_webstatus') == 2) {
                    Mail::to($emailCC)
                        ->queue(new WebsiteReviewNotice($mailData));
                }
            }

            $mailData = [
                'chapter_name' => $chDetailsUpd->name,
                'chapter_state' => $stateShortName,
                'webUrlUpd' => $chDetailsUpd->website_url,
                'webStatusUpd' => $chDetailsUpd->website_status,
                'webUrlPre' => $chDetailsPre->website_url,
                'webStatusPre' => $chDetailsPre->website_status,
                'updated_byUpd' => $chDetailsPre->last_updated_date,
            ];

            if ($chDetailsUpd->website_url != $chDetailsPre->website_url || $chDetailsUpd->website_status != $chDetailsPre->website_status) {
                Mail::to($emailPC)
                    ->queue(new WebsiteUpdatePrimaryCoor($mailData));
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $currentYear = date('Y');
        $currentMonth = date('m');

        $baseQuery = $this->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox3Status = $baseQuery['checkBox3Status'];

        if ($checkBox3Status) {
            $reChapterList = $baseQuery['query']
                ->get();
        } else {
            $reChapterList = $baseQuery['query']
                ->where(function ($query) use ($currentYear, $currentMonth) {
                    $query->where('next_renewal_year', '<', $currentYear)
                        ->orWhere(function ($query) use ($currentYear, $currentMonth) {
                            $query->where('next_renewal_year', '=', $currentYear)
                                ->where('start_month_id', '<=', $currentMonth);
                        });
                })
                ->orderBy('start_month_id', 'desc')
                ->orderBy('next_renewal_year', 'desc')
                ->get();
        }

        $countList = count($reChapterList);
        $data = ['countList' => $countList, 'reChapterList' => $reChapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox3Status' => $checkBox3Status, 'corId' => $cdId];

        return view('chapters.chapreregistration')->with($data);
    }

    /**
     * ReRegistration Reminders Auto Send
     */
    public function createChapterReRegistrationReminder(Request $request): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;

        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;
        $monthInWords = $now->format('F');
        $rangeEndDate = $now->copy()->subMonth()->endOfMonth();
        $rangeStartDate = $rangeEndDate->copy()->startOfMonth()->subYear()->addMonth();

        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        try {

            $chapters = Chapters::with(['state', 'conference', 'region',])
                ->where('conference_id', $cdConfId)
                ->where('start_month_id', $month)
                ->where('next_renewal_year', $year)
                ->where('is_active', 1)
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

                $chapterName = $chapter->name;
                $stateShortName = $chapter->state->state_short_name;

                if ($chapterName) {
                    $emailData = $this->userController->loadEmailDetails($chapter->id);
                    $emailListChap = $emailData['emailListChap'];
                    $emailListCoord = $emailData['emailListCoord'];

                    $chapterEmails[$chapterName] = $emailListChap;
                    $coordinatorEmails[$chapterName] = $emailListCoord;
                }

                $mailData[$chapterName] = [
                    'chapterName' => $chapterName,
                    'chapterState' => $stateShortName,
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

            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/chapter/reregistration')->with('success', 'Re-Registration Reminders have been successfully sent.');
    }

    /**
     * ReRegistration Late Notices Auto Send
     */
    public function createChapterReRegistrationLateReminder(Request $request): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;

        $now = Carbon::now();
        $month = $now->month;
        $lastMonth = $now->copy()->subMonth()->format('m');
        $year = $now->year;
        if ($now->format('m') == '01' && $lastMonth == '12') {
            $year = $now->year - 1;
        }
        $monthInWords = $now->format('F');
        $lastMonthInWords = $now->copy()->subMonth()->format('F');
        $rangeEndDate = $now->copy()->subMonths(2)->endOfMonth();
        $rangeStartDate = $rangeEndDate->copy()->startOfMonth()->subYear()->addMonth();

        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        try {

            $chapters = Chapters::with(['state', 'conference', 'region',])
                ->where('chapters.conference_id', $cdConfId)
                ->where('chapters.start_month_id', $lastMonth)
                ->where('chapters.next_renewal_year', $year)
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

                $chapterName = $chapter->name;
                $stateShortName = $chapter->state->state_short_name;

                if ($chapterName) {
                    $emailData = $this->userController->loadEmailDetails($chapter->id);
                    $emailListChap = $emailData['emailListChap'];
                    $emailListCoord = $emailData['emailListCoord'];

                    $chapterEmails[$chapterName] = $emailListChap;
                    $coordinatorEmails[$chapterName] = $emailListCoord;
                }

                $mailData[$chapterName] = [
                    'chapterName' => $chapterName,
                    'chapterState' => $stateShortName,
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

            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/chapter/reregistration')->with('success', 'Re-Registration Late Reminders have been successfully sent.');
    }

    /**
     * View Doantions List
     */
    public function showRptDonations(Request $request): View
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

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $cdId];

        return view('chapters.chapdonations')->with($data);
    }

    /**
     * View the International M2M Doantions
     */
    public function showIntdonation(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $chapterList = Chapters::with(['state', 'conference', 'region', 'webLink', 'president', 'avp', 'mvp', 'treasurer', 'secretary', 'startMonth',
        'state', 'primaryCoordinator'])
        ->where('is_active', 1)
        ->orderBy(State::select('state_short_name')
            ->whereColumn('state.id', 'chapters.state_id'), 'asc')
            ->orderBy('chapters.name', 'asc')
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

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $chDetails->status->chapter_status;
        $chIsActive = $baseQuery['chIsActive'];

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'stateShortName' => $stateShortName, 'startMonthName' => $startMonthName,
            'chDetails' => $chDetails, 'chapterStatus' => $chapterStatus, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
        ];

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

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $nextRenewalYear = $baseQuery['chDetails']->next_renewal_year;
        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];
        $emailPC = $baseQuery['emailPC'];

        $input = $request->all();
        $reg_notes = $input['ch_regnotes'];
        $dues_last_paid = $input['PaymentDate'];
        $members_paid_for = $input['MembersPaidFor'];
        $m2m_date = $input['M2MPaymentDate'];
        $m2m_payment = $input['M2MPayment'];
        $sustaining_date = $input['SustainingPaymentDate'];
        $sustaining_donation = $input['SustainingPayment'];

        $chapter = Chapters::find($id);

        DB::beginTransaction();
        try {
                $chapter->reg_notes = $reg_notes;
                $chapter->save();

            if ($dues_last_paid != null) {
                $chapter->dues_last_paid = $dues_last_paid;
                $chapter->members_paid_for = $members_paid_for;
                $chapter->next_renewal_year = $nextRenewalYear + 1;
                $chapter->last_updated_by = $lastUpdatedBy;
                $chapter->last_updated_date = date('Y-m-d H:i:s');
                $chapter->save();

                if ($request->input('ch_notify') == 'on') {
                    $mailData = [
                        'chapterName' => $chDetails->name,
                        'chapterState' => $stateShortName,
                        'chapterDate' => $dues_last_paid,
                        'chapterMembers' => $members_paid_for,
                    ];

                    // Payment Thank You Email
                    Mail::to($emailListChap)
                        ->cc($emailPC)
                        ->queue(new PaymentsReRegChapterThankYou($mailData));
                }
            }

            if ($m2m_date != null) {
                $chapter->m2m_date = $m2m_date;
                $chapter->m2m_payment = $m2m_payment;
                $chapter->last_updated_by = $lastUpdatedBy;
                $chapter->last_updated_date = date('Y-m-d H:i:s');
                $chapter->save();

                if ($request->input('ch_thanks') == 'on') {
                    $mailData = [
                        'chapterName' => $chDetails->name,
                        'chapterState' => $stateShortName,
                        'chapterAmount' => $m2m_payment,
                    ];

                    //M2M Donation Thank You Email//
                    Mail::to($emailListChap)
                        ->cc($emailPC)
                        ->queue(new PaymentsM2MChapterThankYou($mailData));
                }
            }

            if ($sustaining_date != null) {
                $chapter->sustaining_date = $sustaining_date;
                $chapter->sustaining_donation = $sustaining_donation;
                $chapter->last_updated_by = $lastUpdatedBy;
                $chapter->last_updated_date = date('Y-m-d H:i:s');
                $chapter->save();

                if ($request->input('ch_sustaining') == 'on') {
                    $mailData = [
                        'chapterName' => $chDetails->name,
                        'chapterState' => $stateShortName,
                        'chapterTotal' => $sustaining_donation,
                    ];

                    //Sustaining Chapter Thank You Email//
                    Mail::to($emailListChap)
                        ->cc($emailPC)
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
