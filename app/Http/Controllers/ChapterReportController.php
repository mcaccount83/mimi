<?php

namespace App\Http\Controllers;

use App\Mail\PaymentsM2MChapterThankYou;
use App\Mail\PaymentsSustainingChapterThankYou;
use App\Models\User;
use App\Models\Chapter;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ChapterReportController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
        {
        $this->middleware('auth')->except('logout');
        $this->userController = $userController;
    }

    /**
     * Chpater Status Report
     */
    public function showRptChapterStatus(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        $request->session()->put('corconfid', $corConfId);
        $request->session()->put('corregid', $corRegId);

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
            //Get Coordinator Reporting Tree
                $reportIdList = DB::table('coordinator_reporting_tree as crt')
                    ->select('crt.id')
                    ->where($sqlLayerId, '=', $corId)
                    ->get();

            $inQryStr = '';
            foreach ($reportIdList as $key => $val) {
                $inQryStr .= $val->id.',';
            }
            $inQryStr = rtrim($inQryStr, ',');
            $inQryArr = explode(',', $inQryStr);
        }

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone',
                'st.state_short_name as state', 'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->leftJoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->where('chapters.is_active', '=', '1')
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
            $baseQuery->where('chapters.status', '<>', '1')
                ->orderBy('st.state_short_name')
                ->orderBy('chapters.name');
        } else {
            $checkBoxStatus = '';
            $baseQuery->orderBy('st.state_short_name')
                ->orderBy('chapters.name');
        }

        $chapterList = $baseQuery->get();

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

        return view('chapreports.chaprptchapterstatus')->with($data);
    }

    /**
     * View the Chapter Status Details
     */
    public function showChapterStatusView(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone',
                'bd.state as bd_state')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
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
            ->where('conference_id', '=', '5')
            ->orderBy('long_name')
            ->get();

        $primaryCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            // ->where('cd.conference_id', '=', '5')
            ->where('cd.position_id', '<=', '7')
            ->where('cd.position_id', '>=', '1')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG',
            '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $data = ['chapterList' => $chapterList, 'regionList' => $regionList, 'primaryCoordinatorList' => $primaryCoordinatorList, 'stateArr' => $stateArr,
            'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('chapters.chapterview')->with($data);
    }

    /**
     * View the EIN Status
     */
    public function showRptEINstatus(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

       // Get the conditions
       $conditions = getPositionConditions($positionId, $secPositionId);

       if ($conditions['coordinatorCondition']) {
           //Get Coordinator Reporting Tree
               $reportIdList = DB::table('coordinator_reporting_tree as crt')
                   ->select('crt.id')
                   ->where($sqlLayerId, '=', $corId)
                   ->get();

           $inQryStr = '';
           foreach ($reportIdList as $key => $val) {
               $inQryStr .= $val->id.',';
           }
           $inQryStr = rtrim($inQryStr, ',');
           $inQryArr = explode(',', $inQryStr);
       }

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone',
                'st.state_short_name as state', 'db.month_long_name as start_month', 'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->leftJoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->leftJoin('db_month as db', 'chapters.start_month_id', '=', 'db.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name');

            if ($conditions['founderCondition']) {
                $baseQuery;
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
                 $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('chapters.region', '=', $corRegId);
        } else {
            $baseQuery->whereIn('chapters.primary_coordinator_id', $inQryArr);
        }

        $chapterList = $baseQuery->get();

        $data = ['chapterList' => $chapterList, 'corId' => $corId];

        return view('chapreports.chaprpteinstatus')->with($data);
    }

     /**
     * View EIN Status Details
     */
    public function showRptEINstatusView(Request $request, $id)
    {
        //$corDetails = User::find($request->user()->id)->Coordinators;
        $user = User::find($request->user()->id);
        // Check if user is not found
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->Coordinators;
        // Check if BoardDetails is not found for the user
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $chapterList = DB::table('chapters as ch')
            ->select('ch.id', 'ch.name', 'ch.ein', 'ch.ein_notes',
                'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.conference_id as cor_confid', 'cd.email as cor_email', 'bd.email as bor_email', 'st.state_short_name as statename')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'ch.id')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('ch.id', $id)
            ->get();
        $maxDateLimit = Carbon::now()->format('Y-m-d');
        $minDateLimit = Carbon::now()->subYear()->format('Y-m-d');
        // $minDateLimit = '';
        $data = ['chapterList' => $chapterList, 'maxDateLimit' => $maxDateLimit, 'minDateLimit' => $minDateLimit];

        return view('chapreports.chaprpteinstatusview')->with($data);
    }

    /**
     * Update EIN Status Notes (store)
     */
    public function updateRptEINstatus(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $nextRenewalYear = $request->input('ch_nxt_renewalyear');

        //$nextRenewalYear = date('Y');
        $primaryCordEmail = $request->input('ch_pc_email');
        $boardPresEmail = $request->input('ch_pre_email');
        $chapter = Chapter::find($id);
        DB::beginTransaction();
        try {

            $chapter->ein_notes = $request->input('ch_einnotes');

            $chapter->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->to('/chapterreports/einstatus')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/chapterreports/einstatus')->with('success', 'Your EIN/IRS Notes have been saved');
    }


    /**
     * New Chapter Report
     */
    public function showRptNewChapters(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);

       // Get the conditions
       $conditions = getPositionConditions($positionId, $secPositionId);

       if ($conditions['coordinatorCondition']) {
           //Get Coordinator Reporting Tree
               $reportIdList = DB::table('coordinator_reporting_tree as crt')
                   ->select('crt.id')
                   ->where($sqlLayerId, '=', $corId)
                   ->get();

           $inQryStr = '';
           foreach ($reportIdList as $key => $val) {
               $inQryStr .= $val->id.',';
           }
           $inQryStr = rtrim($inQryStr, ',');
           $inQryArr = explode(',', $inQryStr);
       }

        //Get Chapter List mapped with login coordinator
        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'chapters.id as ch_id', 'chapters.name as ch_name', 'db.month_short_name as month_name', 'db.month_long_name as start_month', 'start_year as year', 'cd.first_name as cor_f_name',
                'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone',
                'st.state_short_name as ch_state', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->leftJoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->leftJoin('db_month as db', 'chapters.start_month_id', '=', 'db.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->whereRaw('DATE_ADD(CONCAT(chapters.start_year, "-", chapters.start_month_id, "-01"), INTERVAL 1 YEAR) > CURDATE()');

            if ($conditions['founderCondition']) {
                $baseQuery;
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
                $baseQuery->where('chapters.conference', '=', $corConfId);
       } elseif ($conditions['regionalCoordinatorCondition']) {
           $baseQuery->where('chapters.region', '=', $corRegId);
       } else {
           $baseQuery->whereIn('chapters.primary_coordinator_id', $inQryArr);
       }

       $chapterList = $baseQuery->get();

        $data = ['chapterList' => $chapterList, 'corId' => $corId];

        return view('chapreports.chaprptnewchapters')->with($data);
    }

    /**
     * Large Chapter Report
     */
    public function showRptLargeChapters(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        $request->session()->put('corconfid', $corConfId);
        $request->session()->put('corregid', $corRegId);

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
            //Get Coordinator Reporting Tree
                $reportIdList = DB::table('coordinator_reporting_tree as crt')
                    ->select('crt.id')
                    ->where($sqlLayerId, '=', $corId)
                    ->get();

            $inQryStr = '';
            foreach ($reportIdList as $key => $val) {
                $inQryStr .= $val->id.',';
            }
            $inQryStr = rtrim($inQryStr, ',');
            $inQryArr = explode(',', $inQryStr);
        }

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->leftJoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.members_paid_for', '>=', '60');

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

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

        return view('chapreports.chaprptlargechapters')->with($data);
    }

    /**
     * Chapter Probation Report
     */
    public function showRptProbation(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        $request->session()->put('corconfid', $corConfId);
        $request->session()->put('corregid', $corRegId);

         // Get the conditions
         $conditions = getPositionConditions($positionId, $secPositionId);

         if ($conditions['coordinatorCondition']) {
             //Get Coordinator Reporting Tree
                 $reportIdList = DB::table('coordinator_reporting_tree as crt')
                     ->select('crt.id')
                     ->where($sqlLayerId, '=', $corId)
                     ->get();

             $inQryStr = '';
             foreach ($reportIdList as $key => $val) {
                 $inQryStr .= $val->id.',';
             }
             $inQryStr = rtrim($inQryStr, ',');
             $inQryArr = explode(',', $inQryStr);
         }

        $status = [4, 5, 6];

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone',
                'st.state_short_name as state', 'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->leftJoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->whereIn('chapters.status', $status);

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
            $baseQuery->orderByDesc('st.state_short_name')
                ->orderByDesc('chapters.name');
        }

        $chapterList = $baseQuery->get();

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

        return view('chapreports.chaprptprobation')->with($data);
    }

    /**
     * View Doantions List
     */
    public function showRptDonations(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);

         // Get the conditions
         $conditions = getPositionConditions($positionId, $secPositionId);

         if ($conditions['coordinatorCondition']) {
             //Get Coordinator Reporting Tree
                 $reportIdList = DB::table('coordinator_reporting_tree as crt')
                     ->select('crt.id')
                     ->where($sqlLayerId, '=', $corId)
                     ->get();

             $inQryStr = '';
             foreach ($reportIdList as $key => $val) {
                 $inQryStr .= $val->id.',';
             }
             $inQryStr = rtrim($inQryStr, ',');
             $inQryArr = explode(',', $inQryStr);
         }

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->leftJoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->where('chapters.is_active', '=', '1')
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

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

        return view('chapreports.chaprptdonations')->with($data);
    }

    /**
     * View Donations & Payments Details
     */
    public function showRptDonationsView(Request $request, $id)
    {
        //$corDetails = User::find($request->user()->id)->Coordinators;
        $user = User::find($request->user()->id);
        // Check if user is not found
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->Coordinators;
        // Check if BoardDetails is not found for the user
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $chapterList = DB::table('chapters as ch')
            ->select('ch.id', 'ch.state', 'ch.name', 'ch.sustaining_donation', 'ch.m2m_payment', 'ch.m2m_date', 'cd.conference_id as cor_confid', 'ch.sustaining_date',
                'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.conference_id as cor_confid', 'cd.email as cor_email', 'bd.email as bor_email', 'st.state_short_name as statename')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'ch.id')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('ch.id', $id)
            ->get();
        $maxDateLimit = Carbon::now()->format('Y-m-d');
        $minDateLimit = Carbon::now()->subYear()->format('Y-m-d');
        // $minDateLimit = '';
        $data = ['chapterList' => $chapterList, 'maxDateLimit' => $maxDateLimit, 'minDateLimit' => $minDateLimit];

        return view('chapreports.chaprptdonationsview')->with($data);
    }

    /**
     * Update Donations & Payments (store)
     */
    public function updateRptDonations(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $primaryCordEmail = $request->input('ch_pc_email');
        $boardPresEmail = $request->input('ch_pre_email');

        $chapter = Chapter::find($id);
        $chId = $chapter['id'];

        $emailData = $this->userController->loadEmailDetails($chId);
        $chapEmail = $emailData['chapEmail'];
        $emailListChap = $emailData['emailListChap'];
        $emailListCoord = $emailData['emailListCoord'];

        $to_email1 = $emailListChap;
        $to_email2 = $chapEmail;
        $to_email = array_merge((array)$to_email1, (array)$to_email2);
        $cc_email = $primaryCordEmail;

        DB::beginTransaction();
        try {
            $chapter->m2m_date = $request->input('ch_m2m_date');
            $chapter->m2m_payment = $request->input('ch_m2m_payment');
            $chapter->sustaining_date = $request->input('ch_sustaining_date');
            $chapter->sustaining_donation = $request->input('ch_sustaining_donation');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();
            if ($request->input('ch_thanks') == 'on') {
                $mailData = [
                    'chapterName' => $request->input('ch_name'),
                    'chapterState' => $request->input('ch_state'),
                    'chapterPreEmail' => $request->input('ch_pre_email'),
                    'chapterAmount' => $request->input('ch_m2m_payment'),
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
                    'chapterTotal' => $request->input('ch_sustaining_donation'),
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
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->to('/chapterreports/donations')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/chapterreports/donations')->with('success', 'Donation has been successfully saved');
    }

    /**
     * View the Social Media Report
     */
    public function showRptSocialMedia(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

         // Get the conditions
         $conditions = getPositionConditions($positionId, $secPositionId);

         if ($conditions['coordinatorCondition']) {
             //Get Coordinator Reporting Tree
                 $reportIdList = DB::table('coordinator_reporting_tree as crt')
                     ->select('crt.id')
                     ->where($sqlLayerId, '=', $corId)
                     ->get();

             $inQryStr = '';
             foreach ($reportIdList as $key => $val) {
                 $inQryStr .= $val->id.',';
             }
             $inQryStr = rtrim($inQryStr, ',');
             $inQryArr = explode(',', $inQryStr);
         }

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'db.month_long_name as start_month', 'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->leftJoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->leftJoin('db_month as db', 'chapters.start_month_id', '=', 'db.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderByDesc('st.state_short_name')
            ->orderByDesc('chapters.name');

            if ($conditions['founderCondition']) {
                $baseQuery;
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
                $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('chapters.region', '=', $corRegId);
        } else {
            $baseQuery->whereIn('chapters.primary_coordinator_id', $inQryArr);
        }

        $chapterList = $baseQuery->get();

        $data = ['chapterList' => $chapterList, 'corId' => $corId];

        return view('chapreports.chaprptsocialmedia')->with($data);
    }

    /**
     * View the Chapter Coordinators List
     */
    public function showRptChapterCoordinators(Request $request): View
    {
        try {
            //Get Coordinators Details
            $corDetails = User::find($request->user()->id)->Coordinators;
            $corId = $corDetails['id'];
            $corConfId = $corDetails['conference_id'];
            $corRegId = $corDetails['region_id'];
            $corlayerId = $corDetails['layer_id'];
            $sqlLayerId = 'crt.layer'.$corlayerId;
            $positionId = $corDetails['position_id'];
            $secPositionId = $corDetails['sec_position_id'];
            $request->session()->put('positionid', $positionId);
            $request->session()->put('secpositionid', $secPositionId);

            // Get the conditions
            $conditions = getPositionConditions($positionId, $secPositionId);

            if ($conditions['coordinatorCondition']) {
                //Get Coordinator Reporting Tree
                    $reportIdList = DB::table('coordinator_reporting_tree as crt')
                        ->select('crt.id')
                        ->where($sqlLayerId, '=', $corId)
                        ->get();

                $inQryStr = '';
                foreach ($reportIdList as $key => $val) {
                    $inQryStr .= $val->id.',';
                }
                $inQryStr = rtrim($inQryStr, ',');
                $inQryArr = explode(',', $inQryStr);
            }

            $baseQuery = DB::table('chapters')
                ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                    'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'rg.short_name as reg', 'cf.short_name as conf')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
                ->leftJoin('region as rg', 'chapters.region', '=', 'rg.id')
                ->where('chapters.is_active', '=', '1')
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

            $countList = count($chapterList);
            $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

            return view('chapreports.chaprptcoordinators')->with($data);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}
