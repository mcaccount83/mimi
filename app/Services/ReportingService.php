<?php
// app/Services/ReportingService.php

namespace App\Services;

use App\Http\Controllers\UserController;
use App\Models\Chapters;
use App\Models\Coordinators;

class ReportingService
{
    public function __construct(
        private UserController $userController
    ) {}

    /**
     * Check if coordinator has any chapter reports (direct or indirect)
     */
    public function hasAnyChapterReports($coorId): bool
    {
        $reportingData = $this->calculateChapterReporting($coorId);

        return $reportingData['total_report'] > 0;
    }

    /**
     * Check if coordinator has any direct chapter reports
     */
    public function hasAnyDirectChapterReports($coorId): bool
    {
        $reportingData = $this->calculateChapterReporting($coorId);

        return $reportingData['direct_report'] > 0;
    }

    /**
     * Calculate Direct/Indirect chapter Reports
     */
    public function calculateChapterReporting($coorId)
    {
        // Direct chapter reports
        $direct_report = Chapters::where('active_status', 1)
            ->where('primary_coordinator_id', $coorId)
            ->count();

        // Indirect chapter reports
        $coordinatorData = $this->userController->loadReportingTree($coorId);
        $inQryArr = $coordinatorData['inQryArr'];
        $inQryArr = array_filter($inQryArr, fn ($id) => $id != $coorId);

        $indirect_report = Chapters::where('active_status', 1)
            ->whereIn('primary_coordinator_id', $inQryArr)
            ->count();

        $total_report = $direct_report + $indirect_report;

        return [
            'direct_report' => $direct_report,
            'indirect_report' => $indirect_report,
            'total_report' => $total_report,
        ];
    }

    /**
     * Check if coordinator has any coordinators reporting to them
     */
    public function hasAnyCoordinatorReports($coorId): bool
    {
        $reportingData = $this->calculateCoordinatorReporting($coorId);

        return $reportingData['total_report'] > 0;
    }

    /**
     * Calculate Direct/Indirect Coordinator Reports
     */
    public function calculateCoordinatorReporting($coorId)
    {
        // Get all coordinators in the reporting tree
        $coordinatorData = $this->userController->loadReportingTree($coorId);
        $inQryArr = $coordinatorData['inQryArr'];

        // Remove the coordinator themselves from the array
        $inQryArr = array_filter($inQryArr, fn ($id) => $id != $coorId);

        // Direct coordinator reports (layer immediately below)
        $cdDetails = Coordinators::find($coorId);
        $cdLayerId = $cdDetails->layer_id;
        $nextLayer = $cdLayerId + 1;

        $direct_report = Coordinators::where('active_status', 1)
            ->where('report_id', $coorId)
            ->where('layer_id', $nextLayer)
            ->count();

        // Indirect coordinator reports (everyone else in tree)
        $indirect_report = count($inQryArr) - $direct_report;

        $total_report = count($inQryArr);

        return [
            'direct_report' => $direct_report,
            'indirect_report' => $indirect_report,
            'total_report' => $total_report,
        ];
    }
}
