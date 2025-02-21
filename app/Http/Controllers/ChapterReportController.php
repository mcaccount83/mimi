<?php

namespace App\Http\Controllers;

use App\Models\Chapters;
use App\Models\State;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ChapterReportController extends Controller
{
    protected $userController;
    protected $baseChapterController;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController)
    {
        $this->middleware('auth')->except('logout');
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class);
        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
    }

    /*/ Base Chapter Controller /*/
    //  $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    //  $this->baseChapterController->getChapterDetails($chId)

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

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox4Status = $baseQuery['checkBox4Status'];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox4Status' => $checkBox4Status,'corId' => $cdId];

        return view('chapreports.chaprptchapterstatus')->with($data);
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

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $cdId];

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
            ->orderBy('chapters.name');

        $chapterList = $baseQuery->get();

        $data = ['chapterList' => $chapterList, 'corId' => $cdId];

        return view('international.inteinstatus')->with($data);
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

        $now = Carbon::now();
        $oneYearAgo = $now->copy()->subYear();

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($oneYearAgo) {
                $query->where(function ($q) use ($oneYearAgo) {
                    $q->where('start_year', '>', $oneYearAgo->year)
                        ->orWhere(function ($q) use ($oneYearAgo) {
                            $q->where('start_year', '=', $oneYearAgo->year)
                                ->where('start_month_id', '>=', $oneYearAgo->month);
                        });
                });
            })
            ->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $cdId];

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

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']
            ->where('members_paid_for', '>=', '75')
            ->get();
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

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
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

            $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
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

}
