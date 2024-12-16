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
            ->select('ch.id', 'ch.name', 'ch.state_id', 'ch.ein', 'ch.primary_coordinator_id', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name',
                'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.phone as pre_phone', 'st.state_short_name as state',
                'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'cd.conference_id as cor_cid', 'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'ch.id')
            ->leftJoin('state as st', 'ch.state_id', '=', 'st.id')
            ->leftjoin('region as rg', 'ch.region_id', '=', 'rg.id')
            ->leftjoin('conference as cf', 'ch.conference_id', '=', 'cf.id')
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
            ->select('ch.id', 'ch.conference_id', 'ch.state_id', 'ch.name', 'ch.ein', 'ch.zap_date', 'ch.disband_reason', 'st.state_short_name as state',
                'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'ch.id')
            ->leftJoin('state as st', 'ch.state_id', '=', 'st.id')
            ->leftjoin('region as rg', 'ch.region_id', '=', 'rg.id')
            ->leftjoin('conference as cf', 'ch.conference_id', '=', 'cf.id')
            ->where('ch.is_active', '=', '0')
            ->where('bd.board_position_id', '=', '1')
            ->orderByDesc('ch.zap_date')
            ->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('international.intchapterzapped')->with($data);
    }

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
            ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
            ->leftJoin('conference as cf', 'chapters.conference_id', '=', 'cf.id')
            ->leftJoin('region as rg', 'chapters.region_id', '=', 'rg.id')
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
}
