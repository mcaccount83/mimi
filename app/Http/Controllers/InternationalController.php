<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\FinancialReport;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class InternationalController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->middleware('auth')->except('logout');
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class);
        $this->userController = $userController;
    }

    /**
     * Display the International chapter list
     */
    public function showIntChapter(Request $request)
    {
        $user = User::find($request->user()->id);

        $corDetails = $user->Coordinators;
        if (! $corDetails) {
            return to_route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $intChapterList = DB::table('chapters as ch')
            ->select('ch.id', 'ch.name', 'ch.state', 'ch.ein', 'ch.primary_coordinator_id', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name',
                'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.phone as pre_phone', 'st.state_short_name as state',
                'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'cd.conference_id as cor_cid', 'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'ch.id')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->leftjoin('region as rg', 'ch.region', '=', 'rg.id')
            ->leftjoin('conference as cf', 'ch.conference', '=', 'cf.id')
            ->where('ch.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('ch.name')
            ->get();
        $countList = count($intChapterList);
        $data = ['countList' => $countList, 'intChapterList' => $intChapterList];

        return view('international.intchapter')->with($data);
    }

    /**
     * View the International chapter list
     */
    // public function showIntChapterView(Request $request, $id)
    // {
    //     $user = User::find($request->user()->id);
    //     if (! $user) {
    //         return to_route('home');
    //     }

    //     $corDetails = $user->Coordinators;
    //     if (! $corDetails) {
    //         return to_route('home');
    //     }

    //     $corConfId = $corDetails['conference_id'];
    //     $corId = $corDetails['id'];
    //     $positionid = $corDetails['position_id'];
    //     $financial_report_array = FinancialReport::find($id);
    //     if ($financial_report_array) {
    //         $reviewComplete = $financial_report_array['review_complete'];
    //     } else {
    //         $reviewComplete = null;
    //     }
    //     $chapterList = DB::table('chapters as ch')
    //         ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
    //         ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
    //         ->where('ch.is_active', '=', '1')
    //         ->where('ch.id', '=', $id)
    //         ->where('bd.board_position_id', '=', '1')
    //         ->get();
    //     $corConfId = $chapterList[0]->conference;
    //     $corId = $chapterList[0]->primary_coordinator_id;
    //     $AVPDetails = DB::table('boards as bd')
    //         ->select('bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id', 'bd.street_address as avp_addr', 'bd.city as avp_city', 'bd.zip as avp_zip', 'bd.phone as avp_phone', 'bd.state as avp_state', 'bd.user_id as user_id')
    //         ->where('bd.chapter_id', '=', $id)
    //         ->where('bd.board_position_id', '=', '2')
    //         ->get();
    //     if (count($AVPDetails) == 0) {
    //         $AVPDetails[0] = ['avp_fname' => '', 'avp_lname' => '', 'avp_email' => '', 'avp_addr' => '', 'avp_city' => '', 'avp_zip' => '', 'avp_phone' => '', 'avp_state' => '', 'user_id' => ''];
    //         $AVPDetails = json_decode(json_encode($AVPDetails));
    //     }

    //     $MVPDetails = DB::table('boards as bd')
    //         ->select('bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id', 'bd.street_address as mvp_addr', 'bd.city as mvp_city', 'bd.zip as mvp_zip', 'bd.phone as mvp_phone', 'bd.state as mvp_state', 'bd.user_id as user_id')
    //         ->where('bd.chapter_id', '=', $id)
    //         ->where('bd.board_position_id', '=', '3')
    //         ->get();
    //     if (count($MVPDetails) == 0) {
    //         $MVPDetails[0] = ['mvp_fname' => '', 'mvp_lname' => '', 'mvp_email' => '', 'mvp_addr' => '', 'mvp_city' => '', 'mvp_zip' => '', 'mvp_phone' => '', 'mvp_state' => '', 'user_id' => ''];
    //         $MVPDetails = json_decode(json_encode($MVPDetails));
    //     }

    //     $TRSDetails = DB::table('boards as bd')
    //         ->select('bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id', 'bd.street_address as trs_addr', 'bd.city as trs_city', 'bd.zip as trs_zip', 'bd.phone as trs_phone', 'bd.state as trs_state', 'bd.user_id as user_id')
    //         ->where('bd.chapter_id', '=', $id)
    //         ->where('bd.board_position_id', '=', '4')
    //         ->get();
    //     if (count($TRSDetails) == 0) {
    //         $TRSDetails[0] = ['trs_fname' => '', 'trs_lname' => '', 'trs_email' => '', 'trs_addr' => '', 'trs_city' => '', 'trs_zip' => '', 'trs_phone' => '', 'trs_state' => '', 'user_id' => ''];
    //         $TRSDetails = json_decode(json_encode($TRSDetails));
    //     }

    //     $SECDetails = DB::table('boards as bd')
    //         ->select('bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.board_position_id', 'bd.street_address as sec_addr', 'bd.city as sec_city', 'bd.zip as sec_zip', 'bd.phone as sec_phone', 'bd.state as sec_state', 'bd.user_id as user_id')
    //         ->where('bd.chapter_id', '=', $id)
    //         ->where('bd.board_position_id', '=', '5')
    //         ->get();
    //     if (count($SECDetails) == 0) {
    //         $SECDetails[0] = ['sec_fname' => '', 'sec_lname' => '', 'sec_email' => '', 'sec_addr' => '', 'sec_city' => '', 'sec_zip' => '', 'sec_phone' => '', 'sec_state' => '', 'user_id' => ''];
    //         $SECDetails = json_decode(json_encode($SECDetails));
    //     }

    //     $stateArr = DB::table('state')
    //         ->select('state.*')
    //         ->orderBy('id')
    //         ->get();
    //     $countryArr = DB::table('country')
    //         ->select('country.*')
    //         ->orderBy('id')
    //         ->get();
    //     $regionList = DB::table('region')
    //         ->select('id', 'long_name')
    //         ->where('conference_id', '=', $corConfId)
    //         ->orderBy('long_name')
    //         ->get();
    //     $confList = DB::table('conference')
    //         ->select('id', 'conference_name')
    //         ->where('id', '>=', 0)
    //         ->orderBy('conference_name')
    //         ->get();

    //     $primaryCoordinatorList = DB::table('coordinators as cd')
    //         ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos', 'pos2.short_title as sec_pos')
    //         ->join('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
    //         ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
    //         ->where('cd.conference_id', '=', $corConfId)
    //         ->where('cd.position_id', '<=', '7')
    //         ->where('cd.position_id', '>=', '1')
    //         ->where('cd.is_active', '=', '1')
    //         ->where('cd.is_active', '=', '1')
    //         ->orderBy('cd.first_name')
    //         ->get();

    //     $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
    //     $currentMonth = $chapterList[0]->start_month_id;

    //     $data = ['positionid' => $positionid, 'corId' => $corId, 'reviewComplete' => $reviewComplete, 'currentMonth' => $currentMonth, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'chapterList' => $chapterList, 'regionList' => $regionList, 'confList' => $confList, 'primaryCoordinatorList' => $primaryCoordinatorList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

    //     return view('international.intchapterview')->with($data);
    // }

    /**
     * Display the International Zapped chapter list
     */
    public function showIntZappedChapter(Request $request)
    {
        $user = User::find($request->user()->id);

        $corDetails = $user->Coordinators;
        if (! $corDetails) {
            return to_route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $chapterList = DB::table('chapters as ch')
            ->select('ch.id', 'ch.conference', 'ch.state', 'ch.name', 'ch.ein', 'ch.zap_date', 'ch.disband_reason', 'st.state_short_name as state',
                'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'ch.id')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->leftjoin('region as rg', 'ch.region', '=', 'rg.id')
            ->leftjoin('conference as cf', 'ch.conference', '=', 'cf.id')
            ->where('ch.is_active', '=', '0')
            ->where('bd.board_position_id', '=', '1')
            ->orderByDesc('ch.zap_date')
            ->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('international.intchapterzapped')->with($data);
    }

    /**
     * View the International Zapped chapter list
     */
    // public function showIntZappedChapterView(Request $request, $id)
    // {
    //     $user = User::find($request->user()->id);
    //     if (! $user) {
    //         return to_route('home');
    //     }

    //     $corDetails = $user->Coordinators;
    //     if (! $corDetails) {
    //         return to_route('home');
    //     }

    //     $corId = $corDetails['id'];
    //     $corConfId = $corDetails['conference_id'];
    //     $positionId = $corDetails['position_id'];
    //     $secPositionId = $corDetails['sec_position_id'];
    //     $financial_report_array = FinancialReport::find($id);
    //     //$reviewComplete = $financial_report_array['review_complete'];
    //     $chapterList = DB::table('chapters as ch')
    //         ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
    //         ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
    //         ->where('ch.is_active', '=', '0')
    //         ->where('ch.id', '=', $id)
    //         ->where('bd.board_position_id', '=', '1')
    //         ->get();
    //     $corConfId = $chapterList[0]->conference;
    //     $corId = $chapterList[0]->primary_coordinator_id;
    //     $AVPDetails = DB::table('boards as bd')
    //         ->select('bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id', 'bd.street_address as avp_addr', 'bd.city as avp_city', 'bd.zip as avp_zip', 'bd.phone as avp_phone', 'bd.state as avp_state', 'bd.user_id as user_id')
    //         ->where('bd.chapter_id', '=', $id)
    //         ->where('bd.board_position_id', '=', '2')
    //         ->get();
    //     if (count($AVPDetails) == 0) {
    //         $AVPDetails[0] = ['avp_fname' => '', 'avp_lname' => '', 'avp_email' => '', 'avp_addr' => '', 'avp_city' => '', 'avp_zip' => '', 'avp_phone' => '', 'avp_state' => '', 'user_id' => ''];
    //         $AVPDetails = json_decode(json_encode($AVPDetails));
    //     }

    //     $MVPDetails = DB::table('boards as bd')
    //         ->select('bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id', 'bd.street_address as mvp_addr', 'bd.city as mvp_city', 'bd.zip as mvp_zip', 'bd.phone as mvp_phone', 'bd.state as mvp_state', 'bd.user_id as user_id')
    //         ->where('bd.chapter_id', '=', $id)
    //         ->where('bd.board_position_id', '=', '3')
    //         ->get();
    //     if (count($MVPDetails) == 0) {
    //         $MVPDetails[0] = ['mvp_fname' => '', 'mvp_lname' => '', 'mvp_email' => '', 'mvp_addr' => '', 'mvp_city' => '', 'mvp_zip' => '', 'mvp_phone' => '', 'mvp_state' => '', 'user_id' => ''];
    //         $MVPDetails = json_decode(json_encode($MVPDetails));
    //     }

    //     $TRSDetails = DB::table('boards as bd')
    //         ->select('bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id', 'bd.street_address as trs_addr', 'bd.city as trs_city', 'bd.zip as trs_zip', 'bd.phone as trs_phone', 'bd.state as trs_state', 'bd.user_id as user_id')
    //         ->where('bd.chapter_id', '=', $id)
    //         ->where('bd.board_position_id', '=', '4')
    //         ->get();
    //     if (count($TRSDetails) == 0) {
    //         $TRSDetails[0] = ['trs_fname' => '', 'trs_lname' => '', 'trs_email' => '', 'trs_addr' => '', 'trs_city' => '', 'trs_zip' => '', 'trs_phone' => '', 'trs_state' => '', 'user_id' => ''];
    //         $TRSDetails = json_decode(json_encode($TRSDetails));
    //     }

    //     $SECDetails = DB::table('boards as bd')
    //         ->select('bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.board_position_id', 'bd.street_address as sec_addr', 'bd.city as sec_city', 'bd.zip as sec_zip', 'bd.phone as sec_phone', 'bd.state as sec_state', 'bd.user_id as user_id')
    //         ->where('bd.chapter_id', '=', $id)
    //         ->where('bd.board_position_id', '=', '5')
    //         ->get();
    //     if (count($SECDetails) == 0) {
    //         $SECDetails[0] = ['sec_fname' => '', 'sec_lname' => '', 'sec_email' => '', 'sec_addr' => '', 'sec_city' => '', 'sec_zip' => '', 'sec_phone' => '', 'sec_state' => '', 'user_id' => ''];
    //         $SECDetails = json_decode(json_encode($SECDetails));
    //     }

    //     $stateArr = DB::table('state')
    //         ->select('state.*')
    //         ->orderBy('id')
    //         ->get();
    //     $countryArr = DB::table('country')
    //         ->select('country.*')
    //         ->orderBy('id')
    //         ->get();
    //     $regionList = DB::table('region')
    //         ->select('id', 'long_name')
    //         ->where('conference_id', '=', $corConfId)
    //         ->orderBy('long_name')
    //         ->get();
    //     $confList = DB::table('conference')
    //         ->select('id', 'conference_name')
    //         ->where('id', '>=', 0)
    //         ->orderBy('conference_name')
    //         ->get();

    //     $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
    //     $currentMonth = $chapterList[0]->start_month_id;
    //     $data = ['corId' => $corId, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'chapterList' => $chapterList, 'regionList' => $regionList, 'confList' => $confList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth, 'currentMonth' => $currentMonth];

    //     return view('international.intchapterzappedview')->with($data);
    // }

    /**
     * International Coordinators List
     */
    public function showIntCoordinator(): View
    {
        //Get International Coordinator List
        $intCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cor_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.conference_id as cor_cid',
                'rg.short_name as reg', 'cf.short_name as conf', 'cp.long_title as position',
                DB::raw('(SELECT cp2.long_title FROM coordinator_position as cp2 WHERE cp2.id = cd.sec_position_id) as sec_pos'), // Subquery to get secondary position
            )
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->leftJoin('region as rg', 'rg.id', '=', 'cd.region_id')
            ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();
        $data = ['intCoordinatorList' => $intCoordinatorList];

        return view('international.intcoord')->with($data);
    }

    /**
     * International Coordinators Detail
     */
    // public function showIntCoordinatorView(Request $request, $id): View
    // {
    //     $corDetails = User::find($request->user()->id)->Coordinators;
    //     $corId = $corDetails['id'];
    //     $corConfId = $corDetails['conference_id'];
    //     $coordinatorDetails = DB::table('coordinators as cd')
    //         ->select('cd.*')
    //         ->where('cd.is_active', '=', '1')
    //         ->where('cd.id', '=', $id)
    //         ->get();
    //     $stateArr = DB::table('state')
    //         ->select('state.*')
    //         ->orderBy('id')
    //         ->get();
    //     $countryArr = DB::table('country')
    //         ->select('country.*')
    //         ->orderBy('id')
    //         ->get();
    //     $regionList = DB::table('region')
    //         ->select('id', 'long_name')
    //         ->orderBy('long_name')
    //         ->get();
    //     $confList = DB::table('conference')
    //         ->select('id', 'conference_name')
    //         ->orderBy('conference_name')
    //         ->get();
    //     $positionList = DB::table('coordinator_position')
    //         ->select('id', 'long_title')
    //         ->orderBy('long_title')
    //         ->get();

    //     $primaryCoordinatorList = DB::table('coordinators as cd')
    //         ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
    //         ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
    //         ->where('cd.is_active', '=', '1')
    //         ->orderBy('cd.first_name')
    //         ->get();
    //     $directReportTo = DB::table('coordinators as cd')
    //         ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
    //         ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
    //         ->where('cd.report_id', '=', $id)
    //         ->where('cd.is_active', '=', '1')
    //         ->get();

    //     $directChapterTo = DB::table('chapters as ch')
    //         ->select('ch.id as ch_id', 'ch.name as ch_name', 'st.state_short_name as st_name')
    //         ->join('state as st', 'ch.state', '=', 'st.id')
    //         ->where('ch.primary_coordinator_id', '=', $id)
    //         ->where('ch.is_active', '=', '1')
    //         ->get();
    //     $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG',
    //         '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
    //     $currentMonth = $coordinatorDetails[0]->birthday_month_id;

    //     $data = ['directChapterTo' => $directChapterTo, 'directReportTo' => $directReportTo, 'primaryCoordinatorList' => $primaryCoordinatorList,
    //         'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails,
    //         'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth, 'cor_id' => $corId];

    //     return view('international.intcoordview')->with($data);
    // }

    /**
     * International Retired Coordinator List
     */
    public function showIntCoordinatorRetired(): View
    {
        //Get International Coordinator List
        $intCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cor_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.conference_id as cor_cid', 'rg.short_name as reg', 'cf.short_name as conf', 'cp.long_title as position', 'cd.sec_position_id as sec_position_id', 'cd.zapped_date as zapdate', 'cd.reason_retired as reason')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->leftJoin('region as rg', 'rg.id', '=', 'cd.region_id')
            ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
            ->where('cd.is_active', '=', '0')
            ->orderByDesc('cd.zapped_date')
            ->get();

        $data = ['intCoordinatorList' => $intCoordinatorList];

        return view('international.intcoordretired')->with($data);
    }

    /**
     * International Retired Coordinator Details
     */
    // public function showIntCoordinatorRetiredView(Request $request, $id): View
    // {
    //     $corDetails = User::find($request->user()->id)->Coordinators;
    //     $corId = $corDetails['id'];
    //     $corConfId = $corDetails['conference_id'];
    //     $coordinatorDetails = DB::table('coordinators as cd')
    //         ->select('cd.*')
    //         ->where('cd.id', '=', $id)
    //         ->get();
    //     $stateArr = DB::table('state')
    //         ->select('state.*')
    //         ->orderBy('id')
    //         ->get();
    //     $countryArr = DB::table('country')
    //         ->select('country.*')
    //         ->orderBy('id')
    //         ->get();
    //     $regionList = DB::table('region')
    //         ->select('id', 'long_name')
    //         ->orderBy('long_name')
    //         ->get();
    //     $confList = DB::table('conference')
    //         ->select('id', 'conference_name')
    //         ->orderBy('conference_name')
    //         ->get();
    //     $positionList = DB::table('coordinator_position')
    //         ->select('id', 'long_title')
    //         ->orderBy('long_title')
    //         ->get();

    //     $primaryCoordinatorList = DB::table('coordinators as cd')
    //         ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
    //         ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
    //         ->where('cd.is_active', '=', '1')
    //         ->orderBy('cd.first_name')
    //         ->get();
    //     $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
    //     $currentMonth = $coordinatorDetails[0]->birthday_month_id;

    //     $data = ['primaryCoordinatorList' => $primaryCoordinatorList, 'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

    //     return view('international.intcoordretiredview')->with($data);
    // }

    /**
     * View the International Coordinator ToDo List
     */
    public function showIntCoordinatorToDo(Request $request): View
    {
        //Get Coordinator Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);

        //Get Coordinator List mapped with login coordinator
        $coordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cor_id', 'cd.layer_id as layer_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.report_id as report_id', 'cd.sec_position_id as sec_position_id', 'cp.long_title as position', 'rg.short_name as reg', 'cd.conference_id as cor_conf',
                'cd.todo_month as todo_month', 'cd.todo_send_rereg as todo_send_rereg', 'cd.todo_send_late as todo_send_late', 'cd.todo_record_rereg as todo_record_rereg', 'cd.todo_record_m2m as todo_record_m2m', 'cd.todo_export_reports as todo_export_reports', 'cd.dashboard_updated as dashboard_updated',
                'cf.short_name as conf')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
            ->where('cd.is_active', '=', '1')
            ->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('international.intcoordtodo')->with($data);

    }

    /**
     * View the International EIN Status
     */
    public function showIntEINstatus(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];

        // Load Reporting Tree
        $coordinatorData = $this->userController->loadReportingTree($corId);
        $inQryArr = $coordinatorData['inQryArr'];

        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone',
                'st.state_short_name as state', 'db.month_long_name as start_month', 'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->leftJoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->leftJoin('month as db', 'chapters.start_month_id', '=', 'db.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        $data = ['chapterList' => $chapterList, 'corId' => $corId];

        return view('international.inteinstatus')->with($data);
    }

    /**
     * show EIN Status Details
     */
    // public function showIntEINstatusView(Request $request, $id)
    // {
    //     //$corDetails = User::find($request->user()->id)->Coordinators;
    //     $user = User::find($request->user()->id);
    //     // Check if user is not found
    //     if (! $user) {
    //         return to_route('home');
    //     }

    //     $corDetails = $user->Coordinators;
    //     // Check if BoardDetails is not found for the user
    //     if (! $corDetails) {
    //         return to_route('home');
    //     }

    //     $corId = $corDetails['id'];
    //     $corConfId = $corDetails['conference_id'];
    //     $chapterList = DB::table('chapters as ch')
    //         ->select('ch.id', 'ch.name', 'ch.ein', 'ch.ein_notes',
    //             'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.conference_id as cor_confid', 'cd.email as cor_email', 'bd.email as bor_email', 'st.state_short_name as statename')
    //         ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
    //         ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'ch.id')
    //         ->leftJoin('state as st', 'ch.state', '=', 'st.id')
    //         ->where('ch.is_active', '=', '1')
    //         ->where('bd.board_position_id', '=', '1')
    //         ->where('ch.id', $id)
    //         ->get();
    //     $maxDateLimit = Carbon::now()->format('Y-m-d');
    //     $minDateLimit = Carbon::now()->subYear()->format('Y-m-d');
    //     // $minDateLimit = '';
    //     $data = ['chapterList' => $chapterList, 'maxDateLimit' => $maxDateLimit, 'minDateLimit' => $minDateLimit];

    //     return view('international.inteinstatusview')->with($data);
    // }

    /**
     * Update EIN/IRS Notes (store)
     */
    // public function updateIntEINstatus(Request $request, $id): RedirectResponse
    // {
    //     $corDetails = User::find($request->user()->id)->Coordinators;
    //     $corId = $corDetails['id'];
    //     $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

    //     $nextRenewalYear = $request->input('ch_nxt_renewalyear');

    //     //$nextRenewalYear = date('Y');
    //     $primaryCordEmail = $request->input('ch_pc_email');
    //     $boardPresEmail = $request->input('ch_pre_email');
    //     $chapter = Chapter::find($id);
    //     DB::beginTransaction();
    //     try {

    //         $chapter->ein_notes = $request->input('ch_einnotes');

    //         $chapter->save();

    //         DB::commit();
    //     } catch (\Exception $e) {
    //         // Rollback Transaction
    //         DB::rollback();
    //         // Log the error
    //         Log::error($e);

    //         return redirect()->to('/international/einstatus')->with('fail', 'Something went wrong, Please try again.');
    //     }

    //     return redirect()->to('/international/einstatus')->with('success', 'Your EIN/IRS Notes have been saved');
    // }

    /**
     * View the International M2M Doantions
     */
    public function showIntdonation(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
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
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->leftJoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        $data = ['chapterList' => $chapterList];

        return view('international.intdonation')->with($data);
    }
}
