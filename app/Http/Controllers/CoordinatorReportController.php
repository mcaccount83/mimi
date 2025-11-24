<?php

namespace App\Http\Controllers;

use App\Enums\CoordinatorCheckbox;
use App\Services\ReportingService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class CoordinatorReportController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseChapterController;

    protected $baseCoordinatorController;

    protected $reportingService;

    public function __construct(UserController $userController, BaseCoordinatorController $baseCoordinatorController, BaseChapterController $baseChapterController,
        ReportingService $reportingService)
    {
        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->baseCoordinatorController = $baseCoordinatorController;
        $this->reportingService = $reportingService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /**
     * View the Volunteer Utilization list
     */
    public function showRptVolUtilization(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $coordinatorList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery[CoordinatorCheckbox::CHECK_DIRECT];
        $checkBox3Status = $baseQuery[CoordinatorCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[CoordinatorCheckbox::CHECK_INTERNATIONAL];

        foreach ($coordinatorList as $list) {
            $cdCoorId = $list->id;
            $reportingData = $this->reportingService->calculateChapterReporting($cdCoorId);

            $list->direct_report = $reportingData['direct_report'];
            $list->indirect_report = $reportingData['indirect_report'];
            $list->total_report = $reportingData['total_report'];
        }

        $data = ['coordinatorList' => $coordinatorList, 'checkBoxStatus' => $checkBoxStatus,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status,
        ];

        return view('coordreports.coordrptvolutilization')->with($data);
    }

    /**
     * Coordiantor Appreciation List
     */
    public function showRptAppreciation(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $coordinatorList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery[CoordinatorCheckbox::CHECK_DIRECT];
        $checkBox3Status = $baseQuery[CoordinatorCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[CoordinatorCheckbox::CHECK_INTERNATIONAL];

        $data = ['coordinatorList' => $coordinatorList, 'checkBoxStatus' => $checkBoxStatus,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status,
        ];

        return view('coordreports.coordrptappreciation')->with($data);
    }

    public function showRptAppreciationOLD(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $coordinatorList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery[CoordinatorCheckbox::CHECK_DIRECT];
        $checkBox3Status = $baseQuery[CoordinatorCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[CoordinatorCheckbox::CHECK_INTERNATIONAL];

        $data = ['coordinatorList' => $coordinatorList, 'checkBoxStatus' => $checkBoxStatus,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status,
        ];

        return view('coordreports.old_coordrptappreciation')->with($data);
    }

    /**
     * View the Volunteer Birthday list
     */
    public function showRptBirthdays(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $coordinatorList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery[CoordinatorCheckbox::CHECK_DIRECT];
        $checkBox3Status = $baseQuery[CoordinatorCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[CoordinatorCheckbox::CHECK_INTERNATIONAL];

        $data = ['coordinatorList' => $coordinatorList, 'checkBoxStatus' => $checkBoxStatus,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status,
        ];

        return view('coordreports.coordrptbirthdays')->with($data);
    }

    /**
     * View the Reporting Tree
     */
    public function showRptReportingTree(Request $request): View
    {
        // Check if International's reporting tree checkbox is selected
        $showFullTree = $request->has(CoordinatorCheckbox::REPORTING_TREE) &&
                        $request->get(CoordinatorCheckbox::REPORTING_TREE) == 'yes';

        if ($showFullTree) {
            // Show International's full reporting tree based on Founder Data
            $coorId = 1;
            $confId = 0;
            $regId = 0;
            $positionId = 8;
            $secPositionId = null;
        } else {
            // Normal view - logged in user's tree
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['user_coorId'];
            $confId = $user['user_confId'];
            $regId = $user['user_regId'];
            $positionId = 6;
            $secPositionId = null;
            // $positionId = $user['user_positionId'];
            // $secPositionId = $user['user_secPositionId'];
        }

        $baseQuery = $this->baseCoordinatorController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $coordinatorList = $baseQuery['query']->get();

        $checkBox6Status = $showFullTree ? 'checked' : '';

        $data = ['coordinatorList' => $coordinatorList, 'checkBox6Status' => $checkBox6Status];

        return view('coordreports.coordrptreportingtree')->with($data);
    }
}
