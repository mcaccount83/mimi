<?php

namespace App\Services;

class PositionConditionsService
{
    /**
     * Get all position-based conditions for a user
     *
     * @param int|null $positionId The primary position ID
     * @param array $secPositionId Array of secondary position IDs
     * @return array All position conditions
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
     * Get conditions for a specific use case
     *
     * @param array $conditions The full conditions array
     * @param string $type The type of conditions to get (e.g., 'inquiries', 'coord')
     * @return array Filtered conditions
     */
    // public function getConditionsForType(array $conditions, string $type)
    // {
    //     switch ($type) {
    //         case 'inquiries':
    //             return [
    //                 'founderCondition' => $conditions['founderCondition'],
    //                 'inquiriesInternationalCondition' => $conditions['inquiriesInternationalCondition'],
    //                 'assistConferenceCoordinatorCondition' => $conditions['assistConferenceCoordinatorCondition'],
    //                 'inquiriesConferenceCondition' => $conditions['inquiriesConferenceCondition'],
    //                 'regionalCoordinatorCondition' => $conditions['regionalCoordinatorCondition'],
    //             ];

    //         case 'coord':
    //             return [
    //                 'founderCondition' => $conditions['founderCondition'],
    //                 'conferenceCoordinatorCondition' => $conditions['conferenceCoordinatorCondition'],
    //                 'assistConferenceCoordinatorCondition' => $conditions['assistConferenceCoordinatorCondition'],
    //                 'regionalCoordinatorCondition' => $conditions['regionalCoordinatorCondition'],
    //                 'assistRegionalCoordinatorCondition' => $conditions['assistRegionalCoordinatorCondition'],
    //             ];

    //         default:
    //             return $conditions;
    //     }
    // }
}
