<?php

namespace App\Http\Controllers;

use App\Enums\ChapterCheckbox;
use App\Models\Chapters;
use App\Models\Documents;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $checkBox4Status = $baseQuery['checkBox4Status'];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox4Status' => $checkBox4Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status,
        ];

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

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();

        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status,
        ];

        return view('chapreports.chaprpteinstatus')->with($data);
    }

    /**
     *Edit Chapter EIN Notes
     */
    public function editChapterIRS(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chConfId = $baseQuery['chConfId'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $startMonthName = $baseQuery['startMonthName'];
        $chActiveId = $baseQuery['chActiveId'];
        $chPcId = $baseQuery['chPcId'];
        $chDocuments = $baseQuery['chDocuments'];

        $data = ['id' => $id, 'chActiveId' => $chActiveId, 'conferenceDescription' => $conferenceDescription,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'startMonthName' => $startMonthName,
            'chPcId' => $chPcId, 'chDocuments' => $chDocuments, 'confId' => $confId, 'chConfId' => $chConfId,
        ];

        return view('chapreports.editirs')->with($data);
    }

    /**
     *Update Chapter EIN Notes
     */
    public function updateChapterIRS(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $chapter = Chapters::find($id);
        $documents = Documents::find($id);

        DB::beginTransaction();
        try {
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            $documents->ein_letter = $request->has('ch_ein_letter') ? 1 : 0;
            $documents->ein_notes = $request->input('ein_notes');
            $documents->irs_verified = $request->has('irs_verified') ? 1 : 0;
            $documents->ein_sent = $request->has('ein_sent') ? 1 : 0;
            $documents->irs_notes = $request->input('irs_notes');
            $documents->save();

            DB::commit();

            return to_route('chapters.editirs', ['id' => $id])->with('success', 'Chapter IRS Information has been updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('chapters.editirs', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
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

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
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
        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox3Status' => $checkBox3Status,
            'checkBox5Status' => $checkBox5Status,
        ];

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

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->whereHas('payments', function ($query) {
                $query->where('rereg_members', '>=', 75);
            })
            ->get();
        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox3Status' => $checkBox3Status,
            'checkBox5Status' => $checkBox5Status,
        ];

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

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where('status_id', '!=', 1)
            ->get();
        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox3Status' => $checkBox3Status,
            'checkBox5Status' => $checkBox5Status,
        ];

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

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

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
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status,
            'positionCodes' => ['BS', 'AC', 'SC', 'ARC', 'RC', 'ACC', 'CC'],
        ];

        return view('chapreports.chaprptcoordinators')->with($data);
    }
}
