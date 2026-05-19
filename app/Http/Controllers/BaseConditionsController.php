<?php

namespace App\Http\Controllers;

use App\Services\PositionConditionsService;
use Illuminate\Database\Eloquent\Builder;

class BaseConditionsController extends Controller
{
    public function __construct(
        protected PositionConditionsService $positionConditionsService,
        protected UserController $userController,
    ) {}

    /**
     * Get conditions and reporting tree data based on coordinator position
     */
    public function getConditions(int $cdId, int $cdPositionid, array $cdSecPositionid)
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
    public function applyPositionConditions(Builder $baseQuery, array $conditions, int $cdConfId, int $cdRegId, array $inQryArr): Builder
    {
        if ($conditions['founderCondition']) {
            // View Full International List - no filter
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->whereHas('state', function ($query) use ($cdConfId) {
                $query->where('conference_id', '=', $cdConfId);
            });
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->whereHas('state', function ($query) use ($cdRegId) {
                $query->where('region_id', '=', $cdRegId);
            });
        } elseif ($conditions['inquiriesInternationalCondition'] || $conditions['ITCondition'] || $conditions['einCondition']) {
            // View Full International List - no filter
        } else {
            $baseQuery->whereIn('primary_coordinator_id', $inQryArr);
        }

        return $baseQuery;
    }

    public function applyCordPositionConditions(Builder $baseQuery, array $conditions, int $cdConfId, int $cdRegId, array $inQryArr): Builder
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
