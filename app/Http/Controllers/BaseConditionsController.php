<?php

namespace App\Http\Controllers;

class BaseConditionsController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->userController = $userController;
    }

    /* /Custom Helpers/ */
    // $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);
    // $displayEOY = getEOYDisplay();

    /**
     * Get conditions and reporting tree data based on coordinator position
     */
    public function getConditions($cdId, $cdPositionid, $cdSecPositionid)
    {
        $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);
        $inQryArr = [];

        if ($conditions['coordinatorCondition']) {
            $coordinatorData = $this->userController->loadReportingTree($cdId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        return ['conditions' => $conditions, 'inQryArr' => $inQryArr];
    }

    /**
     * Apply position-based conditions to the query
     */
    public function applyPositionConditions($baseQuery, $conditions, $cdConfId, $cdRegId, $inQryArr)
    {
        if ($conditions['founderCondition']) {
            // No restrictions for founder
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('conference_id', '=', $cdConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('region_id', '=', $cdRegId);
        } else {
            $baseQuery->whereIn('primary_coordinator_id', $inQryArr);
        }

        return $baseQuery;
    }

    public function applyPositionCoordConditions($baseQuery, $conditions, $cdConfId, $cdRegId, $inQryArr)
    {
        if ($conditions['founderCondition']) {
            // No restrictions for founder
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('conference_id', '=', $cdConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('region_id', '=', $cdRegId);
        } else {
            $baseQuery->whereIn('report_id', $inQryArr);
        }

        return $baseQuery;
    }

    /**
     * Apply position-based inquiries conditions to the query
     */
    public function applyInquiriesPositionConditions($baseQuery, $conditions, $cdConfId, $cdRegId, $inQryArrrr)
    {
        if ($conditions['founderCondition'] || $conditions['inquiriesInternationalCondition']) {
        } elseif ($conditions['assistConferenceCoordinatorCondition'] || $conditions['inquiriesConferneceCondition']) {
            $baseQuery->where('conference_id', '=', $cdConfId);
        } else {
            $baseQuery->where('region_id', '=', $cdRegId);
        }

        return $baseQuery;
    }
}
