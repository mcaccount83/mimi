<?php

namespace App\Http\Controllers;

use App\Services\PositionConditionsService;

class BaseConditionsController extends Controller
{
    protected $positionConditionsService;

    protected $userController;

    public function __construct(PositionConditionsService $positionConditionsService, UserController $userController)
    {
        $this->positionConditionsService = $positionConditionsService;
        $this->userController = $userController;
    }

    /**
     * Get conditions and reporting tree data based on coordinator position
     */
    public function getConditions($cdId, $cdPositionid, $cdSecPositionid)
    {
        $conditions = $this->positionConditionsService->getConditionsForUser($cdPositionid, $cdSecPositionid, $cdId);
        $inQryArr = [];

        if ($conditions['coordinatorCondition']) {
            $coordinatorData = $this->userController->loadReportingTree($cdId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        return ['conditions' => $conditions, 'inQryArr' => $inQryArr];
    }

    /**
     * Apply position-based conditions to the chapter query
     */
    public function applyPositionConditions($baseQuery, $conditions, $cdConfId, $cdRegId, $inQryArr)
{
    if ($conditions['founderCondition']) {
        // View Full International List - no filter
    } elseif ($conditions['assistConferenceCoordinatorCondition']) {
        $baseQuery->where('conference_id', '=', $cdConfId);
    } elseif ($conditions['regionalCoordinatorCondition']) {
        $baseQuery->where('region_id', '=', $cdRegId);
    } elseif ($conditions['inquiriesInternationalCondition'] || $conditions['ITCondition'] || $conditions['einCondition']) {
        // View Full International List - no filter
    } else {
        $baseQuery->whereIn('primary_coordinator_id', $inQryArr);
    }

    return $baseQuery;
}

    public function applyCordPositionConditions($baseQuery, $conditions, $cdConfId, $cdRegId, $inQryArr)
    {
        if ($conditions['founderCondition']) {
            // View Full International List - no filter
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('conference_id', '=', $cdConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('region_id', '=', $cdRegId);
        } elseif ($conditions['ITCondition']) {
            // View Full International List - no filter
        } else {
            $baseQuery->whereIn('report_id', $inQryArr);
        }

        return $baseQuery;
    }

}
