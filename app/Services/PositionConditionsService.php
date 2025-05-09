<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Support\Facades\Request;

class PositionConditionsService
{
    /**
     * Check if route is active
     */
    public function isActiveRoute(array $routes): string
    {
        foreach ($routes as $route) {
            if (Request::is($route)) {
                return 'active';
            }
        }

        return '';
    }

    /**
     * Get all position-based conditions for a user
     */
    public function getConditionsForUser($positionId, $secPositionId = [])
    {
        // Handle null values gracefully
        $positionId = (int) ($positionId ?? 0);
        $secPositionId = array_map('intval', is_array($secPositionId) ? $secPositionId : [$secPositionId]);

        return [
            'ITCondition' => ($positionId == 13 || in_array(13, $secPositionId)),
            'coordinatorCondition' => ($positionId >= 1 && $positionId <= 8),
            'founderCondition' => $positionId == 8,
            'conferenceCoordinatorCondition' => ($positionId >= 7 && $positionId <= 8),
            'assistConferenceCoordinatorCondition' => ($positionId >= 6 && $positionId <= 8),
            'regionalCoordinatorCondition' => ($positionId >= 5 && $positionId <= 8),
            'assistRegionalCoordinatorCondition' => ($positionId >= 4 && $positionId <= 8),
            'supervisingCoordinatorCondition' => ($positionId >= 3 && $positionId <= 8),
            'areaCoordinatorCondition' => ($positionId >= 2 && $positionId <= 8),
            'bigSisterCondition' => ($positionId >= 1 && $positionId <= 8),
            'eoyTestCondition' => ($positionId >= 6 && $positionId <= 8) || ($positionId == 29 || in_array(29, $secPositionId)),
            'eoyReportCondition' => ($positionId >= 1 && $positionId <= 8) || ($positionId == 19 || in_array(19, $secPositionId)) || ($positionId == 29 || in_array(29, $secPositionId)),
            'eoyReportConditionDISABLED' => ($positionId == 13 || in_array(13, $secPositionId)),
            'inquiriesCondition' => ($positionId == 15 || in_array(15, $secPositionId) || $positionId == 18 || in_array(18, $secPositionId)),
            'inquiriesInternationalCondition' => ($positionId == 18 || in_array(18, $secPositionId)),
            'inquiriesConferenceCondition' => ($positionId == 15 || in_array(15, $secPositionId)),
            'webReviewCondition' => ($positionId == 9 || in_array(9, $secPositionId)),
            'einCondition' => ($positionId == 12 || in_array(12, $secPositionId)),
            'm2mCondition' => ($positionId == 21 || in_array(21, $secPositionId) || $positionId == 20 || in_array(20, $secPositionId)),
            'listAdminCondition' => ($positionId == 23 || in_array(23, $secPositionId)),
        ];
    }

    /**
     * Get user admin status
     */
    public function getUserAdmin(string $userAdmin): array
    {
        return [
            'userAdmin' => ($userAdmin == '1'),
            'userModerator' => ($userAdmin == '2'),
        ];
    }

    /**
     * Get user type flags
     */
    public function getUserType(string $userType): array
    {
        return [
            'coordinator' => ($userType == 'coordinator'),  // Coordinator
            'board' => ($userType == 'board'),  // Current Board Member
            'outgoing' => $userType == 'outgoing',  // Outgoing Board Member
            'disbanded' => $userType == 'disbanded',  // Disbanded Chapter Board Member
        ];
    }

    /**
     * Get EOY display flags
     */
    public function getEOYDisplay(): array
    {
        $admin = Admin::orderByDesc('id')
            ->limit(1)
            ->first();
        $display_testing = ($admin->display_testing == 1);
        $display_live = ($admin->display_live == 1);

        return [
            'display_testing' => $display_testing,
            'display_live' => $display_live,
            'displayTESTING' => ($display_testing == true && $display_live != true),
            'displayLIVE' => ($display_live == true),
        ];
    }
}
