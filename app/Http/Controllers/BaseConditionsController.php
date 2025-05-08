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
    $conditions = $this->positionConditionsService->getConditionsForUser($cdPositionid, $cdSecPositionid);
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
    } elseif ($conditions['assistConferenceCoordinatorCondition'] ) {
        $baseQuery->where('conference_id', '=', $cdConfId);
    } elseif ($conditions['regionalCoordinatorCondition']) {
        $baseQuery->where('region_id', '=', $cdRegId);
    } else {
        $baseQuery->whereIn('primary_coordinator_id', $inQryArr);
    }

    return $baseQuery;
}

/**
 * Apply position-based conditions to the query for pending chapters
 * This method handles chapters with active_status = 2 (pending) or 3 (not approved)
 */
public function applyPendingPositionConditions($baseQuery, $conditions, $cdConfId, $cdRegId, $inQryArr)
{
    // For pending chapters, the conditions are the same as regular active chapters
    // We just need to ensure we're using the correct board data sources
    if ($conditions['founderCondition']) {
        // No restrictions for founder
    } elseif ($conditions['assistConferenceCoordinatorCondition'] ) {
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
    if ($conditions['founderCondition'] ) {
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
 * Apply position coordinator conditions to the query for pending chapters
 * This method handles chapters with active_status = 2 (pending) or 3 (not approved)
 */
public function applyPendingPositionCoordConditions($baseQuery, $conditions, $cdConfId, $cdRegId, $inQryArr)
{
    // For pending chapters, the conditions are the same as regular active chapters
    // The difference is in how the relations are loaded in the query builder
    if ($conditions['founderCondition'] ) {
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
public function applyInquiriesPositionConditions($baseQuery, $conditions, $cdConfId, $cdRegId, $inQryArr)
{
    if ($conditions['founderCondition'] || $conditions['inquiriesInternationalCondition']) {
        // No restrictions for founder
    } elseif ($conditions['assistConferenceCoordinatorCondition'] || $conditions['inquiriesConferenceCondition']) {
        $baseQuery->where('conference_id', '=', $cdConfId);
    } else  {
        $baseQuery->where('region_id', '=', $cdRegId);
    }

    return $baseQuery;
}

/**
 * Apply position-based inquiries conditions to the query for pending chapters
 * This method handles chapters with active_status = 2 (pending) or 3 (not approved)
 */
public function applyPendingInquiriesPositionConditions($baseQuery, $conditions, $cdConfId, $cdRegId, $inQryArr)
{
    // For pending chapters, the inquiries conditions are the same as for active chapters
    // The difference is in how the relations are loaded in the query builder
    if ($conditions['founderCondition'] || $conditions['inquiriesInternationalCondition']) {
        // No restrictions for founder
    } elseif ($conditions['assistConferenceCoordinatorCondition'] || $conditions['inquiriesConferenceCondition']) {
        $baseQuery->where('conference_id', '=', $cdConfId);
    } else  {
        $baseQuery->where('region_id', '=', $cdRegId);
    }

    return $baseQuery;
}
}
