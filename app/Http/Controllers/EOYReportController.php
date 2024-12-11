<?php

namespace App\Http\Controllers;

use App\Mail\EOYReviewrAssigned;
use App\Mail\EOYElectionReportReminder;
use App\Mail\EOYFinancialReportReminder;
use App\Mail\EOYLateReportReminder;
use App\Models\Chapter;
use App\Models\FinancialReport;
use App\Models\User;
use App\Http\Controllers\UserController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EOYReportController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->middleware('auth')->except('logout');
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class);
        $this->userController = $userController;
    }

    /**
     * View the EOY Status list
     */
    public function showEOYStatus(Request $request): View
    {
        $user = $request->user();
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        $request->session()->put('corregid', $corRegId);

           // Get the conditions
           $conditions = getPositionConditions($positionId, $secPositionId);

           if ($conditions['coordinatorCondition']) {
            // Load Reporting Tree
            $coordinatorData = $this->userController->loadReportingTree($corId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $year = date('Y');

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'rg.short_name as region', 'st.state_short_name as state', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname',
                'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->join('region as rg', 'rg.id', '=', 'chapters.region')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where(function ($query) {
                $query->where('created_at', '<=', date('Y-06-30'))
                      ->orWhereNull('created_at');
            });

        if ($conditions['founderCondition']) {
                $baseQuery;
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
                $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('chapters.region', '=', $corRegId);
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

        $row_count = count($chapterList);

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('eoyreports.eoystatus')->with($data);
    }

    /**
     * EOY Report Status Reminder Auto Send
     */
    public function showEOYStatusReminder(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corName = $corDetails['first_name'].' '.$corDetails['last_name'];

        //Get Chapter List mapped with login coordinator
        $chapters = Chapter::select('chapters.*', 'chapters.name as name', 'state.state_short_name as state',
            'chapters.primary_coordinator_id as pcid', 'chapters.email as ch_email', 'chapters.start_month_id as start_month',)
            ->join('state', 'chapters.state', '=', 'state.id')
            ->join('financial_report', 'chapters.id', '=', 'financial_report.chapter_id')
            ->where('financial_report.reviewer_id', null)
            ->where(function ($query) {
                $query->where('chapters.report_extension', '=', '0')
                    ->orWhereNull('chapters.report_extension');
            })->where('chapters.conference', $corConfId)
            ->where(function ($query) {
                $query->where('chapters.new_board_submitted', '=', '0')
                    ->orWhereNull('chapters.new_board_submitted')
                    ->orwhere('chapters.financial_report_received', '=', '0')
                    ->orWhereNull('chapters.financial_report_received');
            })
            ->where(function ($query) {
                $query->where('created_at', '<=', date('Y-06-30'))
                      ->orWhereNull('created_at');
            })
            ->where('chapters.is_active', 1)
            ->get();

            if ($chapters->isEmpty()) {
                return redirect()->back()->with('info', 'There are no Chapters with Reports Due.');
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

                $chapterState = $chapter->state;

                $mailData[$chapter->name] = [
                    'chapterName' => $chapter->name,
                    'chapterState' => $chapterState,
                    'boardElectionReportReceived' => $chapter->new_board_submitted,
                    'financialReportReceived' => $chapter->financial_report_received,
                    '990NSubmissionReceived' => $chapter->financial_report_received,
                    'einLetterCopyReceived' => $chapter->ein_letter_path,
                ];
            }

            foreach ($mailData as $chapterName => $data) {
                $to_email = $chapterEmails[$chapterName] ?? [];
                $cc_email = $coordinatorEmails[$chapterName] ?? [];

                if (!empty($to_email)) {
                    Mail::to($to_email)
                        ->cc($cc_email)
                        ->queue(new EOYLateReportReminder($data));
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

        return redirect()->to('/eoy/status')->with('success', 'EOY Late Notices have been successfully sent.');

    }

    /**
     * View the Report Status Details
     */
    public function showEOYStatusView(Request $request, $id)
    {
        $user = User::find($request->user()->id);

        $corDetails = $user->Coordinators;
        if (! $corDetails) {
            return to_route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];

        $chapterList = DB::table('chapters as ch')
            ->select('ch.id', 'ch.name', 'ch.state', 'ch.region', 'ch.new_board_submitted', 'ch.new_board_active', 'ch.financial_report_received', 'financial_report_complete',
                'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id',
                'ch.report_extension', 'ch.extension_notes')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->get();

        $data = ['chapterList' => $chapterList, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr];

        return view('eoyreports.eoystatusview')->with($data);
    }

    /**
     * Update the Report Status
     */
    public function updateEOYStatus(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $userId = $corDetails['user_id'];
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $chapter = Chapter::find($id);
        DB::beginTransaction();
        try {
            $chapter->new_board_submitted = (int) $request->has('ch_board_submitted');
            $chapter->new_board_active = (int) $request->has('ch_board_active');
            $chapter->financial_report_received = (int) $request->has('ch_financial_received');
            $chapter->financial_report_complete = (int) $request->has('ch_financial_complete');
            $chapter->report_extension = (int) $request->has('ch_report_extension');
            $chapter->extension_notes = $request->input('ch_extension_notes');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();

            $report = FinancialReport::find($id);
            if ($request->has('ch_financial_received') != null) {
                $report->submitted = date('Y-m-d H:i:s');
                $report->reviewer_id = $userId;
            }
            if ($request->has('ch_financial_received') == null) {
                $report->submitted = null;
                $report->reviewer_id = null;
            }
            if ($request->has('ch_financial_complete') != null) {
                $report->review_complete = date('Y-m-d H:i:s');
            }
            if ($request->has('ch_financial_complete') == null) {
                $report->review_complete = null;
            }
            $report->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            // Log the error
            Log::error($e);

            return redirect()->to('/eoy/status')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/eoy/status')->with('success', 'Report status successfully updated');
    }

    /**
     * View the Board Info Received list
     */
    public function showEOYBoardReport(Request $request)
    {
        $user = $request->user();
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        $request->session()->put('corconfid', $corConfId);

         // Get the conditions
         $conditions = getPositionConditions($positionId, $secPositionId);

         if ($conditions['coordinatorCondition']) {
            // Load Reporting Tree
            $coordinatorData = $this->userController->loadReportingTree($corId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $year = date('Y');

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'rg.short_name as region', 'st.state_short_name as state', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname',
                'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->join('region as rg', 'rg.id', '=', 'chapters.region')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where(function ($query) {
                $query->where('created_at', '<=', date('Y-06-30'))
                      ->orWhereNull('created_at');
            });

            if ($conditions['founderCondition']) {
                $baseQuery;
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('chapters.region', '=', $corRegId);
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

        $row_count = count($chapterList);

        if (isset($_GET['board'])) {
            $status = '';
            if ($row_count > 0) {
                for ($i = 0; $i < $row_count; $i++) {
                    if ($chapterList[$i]->new_board_submitted && ! $chapterList[$i]->new_board_active) {
                        $status = $this->activateBoard($chapterList[$i]->id, $lastUpdatedBy);
                    }
                }
            }

            if ($status == 'success') {
                return redirect()->to('/eoy/boardreport')->with('success', 'All Board Info has been successfully activated');
            } elseif ($status == 'fail') {
                return redirect()->to('/eoy/boardreport')->with('fail', 'Something went wrong, Please try again.');
            } else {
                return redirect()->to('/eoy/boardreport')->with('info', 'No Incoming Board Members for Activation');
            }
            exit;
        }

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('eoyreports.eoyboardreport')->with($data);
    }

    /**
     * Board Election Report Reminder Auto Send
     */
    public function showEOYBoardReportReminder(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corName = $corDetails['first_name'].' '.$corDetails['last_name'];

        //Get Chapter List mapped with login coordinator
        $chapters = Chapter::select('chapters.*', 'chapters.name as name', 'state.state_short_name as state',
            'chapters.primary_coordinator_id as pcid', 'chapters.email as ch_email', 'chapters.start_month_id as start_month',
            )
            ->join('state', 'chapters.state', '=', 'state.id')
            ->where('chapters.conference', $corConfId)
            ->where('chapters.is_active', 1)
            ->where(function ($query) {
                $query->where('chapters.new_board_submitted', '=', '0')
                    ->orWhereNull('chapters.new_board_submitted');
            })
            ->where(function ($query) {
                $query->where('created_at', '<=', date('Y-06-30'))
                      ->orWhereNull('created_at');
            })
            ->get();

            if ($chapters->isEmpty()) {
                return redirect()->back()->with('info', 'There are no Chapters with Board Reports Due.');
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

                $chapterState = $chapter->state;

                $mailData[$chapter->name] = [
                    'chapterName' => $chapter->name,
                    'chapterState' => $chapterState,
                ];
            }

            foreach ($mailData as $chapterName => $data) {
                $to_email = $chapterEmails[$chapterName] ?? [];
                $cc_email = $coordinatorEmails[$chapterName] ?? [];

                if (!empty($to_email)) {
                    Mail::to($to_email)
                        ->cc($cc_email)
                        ->queue(new EOYElectionReportReminder($data));
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

        return redirect()->to('/eoy/boardreport')->with('success', 'Board Election Reminders have been successfully sent.');

    }

    /**
     * Board Info Report Details
     */
    public function showEOYBoardReportView(Request $request, $chapterId)
    {
        //$corDetails = User::find($request->user()->id)->Coordinators;
        $user = User::find($request->user()->id);

        $corDetails = $user->Coordinators;
        // Check if BoardDetails is not found for the user
        if (! $corDetails) {
            return to_route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $chapterDetails = Chapter::find($chapterId);
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();

        $chapterState = DB::table('state')
            ->select('state_short_name')
            ->where('id', '=', $chapterDetails->state)
            ->get();
        $chapterState = $chapterState[0]->state_short_name;

        $chapterList = DB::table('chapters as ch')
            ->select('ch.id', 'ch.name', 'ch.state', 'ch.territory', 'ch.boundary_issues', 'ch.boundary_issue_notes', 'ch.inquiries_contact', 'ch.website_url', 'ch.website_status',
                'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'ch.new_board_submitted')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '1')
            ->get();

        $PREDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.board_position_id', 'bd.street_address as pre_addr', 'bd.city as pre_city', 'bd.zip as pre_zip', 'bd.phone as pre_phone', 'bd.state as pre_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        if (count($PREDetails) == 0) {
            $PREDetails[0] = ['pre_fname' => '', 'pre_lname' => '', 'pre_email' => '', 'pre_addr' => '', 'pre_city' => '', 'pre_zip' => '', 'pre_phone' => '', 'pre_state' => '', 'ibd_id' => ''];
            $PREDetails = json_decode(json_encode($PREDetails));
        }

        $AVPDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id', 'bd.street_address as avp_addr', 'bd.city as avp_city', 'bd.zip as avp_zip', 'bd.phone as avp_phone', 'bd.state as avp_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '2')
            ->get();
        if (count($AVPDetails) == 0) {
            $AVPDetails[0] = ['avp_fname' => '', 'avp_lname' => '', 'avp_email' => '', 'avp_addr' => '', 'avp_city' => '', 'avp_zip' => '', 'avp_phone' => '', 'avp_state' => '', 'ibd_id' => ''];
            $AVPDetails = json_decode(json_encode($AVPDetails));
        }

        $MVPDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id', 'bd.street_address as mvp_addr', 'bd.city as mvp_city', 'bd.zip as mvp_zip', 'bd.phone as mvp_phone', 'bd.state as mvp_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '3')
            ->get();
        if (count($MVPDetails) == 0) {
            $MVPDetails[0] = ['mvp_fname' => '', 'mvp_lname' => '', 'mvp_email' => '', 'mvp_addr' => '', 'mvp_city' => '', 'mvp_zip' => '', 'mvp_phone' => '', 'mvp_state' => '', 'ibd_id' => ''];
            $MVPDetails = json_decode(json_encode($MVPDetails));
        }

        $TRSDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id', 'bd.street_address as trs_addr', 'bd.city as trs_city', 'bd.zip as trs_zip', 'bd.phone as trs_phone', 'bd.state as trs_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '4')
            ->get();
        if (count($TRSDetails) == 0) {
            $TRSDetails[0] = ['trs_fname' => '', 'trs_lname' => '', 'trs_email' => '', 'trs_addr' => '', 'trs_city' => '', 'trs_zip' => '', 'trs_phone' => '', 'trs_state' => '', 'ibd_id' => ''];
            $TRSDetails = json_decode(json_encode($TRSDetails));
        }

        $SECDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.board_position_id', 'bd.street_address as sec_addr', 'bd.city as sec_city', 'bd.zip as sec_zip', 'bd.phone as sec_phone', 'bd.state as sec_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '5')
            ->get();
        if (count($SECDetails) == 0) {
            $SECDetails[0] = ['sec_fname' => '', 'sec_lname' => '', 'sec_email' => '', 'sec_addr' => '', 'sec_city' => '', 'sec_zip' => '', 'sec_phone' => '', 'sec_state' => '', 'ibd_id' => ''];
            $SECDetails = json_decode(json_encode($SECDetails));
        }

        $data = ['chapterState' => $chapterState, 'stateArr' => $stateArr, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'PREDetails' => $PREDetails, 'chapterList' => $chapterList];

        return view('eoyreports.eoyboardreportview')->with($data);
    }

/**
     * Board Info Report Details
     */
    public function editBoardReport(Request $request, $chapterId)
    {
        //$corDetails = User::find($request->user()->id)->Coordinators;
        $user = User::find($request->user()->id);

        $corDetails = $user->Coordinators;
        // Check if BoardDetails is not found for the user
        if (! $corDetails) {
            return to_route('home');
        }

        $loggedInName = $corDetails['first_name'].' '.$corDetails['last_name'];
        $positionId = $corDetails['position_id'];
        $request->session()->put('positionid', $positionId);

        $financial_report_array = FinancialReport::find($chapterId);

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $chapterDetails = Chapter::find($chapterId);
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();

        $chapterState = DB::table('state')
            ->select('state_short_name')
            ->where('id', '=', $chapterDetails->state)
            ->get();
        $chapterState = $chapterState[0]->state_short_name;

        $webStatusArr = ['0' => 'Website Not Linked', '1' => 'Website Linked', '2' => 'Add Link Requested', '3' => 'Do Not Link'];

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address',
                'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '1')
            ->get();

        $PREDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.board_position_id', 'bd.street_address as pre_addr', 'bd.city as pre_city', 'bd.zip as pre_zip', 'bd.phone as pre_phone', 'bd.state as pre_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        if (count($PREDetails) == 0) {
            $PREDetails[0] = ['pre_fname' => '', 'pre_lname' => '', 'pre_email' => '', 'pre_addr' => '', 'pre_city' => '', 'pre_zip' => '', 'pre_phone' => '', 'pre_state' => '', 'ibd_id' => ''];
            $PREDetails = json_decode(json_encode($PREDetails));
        }

        $AVPDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id', 'bd.street_address as avp_addr', 'bd.city as avp_city', 'bd.zip as avp_zip', 'bd.phone as avp_phone', 'bd.state as avp_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '2')
            ->get();
        if (count($AVPDetails) == 0) {
            $AVPDetails[0] = ['avp_fname' => '', 'avp_lname' => '', 'avp_email' => '', 'avp_addr' => '', 'avp_city' => '', 'avp_zip' => '', 'avp_phone' => '', 'avp_state' => '', 'ibd_id' => ''];
            $AVPDetails = json_decode(json_encode($AVPDetails));
        }

        $MVPDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id', 'bd.street_address as mvp_addr', 'bd.city as mvp_city', 'bd.zip as mvp_zip', 'bd.phone as mvp_phone', 'bd.state as mvp_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '3')
            ->get();
        if (count($MVPDetails) == 0) {
            $MVPDetails[0] = ['mvp_fname' => '', 'mvp_lname' => '', 'mvp_email' => '', 'mvp_addr' => '', 'mvp_city' => '', 'mvp_zip' => '', 'mvp_phone' => '', 'mvp_state' => '', 'ibd_id' => ''];
            $MVPDetails = json_decode(json_encode($MVPDetails));
        }

        $TRSDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id', 'bd.street_address as trs_addr', 'bd.city as trs_city', 'bd.zip as trs_zip', 'bd.phone as trs_phone', 'bd.state as trs_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '4')
            ->get();
        if (count($TRSDetails) == 0) {
            $TRSDetails[0] = ['trs_fname' => '', 'trs_lname' => '', 'trs_email' => '', 'trs_addr' => '', 'trs_city' => '', 'trs_zip' => '', 'trs_phone' => '', 'trs_state' => '', 'ibd_id' => ''];
            $TRSDetails = json_decode(json_encode($TRSDetails));
        }

        $SECDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.board_position_id', 'bd.street_address as sec_addr', 'bd.city as sec_city', 'bd.zip as sec_zip', 'bd.phone as sec_phone', 'bd.state as sec_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '5')
            ->get();
        if (count($SECDetails) == 0) {
            $SECDetails[0] = ['sec_fname' => '', 'sec_lname' => '', 'sec_email' => '', 'sec_addr' => '', 'sec_city' => '', 'sec_zip' => '', 'sec_phone' => '', 'sec_state' => '', 'ibd_id' => ''];
            $SECDetails = json_decode(json_encode($SECDetails));
        }

        $data = ['chapterState' => $chapterState, 'stateArr' => $stateArr, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'PREDetails' => $PREDetails, 'chapterList' => $chapterList,
        'webStatusArr' => $webStatusArr];

        return view('eoyreports.editboardreport')->with($data);
    }

    /**
     * Update Board Report (store)
     */
    public function updateEOYBoardReport(Request $request, $chapter_id): RedirectResponse
    {
        $user = $request->user();
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;

        if ($request->input('submit_type') == 'activate_board') {
            $status = $this->activateBoard($chapter_id, $lastUpdatedBy);
            if ($status == 'success') {
                return redirect()->to('/eoy/boardreport')->with('success', 'Board Info has been successfully activated');
            } elseif ($status == 'fail') {
                return redirect()->to('/eoy/boardreport')->with('fail', 'Something went wrong, Please try again.');
            }
        }

        $chapter = Chapter::find($chapter_id);
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
        if (!str_starts_with($website, 'http://') && !str_starts_with($website, 'https://')) {
            $website = 'http://' . $website;
        }

        DB::beginTransaction();
        try {
            $chapter->inquiries_contact = $request->input('InquiriesContact');
            $chapter->boundary_issues = $request->input('BoundaryStatus');
            $chapter->boundary_issue_notes = $issue_note;
            $chapter->website_url = $website;
            $chapter->website_status = $ch_webstatus;
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->new_board_submitted = 1;
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();

            //President Info
            if ($request->input('ch_pre_fname') != '' && $request->input('ch_pre_lname') != '' && $request->input('ch_pre_email') != '') {
                $PREDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $chapter_id)
                    ->where('board_position_id', '=', '1')
                    ->get();
                $id = $request->input('presID');
                if (count($PREDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $id)
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
                            'chapter_id' => $chapter_id,
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
                $id = $request->input('avpID');
                DB::table('incoming_board_member')
                    ->where('id', $id)
                    ->delete();
            }
            if ($request->input('ch_avp_fname') != '' && $request->input('ch_avp_lname') != '' && $request->input('ch_avp_email') != '') {
                $AVPDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $chapter_id)
                    ->where('board_position_id', '=', '2')
                    ->get();
                $id = $request->input('avpID');
                if (count($AVPDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $id)
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
                            'chapter_id' => $chapter_id,
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
                $id = $request->input('mvpID');
                DB::table('incoming_board_member')
                    ->where('id', $id)
                    ->delete();
            }
            if ($request->input('ch_mvp_fname') != '' && $request->input('ch_mvp_lname') != '' && $request->input('ch_mvp_email') != '') {
                $MVPDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $chapter_id)
                    ->where('board_position_id', '=', '3')
                    ->get();
                $id = $request->input('mvpID');
                if (count($MVPDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $id)
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
                            'chapter_id' => $chapter_id,
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
                $id = $request->input('trsID');
                DB::table('incoming_board_member')
                    ->where('id', $id)
                    ->delete();
            }
            if ($request->input('ch_trs_fname') != '' && $request->input('ch_trs_lname') != '' && $request->input('ch_trs_email') != '') {
                $TRSDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $chapter_id)
                    ->where('board_position_id', '=', '4')
                    ->get();
                $id = $request->input('trsID');
                if (count($TRSDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $id)
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
                            'chapter_id' => $chapter_id,
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
                $id = $request->input('secID');
                DB::table('incoming_board_member')
                    ->where('id', $id)
                    ->delete();
            }
            if ($request->input('ch_sec_fname') != '' && $request->input('ch_sec_lname') != '' && $request->input('ch_sec_email') != '') {
                $SECDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $chapter_id)
                    ->where('board_position_id', '=', '5')
                    ->get();
                $id = $request->input('secID');
                if (count($SECDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $id)
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
                            'chapter_id' => $chapter_id,
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
            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->back()->with('success', 'Board Info has been Saved');
    }

     /**
     * View the Financial Reports List
     */
    public function showEOYFinancialReport(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
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

        $year = date('Y');

        $baseQuery = DB::table('chapters as ch')
            ->select('ch.id as chap_id', 'ch.primary_coordinator_id as primary_coordinator_id', 'ch.name as name', 'ch.financial_report_received as financial_report_received',
                'ch.financial_report_complete as report_complete', 'ch.report_extension as report_extension', 'ch.extension_notes as extension_notes', 'cd.id AS cord_id', 'cd.first_name as fname', 'cd.last_name as lname', 'st.state_short_name as state',
                'fr.submitted as report_received', 'fr.review_complete as review_complete', 'fr.post_balance as post_balance', 'fr.financial_pdf_path as financial_pdf_path', 'cd_reviewer.first_name as pcfname', 'cd_reviewer.last_name as pclname',
                'rg.short_name as reg', 'cf.short_name as conf')
            ->join('state as st', 'ch.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'ch.conference', '=', 'cf.id')
            ->leftJoin('region as rg', 'rg.id', '=', 'ch.region')
            ->leftJoin('financial_report as fr', 'fr.chapter_id', '=', 'ch.id')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->leftJoin('coordinators as cd_reviewer', 'cd_reviewer.id', '=', 'fr.reviewer_id')
            ->where(function ($query) {
                $query->where('created_at', '<=', date('Y-06-30'))
                      ->orWhereNull('created_at');
            })
            ->where('ch.is_active', 1);

            if ($conditions['founderCondition']) {
                $baseQuery;
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
                        $baseQuery->where('ch.conference', '=', $corConfId);
                    } elseif ($conditions['regionalCoordinatorCondition']) {
                        $baseQuery->where('ch.region', '=', $corRegId);
        } else {
            $baseQuery->whereIn('ch.primary_coordinator_id', $inQryArr);
        }

        if (isset($_GET['check']) && $_GET['check'] == 'yes') {
            $checkBoxStatus = 'checked';
            $baseQuery->where('fr.reviewer_id', $corId)
                ->orderBy('st.state_short_name')
                ->orderBy('ch.name');
        } else {
            $checkBoxStatus = '';
            $baseQuery->orderBy('st.state_short_name')
                ->orderBy('ch.name');
        }

        if (isset($_GET['check2']) && $_GET['check2'] == 'yes') {
            $checkBox2Status = 'checked';
            $baseQuery->where('ch.primary_coordinator_id', '=', $corId)
                ->orderBy('st.state_short_name')
                ->orderBy('ch.name');
        } else {
            $checkBox2Status = '';
            $baseQuery->orderBy('st.state_short_name')
                ->orderBy('ch.name');
        }

        $chapterList = $baseQuery->get();

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status];

        return view('eoyreports.eoyfinancialreport')->with($data);
    }

    /**
     * Financial Report Reminder Auto Send
     */
    public function showEOYFinancialReportReminder(Request $request): RedirectResponse
{
    $corDetails = User::find($request->user()->id)->Coordinators;
    $corId = $corDetails['id'];
    $corConfId = $corDetails['conference_id'];
    $corName = $corDetails['first_name'].' '.$corDetails['last_name'];

    // Get Chapter List mapped with login coordinator
    $chapters = Chapter::select('chapters.*', 'chapters.name as name', 'state.state_short_name as state',
        'chapters.primary_coordinator_id as pcid', 'chapters.email as ch_email', 'chapters.start_month_id as start_month',)
        ->join('state', 'chapters.state', '=', 'state.id')
        ->join('financial_report', 'chapters.id', '=', 'financial_report.chapter_id')
        ->where('financial_report.reviewer_id', null)
        ->where('chapters.conference', $corConfId)
        ->where('chapters.is_active', 1)
        ->where(function ($query) {
            $query->where('chapters.report_extension', '=', '0')
                ->orWhereNull('chapters.report_extension');
        })
        ->where(function ($query) {
            $query->where('chapters.financial_report_received', '=', '0')
                ->orWhereNull('chapters.financial_report_received');
        })
        ->where(function ($query) {
            $query->where('created_at', '<=', date('Y-06-30'))
                  ->orWhereNull('created_at');
        })
        ->get();

    if ($chapters->isEmpty()) {
        return redirect()->back()->with('info', 'There are no Chapters with Financial Reports Due.');
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

        $chapterState = $chapter->state;

        $mailData[$chapter->name] = [
            'chapterName' => $chapter->name,
            'chapterState' => $chapterState,
        ];
    }

    foreach ($mailData as $chapterName => $data) {
        $to_email = $chapterEmails[$chapterName] ?? [];
        $cc_email = $coordinatorEmails[$chapterName] ?? [];

        if (!empty($to_email)) {
            Mail::to($to_email)
                ->cc($cc_email)
                ->queue(new EOYFinancialReportReminder($data));
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

        return redirect()->to('/eoy/financialreport')->with('success', 'Financial Report Reminders have been successfully sent.');

    }

    /**
     * Financial Report for Coordinator side for Reviewing of Chapters
     */
    // public function showEOYFinancialReportView(Request $request, $chapterId)
    // {
    //     //$corDetails = User::find($request->user()->id)->Coordinators;
    //     $user = User::find($request->user()->id);

    //     $corDetails = $user->Coordinators;
    //     // Check if BoardDetails is not found for the user
    //     if (! $corDetails) {
    //         return to_route('home');
    //     }

    //     $loggedInName = $corDetails['first_name'].' '.$corDetails['last_name'];
    //     $positionId = $corDetails['position_id'];
    //     $request->session()->put('positionid', $positionId);

    //     $financial_report_array = FinancialReport::find($chapterId);
    //     $chapterDetails = DB::table('chapters')
    //         ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.financial_report_received as financial_report_received', 'chapters.primary_coordinator_id as pcid', 'chapters.balance as balance', 'st.state_short_name as state',
    //             'chapters.financial_report_complete as financial_report_complete', 'chapters.financial_pdf_path as financial_pdf_path')
    //         ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
    //         ->where('chapters.is_active', '=', '1')
    //         ->where('chapters.id', '=', $chapterId)
    //         ->get();

    //     $submitted = $chapterDetails[0]->financial_report_received;
    //     $balance = $chapterDetails[0]->balance;
    //     $pcid = $chapterDetails[0]->pcid;

    //     $reportingList = DB::table('coordinator_reporting_tree')
    //     ->select('*')
    //     ->where('id', '=', $pcid)
    //     ->get();

    // if ($reportingList->isNotEmpty()) {
    //     $reportingList = (array) $reportingList[0];
    //     $filterReportingList = array_filter($reportingList);
    //     unset($filterReportingList['id'], $filterReportingList['layer0']);
    //     $filterReportingList = array_reverse($filterReportingList);

    //     $i = 0;
    //     foreach ($filterReportingList as $key => $val) {
    //         $corList = DB::table('coordinators as cd')
    //             ->select('cd.id as cid', 'cd.first_name as fname', 'cd.last_name as lname', 'cp.short_title as pos')
    //             ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
    //             ->where('cd.id', '=', $val)
    //             ->where('cd.is_active', '=', 1)
    //             ->get();

    //         if ($corList->isNotEmpty()) {
    //         $reviewerList[$i] = ['cid' => $corList[0]->cid, 'cname' => $corList[0]->fname.' '.$corList[0]->lname.' ('.$corList[0]->pos.')'];
    //             $i++;
    //         }
    //     }
    // }

    //     $data = ['reviewerList' => $reviewerList, 'chapterid' => $chapterId, 'financial_report_array' => $financial_report_array, 'loggedInName' => $loggedInName, 'balance' => $balance, 'submitted' => $submitted, 'chapterDetails' => $chapterDetails];

    //     return view('eoyreports.eoyfinancialreportview')->with($data);
    // }

    /**
     * Financial Report for Coordinator side for Reviewing of Chapters
     */
    public function reviewFinancialReport(Request $request, $chapterId)
    {
        //$corDetails = User::find($request->user()->id)->Coordinators;
        $user = User::find($request->user()->id);

        $corDetails = $user->Coordinators;
        // Check if BoardDetails is not found for the user
        if (! $corDetails) {
            return to_route('home');
        }

        $loggedInName = $corDetails['first_name'].' '.$corDetails['last_name'];
        $positionId = $corDetails['position_id'];
        $request->session()->put('positionid', $positionId);

        $financial_report_array = FinancialReport::find($chapterId);
        $chapterDetails = DB::table('chapters')
            ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.financial_report_received as financial_report_received', 'chapters.primary_coordinator_id as pcid', 'chapters.balance as balance', 'st.state_short_name as state',
                'chapters.financial_report_complete as financial_report_complete', 'chapters.financial_pdf_path as financial_pdf_path')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('chapters.id', '=', $chapterId)
            ->get();

        $submitted = $chapterDetails[0]->financial_report_received;
        $balance = $chapterDetails[0]->balance;
        $pcid = $chapterDetails[0]->pcid;

        $reportingList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('id', '=', $pcid)
            ->get();

        if ($reportingList->isNotEmpty()) {
            $reportingList = (array) $reportingList[0];
            $filterReportingList = array_filter($reportingList);
            unset($filterReportingList['id'], $filterReportingList['layer0']);
            $filterReportingList = array_reverse($filterReportingList);

            $i = 0;
            foreach ($filterReportingList as $key => $val) {
                $corList = DB::table('coordinators as cd')
                    ->select('cd.id as cid', 'cd.first_name as fname', 'cd.last_name as lname', 'cp.short_title as pos')
                    ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                    ->where('cd.id', '=', $val)
                    ->where('cd.is_active', '=', 1)
                    ->get();

                if ($corList->isNotEmpty()) {
                $reviewerList[$i] = ['cid' => $corList[0]->cid, 'cname' => $corList[0]->fname.' '.$corList[0]->lname.' ('.$corList[0]->pos.')'];
                    $i++;
                }
            }
        }

        $data = ['reviewerList' => $reviewerList, 'chapterid' => $chapterId, 'financial_report_array' => $financial_report_array, 'loggedInName' => $loggedInName, 'balance' => $balance, 'submitted' => $submitted, 'chapterDetails' => $chapterDetails];

        return view('eoyreports.reviewfinancialreport')->with($data);
    }

    /**
     * Save Financial Report Review
     */
    public function updateEOYFinancialReport(Request $request, $chapter_id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $userName = $corDetails['first_name'].' '.$corDetails['last_name'];

        $input = $request->all();
        $farthest_step_visited_coord = $input['FurthestStep'];
        $reviewer_id = isset($input['AssignedReviewer']) ? $input['AssignedReviewer'] : null;
        $reportReceived = $input['submitted'];
        $submitType = $input['submit_type'];
            $step_1_notes_log = $input['Step1_Log'];
            $step_2_notes_log = $input['Step2_Log'];
            $step_3_notes_log = $input['Step3_Log'];
            $step_4_notes_log = $input['Step4_Log'];
            $step_5_notes_log = $input['Step5_Log'];
            $step_6_notes_log = $input['Step6_Log'];
            $step_7_notes_log = $input['Step7_Log'];
            $step_8_notes_log = $input['Step8_Log'];
            $step_9_notes_log = $input['Step9_Log'];
            $step_10_notes_log = $input['Step10_Log'];
            $step_11_notes_log = $input['Step11_Log'];
            $step_12_notes_log = $input['Step12_Log'];
            $step_13_notes_log = $input['Step13_Log'];

            $reviewer_email_message = $input['reviewer_email_message'];

            // Step 1 - Dues
            $check_roster_attached = isset($input['checkRosterAttached']) ? $input['checkRosterAttached'] : null;
            $check_renewal_seems_right = isset($input['checkRenewalSeemsRight']) ? $input['checkRenewalSeemsRight'] : null;

            // Step 3 - Service
            $check_minimum_service_project = isset($input['checkServiceProject']) ? $input['checkServiceProject'] : null;
            $check_m2m_donation = isset($input['checkM2MDonation']) ? $input['checkM2MDonation'] : null;

            // Step 4 - Parties
            $check_party_percentage = isset($input['check_party_percentage']) ? $input['check_party_percentage'] : null;

            //Step - Financials
            $check_total_income_less = isset($input['checkTotalIncome']) ? $input['checkTotalIncome'] : null;

            //Step 8 - Reconciliation
            $check_beginning_balance = isset($input['check_beginning_balance']) ? $input['check_beginning_balance'] : null;
            $check_bank_statement_included = isset($input['checkBankStatementIncluded']) ? $input['checkBankStatementIncluded'] : null;
            $check_bank_statement_matches = isset($input['checkBankStatementMatches']) ? $input['checkBankStatementMatches'] : null;

            $post_balance = $input['post_balance'];
            $post_balance = str_replace(',', '', $post_balance);
            $post_balance = $post_balance === '' ? null : $post_balance;

            //Step 9 - Questions
            $check_purchased_pins = isset($input['checkPurchasedPins']) ? $input['checkPurchasedPins'] : null;
            $check_purchased_mc_merch = isset($input['checkPurchasedMCMerch']) ? $input['checkPurchasedMCMerch'] : null;
            $check_offered_merch = isset($input['checkOfferedMerch']) ? $input['checkOfferedMerch'] : null;
            $check_bylaws_available = isset($input['checkBylawsMadeAvailable']) ? $input['checkBylawsMadeAvailable'] : null;
            $check_sistered_another_chapter = isset($input['checkSisteredAnotherChapter']) ? $input['checkSisteredAnotherChapter'] : null;
            $check_attended_training = isset($input['checkAttendedTraining']) ? $input['checkAttendedTraining'] : null;
            $check_current_990N_included = isset($input['checkCurrent990NAttached']) ? $input['checkCurrent990NAttached'] : null;

            // Step 10 - Awards
            $check_award_1_approved = isset($input['checkAward1Approved']) ? $input['checkAward1Approved'] : null;
            $check_award_2_approved = isset($input['checkAward2Approved']) ? $input['checkAward2Approved'] : null;
            $check_award_3_approved = isset($input['checkAward3Approved']) ? $input['checkAward3Approved'] : null;
            $check_award_4_approved = isset($input['checkAward4Approved']) ? $input['checkAward4Approved'] : null;
            $check_award_5_approved = isset($input['checkAward5Approved']) ? $input['checkAward5Approved'] : null;

            $chapterDetails = DB::table('chapters')
                ->select('chapters.*', 'st.state_short_name as state_short_name')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.id', '=', $chapter_id)
                ->get();
            $chapter_conf = $chapterDetails[0]->conference;
            $chapter_state = $chapterDetails[0]->state_short_name;
            $chapter_name = $chapterDetails[0]->name;
            $chapter_country = $chapterDetails[0]->country;

            // Links for uploaded documents
            $files = DB::table('financial_report')
                ->select('*')
                ->where('chapter_id', '=', $chapter_id)
                ->get();

            $roster_path = $files[0]->roster_path;
            $file_irs_path = $files[0]->file_irs_path;
            $bank_statement_included_path = $files[0]->bank_statement_included_path;
            $bank_statement_2_included_path = $files[0]->bank_statement_2_included_path;
            $completed_name = $files[0]->completed_name;
            $completed_email = $files[0]->completed_email;

            $Reviewer = DB::table('coordinators')
                ->select('coordinators.*')
                ->where('coordinators.id', '=', $reviewer_id)
                ->get();

            $ReviewerEmail = $Reviewer[0]->email;

            DB::beginTransaction();
            try {
                $report = FinancialReport::find($chapter_id);
                $report->reviewer_id = $reviewer_id;
                $report->step_1_notes_log = $step_1_notes_log;
                $report->step_2_notes_log = $step_2_notes_log;
                $report->step_3_notes_log = $step_3_notes_log;
                $report->step_4_notes_log = $step_4_notes_log;
                $report->step_5_notes_log = $step_5_notes_log;
                $report->step_6_notes_log = $step_6_notes_log;
                $report->step_7_notes_log = $step_7_notes_log;
                $report->step_8_notes_log = $step_8_notes_log;
                $report->step_9_notes_log = $step_9_notes_log;
                $report->step_10_notes_log = $step_10_notes_log;
                $report->step_11_notes_log = $step_11_notes_log;
                $report->step_12_notes_log = $step_12_notes_log;
                $report->step_13_notes_log = $step_13_notes_log;
                $report->check_roster_attached = $check_roster_attached;
                $report->check_renewal_seems_right = $check_renewal_seems_right;
                $report->check_minimum_service_project = $check_minimum_service_project;
                $report->check_m2m_donation = $check_m2m_donation;
                $report->check_party_percentage = $check_party_percentage;
                $report->check_attended_training = $check_attended_training;
                $report->check_bank_statement_matches = $check_bank_statement_matches;
                $report->check_bank_statement_included = $check_bank_statement_included;
                $report->check_beginning_balance = $check_beginning_balance;
                $report->post_balance = $post_balance;
                $report->check_purchased_pins = $check_purchased_pins;
                $report->check_purchased_mc_merch = $check_purchased_mc_merch;
                $report->check_offered_merch = $check_offered_merch;
                $report->check_bylaws_available = $check_bylaws_available;
                $report->check_current_990N_included = $check_current_990N_included;
                $report->check_total_income_less = $check_total_income_less;
                $report->check_sistered_another_chapter = $check_sistered_another_chapter;
                $report->check_award_1_approved = $check_award_1_approved;
                $report->check_award_2_approved = $check_award_2_approved;
                $report->check_award_3_approved = $check_award_3_approved;
                $report->check_award_4_approved = $check_award_4_approved;
                $report->check_award_5_approved = $check_award_5_approved;
                $report->farthest_step_visited_coord = $farthest_step_visited_coord;
                if ($submitType == 'review_complete') {
                    $report->review_complete = date('Y-m-d H:i:s');
                }

                // Send email to new Assigned Reviewer//
                $to_email = $ReviewerEmail;
                $mailData = [
                    'chapterid' => $chapter_id,
                    'chapter_name' => $chapter_name,
                    'chapter_state' => $chapter_state,
                    'completed_name' => $completed_name,
                    'completed_email' => $completed_email,
                    'roster_path' => $roster_path,
                    'file_irs_path' => $file_irs_path,
                    'bank_statement_included_path' => $bank_statement_included_path,
                    'bank_statement_2_included_path' => $bank_statement_2_included_path,
                    'reviewer_email_message' => $reviewer_email_message,
                    'userName' => $userName,
                ];

                if ($report->isDirty('reviewer_id')) {
                    Mail::to($to_email)
                        ->queue(new EOYReviewrAssigned($mailData));
                }

                $report->save();

                $chapter = Chapter::find($chapter_id);

                if ($submitType == 'review_complete') {
                    $chapter->financial_report_complete = 1;
                }

                $chapter->save();

                DB::commit();
                if ($submitType == 'review_complete') {
                    return redirect()->back()->with('success', 'Report has been successfully Marked as Review Complete');
                } else {
                    return redirect()->back()->with('success', 'Report has been successfully Updated');
                }

            } catch (\Exception $e) {
                DB::rollback();
                // Log the error
                Log::error($e);
                //throw $e;     // Show on screen error intead of message - use only for testing
            }

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
    }

     /**
     * Unsubmit Report
     */
    public function updateUnsubmit(Request $request, $chapter_id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $userId = $corDetails['user_id'];
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $chapter = Chapter::find($chapter_id);
        DB::beginTransaction();
        try {
            $chapter->financial_report_received = null;
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();

            $report = FinancialReport::find($chapter_id);
            // $report->farthest_step_visited_coord = '13';
            $report->submitted = null;
            $report->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            // Log the error
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }
            return redirect()->back()->with('success', 'Report has been successfully Unsubmitted.');
    }

    /**
     * Clear Report Review
     */
    public function updateClearReview(Request $request, $chapter_id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $userId = $corDetails['user_id'];
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $chapter = Chapter::find($chapter_id);
        DB::beginTransaction();
        try {
            $chapter->financial_report_received = '1';
            $chapter->financial_report_complete = null;
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();

            $report = FinancialReport::find($chapter_id);
            // $report->farthest_step_visited_coord = '13';
            $report->review_complete = null;
            $report->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            // Log the error
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }
            return redirect()->back()->with('success', 'Review Complete has been successfully Cleared.');
    }

     /**
     * View the EOY Attachments list
     */
    public function showEOYAttachments(Request $request): View
    {
        $user = $request->user();
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        $request->session()->put('corregid', $corRegId);

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
            // Load Reporting Tree
            $coordinatorData = $this->userController->loadReportingTree($corId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $year = date('Y');

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'rg.short_name as region', 'st.state_short_name as state', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname',
                'fr.roster_path as roster_path', 'fr.file_irs_path as file_irs_path', 'fr.bank_statement_included_path as bank_statement_included_path',
                'fr.bank_statement_2_included_path as bank_statement_2_included_path', 'fr.check_current_990N_verified_IRS as check_current_990N_verified_IRS',
                'fr.check_current_990N_notes as check_current_990N_notes', 'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('financial_report as fr', 'fr.chapter_id', '=', 'chapters.id')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->leftJoin('region as rg', 'rg.id', '=', 'chapters.region')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where(function ($query) {
                $query->where('created_at', '<=', date('Y-06-30'))
                      ->orWhereNull('created_at');
            });

            if ($conditions['founderCondition']) {
                $baseQuery;
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('chapters.region', '=', $corRegId);
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

        $row_count = count($chapterList);

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('eoyreports.eoyattachments')->with($data);
    }

    /**
     * View the Attachments Details
     */
    public function showEOYAttachmentsView(Request $request, $id)
    {
        $user = User::find($request->user()->id);

        $corDetails = $user->Coordinators;
        if (! $corDetails) {
            return to_route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];

        $chapterList = DB::table('chapters as ch')
            ->select('ch.id', 'ch.name', 'ch.state', 'ch.ein', 'fr.roster_path as roster_path', 'fr.file_irs_path as file_irs_path', 'fr.bank_statement_included_path as bank_statement_included_path',
                'fr.bank_statement_2_included_path as bank_statement_2_included_path', 'fr.check_current_990N_verified_IRS as check_current_990N_verified_IRS', 'fr.check_current_990N_notes as check_current_990N_notes')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->leftJoin('financial_report as fr', 'fr.chapter_id', '=', 'ch.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->get();

        $data = ['chapterList' => $chapterList, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr];

        return view('eoyreports.eoyattachmentsview')->with($data);
    }

    /**
     * Update the Attachments Details
     */
    public function updateEOYAttachments(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $userId = $corDetails['user_id'];
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $report = FinancialReport::find($id);
        DB::beginTransaction();
        try {
            $report->check_current_990N_verified_IRS = (int) $request->has('irs_verified');
            $report->check_current_990N_notes = $request->input('irs_notes');
            $report->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            // Log the error
            Log::error($e);

            return redirect()->to('/eoy/attachments')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/eoy/attachments')->with('success', 'Report attachments successfully updated');
    }

    /**
     * Boundaires Issues
     */
    public function showEOYBoundaries(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        $request->session()->put('corconfid', $corConfId);

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
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->leftJoin('region as rg', 'rg.id', '=', 'chapters.region')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('chapters.boundary_issues', '=', '1')
            ->where('chapters.new_board_submitted', '=', '1')
            ->where('bd.board_position_id', '=', '1');

            if ($conditions['founderCondition']) {
                $baseQuery;
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
                $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('chapters.region', '=', $corRegId);
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

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('eoyreports.eoyboundaries')->with($data);
    }

    /**
     * View the Boundary Details
     */
    public function showEOYBoundariesView(Request $request, $id)
    {
        //$corDetails = User::find($request->user()->id)->Coordinators;
        $user = User::find($request->user()->id);

        $corDetails = $user->Coordinators;
        // Check if CorDetails is not found for the user
        if (! $corDetails) {
            return to_route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];

        $chapterList = DB::table('chapters as ch')
            ->select('ch.id', 'ch.conference', 'ch.region', 'ch.country', 'ch.state', 'ch.name', 'ch.territory', 'ch.boundary_issues', 'ch.boundary_issue_notes', 'ch.boundary_issue_resolved')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->get();
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name')
            ->get();

        $data = ['chapterList' => $chapterList, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr];

        return view('eoyreports.eoyboundariesview')->with($data);
    }

    /**
     * Update Boundary Details
     */
    public function updateEOYBoundaries5(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $chapter = Chapter::find($id);
        DB::beginTransaction();
        try {
            $chapter->territory = $request->input('ch_territory');
            $chapter->boundary_issue_resolved = (int) $request->has('ch_resolved');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->to('/eoy/boundaries')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/eoy/boundaries')->with('success', 'Boundary issue has been successfully updated');
    }

    /**
     * List of Chapter Awards
     */
    public function showEOYAwards(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        $request->session()->put('corconfid', $corConfId);

         // Get the conditions
         $conditions = getPositionConditions($positionId, $secPositionId);

         if ($conditions['coordinatorCondition']) {
            // Load Reporting Tree
            $coordinatorData = $this->userController->loadReportingTree($corId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = DB::table('chapters as ch')
            ->select('ch.id as id', 'ch.primary_coordinator_id as primary_coordinator_id', 'ch.name as name',
            'cd.id AS cord_id', 'cd.first_name as fname', 'cd.last_name as lname', 'st.state_short_name as state',
            'fr.award_1_nomination_type', 'fr.award_2_nomination_type',
            'fr.award_3_nomination_type', 'fr.award_4_nomination_type', 'fr.award_5_nomination_type',
            'fr.check_award_1_approved as award_1_approved', 'fr.check_award_2_approved as award_2_approved',
            'fr.check_award_3_approved as award_3_approved', 'fr.check_award_4_approved as award_4_approved',
            'fr.check_award_5_approved as award_5_approved',
                'rg.short_name as reg', 'cf.short_name as conf')
            ->join('state as st', 'ch.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'ch.conference', '=', 'cf.id')
            ->leftJoin('region as rg', 'rg.id', '=', 'ch.region')
            ->leftJoin('financial_report as fr', 'fr.chapter_id', '=', 'ch.id')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->leftJoin('coordinators as cd_reviewer', 'cd_reviewer.id', '=', 'fr.reviewer_id')
            ->where(function ($query) {
                $query->whereNotNull('fr.award_1_nomination_type')
                    ->orWhereNotNull('fr.award_2_nomination_type')
                    ->orWhereNotNull('fr.award_3_nomination_type')
                    ->orWhereNotNull('fr.award_4_nomination_type')
                    ->orWhereNotNull('fr.award_5_nomination_type');
            })
            ->where(function ($query) {
                $query->where('created_at', '<=', date('Y-06-30'))
                    ->orWhereNull('created_at');
            })
            ->where('ch.is_active', 1);

            if ($conditions['founderCondition']) {
                $baseQuery;
            } elseif ($conditions['assistConferenceCoordinatorCondition']) {
                    $baseQuery->where('ch.conference', '=', $corConfId);
                } elseif ($conditions['regionalCoordinatorCondition']) {
                    $baseQuery->where('ch.region', '=', $corRegId);
            } else {
                $baseQuery->whereIn('ch.primary_coordinator_id', $inQryArr);
            }

            if (isset($_GET['check']) && $_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $baseQuery->where('ch.primary_coordinator_id', '=', $corId)
                    ->orderBy('st.state_short_name')
                    ->orderBy('ch.name');
            } else {
                $checkBoxStatus = '';
                $baseQuery->orderBy('st.state_short_name')
                    ->orderBy('ch.name');
            }

        $chapterList = $baseQuery->get();

        $chapterList = $chapterList->toArray();
        $countList = count($chapterList);

        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('eoyreports.eoyawards', $data);
    }

     /**
     * View the Award Details
     */
    public function showEOYAwardsView(Request $request, $id)
    {
        //$corDetails = User::find($request->user()->id)->Coordinators;
        $user = User::find($request->user()->id);

        $corDetails = $user->Coordinators;
        // Check if BoardDetails is not found for the user
        if (! $corDetails) {
            return to_route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $financial_report_array = FinancialReport::find($id);

        $chapterList = DB::table('chapters as ch')
            ->select('ch.id', 'ch.name', 'ch.state', 'ch.region')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->get();
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name')
            ->get();

        $data = ['chapterList' => $chapterList, 'regionList' => $regionList, 'financial_report_array' => $financial_report_array, 'stateArr' => $stateArr, 'countryArr' => $countryArr];

        return view('eoyreports.eoyawardsview')->with($data);
    }

    /**
     * Upate Awards (store)
     */
    public function updateEOYAwards5(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $award_1_nomination_type = $request->input('checkNominationType1', null);
        $award_2_nomination_type = $request->input('checkNominationType2', null);
        $award_3_nomination_type = $request->input('checkNominationType3', null);
        $award_4_nomination_type = $request->input('checkNominationType4', null);
        $award_5_nomination_type = $request->input('checkNominationType5', null);
        $award_1_outstanding_project_desc = $request->input('AwardDesc1', null);
        $award_2_outstanding_project_desc = $request->input('AwardDesc2', null);
        $award_3_outstanding_project_desc = $request->input('AwardDesc3', null);
        $award_4_outstanding_project_desc = $request->input('AwardDesc4', null);
        $award_5_outstanding_project_desc = $request->input('AwardDesc5', null);
        $check_award_1_approved = $request->input('checkAward1Approved', null);
        $check_award_2_approved = $request->input('checkAward2Approved', null);
        $check_award_3_approved = $request->input('checkAward3Approved', null);
        $check_award_4_approved = $request->input('checkAward4Approved', null);
        $check_award_5_approved = $request->input('checkAward5Approved', null);

        $report = FinancialReport::find($id);
        DB::beginTransaction();
        try {
            $report->award_1_nomination_type = $award_1_nomination_type;
            $report->award_2_nomination_type = $award_2_nomination_type;
            $report->award_3_nomination_type = $award_3_nomination_type;
            $report->award_4_nomination_type = $award_4_nomination_type;
            $report->award_5_nomination_type = $award_5_nomination_type;
            $report->award_1_outstanding_project_desc = $award_1_outstanding_project_desc;
            $report->award_2_outstanding_project_desc = $award_2_outstanding_project_desc;
            $report->award_3_outstanding_project_desc = $award_3_outstanding_project_desc;
            $report->award_4_outstanding_project_desc = $award_4_outstanding_project_desc;
            $report->award_5_outstanding_project_desc = $award_5_outstanding_project_desc;
            $report->check_award_1_approved = $check_award_1_approved;
            $report->check_award_2_approved = $check_award_2_approved;
            $report->check_award_3_approved = $check_award_3_approved;
            $report->check_award_4_approved = $check_award_4_approved;
            $report->check_award_5_approved = $check_award_5_approved;
            $report->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->to('/eoy/awards')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/eoy/awards')->with('success', 'Chapter Awards have been successfully updated');
    }


    /**
     * Activate Board Function
     */
    public function activateBoard($chapter_id, $lastUpdatedBy)
    {
        // Fetching New Board Info from Incoming Board Members
        $incomingBoardDetails = DB::table('incoming_board_member')
            ->select('*')
            ->where('chapter_id', '=', $chapter_id)
            ->orderBy('board_position_id')
            ->get();
        $countIncomingBoardDetails = count($incomingBoardDetails);

        if ($countIncomingBoardDetails > 0) {
            DB::beginTransaction();
            try {
                // Fetching Existing Board Members from Board Details
                $boardDetails = DB::table('boards')
                    ->select('*')
                    ->where('chapter_id', '=', $chapter_id)
                    ->get();
                $countBoardDetails = count($boardDetails);

                if ($countBoardDetails > 0) {
                    // Mark ALL existing board members as outgoing
                    foreach ($boardDetails as $record) {
                        DB::table('users')
                            ->where('id', $record->user_id)
                            ->update([
                                'user_type' => 'outgoing',
                                'updated_at' => now(),
                            ]);
                    }

                    // Delete all board members associated with the chapter from boards table
                    DB::table('boards')
                        ->where('chapter_id', $chapter_id)
                        ->delete();
                }

                // Create & Activate Details of Board members from Incoming Board Members
                foreach ($incomingBoardDetails as $incomingRecord) {
                    // Check if user already exists
                    $existingUser = DB::table('users')->where('email', $incomingRecord->email)->first();

                    if ($existingUser) {
                        // If the user exists, update all necessary fields, including is_active and user_type
                        DB::table('users')
                            ->where('id', $existingUser->id)
                            ->update([
                                'first_name' => $incomingRecord->first_name,
                                'last_name' => $incomingRecord->last_name,
                                'email' => $incomingRecord->email,
                                'is_active' => 1,
                                'user_type' => 'board',
                                'updated_at' => now(),
                            ]);

                        $userId = $existingUser->id;
                    } else {
                        // Insert new user
                        $userId = DB::table('users')->insertGetId([
                            'first_name' => $incomingRecord->first_name,
                            'last_name' => $incomingRecord->last_name,
                            'email' => $incomingRecord->email,
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1,
                            'updated_at' => now(),
                        ]);
                    }

                    // Prepare board details data
                    $boardId = [
                        'user_id' => $userId,
                        'first_name' => $incomingRecord->first_name,
                        'last_name' => $incomingRecord->last_name,
                        'email' => $incomingRecord->email,
                        'board_position_id' => $incomingRecord->board_position_id,
                        'chapter_id' => $chapter_id,
                        'street_address' => $incomingRecord->street_address,
                        'city' => $incomingRecord->city,
                        'state' => $incomingRecord->state,
                        'zip' => $incomingRecord->zip,
                        'country' => 'USA',
                        'phone' => $incomingRecord->phone,
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                        'is_active' => 1,
                    ];

                    // Upsert board details (update if the user and chapter already exist)
                    DB::table('boards')->upsert(
                        [$boardId], // The values to insert or update
                        ['user_id', 'chapter_id'], // The unique constraints for upsert
                        array_keys($boardId) // The columns to update if a conflict occurs
                    );

                }

                // Update Chapter after Board Active
                DB::update('UPDATE chapters SET new_board_active = ? WHERE id = ?', [1, $chapter_id]);

                // Delete all board members associated with the chapter from incoming_boards table
                DB::table('incoming_board_member')
                    ->where('chapter_id', $chapter_id)
                    ->delete();

                DB::commit();
                $status = 'success'; // Set status to success if everything goes well
            } catch (\Exception $e) {
                DB::rollback();
                // Log the exception or print it out for debugging
                Log::error('Error activating board: ' . $e->getMessage());
                $status = 'fail'; // Set status to fail if an exception occurs
            }
        }

        return $status;
    }

    public function viewEOYDetails(Request $request, $id): View
    {

        $user = User::find($request->user()->id);
        $userId = $user->id;

        // $corDetails = User::find($request->user()->id)->Coordinators;
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

        $financial_report_array = FinancialReport::find($id);

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id',
                'ct.name as countryname', 'st.state_short_name as statename', 'cf.conference_description as confname', 'rg.long_name as regname', 'mo.month_long_name as startmonth',
                'fr.check_current_990N_verified_IRS as irs_verified', 'fr.check_current_990N_notes as irs_notes',
                'fr.file_irs_path as file_irs_path')
            ->join('country as ct', 'ch.country', '=', 'ct.short_name')
            ->join('state as st', 'ch.state', '=', 'st.id')
            ->join('conference as cf', 'ch.conference', '=', 'cf.id')
            ->join('region as rg', 'ch.region', '=', 'rg.id')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->leftJoin('month as mo', 'ch.start_month_id', '=', 'mo.id')
            ->leftJoin('financial_report as fr', 'fr.chapter_id', '=', 'ch.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();

        $chConfId = $chapterList[0]->conference;
        $chRegId = $chapterList[0]->region;
        $chPCid = $chapterList[0]->primary_coordinator_id;

        // Load Active Status for Active/Zapped Visibility
        $chIsActive = $chapterList[0]->is_active;

        $reportReviewerList = DB::table('chapters as ch')
            ->select('cd.id as cid', 'cd.first_name as rfname', 'cd.last_name as rlname', 'cp.short_title as pos', 'pos2.short_title as sec_pos')
            ->leftJoin('financial_report as fr', 'fr.chapter_id', '=', 'ch.id')
            ->leftJoin('coordinators as cd', 'fr.reviewer_id', '=', 'cd.id')
            ->leftJoin('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
            ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
            ->where(function($query) use ($chRegId, $chConfId) {
                $query->where('cd.region_id', '=', $chRegId)
                    ->orWhere(function($subQuery) use ($chConfId) {
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

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'positionid' => $positionid, 'coordId' => $coordId, 'reviewComplete' => $reviewComplete,
            'chapterList' => $chapterList, 'reportReviewerList' => $reportReviewerList, 'corConfId' => $corConfId, 'chConfId' => $chConfId, 'chPCid' => $chPCid, 'financial_report_array' => $financial_report_array
            ];

        return view('eoyreports.view')->with($data);
}

public function updateEOYDetails(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $userId = $corDetails['user_id'];
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $chapter = Chapter::find($id);
        DB::beginTransaction();
        try {
            $chapter->new_board_submitted = (int) $request->has('new_board_submitted');
            $chapter->new_board_active = (int) $request->has('new_board_active');
            $chapter->financial_report_received = (int) $request->has('financial_report_received');
            $chapter->financial_report_complete = (int) $request->has('financial_report_complete');
            $chapter->report_extension = (int) $request->has('report_extension');
            $chapter->extension_notes = $request->input('extension_notes');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = now();
            $chapter->save();

            $report = FinancialReport::find($id);
            $report->reviewer_id = $request->input('ch_reportrev') ?? $userId;
            $report->check_current_990N_verified_IRS = (int) $request->has('irs_verified');
            $report->check_current_990N_notes = $request->input('irs_notes');

            if ($request->has('financial_report_received') != null) {
                $report->submitted = now();
                $report->reviewer_id = $report->reviewer_id ?? $userId; // Ensures reviewer_id is set to $userId if not already set
            }
            if ($request->has('financial_report_received') == null) {
                $report->submitted = null;
                // $report->reviewer_id = null; // Keep or remove depending on your requirements
            }
            if ($request->has('financial_report_complete') != null) {
                $report->review_complete = now();
            }
            if ($request->has('financial_report_complete') == null) {
                $report->review_complete = null;
            }

            $report->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            // Log the error
            Log::error($e);

            return to_route('eoyreports.view', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
    }

        return to_route('eoyreports.view', ['id' => $id])->with('success', 'EOY Information successfully updated.');
    }

    public function editEOYBoundaries(Request $request, $id): View
    {

        $user = User::find($request->user()->id);
        $userId = $user->id;

        // $corDetails = User::find($request->user()->id)->Coordinators;
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

        $financial_report_array = FinancialReport::find($id);

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id',
                'ct.name as countryname', 'st.state_short_name as statename', 'cf.conference_description as confname', 'rg.long_name as regname', 'mo.month_long_name as startmonth',
                'fr.check_current_990N_verified_IRS as irs_verified', 'fr.check_current_990N_notes as irs_notes', 'cd.first_name as rfname', 'cd.last_name as rlname',
                'fr.file_irs_path as file_irs_path')
            ->join('country as ct', 'ch.country', '=', 'ct.short_name')
            ->join('state as st', 'ch.state', '=', 'st.id')
            ->join('conference as cf', 'ch.conference', '=', 'cf.id')
            ->join('region as rg', 'ch.region', '=', 'rg.id')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->leftJoin('month as mo', 'ch.start_month_id', '=', 'mo.id')
            ->leftJoin('financial_report as fr', 'fr.chapter_id', '=', 'ch.id')
            ->leftJoin('coordinators as cd', 'fr.reviewer_id', '=', 'cd.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();

        $chConfId = $chapterList[0]->conference;
        $chRegId = $chapterList[0]->region;
        $chPCid = $chapterList[0]->primary_coordinator_id;

        // Load Active Status for Active/Zapped Visibility
        $chIsActive = $chapterList[0]->is_active;

        $reportReviewerList = DB::table('chapters as ch')
            ->select('cd.id as cid', 'cd.first_name as rfname', 'cd.last_name as rlname', 'cp.short_title as pos', 'pos2.short_title as sec_pos')
            ->leftJoin('financial_report as fr', 'fr.chapter_id', '=', 'ch.id')
            ->leftJoin('coordinators as cd', 'fr.reviewer_id', '=', 'cd.id')
            ->leftJoin('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
            ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
            ->where(function($query) use ($chRegId, $chConfId) {
                $query->where('cd.region_id', '=', $chRegId)
                    ->orWhere(function($subQuery) use ($chConfId) {
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



        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'positionid' => $positionid, 'coordId' => $coordId, 'reviewComplete' => $reviewComplete,
            'chapterList' => $chapterList, 'reportReviewerList' => $reportReviewerList, 'corConfId' => $corConfId, 'chConfId' => $chConfId, 'chPCid' => $chPCid, 'financial_report_array' => $financial_report_array
            ];

        return view('eoyreports.editboundaries')->with($data);
}

public function updateEOYBoundaries(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $userId = $corDetails['user_id'];
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $chapter = Chapter::find($id);
        DB::beginTransaction();
        try {
            $chapter->territory = $request->filled('ch_territory') ? $request->input('ch_territory') : $request->input('ch_old_territory');
            $chapter->boundary_issue_resolved = (int) $request->has('ch_resolved');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = now();
            $chapter->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            // Log the error
            Log::error($e);

            return to_route('eoyreports.editboundaries', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        }

        return to_route('eoyreports.editboundaries', ['id' => $id])->with('success', 'EOY Information successfully updated.');
    }

public function editEOYAwards(Request $request, $id): View
    {

        $user = User::find($request->user()->id);
        $userId = $user->id;

        // $corDetails = User::find($request->user()->id)->Coordinators;
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

        $financial_report_array = FinancialReport::find($id);

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id',
                'ct.name as countryname', 'st.state_short_name as statename', 'cf.conference_description as confname', 'rg.long_name as regname', 'mo.month_long_name as startmonth',
                'fr.check_current_990N_verified_IRS as irs_verified', 'fr.check_current_990N_notes as irs_notes', 'cd.first_name as rfname', 'cd.last_name as rlname',
                'fr.file_irs_path as file_irs_path')
            ->join('country as ct', 'ch.country', '=', 'ct.short_name')
            ->join('state as st', 'ch.state', '=', 'st.id')
            ->join('conference as cf', 'ch.conference', '=', 'cf.id')
            ->join('region as rg', 'ch.region', '=', 'rg.id')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->leftJoin('month as mo', 'ch.start_month_id', '=', 'mo.id')
            ->leftJoin('financial_report as fr', 'fr.chapter_id', '=', 'ch.id')
            ->leftJoin('coordinators as cd', 'fr.reviewer_id', '=', 'cd.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();

        $chConfId = $chapterList[0]->conference;
        $chRegId = $chapterList[0]->region;
        $chPCid = $chapterList[0]->primary_coordinator_id;

        // Load Active Status for Active/Zapped Visibility
        $chIsActive = $chapterList[0]->is_active;

        $reportReviewerList = DB::table('chapters as ch')
            ->select('cd.id as cid', 'cd.first_name as rfname', 'cd.last_name as rlname', 'cp.short_title as pos', 'pos2.short_title as sec_pos')
            ->leftJoin('financial_report as fr', 'fr.chapter_id', '=', 'ch.id')
            ->leftJoin('coordinators as cd', 'fr.reviewer_id', '=', 'cd.id')
            ->leftJoin('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
            ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
            ->where(function($query) use ($chRegId, $chConfId) {
                $query->where('cd.region_id', '=', $chRegId)
                    ->orWhere(function($subQuery) use ($chConfId) {
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



        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'positionid' => $positionid, 'coordId' => $coordId, 'reviewComplete' => $reviewComplete,
            'chapterList' => $chapterList, 'reportReviewerList' => $reportReviewerList, 'corConfId' => $corConfId, 'chConfId' => $chConfId, 'chPCid' => $chPCid, 'financial_report_array' => $financial_report_array
            ];

        return view('eoyreports.editawards')->with($data);
}

public function updateEOYAwards(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $userId = $corDetails['user_id'];
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $report = FinancialReport::find($id);
        DB::beginTransaction();
        try {
            $report->award_1_nomination_type = $request->input('checkNominationType1');
            $report->award_1_outstanding_project_desc = $request->input('AwardDesc1');
            $report->check_award_1_approved = (int) $request->has('checkAward1Approved');
            $report->award_2_nomination_type = $request->input('checkNominationType2');
            $report->award_2_outstanding_project_desc = $request->input('AwardDesc2');
            $report->check_award_2_approved = (int) $request->has('checkAward2Approved');
            $report->award_3_nomination_type = $request->input('checkNominationType3');
            $report->award_3_outstanding_project_desc = $request->input('AwardDesc3');
            $report->check_award_3_approved = (int) $request->has('checkAward3Approved');
            $report->award_4_nomination_type = $request->input('checkNominationType4');
            $report->award_4_outstanding_project_desc = $request->input('AwardDesc4');
            $report->check_award_4_approved = (int) $request->has('checkAward4Approved');
            $report->award_5_nomination_type = $request->input('checkNominationType5');
            $report->award_5_outstanding_project_desc = $request->input('AwardDesc5');
            $report->check_award_5_approved = (int) $request->has('checkAward5Approved');

            $report->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            // Log the error
            Log::error($e);

            return to_route('eoyreports.editawards', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        }

        return to_route('eoyreports.editawards', ['id' => $id])->with('success', 'EOY Information successfully updated.');
    }


}
