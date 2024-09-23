<?php

namespace App\Http\Controllers;

use App\Mail\EOYElectionReportReminder;
use App\Mail\EOYFinancialReportReminder;
use App\Mail\EOYLateReportReminder;
use App\Models\Chapter;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct()
    {
        //$this->middleware('preventBackHistory');
        $this->middleware('auth')->except('logout');
    }

    /**
     * Chpater Status Report
     */
    public function showChapterStatus(Request $request): View
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

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
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

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1');

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($positionId == 5) {
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

        return view('reports.chapterstatus')->with($data);
    }

    /**
     * View the Downloads List
     */
    public function showDownloads(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
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
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
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
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
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

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'db.month_long_name as start_month')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('db_month as db', 'chapters.start_month_id', '=', 'db.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name');

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($positionId == 5) {
            $baseQuery->where('chapters.region', '=', $corRegId);
        } else {
            $baseQuery->whereIn('chapters.primary_coordinator_id', $inQryArr);
        }

        $chapterList = $baseQuery->get();

        $data = ['chapterList' => $chapterList, 'corId' => $corId];

        return view('reports.einstatus')->with($data);
    }

    /**
     * View the International EIN Status
     */
    public function showIntEINstatus(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
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
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
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
            ->where('cd.conference_id', '=', '5')
            ->where('cd.position_id', '<=', '6')
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

    public function showChapterNew(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
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

        //Get Chapter List mapped with login coordinator
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'chapters.id as ch_id', 'chapters.name as ch_name', 'db.month_short_name as month_name', 'db.month_long_name as start_month', 'start_year as year', 'cd.first_name as cor_f_name',
                'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone',
                'st.state_short_name as ch_state', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('db_month as db', 'chapters.start_month_id', '=', 'db.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->whereRaw('DATE_ADD(CONCAT(chapters.start_year, "-", chapters.start_month_id, "-01"), INTERVAL 1 YEAR) > CURDATE()')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $chapterList = DB::table('chapters')
                    ->select('chapters.*', 'chapters.id as ch_id', 'chapters.name as ch_name', 'db.month_short_name as month_name', 'db.month_long_name as start_month', 'start_year as year', 'cd.first_name as cor_f_name',
                        'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone',
                        'st.state_short_name as ch_state', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname')
                    ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                    ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                    ->leftJoin('db_month as db', 'chapters.start_month_id', '=', 'db.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '1')
                    ->where('chapters.primary_coordinator_id', '=', $corId)
                    ->whereRaw('DATE_ADD(CONCAT(chapters.start_year, "-", chapters.start_month_id, "-01"), INTERVAL 1 YEAR) > CURDATE()')
                    ->orderBy('st.state_short_name')
                    ->orderBy('chapters.name')
                    ->get();
            }
        } else {
            $checkBoxStatus = '';
        }

        $data = ['chapterList' => $chapterList, 'corId' => $corId];

        return view('reports.chapternew')->with($data);
    }

    /**
     * Large Chapter Report
     */
    public function showChapterLarge(Request $request): View
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

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
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

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.members_paid_for', '>=', '60');

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($positionId == 5) {
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

        return view('reports.chapterlarge')->with($data);
    }

    /**
     * Chapter Probation Report
     */
    public function showChapterProbation(Request $request): View
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

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
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

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->whereIn('chapters.status', $status);

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($positionId == 5) {
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

        return view('reports.chapterprobation')->with($data);
    }

    /**
     * View the Social Media Report
     */
    public function showSocialMedia(Request $request): View
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

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
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

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'db.month_long_name as start_month')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('db_month as db', 'chapters.start_month_id', '=', 'db.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderByDesc('st.state_short_name')
            ->orderByDesc('chapters.name');

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($positionId == 5) {
            $baseQuery->where('chapters.region', '=', $corRegId);
        } else {
            $baseQuery->whereIn('chapters.primary_coordinator_id', $inQryArr);
        }

        $chapterList = $baseQuery->get();

        $data = ['chapterList' => $chapterList, 'corId' => $corId];

        return view('reports.socialmedia')->with($data);
    }

    /**
     * View the M2M Doantions
     */
    public function showM2Mdonation(Request $request): View
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

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
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

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1');

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($positionId == 5) {
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

        return view('reports.m2mdonation')->with($data);
    }

    /**
     * View the International M2M Doantions
     */
    public function showIntM2Mdonation(Request $request): View
    {
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
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
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $chapterList = DB::table('chapters')
                    ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                        'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
                    ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                    ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
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

            if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
                //Show Full Conference or Region
                $reportIdList = DB::table('coordinator_reporting_tree as crt')
                    ->select('crt.id')
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

            $baseQuery = DB::table('chapters')
                ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                    'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1');

            if ($positionId == 6 || $positionId == 25) {
                $baseQuery->where('chapters.conference', '=', $corConfId);
            } elseif ($positionId == 5) {
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
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $request->session()->put('positionid', $positionId);

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
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

        $baseQuery = DB::table('coordinators as cd')
            ->select('cd.id as cor_id', 'cd.layer_id as layer_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email',
                'cd.report_id as report_id', 'cp.long_title as position',
                DB::raw('(SELECT cp2.long_title FROM coordinator_position as cp2 WHERE cp2.id = cd.sec_position_id) as sec_pos'), // Subquery to get secondary position
                'rg.short_name as reg', 'cd.conference_id as cor_conf')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.region_id')
            ->orderByDesc('cp.id');

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('cd.conference_id', '=', $corConfId);
        } elseif ($positionId == 5) {
            $baseQuery->where('cd.region_id', '=', $corRegId);
        } else {
            $baseQuery->whereIn('cd.id', $inQryArr);
        }

        $coordinatorList = $baseQuery->get();

        foreach ($coordinatorList as $list) {
            $reportingData = $this->calculateReporting($list->cor_id, $list->layer_id, $inQryArr);

            $list->direct_report = $reportingData['direct_report'];
            $list->indirect_report = $reportingData['indirect_report'];
            $list->total_report = $reportingData['total_report'];
        }

        $data = ['coordinatorList' => $coordinatorList];

        return view('reports.chaptervolunteer')->with($data);
    }

    /**
     * Calculate Direct/Indirect Reports
     */
    private function calculateReporting($coordinatorId, $corlayerId, $inQryArr)
    {
        // Calculate direct chapter report
        $coordinator_options = DB::table('chapters')
            ->select('name')
            ->where('primary_coordinator_id', $coordinatorId)
            ->where('is_active', '1')
            ->get();
        $direct_report = count($coordinator_options);

        // Calculate indirect chapter report
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $reportIdList = DB::table('coordinator_reporting_tree as crt')
            ->select('crt.id')
            ->where($sqlLayerId, '=', $coordinatorId)
            ->get();

        $inQryStr = '';
        foreach ($reportIdList as $key => $val) {
            $inQryStr .= $val->id.',';
        }
        $inQryStr = rtrim($inQryStr, ',');
        $inQryArr = explode(',', $inQryStr);

        $indirectChapterReport = DB::table('chapters')
            ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.inquiries_contact as inq_con', 'chapters.territory as terry', 'chapters.status as status', 'chapters.inquiries_note as inq_note')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->get();

        $indirect_report = count($indirectChapterReport) - $direct_report;
        $total_report = $direct_report + $indirect_report;

        return [
            'direct_report' => $direct_report,
            'indirect_report' => $indirect_report,
            'total_report' => $total_report,
        ];
    }

    /**
     * View the Coordinator ToDo List
     */
    public function showCoordinatorToDo(Request $request): View
    {
        //Get Coordinator Details
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

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
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

        $baseQuery = DB::table('coordinators as cd')
            ->select('cd.id as cor_id', 'cd.layer_id as layer_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.report_id as report_id', 'cd.sec_position_id as sec_position_id', 'cp.long_title as position', 'rg.short_name as reg', 'cd.conference_id as cor_conf',
                'cd.todo_month as todo_month', 'cd.todo_check_chapters as todo_check_chapters', 'cd.todo_election_faq as todo_election_faq', 'cd.dashboard_updated as dashboard_updated')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('rg.short_name')
            ->orderByDesc('cp.id');

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('cd.conference_id', '=', $corConfId);
        } elseif ($positionId == 5) {
            $baseQuery->where('cd.region_id', '=', $corRegId);
        } else {
            $baseQuery->whereIn('cd.id', $inQryArr);
        }

        $coordinatorList = $baseQuery->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('reports.coordinatortodo')->with($data);

    }

    /**
     * View the International Coordinator ToDo List
     */
    public function showIntCoordinatorToDo(Request $request): View
    {
        //Get Coordinator Details
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
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
        $coordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cor_id', 'cd.layer_id as layer_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.report_id as report_id', 'cd.sec_position_id as sec_position_id', 'cp.long_title as position', 'rg.short_name as reg', 'cd.conference_id as cor_conf',
                'cd.todo_month as todo_month', 'cd.todo_send_rereg as todo_send_rereg', 'cd.todo_send_late as todo_send_late', 'cd.todo_record_rereg as todo_record_rereg', 'cd.todo_record_m2m as todo_record_m2m', 'cd.todo_export_reports as todo_export_reports', 'cd.dashboard_updated as dashboard_updated')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->where('cd.is_active', '=', '1')
            ->whereIn('cd.position_id', ['6', '25'])
            ->whereIn('cd.id', $inQryArr)
            ->orderBy('rg.short_name')
            ->orderByDesc('cp.id')
            ->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('reports.intcoordinatortodo')->with($data);

    }

    /**
     * BoardList
     */
    public function showBoardlist(Request $request): View
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
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
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
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
                    ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
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
                    ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
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
                    ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
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
                    ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
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

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
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

        $baseQuery = DB::table('coordinators as cd')
            ->select('cd.id as cor_id', 'cd.layer_id as layer_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email',
                'cd.report_id as report_id', 'cd.sec_position_id as sec_position_id', 'cp.long_title as position', 'rg.short_name as reg', 'cd.conference_id as cor_conf',
                'cd.recognition_year0 as yr_0', 'cd.recognition_year1 as yr_1', 'cd.recognition_year2 as yr_2', 'cd.recognition_year3 as yr_3', 'cd.recognition_year4 as yr_4',
                'cd.recognition_year5 as yr_5', 'cd.recognition_year6 as yr_6', 'cd.recognition_year7 as yr_7', 'cd.recognition_year8 as yr_8', 'cd.recognition_year9 as yr_9',
                'cd.recognition_toptier as toptier', 'cd.coordinator_start_date as start_date', 'cd.recognition_necklace as necklace')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.coordinator_start_date');

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('cd.conference_id', '=', $corConfId);
        } elseif ($positionId == 5) {
            $baseQuery->where('cd.region_id', '=', $corRegId);
        } else {
            $baseQuery->whereIn('cd.id', $inQryArr);
        }

        $coordinatorList = $baseQuery->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('reports.appreciation')->with($data);
    }

    /**
     * View the Volunteer Birthday list
     */
    public function showBirthday(Request $request): View
    {
        //Get Coordinator Details
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

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
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

        $baseQuery = DB::table('coordinators as cd')
            ->select('cd.id as cor_id', 'cd.layer_id as layer_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.report_id as report_id', 'cd.sec_position_id as sec_position_id', 'cd.card_sent as card_sent', 'cp.long_title as position', 'rg.short_name as reg', 'cd.conference_id as cor_conf', 'cd.birthday_month_id as b_month', 'cd.birthday_day as b_day', 'db.month_long_name as month')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->join('db_month as db', 'cd.birthday_month_id', '=', 'db.id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.birthday_month_id')
            ->orderBy('cd.birthday_day');

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('cd.conference_id', '=', $corConfId);
        } elseif ($positionId == 5) {
            $baseQuery->where('cd.region_id', '=', $corRegId);
        } else {
            $baseQuery->whereIn('cd.id', $inQryArr);
        }

        $coordinatorList = $baseQuery->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('reports.birthday')->with($data);
    }

    /**
     * View the Reporting Tree
     */
    public function showReportingTree(Request $request): View
    {
        $coordinator_array = [];
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corConfId = $corDetails['conference_id'];
        $positionId = $corDetails['position_id'];
        $request->session()->put('positionid', $positionId);
        $cord_pos_id = $request->session()->get('positionid');

        if ($positionId != 7) {
            $coordinatorDetails = DB::table('coordinators')
                ->select('coordinators.id AS id', 'coordinators.first_name', 'coordinators.last_name', 'pos1.short_title AS position_title',
                    'pos2.short_title AS sec_position_title', 'coordinators.layer_id', 'coordinators.report_id', 'coordinators.report_id AS tree_id',
                    'region.short_name AS region')
                ->join('coordinator_position as pos1', 'pos1.id', '=', 'coordinators.position_id')
                ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'coordinators.sec_position_id')
                ->join('region', 'coordinators.region_id', '=', 'region.id')
                ->where('coordinators.on_leave', 0)
                ->where('coordinators.conference_id', $corConfId)
                ->where('coordinators.is_active', 1)
                ->orderBy('coordinators.region_id')
                ->orderBy('coordinators.position_id', 'desc')
                ->get();
        } else {
            $coordinatorDetails = DB::table('coordinators')
                ->select('coordinators.coordinator_id AS id', 'coordinators.first_name', 'coordinators.last_name', 'pos1.short_title AS position_title',
                    'pos2.short_title AS sec_position_title', 'coordinators.layer_id', 'coordinators.report_id', 'coordinators.report_id AS tree_id',
                    'region.short_name AS region')
                ->join('coordinator_position as pos1', 'pos1.id', '=', 'coordinators.position_id')
                ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'coordinators.sec_position_id')
                ->join('region', 'coordinators.region_id', '=', 'region.id')
                ->where('coordinators.on_leave', 0)
                ->where('coordinators.is_active', 1)
                ->orderBy('coordinators.region_id')
                ->orderBy('coordinators.position_id', 'desc')
                ->get();
        }
        foreach ($coordinatorDetails as $key => $value) {
            $coordinator_array[$key] = (array) $value;
        }

        return view('reports.reportingtree', [
            'coordinator_array' => $coordinator_array,
            'cord_pos_id' => $cord_pos_id,
        ]);
    }

    /**
     * get CCMail
     */
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
                $corList = DB::table('coordinators as cd')
                    ->select('cd.email as cord_email')
                    ->where('cd.id', '=', $val)
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
        $request->session()->put('corregid', $corRegId);

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
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

        $year = date('Y');

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'rg.short_name as region', 'st.state_short_name as state', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where(function ($query) {
                $query->where('created_at', '<=', date('Y-06-30'))
                      ->orWhereNull('created_at');
            });

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($positionId == 5) {
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

        return view('reports.eoystatus')->with($data);
    }

    /**
     * View the Financial Reports List
     */
    public function showReportToReview(Request $request): View
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

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
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

        $year = date('Y');

        $baseQuery = DB::table('chapters as ch')
            ->select('ch.id as chap_id', 'ch.primary_coordinator_id as primary_coordinator_id', 'ch.name as name', 'ch.financial_report_received as financial_report_received',
                'ch.financial_report_complete as report_complete', 'ch.report_extension as report_extension', 'ch.extension_notes as extension_notes', 'cd.id AS cord_id', 'cd.first_name as fname', 'cd.last_name as lname', 'st.state_short_name as state',
                'fr.submitted as report_received', 'fr.review_complete as review_complete', 'fr.post_balance as post_balance', 'fr.financial_pdf_path as financial_pdf_path', 'cd_reviewer.first_name as pcfname', 'cd_reviewer.last_name as pclname')
            ->join('state as st', 'ch.state', '=', 'st.id')
            ->leftJoin('financial_report as fr', 'fr.chapter_id', '=', 'ch.id')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->leftJoin('coordinators as cd_reviewer', 'cd_reviewer.id', '=', 'fr.reviewer_id')
            ->where(function ($query) {
                $query->where('created_at', '<=', date('Y-06-30'))
                      ->orWhereNull('created_at');
            })
            ->where('ch.is_active', 1);

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('ch.conference', '=', $corConfId);
        } elseif ($positionId == 5) {
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

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
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

        $year = date('Y');

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'rg.short_name as region', 'st.state_short_name as state', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where(function ($query) {
                $query->where('created_at', '<=', date('Y-06-30'))
                      ->orWhereNull('created_at');
            });

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($positionId == 5) {
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
                return redirect()->to('/yearreports/boardinfo')->with('success', 'All Board Info has been successfully activated');
            } elseif ($status == 'fail') {
                return redirect()->to('/yearreports/boardinfo')->with('fail', 'Something went wrong, Please try again.');
            } else {
                return redirect()->to('/yearreports/boardinfo')->with('info', 'No Incoming Board Members for Activation');
            }
            exit;
        }

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('reports.boardinfo')->with($data);
    }

    /**
     * Board Election Report Reminder Auto Send
     */
    public function showReminderBoardInfo(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corName = $corDetails['first_name'].' '.$corDetails['last_name'];

        //Get Chapter List mapped with login coordinator
        $chapters = Chapter::select('chapters.name as name', 'state.state_short_name as state', 'boards.email as bor_email',
            'chapters.primary_coordinator_id as pcid', 'chapters.email as ch_email', 'chapters.start_month_id as start_month',
            'boards.board_position_id')
            ->join('state', 'chapters.state', '=', 'state.id')
            ->join('boards', 'chapters.id', '=', 'boards.chapter_id')
            ->whereIn('boards.board_position_id', [1, 2, 3, 4, 5])
            ->where('chapters.conference', $corConfId)
            ->where(function ($query) {
                $query->where('chapters.new_board_submitted', '=', '0')
                    ->orWhereNull('chapters.new_board_submitted');
            })
            ->where(function ($query) {
                $query->where('created_at', '<=', date('Y-06-30'))
                      ->orWhereNull('created_at');
            })
            ->where('chapters.is_active', 1)
            ->get();

        $cc_email = [];
        $chapterEmails = []; // Store email addresses for each chapter
        $coordinatorEmails = []; // Store coordinator email addresses by chapter
        $chapterChEmails = []; // Store ch_email addresses by chapter

        $mailData = [];

        if ($chapters->isEmpty()) {
            return redirect()->back()->with('info', 'There are no Chapters with Board Election Reports Due.');
        } else {
            foreach ($chapters as $chapter) {
                if ($chapter->name) {
                    $chapterEmails[$chapter->name][] = $chapter->bor_email; // Store emails by chapter name

                    $cc_email1 = $this->getCCMail($chapter->pcid);
                    $cc_email1 = array_filter($cc_email1);

                    if (! empty($cc_email1)) {
                        $coordinatorEmails[$chapter->name] = $cc_email1; // Store coordinator emails by chapter
                    }
                }

                // Check if ch_email is not null before adding it to the chapterChEmails array
                if ($chapter->ch_email) {
                    $chapterChEmails[$chapter->name] = $chapter->ch_email;
                    $chapterEmails[$chapter->name][] = $chapter->ch_email; // Add ch_email to chapterEmails
                }

                // Set the state for this chapter
                $chapterState = $chapter->state; // Use the state for this chapter

                $mailData[$chapter->name] = [
                    'chapterName' => $chapter->name,
                    'chapterState' => $chapterState,
                ];

                if (isset($chapterChEmails[$chapter->name])) {
                    $chapterEmails[$chapter->name][] = $chapterChEmails[$chapter->name];
                }
            }
        }

        foreach ($mailData as $chapterName => $data) {
            $emailRecipients = isset($chapterEmails[$chapterName]) ? $chapterEmails[$chapterName] : [];
            $cc_email = isset($coordinatorEmails[$chapterName]) ? $coordinatorEmails[$chapterName] : [];

            if (! empty($emailRecipients)) {
                // Split recipients into batches of 50 - so won't be over 100 after adding ccRecipients
                $toBatches = array_chunk($emailRecipients, 25);

                foreach ($toBatches as $toBatch) {
                    Mail::to($toBatch)
                        ->cc($cc_email)
                        ->queue(new EOYElectionReportReminder($data));

                    // usleep(500000); // Delay for 0.5 seconds between each batch
                }
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

        return redirect()->to('/yearreports/boardinfo')->with('success', 'Board Election Reminders have been successfully sent.');

    }

    /**
     * Financial Report Reminder Auto Send
     */
    public function showReminderFinancialReport(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corName = $corDetails['first_name'].' '.$corDetails['last_name'];

        //Get Chapter List mapped with login coordinator
        $chapters = Chapter::select('chapters.*', 'chapters.name as name', 'state.state_short_name as state', 'boards.email as bor_email',
            'chapters.primary_coordinator_id as pcid', 'chapters.email as ch_email', 'chapters.start_month_id as start_month',
            'boards.board_position_id')
            ->join('state', 'chapters.state', '=', 'state.id')
            ->join('boards', 'chapters.id', '=', 'boards.chapter_id')
            ->join('financial_report', 'chapters.id', '=', 'financial_report.chapter_id')
            ->whereIn('boards.board_position_id', [1, 2, 3, 4, 5])
            ->where('financial_report.reviewer_id', null)
            ->where(function ($query) {
                $query->where('chapters.report_extension', '=', '0')
                    ->orWhereNull('chapters.report_extension');
            })
            ->where('chapters.conference', $corConfId)
            ->where(function ($query) {
                $query->where('chapters.financial_report_received', '=', '0')
                    ->orWhereNull('chapters.financial_report_received');
            })
            ->where(function ($query) {
                $query->where('created_at', '<=', date('Y-06-30'))
                      ->orWhereNull('created_at');
            })
            ->where('chapters.is_active', 1)
            ->get();

        $cc_email = [];
        $chapterEmails = []; // Store email addresses for each chapter
        $coordinatorEmails = []; // Store coordinator email addresses by chapter
        $chapterChEmails = []; // Store ch_email addresses by chapter

        $mailData = [];

        if ($chapters->isEmpty()) {
            return redirect()->back()->with('info', 'There are no Chapters with Financial Reports Due.');
        } else {
            foreach ($chapters as $chapter) {
                if ($chapter->name) {
                    $chapterEmails[$chapter->name][] = $chapter->bor_email; // Store emails by chapter name

                    $cc_email1 = $this->getCCMail($chapter->pcid);
                    $cc_email1 = array_filter($cc_email1);

                    if (! empty($cc_email1)) {
                        $coordinatorEmails[$chapter->name] = $cc_email1; // Store coordinator emails by chapter
                    }
                }

                // Check if ch_email is not null before adding it to the chapterChEmails array
                if ($chapter->ch_email) {
                    $chapterChEmails[$chapter->name] = $chapter->ch_email;
                    $chapterEmails[$chapter->name][] = $chapter->ch_email; // Add ch_email to chapterEmails
                }

                // Set the state for this chapter
                $chapterState = $chapter->state; // Use the state for this chapter

                $mailData[$chapter->name] = [
                    'chapterName' => $chapter->name,
                    'chapterState' => $chapterState,
                ];

                if (isset($chapterChEmails[$chapter->name])) {
                    $chapterEmails[$chapter->name][] = $chapterChEmails[$chapter->name];
                }
            }
        }

        foreach ($mailData as $chapterName => $data) {
            $emailRecipients = isset($chapterEmails[$chapterName]) ? $chapterEmails[$chapterName] : [];
            $cc_email = isset($coordinatorEmails[$chapterName]) ? $coordinatorEmails[$chapterName] : [];

            if (! empty($emailRecipients)) {
                // Split recipients into batches of 50 - so won't be over 100 after adding ccRecipients
                $toBatches = array_chunk($emailRecipients, 25);

                foreach ($toBatches as $toBatch) {
                    Mail::to($toBatch)
                        ->cc($cc_email)
                        ->queue(new EOYFinancialReportReminder($data));

                    // usleep(500000); // Delay for 0.5 seconds between each batch
                }
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

        return redirect()->to('/yearreports/review')->with('success', 'Financial Report Reminders have been successfully sent.');

    }

    /**
     * EOY Reports LATE Reminder Auto Send
     */
    public function showReminderEOYReportsLate(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corName = $corDetails['first_name'].' '.$corDetails['last_name'];

        //Get Chapter List mapped with login coordinator
        $chapters = Chapter::select('chapters.*', 'chapters.name as name', 'state.state_short_name as state', 'boards.email as bor_email',
            'chapters.primary_coordinator_id as pcid', 'chapters.email as ch_email', 'chapters.start_month_id as start_month',
            'boards.board_position_id')
            ->join('state', 'chapters.state', '=', 'state.id')
            ->join('boards', 'chapters.id', '=', 'boards.chapter_id')
            ->join('financial_report', 'chapters.id', '=', 'financial_report.chapter_id')
            ->whereIn('boards.board_position_id', [1, 2, 3, 4, 5])
            ->where('financial_report.reviewer_id', null)
            ->where(function ($query) {
                $query->where('chapters.report_extension', '=', '0')
                    ->orWhereNull('chapters.report_extension');
            })->where('chapters.conference', $corConfId)
            // ->where(function ($query) {
            //     $query->where('chapters.new_board_submitted', null)
            //         ->orWhere('chapters.financial_report_received', null);
            // })
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

        $cc_email = [];
        $chapterEmails = []; // Store email addresses for each chapter
        $coordinatorEmails = []; // Store coordinator email addresses by chapter
        $chapterChEmails = []; // Store ch_email addresses by chapter

        $mailData = [];

        if ($chapters->isEmpty()) {
            return redirect()->back()->with('info', 'There are no Chapters with End of Year Reports Due.');
        } else {
            foreach ($chapters as $chapter) {
                if ($chapter->name) {
                    $chapterEmails[$chapter->name][] = $chapter->bor_email; // Store emails by chapter name

                    $cc_email1 = $this->getCCMail($chapter->pcid);
                    $cc_email1 = array_filter($cc_email1);

                    if (! empty($cc_email1)) {
                        $coordinatorEmails[$chapter->name] = $cc_email1; // Store coordinator emails by chapter
                    }
                }

                // Check if ch_email is not null before adding it to the chapterChEmails array
                if ($chapter->ch_email) {
                    $chapterChEmails[$chapter->name] = $chapter->ch_email;
                    $chapterEmails[$chapter->name][] = $chapter->ch_email; // Add ch_email to chapterEmails
                }

                // Set the state for this chapter
                $chapterState = $chapter->state; // Use the state for this chapter

                $mailData[$chapter->name] = [
                    'chapterName' => $chapter->name,
                    'chapterState' => $chapterState,
                    'boardElectionReportReceived' => $chapter->new_board_submitted,
                    'financialReportReceived' => $chapter->financial_report_received,
                    '990NSubmissionReceived' => $chapter->financial_report_received,
                    'einLetterCopyReceived' => $chapter->ein_letter_path,
                ];

                if (isset($chapterChEmails[$chapter->name])) {
                    $chapterEmails[$chapter->name][] = $chapterChEmails[$chapter->name];
                }
            }
        }

        // Send emails in batches
        foreach ($mailData as $chapterName => $data) {
            $emailRecipients = isset($chapterEmails[$chapterName]) ? $chapterEmails[$chapterName] : [];
            $cc_email = isset($coordinatorEmails[$chapterName]) ? $coordinatorEmails[$chapterName] : [];

            if (! empty($emailRecipients)) {
                // Split recipients into batches of 50
                $toBatches = array_chunk($emailRecipients, 25);

                foreach ($toBatches as $toBatch) {
                    Mail::to($toBatch)
                        ->cc($cc_email)
                        ->queue(new EOYLateReportReminder($data));

                    // usleep(500000); // Delay for 0.5 seconds between each batch
                }
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

        return redirect()->to('/yearreports/eoystatus')->with('success', 'EOY Late Notices have been successfully sent.');

    }

    //

    /**
     * Activate Board
     */
    // public function activateBoard($chapter_id, $lastUpdatedBy)
    // {
    //     $message = '';
    //     //Fetching New Board Info from Incoming Board Members
    //     $incomingBoardDetails = DB::table('incoming_board_member')
    //         ->select('*')
    //         ->where('chapter_id', '=', $chapter_id)
    //         ->orderBy('board_position_id')
    //         ->get();
    //     $countIncomingBoardDetails = count($incomingBoardDetails);
    //     if ($countIncomingBoardDetails > 0) {
    //         DB::beginTransaction();
    //         try {
    //             //Fetching Existing Board Members from Board Details
    //             $boardDetails = DB::table('boards')
    //                 ->select('*')
    //                 ->where('chapter_id', '=', $chapter_id)
    //                 ->get();
    //             $countBoardDetails = count($boardDetails);
    //             if ($countBoardDetails > 0) {
    //                 //Insert Outgoing Board Members
    //                 foreach ($boardDetails as $record) {
    //                     // Fetch existing password
    //                     $existingPassword = DB::table('users')
    //                         ->where('id', $record->user_id)
    //                         ->value('password');

    //                     // Set default password if existing password is null
    //                     if (is_null($existingPassword)) {
    //                         $existingPassword = Hash::make('TempPass4You');
    //                     }

    //                     DB::table('outgoing_board_member')->insert(
    //                         [
    //                             'first_name' => $record->first_name,
    //                             'last_name' => $record->last_name,
    //                             'email' => $record->email,
    //                             'password' => $existingPassword, // Use existing or default password
    //                             'remember_token' => '',
    //                             'board_position_id' => $record->board_position_id,
    //                             'chapter_id' => $chapter_id,
    //                             'street_address' => $record->street_address,
    //                             'city' => $record->city,
    //                             'state' => $record->state,
    //                             'zip' => $record->zip,
    //                             'country' => $record->country,
    //                             'phone' => $record->phone,
    //                             'last_updated_by' => $lastUpdatedBy,
    //                             'last_updated_date' => now(),
    //                             'board_id' => $record->board_id,
    //                             'user_id' => $record->user_id,
    //                         ]
    //                     );

    //                     //Delete Details of Board members from users table
    //                     DB::table('users')->where('id', $record->user_id)->delete();
    //                 }

    //                 //Delete Details of Board members from Board Details table
    //                 DB::table('boards')->where('chapter_id', $chapter_id)->delete();

    //                 // Fetch the latest board_id and increment it for each new board member
    //                 $latestBoardId = DB::table('boards')
    //                     ->select('board_id')
    //                     ->orderByDesc('board_id')
    //                     ->value('board_id');

    //                 // Set initial board_id
    //                 $boardId = is_null($latestBoardId) ? 1 : $latestBoardId + 1;

    //                 // Create & Activate Details of Board members from Incoming Board Members
    //                 foreach ($incomingBoardDetails as $incomingRecord) {
    //                     // Check if user already exists
    //                     $existingUser = DB::table('users')->where('email', $incomingRecord->email)->first();

    //                     if ($existingUser) {
    //                         $userId = $existingUser->id;
    //                     } else {
    //                         // Insert new user
    //                         $userId = DB::table('users')->insertGetId(
    //                             [
    //                                 'first_name' => $incomingRecord->first_name,
    //                                 'last_name' => $incomingRecord->last_name,
    //                                 'email' => $incomingRecord->email,
    //                                 'password' => Hash::make('TempPass4You'),
    //                                 'user_type' => 'board',
    //                                 'is_active' => 1,
    //                             ]
    //                         );
    //                     }

    //                     // Fetch the latest board_id for each new board member
    //                     $latestBoardId = DB::table('boards')
    //                         ->select('board_id')
    //                         ->orderByDesc('board_id')
    //                         ->value('board_id');

    //                     // Set board_id for the new board member
    //                     $boardId = is_null($latestBoardId) ? 1 : $latestBoardId + 1;

    //                     // Prepare board details data
    //                     $boardDetailsData = [
    //                         'user_id' => $userId,
    //                         'board_id' => $boardId,
    //                         'first_name' => $incomingRecord->first_name,
    //                         'last_name' => $incomingRecord->last_name,
    //                         'email' => $incomingRecord->email,
    //                         'password' => Hash::make('TempPass4You'),
    //                         'remember_token' => '',
    //                         'board_position_id' => $incomingRecord->board_position_id,
    //                         'chapter_id' => $chapter_id,
    //                         'street_address' => $incomingRecord->street_address,
    //                         'city' => $incomingRecord->city,
    //                         'state' => $incomingRecord->state,
    //                         'zip' => $incomingRecord->zip,
    //                         'country' => 'USA',
    //                         'phone' => $incomingRecord->phone,
    //                         'last_updated_by' => $lastUpdatedBy,
    //                         'last_updated_date' => now(),
    //                         'is_active' => 1,
    //                     ];

    //                     // Upsert board details
    //                     DB::table('boards')->upsert(
    //                         [$boardDetailsData], // The values to insert or update
    //                         ['user_id', 'chapter_id'], // The unique constraints for upsert
    //                         array_keys($boardDetailsData) // The columns to update if a conflict occurs
    //                     );

    //                     // Increment board_id for the next board member
    //                     $boardId++;
    //                 }

    //                 //Update Chapter after Board Active
    //                 DB::update('UPDATE chapters SET new_board_active = ? WHERE id = ?', [1, $chapter_id]);

    //                 //Delete Details of Board members from Incoming Board Member table
    //                 DB::table('incoming_board_member')
    //                     ->where('chapter_id', $chapter_id)
    //                     ->delete();
    //             }

    //             $chunkSize = 100;

    //             // Update or insert for outgoing board members
    //             $outgoingBoardMembers = DB::table('outgoing_board_member')->get();
    //             foreach (array_chunk($outgoingBoardMembers->toArray(), $chunkSize) as $chunk) {
    //                 foreach ($chunk as $outgoingMember) {
    //                     $outgoingUser = DB::table('users')->where('email', $outgoingMember->email)->first();

    //                     if ($outgoingUser) {
    //                         // Update user_type for existing record
    //                         DB::table('users')->where('email', $outgoingMember->email)->update([
    //                             'user_type' => 'outgoing',
    //                         ]);

    //                         // Retrieve the user_id
    //                         $userId = $outgoingUser->id;
    //                     } else {
    //                         // Insert new record
    //                         $userId = DB::table('users')->insertGetId([
    //                             'email' => $outgoingMember->email,
    //                             'first_name' => $outgoingMember->first_name,
    //                             'last_name' => $outgoingMember->last_name,
    //                             'password' => Hash::make('TempPass4You'),
    //                             'remember_token' => '',
    //                             'user_type' => 'outgoing',
    //                             'is_active' => 1,
    //                         ]);
    //                     }

    //                     // Update outgoing_board_member with user_id
    //                     DB::table('outgoing_board_member')->where('email', $outgoingMember->email)->update([
    //                         'user_id' => $userId,
    //                     ]);
    //                 }
    //             }

    //             // Only update for board members who exist in the users table
    //             $boardMembers = DB::table('boards')->get();
    //             foreach ($boardMembers as $member) {
    //                 $user = DB::table('users')->where('email', $member->email)->first();

    //                 if ($user) {
    //                     // Update user_type for existing record
    //                     DB::table('users')->where('email', $member->email)->update([
    //                         'user_type' => 'board',
    //                     ]);
    //                 }
    //             }

    //             DB::commit();
    //         } catch (\Illuminate\Database\QueryException $e) {
    //             DB::rollback();
    //             Log::error($e);
    //             $errorCode = $e->errorInfo[1];
    //             if ($errorCode == 1062) {
    //                 return $message = $e->errorInfo[2];
    //             } else {
    //                 return $message = 'fail';
    //             }
    //         }

    //         return $message = 'success';
    //     }
    // }

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

    /**
     * Boundaires Issues
     */
    public function showReportToIssues(Request $request): View
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

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
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

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('chapters.boundary_issues', '=', '1')
            ->where('chapters.new_board_submitted', '=', '1')
            ->where('bd.board_position_id', '=', '1');

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($positionId == 5) {
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

        return view('reports.issues')->with($data);
    }

    /**
     * List of Chapter Awards
     */
    public function showChapterAwards(Request $request): View
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

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
                ->get();
        } else {
            //Get Coordinator Reporting Tree
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
                ->where($sqlLayerId, '=', $corId)
                ->get();
        }
        $reportIds = $reportIdList->pluck('id')->toArray();
        $inQryStr = implode(',', $reportIds);

        $baseQuery = DB::table('chapters as ch')
            ->select('ch.id as id', 'ch.name as name', 'ch.primary_coordinator_id as pc_id', 'fr.reviewer_id as reviewer_id',
                'cd.id as cord_id', 'cd.first_name as reviewer_first_name', 'cd.last_name as reviewer_last_name', 'st.state_short_name as state',
                'fr.award_1_nomination_type', 'fr.award_2_nomination_type', 'fr.award_3_nomination_type',
                'fr.award_4_nomination_type', 'fr.award_5_nomination_type', 'fr.check_award_1_approved as award_1_approved',
                'fr.check_award_2_approved as award_2_approved', 'fr.check_award_3_approved as award_3_approved',
                'fr.check_award_4_approved as award_4_approved', 'fr.check_award_5_approved as award_5_approved')
            ->join('state as st', 'ch.state', '=', 'st.id')
            ->leftJoin('financial_report as fr', function ($join) {
                $join->on('fr.chapter_id', '=', 'ch.id');
            })
            ->leftJoin('coordinators as cd', function ($join) {
                $join->on('cd.id', '=', 'fr.reviewer_id');
            })
            ->where('ch.is_active', 1)
            ->where(function ($query) {
                // Add a condition to filter chapters with at least one award
                $query->whereNotNull('fr.award_1_nomination_type')
                    ->orWhereNotNull('fr.award_2_nomination_type')
                    ->orWhereNotNull('fr.award_3_nomination_type')
                    ->orWhereNotNull('fr.award_4_nomination_type')
                    ->orWhereNotNull('fr.award_5_nomination_type');
            });

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('ch.conference', '=', $corConfId);
        } elseif ($positionId == 5) {
            $baseQuery->where('ch.region', '=', $corRegId);
        } else {
            $baseQuery->whereIn('ch.primary_coordinator_id', $reportIds);
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

        $chapterList = $baseQuery->get();

        $chapterList = $chapterList->toArray();
        $countList = count($chapterList);

        $data = ['corId' => $corId, 'countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('reports.chapteraward', $data);
    }

    /**
     * Add Chaper Awards -- i don't think we need this anymore
     */
    public function showAddAwards(Request $request): View
    {
        $user = $request->user();
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;
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

        if ($positionId == 5 || $positionId == 6 || $positionId == 25) {
            //Show Full Conference or Region
            $reportIdList = DB::table('coordinator_reporting_tree as crt')
                ->select('crt.id')
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

        $year = date('Y');

        $baseQuery = DB::table('chapters')
            ->select('chapters.*', 'rg.short_name as region', 'st.state_short_name as state', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname',
                'fr.award_1_nomination_type', 'fr.award_2_nomination_type', 'fr.award_3_nomination_type', 'fr.award_4_nomination_type', 'fr.award_5_nomination_type',
                'fr.check_award_1_approved as award_1_approved',
                'fr.check_award_2_approved as award_2_approved', 'fr.check_award_3_approved as award_3_approved',
                'fr.check_award_4_approved as award_4_approved', 'fr.check_award_5_approved as award_5_approved')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('financial_report as fr', 'chapters.id', '=', 'fr.chapter_id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('created_at', '<=', date('Y-06-30'));

        if ($positionId == 6 || $positionId == 25) {
            $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($positionId == 5) {
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

        // echo '<pre>';print_r($data);die;
        return view('reports.addawards')->with($data);
    }
}
