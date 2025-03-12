<?php

namespace App\Http\Controllers;

use App\Models\Coordinators;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    /* /Custom Helpers/ */
    // $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);

    /* / Base Chapter Controller / */
    //  $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    //  $this->baseChapterController->getZappedBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    //  $this->baseChapterController->getChapterDetails($chId)

    /* / Base Coordinator Controller / */
    //  $this->baseCoordinatorController->getActiveBaseQuery($userConfId, $userRegId, $userCdId, $userPositionid, $userSecPositionid)
    //  $this->baseCoordinatorController->getRetiredBaseQuery($userConfId, $userRegId, $userCdId, $userPositionid, $userSecPositionid)
    //  $this->baseCoordinatorController->getCoordinatorDetails($id)

    /**
     * Get Base Chapter List
     */
    public function getBaseChapterQuery()
    {
        return DB::table('chapters')
            ->select('chapters.*', 'chapters.conference_id as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'bd.first_name as pre_fname',
                'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city', 'bd.state as pre_state', 'bd.zip as pre_zip',
                'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state',
                'chapters.new_board_submitted as new_board_submitted', 'chapters.new_board_active as new_board_active', 'chapters.financial_report_received as financial_report_received',
                'chapters.financial_report_complete as financial_report_complete', 'cd2.first_name as fr_fname', 'cd2.last_name as fr_lname',
                'avp.first_name as avp_fname', 'avp.last_name as avp_lname', 'avp.email as avp_email', 'avp.phone as avp_phone',
                'mvp.first_name as mvp_fname', 'mvp.last_name as mvp_lname', 'mvp.email as mvp_email', 'mvp.phone as mvp_phone',
                'trs.first_name as trs_fname', 'trs.last_name as trs_lname', 'trs.email as trs_email', 'trs.phone as trs_phone',
                'sec.first_name as sec_fname', 'sec.last_name as sec_lname', 'sec.email as sec_email', 'sec.phone as sec_phone'
            )
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')  // Primary Coordinator
            ->leftJoin('financial_report as fr', 'fr.chapter_id', '=', 'chapters.id')
            ->leftJoin('coordinators as cd2', 'fr.reviewer_id', '=', 'cd2.id') // Financial Report Reviewer
            ->leftJoin('boards as bd', function ($join) {
                $join->on('bd.chapter_id', '=', 'chapters.id')
                    ->where('bd.board_position_id', '=', 1); // President
            })
            ->leftJoin('boards as avp', function ($join) {
                $join->on('avp.chapter_id', '=', 'chapters.id')
                    ->where('avp.board_position_id', '=', 2); // AVP
            })
            ->leftJoin('boards as mvp', function ($join) {
                $join->on('mvp.chapter_id', '=', 'chapters.id')
                    ->where('mvp.board_position_id', '=', 3); // MVP
            })
            ->leftJoin('boards as trs', function ($join) {
                $join->on('trs.chapter_id', '=', 'chapters.id')
                    ->where('trs.board_position_id', '=', 4); // Treasurer
            })
            ->leftJoin('boards as sec', function ($join) {
                $join->on('sec.chapter_id', '=', 'chapters.id')
                    ->where('sec.board_position_id', '=', 5); // Secretary
            })
            ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
            ->leftJoin('region as rg', 'chapters.region_id', '=', 'rg.id');
    }

    /**
     * Get Base Chapter List
     */
    // public function getBaseIncomingChapterQuery()
    // {
    //     return DB::table('chapters')
    //         ->select('chapters.*', 'chapters.conference_id as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'bd.first_name as pre_fname',
    //             'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city', 'bd.state as pre_state', 'bd.zip as pre_zip',
    //             'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state',
    //             'avp.first_name as avp_fname', 'avp.last_name as avp_lname', 'avp.email as avp_email', 'avp.phone as avp_phone',
    //             'avp.street_address as avp_address', 'avp.city as avp_city', 'avp.state as avp_state', 'avp.zip as avp_zip',
    //             'mvp.first_name as mvp_fname', 'mvp.last_name as mvp_lname', 'mvp.email as mvp_email', 'mvp.phone as mvp_phone',
    //             'mvp.street_address as mvp_address', 'mvp.city as mvp_city', 'mvp.state as mvp_state', 'mvp.zip as mvp_zip',
    //             'trs.first_name as trs_fname', 'trs.last_name as trs_lname', 'trs.email as trs_email', 'trs.phone as trs_phone',
    //             'trs.street_address as trs_address', 'trs.city as trs_city', 'trs.state as trs_state', 'trs.zip as trs_zip',
    //             'sec.first_name as sec_fname', 'sec.last_name as sec_lname', 'sec.email as sec_email', 'sec.phone as sec_phone',
    //             'sec.street_address as sec_address', 'sec.city as sec_city', 'sec.state as sec_state', 'sec.zip as sec_zip'
    //         )
    //         ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')  // Primary Coordinator
    //         ->leftJoin('financial_report as fr', 'fr.chapter_id', '=', 'chapters.id') // Financial Report
    //         ->leftJoin('coordinators as cd2', 'fr.reviewer_id', '=', 'cd2.id') // Financial Report Reviewer
    //         ->leftJoin('incoming_board_member as bd', function ($join) {
    //             $join->on('bd.chapter_id', '=', 'chapters.id')
    //                 ->where('bd.board_position_id', '=', 1); // President
    //         })
    //         ->leftJoin('incoming_board_member as avp', function ($join) {
    //             $join->on('avp.chapter_id', '=', 'chapters.id')
    //                 ->where('avp.board_position_id', '=', 2); // AVP
    //         })
    //         ->leftJoin('incoming_board_member as mvp', function ($join) {
    //             $join->on('mvp.chapter_id', '=', 'chapters.id')
    //                 ->where('mvp.board_position_id', '=', 3); // MVP
    //         })
    //         ->leftJoin('incoming_board_member as trs', function ($join) {
    //             $join->on('trs.chapter_id', '=', 'chapters.id')
    //                 ->where('trs.board_position_id', '=', 4); // Treasurer
    //         })
    //         ->leftJoin('incoming_board_member as sec', function ($join) {
    //             $join->on('sec.chapter_id', '=', 'chapters.id')
    //                 ->where('sec.board_position_id', '=', 5); // Secretary
    //         })
    //         ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
    //         ->leftJoin('region as rg', 'chapters.region_id', '=', 'rg.id');
    // }

    /**
     * Export Chapter List
     */
    public function indexChapter(Request $request)
    {
        $fileName = 'chapter_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();

        if (count($chapterList) > 0) {
            $exportChapterList = [];

            foreach ($chapterList as $list) {
                $chId = $list->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];
                $chId = $baseQuery['chId'];
                $chIsActive = $baseQuery['chIsActive'];
                $stateShortName = $baseQuery['stateShortName'];
                $regionLongName = $baseQuery['regionLongName'];
                $chConfId = $baseQuery['chConfId'];
                $pcName = $baseQuery['pcName'];
                $startMonthName = $baseQuery['startMonthName'];
                $chapterStatus = $baseQuery['chapterStatus'];
                $websiteLink = $baseQuery['websiteLink'];
                $PresDetails = $baseQuery['PresDetails'];
                $AVPDetails = $baseQuery['AVPDetails'];
                $MVPDetails = $baseQuery['MVPDetails'];
                $TRSDetails = $baseQuery['TRSDetails'];
                $SECDetails = $baseQuery['SECDetails'];

                $rowData = [
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
                    'Pres Name' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'Pres Email' => $PresDetails->email,
                    'Pres Phone' => $PresDetails->phone,
                    'AVP Name' => $AVPDetails->first_name.' '.$AVPDetails->last_name,
                    'AVP Email' => $AVPDetails->email,
                    'AVP Phone' => $AVPDetails->phone,
                    'MVP Name' => $MVPDetails->first_name.' '.$MVPDetails->last_name,
                    'MVP Email' => $MVPDetails->email,
                    'MVP Phone' => $MVPDetails->phone,
                    'Treasurer Name' => $TRSDetails->first_name.' '.$TRSDetails->last_name,
                    'Treasurer Email' => $TRSDetails->email,
                    'Treasurer Phone' => $TRSDetails->phone,
                    'Secretary Name' => $SECDetails->first_name.' '.$SECDetails->last_name,
                    'Secretary Email' => $SECDetails->email,
                    'Secretary Phone' => $SECDetails->phone,
                    'Website' => $websiteLink,
                    'Linked Status' => $chDetails->website_status,
                    'EGroup' => $chDetails->egroup,
                    'Social Media' => $chDetails->social1.' '.$chDetails->social1.' '.$chDetails->social1,
                    'Start Month' => $startMonthName,
                    'Start Year' => $chDetails->start_year,
                    'Dues Last Paid' => $chDetails->dues_last_paid,
                    'Members paid for' => $chDetails->members_paid_for,
                    'NextRenewal' => $chDetails->next_renewal_year,
                    'Founder' => $chDetails->founders_name,
                    'Sistered By' => $chDetails->sistered_by,
                    'FormerName' => $chDetails->former_name,
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
     * Export Zapped Chapter List
     */
    public function indexZappedChapter(Request $request)
    {
        $fileName = 'chapter_zap_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseChapterController->getZappedBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $zappedChapterList = $baseQuery['query']->get();

        if (count($zappedChapterList) > 0) {
            $exportZapChapterList = [];

            foreach ($zappedChapterList as $list) {
                $chId = $list->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];
                $chId = $baseQuery['chId'];
                $chIsActive = $baseQuery['chIsActive'];
                $stateShortName = $baseQuery['stateShortName'];
                $regionLongName = $baseQuery['regionLongName'];
                $chConfId = $baseQuery['chConfId'];
                $pcName = $baseQuery['pcName'];
                $startMonthName = $baseQuery['startMonthName'];
                $chapterStatus = $baseQuery['chapterStatus'];
                $websiteLink = $baseQuery['websiteLink'];
                $PresDetails = $baseQuery['PresDetails'];
                $AVPDetails = $baseQuery['AVPDetails'];
                $MVPDetails = $baseQuery['MVPDetails'];
                $TRSDetails = $baseQuery['TRSDetails'];
                $SECDetails = $baseQuery['SECDetails'];

                $rowData = [
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
                    'Pres Name' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'Pres Email' => $PresDetails->email,
                    'Pres Phone' => $PresDetails->phone,
                    'AVP Name' => $AVPDetails->first_name.' '.$AVPDetails->last_name,
                    'AVP Email' => $AVPDetails->email,
                    'AVP Phone' => $AVPDetails->phone,
                    'MVP Name' => $MVPDetails->first_name.' '.$MVPDetails->last_name,
                    'MVP Email' => $MVPDetails->email,
                    'MVP Phone' => $MVPDetails->phone,
                    'Treasurer Name' => $TRSDetails->first_name.' '.$TRSDetails->last_name,
                    'Treasurer Email' => $TRSDetails->email,
                    'Treasurer Phone' => $TRSDetails->phone,
                    'Secretary Name' => $SECDetails->first_name.' '.$SECDetails->last_name,
                    'Secretary Email' => $SECDetails->email,
                    'Secretary Phone' => $SECDetails->phone,
                    'Website' => $websiteLink,
                    'Linked Status' => $chDetails->website_status,
                    'EGroup' => $chDetails->egroup,
                    'Social Media' => $chDetails->social1.' '.$chDetails->social1.' '.$chDetails->social1,
                    'Start Month' => $startMonthName,
                    'Start Year' => $chDetails->start_year,
                    'Dues Last Paid' => $chDetails->dues_last_paid,
                    'Members paid for' => $chDetails->members_paid_for,
                    'NextRenewal' => $chDetails->next_renewal_year,
                    'Founder' => $chDetails->founders_name,
                    'Sistered By' => $chDetails->sistered_by,
                    'FormerName' => $chDetails->former_name,
                    'Disband Date' => $chDetails->zap_date,
                    'Disband Reason' => $chDetails->disband_reason,
                ];

                $exportZapChapterList[] = $rowData;
            }

            $callback = function () use ($exportZapChapterList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportZapChapterList)) {
                    fputcsv($file, array_keys($exportZapChapterList[0]));
                }

                foreach ($exportZapChapterList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export Zapped Chapter List
     */
    // public function indexZappedChapter2(Request $request)
    // {
    //     $fileName = 'chapter_zap_export_'.date('Y-m-d').'.csv';
    //     $headers = [
    //         'Content-type' => 'text/csv',
    //         'Content-Disposition' => "attachment; filename=$fileName",
    //         'Pragma' => 'no-cache',
    //         'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
    //         'Expires' => '0',
    //     ];
    //     //Get Coordinators Details
    //     $corDetails = User::find($request->user()->id)->coordinator;
    //     $corId = $corDetails['id'];
    //     $corConfId = $corDetails['conference_id'];
    //     $corRegId = $corDetails['region_id'];
    //     $positionId = $corDetails['position_id'];
    //     $secPositionId = $corDetails['sec_position_id'];

    //     // Get the conditions
    //     $conditions = getPositionConditions($positionId, $secPositionId);

    //     if ($conditions['coordinatorCondition']) {
    //         // Load Reporting Tree
    //         $coordinatorData = $this->userController->loadReportingTree($corId);
    //         $inQryArr = $coordinatorData['inQryArr'];
    //     }

    //     // Get the base query
    //     $baseQuery = $this->getBaseChapterQuery();

    //     // Apply specific conditions for this scenario
    //     $chapterList = $baseQuery->where('chapters.is_active', '=', '0')
    //         ->orderByDesc('chapters.zap_date');

    //     // Apply conditions
    //     if ($conditions['founderCondition']) {
    //         // No additional condition; proceed with the base query
    //     } elseif ($conditions['assistConferenceCoordinatorCondition']) {
    //         $chapterList = $chapterList->where('chapters.conference_id', '=', $corConfId);
    //     } elseif ($conditions['regionalCoordinatorCondition']) {
    //         $chapterList = $chapterList->where('chapters.region_id', '=', $corRegId);
    //     } else {
    //         $chapterList = $chapterList->whereIn('chapters.primary_coordinator_id', $inQryArr);
    //     }

    //     // Execute query
    //     $zappedChapterList = $chapterList->get();

    //     //print sizeof($zappedChapterList); die;
    //     if (count($zappedChapterList) > 0) {
    //         $exportZapChapterList = [];

    //         $statusValues = [
    //             1 => 'Operating OK',
    //             4 => 'On Hold Do not Refer',
    //             5 => 'Probation',
    //             6 => 'Probation Do Not Refer',
    //         ];

    //         foreach ($zappedChapterList as $list) {
    //             $list->status_value = $statusValues[$list->status_id] ?? 'Unknown';
    //             $exportZapChapterList[] = $list;
    //         }

    //         $columns = ['EIN', 'Conference', 'Region', 'State', 'Name', 'Primary Coordinator', 'Last Updated', 'First Name', 'Last Name', 'Address', 'City',
    //             'State', 'Zip', 'Country', 'Phone', 'email', 'Inquiries', 'Chapter Email', 'AVP First Name', 'AVP Last Name', 'AVP Email', 'MVP First Name',
    //             'MVP Last Name', 'MVP Email', 'Treasurer First Name', 'Treasurer Last Name', 'Treasurer Email', 'Secretary First Name', 'Secretary Last Name',
    //             'Secretary Email', 'Chapter P.O. Box', 'WebpageURL', 'Linked', 'E-Groups', 'Territory', 'InquiriesNote', 'Status', 'Start Month', 'Start Year',
    //             'Dues Last Paid', 'Members paid for', 'NextRenewal', 'Notes', 'Founder', 'Sistered By', 'FormerName', 'Disband Date', 'Disband Reason'];
    //         $callback = function () use ($exportZapChapterList, $columns) {
    //             $file = fopen('php://output', 'w');
    //             fputcsv($file, $columns);

    //             foreach ($exportZapChapterList as $list) {
    //                 fputcsv($file, [$list->ein,
    //                     $list->conf,
    //                     $list->reg_name,
    //                     $list->state,
    //                     $list->name,
    //                     $list->cd_fname,
    //                     $list->last_updated_date,
    //                     $list->pre_fname,
    //                     $list->pre_lname,
    //                     $list->pre_add,
    //                     $list->pre_city,
    //                     $list->pre_state,
    //                     $list->pre_zip,
    //                     $list->pre_country,
    //                     $list->pre_phone,
    //                     $list->pre_email,
    //                     $list->inquiries_contact,
    //                     $list->email,
    //                     $list->avp_fname,
    //                     $list->avp_lname,
    //                     $list->avp_email,
    //                     $list->mvp_fname,
    //                     $list->mvp_lname,
    //                     $list->mvp_email,
    //                     $list->trs_fname,
    //                     $list->trs_lname,
    //                     $list->trs_email,
    //                     $list->sec_fname,
    //                     $list->sec_lname,
    //                     $list->sec_email,
    //                     $list->po_box,
    //                     $list->website_url,
    //                     $list->website_status,
    //                     $list->egroup,
    //                     $list->territory,
    //                     $list->inquiries_note,
    //                     $list->status_value,
    //                     $list->start_month_id,
    //                     $list->start_year,
    //                     $list->dues_last_paid,
    //                     $list->members_paid_for,
    //                     $list->next_renewal_year,
    //                     $list->notes,
    //                     $list->founders_name,
    //                     $list->sistered_by,
    //                     $list->former_name,
    //                     $list->zap_date,
    //                     $list->disband_reason,
    //                 ]);
    //             }
    //             fclose($file);
    //         };

    //         return Response::stream($callback, 200, $headers);
    //     }

    //     return redirect()->to('/home');
    // }

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

        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $currentYear = date('Y');
        $currentMonth = date('m');

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $query = $baseQuery['query'];

        // Extra conditions applied to *already filtered* base query
        $chapterList = $query->where(function ($query) use ($currentYear, $currentMonth) {
            $query->where(function ($q) use ($currentYear, $currentMonth) {
                $q->where('chapters.next_renewal_year', '=', $currentYear)
                    ->where('chapters.start_month_id', '<', $currentMonth);
            })
                ->orWhere(function ($q) use ($currentYear) {
                    $q->where('chapters.next_renewal_year', '<', $currentYear);
                });
        })
            ->orderBy('chapters.next_renewal_year')
            ->orderBy('chapters.start_month_id');

        $ReRegList = $chapterList->get();

        if (count($ReRegList) >= 0) {
            $exportReRegList = [];

            foreach ($ReRegList as $list) {
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
                $PresDetails = $baseQuery['PresDetails'];

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

        $currentYear = date('Y');
        $currentMonth = date('m');

        // Get the base query
        $baseQuery = $this->getBaseChapterQuery();

        // Apply specific conditions for this scenario
        $chapterList = $baseQuery->where(function ($query) use ($currentYear, $currentMonth) {
            $query->where('chapters.is_active', '=', '1')
                ->where('chapters.next_renewal_year', '=', $currentYear)
                ->where('chapters.start_month_id', '<', $currentMonth);
        })->orWhere(function ($query) use ($currentYear) {
            $query->where('chapters.is_active', '=', '1')
                ->where('chapters.next_renewal_year', '<', $currentYear);
        })->orderBy('chapters.next_renewal_year')
            ->orderBy('chapters.start_month_id');

        // Execute query
        $ReRegList = $chapterList->get();

        if (count($ReRegList) >= 0) {
            $exportReRegList = [];

            $statusValues = [
                1 => 'Operating OK',
                4 => 'On Hold Do not Refer',
                5 => 'Probation',
                6 => 'Probation Do Not Refer',
            ];

            foreach ($ReRegList as $list) {
                $list->status_value = $statusValues[$list->status_id] ?? 'Unknown';
                $exportReRegList[] = $list;
            }

            $columns = ['Conference', 'Region', 'State', 'Name', 'Primary Coordinator', 'Status', 'Month Due', 'Year Due', 'Re-Reg Notes', 'Dues Last Paid', 'Members paid for'];
            $callback = function () use ($exportReRegList, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($exportReRegList as $list) {
                    fputcsv($file, [
                        $list->conf,
                        $list->reg_name,
                        $list->state,
                        $list->name,
                        $list->cd_fname,
                        $list->status_value,
                        $list->start_month_id,
                        $list->next_renewal_year,
                        $list->reg_notes,
                        $list->dues_last_paid,
                        $list->members_paid_for,
                    ]);
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
    public function indexInternationalChapter(Request $request)
    {
        $fileName = 'int_chapter_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Get the base query
        $baseQuery = $this->getBaseChapterQuery();

        // Apply specific conditions for this scenario
        $chapterList = $baseQuery->where('chapters.is_active', '=', '1')
            ->orderBy('chapters.name');

        // Execute query
        $activeChapterList = $chapterList->get();

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];

            $statusValues = [
                1 => 'Operating OK',
                4 => 'On Hold Do not Refer',
                5 => 'Probation',
                6 => 'Probation Do Not Refer',
            ];

            foreach ($activeChapterList as $list) {
                $list->status_value = $statusValues[$list->status_id] ?? 'Unknown';
                $exportChapterList[] = $list;
            }

            $columns = ['EIN', 'Conference', 'Region', 'State', 'Name', 'Primary Coordinator', 'Last Updated', 'First Name', 'Last Name', 'Address', 'City',
                'State', 'Zip', 'Country', 'Phone', 'email', 'Inquiries', 'Chapter Email', 'AVP First Name', 'AVP Last Name', 'AVP Email', 'MVP First Name',
                'MVP Last Name', 'MVP Email', 'Treasurer First Name', 'Treasurer Last Name', 'Treasurer Email', 'Secretary First Name', 'Secretary Last Name',
                'Secretary Email', 'Chapter P.O. Box', 'WebpageURL', 'Linked', 'E-Groups', 'Territory', 'InquiriesNote', 'Status', 'Start Month', 'Start Year',
                'Dues Last Paid', 'Members paid for', 'NextRenewal', 'Notes', 'Founder', 'Sistered By', 'FormerName'];
            $callback = function () use ($exportChapterList, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($exportChapterList as $list) {
                    fputcsv($file, [$list->ein,
                        $list->conf,
                        $list->reg_name,
                        $list->state,
                        $list->name,
                        $list->cd_fname.' '.$list->cd_lname,
                        $list->last_updated_date,
                        $list->pre_fname,
                        $list->pre_lname,
                        $list->pre_add,
                        $list->pre_city,
                        $list->pre_state,
                        $list->pre_zip,
                        $list->pre_country,
                        $list->pre_phone,
                        $list->pre_email,
                        $list->inquiries_contact,
                        $list->email,
                        $list->avp_fname,
                        $list->avp_lname,
                        $list->avp_email,
                        $list->mvp_fname,
                        $list->mvp_lname,
                        $list->mvp_email,
                        $list->trs_fname,
                        $list->trs_lname,
                        $list->trs_email,
                        $list->sec_fname,
                        $list->sec_lname,
                        $list->sec_email,
                        $list->po_box,
                        $list->website_url,
                        $list->website_status,
                        $list->egroup,
                        $list->territory,
                        $list->inquiries_note,
                        $list->status_id,
                        $list->start_month_id,
                        $list->start_year,
                        $list->dues_last_paid,
                        $list->members_paid_for,
                        $list->next_renewal_year,
                        $list->notes,
                        $list->founders_name,
                        $list->sistered_by,
                        $list->former_name,
                    ]);
                }
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export International Zapped Chapter List
     */
    public function indexInternationalZapChapter(Request $request)
    {
        $fileName = 'int_chapter_zap_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Get the base query
        $baseQuery = $this->getBaseChapterQuery();

        // Apply specific conditions for this scenario
        $chapterList = $baseQuery->where('chapters.is_active', '=', '0')
            ->orderByDesc('chapters.zap_date');

        // Execute query
        $zappedChapterList = $chapterList->get();

        // print sizeof($zappedChapterList); die;
        if (count($zappedChapterList) > 0) {
            $exportZapChapterList = [];

            $statusValues = [
                1 => 'Operating OK',
                4 => 'On Hold Do not Refer',
                5 => 'Probation',
                6 => 'Probation Do Not Refer',
            ];

            foreach ($zappedChapterList as $list) {
                $list->status_value = $statusValues[$list->status_id] ?? 'Unknown';
                $exportZapChapterList[] = $list;
            }

            $columns = ['EIN', 'Conference', 'Region', 'State', 'Name', 'Primary Coordinator', 'Last Updated', 'First Name', 'Last Name', 'Address', 'City', 'State',
                'Zip', 'Country', 'Phone', 'email', 'Inquiries', 'Chapter Email', 'AVP First Name', 'AVP Last Name', 'AVP Email', 'MVP First Name', 'MVP Last Name',
                'MVP Email', 'Treasurer First Name', 'Treasurer Last Name', 'Treasurer Email', 'Secretary First Name', 'Secretary Last Name', 'Secretary Email',
                'Chapter P.O. Box', 'WebpageURL', 'Linked', 'E-Groups', 'Territory', 'InquiriesNote', 'Status', 'Start Month', 'Start Year', 'Dues Last Paid',
                'Members paid for', 'NextRenewal', 'Notes', 'Founder', 'Sistered By', 'FormerName', 'Disband Date', 'Disband Reason'];
            $callback = function () use ($exportZapChapterList, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($exportZapChapterList as $list) {
                    fputcsv($file, [$list->ein,
                        $list->conf,
                        $list->reg_name,
                        $list->state,
                        $list->name,
                        $list->cd_fname,
                        $list->last_updated_date,
                        $list->pre_fname,
                        $list->pre_lname,
                        $list->pre_add,
                        $list->pre_city,
                        $list->pre_state,
                        $list->pre_zip,
                        $list->pre_country,
                        $list->pre_phone,
                        $list->pre_email,
                        $list->inquiries_contact,
                        $list->email,
                        $list->avp_fname,
                        $list->avp_lname,
                        $list->avp_email,
                        $list->mvp_fname,
                        $list->mvp_lname,
                        $list->mvp_email,
                        $list->trs_fname,
                        $list->trs_lname,
                        $list->trs_email,
                        $list->sec_fname,
                        $list->sec_lname,
                        $list->sec_email,
                        $list->po_box,
                        $list->website_url,
                        $list->website_status,
                        $list->egroup,
                        $list->territory,
                        $list->inquiries_note,
                        $list->status_id,
                        $list->start_month_id,
                        $list->start_year,
                        $list->dues_last_paid,
                        $list->members_paid_for,
                        $list->next_renewal_year,
                        $list->notes,
                        $list->founders_name,
                        $list->sistered_by,
                        $list->former_name,
                        $list->zap_date,
                        $list->disband_reason,
                    ]);
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
    public function indexInternationalIRSFiling(Request $request): StreamedResponse
    {
        $fileName = 'int_subordinate_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Get January 1st of the previous year
        $previousYear = Carbon::now()->subYear()->startOfYear();

        // Get Coordinators Details
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $activeChapterList = DB::table('chapters')
            ->select('chapters.*', 'chapters.conference_id as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname',
                'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city',
                'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
            ->leftjoin('region as rg', 'chapters.region_id', '=', 'rg.id')
            // ->where('chapters.is_active', '=', '1')
            ->where(function ($query) use ($previousYear) {
                $query->where('chapters.is_active', '=', 1)
                    ->orWhere(function ($query) use ($previousYear) {
                        $query->where('chapters.is_active', '=', 0)
                            ->where('chapters.zap_date', '>', $previousYear);
                    });
            })
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('chapters.ein')
            ->get();

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];
            foreach ($activeChapterList as $list) {

                // Check if the chapter is active
                $deleteColumn = ($list->is_active == 1) ? 'DELETE' : '';

                // Prepare the chapter list data
                $exportChapterList[] = [
                    'delete' => $deleteColumn,  // Column 1 (DELETE or empty)
                    'ein' => $list->ein,
                    'name' => $list->name,
                    'pre_fname' => $list->pre_fname,
                    'pre_lname' => $list->pre_lname,
                    'pre_add' => $list->pre_add,
                    'pre_city' => $list->pre_city,
                    'pre_state' => $list->pre_state,
                    'pre_zip' => $list->pre_zip,
                ];
            }

            $columns = ['Updates', 'EIN', 'Name', 'First Name', 'Last Name', 'Address', 'City', 'State', 'Zip'];
            $callback = function () use ($exportChapterList, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($exportChapterList as $list) {
                    fputcsv($file, [
                        $list['delete'],  // Column 1
                        $list['ein'],
                        $list['name'],
                        $list['pre_fname'],
                        $list['pre_lname'],
                        $list['pre_add'],
                        $list['pre_city'],
                        $list['pre_state'],
                        $list['pre_zip'],
                    ]);
                }
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }
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
        // Get Coordinators Details
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
            // Load Reporting Tree
            $coordinatorData = $this->userController->loadReportingTree($corId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        // Get the base query
        $baseQuery = $this->getBaseChapterQuery();

        // Apply specific conditions for this scenario
        $chapterList = $baseQuery->where('chapters.is_active', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name');

        // Apply conditions
        if ($conditions['founderCondition']) {
            // No additional condition; proceed with the base query
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $chapterList = $chapterList->where('chapters.conference_id', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $chapterList = $chapterList->where('chapters.region_id', '=', $corRegId);
        } else {
            $chapterList = $chapterList->whereIn('chapters.primary_coordinator_id', $inQryArr);
        }

        // Execute query
        $activeChapterList = $chapterList->get();

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];
            foreach ($activeChapterList as $list) {
                if ($list->ein_letter_path != null) {
                    $list->ein_letter = 'YES';
                } else {
                    $list->ein_letter = '';
                }

                $exportChapterList[] = $list;

            }
            $columns = ['Conference', 'Region', 'State', 'Name', 'EIN', 'EIN Leter', 'EIN Path', 'Start Month', 'Start Year', 'First Name', 'Last Name', 'Address',
                'City', 'State', 'Zip', 'Country', 'Phone', 'Email'];
            $callback = function () use ($exportChapterList, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($exportChapterList as $list) {
                    fputcsv($file, [$list->conf,
                        $list->reg_name,
                        $list->state,
                        $list->name,
                        $list->ein,
                        $list->ein_letter,
                        $list->ein_letter_path,
                        $list->start_month_id,
                        $list->start_year,
                        $list->pre_fname,
                        $list->pre_lname,
                        $list->pre_add,
                        $list->pre_city,
                        $list->pre_state,
                        $list->pre_zip,
                        $list->pre_country,
                        $list->pre_phone,
                        $list->pre_email,
                    ]);
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

        // Get the base query
        $baseQuery = $this->getBaseChapterQuery();

        // Apply specific conditions for this scenario
        $chapterList = $baseQuery->where('chapters.is_active', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name');

        // Execute query
        $activeChapterList = $chapterList->get();

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];
            foreach ($activeChapterList as $list) {
                if ($list->ein_letter_path != null) {
                    $list->ein_letter = 'YES';
                } else {
                    $list->ein_letter = '';
                }

                $exportChapterList[] = $list;

            }
            $columns = ['Conference', 'Region', 'State', 'Name', 'EIN', 'EIN Leter', 'EIN Path', 'Start Month', 'Start Year', 'First Name', 'Last Name', 'Address',
                'City', 'State', 'Zip', 'Country', 'Phone', 'Email'];
            $callback = function () use ($exportChapterList, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($exportChapterList as $list) {
                    fputcsv($file, [$list->conf,
                        $list->reg_name,
                        $list->state,
                        $list->name,
                        $list->ein,
                        $list->ein_letter,
                        $list->ein_letter_path,
                        $list->start_month_id,
                        $list->start_year,
                        $list->pre_fname,
                        $list->pre_lname,
                        $list->pre_add,
                        $list->pre_city,
                        $list->pre_state,
                        $list->pre_zip,
                        $list->pre_country,
                        $list->pre_phone,
                        $list->pre_email,
                    ]);
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
        // Get Coordinators Details
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
            // Load Reporting Tree
            $coordinatorData = $this->userController->loadReportingTree($corId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        // Get the base query
        $baseQuery = $this->getBaseChapterQuery();

        // Apply specific conditions for this scenario
        $chapterList = $baseQuery->where('chapters.is_active', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name');

        // Apply conditions
        if ($conditions['founderCondition']) {
            // No additional condition; proceed with the base query
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $chapterList = $chapterList->where('chapters.conference_id', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $chapterList = $chapterList->where('chapters.region_id', '=', $corRegId);
        } else {
            $chapterList = $chapterList->whereIn('chapters.primary_coordinator_id', $inQryArr);
        }

        // Execute query
        $activeChapterList = $chapterList->get();

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];

            // Define checkValues mapping
            $checkValues = [
                1 => 'YES',
                0 => 'NO',
                null => 'NO',  // Treat null as 'NO'
            ];

            foreach ($activeChapterList as $list) {
                $list->new_board_submitted_value = isset($checkValues[$list->new_board_submitted]) ? $checkValues[$list->new_board_submitted] : 'Unknown';
                $list->new_board_active_value = isset($checkValues[$list->new_board_active]) ? $checkValues[$list->new_board_active] : 'Unknown';
                $list->financial_report_received_value = isset($checkValues[$list->financial_report_received]) ? $checkValues[$list->financial_report_received] : 'Unknown';
                $list->financial_report_complete_value = isset($checkValues[$list->financial_report_complete]) ? $checkValues[$list->financial_report_complete] : 'Unknown';

                // Collect the modified list item
                $exportChapterList[] = $list;
            }

            $columns = ['Conference', 'Region', 'State', 'Name', 'Board Report Received', 'Board Report Activated', 'Financial Report Received', 'Financial Report Reviewed',
                'Primary Coordinator', 'Assigned Reviewer'];
            $callback = function () use ($exportChapterList, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($exportChapterList as $list) {
                    fputcsv($file, [$list->conf,
                        $list->reg_name,
                        $list->state,
                        $list->name,
                        $list->new_board_submitted_value,
                        $list->new_board_active_value,
                        $list->financial_report_received_value,
                        $list->financial_report_complete_value,
                        $list->cd_fname.' '.$list->cd_lname,
                        $list->fr_fname.' '.$list->fr_lname,
                    ]);
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

        // Get the base query
        $baseQuery = $this->getBaseChapterQuery();

        // Apply specific conditions for this scenario
        $chapterList = $baseQuery->where('chapters.is_active', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name');

        // Execute query
        $activeChapterList = $chapterList->get();

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];

            // Define checkValues mapping
            $checkValues = [
                1 => 'YES',
                0 => 'NO',
                null => 'NO',  // Treat null as 'NO'
            ];

            foreach ($activeChapterList as $list) {
                $list->new_board_submitted_value = isset($checkValues[$list->new_board_submitted]) ? $checkValues[$list->new_board_submitted] : 'Unknown';
                $list->new_board_active_value = isset($checkValues[$list->new_board_active]) ? $checkValues[$list->new_board_active] : 'Unknown';
                $list->financial_report_received_value = isset($checkValues[$list->financial_report_received]) ? $checkValues[$list->financial_report_received] : 'Unknown';
                $list->financial_report_complete_value = isset($checkValues[$list->financial_report_complete]) ? $checkValues[$list->financial_report_complete] : 'Unknown';

                // Collect the modified list item
                $exportChapterList[] = $list;
            }
            $columns = ['Conference', 'Region', 'State', 'Name', 'Board Report Received', 'Board Report Activated', 'Financial Report Received',
                'Financial Report Reviewed', 'Primary Coordinator'];
            $callback = function () use ($exportChapterList, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($exportChapterList as $list) {
                    fputcsv($file, [$list->conf,
                        $list->reg_name,
                        $list->state,
                        $list->name,
                        $list->new_board_submitted_value,
                        $list->new_board_active_value,
                        $list->financial_report_received_value,
                        $list->financial_report_complete_value,
                        $list->cd_fname.' '.$list->cd_lname,
                    ]);
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

        $user = User::find($request->user()->id);
        $cdDetails = $user->coordinator;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
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

        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseCoordinatorController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $coordinatorList = $baseQuery['query']->get();

        if (count($coordinatorList) > 0) {
            $exportCoordinatorList = [];

            foreach ($coordinatorList as $list) {
                $cdId = $list->id;
                $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                $cdDetails = $baseQuery['cdDetails'];
                $cdId = $baseQuery['cdId'];
                $cdPositionid = $baseQuery['cdPositionid'];
                $regionLongName = $baseQuery['regionLongName'];
                $cdConfId = $baseQuery['cdConfId'];
                $RptFName = $baseQuery['RptFName'];
                $RptLName = $baseQuery['RptLName'];
                $ReportTo = $RptFName.' '.$RptLName;
                $displayPosition = $baseQuery['displayPosition'];
                $secondaryPosition = $baseQuery['secondaryPosition'];
                $cdLeave = ($baseQuery['cdDetails']->on_leave == 1) ? 'YES' : '';

                $rowData = [
                    'Conference' => $cdConfId,
                    'Region' => $regionLongName,
                    'Coordinator Name' => $cdDetails->first_name.' '.$cdDetails->last_name,
                    'Position' => $displayPosition->long_title,
                    'Sec Position' => $secondaryPosition->long_title ?? null,
                    'Email' => $cdDetails->email,
                    'Email2' => $cdDetails->sec_email,
                    'Report To' => $ReportTo,
                    'Address' => $cdDetails->address,
                    'City' => $cdDetails->city,
                    'State' => $cdDetails->state,
                    'Zip' => $cdDetails->zip,
                    'Phone' => $cdDetails->phone,
                    'Phone2' => $cdDetails->alt_phone,
                    'Birthday' => $cdDetails->birthday_month_id.' / '.$cdDetails->birthday_day,
                    'Coordinator Start' => $cdDetails->coordinator_start_date,
                    'Last Promoted' => $cdDetails->last_promoted,
                    'Leave of Absense' => $cdLeave,
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

        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseCoordinatorController->getRetiredBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $coordinatorList = $baseQuery['query']->get();

        if (count($coordinatorList) > 0) {
            $exportCoordinatorList = [];

            foreach ($coordinatorList as $list) {
                $cdId = $list->id;
                $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                $cdDetails = $baseQuery['cdDetails'];
                $cdId = $baseQuery['cdId'];
                $cdPositionid = $baseQuery['cdPositionid'];
                $regionLongName = $baseQuery['regionLongName'];
                $cdConfId = $baseQuery['cdConfId'];
                $RptFName = $baseQuery['RptFName'];
                $RptLName = $baseQuery['RptLName'];
                $ReportTo = $RptFName.' '.$RptLName;
                $displayPosition = $baseQuery['displayPosition'];
                $secondaryPosition = $baseQuery['secondaryPosition'];
                $cdLeave = ($baseQuery['cdDetails']->on_leave == 1) ? 'YES' : '';

                $rowData = [
                    'Conference' => $cdConfId,
                    'Region' => $regionLongName,
                    'Coordinator Name' => $cdDetails->first_name.' '.$cdDetails->last_name,
                    'Position' => $displayPosition->long_title,
                    'Sec Position' => $secondaryPosition->long_title ?? null,
                    'Email' => $cdDetails->email,
                    'Email2' => $cdDetails->sec_email,
                    'Report To' => $ReportTo,
                    'Address' => $cdDetails->address,
                    'City' => $cdDetails->city,
                    'State' => $cdDetails->state,
                    'Zip' => $cdDetails->zip,
                    'Phone' => $cdDetails->phone,
                    'Phone2' => $cdDetails->alt_phone,
                    'Birthday' => $cdDetails->birthday_month_id.' / '.$cdDetails->birthday_day,
                    'Coordinator Start' => $cdDetails->coordinator_start_date,
                    'Last Promoted' => $cdDetails->last_promoted,
                    'Leave of Absense' => $cdLeave,
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
        // Get Coordinators Details
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];

        // Load Reporting Tree
        $coordinatorData = $this->userController->loadReportingTree($corId);
        $inQryArr = $coordinatorData['inQryArr'];

        // Get Coordinator List mapped with login coordinator
        $exportCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.*', 'cp.long_title as position', 'cds.first_name as reporting_fname', 'cds.last_name as reporting_lname', 'rg.short_name as reg_name',
                'cp2.long_title as sec_position')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->leftjoin('coordinator_position as cp2', 'cp2.id', '=', 'cd.sec_position_id')
            ->join('coordinators as cds', 'cds.id', '=', 'cd.report_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();

        if (count($exportCoordinatorList) > 0) {
            $columns = ['Conference', 'Region', 'First Name', 'Last Name', 'Position', 'Sec Position', 'Email', 'Sec Email', 'Reports To', 'Address', 'City',
                'State', 'Zip', 'Phone', 'Alt Phone', 'Birthday', 'Coordinator Start', 'Last Promoted', 'Last UpdatedBy', 'Last UpdatedDate'];
            $callback = function () use ($exportCoordinatorList, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($exportCoordinatorList as $list) {
                    fputcsv($file, [$list->conference_id,
                        $list->reg_name,
                        $list->first_name,
                        $list->last_name,
                        $list->position,
                        $list->sec_position,
                        $list->email,
                        $list->sec_email,
                        $list->reporting_fname.' '.$list->reporting_lname,
                        $list->address,
                        $list->city,
                        $list->state,
                        $list->zip,
                        $list->phone,
                        $list->alt_phone,
                        $list->birthday_month_id.'/'.$list->birthday_day,
                        $list->coordinator_start_date,
                        $list->last_promoted,
                        $list->last_updated_by,
                        $list->last_updated_date,
                    ]);
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
        // Get Coordinators Details
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];

        // Load Reporting Tree
        $coordinatorData = $this->userController->loadReportingTree($corId);
        $inQryArr = $coordinatorData['inQryArr'];

        // Get Coordinator List mapped with login coordinator

        $exportCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.*', 'cp.long_title as position', 'cds.first_name as reporting_fname', 'cds.last_name as reporting_lname', 'rg.short_name as reg_name',
                'cp2.long_title as sec_position')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->leftjoin('coordinator_position as cp2', 'cp2.id', '=', 'cd.sec_position_id')
            ->join('coordinators as cds', 'cds.id', '=', 'cd.report_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->where('cd.is_active', '=', '0')
            ->orderByDesc('cd.zapped_date')
            ->get();

        if (count($exportCoordinatorList) > 0) {
            $columns = ['Conference', 'Region', 'First Name', 'Last Name', 'Position', 'Sec Position', 'Email', 'Sec Email', 'Reports To', 'Address', 'City', 'State',
                'Zip', 'Phone', 'Alt Phone', 'Birthday', 'Coordinator Start', 'Last Promoted', 'Last UpdatedBy', 'Last UpdatedDate',
                'Disband Date', 'Disband Reason'];
            $callback = function () use ($exportCoordinatorList, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($exportCoordinatorList as $list) {
                    fputcsv($file, [$list->conference_id,
                        $list->reg_name,
                        $list->first_name,
                        $list->last_name,
                        $list->position,
                        $list->sec_position,
                        $list->email,
                        $list->sec_email,
                        $list->reporting_fname.' '.$list->reporting_lname,
                        $list->address,
                        $list->city,
                        $list->state,
                        $list->zip,
                        $list->phone,
                        $list->alt_phone,
                        $list->birthday_month_id.'/'.$list->birthday_day,
                        $list->coordinator_start_date,
                        $list->last_promoted,
                        $list->last_updated_by,
                        $list->last_updated_date,
                        $list->zapped_date,
                        $list->reason_retired,
                    ]);
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
    public function indexAppreciation(Request $request): StreamedResponse
    {
        $fileName = 'coordinator_appreciation_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseCoordinatorController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $coordinatorList = $baseQuery['query']->get();

        if (count($coordinatorList) > 0) {
            $exportCoordinatorList = [];

            foreach ($coordinatorList as $list) {
                $cdId = $list->id;
                $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                $cdDetails = $baseQuery['cdDetails'];
                $cdId = $baseQuery['cdId'];
                $cdPositionid = $baseQuery['cdPositionid'];
                $regionLongName = $baseQuery['regionLongName'];
                $cdConfId = $baseQuery['cdConfId'];
                $RptFName = $baseQuery['RptFName'];
                $RptLName = $baseQuery['RptLName'];
                $ReportTo = $RptFName.' '.$RptLName;
                $displayPosition = $baseQuery['displayPosition'];
                $secondaryPosition = $baseQuery['secondaryPosition'];
                $cdLeave = ($baseQuery['cdDetails']->on_leave == 1) ? 'YES' : '';
                $necklace = ($cdDetails->recognition_necklace == 1) ? 'YES' : '';

                $rowData = [
                    'Conference' => $cdConfId,
                    'Region' => $regionLongName,
                    'Coordinator Name' => $cdDetails->first_name.' '.$cdDetails->last_name,
                    'Position' => $displayPosition->long_title,
                    'Sec Position' => $secondaryPosition->long_title ?? null,
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
                    'Necklace' => $necklace,
                    'Top Tier/Other' => $cdDetails->recognition_toptier,
                    'Leave of Absense' => $cdLeave,
                    'Leave Date' => $cdDetails->leave_date,
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
     * Export Board Election Report List
     */
    public function indexBoardElection(Request $request)
    {
        $fileName = 'board_election_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        // Get Coordinators Details
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
        $positionid = $corDetails['position_id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
            // Load Reporting Tree
            $coordinatorData = $this->userController->loadReportingTree($corId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        // Get the base query
        $baseQuery = $this->getBaseIncomingChapterQuery();

        // Apply specific conditions for this scenario
        $chapterList = $baseQuery->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '!=', null)
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name');

        // Apply conditions
        if ($conditions['founderCondition']) {
            // No additional condition; proceed with the base query
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $chapterList = $chapterList->where('chapters.conference_id', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $chapterList = $chapterList->where('chapters.region_id', '=', $corRegId);
        } else {
            $chapterList = $chapterList->whereIn('chapters.primary_coordinator_id', $inQryArr);
        }

        // Execute query
        $activeChapterList = $chapterList->get();

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];

            $statusValues = [
                1 => 'Operating OK',
                4 => 'On Hold Do not Refer',
                5 => 'Probation',
                6 => 'Probation Do Not Refer',
            ];

            foreach ($activeChapterList as $list) {
                $list->status_value = $statusValues[$list->status_id] ?? 'Unknown';
                $exportChapterList[] = $list;
            }

            $columns = ['Conference', 'Region', 'State', 'Chapter Name', 'First Name', 'Last Name', 'email', 'Phone', 'Address', 'City', 'State', 'Zip',
                'AVP First Name', 'AVP Last Name', 'AVP Email', 'AVP Phone', 'AVP Address', 'AVP City', 'AVP State', 'AVP Zip', 'MVP First Name',
                'MVP Last Name', 'MVP Email', 'MVP Phone', 'MVP Address', 'MVP City', 'MVP State', 'MVP Zip', 'Treasurer First Name', 'Treasurer Last Name',
                'Treasurer Email', 'Treasurer Phone', 'Treasurer Address', 'Treasurer City', 'Treasurer State', 'Treasurer Zip', 'Secretary First Name',
                'Secretary Last Name', 'Secretary Email', 'Secretary Phone', 'Secretary Address', 'Secretary City', 'Secretary State', 'Secretary Zip'];
            $callback = function () use ($exportChapterList, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($exportChapterList as $list) {
                    fputcsv($file, [$list->conf,
                        $list->reg_name,
                        $list->state,
                        $list->name,
                        $list->pre_fname,
                        $list->pre_lname,
                        $list->pre_email,
                        $list->pre_phone,
                        $list->pre_add,
                        $list->pre_city,
                        $list->pre_state,
                        $list->pre_zip,
                        $list->avp_fname,
                        $list->avp_lname,
                        $list->avp_email,
                        $list->avp_phone,
                        $list->avp_address,
                        $list->avp_city,
                        $list->avp_state,
                        $list->avp_zip,
                        $list->mvp_fname,
                        $list->mvp_lname,
                        $list->mvp_email,
                        $list->mvp_phone,
                        $list->mvp_address,
                        $list->mvp_city,
                        $list->mvp_state,
                        $list->mvp_zip,
                        $list->trs_fname,
                        $list->trs_lname,
                        $list->trs_email,
                        $list->trs_phone,
                        $list->trs_address,
                        $list->trs_city,
                        $list->trs_state,
                        $list->trs_zip,
                        $list->sec_fname,
                        $list->sec_lname,
                        $list->sec_email,
                        $list->sec_phone,
                        $list->sec_address,
                        $list->sec_city,
                        $list->sec_state,
                        $list->sec_zip,

                    ]);
                }
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }
}
