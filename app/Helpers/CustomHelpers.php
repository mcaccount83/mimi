<?php

use Illuminate\Support\Facades\Request;

if (! function_exists('getPositionConditions')) {
    function getPositionConditions($positionId, $secPositionId)
    {
        return [
            'ITCondition' => ($positionId == 13 || $secPositionId == 13),  // IT Coordinator
            'coordinatorCondition' => ($positionId >= 1 && $positionId <= 8),  // BS-Founder
            'founderCondition' => $positionId == 8,  // Founder
            'conferenceCoordinatorCondition' => ($positionId >= 7 && $positionId <= 8),  // CC-Founder
            'assistConferenceCoordinatorCondition' => ($positionId >= 6 && $positionId <= 8),  // ACC-Founder
            'regionalCoordinatorCondition' => ($positionId >= 5 && $positionId <= 8),  // RC-Founder
            'assistRegionalCoordinatorCondition' => ($positionId >= 4 && $positionId <= 8),  // ARC-Founder
            'supervisingCoordinatorCondition' => ($positionId >= 3 && $positionId <= 8),  // SC-Founder
            'areaCoordinatorCondition' => ($positionId >= 2 && $positionId <= 8),  // AC-Founder
            'bigSisterCondition' => ($positionId >= 1 && $positionId <= 8),  // BS-Founder
            'eoyTestCondition' => ($positionId >= 6 && $positionId <= 8) || ($positionId == 29 || $secPositionId == 29),
            'eoyReportCondition' => ($positionId >= 1 && $positionId <= 8) || ($positionId == 19 || $secPositionId == 19) || ($positionId == 29 || $secPositionId == 29),
            'eoyReportConditionDISABLED' => ($positionId == 13 || $secPositionId == 13),
            'inquiriesCondition' => ($positionId == 15 || $secPositionId == 15 || $positionId == 18 || $secPositionId == 18),
            'inquiriesInternationalCondition' => ($positionId == 18 || $secPositionId == 18),
            'inquiriesConferneceCondition' => ($positionId == 15 || $secPositionId == 15),
            'webReviewCondition' => ($positionId == 9 || $secPositionId == 9),
            'einCondition' => ($positionId == 12 || $secPositionId == 12),
            'adminReportCondition' => ($positionId == 13 || $secPositionId == 13),
            'm2mCondition' => ($positionId == 21 || $secPositionId == 21 || $positionId == 20 || $secPositionId == 20),
            'listAdminCondition' => ($positionId == 23 || $secPositionId == 23),
        ];
    }
}

if (! function_exists('isActiveRoute')) {
    function isActiveRoute(array $routes)
    {
        foreach ($routes as $route) {
            if (Request::is($route)) {
                return 'active';
            }
        }

        return '';
    }
}
