<?php

namespace App\Services;

use App\Enums\CoordinatorPosition;
use App\Models\Admin;
use App\Models\AdminEmail;
use Illuminate\Support\Facades\Request;

class PositionConditionsService
{
    public function __construct(
        private ReportingService $reportingService
    ) {}

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
     * Get all position-based conditions for a user  // Loaded automatically for blades in ViewServiceProvider
     */
    public function getConditionsForUser($positionId, $secPositionId = [], $coorId = null)
    {
        // Handle null values gracefully
        $positionId = (int) ($positionId ?? 0);
        $secPositionId = array_map('intval', is_array($secPositionId) ? $secPositionId : [$secPositionId]);

        $hasChapterReports = $coorId ? $this->reportingService->hasAnyChapterReports($coorId) : false;
        $hasCoordinatorReports = $coorId ? $this->reportingService->hasAnyCoordinatorReports($coorId) : false;

        return [
            'coordinatorCondition' => $hasChapterReports,
            'supervisingCoordinatorCondition' => $hasCoordinatorReports,
            'founderCondition' => ($positionId == CoordinatorPosition::FOUNDER),
            'conferenceCoordinatorCondition' => ($positionId >= CoordinatorPosition::CC && $positionId <= CoordinatorPosition::FOUNDER),
            'assistConferenceCoordinatorCondition' => ($positionId >= CoordinatorPosition::ACC && $positionId <= CoordinatorPosition::FOUNDER),
            'regionalCoordinatorCondition' => ($positionId >= CoordinatorPosition::RC && $positionId <= CoordinatorPosition::FOUNDER),
            'assistRegionalCoordinatorCondition' => ($positionId >= CoordinatorPosition::ARC && $positionId <= CoordinatorPosition::FOUNDER),
            'areaCoordinatorCondition' => ($positionId >= CoordinatorPosition::AC && $positionId <= CoordinatorPosition::FOUNDER),
            'bigSisterCondition' => ($positionId >= CoordinatorPosition::BS && $positionId <= CoordinatorPosition::FOUNDER),
            'eoyTestCondition' => ($positionId >= CoordinatorPosition::ACC && $positionId <= CoordinatorPosition::FOUNDER) || $this->hasPosition(CoordinatorPosition::ART, $positionId, $secPositionId),
            'eoyReportCondition' => ($positionId >= CoordinatorPosition::BS && $positionId <= CoordinatorPosition::FOUNDER) || $this->hasPosition(CoordinatorPosition::ART, $positionId, $secPositionId) || $this->hasPosition(CoordinatorPosition::ARR, $positionId, $secPositionId),
            'eoyReportConditionDISABLED' => $this->hasPosition(CoordinatorPosition::IT, $positionId, $secPositionId),
            'inquiriesCondition' => $this->hasPosition(CoordinatorPosition::IC, $positionId, $secPositionId) || $this->hasPosition(CoordinatorPosition::IIC, $positionId, $secPositionId),
            'inquiriesInternationalCondition' => $this->hasPosition(CoordinatorPosition::IIC, $positionId, $secPositionId),
            'inquiriesConferenceCondition' => $this->hasPosition(CoordinatorPosition::IC, $positionId, $secPositionId),
            'webReviewCondition' => $this->hasPosition(CoordinatorPosition::WR, $positionId, $secPositionId),
            'einCondition' => $this->hasPosition(CoordinatorPosition::EIN, $positionId, $secPositionId),
            'm2mCondition' => $this->hasPosition(CoordinatorPosition::M2M, $positionId, $secPositionId) || $this->hasPosition(CoordinatorPosition::M2M2, $positionId, $secPositionId),
            'listAdminCondition' => $this->hasPosition(CoordinatorPosition::LIST, $positionId, $secPositionId),
            'ITCondition' => $this->hasPosition(CoordinatorPosition::IT, $positionId, $secPositionId),
        ];
    }

     /**
     * Check if user has a position (primary or secondary) // Helper function for getConditionsForUser
     */
    private function hasPosition(int $position, int $primaryPositionId, array $secondaryPositionIds): bool
    {
        return $primaryPositionId == $position || in_array($position, $secondaryPositionIds);
    }

    /**
     * Get EOY Display options/menus/buttons // Loaded automatically for blades in ViewServiceProvider
     */
    public function getEOYDisplay(): array
    {
        $admin = Admin::orderByDesc('id')
            ->limit(1)
            ->first();
        $display_testing = ($admin->display_testing == 1);
        $display_live = ($admin->display_live == 1);

        $currentMonth = now()->month;

        return [
            'displayTESTING' => ($display_testing == true && $display_live != true),
            'displayLIVE' => ($display_live == true && $currentMonth >= 5 && $currentMonth <= 12),
            'displayBoardRptLIVE' => ($display_live == true && $currentMonth >= 5 && $currentMonth <= 12),
            'displayFinancialRptLIVE' => ($display_live == true && $currentMonth >= 6 && $currentMonth <= 12),
            'displayEINInstructionsLIVE' => ($display_live == true && $currentMonth >= 7 && $currentMonth <= 12),
        ];
    }

    /**
     * Get Admin Email Addresses  // Called manually when needed
     */
    public function getAdminEmail(): array
    {
        $adminEmail = AdminEmail::first(); // Returns a single model instead of a collection

        $list_admin = $adminEmail->list_admin;
        $payments_admin = $adminEmail->payments_admin;
        $ein_admin = $adminEmail->ein_admin;
        $gsuite_admin = $adminEmail->gsuite_admin;
        $mimi_admin = $adminEmail->mimi_admin;

        return [
            'list_admin' => $list_admin,
            'payments_admin' => $payments_admin,
            'ein_admin' => $ein_admin,
            'gsuite_admin' => $gsuite_admin,
            'mimi_admin' => $mimi_admin,
        ];
    }

      /**
     * Get user admin status
     */
    // public function getUserAdmin(string $userAdmin): array
    // {
    //     return [
    //         'userAdmin' => ($userAdmin == '1'),
    //         'userModerator' => ($userAdmin == '2'),
    //     ];
    // }


    /**
     * Get user type flags // Used with ForumConditions and in forum main blade
     */
    // public function getUserType(string $userType): array
    // {
    //     return [
    //         'coordinator' => ($userType == 'coordinator'),  // Coordinator
    //         'board' => ($userType == 'board'),  // Current Board Member
    //         'outgoing' => $userType == 'outgoing',  // Outgoing Board Member
    //         'disbanded' => $userType == 'disbanded',  // Disbanded Chapter Board Member
    //     ];
    // }
}
