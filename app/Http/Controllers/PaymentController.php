<?php

namespace App\Http\Controllers;

use App\Mail\PaymentsReRegLate;
use App\Mail\PaymentsReRegReminder;
use App\Mail\PaymentsM2MChapterThankYou;
use App\Mail\PaymentsM2MOnline;
use App\Mail\PaymentsReRegChapterThankYou;
use App\Mail\PaymentsReRegOnline;
use App\Mail\PaymentsSustainingChapterThankYou;
use App\Models\Chapters;
use App\Models\FinancialReport;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class PaymentController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->middleware('auth')->except('logout');
        $this->userController = $userController;
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
        $chapterId = $id;
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

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

        $chapter = Chapters::find($chapterId);
        DB::beginTransaction();
        try {
            $chapter->dues_last_paid = $request->filled('PaymentDate') ? $request->input('PaymentDate') : $chapter->dues_last_paid;
            $chapter->members_paid_for = $request->filled('MembersPaidFor') ? $request->input('MembersPaidFor') : $chapter->members_paid_for;
            // $chapter->reg_notes = $request->filled('ch_regnotes') ? $request->input('ch_regnotes') : $chapter->reg_notes;
            $chapter->reg_notes = $request->has('ch_regnotes') ? $request->input('ch_regnotes') : $chapter->reg_notes;
            $chapter->m2m_date = $request->filled('M2MPaymentDate') ? $request->input('M2MPaymentDate') : $chapter->m2m_date;
            $chapter->m2m_payment = $request->filled('M2MPayment') ? $request->input('M2MPayment') : $chapter->m2m_payment;
            $chapter->sustaining_date = $request->filled('SustainingPaymentDate') ? $request->input('SustainingPaymentDate') : $chapter->sustaining_date;
            $chapter->sustaining_donation = $request->filled('SustainingPayment') ? $request->input('SustainingPayment') : $chapter->sustaining_donation;
            if ($request->filled('MembersPaidFor') && $request->input('MembersPaidFor') != $chapter->members_paid_for) {
                $chapter->next_renewal_year = $nextRenewalYear + 1;
            }
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

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            echo $e->getMessage();
            exit();
            DB::rollback();
            // Log the error
            Log::error($e);

            return to_route('chapters.editpayment', ['id' => $id])->with('fail', 'Something went wrong, Please try again..');
        }

        return to_route('chapters.editpayment', ['id' => $id])->with('success', 'Chapter Payments/Donations have been updated');
    }

    public function processPayment(Request $request): RedirectResponse
    {
        $borDetails = User::find($request->user()->id)->board;
        $chapterId = $borDetails['chapter_id'];

        $chapterDetails = Chapters::find($chapterId);
        $chapterState = DB::table('state')
            ->select('state_short_name')
            ->where('id', '=', $chapterDetails->state)
            ->get();
        $chapterState = $chapterState[0]->state_short_name;
        $chapterName = $chapterDetails['name'];
        $chConf = $chapterDetails['conference'];
        $chPcid = $chapterDetails['primary_coordinator_id'];

        $presDetails = DB::table('boards as bd')
            ->select('bd.chapter_id as chapter_id', 'bd.board_position_id as board_position_id', 'bd.first_name as first_name', 'bd.last_name as last_name',
                'bd.street_address as street_address', 'bd.city as city', 'bd.state as state', 'bd.zip as zip')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', 1)
            ->first();

        $company = $chapterName.', '.$chapterState;
        $next_renewal_year = $chapterDetails['next_renewal_year'];

        $chapterEmailList = DB::table('boards as bd')
            ->select('bd.email as bor_email')
            ->where('bd.chapter_id', '=', $chapterId)
            ->get();
        $emailListBoard = '';
        foreach ($chapterEmailList as $val) {
            $email = $val->bor_email;
            $escaped_email = str_replace("'", "\\'", $email);
            if ($emailListBoard == '') {
                $emailListBoard = $escaped_email;
            } else {
                $emailListBoard .= ','.$escaped_email;
            }
        }

        $corDetails = DB::table('coordinators')
            ->select('email')
            ->where('id', $chapterDetails->primary_coordinator_id)
            ->first();
        $cor_pcemail = $corDetails->email;

        $coordinatorEmailList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('id', '=', $chPcid)
            ->get();

        foreach ($coordinatorEmailList as $key => $value) {
            $coordinatorList[$key] = (array) $value;
        }
        $filterCoordinatorList = array_filter($coordinatorList[0]);
        unset($filterCoordinatorList['id']);
        unset($filterCoordinatorList['layer0']);
        $filterCoordinatorList = array_reverse($filterCoordinatorList);
        $str = '';
        $array_rows = count($filterCoordinatorList);
        $i = 0;

        $emailListCoor = '';
        foreach ($filterCoordinatorList as $key => $val) {
            if ($val > 1) {
                $corList = DB::table('coordinators as cd')
                    ->select('cd.email as cord_email')
                    ->where('cd.id', '=', $val)
                    ->where('cd.is_active', '=', 1)
                    ->get();
                if (count($corList) > 0) {
                    if ($emailListCoor == '') {
                        $emailListCoor = $corList[0]->cord_email;
                    } else {
                        $emailListCoor .= ','.$corList[0]->cord_email;
                    }
                }
            }
        }

        $members = $request->input('members');
        $late = $request->input('late');
        $rereg = $request->input('rereg');
        $donation = $request->input('sustaining');
        $sustaining = (float) preg_replace('/[^\d.]/', '', $donation);
        $fee = $request->input('fee');
        $cardNumber = $request->input('card_number');
        $expirationDate = $request->input('expiration_date');
        $cvv = $request->input('cvv');
        $first = $request->input('first_name');
        $last = $request->input('last_name');
        $address = $request->input('address');
        $city = $request->input('city');
        $state = $request->input('state');
        $zip = $request->input('zip');
        $email = $request->input('email');
        $total = $request->input('total');
        $amount = (float) preg_replace('/[^\d.]/', '', $total);
        $today = Carbon::today()->format('m-d-Y');

        // Call the load_coordinators function
        $coordinatorData = $this->load_coordinators($chapterId, $chConf, $chPcid);
        $ConfCoorEmail = $coordinatorData['ConfCoorEmail'];
        $coordinator_array = $coordinatorData['coordinator_array'];

        /* Create a merchantAuthenticationType object with authentication details
            retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType;
        $merchantAuthentication->setName(config('settings.authorizenet_api_login_id'));
        $merchantAuthentication->setTransactionKey(config('settings.authorizenet_transaction_key'));

        // Set the transaction's refId
        $refId = 'ref'.time();

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType;
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($expirationDate);
        $creditCard->setCardCode($cvv);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType;
        $paymentOne->setCreditCard($creditCard);

        // Generate a random invoice number
        $randomInvoiceNumber = mt_rand(100000, 999999);
        // Create order information
        $order = new AnetAPI\OrderType;
        $order->setInvoiceNumber($randomInvoiceNumber);
        $order->setDescription('Re-Registration Payment');

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType;
        $customerAddress->setFirstName($first);
        $customerAddress->setLastName($last);
        $customerAddress->setCompany($company);
        $customerAddress->setAddress($address);
        $customerAddress->setCity($city);
        $customerAddress->setState($state);
        $customerAddress->setZip($zip);
        $customerAddress->setCountry('USA');

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType;
        $customerData->setType('individual');
        $customerData->setId($chapterId);
        $customerData->setEmail($email);

        // Add values for transaction settings
        $duplicateWindowSetting = new AnetAPI\SettingType;
        $duplicateWindowSetting->setSettingName('duplicateWindow');
        $duplicateWindowSetting->setSettingValue('60');

        // Add some merchant defined fields. These fields won't be stored with the transaction,
        // but will be echoed back in the response.
        $merchantDefinedField1 = new AnetAPI\UserFieldType;
        $merchantDefinedField1->setName('MemberCount');
        $merchantDefinedField1->setValue($members);

        $merchantDefinedField2 = new AnetAPI\UserFieldType;
        $merchantDefinedField2->setName('SustainingDonation');
        $merchantDefinedField2->setValue($sustaining);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType;
        //$transactionRequestType->setTransactionType('authOnlyTransaction');
        $transactionRequestType->setTransactionType('authCaptureTransaction');
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        $transactionRequestType->addToUserFields($merchantDefinedField1);
        $transactionRequestType->addToUserFields($merchantDefinedField2);

        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest;
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == 'Ok') {
                // Since the API request was successful, look for a transaction response and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {

                    $mailData = [
                        'chapterName' => $chapterName,
                        'chapterState' => $chapterState,
                        'pres_fname' => $presDetails->first_name,
                        'pres_lname' => $presDetails->last_name,
                        'pres_street' => $presDetails->street_address,
                        'pres_city' => $presDetails->city,
                        'pres_state' => $presDetails->state,
                        'pres_zip' => $presDetails->zip,
                        'members' => $members,
                        'late' => $late,
                        'sustaining' => $donation,
                        'reregTotal' => $rereg,
                        'processing' => $fee,
                        'totalPaid' => $total,
                        'fname' => $first,
                        'lname' => $last,
                        'email' => $email,
                        'chapterId' => $chapterId,
                        'invoice' => $randomInvoiceNumber,
                        'datePaid' => $today,
                        'chapterMembers' => $members,
                        'chapterDate' => Carbon::today()->format('m-d-Y'),
                        'chapterTotal' => $sustaining,
                    ];

                    $to_email = $email;
                    $to_email2 = explode(',', $emailListBoard);
                    $to_email3 = $cor_pcemail;
                    $to_email4 = explode(',', $emailListCoor);
                    $to_email5 = $ConfCoorEmail;
                    $to_email6 = 'dragonmom@msn.com';

                    $existingRecord = Chapters::where('id', $chapterId)->first();
                    $existingRecord->members_paid_for = $members;
                    $existingRecord->next_renewal_year = $next_renewal_year + 1;
                    $existingRecord->dues_last_paid = Carbon::today();

                    Mail::to([$to_email])
                        ->cc($to_email3)
                        ->queue(new PaymentsReRegChapterThankYou($mailData));

                    Mail::to([$to_email5, $to_email6])
                        ->queue(new PaymentsReRegOnline($mailData, $coordinator_array));

                    if ($sustaining > 0.00) {
                        $existingRecord->sustaining_donation = $sustaining;
                        $existingRecord->sustaining_date = Carbon::today();

                        Mail::to([$to_email])
                            ->cc($to_email3)
                            ->queue(new PaymentsSustainingChapterThankYou($mailData));
                    }
                    $existingRecord->save();

                    // Success notification
                    return redirect()->to('/home')->with('success', 'Payment was successfully processed and profile has been updated!');
                    // return redirect()->route('home')->with('success', 'Payment successful! Transaction ID: ' . $tresponse->getTransId());
                } else {
                    // Transaction failed
                    $error_message = 'Transaction Failed';
                    if ($tresponse->getErrors() != null) {
                        $error_message .= "\n Error Code: ".$tresponse->getErrors()[0]->getErrorCode();
                        $error_message .= "\n Error Message: ".$tresponse->getErrors()[0]->getErrorText();
                    }

                    return redirect()->to('/board/reregpayment')->with('fail', $error_message);
                }

                // Or, print errors if the API request wasn't successful
            } else {
                // Transaction Failed
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $error_message = 'Transaction Failed';
                    $error_message .= "\n Error Code: ".$tresponse->getErrors()[0]->getErrorCode();
                    $error_message .= "\n Error Message: ".$tresponse->getErrors()[0]->getErrorText();

                    return redirect()->back()->with('fail', $error_message);
                } else {
                    $error_message = 'Transaction Failed';
                    $error_message .= "\n Error Code: ".$response->getMessages()->getMessage()[0]->getCode();
                    $error_message .= "\n Error Message: ".$response->getMessages()->getMessage()[0]->getText();

                    return redirect()->to('/board/reregpayment')->with('fail', $error_message);
                }
            }
        } else {
            // No response returned
            return redirect()->to('/board/reregpayment')->with('fail', 'No response returned');
        }
    }

    public function processDonation(Request $request): RedirectResponse
    {
        $borDetails = User::find($request->user()->id)->board;
        $chapterId = $borDetails['chapter_id'];

        $chapterDetails = Chapters::find($chapterId);
        $chapterState = DB::table('state')
            ->select('state_short_name')
            ->where('id', '=', $chapterDetails->state)
            ->get();
        $chapterState = $chapterState[0]->state_short_name;
        $chapterName = $chapterDetails['name'];
        $chConf = $chapterDetails['conference'];
        $chPcid = $chapterDetails['primary_coordinator_id'];

        $presDetails = DB::table('boards as bd')
            ->select('bd.chapter_id as chapter_id', 'bd.board_position_id as board_position_id', 'bd.first_name as first_name', 'bd.last_name as last_name',
                'bd.street_address as street_address', 'bd.city as city', 'bd.state as state', 'bd.zip as zip')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', 1)
            ->first();

        $company = $chapterName.', '.$chapterState;
        $next_renewal_year = $chapterDetails['next_renewal_year'];

        $chapterEmailList = DB::table('boards as bd')
            ->select('bd.email as bor_email')
            ->where('bd.chapter_id', '=', $chapterId)
            ->get();
        $emailListBoard = '';
        foreach ($chapterEmailList as $val) {
            $email = $val->bor_email;
            $escaped_email = str_replace("'", "\\'", $email);
            if ($emailListBoard == '') {
                $emailListBoard = $escaped_email;
            } else {
                $emailListBoard .= ','.$escaped_email;
            }
        }

        $corDetails = DB::table('coordinators')
            ->select('email')
            ->where('id', $chapterDetails->primary_coordinator_id)
            ->first();
        $cor_pcemail = $corDetails->email;

        $coordinatorEmailList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('id', '=', $chPcid)
            ->get();

        foreach ($coordinatorEmailList as $key => $value) {
            $coordinatorList[$key] = (array) $value;
        }
        $filterCoordinatorList = array_filter($coordinatorList[0]);
        unset($filterCoordinatorList['id']);
        unset($filterCoordinatorList['layer0']);
        $filterCoordinatorList = array_reverse($filterCoordinatorList);
        $str = '';
        $array_rows = count($filterCoordinatorList);
        $i = 0;

        $emailListCoor = '';
        foreach ($filterCoordinatorList as $key => $val) {
            if ($val > 1) {
                $corList = DB::table('coordinators as cd')
                    ->select('cd.email as cord_email')
                    ->where('cd.id', '=', $val)
                    ->where('cd.is_active', '=', 1)
                    ->get();
                if (count($corList) > 0) {
                    if ($emailListCoor == '') {
                        $emailListCoor = $corList[0]->cord_email;
                    } else {
                        $emailListCoor .= ','.$corList[0]->cord_email;
                    }
                }
            }
        }

        $m2mdonation = $request->input('donation');
        $donation = (float) preg_replace('/[^\d.]/', '', $m2mdonation);
        $fee = $request->input('fee');
        $cardNumber = $request->input('card_number');
        $expirationDate = $request->input('expiration_date');
        $cvv = $request->input('cvv');
        $first = $request->input('first_name');
        $last = $request->input('last_name');
        $address = $request->input('address');
        $city = $request->input('city');
        $state = $request->input('state');
        $zip = $request->input('zip');
        $email = $request->input('email');
        $total = $request->input('total');
        $amount = (float) preg_replace('/[^\d.]/', '', $total);
        $today = Carbon::today()->format('m-d-Y');

        // Call the load_coordinators function
        $coordinatorData = $this->load_coordinators($chapterId, $chConf, $chPcid);
        $ConfCoorEmail = $coordinatorData['ConfCoorEmail'];
        $coordinator_array = $coordinatorData['coordinator_array'];

        /* Create a merchantAuthenticationType object with authentication details
            retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType;
        $merchantAuthentication->setName(config('settings.authorizenet_api_login_id'));
        $merchantAuthentication->setTransactionKey(config('settings.authorizenet_transaction_key'));

        // Set the transaction's refId
        $refId = 'ref'.time();

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType;
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($expirationDate);
        $creditCard->setCardCode($cvv);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType;
        $paymentOne->setCreditCard($creditCard);

        // Generate a random invoice number
        $randomInvoiceNumber = mt_rand(100000, 999999);
        // Create order information
        $order = new AnetAPI\OrderType;
        $order->setInvoiceNumber($randomInvoiceNumber);
        $order->setDescription('Mother-to-Mother Donation');

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType;
        $customerAddress->setFirstName($first);
        $customerAddress->setLastName($last);
        $customerAddress->setCompany($company);
        $customerAddress->setAddress($address);
        $customerAddress->setCity($city);
        $customerAddress->setState($state);
        $customerAddress->setZip($zip);
        $customerAddress->setCountry('USA');

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType;
        $customerData->setType('individual');
        $customerData->setId($chapterId);
        $customerData->setEmail($email);

        // Add values for transaction settings
        $duplicateWindowSetting = new AnetAPI\SettingType;
        $duplicateWindowSetting->setSettingName('duplicateWindow');
        $duplicateWindowSetting->setSettingValue('60');

        // Add some merchant defined fields. These fields won't be stored with the transaction,
        // but will be echoed back in the response.
        // $merchantDefinedField1 = new AnetAPI\UserFieldType();
        // $merchantDefinedField1->setName('MemberCount');
        // $merchantDefinedField1->setValue($members);

        $merchantDefinedField1 = new AnetAPI\UserFieldType;
        $merchantDefinedField1->setName('Donation');
        $merchantDefinedField1->setValue($donation);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType;
        //$transactionRequestType->setTransactionType('authOnlyTransaction');
        $transactionRequestType->setTransactionType('authCaptureTransaction');
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        $transactionRequestType->addToUserFields($merchantDefinedField1);

        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest;
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == 'Ok') {
                // Since the API request was successful, look for a transaction response and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {

                    $mailData = [
                        'chapterName' => $chapterName,
                        'chapterState' => $chapterState,
                        'pres_fname' => $presDetails->first_name,
                        'pres_lname' => $presDetails->last_name,
                        'pres_street' => $presDetails->street_address,
                        'pres_city' => $presDetails->city,
                        'pres_state' => $presDetails->state,
                        'pres_zip' => $presDetails->zip,
                        'donation' => $donation,
                        'processing' => $fee,
                        'total' => $total,
                        'fname' => $first,
                        'lname' => $last,
                        'email' => $email,
                        'chapterId' => $chapterId,
                        'invoice' => $randomInvoiceNumber,
                        'datePaid' => $today,
                        'chapterAmount' => $donation,
                    ];

                    $to_email = $email;
                    $to_email2 = explode(',', $emailListBoard);
                    $to_email3 = $cor_pcemail;
                    $to_email4 = explode(',', $emailListCoor);
                    $to_email5 = $ConfCoorEmail;
                    $to_email6 = 'dragonmom@msn.com';

                    $existingRecord = Chapters::where('id', $chapterId)->first();

                    Mail::to([$to_email5, $to_email6])
                        ->queue(new PaymentsM2MOnline($mailData, $coordinator_array));

                    if ($donation > 0.00) {
                        $existingRecord->m2m_payment = $donation;
                        $existingRecord->m2m_date = Carbon::today();

                        Mail::to([$to_email])
                            ->cc($to_email3)
                            ->queue(new PaymentsM2MChapterThankYou($mailData));
                    }
                    $existingRecord->save();

                    // Success notification
                    return redirect()->to('/home')->with('success', 'Payment was successfully processed, thank you for your donation!');
                    // return redirect()->route('home')->with('success', 'Payment successful! Transaction ID: ' . $tresponse->getTransId());
                } else {
                    // Transaction failed
                    $error_message = 'Transaction Failed';
                    if ($tresponse->getErrors() != null) {
                        $error_message .= "\n Error Code: ".$tresponse->getErrors()[0]->getErrorCode();
                        $error_message .= "\n Error Message: ".$tresponse->getErrors()[0]->getErrorText();
                    }

                    return redirect()->to('/board/m2mdonation')->with('fail', $error_message);
                }

                // Or, print errors if the API request wasn't successful
            } else {
                // Transaction Failed
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $error_message = 'Transaction Failed';
                    $error_message .= "\n Error Code: ".$tresponse->getErrors()[0]->getErrorCode();
                    $error_message .= "\n Error Message: ".$tresponse->getErrors()[0]->getErrorText();

                    return redirect()->back()->with('fail', $error_message);
                } else {
                    $error_message = 'Transaction Failed';
                    $error_message .= "\n Error Code: ".$response->getMessages()->getMessage()[0]->getCode();
                    $error_message .= "\n Error Message: ".$response->getMessages()->getMessage()[0]->getText();

                    return redirect()->to('/board/m2mdonation')->with('fail', $error_message);
                }
            }
        } else {
            // No response returned
            return redirect()->to('/board/m2mdonation')->with('fail', 'No response returned');
        }
    }

    public function load_coordinators($chapterId, $chConf, $chPcid)
    {
        $chapterDetails = Chapters::find($chapterId)
            ->select('chapters.id as id', 'chapters.name as chapter_name', 'st.state_short_name as state',
                'chapters.conference_id as conf', 'chapters.primary_coordinator_id as pcid')
            ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->first();

        $reportingList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('id', '=', $chPcid)
            ->get();

        foreach ($reportingList as $key => $value) {
            $reportingList[$key] = (array) $value;
        }
        $filterReportingList = array_filter($reportingList[0]);
        unset($filterReportingList['id']);
        unset($filterReportingList['layer0']);
        $filterReportingList = array_reverse($filterReportingList);
        $str = '';
        $array_rows = count($filterReportingList);
        $i = 0;
        $coordinator_array = [];
        foreach ($filterReportingList as $key => $val) {
            $corList = DB::table('coordinators as cd')
                ->select('cd.id as cid', 'cd.first_name as fname', 'cd.last_name as lname', 'cd.email as email', 'cp.short_title as pos')
                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                ->where('cd.id', '=', $val)
                ->get();
            $coordinator_array[$i] = ['id' => $corList[0]->cid,
                'first_name' => $corList[0]->fname,
                'last_name' => $corList[0]->lname,
                'email' => $corList[0]->email,
                'position' => $corList[0]->pos];

            $i++;
        }
        $coordinator_count = count($coordinator_array);

        for ($i = 0; $i < $coordinator_count; $i++) {
            if ($coordinator_array[$i]['position'] == 'RC') {
                $rc_email = $coordinator_array[$i]['email'];
                $rc_id = $coordinator_array[$i]['id'];
            } elseif ($coordinator_array[$i]['position'] == 'CC') {
                $cc_email = $coordinator_array[$i]['email'];
                $cc_id = $coordinator_array[$i]['id'];
            }
        }

        switch ($chConf) {
            case 1:
                $to_email = $cc_email;
                break;
            case 2:
                $to_email = $cc_email;
                break;
            case 3:
                $to_email = $cc_email;
                break;
            case 4:
                $to_email = $cc_email;
                break;
            case 5:
                $to_email = $cc_email;
                break;
            default:
                $to_email = 'admin@momsclub.org';
        }

        return [
            'ConfCoorEmail' => $to_email,
            'coordinator_array' => $coordinator_array,
        ];
    }
}
