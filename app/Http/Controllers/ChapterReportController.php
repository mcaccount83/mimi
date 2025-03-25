<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ChapterReportController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseChapterController;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController)
    {

        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /**
     * Chpater Status Report
     */
    public function showRptChapterStatus(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox4Status = $baseQuery['checkBox4Status'];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox4Status' => $checkBox4Status];

        return view('chapreports.chaprptchapterstatus')->with($data);
    }

    /**
     * View the EIN Status -- Edit/Details and Update/Store are in ChapterController
     */
    public function showRptEINstatus(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('chapreports.chaprpteinstatus')->with($data);
    }

    /**
     * View the International EIN Status
     */
    public function showIntEINstatus(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);
        $chapterList = $baseQuery['query']->get();

        $data = ['chapterList' => $chapterList];

        return view('international.inteinstatus')->with($data);
    }

    /**
     * New Chapter Report
     */
    public function showRptNewChapters(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $now = Carbon::now();
        $oneYearAgo = $now->copy()->subYear();

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
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

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('chapreports.chaprptnewchapters')->with($data);
    }

    /**
     * Large Chapter Report
     */
    public function showRptLargeChapters(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where('members_paid_for', '>=', '75')
            ->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('chapreports.chaprptlargechapters')->with($data);
    }

    /**
     * Chapter Probation Report
     */
    public function showRptProbation(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where('status_id', '!=', 1)
            ->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('chapreports.chaprptprobation')->with($data);
    }

    /**
     * View the Chapter Coordinators List
     */
    public function showRptChapterCoordinators(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $chaptersData = $chapterList->map(function ($chapter) {
            $id = $chapter->primary_coordinator_id;
            $reportingList = DB::table('coordinator_reporting_tree')
                ->select('*')
                ->where('coordinator_id', $id)
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
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'chaptersData' => $chaptersData,
            'positionCodes' => ['BS', 'AC', 'SC', 'ARC', 'RC', 'ACC', 'CC'], ];

        return view('chapreports.chaprptcoordinators')->with($data);
    }
}
