<?php

namespace App\Http\Controllers;

use App\Enums\CheckboxFilterEnum;
use App\Enums\OperatingStatusEnum;
use App\Models\Chapters;
use App\Models\Documents;
use App\Services\PositionConditionsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ChapterReportController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseChapterController;

    protected PositionConditionsService $positionConditionsService;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, PositionConditionsService $positionConditionsService)
    {

        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->positionConditionsService = $positionConditionsService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /**
     * Chapter Status Report
     */
    public function showRptChapterStatus(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $data = ['chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status,
        ];

        return view('coordinators.chapreports.chaprptchapterstatus')->with($data);
    }

    /**
     * View the EIN Status -- Edit/Details and Update/Store are in ChapterController
     */
    public function showRptEINstatus(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();

        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $data = ['chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status,
        ];

        return view('coordinators.chapreports.chaprpteinstatus')->with($data);
    }

    /**
     *Edit Chapter EIN Notes
     */
    public function editChapterIRS(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chConfId = $baseQuery['chConfId'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $baseQuery['chapterStatus'];
        $chActiveId = $baseQuery['chActiveId'];
        $chPcId = $baseQuery['chPcId'];
        $chDocuments = $baseQuery['chDocuments'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];

        $data = ['id' => $id, 'chActiveId' => $chActiveId, 'conferenceDescription' => $conferenceDescription, 'chEOYDocuments' => $chEOYDocuments,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'startMonthName' => $startMonthName,
            'chPcId' => $chPcId, 'chDocuments' => $chDocuments, 'confId' => $confId, 'chConfId' => $chConfId, 'chapterStatus' => $chapterStatus
        ];

        return view('coordinators.chapreports.editirs')->with($data);
    }

    /**
     *Update Chapter EIN Notes
     */
    public function updateChapterIRS(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $chapter = Chapters::find($id);
        $documents = Documents::find($id);

        DB::beginTransaction();
        try {
            $chapter->updated_by = $updatedBy;
            $chapter->updated_by = $updatedId;
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
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $dateOptions = $this->positionConditionsService->getDateOptions();
        $oneYearAgo = $dateOptions['oneYearAgo'];

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
        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $data = ['chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status, 'checkBox3Status' => $checkBox3Status,
            'checkBox51Status' => $checkBox51Status,
        ];

        return view('coordinators.chapreports.chaprptnewchapters')->with($data);
    }

    /**
     * Large Chapter Report
     */
    public function showRptLargeChapters(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->whereHas('payments', function ($query) {
                $query->where('rereg_members', '>=', 75);
            })
            ->get();
        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $data = ['chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status, 'checkBox3Status' => $checkBox3Status,
            'checkBox51Status' => $checkBox51Status,
        ];

        return view('coordinators.chapreports.chaprptlargechapters')->with($data);
    }

    /**
     * Chapter Probation Report
     */
    public function showRptProbation(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where('status_id', '!=', OperatingStatusEnum::OK)
            ->get();
        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $data = ['chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status, 'checkBox3Status' => $checkBox3Status,
            'checkBox51Status' => $checkBox51Status,
        ];

        return view('coordinators.chapreports.chaprptprobation')->with($data);
    }

    /**
     * View the Chapter Coordinators List
     */
    public function showRptChapterCoordinators(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

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
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status, 'chaptersData' => $chaptersData,
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status,
            'positionCodes' => ['BS', 'AC', 'SC', 'ARC', 'RC', 'ACC', 'CC'],
        ];

        return view('coordinators.chapreports.chaprptcoordinators')->with($data);
    }
}
