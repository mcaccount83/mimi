<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Coordinator;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('preventBackHistory');
        $this->middleware('auth')->except('logout');
    }

    public function showChapterStatus(Request $request): View
    {
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

        if ($corId == 25 || $positionId == 25) {
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

        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();
        //->paginate(30);
        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $chapterList = DB::table('chapters')
                    ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
                    ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('chapters.status', '<>', '1')
                    ->where('bd.board_position_id', '=', '1')
                    ->whereIn('chapters.primary_coordinator_id', $inQryArr)
                    ->orderBy('st.state_short_name')
                    ->orderBy('chapters.name')
                    ->get();
            }

        } else {
            $checkBoxStatus = '';
        }

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

        return view('reports.chapterstatus')->with($data);
    }

    /**
     * View the Downloads List
     */
    public function showDownloads(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

        if ($corId == 25 || $positionId == 25) {
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

        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'db.month_long_name as start_month')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('db_month as db', 'chapters.start_month_id', '=', 'db.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        $data = ['chapterList' => $chapterList, 'corId' => $corId, 'positionId' => $positionId, 'secPositionId' => $secPositionId];

        return view('reports.downloads')->with($data);
    }

    /**
     * View the EIN Status
     */
    public function showEINstatus(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

        if ($corId == 25 || $positionId == 25) {
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

        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'db.month_long_name as start_month')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('db_month as db', 'chapters.start_month_id', '=', 'db.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        $data = ['chapterList' => $chapterList, 'corId' => $corId];

        return view('reports.einstatus')->with($data);
    }

    /**
     * View the International EIN Status
     */
    public function intEINstatus(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

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

        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'db.month_long_name as start_month')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('db_month as db', 'chapters.start_month_id', '=', 'db.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        $data = ['chapterList' => $chapterList, 'corId' => $corId];

        return view('reports.inteinstatus')->with($data);
    }

    /**
     * View the Zapped chapter list
     */
    public function showChapterStatusView(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state')
            ->leftJoin('board_details as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
                            //->orderBy('bd.board_position_id','ASC')
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

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', '=', '5')
            ->where('cd.position_id', '<=', '6')
            ->where('cd.position_id', '>=', '1')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $data = ['chapterList' => $chapterList, 'regionList' => $regionList, 'primaryCoordinatorList' => $primaryCoordinatorList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('chapters.chapterview')->with($data);
    }

    public function showChapterNew(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $regionId = $corDetails['region_id'];
        if ($positionId == 6 || $positionId == 7 || $positionId == 10 || $positionId == 10 || ($corId == 158 && $positionId == 5) || ($corId == 25 && $secPositionId == 25) || $positionId == 25) {
            $full_list = true;
        } else {
            $full_list = false;
        }

        $date_clause = '';
        $last_year = date('Y') - 1;
        $month_last_year = date('m');

        //see if dates stretch into next year
        if (date('m') < 12) {
            $date_clause = 'WHERE ((chapters.start_year='.$last_year.' AND chapters.start_month_id>='.$month_last_year.')
                        OR (chapters.start_year='.date('Y').'))';
        } else {
            $date_clause = 'WHERE (chapters.start_year='.date('Y').' AND chapters.start_month_id<='.date('m').')';
        }
        if ($positionId != 7) {
            $conference_clause = 'AND chapters.conference='.$corConfId;
        } else {
            $conference_clause = '';
        }
        // AND chapters.conference = $corConfId

        // Create a new query that will get a list of all the chapters in this conference
        if ($full_list) {
            $chapterList = DB::select(DB::raw('SELECT chapters.id as ch_id, state.state_short_name as ch_state, chapters.name as ch_name, chapters.start_month_id as month, chapters.ein_letter_path as ein_letter_path, db_month.month_short_name as month_name, chapters.start_year as year, coordinator.first_name as cor_fname, coordinator.last_name as cor_lname
                        FROM chapters
                        INNER JOIN state
                        ON chapters.state=state.id
                        INNER JOIN coordinator_details as coordinator
                        ON chapters.primary_coordinator_id = coordinator.coordinator_id
                        INNER JOIN db_month
                        ON chapters.start_month_id=db_month.id '.
            $date_clause.' '.
            $conference_clause.
            " AND chapters.is_active ='1'
                        ORDER BY chapters.state, chapters.name ASC"));
        } else {
            // AND chapters.conference = $corConfId

            $chapterList = DB::select(DB::raw('SELECT chapters.id as ch_id, state.state_short_name as ch_state, chapters.name as ch_name, chapters.start_month_id as month, chapters.ein_letter_path as ein_letter_path, db_month.month_short_name as month_name, chapters.start_year as year, coordinator.first_name as cor_fname, coordinator.last_name as cor_lname
                        FROM chapters
                        INNER JOIN state
                        ON chapters.state=state.id
                        INNER JOIN coordinator_details as coordinator
                        ON chapters.primary_coordinator_id = coordinator.coordinator_id
                        INNER JOIN db_month
                        ON chapters.start_month_id=db_month.id '.
            $date_clause.' '.
            $conference_clause.
            " AND chapters.region = $regionId
                        AND chapters.is_active ='1'
                        ORDER BY chapters.state, chapters.name ASC"));
        }
        //  echo "<pre>"; print_r($chapterList);
        //Get Coordinator Reporting Tree
        $data = ['chapterList' => $chapterList, 'corId' => $corId];

        return view('reports.chapternew')->with($data);
    }

    public function showChapterLarge(Request $request): View
    {
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

        //Get Coordinator Reporting Tree
        if ($corId == 25 || $positionId == 25) {
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
                ->where('crt.layer1', '=', '6')
                ->get();
        } else {
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

        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();
        //->paginate(30);
        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $chapterList = DB::table('chapters')
                    ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
                    ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '1')
                    ->where('chapters.primary_coordinator_id', '=', $corId)
                    ->orderBy('st.state_short_name')
                    ->orderBy('chapters.name')
                    ->get();
            }

        } else {
            $checkBoxStatus = '';
        }

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

        return view('reports.chapterlarge')->with($data);
    }

    public function showChapterProbation(Request $request): View
    {
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

        //Get Coordinator Reporting Tree
        if ($corId == 25 || $positionId == 25) {
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
                ->where('crt.layer1', '=', '6')
                ->get();
        } else {
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
        $status = [4, 5, 6];
        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
                        // ->where('chapters.status', '=', '5')
            ->where('bd.board_position_id', '=', '1')
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->whereIn('chapters.status', $status)
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();
        //->paginate(30);
        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $chapterList = DB::table('chapters')
                    ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
                    ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('chapters.status', '=', '5')
                    ->where('bd.board_position_id', '=', '1')
                    ->where('chapters.primary_coordinator_id', '=', $corId)
                    ->orderBy('st.state_short_name')
                    ->orderBy('chapters.name')
                    ->get();
            }

        } else {
            $checkBoxStatus = '';
        }

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

        return view('reports.chapterprobation')->with($data);
    }

    /**
     * View the Social Media Report
     */
    public function showSocialMedia(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

        if ($corId == 25 || $positionId == 25) {
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

        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'db.month_long_name as start_month')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('db_month as db', 'chapters.start_month_id', '=', 'db.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        $data = ['chapterList' => $chapterList, 'corId' => $corId];

        return view('reports.socialmedia')->with($data);
    }

    /**
     * View the M2M Doantions
     */
    public function showM2Mdonation(Request $request): View
    {
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

        if ($corId == 25 || $positionId == 25) {
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

        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();
        //->paginate(30);
        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $chapterList = DB::table('chapters')
                    ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
                    ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('chapters.status', '<>', '1')
                    ->where('bd.board_position_id', '=', '1')
                    ->whereIn('chapters.primary_coordinator_id', $inQryArr)
                    ->orderBy('st.state_short_name')
                    ->orderBy('chapters.name')
                    ->get();
            }

        } else {
            $checkBoxStatus = '';
        }

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

        return view('reports.m2mdonation')->with($data);
    }

    /**
     * View the International M2M Doantions
     */
    public function intM2Mdonation(Request $request): View
    {
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

        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();
        //->paginate(30);
        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $chapterList = DB::table('chapters')
                    ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
                    ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('chapters.status', '<>', '1')
                    ->where('bd.board_position_id', '=', '1')
                    ->orderBy('st.state_short_name')
                    ->orderBy('chapters.name')
                    ->get();
            }

        } else {
            $checkBoxStatus = '';
        }

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

        return view('reports.intm2mdonation')->with($data);
    }

    /**
     * View the Chapter Coordinators List
     */
    public function showChapterCoordinators(Request $request): View
    {
        try {
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

            //Get Coordinator Reporting Tree
            if ($corId == 25) {
                $reportIdList = DB::table('coordinator_reporting_tree as crt')
                    ->select('crt.id')
                    ->where('crt.layer1', '=', '6')
                    ->get();
            } else {
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

            //Get Chapter List mapped with login coordinator
            $chapterList = DB::table('chapters')
                ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
                ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->whereIn('chapters.primary_coordinator_id', $inQryArr)
                ->orderBy('st.state_short_name')
                ->orderBy('chapters.name')
                ->get();
            //->paginate(30);

            if (isset($_GET['check'])) {
                if ($_GET['check'] == 'yes') {
                    $checkBoxStatus = 'checked';
                    $chapterList = DB::table('chapters')
                        ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
                        ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                        ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                        ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                        ->where('chapters.is_active', '=', '1')
                        ->where('bd.board_position_id', '=', '1')
                        ->where('chapters.primary_coordinator_id', '=', $corId)
                        ->orderBy('st.state_short_name')
                        ->orderBy('chapters.name')
                        ->get();
                }

            } else {
                $checkBoxStatus = '';
            }

            $countList = count($chapterList);
            $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

            return view('reports.chaptercoordinators')->with($data);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * View the Volunteer Utilization list
     */
    public function showChapterVolunteer(Request $request): View
    {
        //Get Coordinator Details

        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        //Get Coordinator Reporting Tree
        if ($corId == 25 || $positionId == 25) {
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
                ->where('crt.layer1', '=', '6')
                ->get();

        } else {
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
                ->where($sqlLayerId, '=', $corId)
                ->get();
        }
        //var_dump($corConfId,$corId);
        $inQryStr = '';
        foreach ($reportIdList as $key => $val) {
            $inQryStr .= $val->id.',';
        }
        $inQryStr = rtrim($inQryStr, ',');
        $inQryArr = explode(',', $inQryStr);

        //Get Coordinator List mapped with login coordinator
        $coordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cor_id', 'cd.layer_id as layer_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.report_id as report_id', 'cd.sec_position_id as sec_position_id', 'cp.long_title as position', 'rg.short_name as reg', 'cd.conference_id as cor_conf')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
                            //  ->join('coordinator_position as cp', 'cp.id', '=', 'cd.sec_position_id')
            ->where('cd.is_active', '=', '1')
                            //->whereIn('cd.report_id', $inQryArr)
            ->whereIn('cd.coordinator_id', $inQryArr)
                            //  ->orderBy('cd.first_name')
            ->orderByRaw("FIELD(rg.short_name , 'None') DESC")
            ->orderByRaw('rg.short_name', 'ASC')
                            // ->orderBy('rg.short_name','=','None','DESC')
            ->orderByDesc('cp.id')
            ->get();
        $data = ['coordinatorList' => $coordinatorList];
        // echo '<pre>';print_r($inQryArr);
        return view('reports.chaptervolunteer')->with($data);
    }

    /**
     * View the Coordinator ToDo List
     */
    public function showCoordinatorToDo(Request $request): View
    {
        //Get Coordinator Details

        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        //Get Coordinator Reporting Tree
        if ($corId == 25 || $positionId == 25) {
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
                ->where('crt.layer1', '=', '6')
                ->get();

        } else {
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

        //Get Coordinator List mapped with login coordinator
        $coordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cor_id', 'cd.layer_id as layer_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.report_id as report_id', 'cd.sec_position_id as sec_position_id', 'cp.long_title as position', 'rg.short_name as reg', 'cd.conference_id as cor_conf',
                'cd.todo_month as todo_month', 'cd.todo_check_chapters as todo_check_chapters', 'cd.todo_election_faq as todo_election_faq')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->where('cd.is_active', '=', '1')
            ->whereIn('cd.coordinator_id', $inQryArr)
            ->orderByRaw("FIELD(rg.short_name , 'None') DESC")
            ->orderByRaw('rg.short_name', 'ASC')
            ->orderByDesc('cp.id')
            ->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('reports.coordinatortodo')->with($data);

    }

    /**
     * View the International Coordinator ToDo List
     */
    public function showIntCoordinatorToDo(Request $request): View
    {
        //Get Coordinator Details

        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        //Get Coordinator Reporting Tree
        if ($corId == 25 || $positionId == 25) {
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
                ->where('crt.layer1', '=', '6')
                ->get();

        } else {
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

        //Get Coordinator List mapped with login coordinator
        $coordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cor_id', 'cd.layer_id as layer_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.report_id as report_id', 'cd.sec_position_id as sec_position_id', 'cp.long_title as position', 'rg.short_name as reg', 'cd.conference_id as cor_conf',
                'cd.todo_month as todo_month', 'cd.todo_send_rereg as todo_send_rereg', 'cd.todo_send_late as todo_send_late', 'cd.todo_record_rereg as todo_record_rereg', 'cd.todo_record_m2m as todo_record_m2m', 'cd.todo_export_reports as todo_export_reports')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->where('cd.is_active', '=', '1')
            ->whereIn('cd.position_id', ['6', '25'])
            ->whereIn('cd.coordinator_id', $inQryArr)
            ->orderByRaw("FIELD(rg.short_name , 'None') DESC")
            ->orderByRaw('rg.short_name', 'ASC')
            ->orderByDesc('cp.id')
            ->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('reports.intcoordinatortodo')->with($data);

    }

    public function showBoardlist(Request $request): View
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
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

        //Get Chapter List mapped with login coordinator

        $activeChapterList = DB::table('chapters')
            ->select('chapters.*', 'chapters.conference as conf', 'chapters.email as chapter_email', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city', 'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];
            foreach ($activeChapterList as $list) {
                if ($list->status == 1) {
                    $list->status_value = 'Operating OK';
                }
                if ($list->status == 4) {
                    $list->status_value = 'On Hold Do not Refer';
                }
                if ($list->status == 5) {
                    $list->status_value = 'Probation';
                }
                if ($list->status == 6) {
                    $list->status_value = 'Probation Do Not Refer';
                }
                $chapterId = $list->id;
                //For AVP Details
                $avpDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.phone as avp_phone', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '2')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($avpDeatils) > 0) {
                    $list->avp_fname = $avpDeatils[0]->avp_fname;
                    $list->avp_lname = $avpDeatils[0]->avp_lname;
                    $list->avp_email = $avpDeatils[0]->avp_email;
                    $list->avp_phone = $avpDeatils[0]->avp_phone;
                } else {
                    $list->avp_fname = '';
                    $list->avp_lname = '';
                    $list->avp_email = '';
                    $list->avp_phone = '';
                }
                //For MVP Details
                $mvpDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.phone as mvp_phone', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '3')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($mvpDeatils) > 0) {
                    $list->mvp_fname = $mvpDeatils[0]->mvp_fname;
                    $list->mvp_lname = $mvpDeatils[0]->mvp_lname;
                    $list->mvp_email = $mvpDeatils[0]->mvp_email;
                    $list->mvp_phone = $mvpDeatils[0]->mvp_phone;
                } else {
                    $list->mvp_fname = '';
                    $list->mvp_lname = '';
                    $list->mvp_email = '';
                    $list->mvp_phone = '';
                }
                //For TREASURER Details
                $trsDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.phone as trs_phone', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '4')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($trsDeatils) > 0) {
                    $list->trs_fname = $trsDeatils[0]->trs_fname;
                    $list->trs_lname = $trsDeatils[0]->trs_lname;
                    $list->trs_email = $trsDeatils[0]->trs_email;
                    $list->trs_phone = $trsDeatils[0]->trs_phone;
                } else {
                    $list->trs_fname = '';
                    $list->trs_lname = '';
                    $list->trs_email = '';
                    $list->trs_phone = '';
                }
                //For SECRETARY Details
                $secDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.phone as sec_phone', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '5')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($secDeatils) > 0) {
                    $list->sec_fname = $secDeatils[0]->sec_fname;
                    $list->sec_lname = $secDeatils[0]->sec_lname;
                    $list->sec_email = $secDeatils[0]->sec_email;
                    $list->sec_phone = $secDeatils[0]->sec_phone;
                } else {
                    $list->sec_fname = '';
                    $list->sec_lname = '';
                    $list->sec_email = '';
                    $list->sec_phone = '';
                }

                $exportChapterList[] = $list;
            }

            $countList = count($activeChapterList);
            $data = ['countList' => $countList, 'activeChapterList' => $activeChapterList, 'avpDeatils' => $avpDeatils, 'mvpDeatils' => $mvpDeatils, 'secDeatils' => $secDeatils, 'trsDeatils' => $trsDeatils];

            return view('reports.boardlist')->with($data);
        }
    }

    /**
     * View the Volunteer Appreciation list
     */
    public function showAppreciation(Request $request): View
    {
        //Get Coordinator Details

        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        //Get Coordinator Reporting Tree
        if ($corId == 25 || $positionId == 25) {
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
                ->where('crt.layer1', '=', '6')
                ->get();

        } else {
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
                ->where($sqlLayerId, '=', $corId)
                ->get();
        }
        //var_dump($corConfId,$corId);
        $inQryStr = '';
        foreach ($reportIdList as $key => $val) {
            $inQryStr .= $val->id.',';
        }
        $inQryStr = rtrim($inQryStr, ',');
        $inQryArr = explode(',', $inQryStr);

        //Get Coordinator List mapped with login coordinator
        $coordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cor_id', 'cd.layer_id as layer_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.report_id as report_id', 'cd.sec_position_id as sec_position_id', 'cp.long_title as position', 'rg.short_name as reg', 'cd.conference_id as cor_conf', 'cd.recognition_year0 as yr_0', 'cd.recognition_year1 as yr_1', 'cd.recognition_year2 as yr_2', 'cd.recognition_year3 as yr_3', 'cd.recognition_year4 as yr_4', 'cd.recognition_year5 as yr_5', 'cd.recognition_year6 as yr_6', 'cd.recognition_year7 as yr_7', 'cd.recognition_year8 as yr_8', 'cd.recognition_year9 as yr_9', 'cd.recognition_toptier as toptier', 'cd.coordinator_start_date as start_date', 'cd.recognition_necklace as necklace')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
                            //  ->join('coordinator_position as cp', 'cp.id', '=', 'cd.sec_position_id')
            ->where('cd.is_active', '=', '1')
                            //->whereIn('cd.report_id', $inQryArr)
            ->whereIn('cd.coordinator_id', $inQryArr)
                            //  ->orderBy('cd.first_name','ASC')
                            //->orderByRaw("FIELD(rg.short_name , 'None') DESC")
            ->orderByRaw('cd.coordinator_start_date', 'ASC')
                            // ->orderBy('rg.short_name','=','None','DESC')
                            //->orderBy('cp.id','DESC')
            ->get();
        $data = ['coordinatorList' => $coordinatorList];
        // echo '<pre>';print_r($inQryArr);
        return view('reports.appreciation')->with($data);
    }

    /**
     * View the Volunteer Birthday list
     */
    public function showBirthday(Request $request): View
    {
        //Get Coordinator Details

        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        //Get Coordinator Reporting Tree
        if ($corId == 25 || $positionId == 25) {
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
                ->where('crt.layer1', '=', '6')
                ->get();

        } else {
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
                ->where($sqlLayerId, '=', $corId)
                ->get();
        }
        //var_dump($corConfId,$corId);
        $inQryStr = '';
        foreach ($reportIdList as $key => $val) {
            $inQryStr .= $val->id.',';
        }
        $inQryStr = rtrim($inQryStr, ',');
        $inQryArr = explode(',', $inQryStr);

        //Get Coordinator List mapped with login coordinator
        $coordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cor_id', 'cd.layer_id as layer_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.report_id as report_id', 'cd.sec_position_id as sec_position_id', 'cd.card_sent as card_sent', 'cp.long_title as position', 'rg.short_name as reg', 'cd.conference_id as cor_conf', 'cd.birthday_month_id as b_month', 'cd.birthday_day as b_day', 'db.month_long_name as month')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->join('db_month as db', 'cd.birthday_month_id', '=', 'db.id')
            ->where('cd.is_active', '=', '1')
            ->whereIn('cd.coordinator_id', $inQryArr)
            ->orderBy('cd.birthday_month_id')
            ->orderBy('cd.birthday_day')
            ->get();
        $data = ['coordinatorList' => $coordinatorList];

        return view('reports.birthday')->with($data);
    }

    //public function resources()
    //   {
    //         //Get Coordinator Details
    //       $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
    //     $corId = $corDetails['coordinator_id'];
    //   $corConfId = $corDetails['conference_id'];
    //            $corlayerId = $corDetails['layer_id'];
    //          $sqlLayerId = 'crt.layer'.$corlayerId;
    //        $positionId = $corDetails['position_id'];
    //      $secPositionId = $corDetails['sec_position_id'];
    //            Session::put('positionid', $positionId);
    //          Session::put('secpositionid', $secPositionId);
    //        return view('reports.resources');
    //  }

    /**
     * View the Reporting Tree
     */
    public function showReportingTree(Request $request): View
    {
        $coordinator_array = [];
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corConfId = $corDetails['conference_id'];
        $positionId = $corDetails['position_id'];
        $request->session()->put('positionid', $positionId);
        $cord_pos_id = $request->session()->get('positionid');
        if ($positionId != 7) {
            $conference_clause = 'AND coordinator_details.conference_id='.$corConfId;
        } else {
            $conference_clause = '';
        }
        $resultOne = DB::select(DB::raw('SELECT coordinator_details.coordinator_id AS id, coordinator_details.first_name, coordinator_details.last_name, pos1.short_title AS position_title, pos2.short_title AS sec_position_title, coordinator_details.layer_id, coordinator_details.report_id, coordinator_details.report_id AS tree_id, region.short_name AS region
                    FROM coordinator_details
                    INNER JOIN coordinator_position pos1 ON pos1.id=coordinator_details.position_id
                    LEFT JOIN coordinator_position pos2 ON pos2.id=coordinator_details.sec_position_id
                    INNER JOIN region ON coordinator_details.region_id = region.id
                    WHERE coordinator_details.on_leave = 0 '.
            $conference_clause.
            ' AND coordinator_details.is_active = 1
                    ORDER BY coordinator_details.region_id, coordinator_details.position_id DESC'));
        foreach ($resultOne as $key => $value) {
            $resultOne[$key]->chapter_list = '';
            $resultTwo = DB::select(DB::raw("SELECT chapters.name, state.state_short_name FROM chapters INNER JOIN state ON chapters.state=state.id WHERE chapters.primary_coordinator_id = '".$resultOne[$key]->id."' ORDER BY state.state_short_name, chapters.name"));
            foreach ($resultTwo as $key1 => $value1) {
                $resultOne[$key]->chapter_list = $resultTwo[$key1]->state_short_name.' - '.$resultTwo[$key1]->name.'<br>';
            }
            $resultOne[$key]->indirect_count = 0;
            $resultOne[$key]->total_count = 0;
        }
        foreach ($resultOne as $key => $value) {
            $coordinator_array[$key] = (array) $value;
        }

        // echo "<pre>"; print_r(compact('coordinator_array')); exit();
        return view('reports.reportingtree', compact('coordinator_array', 'cord_pos_id'));
    }

    public function getCCMail($pcid)
    {
        $reportingList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('id', '=', $pcid)
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
        $down_line_email = [];
        foreach ($filterReportingList as $key => $val) {
            //if($corId != $val && $val >1){
            if ($val > 1) {
                $corList = DB::table('coordinator_details as cd')
                    ->select('cd.email as cord_email')
                    ->where('cd.coordinator_id', '=', $val)
                    ->where('cd.is_active', '=', 1)
                    ->get();
                if (count($corList) > 0) {
                    if ($down_line_email == '') {
                        $down_line_email[] = $corList[0]->cord_email;
                    } else {
                        $down_line_email[] = $corList[0]->cord_email;
                    }
                }
            }
        }

        return $down_line_email;
    }

    /**
     * View the EOY Status list
     */
    public function showEOYStatus(Request $request): View
    {
        $user = $request->user();
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;
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

        $year = date('Y');

        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'rg.short_name as region', 'st.state_short_name as state', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('created_at', '<=', date('Y-06-30'))
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();
        //->paginate(30);

        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $chapterList = DB::table('chapters')
                    ->select('chapters.*', 'st.state_short_name as state', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname')
                    ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '1')
                    ->where('created_at', '<=', date('Y-06-30'))
                    ->where('chapters.primary_coordinator_id', '=', $corId)
                    ->orderBy('st.state_short_name')
                    ->orderBy('chapters.name')
                    ->get();

            }
        } else {
            $checkBoxStatus = '';
        }
        $row_count = count($chapterList);

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];
        // echo '<pre>';print_r($data);die;
        return view('reports.eoystatus')->with($data);
    }

    /**
     * View the Financial Reports List
     */
    public function showReportToReview(Request $request): View
    {
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

        $year = date('Y');

        $chapterList = DB::select(DB::raw("SELECT ch.id as chap_id, ch.primary_coordinator_id as primary_coordinator_id, ch.name as name, ch.financial_report_received as financial_report_received, ch.financial_report_complete as report_complete, cd.coordinator_id AS cord_id, cd.first_name as fname, cd.last_name as lname, st.state_short_name as state, fr.review_complete as review_complete, fr.post_balance as post_balance FROM chapters as ch INNER JOIN state as st ON ch.state=st.id LEFT JOIN financial_report as fr ON fr.chapter_id=ch.id LEFT JOIN coordinator_details as cd ON cd.coordinator_id = fr.reviewer_id WHERE ch.created_at <=  date('$year-06-30') and ch.is_active=1 and ch.primary_coordinator_id IN ($inQryStr) ORDER BY ch.state, ch.name"));

        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $chapterList = DB::select(DB::raw("SELECT ch.id as chap_id,ch.primary_coordinator_id as primary_coordinator_id, ch.name as name, ch.financial_report_received as financial_report_received, ch.financial_report_complete as report_complete, cd.coordinator_id AS cord_id, cd.first_name as fname, cd.last_name as lname, st.state_short_name as state, fr.review_complete as review_complete, fr.post_balance as post_balance
						FROM chapters as ch
						INNER JOIN state as st ON ch.state=st.id
						LEFT JOIN financial_report as fr ON fr.chapter_id=ch.id
						LEFT JOIN coordinator_details as cd ON cd.coordinator_id = fr.reviewer_id
						WHERE ch.created_at <=  date('$year-06-30') and ch.is_active=1 and fr.reviewer_id =$corId"));
            }

        } else {
            $checkBoxStatus = '';
        }

        if (isset($_GET['check2'])) {
            if ($_GET['check2'] == 'yes') {
                $checkBox2Status = 'checked';
                $chapterList = DB::select(DB::raw("SELECT ch.id as chap_id,ch.primary_coordinator_id as primary_coordinator_id, ch.name as name, ch.financial_report_received as financial_report_received, ch.financial_report_complete as report_complete, cd.coordinator_id AS cord_id, cd.first_name as fname, cd.last_name as lname, st.state_short_name as state, fr.review_complete as review_complete, fr.post_balance as post_balance
						FROM chapters as ch
						INNER JOIN state as st ON ch.state=st.id
						LEFT JOIN financial_report as fr ON fr.chapter_id=ch.id
						LEFT JOIN coordinator_details as cd ON cd.coordinator_id = fr.reviewer_id
						WHERE ch.created_at <=  date('$year-06-30') and ch.is_active=1 and ch.primary_coordinator_id =$corId"));
            }

        } else {
            $checkBox2Status = '';
        }

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status];

        return view('reports.review')->with($data);
    }

    /**
     * View the Board Info Received list
     */
    public function showReportToBoardInfo(Request $request)
    {
        $user = $request->user();
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;
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

        $year = date('Y');

        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'rg.short_name as region', 'st.state_short_name as state', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('created_at', '<=', date('Y-06-30'))
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();
        //->paginate(30);

        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $chapterList = DB::table('chapters')
                    ->select('chapters.*', 'st.state_short_name as state', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname')
                    ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '1')
                    ->where('created_at', '<=', date('Y-06-30'))
                    ->where('chapters.primary_coordinator_id', '=', $corId)
                    ->orderBy('st.state_short_name')
                    ->orderBy('chapters.name')
                    ->get();

            }
        } else {
            $checkBoxStatus = '';
        }
        $row_count = count($chapterList);

        if (isset($_GET['board'])) {
            $status = '';
            if ($row_count > 0) {
                for ($i = 0; $i < $row_count; $i++) {
                    if ($chapterList[$i]->new_board_submitted && ! $chapterList[$i]->new_board_active) {
                        //if($chapterList[$i]->id == 1898)
                        $status = $this->activateBoard($chapterList[$i]->id, $lastUpdatedBy);
                    }
                }
            }

            if ($status == 'success') {
                return redirect()->to('/yearreports/boardinfo')->with('success', 'All Board Info has been successfully activated');
            } elseif ($status == 'fail') {
                return redirect()->to('/yearreports/boardinfo')->with('fail', 'Something went wrong, Please try again.');
            } elseif ($status == 'empty') {
                return redirect()->to('/yearreports/boardinfo')->with('success', 'No Incoming Board Members for Activation');
            } elseif ($status == 'duplicate') {
                return redirect()->to('/yearreports/boardinfo')->with('fail', 'Email already used in the system. Please try with new one.');
            } else {
                return redirect()->to('/yearreports/boardinfo')->with('success', 'No Incoming Board Members for Activation');
            }
            exit;
        }

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];
        // echo '<pre>';print_r($data);die;
        return view('reports.boardinfo')->with($data);
    }

    public function activateBoard($chapter_id, $lastUpdatedBy)
    {
        $message = '';
        //Fetching New Board Info from Incoming Board Members
        $incomingBoardDetails = DB::table('incoming_board_member')
            ->select('*')
            ->where('chapter_id', '=', $chapter_id)
            ->orderBy('board_position_id')
            ->get();
        $countIncomingBoardDetails = count($incomingBoardDetails);
        if ($countIncomingBoardDetails > 0) {
            DB::beginTransaction();
            try {
                //Fetching Existing Board Members from Board Details
                $boardDetails = DB::table('board_details')
                    ->select('*')
                    ->where('chapter_id', '=', $chapter_id)
                    ->get();
                $countBoardDetails = count($boardDetails);
                if ($countBoardDetails > 0) {
                    //Insert Outgoing Board Members
                    for ($i = 0; $i < $countBoardDetails; $i++) {
                        $board = DB::table('outgoing_board_member')->insert(
                            ['first_name' => $boardDetails[$i]->first_name,
                                'last_name' => $boardDetails[$i]->last_name,
                                'email' => $boardDetails[$i]->email,
                                'password' => $boardDetails[$i]->password,
                                'board_position_id' => $boardDetails[$i]->board_position_id,
                                'chapter_id' => $chapter_id,
                                'street_address' => $boardDetails[$i]->street_address,
                                'city' => $boardDetails[$i]->city,
                                'state' => $boardDetails[$i]->state,
                                'zip' => $boardDetails[$i]->zip,
                                'country' => $boardDetails[$i]->country,
                                'phone' => $boardDetails[$i]->phone,
                                'last_updated_by' => $lastUpdatedBy,
                                'last_updated_date' => date('Y-m-d H:i:s'),
                                'board_id' => $boardDetails[$i]->board_id,
                                'user_id' => $boardDetails[$i]->user_id]);

                        //Delete Details of Board memebers from users table
                        DB::table('users')
                            ->where('id', $boardDetails[$i]->user_id)
                            ->delete();
                    }
                }
                //Delete Details of Board memebers from Board Detials table
                DB::table('board_details')
                    ->where('chapter_id', $chapter_id)
                    ->delete();

                //Create & Activate Details of Board memebers from Incoming Board Members
                for ($i = 0; $i < $countIncomingBoardDetails; $i++) {
                    $userId = DB::table('users')->insertGetId(
                        ['first_name' => $incomingBoardDetails[$i]->first_name,
                            'last_name' => $incomingBoardDetails[$i]->last_name,
                            'email' => $incomingBoardDetails[$i]->email,
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1]
                    );

                    $boardIdArr = DB::table('board_details')
                        ->select('board_details.board_id')
                        ->orderByDesc('board_details.board_id')
                        ->limit(1)
                        ->get();
                    $boardId = $boardIdArr[0]->board_id + 1;

                    $board = DB::table('board_details')->insert(
                        ['user_id' => $userId,
                            'board_id' => $boardId,
                            'first_name' => $incomingBoardDetails[$i]->first_name,
                            'last_name' => $incomingBoardDetails[$i]->last_name,
                            'email' => $incomingBoardDetails[$i]->email,
                            'password' => Hash::make('TempPass4You'),
                            'remember_token' => '',
                            'board_position_id' => $incomingBoardDetails[$i]->board_position_id,
                            'chapter_id' => $chapter_id,
                            'street_address' => $incomingBoardDetails[$i]->street_address,
                            'city' => $incomingBoardDetails[$i]->city,
                            'state' => $incomingBoardDetails[$i]->state,
                            'zip' => $incomingBoardDetails[$i]->zip,
                            'country' => 'USA',
                            'phone' => $incomingBoardDetails[$i]->phone,
                            //'vacant' => $input['ch_pre_fname'],
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                            'is_active' => 1]
                    );
                }
                //Update Chapter after Board Active
                DB::update('UPDATE chapters SET new_board_active = ? where id = ?', [1, $chapter_id]);

                DB::table('incoming_board_member')
                    ->where('chapter_id', $chapter_id)
                    ->delete();

                DB::commit();
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollback();
                $errorCode = $e->errorInfo[1];
                if ($errorCode == 1062) {
                    //return $message =$e->errorInfo[2];
                    return $message = 'duplicate';
                } else {
                    return $message = 'fail';
                }
            }

            return $message = 'success';
        } else {
            return $message = 'empty';
        }
    }

    /* Listing for boundaries issues */
    public function showReportToIssues(Request $request): View
    {
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

        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('chapters.boundary_issues', '=', '1')
            ->where('chapters.new_board_submitted', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();
        //->paginate(30);
        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $chapterList = DB::table('chapters')
                    ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
                    ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('chapters.boundary_issues', '=', '1')
                    ->where('chapters.new_board_submitted', '=', '1')
                    ->where('bd.board_position_id', '=', '1')
                    ->where('chapters.primary_coordinator_id', $corId)
                    ->orderBy('st.state_short_name')
                    ->orderBy('chapters.name')
                    ->get();
            }
        } else {
            $checkBoxStatus = '';
        }
        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('reports.issues')->with($data);
    }

    /** Listing Chpater Awards */
    public function showChapterAwards(Request $request): View
    {
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
        $chapterList = DB::select(DB::raw("SELECT ch.id as id,ch.name as name,ch.primary_coordinator_id as pc_id,fr.reviewer_id as reviewer_id,cd.coordinator_id AS cord_id, cd.first_name as reviewer_first_name, cd.last_name as reviewer_last_name, st.state_short_name as state,fr.award_1_nomination_type as award_1_type,fr.award_2_nomination_type as award_2_type,fr.award_3_nomination_type as award_3_type,fr.award_4_nomination_type as award_4_type,fr.award_5_nomination_type as award_5_type,fr.check_award_1_approved as award_1_approved,fr.check_award_2_approved as award_2_approved,fr.check_award_3_approved as award_3_approved,fr.check_award_4_approved as award_4_approved,fr.check_award_5_approved as award_5_approved FROM chapters as ch INNER JOIN state as st ON ch.state=st.id LEFT JOIN financial_report as fr ON fr.chapter_id=ch.id LEFT JOIN coordinator_details as cd ON cd.coordinator_id = fr.reviewer_id WHERE ch.is_active=1 and ch.primary_coordinator_id IN ($inQryStr) ORDER BY ch.state, ch.name"));
        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $chapterList = DB::select(DB::raw("SELECT ch.id as id,ch.name as name,ch.primary_coordinator_id as pc_id,fr.reviewer_id as reviewer_id,cd.coordinator_id AS cord_id, cd.first_name as reviewer_first_name, cd.last_name as reviewer_last_name, st.state_short_name as state,fr.award_1_nomination_type as award_1_type,fr.award_2_nomination_type as award_2_type,fr.award_3_nomination_type as award_3_type,fr.award_4_nomination_type as award_4_type,fr.award_5_nomination_type as award_5_type,fr.check_award_1_approved as award_1_approved,fr.check_award_2_approved as award_2_approved,fr.check_award_3_approved as award_3_approved,fr.check_award_4_approved as award_4_approved,fr.check_award_5_approved as award_5_approved FROM chapters as ch INNER JOIN state as st ON ch.state=st.id INNER JOIN financial_report as fr ON fr.chapter_id=ch.id INNER JOIN coordinator_details as cd ON cd.coordinator_id = fr.reviewer_id WHERE cd.is_active=1 AND ch.is_active=1 and fr.reviewer_id =$corId"));

            }
        } else {
            $checkBoxStatus = '';
        }
        $chapterList = json_decode(json_encode($chapterList), true);
        $countList = count($chapterList);
        $data = ['corId' => $corId, 'countList' => $countList, 'chapter_array' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('reports.chapteraward')->with($data);
    }

    /**
     * Add Chaper Awards
     */
    public function addAwards(Request $request): View
    {
        $user = $request->user();
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;
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

        $year = date('Y');

        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'rg.short_name as region', 'st.state_short_name as state', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'fr.award_1_nomination_type as award_1_nomination_type', 'fr.award_2_nomination_type as award_2_nomination_type', 'fr.award_3_nomination_type as award_3_nomination_type', 'fr.award_4_nomination_type as award_4_nomination_type', 'fr.award_5_nomination_type as award_5_nomination_type')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('financial_report as fr', 'chapters.id', '=', 'fr.chapter_id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('created_at', '<=', date('Y-06-30'))
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();
        //->paginate(30);

        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $chapterList = DB::table('chapters')
                    ->select('chapters.*', 'st.state_short_name as state', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'fr.award_1_nomination_type as award_1_nomination_type', 'fr.award_2_nomination_type as award_2_nomination_type', 'fr.award_3_nomination_type as award_3_nomination_type', 'fr.award_4_nomination_type as award_4_nomination_type', 'fr.award_5_nomination_type as award_5_nomination_type')
                    ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                    ->leftJoin('financial_report as fr', 'chapters.id', '=', 'fr.chapter_id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '1')
                    ->where('created_at', '<=', date('Y-06-30'))
                    ->where('chapters.primary_coordinator_id', '=', $corId)
                    ->orderBy('st.state_short_name')
                    ->orderBy('chapters.name')
                    ->get();

            }
        } else {
            $checkBoxStatus = '';
        }
        $row_count = count($chapterList);

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];
        // echo '<pre>';print_r($data);die;
        return view('reports.addawards')->with($data);
    }

    /** Duplicate User List */
    public function showDuplicate(): View
    {

        $userData = DB::table('users')
            ->where('is_active', '=', '1')
            ->groupBy('email')
            ->having(DB::raw('count(email)'), '>', 1)
            ->pluck('email');

        $userList = DB::table('users')
            ->where('is_active', '=', '1')
            ->whereIn('email', $userData)
            ->get();

        $data = ['userList' => $userList];

        return view('reports.duplicateuser')->with($data);
    }

    /** Duplicate Board Id List */
    public function showDuplicateId(): View
    {

        $userData = DB::table('board_details')
            ->where('is_active', '=', '1')
            ->groupBy('board_id')
            ->having(DB::raw('count(board_id)'), '>', 1)
            ->pluck('board_id');

        $userList = DB::table('board_details')
            ->where('is_active', '=', '1')
            ->whereIn('board_id', $userData)
            ->orderBy('board_id')
            ->get();

        $data = ['userList' => $userList];

        return view('reports.duplicateboardid')->with($data);
    }

    /** Multiple Board List */
    public function showMultiple(): View
    {

        $userData = DB::table('board_details')
            ->where('is_active', '=', '1')
            ->groupBy('email')
            ->having(DB::raw('count(email)'), '>', 1)
            ->pluck('email');

        $userList = DB::table('board_details')
            ->where('is_active', '=', '1')
            ->whereIn('email', $userData)
            ->get();

        $data = ['userList' => $userList];

        return view('reports.multipleboard')->with($data);
    }

    /** Board with No President List */
    public function showNoPresident(): View
    {

        $PresId = DB::table('board_details')
            ->where('is_active', '=', '1')
            ->where('board_position_id', '=', '1')
            ->pluck('chapter_id');

        $ChapterPres = DB::table('chapters')
            ->where('is_active', '=', '1')
            ->whereNotIn('id', $PresId)
            ->get();

        $data = ['ChapterPres' => $ChapterPres];

        return view('reports.nopresident')->with($data);
    }
}
