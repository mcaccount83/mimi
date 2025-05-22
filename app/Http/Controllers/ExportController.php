<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseChapterController;

    protected $baseCoordinatorController;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, BaseCoordinatorController $baseCoordinatorController)
    {

        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->baseCoordinatorController = $baseCoordinatorController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

   /**
     * Export Chapter List
     */
    public function indexChapter(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes

        $fileName = 'chapter_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

        // Get all chapter IDs first
        $chapterIds = $baseQuery['query']->pluck('id')->toArray();

        if (empty($chapterIds)) {
            return redirect()->to('/home');
        }

        $callback = function () use ($chapterIds) {
            $file = fopen('php://output', 'w');

            // Write headers
            $headers = [
                'Conference', 'Region', 'State', 'Name', 'Primary Coordinator', 'EIN',
                'Chapter Email', 'Chapter P.O. Box', 'Inquiries Email', 'Inquiries Notes',
                'Status', 'Notes', 'Bounraries', 'Pres Name', 'Pres Email', 'Pres Phone',
                'AVP Name', 'AVP Email', 'AVP Phone', 'MVP Name', 'MVP Email', 'MVP Phone',
                'Treasurer Name', 'Treasurer Email', 'Treasurer Phone', 'Secretary Name',
                'Secretary Email', 'Secretary Phone', 'Website', 'Linked Status', 'EGroup',
                'Social Media', 'Start Month', 'Start Year', 'Dues Last Paid',
                'Members paid for', 'NextRenewal', 'Founder', 'Sistered By', 'FormerName'
            ];
            fputcsv($file, $headers);

            // Process chapters in chunks to manage memory
            $chunkSize = 50; // Smaller chunks since each getChapterDetails call is heavy
            $chunks = array_chunk($chapterIds, $chunkSize);

            foreach ($chunks as $chunkIndex => $chunk) {
                foreach ($chunk as $chId) {
                    // Use your existing base controller method
                    $chapterData = $this->baseChapterController->getChapterDetails($chId);
                    $rowData = $this->formatChapterRowFromBaseController($chapterData);
                    fputcsv($file, $rowData);

                    // Clear memory periodically within chunks
                    if (($chunkIndex * $chunkSize + array_search($chId, $chunk)) % 10 === 0) {
                        if (ob_get_level()) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // Force garbage collection after each chunk
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                // Clear memory after each chunk
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export Zapped Chapter List
     */
    public function indexZappedChapter(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes

        $fileName = 'chapter_zap_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getZappedBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

        // Get all chapter IDs first
        $chapterIds = $baseQuery['query']->pluck('id')->toArray();

        if (empty($chapterIds)) {
            return redirect()->to('/home');
        }

        $callback = function () use ($chapterIds) {
            $file = fopen('php://output', 'w');

            // Write headers
            $headers = [
                'Conference', 'Region', 'State', 'Name', 'Primary Coordinator', 'EIN',
                'Chapter Email', 'Chapter P.O. Box', 'Inquiries Email', 'Inquiries Notes',
                'Status', 'Notes', 'Bounraries', 'Pres Name', 'Pres Email', 'Pres Phone',
                'AVP Name', 'AVP Email', 'AVP Phone', 'MVP Name', 'MVP Email', 'MVP Phone',
                'Treasurer Name', 'Treasurer Email', 'Treasurer Phone', 'Secretary Name',
                'Secretary Email', 'Secretary Phone', 'Website', 'Linked Status', 'EGroup',
                'Social Media', 'Start Month', 'Start Year', 'Dues Last Paid',
                'Members paid for', 'NextRenewal', 'Founder', 'Sistered By', 'FormerName',
                'Disband Date', 'Disband Reason'
            ];
            fputcsv($file, $headers);

            // Process chapters in chunks to manage memory
            $chunkSize = 50; // Smaller chunks since each getChapterDetails call is heavy
            $chunks = array_chunk($chapterIds, $chunkSize);

            foreach ($chunks as $chunkIndex => $chunk) {
                foreach ($chunk as $chId) {
                    // Use your existing base controller method
                    $chapterData = $this->baseChapterController->getChapterDetails($chId);
                    $rowData = $this->formatZappedChapterRowFromBaseController($chapterData);
                    fputcsv($file, $rowData);

                    // Clear memory periodically within chunks
                    if (($chunkIndex * $chunkSize + array_search($chId, $chunk)) % 10 === 0) {
                        if (ob_get_level()) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // Force garbage collection after each chunk
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                // Clear memory after each chunk
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export International Chapter List
     */
    public function indexInternationalChapter(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes

        $fileName = 'chapter_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

        // Get all chapter IDs first
        $chapterIds = $baseQuery['query']->pluck('id')->toArray();

        if (empty($chapterIds)) {
            return redirect()->to('/home');
        }

        $callback = function () use ($chapterIds) {
            $file = fopen('php://output', 'w');

            // Write headers
            $headers = [
                'Conference', 'Region', 'State', 'Name', 'Primary Coordinator', 'EIN',
                'Chapter Email', 'Chapter P.O. Box', 'Inquiries Email', 'Inquiries Notes',
                'Status', 'Notes', 'Bounraries', 'Pres Name', 'Pres Email', 'Pres Phone',
                'AVP Name', 'AVP Email', 'AVP Phone', 'MVP Name', 'MVP Email', 'MVP Phone',
                'Treasurer Name', 'Treasurer Email', 'Treasurer Phone', 'Secretary Name',
                'Secretary Email', 'Secretary Phone', 'Website', 'Linked Status', 'EGroup',
                'Social Media', 'Start Month', 'Start Year', 'Dues Last Paid',
                'Members paid for', 'NextRenewal', 'Founder', 'Sistered By', 'FormerName'
            ];
            fputcsv($file, $headers);

            // Process chapters in chunks to manage memory
            $chunkSize = 50; // Smaller chunks since each getChapterDetails call is heavy
            $chunks = array_chunk($chapterIds, $chunkSize);

            foreach ($chunks as $chunkIndex => $chunk) {
                foreach ($chunk as $chId) {
                    // Use your existing base controller method
                    $chapterData = $this->baseChapterController->getChapterDetails($chId);
                    $rowData = $this->formatChapterRowFromBaseController($chapterData);
                    fputcsv($file, $rowData);

                    // Clear memory periodically within chunks
                    if (($chunkIndex * $chunkSize + array_search($chId, $chunk)) % 10 === 0) {
                        if (ob_get_level()) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // Force garbage collection after each chunk
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                // Clear memory after each chunk
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export International Zapped Chapter List
     */
    public function indexInternationalZapChapter(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes

        $fileName = 'chapter_zap_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getZappedInternationalBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

        // Get all chapter IDs first
        $chapterIds = $baseQuery['query']->pluck('id')->toArray();

        if (empty($chapterIds)) {
            return redirect()->to('/home');
        }

        $callback = function () use ($chapterIds) {
            $file = fopen('php://output', 'w');

            // Write headers
            $headers = [
                'Conference', 'Region', 'State', 'Name', 'Primary Coordinator', 'EIN',
                'Chapter Email', 'Chapter P.O. Box', 'Inquiries Email', 'Inquiries Notes',
                'Status', 'Notes', 'Bounraries', 'Pres Name', 'Pres Email', 'Pres Phone',
                'AVP Name', 'AVP Email', 'AVP Phone', 'MVP Name', 'MVP Email', 'MVP Phone',
                'Treasurer Name', 'Treasurer Email', 'Treasurer Phone', 'Secretary Name',
                'Secretary Email', 'Secretary Phone', 'Website', 'Linked Status', 'EGroup',
                'Social Media', 'Start Month', 'Start Year', 'Dues Last Paid',
                'Members paid for', 'NextRenewal', 'Founder', 'Sistered By', 'FormerName',
                'Disband Date', 'Disband Reason'
            ];
            fputcsv($file, $headers);

            // Process chapters in chunks to manage memory
            $chunkSize = 50; // Smaller chunks since each getChapterDetails call is heavy
            $chunks = array_chunk($chapterIds, $chunkSize);

            foreach ($chunks as $chunkIndex => $chunk) {
                foreach ($chunk as $chId) {
                    // Use your existing base controller method
                    $chapterData = $this->baseChapterController->getChapterDetails($chId);
                    $rowData = $this->formatZappedChapterRowFromBaseController($chapterData);
                    fputcsv($file, $rowData);

                    // Clear memory periodically within chunks
                    if (($chunkIndex * $chunkSize + array_search($chId, $chunk)) % 10 === 0) {
                        if (ob_get_level()) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // Force garbage collection after each chunk
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                // Clear memory after each chunk
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Format chapter row data using existing base controller response
     */
    private function formatChapterRowFromBaseController($chapterData)
    {
        $chDetails = $chapterData['chDetails'];
        $stateShortName = $chapterData['stateShortName'];
        $regionLongName = $chapterData['regionLongName'];
        $chConfId = $chapterData['chConfId'];
        $pcName = $chapterData['pcName'];
        $startMonthName = $chapterData['startMonthName'];
        $chapterStatus = $chapterData['chapterStatus'];
        $websiteLink = $chapterData['websiteLink'];
        $PresDetails = $chapterData['PresDetails'];
        $AVPDetails = $chapterData['AVPDetails'];
        $MVPDetails = $chapterData['MVPDetails'];
        $TRSDetails = $chapterData['TRSDetails'];
        $SECDetails = $chapterData['SECDetails'];

        return [
            'Conference' => $chConfId,
            'Region' => $regionLongName,
            'State' => $stateShortName,
            'Name' => $chDetails->name,
            'Primary Coordinator' => $pcName,
            'EIN' => $chDetails->ein,
            'Chapter Email' => $chDetails->email,
            'Chapter P.O. Box' => $chDetails->po_box,
            'Inquiries Email' => $chDetails->inquiries_contact,
            'Inquiries Notes' => $chDetails->inquiries_note,
            'Status' => $chapterStatus,
            'Notes' => $chDetails->notes,
            'Bounraries' => $chDetails->territory,
            'Pres Name' => ($PresDetails && $PresDetails->first_name) ? $PresDetails->first_name.' '.$PresDetails->last_name : '',
            'Pres Email' => $PresDetails->email ?? '',
            'Pres Phone' => $PresDetails->phone ?? '',
            'AVP Name' => ($AVPDetails && $AVPDetails->first_name) ? $AVPDetails->first_name.' '.$AVPDetails->last_name : '',
            'AVP Email' => $AVPDetails->email ?? '',
            'AVP Phone' => $AVPDetails->phone ?? '',
            'MVP Name' => ($MVPDetails && $MVPDetails->first_name) ? $MVPDetails->first_name.' '.$MVPDetails->last_name : '',
            'MVP Email' => $MVPDetails->email ?? '',
            'MVP Phone' => $MVPDetails->phone ?? '',
            'Treasurer Name' => ($TRSDetails && $TRSDetails->first_name) ? $TRSDetails->first_name.' '.$TRSDetails->last_name : '',
            'Treasurer Email' => $TRSDetails->email ?? '',
            'Treasurer Phone' => $TRSDetails->phone ?? '',
            'Secretary Name' => ($SECDetails && $SECDetails->first_name) ? $SECDetails->first_name.' '.$SECDetails->last_name : '',
            'Secretary Email' => $SECDetails->email ?? '',
            'Secretary Phone' => $SECDetails->phone ?? '',
            'Website' => $websiteLink,
            'Linked Status' => $chDetails->website_status,
            'EGroup' => $chDetails->egroup,
            'Social Media' => trim(($chDetails->social1 ?? '').' '.($chDetails->social2 ?? '').' '.($chDetails->social3 ?? '')),
            'Start Month' => $startMonthName,
            'Start Year' => $chDetails->start_year,
            'Dues Last Paid' => $chDetails->dues_last_paid,
            'Members paid for' => $chDetails->members_paid_for,
            'NextRenewal' => $chDetails->next_renewal_year,
            'Founder' => $chDetails->founders_name,
            'Sistered By' => $chDetails->sistered_by,
            'FormerName' => $chDetails->former_name,
        ];
    }

    /**
     * Format zapped chapter row data using existing base controller response
     */
    private function formatZappedChapterRowFromBaseController($chapterData)
    {
        // Get the base chapter row data
        $rowData = $this->formatChapterRowFromBaseController($chapterData);

        // Override officer details with disbanded versions
        $PresDetails = $chapterData['PresDisbandedDetails'];
        $AVPDetails = $chapterData['AVPDisbandedDetails'];
        $MVPDetails = $chapterData['MVPDisbandedDetails'];
        $TRSDetails = $chapterData['TRSDisbandedDetails'];
        $SECDetails = $chapterData['SECDisbandedDetails'];

        // Update officer information with disbanded details
        $rowData['Pres Name'] = ($PresDetails && $PresDetails->first_name) ? $PresDetails->first_name.' '.$PresDetails->last_name : '';
        $rowData['Pres Email'] = $PresDetails->email ?? '';
        $rowData['Pres Phone'] = $PresDetails->phone ?? '';
        $rowData['AVP Name'] = ($AVPDetails && $AVPDetails->first_name) ? $AVPDetails->first_name.' '.$AVPDetails->last_name : '';
        $rowData['AVP Email'] = $AVPDetails->email ?? '';
        $rowData['AVP Phone'] = $AVPDetails->phone ?? '';
        $rowData['MVP Name'] = ($MVPDetails && $MVPDetails->first_name) ? $MVPDetails->first_name.' '.$MVPDetails->last_name : '';
        $rowData['MVP Email'] = $MVPDetails->email ?? '';
        $rowData['MVP Phone'] = $MVPDetails->phone ?? '';
        $rowData['Treasurer Name'] = ($TRSDetails && $TRSDetails->first_name) ? $TRSDetails->first_name.' '.$TRSDetails->last_name : '';
        $rowData['Treasurer Email'] = $TRSDetails->email ?? '';
        $rowData['Treasurer Phone'] = $TRSDetails->phone ?? '';
        $rowData['Secretary Name'] = ($SECDetails && $SECDetails->first_name) ? $SECDetails->first_name.' '.$SECDetails->last_name : '';
        $rowData['Secretary Email'] = $SECDetails->email ?? '';
        $rowData['Secretary Phone'] = $SECDetails->phone ?? '';

        // Add zapped-specific fields
        $chDetails = $chapterData['chDetails'];
        $rowData['Disband Date'] = $chDetails->zap_date;
        $rowData['Disband Reason'] = $chDetails->disband_reason;

        return $rowData;
    }

    /**
     * Export Coordinator List
     */
    public function indexCoordinator(Request $request)
    {
        $fileName = 'coordinator_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $coordinatorList = $baseQuery['query']->get();

        if (count($coordinatorList) > 0) {
            $exportCoordinatorList = [];

            foreach ($coordinatorList as $list) {
                $cdId = $list->id;
                $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                $cdDetails = $baseQuery['cdDetails'];
                $cdId = $baseQuery['cdId'];
                $regionLongName = $baseQuery['regionLongName'];
                $cdConfId = $baseQuery['cdConfId'];
                $RptFName = $baseQuery['RptFName'];
                $RptLName = $baseQuery['RptLName'];
                $ReportTo = $RptFName.' '.$RptLName;
                $displayPosition = $baseQuery['displayPosition'];
                $secondaryPosition = $baseQuery['secondaryPosition'];
                $cdAdminRole = $baseQuery['cdAdminRole'];

                $secPositionValue = null;

                // Check what type of data $secondaryPosition is
                if ($secondaryPosition) {
                    if (is_object($secondaryPosition)) {
                        // If it's a single object
                        $secPositionValue = $secondaryPosition->long_title ?? null;
                    } elseif (is_array($secondaryPosition) || $secondaryPosition instanceof \Traversable) {
                        // If it's a collection or array
                        $secondaryPositionTitles = [];
                        foreach ($secondaryPosition as $position) {
                            if (is_object($position)) {
                                $secondaryPositionTitles[] = $position->long_title ?? '';
                            } elseif (is_string($position)) {
                                $secondaryPositionTitles[] = $position;
                            }
                        }
                        $secPositionValue = ! empty($secondaryPositionTitles) ? implode(', ', $secondaryPositionTitles) : null;
                    } elseif (is_string($secondaryPosition)) {
                        // If it's already a string
                        $secPositionValue = $secondaryPosition;
                    }
                }

                $rowData = [
                    'Conference' => $cdConfId,
                    'Region' => $regionLongName,
                    'Coordinator Name' => $cdDetails->first_name.' '.$cdDetails->last_name,
                    'Position' => $displayPosition->long_title,
                    'Sec Position' => $secPositionValue,
                    'Admin' => $cdAdminRole->admin_role,
                    'Email' => $cdDetails->email,
                    'Email2' => $cdDetails->sec_email,
                    'Report To' => $ReportTo,
                    'Address' => $cdDetails->address,
                    'City' => $cdDetails->city,
                    'State' => $cdDetails->state_id,
                    'Zip' => $cdDetails->zip,
                    'Phone' => $cdDetails->phone,
                    'Phone2' => $cdDetails->alt_phone,
                    'Birthday' => $cdDetails->birthday_month_id.' / '.$cdDetails->birthday_day,
                    'Coordinator Start' => $cdDetails->coordinator_start_date,
                    'Last Promoted' => $cdDetails->last_promoted,
                    'Leave of Absense' => ($cdDetails->on_leave == 1) ? 'YES' : 'NO',
                    'Leave Date' => $cdDetails->leave_date,
                    'Last UpdatedBy' => $cdDetails->last_updated_by,
                    'Last UpdatedDate' => $cdDetails->last_updated_date,
                ];

                $exportCoordinatorList[] = $rowData;
            }

            $callback = function () use ($exportCoordinatorList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportCoordinatorList)) {
                    fputcsv($file, array_keys($exportCoordinatorList[0]));
                }

                foreach ($exportCoordinatorList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export Retired Coordinator List
     */
    public function indexRetiredCoordinator(Request $request)
    {
        $fileName = 'coordinator_retire_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getRetiredBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $coordinatorList = $baseQuery['query']->get();

        if (count($coordinatorList) > 0) {
            $exportCoordinatorList = [];

            foreach ($coordinatorList as $list) {
                $cdId = $list->id;
                $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                $cdDetails = $baseQuery['cdDetails'];
                $cdId = $baseQuery['cdId'];
                $regionLongName = $baseQuery['regionLongName'];
                $cdConfId = $baseQuery['cdConfId'];
                $RptFName = $baseQuery['RptFName'];
                $RptLName = $baseQuery['RptLName'];
                $ReportTo = $RptFName.' '.$RptLName;
                $displayPosition = $baseQuery['displayPosition'];
                $secondaryPosition = $baseQuery['secondaryPosition'];
                $cdAdminRole = $baseQuery['cdAdminRole'];

                $secPositionValue = null;

                // Check what type of data $secondaryPosition is
                if ($secondaryPosition) {
                    if (is_object($secondaryPosition)) {
                        // If it's a single object
                        $secPositionValue = $secondaryPosition->long_title ?? null;
                    } elseif (is_array($secondaryPosition) || $secondaryPosition instanceof \Traversable) {
                        // If it's a collection or array
                        $secondaryPositionTitles = [];
                        foreach ($secondaryPosition as $position) {
                            if (is_object($position)) {
                                $secondaryPositionTitles[] = $position->long_title ?? '';
                            } elseif (is_string($position)) {
                                $secondaryPositionTitles[] = $position;
                            }
                        }
                        $secPositionValue = ! empty($secondaryPositionTitles) ? implode(', ', $secondaryPositionTitles) : null;
                    } elseif (is_string($secondaryPosition)) {
                        // If it's already a string
                        $secPositionValue = $secondaryPosition;
                    }
                }

                $rowData = [
                    'Conference' => $cdConfId,
                    'Region' => $regionLongName,
                    'Coordinator Name' => $cdDetails->first_name.' '.$cdDetails->last_name,
                    'Position' => $displayPosition->long_title,
                    'Sec Position' => $secPositionValue,
                    'Admin' => $cdAdminRole->admin_role,
                    'Email' => $cdDetails->email,
                    'Email2' => $cdDetails->sec_email,
                    'Report To' => $ReportTo,
                    'Address' => $cdDetails->address,
                    'City' => $cdDetails->city,
                    'State' => $cdDetails->state_id,
                    'Zip' => $cdDetails->zip,
                    'Phone' => $cdDetails->phone,
                    'Phone2' => $cdDetails->alt_phone,
                    'Birthday' => $cdDetails->birthday_month_id.' / '.$cdDetails->birthday_day,
                    'Coordinator Start' => $cdDetails->coordinator_start_date,
                    'Last Promoted' => $cdDetails->last_promoted,
                    'Leave of Absense' => ($cdDetails->on_leave == 1) ? 'YES' : 'NO',
                    'Leave Date' => $cdDetails->leave_date,
                    'Retire Date' => $cdDetails->zapped_date,
                    'Reason' => $cdDetails->reason_retired,
                    'Last UpdatedBy' => $cdDetails->last_updated_by,
                    'Last UpdatedDate' => $cdDetails->last_updated_date,
                ];

                $exportCoordinatorList[] = $rowData;
            }

            $callback = function () use ($exportCoordinatorList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportCoordinatorList)) {
                    fputcsv($file, array_keys($exportCoordinatorList[0]));
                }

                foreach ($exportCoordinatorList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export Coordinator Appreciation List
     */
    public function indexAppreciation(Request $request)
    {
        $fileName = 'coordinator_appreciation_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $coordinatorList = $baseQuery['query']->get();

        if (count($coordinatorList) > 0) {
            $exportCoordinatorList = [];

            foreach ($coordinatorList as $list) {
                $cdId = $list->id;
                $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                $cdDetails = $baseQuery['cdDetails'];
                $cdId = $baseQuery['cdId'];
                $regionLongName = $baseQuery['regionLongName'];
                $cdConfId = $baseQuery['cdConfId'];
                $RptFName = $baseQuery['RptFName'];
                $RptLName = $baseQuery['RptLName'];
                $ReportTo = $RptFName.' '.$RptLName;
                $displayPosition = $baseQuery['displayPosition'];
                $secondaryPosition = $baseQuery['secondaryPosition'];
                $necklace = $cdDetails->recognition_necklace;

                $secPositionValue = null;

                // Check what type of data $secondaryPosition is
                if ($secondaryPosition) {
                    if (is_object($secondaryPosition)) {
                        // If it's a single object
                        $secPositionValue = $secondaryPosition->long_title ?? null;
                    } elseif (is_array($secondaryPosition) || $secondaryPosition instanceof \Traversable) {
                        // If it's a collection or array
                        $secondaryPositionTitles = [];
                        foreach ($secondaryPosition as $position) {
                            if (is_object($position)) {
                                $secondaryPositionTitles[] = $position->long_title ?? '';
                            } elseif (is_string($position)) {
                                $secondaryPositionTitles[] = $position;
                            }
                        }
                        $secPositionValue = ! empty($secondaryPositionTitles) ? implode(', ', $secondaryPositionTitles) : null;
                    } elseif (is_string($secondaryPosition)) {
                        // If it's already a string
                        $secPositionValue = $secondaryPosition;
                    }
                }

                $rowData = [
                    'Conference' => $cdConfId,
                    'Region' => $regionLongName,
                    'Coordinator Name' => $cdDetails->first_name.' '.$cdDetails->last_name,
                    'Position' => $displayPosition->long_title,
                    'Sec Position' => $secPositionValue,
                    'Start Date' => $cdDetails->coordinator_start_date,
                    '<1 Year' => $cdDetails->recognition_year0,
                    '1 Year' => $cdDetails->recognition_year1,
                    '2 Years' => $cdDetails->recognition_year2,
                    '3 Years' => $cdDetails->recognition_year3,
                    '4 Years' => $cdDetails->recognition_year4,
                    '5 Years' => $cdDetails->recognition_year5,
                    '6 Years' => $cdDetails->recognition_year6,
                    '7 Years' => $cdDetails->recognition_year7,
                    '8 Years' => $cdDetails->recognition_year8,
                    '9 Years' => $cdDetails->recognition_year9,
                    'Necklace' => ($necklace == 1) ? 'YES' : 'NO',
                    'Top Tier/Other' => $cdDetails->recognition_toptier,
                    'Leave of Absense' => ($cdDetails->on_leave == 1) ? 'YES' : 'NO',
                    'Leave Date' => $cdDetails->last_updated_date,
                ];

                $exportCoordinatorList[] = $rowData;
            }

            $callback = function () use ($exportCoordinatorList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportCoordinatorList)) {
                    fputcsv($file, array_keys($exportCoordinatorList[0]));
                }

                foreach ($exportCoordinatorList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export Chapter Coordinator List
     */
    public function indexChapterCoordinator(Request $request)
    {
        $fileName = 'chapter_coordinator_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();

        if ($chapterList->isEmpty()) {
            return redirect()->to('/home');
        }

        $positionCodes = ['CC', 'ACC', 'RC', 'ARC', 'SC', 'AC', 'BS'];

        if (count($chapterList) > 0) {
            $exportChapterList = [];

            foreach ($chapterList as $chapter) {
                $chId = $chapter->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];

                // Get coordinator reporting tree
                $reportingList = DB::table('coordinator_reporting_tree')
                    ->where('coordinator_id', $chapter->primary_coordinator_id)
                    ->first();

                // Filter and reverse reporting list
                $coordinatorIds = collect((array) $reportingList)
                    ->except(['id', 'layer0'])
                    ->reverse()
                    ->values();

                // Get coordinator details
                $coordinators = DB::table('coordinators as cd')
                    ->select('cd.id', 'cd.first_name', 'cd.last_name', 'cp.short_title as position')
                    ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                    ->whereIn('cd.id', $coordinatorIds)
                    ->get()
                    ->keyBy('position');

                // Initialize row data with basic chapter info
                $rowData = [
                    'Conference' => $chDetails->conference_id,
                    'Region' => $baseQuery['regionLongName'],
                    'State' => $baseQuery['stateShortName'],
                    'Chapter Name' => $chDetails->name,
                ];

                // Add coordinator positions to row data
                foreach ($positionCodes as $position) {
                    $coordinator = $coordinators->first(function ($coord) use ($position) {
                        return $coord->position === $position;
                    });

                    $rowData[$position] = $coordinator
                        ? "{$coordinator->first_name} {$coordinator->last_name}"
                        : '';
                }

                $exportChapterList[] = $rowData;
            }

            $callback = function () use ($exportChapterList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportChapterList)) {
                    fputcsv($file, array_keys($exportChapterList[0]));
                }

                foreach ($exportChapterList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export Overdue Re-Registration List
     */
    public function indexReReg(Request $request)
    {
        $fileName = 'rereg_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $now = Carbon::now();
        $currentMonth = $now->month;
        $lastMonth = $now->copy()->subMonth()->format('m');
        $currentYear = $now->year;

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

        $reChapterList = $baseQuery['query']
            ->where(function ($query) use ($currentYear, $lastMonth) {
                $query->where('next_renewal_year', '<', $currentYear)
                    ->orWhere(function ($query) use ($currentYear, $lastMonth) {
                        $query->where('next_renewal_year', '=', $currentYear)
                            ->where('start_month_id', '<=', $lastMonth);
                    });
            })
            ->orderByDesc('start_month_id')
            ->orderByDesc('next_renewal_year')
            ->get();

        if (count($reChapterList) >= 0) {
            $exportReRegList = [];

            foreach ($reChapterList as $list) {
                $chId = $list->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];
                $chId = $baseQuery['chId'];
                $stateShortName = $baseQuery['stateShortName'];
                $regionLongName = $baseQuery['regionLongName'];
                $chConfId = $baseQuery['chConfId'];
                $pcName = $baseQuery['pcName'];
                $startMonthName = $baseQuery['startMonthName'];
                $chapterStatus = $baseQuery['chapterStatus'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'Primary Coordinator' => $pcName,
                    'Status' => $chapterStatus,
                    'Notes' => $chDetails->notes,
                    'Month Due' => $startMonthName,
                    'Year Due' => $chDetails->next_renewal_year,
                    'Re-Reg Notes' => $chDetails->reg_notes,
                    'Dues Last Paid' => $chDetails->dues_last_paid,
                    'Members paid for' => $chDetails->members_paid_for,
                ];

                $exportReRegList[] = $rowData;
            }

            $callback = function () use ($exportReRegList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportReRegList)) {
                    fputcsv($file, array_keys($exportReRegList[0]));
                }

                foreach ($exportReRegList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export EIN Status List
     */
    public function indexEINStatus(Request $request)
    {
        $fileName = 'ein_status_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();

        if (count($chapterList) > 0) {
            $exportChapterList = [];

            foreach ($chapterList as $list) {
                $chId = $list->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];
                $chId = $baseQuery['chId'];
                $stateShortName = $baseQuery['stateShortName'];
                $regionLongName = $baseQuery['regionLongName'];
                $chConfId = $baseQuery['chConfId'];
                $startMonthName = $baseQuery['startMonthName'];
                $chDocuments = $baseQuery['chDocuments'];
                $PresDetails = $baseQuery['PresDetails'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'EIN' => $chDetails->ein,
                    'EIN Letter' => ($chDocuments->ein_letter == 1) ? 'YES' : 'NO',
                    'Start Month' => $startMonthName,
                    'Start Year' => $chDetails->start_year,
                    'Pres Name' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'Pres Address' => $PresDetails->street_address,
                    'Pres City' => $PresDetails->city,
                    'Pres State' => $PresDetails->state_id,
                    'Pres Zip' => $PresDetails->zip,
                    'Pres Phone' => $PresDetails->phone,
                    'Pres Email' => $PresDetails->email,
                ];

                $exportChapterList[] = $rowData;
            }

            $callback = function () use ($exportChapterList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportChapterList)) {
                    fputcsv($file, array_keys($exportChapterList[0]));
                }

                foreach ($exportChapterList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export EOY Reports Status List
     */
    public function indexEOYStatus(Request $request)
    {
        $fileName = 'eoy_status_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();

        if (count($chapterList) > 0) {
            $exportChapterList = [];

            foreach ($chapterList as $list) {
                $chId = $list->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];
                $chId = $baseQuery['chId'];
                $stateShortName = $baseQuery['stateShortName'];
                $regionLongName = $baseQuery['regionLongName'];
                $chConfId = $baseQuery['chConfId'];
                $pcName = $baseQuery['pcName'];
                $chDocuments = $baseQuery['chDocuments'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'Primary Coordinator' => $pcName,
                    'Board Report Received' => ($chDocuments->new_board_submitted == 1) ? 'YES' : 'NO',
                    'Board Report Activated' => ($chDocuments->new_board_active == 1) ? 'YES' : 'NO',
                    'Financial Report Received' => ($chDocuments->financial_report_received == 1) ? 'YES' : 'NO',
                    'Financial Review Complete' => ($chDocuments->financial_review_complete == 1) ? 'YES' : 'NO',
                    'Report Notes' => $chDocuments->report_notes,
                    'Extension Given' => ($chDocuments->report_extension == 1) ? 'YES' : 'NO',
                    'Extension Notes' => $chDocuments->extension_notes,
                ];

                $exportChapterList[] = $rowData;
            }

            $callback = function () use ($exportChapterList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportChapterList)) {
                    fputcsv($file, array_keys($exportChapterList[0]));
                }

                foreach ($exportChapterList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export International Coordinator List
     */
    public function indexIntCoordinator(Request $request)
    {
        $fileName = 'int_coordinator_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseCoordinatorController->getActiveInternationalBaseQuery($coorId);
        $coordinatorList = $baseQuery['query']->get();

        if (count($coordinatorList) > 0) {
            $exportCoordinatorList = [];

            foreach ($coordinatorList as $list) {
                $cdId = $list->id;
                $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                $cdDetails = $baseQuery['cdDetails'];
                $cdId = $baseQuery['cdId'];
                $regionLongName = $baseQuery['regionLongName'];
                $cdConfId = $baseQuery['cdConfId'];
                $RptFName = $baseQuery['RptFName'];
                $RptLName = $baseQuery['RptLName'];
                $ReportTo = $RptFName.' '.$RptLName;
                $displayPosition = $baseQuery['displayPosition'];
                $secondaryPosition = $baseQuery['secondaryPosition'];
                $cdAdminRole = $baseQuery['cdAdminRole'];

                $secPositionValue = null;

                // Check what type of data $secondaryPosition is
                if ($secondaryPosition) {
                    if (is_object($secondaryPosition)) {
                        // If it's a single object
                        $secPositionValue = $secondaryPosition->long_title ?? null;
                    } elseif (is_array($secondaryPosition) || $secondaryPosition instanceof \Traversable) {
                        // If it's a collection or array
                        $secondaryPositionTitles = [];
                        foreach ($secondaryPosition as $position) {
                            if (is_object($position)) {
                                $secondaryPositionTitles[] = $position->long_title ?? '';
                            } elseif (is_string($position)) {
                                $secondaryPositionTitles[] = $position;
                            }
                        }
                        $secPositionValue = ! empty($secondaryPositionTitles) ? implode(', ', $secondaryPositionTitles) : null;
                    } elseif (is_string($secondaryPosition)) {
                        // If it's already a string
                        $secPositionValue = $secondaryPosition;
                    }
                }

                $rowData = [
                    'Conference' => $cdConfId,
                    'Region' => $regionLongName,
                    'Coordinator Name' => $cdDetails->first_name.' '.$cdDetails->last_name,
                    'Position' => $displayPosition->long_title,
                    'Sec Position' => $secPositionValue,
                    'Admin' => $cdAdminRole->admin_role,
                    'Email' => $cdDetails->email,
                    'Email2' => $cdDetails->sec_email,
                    'Report To' => $ReportTo,
                    'Address' => $cdDetails->address,
                    'City' => $cdDetails->city,
                    'State' => $cdDetails->state_id,
                    'Zip' => $cdDetails->zip,
                    'Phone' => $cdDetails->phone,
                    'Phone2' => $cdDetails->alt_phone,
                    'Birthday' => $cdDetails->birthday_month_id.' / '.$cdDetails->birthday_day,
                    'Coordinator Start' => $cdDetails->coordinator_start_date,
                    'Last Promoted' => $cdDetails->last_promoted,
                    'Leave of Absense' => ($cdDetails->on_leave == 1) ? 'YES' : 'NO',
                    'Leave Date' => $cdDetails->leave_date,
                    'Last UpdatedBy' => $cdDetails->last_updated_by,
                    'Last UpdatedDate' => $cdDetails->last_updated_date,
                ];

                $exportCoordinatorList[] = $rowData;
            }

            $callback = function () use ($exportCoordinatorList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportCoordinatorList)) {
                    fputcsv($file, array_keys($exportCoordinatorList[0]));
                }

                foreach ($exportCoordinatorList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export International Retired Coordinator List
     */
    public function indexIntRetCoordinator(Request $request)
    {
        $fileName = 'int_coordinator_ret_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseCoordinatorController->getRetiredInternationalBaseQuery($coorId);
        $coordinatorList = $baseQuery['query']->get();

        if (count($coordinatorList) > 0) {
            $exportCoordinatorList = [];

            foreach ($coordinatorList as $list) {
                $cdId = $list->id;
                $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                $cdDetails = $baseQuery['cdDetails'];
                $cdId = $baseQuery['cdId'];
                $regionLongName = $baseQuery['regionLongName'];
                $cdConfId = $baseQuery['cdConfId'];
                $RptFName = $baseQuery['RptFName'];
                $RptLName = $baseQuery['RptLName'];
                $ReportTo = $RptFName.' '.$RptLName;
                $displayPosition = $baseQuery['displayPosition'];
                $secondaryPosition = $baseQuery['secondaryPosition'];
                $cdAdminRole = $baseQuery['cdAdminRole'];

                $secPositionValue = null;

                // Check what type of data $secondaryPosition is
                if ($secondaryPosition) {
                    if (is_object($secondaryPosition)) {
                        // If it's a single object
                        $secPositionValue = $secondaryPosition->long_title ?? null;
                    } elseif (is_array($secondaryPosition) || $secondaryPosition instanceof \Traversable) {
                        // If it's a collection or array
                        $secondaryPositionTitles = [];
                        foreach ($secondaryPosition as $position) {
                            if (is_object($position)) {
                                $secondaryPositionTitles[] = $position->long_title ?? '';
                            } elseif (is_string($position)) {
                                $secondaryPositionTitles[] = $position;
                            }
                        }
                        $secPositionValue = ! empty($secondaryPositionTitles) ? implode(', ', $secondaryPositionTitles) : null;
                    } elseif (is_string($secondaryPosition)) {
                        // If it's already a string
                        $secPositionValue = $secondaryPosition;
                    }
                }

                $rowData = [
                    'Conference' => $cdConfId,
                    'Region' => $regionLongName,
                    'Coordinator Name' => $cdDetails->first_name.' '.$cdDetails->last_name,
                    'Position' => $displayPosition->long_title,
                    'Sec Position' => $secPositionValue,
                    'Admin' => $cdAdminRole->admin_role,
                    'Email' => $cdDetails->email,
                    'Email2' => $cdDetails->sec_email,
                    'Report To' => $ReportTo,
                    'Address' => $cdDetails->address,
                    'City' => $cdDetails->city,
                    'State' => $cdDetails->state_id,
                    'Zip' => $cdDetails->zip,
                    'Phone' => $cdDetails->phone,
                    'Phone2' => $cdDetails->alt_phone,
                    'Birthday' => $cdDetails->birthday_month_id.' / '.$cdDetails->birthday_day,
                    'Coordinator Start' => $cdDetails->coordinator_start_date,
                    'Last Promoted' => $cdDetails->last_promoted,
                    'Leave of Absense' => ($cdDetails->on_leave == 1) ? 'YES' : 'NO',
                    'Leave Date' => $cdDetails->leave_date,
                    'Retire Date' => $cdDetails->zapped_date,
                    'Reason' => $cdDetails->reason_retired,
                    'Last UpdatedBy' => $cdDetails->last_updated_by,
                    'Last UpdatedDate' => $cdDetails->last_updated_date,
                ];

                $exportCoordinatorList[] = $rowData;
            }

            $callback = function () use ($exportCoordinatorList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportCoordinatorList)) {
                    fputcsv($file, array_keys($exportCoordinatorList[0]));
                }

                foreach ($exportCoordinatorList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export International Overdue Re-Registration List
     */
    public function indexIntReReg(Request $request)
    {
        $fileName = 'int_rereg_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $now = Carbon::now();
        $currentMonth = $now->month;
        $lastMonth = $now->copy()->subMonth()->format('m');
        $currentYear = $now->year;

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);

        $reChapterList = $baseQuery['query']
            ->where(function ($query) use ($currentYear, $lastMonth) {
                $query->where('next_renewal_year', '<', $currentYear)
                    ->orWhere(function ($query) use ($currentYear, $lastMonth) {
                        $query->where('next_renewal_year', '=', $currentYear)
                            ->where('start_month_id', '<=', $lastMonth);
                    });
            })
            ->orderByDesc('start_month_id')
            ->orderByDesc('next_renewal_year')
            ->get();

        if (count($reChapterList) >= 0) {
            $exportReRegList = [];

            foreach ($reChapterList as $list) {
                $chId = $list->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];
                $chId = $baseQuery['chId'];
                $stateShortName = $baseQuery['stateShortName'];
                $regionLongName = $baseQuery['regionLongName'];
                $chConfId = $baseQuery['chConfId'];
                $pcName = $baseQuery['pcName'];
                $startMonthName = $baseQuery['startMonthName'];
                $chapterStatus = $baseQuery['chapterStatus'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'Primary Coordinator' => $pcName,
                    'Status' => $chapterStatus,
                    'Notes' => $chDetails->notes,
                    'Month Due' => $startMonthName,
                    'Year Due' => $chDetails->next_renewal_year,
                    'Re-Reg Notes' => $chDetails->reg_notes,
                    'Dues Last Paid' => $chDetails->dues_last_paid,
                    'Members paid for' => $chDetails->members_paid_for,
                ];

                $exportReRegList[] = $rowData;
            }

            $callback = function () use ($exportReRegList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportReRegList)) {
                    fputcsv($file, array_keys($exportReRegList[0]));
                }

                foreach ($exportReRegList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export International EIN Status List
     */
    public function indexIntEINStatus(Request $request)
    {
        $fileName = 'int_ein_status_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);
        $chapterList = $baseQuery['query']->get();

        if (count($chapterList) > 0) {
            $exportChapterList = [];

            foreach ($chapterList as $list) {
                $chId = $list->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];
                $chId = $baseQuery['chId'];
                $stateShortName = $baseQuery['stateShortName'];
                $regionLongName = $baseQuery['regionLongName'];
                $chConfId = $baseQuery['chConfId'];
                $startMonthName = $baseQuery['startMonthName'];
                $chDocuments = $baseQuery['chDocuments'];
                $PresDetails = $baseQuery['PresDetails'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'EIN' => $chDetails->ein,
                    'EIN Letter' => ($chDocuments->ein_letter == 1) ? 'YES' : 'NO',
                    'Start Month' => $startMonthName,
                    'Start Year' => $chDetails->start_year,
                    'Pres Name' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'Pres Address' => $PresDetails->street_address,
                    'Pres City' => $PresDetails->city,
                    'Pres State' => $PresDetails->state_id,
                    'Pres Zip' => $PresDetails->zip,
                    'Pres Phone' => $PresDetails->phone,
                    'Pres Email' => $PresDetails->email,
                ];

                $exportChapterList[] = $rowData;
            }

            $callback = function () use ($exportChapterList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportChapterList)) {
                    fputcsv($file, array_keys($exportChapterList[0]));
                }

                foreach ($exportChapterList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export International Chapter List
     */
    public function indexInternationalIRSFiling(Request $request)
    {
        $fileName = 'int_subordinate_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        // Get January 1st of the previous year
        $previousYear = Carbon::now()->subYear()->startOfYear();

        $baseQueryActive = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);
        $baseQueryZapped = $this->baseChapterController->getZappedInternationalBaseQuery($coorId);

        $activeSubquery = $baseQueryActive['query']
            ->select('chapters.*');

        $zappedSubquery = $baseQueryZapped['query']
            ->where('chapters.zap_date', '>', $previousYear)
            ->select('chapters.*');

        $irsChapterList = DB::table(DB::raw("({$activeSubquery->toSql()}) as active_chapters"))
            ->mergeBindings($activeSubquery->getQuery())
            ->union(
                DB::table(DB::raw("({$zappedSubquery->toSql()}) as zapped_chapters"))
                    ->mergeBindings($zappedSubquery->getQuery())
            )
            ->orderBy('ein')
            ->get();

        if (count($irsChapterList) > 0) {
            $exportChapterList = [];

            foreach ($irsChapterList as $list) {
                $chId = $list->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];
                $chId = $baseQuery['chId'];
                $chIsActive = $baseQuery['chIsActive'];
                if ($chIsActive == '1') {
                    $PresDetails = $baseQuery['PresDetails'];
                    $deleteColumn = null;
                }
                if ($chIsActive == '0') {
                    $PresDetails = $baseQuery['PresDisbandedDetails'];
                    $deleteColumn = 'DELETE';
                }

                $rowData = [
                    'delete' => $deleteColumn,
                    'EIN' => $chDetails->ein,
                    'Name' => $chDetails->name,
                    'Pres Name' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'Pres Address' => $PresDetails->street_address,
                    'Pres City' => $PresDetails->city,
                    'Pres State' => $PresDetails->state_id,
                    'Pres Zip' => $PresDetails->zip,
                ];

                $exportChapterList[] = $rowData;
            }

            $callback = function () use ($exportChapterList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportChapterList)) {
                    fputcsv($file, array_keys($exportChapterList[0]));
                }

                foreach ($exportChapterList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export International EOY Reports Status List
     */
    public function indexIntEOYStatus(Request $request)
    {
        $fileName = 'int_eoy_status_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);
        $chapterList = $baseQuery['query']->get();

        if (count($chapterList) > 0) {
            $exportChapterList = [];

            foreach ($chapterList as $list) {
                $chId = $list->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];
                $chId = $baseQuery['chId'];
                $stateShortName = $baseQuery['stateShortName'];
                $regionLongName = $baseQuery['regionLongName'];
                $chConfId = $baseQuery['chConfId'];
                $pcName = $baseQuery['pcName'];
                $chDocuments = $baseQuery['chDocuments'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'Primary Coordinator' => $pcName,
                    'Board Report Received' => ($chDocuments->new_board_submitted == 1) ? 'YES' : 'NO',
                    'Board Report Activated' => ($chDocuments->new_board_active == 1) ? 'YES' : 'NO',
                    'Financial Report Received' => ($chDocuments->financial_report_received == 1) ? 'YES' : 'NO',
                    'Financial Review Complete' => ($chDocuments->financial_review_complete == 1) ? 'YES' : 'NO',
                    'Report Notes' => $chDocuments->report_notes,
                    'Extension Given' => ($chDocuments->report_extension == 1) ? 'YES' : 'NO',
                    'Extension Notes' => $chDocuments->extension_notes,
                ];

                $exportChapterList[] = $rowData;
            }

            $callback = function () use ($exportChapterList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportChapterList)) {
                    fputcsv($file, array_keys($exportChapterList[0]));
                }

                foreach ($exportChapterList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }
}
