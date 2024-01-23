<?php

namespace App\Http\Controllers;

use App\Models\CoordinatorDetails;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __construct()
    {
        //$this->middleware('preventBackHistory');
        $this->middleware('auth')->except('logout');
    }

    /**
     * Export Chapter List
     */
    public function indexChapter(Request $request, $id)
    {
        $fileName = 'chapter_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        //Get Coordinator Reporting Tree
        $reportIdList = DB::table('coordinator_reporting_tree as crt')
            ->select('crt.id')
            ->where($sqlLayerId, '=', $corId)
            ->get();
        $inQryStr = '';
        foreach ($reportIdList as $key => $val) {
            $inQryStr .= $val->id.',';
        }
        $inQryStr = rtrim($inQryStr, ',');
        $inQryArr = explode(',', $inQryStr);

        //Get Chapter List mapped with login coordinator
        if ($id == '0') {
            $activeChapterList = DB::table('chapters')
                ->select('chapters.*', 'chapters.conference as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city', 'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
                ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
                ->where('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->whereIn('chapters.primary_coordinator_id', $inQryArr)
                ->orderBy('chapters.name')
                ->orderBy('chapters.name')
                ->get();
        } else {
            $activeChapterList = DB::table('chapters')
                ->select('chapters.*', 'chapters.conference as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city', 'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
                ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
                ->where('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.primary_coordinator_id', '=', $corId)
                ->orderBy('st.state_short_name')
                ->orderBy('chapters.name')
                ->get();
        }

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];
            foreach ($activeChapterList as $list) {
                if ($list->status == 1) {
                    $list->status_value = 'Operating OK';
                }
                if ($list->status == 4) {
                    $list->status_value = 'On Hold Do not Refer';
                }
                if ($list->status == 5) {
                    $list->status_value = 'Probation';
                }
                if ($list->status == 6) {
                    $list->status_value = 'Probation Do Not Refer';
                }
                $chapterId = $list->id;
                //For AVP Details
                $avpDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.phone as avp_phone', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '2')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($avpDeatils) > 0) {
                    $list->avp_fname = $avpDeatils[0]->avp_fname;
                    $list->avp_lname = $avpDeatils[0]->avp_lname;
                    $list->avp_email = $avpDeatils[0]->avp_email;
                    $list->avp_phone = $avpDeatils[0]->avp_phone;
                } else {
                    $list->avp_fname = '';
                    $list->avp_lname = '';
                    $list->avp_email = '';
                    $list->avp_phone = '';
                }
                //For MVP Details
                $mvpDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.phone as mvp_phone', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '3')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($mvpDeatils) > 0) {
                    $list->mvp_fname = $mvpDeatils[0]->mvp_fname;
                    $list->mvp_lname = $mvpDeatils[0]->mvp_lname;
                    $list->mvp_email = $mvpDeatils[0]->mvp_email;
                    $list->mvp_phone = $mvpDeatils[0]->mvp_phone;
                } else {
                    $list->mvp_fname = '';
                    $list->mvp_lname = '';
                    $list->mvp_email = '';
                    $list->mvp_phone = '';
                }
                //For TREASURER Details
                $trsDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.phone as trs_phone', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '4')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($trsDeatils) > 0) {
                    $list->trs_fname = $trsDeatils[0]->trs_fname;
                    $list->trs_lname = $trsDeatils[0]->trs_lname;
                    $list->trs_email = $trsDeatils[0]->trs_email;
                    $list->trs_phone = $trsDeatils[0]->trs_phone;
                } else {
                    $list->trs_fname = '';
                    $list->trs_lname = '';
                    $list->trs_email = '';
                    $list->trs_phone = '';
                }
                //For SECRETARY Details
                $secDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.phone as sec_phone', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '5')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($secDeatils) > 0) {
                    $list->sec_fname = $secDeatils[0]->sec_fname;
                    $list->sec_lname = $secDeatils[0]->sec_lname;
                    $list->sec_email = $secDeatils[0]->sec_email;
                    $list->sec_phone = $secDeatils[0]->sec_phone;
                } else {
                    $list->sec_fname = '';
                    $list->sec_lname = '';
                    $list->sec_email = '';
                    $list->sec_phone = '';
                }

                $exportChapterList[] = $list;
            }

            $columns = ['EIN', 'Conference', 'Region', 'State', 'Name', 'Primary Coordinator', 'Last Updated', 'First Name', 'Last Name', 'Address', 'City', 'State',
                'Zip', 'Country', 'Phone', 'email', 'Inquiries', 'Chapter Email', 'AVP First Name', 'AVP Last Name', 'AVP Email', 'AVP Phone', 'MVP First Name',
                'MVP Last Name', 'MVP Email', 'MVP Phone', 'Treasurer First Name', 'Treasurer Last Name', 'Treasurer Email', 'Treasurer Phone', 'Secretary First Name',
                'Secretary Last Name', 'Secretary Email', 'Secretary Phone', 'Chapter P.O. Box', 'WebpageURL', 'Linked', 'E-Groups', 'Territory', 'InquiriesNote',
                'Status', 'Start Month', 'Start Year', 'Dues Last Paid', 'Members paid for', 'NextRenewal', 'Notes', 'Founder', 'Sistered By', 'FormerName'];
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
                        $list->avp_phone,
                        $list->mvp_fname,
                        $list->mvp_lname,
                        $list->mvp_email,
                        $list->mvp_phone,
                        $list->trs_fname,
                        $list->trs_lname,
                        $list->trs_email,
                        $list->trs_phone,
                        $list->sec_fname,
                        $list->sec_lname,
                        $list->sec_email,
                        $list->sec_phone,
                        $list->po_box,
                        $list->website_url,
                        $list->website_status,
                        $list->egroup,
                        $list->territory,
                        $list->inquiries_note,
                        $list->status_value,
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
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;

        //Get Chapter List mapped with login coordinator
        if ($corId == '1') {
            $zappedChapterList = DB::table('chapters')
                ->select('chapters.*', 'chapters.conference as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city', 'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
                ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
                ->where('chapters.is_active', '=', '0')
                ->where('bd.board_position_id', '=', '1')
                ->orderByDesc('chapters.zap_date')
                ->get();
        } else {
            $zappedChapterList = DB::table('chapters')
                ->select('chapters.*', 'chapters.conference as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city', 'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
                ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
                ->where('chapters.is_active', '=', '0')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.conference', '=', $corConfId)
                ->orderByDesc('chapters.zap_date')
                ->get();
        }
        //print sizeof($zappedChapterList); die;
        if (count($zappedChapterList) > 0) {
            $exportZapChapterList = [];
            foreach ($zappedChapterList as $list) {

                if ($list->status == 1) {
                    $list->status_value = 'Operating OK';
                }
                if ($list->status == 4) {
                    $list->status_value = 'On Hold Do not Refer';
                }
                if ($list->status == 5) {
                    $list->status_value = 'Probation';
                }
                if ($list->status == 6) {
                    $list->status_value = 'Probation Do Not Refer';
                }
                $chapterId = $list->id;
                //For AVP Details
                $avpDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '0')
                    ->where('bd.board_position_id', '=', '2')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($avpDeatils) > 0) {
                    $list->avp_fname = $avpDeatils[0]->avp_fname;
                    $list->avp_lname = $avpDeatils[0]->avp_lname;
                    $list->avp_email = $avpDeatils[0]->avp_email;
                } else {
                    $list->avp_fname = '';
                    $list->avp_lname = '';
                    $list->avp_email = '';
                }
                //For MVP Details
                $mvpDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '0')
                    ->where('bd.board_position_id', '=', '3')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($mvpDeatils) > 0) {
                    $list->mvp_fname = $mvpDeatils[0]->mvp_fname;
                    $list->mvp_lname = $mvpDeatils[0]->mvp_lname;
                    $list->mvp_email = $mvpDeatils[0]->mvp_email;
                } else {
                    $list->mvp_fname = '';
                    $list->mvp_lname = '';
                    $list->mvp_email = '';
                }
                //For TREASURER Details
                $trsDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '0')
                    ->where('bd.board_position_id', '=', '4')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($trsDeatils) > 0) {
                    $list->trs_fname = $trsDeatils[0]->trs_fname;
                    $list->trs_lname = $trsDeatils[0]->trs_lname;
                    $list->trs_email = $trsDeatils[0]->trs_email;
                } else {
                    $list->trs_fname = '';
                    $list->trs_lname = '';
                    $list->trs_email = '';
                }
                //For SECRETARY Details
                $secDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '0')
                    ->where('bd.board_position_id', '=', '5')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($secDeatils) > 0) {
                    $list->sec_fname = $secDeatils[0]->sec_fname;
                    $list->sec_lname = $secDeatils[0]->sec_lname;
                    $list->sec_email = $secDeatils[0]->sec_email;
                } else {
                    $list->sec_fname = '';
                    $list->sec_lname = '';
                    $list->sec_email = '';
                }

                $exportZapChapterList[] = $list;
            }
            $columns = ['EIN', 'Conference', 'Region', 'State', 'Name', 'Primary Coordinator', 'Last Updated', 'First Name', 'Last Name', 'Address', 'City',
                'State', 'Zip', 'Country', 'Phone', 'email', 'Inquiries', 'Chapter Email', 'AVP First Name', 'AVP Last Name', 'AVP Email', 'MVP First Name',
                'MVP Last Name', 'MVP Email', 'Treasurer First Name', 'Treasurer Last Name', 'Treasurer Email', 'Secretary First Name', 'Secretary Last Name',
                'Secretary Email', 'Chapter P.O. Box', 'WebpageURL', 'Linked', 'E-Groups', 'Territory', 'InquiriesNote', 'Status', 'Start Month', 'Start Year',
                'Dues Last Paid', 'Members paid for', 'NextRenewal', 'Notes', 'Founder', 'Sistered By', 'FormerName', 'Disband Date', 'Disband Reason'];
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
                        $list->status_value,
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
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;

        $currentYear = date('Y');
        $currentMonth = date('m');

        if ($corId == '1') {
            $ReRegList = DB::table('chapters')
                ->select('chapters.*', 'chapters.conference as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname',
                    'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city',
                    'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
                ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
                ->where('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.next_renewal_year', '=', $currentYear)
                ->where('chapters.start_month_id', '<', $currentMonth)
                ->orwhere('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.next_renewal_year', '<', $currentYear)
                ->orderBy('chapters.next_renewal_year')
                ->orderBy('chapters.start_month_id')
                ->get();

        } else {
            $ReRegList = DB::table('chapters')
                ->select('chapters.*', 'chapters.conference as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname',
                    'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city',
                    'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
                ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
                ->where('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.next_renewal_year', '=', $currentYear)
                ->where('chapters.start_month_id', '<', $currentMonth)
                ->where('chapters.conference', '=', $corConfId)
                ->orwhere('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.next_renewal_year', '<', $currentYear)
                ->where('chapters.conference', '=', $corConfId)
                ->orderBy('chapters.next_renewal_year')
                ->orderBy('chapters.start_month_id')
                ->get();
        }

        if (count($ReRegList) >= 0) {
            $exportReRegList = [];
            foreach ($ReRegList as $list) {
                if ($list->status == 1) {
                    $list->status_value = 'Operating OK';
                }
                if ($list->status == 4) {
                    $list->status_value = 'On Hold Do not Refer';
                }
                if ($list->status == 5) {
                    $list->status_value = 'Probation';
                }
                if ($list->status == 6) {
                    $list->status_value = 'Probation Do Not Refer';
                }
                $chapterId = $list->id;

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
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;

        $currentYear = date('Y');
        $currentMonth = date('m');

        if ($corId == '1') {
            $ReRegList = DB::table('chapters')
                ->select('chapters.*', 'chapters.conference as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname',
                    'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city',
                    'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
                ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
                ->where('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.next_renewal_year', '=', $currentYear)
                ->where('chapters.start_month_id', '<', $currentMonth)
                ->orwhere('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.next_renewal_year', '<', $currentYear)
                ->orderBy('chapters.next_renewal_year')
                ->orderBy('chapters.start_month_id')
                ->get();

        } else {
            $ReRegList = DB::table('chapters')
                ->select('chapters.*', 'chapters.conference as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname',
                    'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city',
                    'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
                ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
                ->where('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.next_renewal_year', '=', $currentYear)
                ->where('chapters.start_month_id', '<', $currentMonth)
                ->where('chapters.conference', '=', $corConfId)
                ->orwhere('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.next_renewal_year', '<', $currentYear)
                ->orderBy('chapters.next_renewal_year')
                ->orderBy('chapters.start_month_id')
                ->get();
        }

        if (count($ReRegList) >= 0) {
            $exportReRegList = [];
            foreach ($ReRegList as $list) {
                if ($list->status == 1) {
                    $list->status_value = 'Operating OK';
                }
                if ($list->status == 4) {
                    $list->status_value = 'On Hold Do not Refer';
                }
                if ($list->status == 5) {
                    $list->status_value = 'Probation';
                }
                if ($list->status == 6) {
                    $list->status_value = 'Probation Do Not Refer';
                }
                $chapterId = $list->id;

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
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $activeChapterList = DB::table('chapters')
            ->select('chapters.*', 'chapters.conference as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname',
                'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city',
                'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];
            foreach ($activeChapterList as $list) {
                $chapterId = $list->id;
                //For AVP Details
                $avpDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '2')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($avpDeatils) > 0) {
                    $list->avp_fname = $avpDeatils[0]->avp_fname;
                    $list->avp_lname = $avpDeatils[0]->avp_lname;
                    $list->avp_email = $avpDeatils[0]->avp_email;
                } else {
                    $list->avp_fname = '';
                    $list->avp_lname = '';
                    $list->avp_email = '';
                }
                //For MVP Details
                $mvpDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '3')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($mvpDeatils) > 0) {
                    $list->mvp_fname = $mvpDeatils[0]->mvp_fname;
                    $list->mvp_lname = $mvpDeatils[0]->mvp_lname;
                    $list->mvp_email = $mvpDeatils[0]->mvp_email;
                } else {
                    $list->mvp_fname = '';
                    $list->mvp_lname = '';
                    $list->mvp_email = '';
                }
                //For TREASURER Details
                $trsDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '4')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($trsDeatils) > 0) {
                    $list->trs_fname = $trsDeatils[0]->trs_fname;
                    $list->trs_lname = $trsDeatils[0]->trs_lname;
                    $list->trs_email = $trsDeatils[0]->trs_email;
                } else {
                    $list->trs_fname = '';
                    $list->trs_lname = '';
                    $list->trs_email = '';
                }
                //For SECRETARY Details
                $secDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '5')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($secDeatils) > 0) {
                    $list->sec_fname = $secDeatils[0]->sec_fname;
                    $list->sec_lname = $secDeatils[0]->sec_lname;
                    $list->sec_email = $secDeatils[0]->sec_email;
                } else {
                    $list->sec_fname = '';
                    $list->sec_lname = '';
                    $list->sec_email = '';
                }

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
                        $list->status,
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
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $activeChapterList = DB::table('chapters')
            ->select('chapters.*', 'chapters.conference as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname',
                'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city',
                'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->where('chapters.is_active', '=', '0')
            ->where('bd.board_position_id', '=', '1')
            ->orderByDesc('chapters.zap_date')
            ->get();

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];
            foreach ($activeChapterList as $list) {
                $chapterId = $list->id;
                //For AVP Details
                $avpDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '0')
                    ->where('bd.board_position_id', '=', '2')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($avpDeatils) > 0) {
                    $list->avp_fname = $avpDeatils[0]->avp_fname;
                    $list->avp_lname = $avpDeatils[0]->avp_lname;
                    $list->avp_email = $avpDeatils[0]->avp_email;
                } else {
                    $list->avp_fname = '';
                    $list->avp_lname = '';
                    $list->avp_email = '';
                }
                //For MVP Details
                $mvpDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '0')
                    ->where('bd.board_position_id', '=', '3')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($mvpDeatils) > 0) {
                    $list->mvp_fname = $mvpDeatils[0]->mvp_fname;
                    $list->mvp_lname = $mvpDeatils[0]->mvp_lname;
                    $list->mvp_email = $mvpDeatils[0]->mvp_email;
                } else {
                    $list->mvp_fname = '';
                    $list->mvp_lname = '';
                    $list->mvp_email = '';
                }
                //For TREASURER Details
                $trsDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '0')
                    ->where('bd.board_position_id', '=', '4')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($trsDeatils) > 0) {
                    $list->trs_fname = $trsDeatils[0]->trs_fname;
                    $list->trs_lname = $trsDeatils[0]->trs_lname;
                    $list->trs_email = $trsDeatils[0]->trs_email;
                } else {
                    $list->trs_fname = '';
                    $list->trs_lname = '';
                    $list->trs_email = '';
                }
                //For SECRETARY Details
                $secDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.board_position_id as positionid')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '0')
                    ->where('bd.board_position_id', '=', '5')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($secDeatils) > 0) {
                    $list->sec_fname = $secDeatils[0]->sec_fname;
                    $list->sec_lname = $secDeatils[0]->sec_lname;
                    $list->sec_email = $secDeatils[0]->sec_email;
                } else {
                    $list->sec_fname = '';
                    $list->sec_lname = '';
                    $list->sec_email = '';
                }

                $exportChapterList[] = $list;
            }
            $columns = ['EIN', 'Conference', 'Region', 'State', 'Name', 'Primary Coordinator', 'Last Updated', 'First Name', 'Last Name', 'Address', 'City', 'State',
                'Zip', 'Country', 'Phone', 'email', 'Inquiries', 'Chapter Email', 'AVP First Name', 'AVP Last Name', 'AVP Email', 'MVP First Name', 'MVP Last Name',
                'MVP Email', 'Treasurer First Name', 'Treasurer Last Name', 'Treasurer Email', 'Secretary First Name', 'Secretary Last Name', 'Secretary Email',
                'Chapter P.O. Box', 'WebpageURL', 'Linked', 'E-Groups', 'Territory', 'InquiriesNote', 'Status', 'Start Month', 'Start Year', 'Dues Last Paid',
                'Members paid for', 'NextRenewal', 'Notes', 'Founder', 'Sistered By', 'FormerName', 'Disband Date', 'Disband Reason'];
            $callback = function () use ($exportChapterList, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($exportChapterList as $list) {
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
                        $list->status,
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
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        //Get Coordinator Reporting Tree
        $reportIdList = DB::table('coordinator_reporting_tree as crt')
            ->select('crt.id')
            ->where($sqlLayerId, '=', $corId)
            ->get();
        $inQryStr = '';
        foreach ($reportIdList as $key => $val) {
            $inQryStr .= $val->id.',';
        }
        $inQryStr = rtrim($inQryStr, ',');
        $inQryArr = explode(',', $inQryStr);

        $activeChapterList = DB::table('chapters')
            ->select('chapters.*', 'chapters.conference as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname',
                'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city',
                'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->where('chapters.is_active', '=', '1')
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];
            foreach ($activeChapterList as $list) {
                if ($list->ein_letter_path != null) {
                    $list->ein_letter = 'YES';
                } else {
                    $list->ein_letter = '';
                }
                $chapterId = $list->id;

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
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $activeChapterList = DB::table('chapters')
            ->select('chapters.*', 'chapters.conference as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname',
                'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city',
                'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
                //->orderBy('chapters.zap_date','DESC')
            ->get();

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];
            foreach ($activeChapterList as $list) {
                if ($list->ein_letter_path != null) {
                    $list->ein_letter = 'YES';
                } else {
                    $list->ein_letter = '';
                }
                $chapterId = $list->id;

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
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        //Get Coordinator Reporting Tree
        $reportIdList = DB::table('coordinator_reporting_tree as crt')
            ->select('crt.id')
            ->where($sqlLayerId, '=', $corId)
            ->get();
        $inQryStr = '';
        foreach ($reportIdList as $key => $val) {
            $inQryStr .= $val->id.',';
        }
        $inQryStr = rtrim($inQryStr, ',');
        $inQryArr = explode(',', $inQryStr);

        $activeChapterList = DB::table('chapters')
            ->select('chapters.*', 'chapters.conference as conf', 'rg.short_name as reg_name', 'chapters.new_board_submitted as new_board_submitted',
                'chapters.new_board_active as new_board_active', 'chapters.financial_report_received as financial_report_received',
                'chapters.financial_report_complete as financial_report_complete', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname',
                'st.state_short_name as state', 'fr.reviewer_id as reviewer_id')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftjoin('financial_report as fr', 'chapters.id', '=', 'fr.chapter_id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->where('chapters.is_active', '=', '1')
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];
            foreach ($activeChapterList as $list) {
                if ($list->new_board_submitted == '1') {
                    $list->new_board_submitted = 'YES';
                } else {
                    $list->new_board_submitted = 'NO';
                }
                if ($list->new_board_active == '1') {
                    $list->new_board_active = 'YES';
                } else {
                    $list->new_board_active = 'NO';
                }
                if ($list->financial_report_received == '1') {
                    $list->financial_report_received = 'YES';
                } else {
                    $list->financial_report_received = 'NO';
                }
                if ($list->financial_report_complete == '1') {
                    $list->financial_report_complete = 'YES';
                } else {
                    $list->financial_report_complete = 'NO';
                }

                $chapterId = $list->id;

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
                        $list->new_board_submitted,
                        $list->new_board_active,
                        $list->financial_report_received,
                        $list->financial_report_complete,
                        $list->cd_fname.' '.$list->cd_lname,
                        $list->reviewer_id,
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
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $activeChapterList = DB::table('chapters')
            ->select('chapters.*', 'chapters.conference as conf', 'rg.short_name as reg_name', 'chapters.new_board_submitted as new_board_submitted',
                'chapters.new_board_active as new_board_active', 'chapters.financial_report_received as financial_report_received',
                'chapters.financial_report_complete as financial_report_complete', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->where('chapters.is_active', '=', '1')
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];
            foreach ($activeChapterList as $list) {
                if ($list->new_board_submitted == '1') {
                    $list->new_board_submitted = 'YES';
                } else {
                    $list->new_board_submitted = 'NO';
                }
                if ($list->new_board_active == '1') {
                    $list->new_board_active = 'YES';
                } else {
                    $list->new_board_active = 'NO';
                }
                if ($list->financial_report_received == '1') {
                    $list->financial_report_received = 'YES';
                } else {
                    $list->financial_report_received = 'NO';
                }
                if ($list->financial_report_complete == '1') {
                    $list->financial_report_complete = 'YES';
                } else {
                    $list->financial_report_complete = 'NO';
                }

                $chapterId = $list->id;

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
                        $list->new_board_submitted,
                        $list->new_board_active,
                        $list->financial_report_received,
                        $list->financial_report_complete,
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
     * Export Coordinator List
     */
    public function indexCoordinator(Request $request, $id)
    {
        $fileName = 'coordinator_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        //Get Coordinator Reporting Tree
        $reportIdList = DB::table('coordinator_reporting_tree as crt')
            ->select('crt.id')
            ->where($sqlLayerId, '=', $corId)
            ->get();
        $inQryStr = '';
        foreach ($reportIdList as $key => $val) {
            $inQryStr .= $val->id.',';
        }
        $inQryStr = rtrim($inQryStr, ',');
        $inQryArr = explode(',', $inQryStr);

        //Get Chapter List mapped with login coordinator

        //Get Coordinator List mapped with login coordinator
        if ($id == '0') {
            $exportCoordinatorList = DB::table('coordinator_details as cd')
                ->select('cd.*', 'cp.long_title as position', 'cd.first_name as reporting_fname', 'cd.last_name as reporting_lname', 'rg.short_name as reg_name')
                ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
                ->join('coordinator_details as cds', 'cds.coordinator_id', '=', 'cd.report_id')
                ->join('region as rg', 'rg.id', '=', 'cd.region_id')
                ->where('cd.is_active', '=', '1')
                ->whereIn('cd.report_id', $inQryArr)
                ->orderBy('cd.first_name')
                ->get();
        } else {
            $exportCoordinatorList = DB::table('coordinator_details as cd')
                ->select('cd.*', 'cp.long_title as position', 'cd.first_name as reporting_fname', 'cd.last_name as reporting_lname', 'rg.short_name as reg_name')
                ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
                ->join('coordinator_details as cds', 'cds.coordinator_id', '=', 'cd.report_id')
                ->join('region as rg', 'rg.id', '=', 'cd.region_id')
                ->where('cd.is_active', '=', '1')
                ->where('cd.report_id', '=', $corId)
                ->orderBy('cd.first_name')
                ->get();
        }
        if (count($exportCoordinatorList) > 0) {
            $columns = ['Id', 'Conference', 'Region', 'First Name', 'Last Name', 'Position', 'Sec Position', 'Email', 'Sec Email', 'Report Id', 'Address', 'City',
                'State', 'Zip', 'Phone', 'Alt Phone', 'Birthday Month', 'Birthday Day', 'Coordinator Start', 'Last Promoted', 'Last UpdatedBy', 'Last UpdatedDate'];
            $callback = function () use ($exportCoordinatorList, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($exportCoordinatorList as $list) {
                    fputcsv($file, [$list->coordinator_id,
                        $list->conference_id,
                        $list->reg_name,
                        $list->first_name,
                        $list->last_name,
                        $list->position,
                        $this->coordinatorPosition($list->sec_position_id),
                        $list->email,
                        $list->sec_email,
                        $this->get_reporting_coordinator($list->report_id),
                        // $list->reporting_fname." ".$list->reporting_lname,
                        $list->address,
                        $list->city,
                        $list->state,
                        $list->zip,
                        $list->phone,
                        $list->alt_phone,
                        $list->birthday_month_id,
                        $list->birthday_day,
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
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        //Get Coordinator Reporting Tree
        $reportIdList = DB::table('coordinator_reporting_tree as crt')
            ->select('crt.id')
            ->where($sqlLayerId, '=', $corId)
            ->get();
        $inQryStr = '';
        foreach ($reportIdList as $key => $val) {
            $inQryStr .= $val->id.',';
        }
        $inQryStr = rtrim($inQryStr, ',');
        $inQryArr = explode(',', $inQryStr);

        //Get Coordinator List mapped with login coordinator
        $exportCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.*', 'cp.long_title as position', 'cd.first_name as reporting_fname', 'cd.last_name as reporting_lname', 'rg.short_name as reg_name')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('coordinator_details as cds', 'cds.coordinator_id', '=', 'cd.report_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.conference_id')
            ->orderBy('reg_name')
            ->orderBy('cd.first_name')
            ->get();

        if (count($exportCoordinatorList) > 0) {
            $columns = ['Conference', 'Region', 'First Name', 'Last Name', 'Position', 'Sec Position', 'Email', 'Sec Email', 'Reports To', 'Address', 'City',
                'State', 'Zip', 'Phone', 'Alt Phone', 'Birthday Month', 'Birthday Day', 'Coordinator Start', 'Last Promoted', 'Last UpdatedBy', 'Last UpdatedDate'];
            $callback = function () use ($exportCoordinatorList, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($exportCoordinatorList as $list) {
                    fputcsv($file, [$list->conference_id,
                        $list->reg_name,
                        $list->first_name,
                        $list->last_name,
                        $list->position,
                        $this->coordinatorPosition($list->sec_position_id),
                        $list->email,
                        $list->sec_email,
                        $this->get_reporting_coordinator($list->report_id),
                        $list->address,
                        $list->city,
                        $list->state,
                        $list->zip,
                        $list->phone,
                        $list->alt_phone,
                        $list->birthday_month_id,
                        $list->birthday_day,
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
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        //Get Coordinator Reporting Tree
        $reportIdList = DB::table('coordinator_reporting_tree as crt')
            ->select('crt.id')
            ->where($sqlLayerId, '=', $corId)
            ->get();
        $inQryStr = '';
        foreach ($reportIdList as $key => $val) {
            $inQryStr .= $val->id.',';
        }
        $inQryStr = rtrim($inQryStr, ',');
        $inQryArr = explode(',', $inQryStr);

        //Get Coordinator List mapped with login coordinator

        $exportCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.*', 'cp.long_title as position', 'cd.first_name as reporting_fname', 'cd.last_name as reporting_lname', 'rg.short_name as reg_name')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('coordinator_details as cds', 'cds.coordinator_id', '=', 'cd.report_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->where('cd.is_active', '=', '0')
            ->orderByDesc('cd.zapped_date')
            ->get();

        if (count($exportCoordinatorList) > 0) {
            $columns = ['Conference', 'Region', 'First Name', 'Last Name', 'Position', 'Sec Position', 'Email', 'Sec Email', 'Reports To', 'Address', 'City', 'State',
                'Zip', 'Phone', 'Alt Phone', 'Birthday Month', 'Birthday Day', 'Coordinator Start', 'Last Promoted', 'Last UpdatedBy', 'Last UpdatedDate',
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
                        $this->coordinatorPosition($list->sec_position_id),
                        $list->email,
                        $list->sec_email,
                        $this->get_reporting_coordinator($list->report_id),
                        $list->address,
                        $list->city,
                        $list->state,
                        $list->zip,
                        $list->phone,
                        $list->alt_phone,
                        $list->birthday_month_id,
                        $list->birthday_day,
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
     * Export Retired Coordinator List
     */
    public function indexRetiredCoordinator(Request $request): StreamedResponse
    {

        $fileName = 'coordinator_retire_'.date('Y-m-d').'.csv';
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $tasks = CoordinatorDetails::select('*')
            ->where([
                ['is_active', '=', 0],
                ['conference_id', '=', $corConfId],
            ])
            ->orderByDesc('coordinator_details.zapped_date')
            ->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        $_result = DB::table('coordinator_position as p')->select('*')->get();
        $_temp = [];
        foreach ($_result as $_v) {
            $_temp[$_v->id] = $_v->long_title;
        }
        $_result = DB::table('region as r')->select('*')->get();
        $_temp = [];
        foreach ($_result as $_v) {
            $_temp[$_v->id] = $_v->long_name;
        }

        //var_dump($_temp);die;
        $columns = ['Conference', 'Region', 'First Name', 'Last Name', 'Position', 'Sec Position', 'Email', 'Sec Email', 'Report Id', 'Address', 'City', 'State',
            'Zip', 'Phone', 'Alt Phone', 'Birthday Month', 'Birthday Day', 'Coordinator Start', 'Last Promoted', 'Last UpdatedBy', 'Last UpdatedDate', 'Retire Date', 'Reason'];

        $callback = function () use ($tasks, $columns, $_temp) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $row['Conference'] = $task->conference_id;
                $row['Region'] = empty($_temp[$task->region_id]) ? '' : $_temp[$task->region_id];
                $row['First Name'] = $task->first_name;
                $row['Last Name'] = $task->last_name;
                $row['Position'] = empty($_temp[$task->position_id]) ? '' : $_temp[$task->position_id];
                $row['Sec Position'] = empty($_temp[$task->sec_position_id]) ? '' : $_temp[$task->sec_position_id];
                $row['Email'] = $task->email;
                $row['Sec Email'] = $task->sec_email;
                $row['Report Id'] = $task->report_id;
                $row['Address'] = $task->address;
                $row['City'] = $task->city;
                $row['State'] = $task->state;
                $row['Zip'] = $task->zip;
                $row['Phone'] = $task->phone;
                $row['Alt Phone'] = $task->alt_phone;
                $row['Birthday Month'] = $task->birthday_month_id;
                $row['Birthday Day'] = $task->birthday_day;
                $row['Coordinator Start'] = $task->coordinator_start_date;
                $row['Last Promoted'] = $task->last_promoted;
                $row['Last UpdatedBy'] = $task->last_updated_by;
                $row['Last UpdatedDate'] = $task->last_updated_date;
                $row['Retire Date'] = $task->zapped_date;
                $row['Reason'] = $task->reason_retired;

                fputcsv($file, [$row['Conference'], $row['Region'], $row['First Name'], $row['Last Name'], $row['Position'], $row['Sec Position'], $row['Email'], $row['Sec Email'], $row['Report Id'], $row['Address'], $row['City'], $row['State'], $row['Zip'], $row['Phone'], $row['Alt Phone'], $row['Birthday Month'], $row['Birthday Day'], $row['Coordinator Start'], $row['Last Promoted'], $row['Last UpdatedBy'], $row['Last UpdatedDate'], $row['Retire Date'], $row['Reason']]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function coordinatorPosition($id)
    {
        if ($id != '0' && $id != '') {
            $data = DB::table('coordinator_position')->select('*')->where('id', $id)->first();

            return $data->long_title;
        } else {
            return '';
        }
    }

    public function get_reporting_coordinator($id)
    {
        if ($id != '0' && $id != '') {
            $data = DB::table('coordinator_details')->select('*')->where('coordinator_id', $id)->first();

            return $data->first_name.' '.$data->last_name;
        } else {
            return '';
        }
    }

    /**
     * Export Coordinator Appreciation List
     */
    public function indexAppreciation(Request $request): StreamedResponse
    {
        $fileName = 'coordinator_appreciation_'.date('Y-m-d').'.csv';
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $exportCoordinatorList = CoordinatorDetails::select('*')
            ->where([
                ['is_active', '=', 1],
                ['conference_id', '=', $corConfId],
            ])
            ->orderBy('coordinator_details.coordinator_start_date')
            ->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $_result = DB::table('coordinator_position as p')->select('*')->get();
        $_temp = [];
        foreach ($_result as $_v) {
            $_temp[$_v->id] = $_v->long_title;
        }
        $columns = ['Conference', 'Region', 'First Name', 'Last Name', 'Start Date', 'Position', 'Secondary Position', '<1 Year', '1 Year', '2 Years',
            '3 Years', '4 Years', '5 Years', '6 Years', '7 Years', '8 Years', '9 Years', 'Necklace', 'Top Tier/Other'];
        $callback = function () use ($exportCoordinatorList, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($exportCoordinatorList as $list) {
                $row['Conference'] = $list->conference_id;
                $row['Region'] = $list->region_id;
                $row['First Name'] = $list->first_name;
                $row['Last Name'] = $list->last_name;
                $row['Start Date'] = $list->coordinator_start_date;
                $row['Position'] = $list->position_id;
                $row['Secondary Position'] = $list->sec_position_id;
                $row['< 1 Year'] = $list->recognition_year0;
                $row['1 Year'] = $list->recognition_year1;
                $row['2 Years'] = $list->recognition_year2;
                $row['3 Years'] = $list->recognition_year3;
                $row['4 Years'] = $list->recognition_year4;
                $row['5 Years'] = $list->recognition_year5;
                $row['6 Years'] = $list->recognition_year6;
                $row['7 Years'] = $list->recognition_year7;
                $row['8 Years'] = $list->recognition_year8;
                $row['9 Years'] = $list->recognition_year9;
                $row['Necklace'] = $list->recognition_necklace;
                $row['Top Tier/Other'] = $list->recognition_toptier;

                fputcsv($file, [$row['Conference'], $row['Region'], $row['First Name'], $row['Last Name'], $row['Start Date'], $row['Position'], $row['Secondary Position'],
                    $row['< 1 Year'], $row['1 Year'], $row['2 Years'], $row['3 Years'], $row['4 Years'], $row['5 Years'], $row['6 Years'], $row['7 Years'], $row['8 Years'],
                    $row['9 Years'], $row['Necklace'], $row['Top Tier/Other']]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export Chapter Coordinator List
     */
    public function indexChapterCoordinator(Request $request): RedirectResponse
    {
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');

        date_default_timezone_set('America/Los_Angeles');
        $today = date('Y-m-d');

        header('Content-Disposition: attachment; filename=chapter_coordinator_export_'.$today.'.csv');

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        fputcsv($output, ['Conference', 'Region', 'State', 'Name', 'CC', 'ACC', 'RC', 'ARC', 'SC', 'AC', 'Big']);

        $chapter_array = null;
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

        //Get Coordinator Reporting Tree
        $reportIdList = DB::table('coordinator_reporting_tree as crt')
            ->select('crt.id')
            ->where($sqlLayerId, '=', $corId)
            ->get();
        $inQryStr = '';
        foreach ($reportIdList as $key => $val) {
            $inQryStr .= $val->id.',';
        }
        $inQryStr = rtrim($inQryStr, ',');
        $inQryArr = explode(',', $inQryStr);

        //Get Chapter List mapped with login coordinator
        $chapter_array = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'rg.short_name as region')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->orderBy('chapters.name')
            ->get();

        if (count($chapter_array) > 0) {
            $row_count = count($chapter_array);

            for ($row = 0; $row < $row_count; $row++) {
                $id = $chapter_array[$row]->primary_coordinator_id;
                $reportingList = DB::table('coordinator_reporting_tree')
                    ->select('*')
                    ->where('id', '=', $id)
                    ->get();

                foreach ($reportingList as $key => $value) {
                    $reportingList[$key] = (array) $value;
                }
                $filterReportingList = array_filter($reportingList[0]);
                unset($filterReportingList['id']);
                unset($filterReportingList['layer0']);
                $filterReportingList = array_reverse($filterReportingList);
                foreach ($filterReportingList as $key => $val) {
                    $coordinatorDetails = DB::table('coordinator_details as cd')
                        ->select('cd.first_name as first_name', 'cd.last_name as last_name', 'cp.short_title as position')
                        ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                        ->where('cd.coordinator_id', $val)
                        ->get();

                    $coordinator_array[] = $coordinatorDetails->toArray();

                }
                $cord_row_count = count($coordinator_array);
                $stacked_coord_array = null;

                for ($pos_row = 7; $pos_row > 0; $pos_row--) {
                    $stacked_coord_array[$pos_row] = '';
                }

                for ($pos_row = 7; $pos_row > 0; $pos_row--) {
                    $position_found = false;

                    for ($cord_row = 0; $cord_row < $cord_row_count; $cord_row++) {
                        switch ($pos_row) {
                            case 1:
                                if (isset($coordinator_array[$cord_row][0]) && $coordinator_array[$cord_row][0]->position == 'BS' && ! $position_found) {
                                    $stacked_coord_array[1] = $coordinator_array[$cord_row][0]->first_name.' '.$coordinator_array[$cord_row][0]->last_name;
                                    $position_found = true;
                                }
                                break;
                            case 2:
                                if (isset($coordinator_array[$cord_row][0]) && $coordinator_array[$cord_row][0]->position == 'AC' && ! $position_found) {
                                    $stacked_coord_array[2] = $coordinator_array[$cord_row][0]->first_name.' '.$coordinator_array[$cord_row][0]->last_name;
                                    $position_found = true;
                                }
                                break;
                            case 3:
                                if (isset($coordinator_array[$cord_row][0]) && $coordinator_array[$cord_row][0]->position == 'SC' && ! $position_found) {
                                    $stacked_coord_array[3] = $coordinator_array[$cord_row][0]->first_name.' '.$coordinator_array[$cord_row][0]->last_name;
                                    $position_found = true;
                                }
                                break;
                            case 4:
                                if (isset($coordinator_array[$cord_row][0]) && $coordinator_array[$cord_row][0]->position == 'ARC' && ! $position_found) {
                                    $stacked_coord_array[4] = $coordinator_array[$cord_row][0]->first_name.' '.$coordinator_array[$cord_row][0]->last_name;
                                    $position_found = true;
                                }
                                break;
                            case 5:
                                if (isset($coordinator_array[$cord_row][0]) && $coordinator_array[$cord_row][0]->position == 'RC' && ! $position_found) {
                                    $stacked_coord_array[5] = $coordinator_array[$cord_row][0]->first_name.' '.$coordinator_array[$cord_row][0]->last_name;
                                    $position_found = true;
                                }
                                break;
                            case 6:
                                if (isset($coordinator_array[$cord_row][0]) && $coordinator_array[$cord_row][0]->position == 'ACC' && ! $position_found) {
                                    $stacked_coord_array[6] = $coordinator_array[$cord_row][0]->first_name.' '.$coordinator_array[$cord_row][0]->last_name;
                                    $position_found = true;
                                }
                                break;
                            case 7:
                                if (isset($coordinator_array[$cord_row][0]) && $coordinator_array[$cord_row][0]->position == 'CC' && ! $position_found) {
                                    $stacked_coord_array[7] = $coordinator_array[$cord_row][0]->first_name.' '.$coordinator_array[$cord_row][0]->last_name;
                                    $position_found = true;
                                }
                                break;
                        }
                    }

                }

                unset($coordinator_array);
                fputcsv($output, [
                    $chapter_array[$row]->conference,
                    $chapter_array[$row]->region,
                    $chapter_array[$row]->state,
                    $chapter_array[$row]->name,
                    $stacked_coord_array['7'],
                    $stacked_coord_array['6'],
                    $stacked_coord_array['5'],
                    $stacked_coord_array['4'],
                    $stacked_coord_array['3'],
                    $stacked_coord_array['2'],
                    $stacked_coord_array['1']]);
            }

            fclose($output);
            exit($output);
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
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $positionid = $corDetails['position_id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        //Get Coordinator Reporting Tree
        $reportIdList = DB::table('coordinator_reporting_tree as crt')
            ->select('crt.id')
            ->where($sqlLayerId, '=', $corId)
            ->get();
        $inQryStr = '';
        foreach ($reportIdList as $key => $val) {
            $inQryStr .= $val->id.',';
        }
        $inQryStr = rtrim($inQryStr, ',');
        $inQryArr = explode(',', $inQryStr);

        //Get Chapter List mapped with login coordinator
        $activeChapterList = DB::table('chapters')
            ->select('chapters.*', 'chapters.conference as conf', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname',
                'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city',
                'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('incoming_board_member as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.conference', '=', $corConfId)
            ->orderBy('st.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        if (count($activeChapterList) > 0) {
            $exportChapterList = [];
            foreach ($activeChapterList as $list) {
                if ($list->status == 1) {
                    $list->status_value = 'Operating OK';
                }
                if ($list->status == 4) {
                    $list->status_value = 'On Hold Do not Refer';
                }
                if ($list->status == 5) {
                    $list->status_value = 'Probation';
                }
                if ($list->status == 6) {
                    $list->status_value = 'Probation Do Not Refer';
                }
                $chapterId = $list->id;
                //For AVP Details
                $avpDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.phone as avp_phone',
                        'bd.street_address as avp_address', 'bd.city as avp_city', 'bd.state as avp_state', 'bd.zip as avp_zip', 'bd.board_position_id as positionid')
                    ->leftJoin('incoming_board_member as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '2')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($avpDeatils) > 0) {
                    $list->avp_fname = $avpDeatils[0]->avp_fname;
                    $list->avp_lname = $avpDeatils[0]->avp_lname;
                    $list->avp_email = $avpDeatils[0]->avp_email;
                    $list->avp_phone = $avpDeatils[0]->avp_phone;
                    $list->avp_address = $avpDeatils[0]->avp_address;
                    $list->avp_city = $avpDeatils[0]->avp_city;
                    $list->avp_state = $avpDeatils[0]->avp_state;
                    $list->avp_zip = $avpDeatils[0]->avp_zip;
                } else {
                    $list->avp_fname = '';
                    $list->avp_lname = '';
                    $list->avp_email = '';
                    $list->avp_phone = '';
                    $list->avp_address = '';
                    $list->avp_city = '';
                    $list->avp_state = '';
                    $list->avp_zip = '';
                }
                //For MVP Details
                $mvpDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.phone as mvp_phone',
                        'bd.street_address as mvp_address', 'bd.city as mvp_city', 'bd.state as mvp_state', 'bd.zip as mvp_zip', 'bd.board_position_id as positionid')
                    ->leftJoin('incoming_board_member as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '3')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($mvpDeatils) > 0) {
                    $list->mvp_fname = $mvpDeatils[0]->mvp_fname;
                    $list->mvp_lname = $mvpDeatils[0]->mvp_lname;
                    $list->mvp_email = $mvpDeatils[0]->mvp_email;
                    $list->mvp_phone = $mvpDeatils[0]->mvp_phone;
                    $list->mvp_address = $mvpDeatils[0]->mvp_address;
                    $list->mvp_city = $mvpDeatils[0]->mvp_city;
                    $list->mvp_state = $mvpDeatils[0]->mvp_state;
                    $list->mvp_zip = $mvpDeatils[0]->mvp_zip;
                } else {
                    $list->mvp_fname = '';
                    $list->mvp_lname = '';
                    $list->mvp_email = '';
                    $list->mvp_phone = '';
                    $list->mvp_address = '';
                    $list->mvp_city = '';
                    $list->mvp_state = '';
                    $list->mvp_zip = '';
                }
                //For TREASURER Details
                $trsDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.phone as trs_phone',
                        'bd.street_address as trs_address', 'bd.city as trs_city', 'bd.state as trs_state', 'bd.zip as trs_zip', 'bd.board_position_id as positionid')
                    ->leftJoin('incoming_board_member as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '4')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($trsDeatils) > 0) {
                    $list->trs_fname = $trsDeatils[0]->trs_fname;
                    $list->trs_lname = $trsDeatils[0]->trs_lname;
                    $list->trs_email = $trsDeatils[0]->trs_email;
                    $list->trs_phone = $trsDeatils[0]->trs_phone;
                    $list->trs_address = $trsDeatils[0]->trs_address;
                    $list->trs_city = $trsDeatils[0]->trs_city;
                    $list->trs_state = $trsDeatils[0]->trs_state;
                    $list->trs_zip = $trsDeatils[0]->trs_zip;
                } else {
                    $list->trs_fname = '';
                    $list->trs_lname = '';
                    $list->trs_email = '';
                    $list->trs_phone = '';
                    $list->trs_address = '';
                    $list->trs_city = '';
                    $list->trs_state = '';
                    $list->trs_zip = '';
                }
                //For SECRETARY Details
                $secDeatils = DB::table('chapters')
                    ->select('chapters.id', 'bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.phone as sec_phone',
                        'bd.street_address as sec_address', 'bd.city as sec_city', 'bd.state as sec_state', 'bd.zip as sec_zip', 'bd.board_position_id as positionid')
                    ->leftJoin('incoming_board_member as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '5')
                    ->where('chapters.id', '=', $chapterId)
                    ->get()->toArray();
                if (count($secDeatils) > 0) {
                    $list->sec_fname = $secDeatils[0]->sec_fname;
                    $list->sec_lname = $secDeatils[0]->sec_lname;
                    $list->sec_email = $secDeatils[0]->sec_email;
                    $list->sec_phone = $secDeatils[0]->sec_phone;
                    $list->sec_address = $secDeatils[0]->sec_address;
                    $list->sec_city = $secDeatils[0]->sec_city;
                    $list->sec_state = $secDeatils[0]->sec_state;
                    $list->sec_zip = $secDeatils[0]->sec_zip;
                } else {
                    $list->sec_fname = '';
                    $list->sec_lname = '';
                    $list->sec_email = '';
                    $list->sec_phone = '';
                    $list->sec_address = '';
                    $list->sec_city = '';
                    $list->sec_state = '';
                    $list->sec_zip = '';
                }

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

    /**
     * Export Chapter Awards List
     */
    public function indexChapterAwardList(Request $request)
    {
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        $today = date('Y-m-d');
        header('Content-Disposition: attachment; filename=chapter_award_export_'.$today.'.csv');
        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');
        // output the column headings
        fputcsv($output, ['State', 'Chapter', 'Award', 'Approved']);
        $award_array = null;
        //Get Coordinators Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;

        $coordinator_id = $corDetails['coordinator_id'];
        $conference_id = $corDetails['conference_id'];
        $layer_id = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$layer_id;
        //Get Coordinator Reporting Tree
        $reportIdList = DB::table('coordinator_reporting_tree as crt')
            ->select('crt.id')
            ->where($sqlLayerId, '=', $coordinator_id)
            ->get();
        $inQryStr = '';
        foreach ($reportIdList as $key => $val) {
            $inQryStr .= $val->id.',';
        }
        $inQryStr = rtrim($inQryStr, ',');
        $chapterList = DB::table('chapters as ch')
            ->select('ch.id as id', 'ch.name as name', 'ch.primary_coordinator_id as pc_id', 'fr.reviewer_id as reviewer_id', 'cd.coordinator_id as cord_id', 'cd.first_name as reviewer_first_name', 'cd.last_name as reviewer_last_name', 'st.state_short_name as state', 'fr.award_1_nomination_type as award_1_type', 'fr.award_2_nomination_type as award_2_type', 'fr.award_3_nomination_type as award_3_type', 'fr.award_4_nomination_type as award_4_type', 'fr.award_5_nomination_type as award_5_type', 'fr.check_award_1_approved as award_1_approved', 'fr.check_award_2_approved as award_2_approved', 'fr.check_award_3_approved as award_3_approved', 'fr.check_award_4_approved as award_4_approved', 'fr.check_award_5_approved as award_5_approved')
            ->join('state as st', 'ch.state', '=', 'st.id')
            ->leftJoin('financial_report as fr', 'fr.chapter_id', '=', 'ch.id')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'fr.reviewer_id')
            ->where('ch.is_active', 1)
            ->whereIn('ch.primary_coordinator_id', explode(',', $inQryStr))
            ->orderBy('ch.state')
            ->orderBy('ch.name')
            ->get();

        $award_array = json_decode(json_encode($chapterList), true);
        $rowcount = count($award_array);
        // loop over the rows, outputting them
        for ($row = 0; $row < $rowcount; $row++) {
            for ($award = 1; $award <= 5; $award++) {
                if ($award_array[$row]['award_'.$award.'_type'] > 0) {
                    if ($award_array[$row]['award_'.$award.'_approved']) {
                        $award_approved = 'Yes';
                    } else {
                        $award_approved = 'No';
                    }

                    switch ($award_array[$row]['award_'.$award.'_type']) {
                        case 1:
                            $award_type = 'Outstanding Specific Service Project';
                            break;
                        case 2:
                            $award_type = 'Outstanding Overall Service Program';
                            break;
                        case 3:
                            $award_type = "Outstanding Children's Activity";
                            break;
                        case 4:
                            $award_type = 'Outstanding Spirit';
                            break;
                        case 5:
                            $award_type = 'Outstanding Chapter';
                            break;
                        case 6:
                            $award_type = 'Outstanding New Chapter';
                            break;
                        case 7:
                            $award_type = 'Other Outstanding Award';
                            break;
                    }
                    fputcsv($output, [
                        $award_array[$row]['state'],
                        $award_array[$row]['name'],

                        $award_type,
                        $award_approved,
                    ]);
                }
            }
        }
        fclose($output);
        exit($output);
    }
}
