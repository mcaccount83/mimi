<?php

namespace App\Http\Controllers;

use App\Enums\UserStatusEnum;
use App\Enums\UserTypeEnum;
use App\Models\User;
use App\Services\PositionConditionsService;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller implements HasMiddleware
{
    public function __construct(
        protected UserController $userController,
        protected BaseChapterController $baseChapterController,
        protected BaseCoordinatorController $baseCoordinatorController,
        protected BaseUserController $baseUserController,
        protected PositionConditionsService $positionConditionsService,
        protected ExportService $exportService,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /**
     * Export Chapter List
     */
    public function indexChapter(Request $request)
    {
        return $this->downloadOrRedirect($this->exportService->generateChapters(
            $this->userController->loadUserInformation($request)
        ));
    }

    /**
     * Export Zapped Chapter List
     */
   public function indexZappedChapter(Request $request)
    {
        return $this->downloadOrRedirect($this->exportService->generateZappedChapters(
            $this->userController->loadUserInformation($request)
        ));
    }

    /**
     * Export International Chapter List
     */
    public function indexInternationalChapter(Request $request)
    {
        return $this->downloadOrRedirect($this->exportService->generateChapters(
            $this->userController->loadUserInformation($request), international: true
        ));
    }


    /**
     * Export International Zapped Chapter List
     */
    public function indexInternationalZapChapter(Request $request)
    {
        return $this->downloadOrRedirect($this->exportService->generateZappedChapters(
            $this->userController->loadUserInformation($request), international: true
        ));
    }

    /**
     * Export Overdue Re-Registration List - Optimized
     */
    public function indexReReg(Request $request)
    {
        return $this->downloadOrRedirect($this->exportService->generateReRegList(
            $this->userController->loadUserInformation($request)
        ));
    }

    /**
     * Export International Overdue Re-Registration List - Optimized
     */
    public function indexIntReReg(Request $request)
    {
        return $this->downloadOrRedirect($this->exportService->generateReRegList(
            $this->userController->loadUserInformation($request), international: true
        ));
    }

    /**
     * Export EIN Status List - Optimized
     */
    public function indexEINStatus(Request $request)
    {
        return $this->downloadOrRedirect($this->exportService->generateEINStatusList(
            $this->userController->loadUserInformation($request)
        ));
    }

    /**
     * Export International EIN Status List - Optimized
     */
    public function indexIntEINStatus(Request $request)
    {
        return $this->downloadOrRedirect($this->exportService->generateEINStatusList(
            $this->userController->loadUserInformation($request), international: true
        ));
    }

    /**
     * Export EOY Reports Status List
     */
    public function indexEOYStatus(Request $request)
    {
        return $this->downloadOrRedirect($this->exportService->generateEOYStatusList(
            $this->userController->loadUserInformation($request)
        ));
    }

    /**
     * Export International EOY Reports Status List
     */
    public function indexIntEOYStatus(Request $request)
    {
        return $this->downloadOrRedirect($this->exportService->generateEOYStatusList(
            $this->userController->loadUserInformation($request), international: true
        ));
    }

    /**
     * Export Coordinator List
     */
    public function indexCoordinator(Request $request)
    {
        return $this->downloadOrRedirect($this->exportService->generateCoordinators(
            $this->userController->loadUserInformation($request)
        ));
    }

    /**
     * Export International Coordinator List
     */
    public function indexIntCoordinator(Request $request)
    {
        return $this->downloadOrRedirect($this->exportService->generateCoordinators(
            $this->userController->loadUserInformation($request), international: true
        ));
    }

    /**
     * Export Retired Coordinator List
     */
    public function indexRetiredCoordinator(Request $request)
    {
        return $this->downloadOrRedirect($this->exportService->generateRetiredCoordinators(
            $this->userController->loadUserInformation($request)
        ));
    }

    /**
     * Export International Retired Coordinator List
     */
   public function indexIntRetCoordinator(Request $request)
    {
        return $this->downloadOrRedirect($this->exportService->generateRetiredCoordinators(
            $this->userController->loadUserInformation($request), international: true
        ));
    }

    /**
     * Export Coordinator Appreciation List
     */
    public function indexAppreciation(Request $request)
    {
        return $this->downloadOrRedirect($this->exportService->generateAppreciationList(
            $this->userController->loadUserInformation($request)
        ));
    }

    /**
     * Export Chapter Coordinator List
     */
    public function indexChapterCoordinator(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes

        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentDateYmd = $dateOptions['currentDateYmd'];

        $fileName = $currentDateYmd.'_chapter_coordinator_export.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();

        if ($chapterList->isEmpty()) {
            return redirect()->to('/home');
        }

        $positionCodes = ['CC', 'ACC', 'RC', 'ARC', 'SC', 'AC', 'BS'];

        if (count($chapterList) > 0) {
            $exportChapterList = [];

            foreach ($chapterList as $chapter) {
                $chId = $chapter->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];

                // Get coordinator reporting tree
                $reportingList = DB::table('coordinator_reporting_tree')
                    ->where('coordinator_id', $chapter->primary_coordinator_id)
                    ->first();

                // Filter and reverse reporting list
                $coordinatorIds = collect((array) $reportingList)
                    ->except(['id', 'layer0'])
                    ->reverse()
                    ->values();

                // Get coordinator details
                $coordinators = DB::table('coordinators as cd')
                    ->select('cd.id', 'cd.first_name', 'cd.last_name', 'cp.short_title as position')
                    ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                    ->whereIn('cd.id', $coordinatorIds)
                    ->get()
                    ->keyBy('position');

                // Initialize row data with basic chapter info
                $rowData = [
                    'Conference' => $chDetails->state->conference_id,
                    'Region' => $baseQuery['regionLongName'],
                    'State' => $baseQuery['stateShortName'],
                    'Chapter Name' => $chDetails->name,
                ];

                // Add coordinator positions to row data
                foreach ($positionCodes as $position) {
                    $coordinator = $coordinators->first(function ($coord) use ($position) {
                        return $coord->position == $position;
                    });

                    $rowData[$position] = $coordinator
                        ? "{$coordinator->first_name} {$coordinator->last_name}"
                        : '';
                }

                $exportChapterList[] = $rowData;
            }

            $callback = function () use ($exportChapterList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportChapterList)) {
                    fputcsv($file, array_keys($exportChapterList[0]));
                }

                foreach ($exportChapterList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export Constant Contact List
     */
    public function indexConstantContact(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentDateYmd = $dateOptions['currentDateYmd'];

        $fileName = $currentDateYmd.'_constant_contact_export.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Load user information like all other exports do
        $user = $this->userController->loadUserInformation($request);

        // Get user IDs that match the criteria
        $userIds = User::where('is_active', UserStatusEnum::ACTIVE)
            ->where(function ($query) {
                $query->where('type_id', UserTypeEnum::BOARD)
                    ->orWhere('type_id', UserTypeEnum::COORD);
            })
            ->where(function ($query) {
                $query->where('first_name', 'NOT LIKE', '%test%')
                    ->where('last_name', 'NOT LIKE', '%test%')
                    ->where('email', 'NOT LIKE', '%test%')
                    ->where('email', 'NOT LIKE', '%noemail%');
            })
            ->pluck('id')
            ->toArray();

        if (empty($userIds)) {
            return redirect()->to('/home');
        }

        $callback = function () use ($userIds) {
            $file = fopen('php://output', 'w');

            // Write headers
            $headers = ['First Name', 'Last Name', 'Email'];
            fputcsv($file, $headers);

            // Process users in chunks to manage memory
            $chunkSize = 100;
            $chunks = array_chunk($userIds, $chunkSize);

            foreach ($chunks as $chunkIndex => $chunk) {
                // Batch load users for this chunk
                $users = User::select('first_name', 'last_name', 'email')
                    ->whereIn('id', $chunk)
                    ->get();

                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->first_name,
                        $user->last_name,
                        $user->email,
                    ]);
                }

                // Clear memory after each chunk
                unset($users);

                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    protected function downloadOrRedirect(?string $path)
{
    if (! $path) {
        return redirect()->to('/home');
    }

    return response()->download($path, basename($path), [
        'Content-Type' => 'text/csv',
    ])->deleteFileAfterSend(true);
}
}
