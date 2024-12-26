<?php

namespace App\Http\Controllers;

use App\Mail\PaymentsM2MChapterThankYou;
use App\Mail\PaymentsSustainingChapterThankYou;
use App\Models\Chapters;
use App\Models\State;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class);
        $this->userController = $userController;
    }

     /**
     * Chpater Reports Base Query
     */
     public function getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    {
        $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);

        $baseQuery = Chapters::with(['state', 'conference', 'region', 'status', 'startMonth', 'documents', 'primaryCoordinator'])
            ->where('is_active', 1);

        if ($conditions['founderCondition'] || $conditions['inquiriesInternationalCondition']) {
            } elseif ($conditions['assistConferenceCoordinatorCondition'] || $conditions['inquiriesConferneceCondition']) {
                $baseQuery->where('conference_id', '=', $cdConfId);
            } else {
                $baseQuery->where('region_id', '=', $cdRegId);
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
     * Chpater Status Report
     */
    public function showRptChapterStatus(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $cdId];

        return view('chapreports.chaprptchapterstatus')->with($data);
    }

    /**
     * View the Chapter Status Details
     */
    public function showChapterStatusView(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();

        $data = ['chapterList' => $chapterList, 'corId' => $cdId];

        return view('chapreports.chaprpteinstatus')->with($data);
    }

    /**
     * View the International EIN Status
     */
    public function showIntEINstatus(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $baseQuery = Chapters::with(['state', 'conference', 'region', 'status', 'startMonth', 'documents', 'primaryCoordinator'])
            ->where('is_active', 1);

        $baseQuery->orderBy(State::select('state_short_name')
            ->whereColumn('state.id', 'chapters.state_id'), 'asc')
            ->orderBy('chapters.name', 'asc');

        $chapterList = $baseQuery->get();

        $data = ['chapterList' => $chapterList, 'corId' => $cdId];

        return view('international.inteinstatus')->with($data);
    }

    /**
     * View EIN Status Details
     */
    public function showRptEINstatusView(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

        $chapterList = DB::table('chapters as ch')
            ->select('ch.id', 'ch.name', 'ch.ein', 'ch.ein_notes',
                'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.conference_id as cor_confid', 'cd.email as cor_email', 'bd.email as bor_email', 'st.state_short_name as statename')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'ch.id')
            ->leftJoin('state as st', 'ch.state_id', '=', 'st.id')
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
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $nextRenewalYear = $request->input('ch_nxt_renewalyear');

        //$nextRenewalYear = date('Y');
        $primaryCordEmail = $request->input('ch_pc_email');
        $boardPresEmail = $request->input('ch_pre_email');

        $chapter = Chapters::find($id);
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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $chapterList = $baseQuery['query']->get();

        $data = ['chapterList' => $chapterList, 'corId' => $cdId];

        return view('chapreports.chaprptnewchapters')->with($data);
    }

    /**
     * Large Chapter Report
     */
    public function showRptLargeChapters(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $cdId];

        return view('chapreports.chaprptlargechapters')->with($data);
    }

    /**
     * Chapter Probation Report
     */
    public function showRptProbation(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']
            ->where('status_id', '!=', 1)
            ->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $cdId];

        return view('chapreports.chaprptprobation')->with($data);
    }

    /**
     * View the Chapter Coordinators List
     */
    public function showRptChapterCoordinators(Request $request): View
    {
        try {
            $user = User::find($request->user()->id);
            $userId = $user->id;

            $cdDetails = $user->coordinator;
            $cdId = $cdDetails->id;
            $cdConfId = $cdDetails->conference_id;
            $cdRegId = $cdDetails->region_id;
            $cdPositionid = $cdDetails->position_id;
            $cdSecPositionid = $cdDetails->sec_position_id;

            $baseQuery = $this->getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
            $chapterList = $baseQuery['query']->get();
            $checkBoxStatus = $baseQuery['checkBoxStatus'];

            $chaptersData = $chapterList->map(function ($chapter) {
                $id = $chapter->primary_coordinator_id;
                $reportingList = DB::table('coordinator_reporting_tree')
                    ->select('*')
                    ->where('id', $id)
                    ->first();

                $filterReportingList = collect((array) $reportingList)
                    ->except(['id', 'layer0'])
                    ->reverse();

                $coordinatorArray = $filterReportingList->map(function ($val) {
                    return DB::table('coordinators as cd')
                        ->select('cd.first_name', 'cd.last_name', 'cp.short_title as position')
                        ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                        ->where('cd.id', $val)
                        ->first();
                });

                return [
                    'chapter' => $chapter,
                    'coordinatorArray' => $coordinatorArray->toArray(),
                ];
            });

            $countList = count($chapterList);
            $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $cdId, 'chaptersData' => $chaptersData,
                'positionCodes' => ['BS', 'AC', 'SC', 'ARC', 'RC', 'ACC', 'CC'], ];

            return view('chapreports.chaprptcoordinators')->with($data);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    // public function viewChaperReports(Request $request): View
    // {
    //     $corDetails = User::find($request->user()->id)->coordinator;
    //     $corId = $corDetails['id'];
    //     $corConfId = $corDetails['conference_id'];
    //     $corRegId = $corDetails['region_id'];
    //     $positionId = $corDetails['position_id'];
    //     $secPositionId = $corDetails['sec_position_id'];
    //     $request->session()->put('positionid', $positionId);
    //     $request->session()->put('secpositionid', $secPositionId);
    //     $request->session()->put('corconfid', $corConfId);
    //     $request->session()->put('corregid', $corRegId);

    //     // Get the conditions
    //     $conditions = getPositionConditions($positionId, $secPositionId);

    //     if ($conditions['coordinatorCondition']) {
    //         // Load Reporting Tree
    //         $coordinatorData = $this->userController->loadReportingTree($corId);
    //         $inQryArr = $coordinatorData['inQryArr'];
    //     }

    //     $status = [4, 5, 6];

    //     $baseQuery = DB::table('chapters')
    //         ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone',
    //             'st.state_short_name as state', 'rg.short_name as reg', 'cf.short_name as conf')
    //         ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
    //         ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
    //         ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
    //         ->leftJoin('conference as cf', 'chapters.conference_id', '=', 'cf.id')
    //         ->leftJoin('region as rg', 'chapters.region_id', '=', 'rg.id')
    //         ->where('chapters.is_active', '=', '1')
    //         ->where('bd.board_position_id', '=', '1')
    //         ->whereIn('chapters.status_id', $status);

    //     if ($conditions['founderCondition']) {

    //     } elseif ($conditions['assistConferenceCoordinatorCondition']) {
    //         $baseQuery->where('chapters.conference_id', '=', $corConfId);
    //     } elseif ($conditions['regionalCoordinatorCondition']) {
    //         $baseQuery->where('chapters.region_id', '=', $corRegId);
    //     } else {
    //         $baseQuery->whereIn('chapters.primary_coordinator_id', $inQryArr);
    //     }

    //     if (isset($_GET['check']) && $_GET['check'] == 'yes') {
    //         $checkBoxStatus = 'checked';
    //         $baseQuery->where('chapters.primary_coordinator_id', '=', $corId)
    //             ->orderBy('st.state_short_name')
    //             ->orderBy('chapters.name');
    //     } else {
    //         $checkBoxStatus = '';
    //         $baseQuery->orderByDesc('st.state_short_name')
    //             ->orderByDesc('chapters.name');
    //     }

    //     $chapterList = $baseQuery->get();

    //     $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

    //     return view('chapreports.view')->with($data);
    // }

}
