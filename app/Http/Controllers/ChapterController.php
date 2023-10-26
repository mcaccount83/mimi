<?php

namespace App\Http\Controllers;

use App\Chapter;
use App\Coordinator;
use App\FinancialReport;
use App\Mail\ChapersUpdateListAdmin;
use App\Mail\ChapersUpdatePrimaryCoor;
use App\Mail\PaymentsM2MChapterThankYou;
use App\Mail\PaymentsReRegChapterThankYou;
use App\Mail\PaymentsReRegLate;
use App\Mail\PaymentsReRegReminder;
use App\Mail\PaymentsSustainingChapterThankYou;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;

class ChapterController extends Controller
{
    public function __construct()
    {
        $this->middleware('preventBackHistory');
        $this->middleware('auth')->except('logout', 'chapterLinks');
    }

    /**
     * Display the Active chapter list mapped with login coordinator
     */
    public function list()
    {
        //Get Coordinators Details
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];

        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        Session::put('positionid', $positionId);
        Session::put('secpositionid', $secPositionId);
        Session::put('corconfid', $corConfId);

        if ($positionId <= 7 || $positionId == 25) {
            if ($corId == 25 || $positionId == 25) {
                //Get Coordinator Reporting Tree
                $reportIdList = DB::table('coordinator_reporting_tree as crt')
                    ->select('crt.id')
                    ->where('crt.layer1', '=', '6')
                    ->get();
            } else {
                //Get Coordinator Reporting Tree
                $reportIdList = DB::table('coordinator_reporting_tree as crt')
                    ->select('crt.id')
                    ->where($sqlLayerId, '=', $corId)
                    ->get();
            }
            $inQryStr = '';
            foreach ($reportIdList as $key => $val) {
                $inQryStr .= $val->id.',';
            }
            $inQryStr = rtrim($inQryStr, ',');
            $inQryArr = explode(',', $inQryStr);

        }
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->whereIn('chapters.primary_coordinator_id', $inQryArr)
            ->orderBy('st.state_short_name', 'ASC')
            ->orderBy('chapters.name', 'ASC')
            ->get();

        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $chapterList = DB::table('chapters')
                    ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
                    ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                    ->where('chapters.is_active', '=', '1')
                    ->where('bd.board_position_id', '=', '1')
                    ->where('chapters.primary_coordinator_id', $corId)
                    ->orderBy('st.state_short_name', 'ASC')
                    ->orderBy('chapters.name', 'ASC')
                    ->get();
            }

        } else {
            $checkBoxStatus = '';
        }

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

        return view('chapters.list')->with($data);
    }

    /**
     * Add New chapter list (View)
     */
    public function create()
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];

        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id', 'ASC')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id', 'ASC')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name', 'ASC')
            ->get();
        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', '=', $corConfId)
            ->where('cd.position_id', '<=', '6')
            ->where('cd.position_id', '>=', '1')
            ->where('cd.is_active', '=', '1')
            ->orWhere('cd.position_id', '=', '25')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name', 'ASC')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = date('m');
        $firstCharacter = $currentMonth[0];
        if ($firstCharacter == '0') {
            $currentMonth = $currentMonth[1];
        }

        $currentYear = date('Y');
        $data = ['currentMonth' => $currentMonth, 'currentYear' => $currentYear, 'regionList' => $regionList, 'primaryCoordinatorList' => $primaryCoordinatorList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('chapters.create')->with($data);
    }

    /**
     * Add New chapter list (Store)
     */
    public function store(Request $request)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $input = request()->all();

        if (isset($input['ch_linkstatus'])) {
            $input['ch_linkstatus'];
        } else {
            $input['ch_linkstatus'] = 0;
        }

        DB::beginTransaction();
        try {
            $chapterId = DB::table('chapters')->insertGetId(
                ['conference' => $corConfId,
                    'name' => $input['ch_name'],
                    'state' => $input['ch_state'],
                    'country' => $input['ch_country'],
                    'region' => $input['ch_region'],
                    'ein' => $input['ch_ein'],
                    'status' => $input['ch_status'],
                    'territory' => $input['ch_boundariesterry'],
                    'additional_info' => $input['ch_addinfo'],
                    'website_link_status' => $input['ch_linkstatus'],
                    'email' => $input['ch_email'],
                    'inquiries_contact' => $input['ch_inqemailcontact'],
                    'inquiries_note' => $input['ch_inqnote'],
                    'egroup' => $input['ch_onlinediss'],
                    'po_box' => $input['ch_pobox'],
                    'notes' => $input['ch_notes'],
                    'start_month_id' => $input['ch_founddate'],
                    'start_year' => $input['ch_foundyear'],
                    'next_renewal_year' => $input['ch_foundyear'] + 1,
                    'primary_coordinator_id' => $input['ch_primarycor'],
                    'founders_name' => $input['ch_pre_fname'].' '.$input['ch_pre_lname'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'is_active' => 1]
            );

            $financial = DB::table('financial_report')->insert(
                ['chapter_id' => $chapterId]
            );

            //President Info
            if (isset($input['ch_pre_fname']) && isset($input['ch_pre_lname']) && isset($input['ch_pre_email'])) {
                $userId = DB::table('users')->insertGetId(
                    ['first_name' => $input['ch_pre_fname'],
                        'last_name' => $input['ch_pre_lname'],
                        'email' => $input['ch_pre_email'],
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1]
                );

                $boardIdArr = DB::table('board_details')
                    ->select('board_details.board_id')
                    ->orderBy('board_details.board_id', 'DESC')
                    ->limit(1)
                    ->get();
                $boardId = $boardIdArr[0]->board_id + 1;

                $board = DB::table('board_details')->insert(
                    ['user_id' => $userId,
                        'board_id' => $boardId,
                        'first_name' => $input['ch_pre_fname'],
                        'last_name' => $input['ch_pre_lname'],
                        'email' => $input['ch_pre_email'],
                        'password' => Hash::make('TempPass4You'),
                        'remember_token' => '',
                        'board_position_id' => 1,
                        'chapter_id' => $chapterId,
                        'street_address' => $input['ch_pre_street'],
                        'city' => $input['ch_pre_city'],
                        'state' => $input['ch_pre_state'],
                        'zip' => $input['ch_pre_zip'],
                        'country' => 'USA',
                        'phone' => $input['ch_pre_phone'],
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s'),
                        'is_active' => 1]
                );
            }

            //AVP Info
            if (isset($input['ch_avp_fname']) && isset($input['ch_avp_lname']) && isset($input['ch_avp_email'])) {
                $userId = DB::table('users')->insertGetId(
                    ['first_name' => $input['ch_avp_fname'],
                        'last_name' => $input['ch_avp_lname'],
                        'email' => $input['required|ch_avp_email'],
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1]
                );

                $boardIdArr = DB::table('board_details')
                    ->select('board_details.board_id')
                    ->orderBy('board_details.board_id', 'DESC')
                    ->limit(1)
                    ->get();
                $boardId = $boardIdArr[0]->board_id + 1;

                $board = DB::table('board_details')->insert(
                    ['user_id' => $userId,
                        'board_id' => $boardId,
                        'first_name' => $input['ch_avp_fname'],
                        'last_name' => $input['ch_avp_lname'],
                        'email' => $input['required|ch_avp_email'],
                        'password' => Hash::make('TempPass4You'),
                        'remember_token' => '',
                        'board_position_id' => 2,
                        'chapter_id' => $chapterId,
                        'street_address' => $input['ch_avp_street'],
                        'city' => $input['ch_avp_city'],
                        'state' => $input['ch_avp_state'],
                        'zip' => $input['ch_avp_zip'],
                        'country' => 'USA',
                        'phone' => $input['ch_avp_phone'],
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s'),
                        'is_active' => 1]
                );
            }
            //MVP Info
            if (isset($input['ch_mvp_fname']) && isset($input['ch_mvp_lname']) && isset($input['ch_mvp_email'])) {
                $userId = DB::table('users')->insertGetId(
                    ['first_name' => $input['ch_mvp_fname'],
                        'last_name' => $input['ch_mvp_lname'],
                        'email' => $input['ch_mvp_email'],
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1]
                );

                $boardIdArr = DB::table('board_details')
                    ->select('board_details.board_id')
                    ->orderBy('board_details.board_id', 'DESC')
                    ->limit(1)
                    ->get();
                $boardId = $boardIdArr[0]->board_id + 1;

                $board = DB::table('board_details')->insert(
                    ['user_id' => $userId,
                        'board_id' => $boardId,
                        'first_name' => $input['ch_mvp_fname'],
                        'last_name' => $input['ch_mvp_lname'],
                        'email' => $input['ch_mvp_email'],
                        'password' => Hash::make('TempPass4You'),
                        'remember_token' => '',
                        'board_position_id' => 3,
                        'chapter_id' => $chapterId,
                        'street_address' => $input['ch_mvp_street'],
                        'city' => $input['ch_mvp_city'],
                        'state' => $input['ch_mvp_state'],
                        'zip' => $input['ch_mvp_zip'],
                        'country' => 'USA',
                        'phone' => $input['ch_mvp_phone'],
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s'),
                        'is_active' => 1]
                );
            }
            //TREASURER Info
            if (isset($input['ch_trs_fname']) && isset($input['ch_trs_lname']) && isset($input['ch_trs_email'])) {
                $userId = DB::table('users')->insertGetId(
                    ['first_name' => $input['ch_trs_fname'],
                        'last_name' => $input['ch_trs_lname'],
                        'email' => $input['ch_trs_email'],
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1]
                );

                $boardIdArr = DB::table('board_details')
                    ->select('board_details.board_id')
                    ->orderBy('board_details.board_id', 'DESC')
                    ->limit(1)
                    ->get();
                $boardId = $boardIdArr[0]->board_id + 1;

                $board = DB::table('board_details')->insert(
                    ['user_id' => $userId,
                        'board_id' => $boardId,
                        'first_name' => $input['ch_trs_fname'],
                        'last_name' => $input['ch_trs_lname'],
                        'email' => $input['ch_trs_email'],
                        'password' => Hash::make('TempPass4You'),
                        'remember_token' => '',
                        'board_position_id' => 4,
                        'chapter_id' => $chapterId,
                        'street_address' => $input['ch_trs_street'],
                        'city' => $input['ch_trs_city'],
                        'state' => $input['ch_trs_state'],
                        'zip' => $input['ch_trs_zip'],
                        'country' => 'USA',
                        'phone' => $input['ch_trs_phone'],
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s'),
                        'is_active' => 1]
                );
            }
            //Secretary Info
            if (isset($input['ch_sec_fname']) && isset($input['ch_sec_lname']) && isset($input['ch_sec_email'])) {
                $userId = DB::table('users')->insertGetId(
                    ['first_name' => $input['ch_sec_fname'],
                        'last_name' => $input['ch_sec_lname'],
                        'email' => $input['ch_sec_email'],
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1]
                );

                $boardIdArr = DB::table('board_details')
                    ->select('board_details.board_id')
                    ->orderBy('board_details.board_id', 'DESC')
                    ->limit(1)
                    ->get();
                $boardId = $boardIdArr[0]->board_id + 1;

                $board = DB::table('board_details')->insert(
                    ['user_id' => $userId,
                        'board_id' => $boardId,
                        'first_name' => $input['ch_sec_fname'],
                        'last_name' => $input['ch_sec_lname'],
                        'email' => $input['ch_sec_email'],
                        'password' => Hash::make('TempPass4You'),
                        'remember_token' => '',
                        'board_position_id' => 5,
                        'chapter_id' => $chapterId,
                        'street_address' => $input['ch_sec_street'],
                        'city' => $input['ch_sec_city'],
                        'state' => $input['ch_sec_state'],
                        'zip' => $input['ch_sec_zip'],
                        'country' => 'USA',
                        'phone' => $input['ch_sec_phone'],
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s'),
                        'is_active' => 1]
                );

            }
            $cordInfo = DB::table('coordinator_details')
                ->select('first_name', 'last_name', 'email')
                ->where('is_active', '=', '1')
                ->where('coordinator_id', $input['ch_primarycor'])
                ->get();
            $state = DB::table('state')
                ->select('state_short_name')
                ->where('id', $input['ch_state'])
                ->get();

            $to_email = [
                $cordInfo[0]->email,
                $input['ch_pre_email'],
            ];

            $mailData = [
                'chapter_name' => $input['ch_name'],
                'chapter_state' => $state[0]->state_short_name,
                'cor_fname' => $cordInfo[0]->first_name,
                'cor_lname' => $cordInfo[0]->last_name,
                'updated_by' => date('Y-m-d H:i:s'),
            ];

            Mail::send('emails.chapteradd', $mailData, function ($message) use ($to_email) {
                $message->to($to_email, 'MOMS Club')->subject('New Chapter Added');
            });

            $to_email2 = 'listadmin@momsclub.org';

            $mailData2 = [
                'chapter_name' => $input['ch_name'],
                'chapter_state' => $state[0]->state_short_name,
                'email' => $input['ch_email'],
                'cor_fname' => $cordInfo[0]->first_name,
                'cor_lname' => $cordInfo[0]->last_name,
                'pfirst' => $input['ch_pre_fname'],
                'plast' => $input['ch_pre_lname'],
                'pemail' => $input['ch_pre_email'],
                'afirst' => $input['ch_avp_fname'],
                'alast' => $input['ch_avp_lname'],
                'aemail' => $input['ch_avp_email'],
                'mfirst' => $input['ch_mvp_fname'],
                'mlast' => $input['ch_mvp_lname'],
                'memail' => $input['ch_mvp_email'],
                'tfirst' => $input['ch_trs_fname'],
                'tlast' => $input['ch_trs_lname'],
                'temail' => $input['ch_trs_email'],
                'sfirst' => $input['ch_sec_fname'],
                'slast' => $input['ch_sec_lname'],
                'semail' => $input['ch_sec_email'],
                'conf' => $corConfId,
                'updated_by' => date('Y-m-d H:i:s'),
            ];

            Mail::send('emails.listadminchapteradd', $mailData2, function ($message2) use ($to_email2) {
                $message2->to($to_email2, 'MOMS Club')->subject('Chapter Add Request');
            });

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            // Log the error
            Log::error($e);

            return redirect('/chapter/list')->with('fail', 'Something went wrong, Please try again..');
        }

        return redirect('/chapter/list')->with('success', 'Chapter created successfully');
    }

    public function edit($id)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corConfId = $corDetails['conference_id'];
        $corId = $corDetails['coordinator_id'];
        $positionid = $corDetails['position_id'];
        $financial_report_array = FinancialReport::find($id);
        if ($financial_report_array) {
            $reviewComplete = $financial_report_array['review_complete'];
        } else {
            $reviewComplete = null;
        }
        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('board_details as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        $corConfId = $chapterList[0]->conference;
        $corId = $chapterList[0]->primary_coordinator_id;
        $AVPDetails = DB::table('board_details as bd')
            ->select('bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id', 'bd.street_address as avp_addr', 'bd.city as avp_city', 'bd.zip as avp_zip', 'bd.phone as avp_phone', 'bd.state as avp_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '2')
            ->get();
        if (count($AVPDetails) == 0) {
            $AVPDetails[0] = ['avp_fname' => '', 'avp_lname' => '', 'avp_email' => '', 'avp_addr' => '', 'avp_city' => '', 'avp_zip' => '', 'avp_phone' => '', 'avp_state' => '', 'user_id' => ''];
            $AVPDetails = json_decode(json_encode($AVPDetails));
        }

        $MVPDetails = DB::table('board_details as bd')
            ->select('bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id', 'bd.street_address as mvp_addr', 'bd.city as mvp_city', 'bd.zip as mvp_zip', 'bd.phone as mvp_phone', 'bd.state as mvp_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '3')
            ->get();
        if (count($MVPDetails) == 0) {
            $MVPDetails[0] = ['mvp_fname' => '', 'mvp_lname' => '', 'mvp_email' => '', 'mvp_addr' => '', 'mvp_city' => '', 'mvp_zip' => '', 'mvp_phone' => '', 'mvp_state' => '', 'user_id' => ''];
            $MVPDetails = json_decode(json_encode($MVPDetails));
        }

        $TRSDetails = DB::table('board_details as bd')
            ->select('bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id', 'bd.street_address as trs_addr', 'bd.city as trs_city', 'bd.zip as trs_zip', 'bd.phone as trs_phone', 'bd.state as trs_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '4')
            ->get();
        if (count($TRSDetails) == 0) {
            $TRSDetails[0] = ['trs_fname' => '', 'trs_lname' => '', 'trs_email' => '', 'trs_addr' => '', 'trs_city' => '', 'trs_zip' => '', 'trs_phone' => '', 'trs_state' => '', 'user_id' => ''];
            $TRSDetails = json_decode(json_encode($TRSDetails));
        }

        $SECDetails = DB::table('board_details as bd')
            ->select('bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.board_position_id', 'bd.street_address as sec_addr', 'bd.city as sec_city', 'bd.zip as sec_zip', 'bd.phone as sec_phone', 'bd.state as sec_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '5')
            ->get();
        if (count($SECDetails) == 0) {
            $SECDetails[0] = ['sec_fname' => '', 'sec_lname' => '', 'sec_email' => '', 'sec_addr' => '', 'sec_city' => '', 'sec_zip' => '', 'sec_phone' => '', 'sec_state' => '', 'user_id' => ''];
            $SECDetails = json_decode(json_encode($SECDetails));
        }

        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id', 'ASC')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id', 'ASC')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name', 'ASC')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->where('id', '>=', 0)
            ->orderBy('conference_name', 'ASC')
            ->get();
        $chapterEmailList = DB::table('board_details as bd')
            ->select('bd.email as bor_email')
            ->where('bd.chapter_id', '=', $id)
            ->get();
        $emailListCord = '';
        foreach ($chapterEmailList as $val) {
            $email = $val->bor_email;
            $escaped_email = str_replace("'", "\\'", $email);
            if ($emailListCord == '') {
                $emailListCord = $escaped_email;
            } else {
                $emailListCord .= ';'.$escaped_email;
            }
        }

        $cc_string = '';
        $reportingList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('id', '=', $chapterList[0]->primary_coordinator_id)
            ->get();
        foreach ($reportingList as $key => $value) {
            $reportingList[$key] = (array) $value;
        }
        $filterReportingList = array_filter($reportingList[0]);
        unset($filterReportingList['id']);
        unset($filterReportingList['layer0']);
        $filterReportingList = array_reverse($filterReportingList);
        $str = '';
        $array_rows = count($filterReportingList);
        $down_line_email = '';
        foreach ($filterReportingList as $key => $val) {
            // if($corId != $val && $val >1){
            if ($val > 1) {
                $corList = DB::table('coordinator_details as cd')
                    ->select('cd.email as cord_email')
                    ->where('cd.coordinator_id', '=', $val)
                    ->where('cd.is_active', '=', 1)
                    ->get();
                if (count($corList) > 0) {
                    if ($down_line_email == '') {
                        $down_line_email = $corList[0]->cord_email;
                    } else {
                        $down_line_email .= ';'.$corList[0]->cord_email;
                    }
                }
            }
        }
        $cc_string = '?cc='.$down_line_email;

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', '=', $corConfId)
            ->where('cd.position_id', '<=', '6')
            ->where('cd.position_id', '>=', '1')
            ->where('cd.is_active', '=', '1')
            ->orWhere('cd.position_id', '=', '25')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name', 'ASC')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $chapterList[0]->start_month_id;

        $data = ['positionid' => $positionid, 'corId' => $corId, 'reviewComplete' => $reviewComplete, 'emailListCord' => $emailListCord, 'cc_string' => $cc_string, 'currentMonth' => $currentMonth, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'chapterList' => $chapterList, 'regionList' => $regionList, 'confList' => $confList, 'primaryCoordinatorList' => $primaryCoordinatorList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('chapters.edit')->with($data);
    }

    //chapterupdate
    public function update(Request $request, $id)
    {

        $presInfoPre = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'bd.street_address as street', 'bd.city as city', 'bd.zip as zip', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.id', $id)
            ->orderBy('chapters.id', 'DESC')
            ->get();

        $AVPInfoPre = DB::table('chapters')
            ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '2')
            ->where('chapters.id', $id)
            ->get();

        $MVPInfoPre = DB::table('chapters')
            ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '3')
            ->where('chapters.id', $id)
            ->get();

        $tresInfoPre = DB::table('chapters')
            ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '4')
            ->where('chapters.id', $id)
            ->get();

        $secInfoPre = DB::table('chapters')
            ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '5')
            ->where('chapters.id', $id)
            ->get();

        $chapterId = $id;
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $positionid = $corDetails['position_id'];
        if ($positionid < 5) {
            $ch_state = $request->get('ch_hid_state');
            $ch_country = $request->get('ch_hid_country');
            $ch_region = $request->get('ch_hid_region');
            $ch_status = $request->get('ch_hid_status');
            $ch_pcid = $request->get('ch_hid_primarycor');
        } else {
            $ch_state = $request->get('ch_state');
            $ch_country = $request->get('ch_country');
            $ch_region = $request->get('ch_region');
            $ch_status = $request->get('ch_status');
            $ch_pcid = $request->get('ch_primarycor');
        }
        if ($positionid == 7) {
            $ch_month = $request->get('ch_founddate');
            $ch_foundyear = $request->get('ch_foundyear');
        } else {
            $ch_month = $request->get('ch_hid_founddate');
            $ch_foundyear = $request->get('ch_hid_foundyear');
        }

        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $chapter = Chapter::find($chapterId);
        DB::beginTransaction();
        try {
            $chapter->name = $request->get('ch_name');
            $chapter->state = $ch_state;
            $chapter->country = $ch_country;
            $chapter->region = $ch_region;
            $chapter->ein = $request->get('ch_ein');
            //$chapter->ein_letter = (int) $request->has('ch_ein_letter');
            $chapter->ein_letter_path = $request->get('ch_ein_letter_path');
            $chapter->status = $ch_status;
            $chapter->territory = $request->get('ch_boundariesterry');
            $chapter->additional_info = $request->get('ch_addinfo');
            $chapter->website_url = $request->get('ch_website');
            $chapter->website_link_status = $request->get('ch_linkstatus');
            $chapter->email = $request->get('ch_email');
            $chapter->inquiries_contact = $request->get('ch_inqemailcontact');
            $chapter->inquiries_note = $request->get('ch_inqnote');
            $chapter->egroup = $request->get('ch_onlinediss');
            $chapter->social1 = $request->get('ch_social1');
            $chapter->social2 = $request->get('ch_social2');
            $chapter->social3 = $request->get('ch_social3');
            $chapter->po_box = $request->get('ch_pobox');
            $chapter->notes = $request->get('ch_notes');
            $chapter->reg_notes = $request->get('ch_regnotes');
            $chapter->po_box = $request->get('ch_pobox');
            $chapter->former_name = $request->get('ch_preknown');
            $chapter->sistered_by = $request->get('ch_sistered');
            $chapter->start_month_id = $ch_month;
            $chapter->start_year = $ch_foundyear;
            // $chapter->next_renewal_year = $request->get('ch_foundyear')+1;
            $chapter->primary_coordinator_id = $ch_pcid;
            //$chapter->founders_name = $request->get('ch_pre_fname').' '.$request->get('ch_pre_lname');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');

            $chapter->save();

            //$financial_report_array = FinancialReport::find($chapterId);
            //if(!empty($financial_report_array))
            //{
            //    DB::update('UPDATE financial_report SET reviewer_id = ? where chapter_id = ?', [null,$chapterId]);
            //}

            //President Info
            if ($request->get('ch_pre_fname') != '' && $request->get('ch_pre_lname') != '' && $request->get('ch_pre_email') != '') {
                $PREDetails = DB::table('board_details')
                    ->select('board_id', 'user_id')
                    ->where('chapter_id', '=', $chapterId)
                    ->where('board_position_id', '=', '1')
                    ->get();
                if (count($PREDetails) != 0) {
                    $userId = $PREDetails[0]->user_id;
                    $boardId = $PREDetails[0]->board_id;

                    $user = User::find($userId);
                    $user->first_name = $request->get('ch_pre_fname');
                    $user->last_name = $request->get('ch_pre_lname');
                    $user->email = $request->get('ch_pre_email');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    DB::table('board_details')
                        ->where('board_id', $boardId)
                        ->update(['first_name' => $request->get('ch_pre_fname'),
                            'last_name' => $request->get('ch_pre_lname'),
                            'email' => $request->get('ch_pre_email'),
                            'street_address' => $request->get('ch_pre_street'),
                            'city' => $request->get('ch_pre_city'),
                            'state' => $request->get('ch_pre_state'),
                            'zip' => $request->get('ch_pre_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_pre_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                }
            }
            //AVP Info
            // if($request->get('ch_avp_fname') !='' && $request->get('ch_avp_lname') !='' && $request->get('ch_avp_email') !='')
            // {
            $AVPDetails = DB::table('board_details')
                ->select('board_id', 'user_id')
                ->where('chapter_id', '=', $chapterId)
                ->where('board_position_id', '=', '2')
                ->get();
            if (count($AVPDetails) != 0) {
                $userId = $AVPDetails[0]->user_id;
                $boardId = $AVPDetails[0]->board_id;
                if ($request->get('AVPVacant') == 'on') {
                    //Delete Details of Board memebers
                    DB::table('board_details')
                        ->where('board_id', $boardId)
                        ->delete();
                    //Delete Details of Board memebers from users table
                    DB::table('users')
                        ->where('id', $userId)
                        ->delete();
                } else {
                    $user = User::find($userId);
                    $user->first_name = $request->get('ch_avp_fname');
                    $user->last_name = $request->get('ch_avp_lname');
                    $user->email = $request->get('ch_avp_email');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    DB::table('board_details')
                        ->where('board_id', $boardId)
                        ->update(['first_name' => $request->get('ch_avp_fname'),
                            'last_name' => $request->get('ch_avp_lname'),
                            'email' => $request->get('ch_avp_email'),
                            'street_address' => $request->get('ch_avp_street'),
                            'city' => $request->get('ch_avp_city'),
                            'state' => $request->get('ch_avp_state'),
                            'zip' => $request->get('ch_avp_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_avp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                }
            } else {
                if ($request->get('AVPVacant') != 'on') {
                    $userId = DB::table('users')->insertGetId(
                        ['first_name' => $request->get('ch_avp_fname'),
                            'last_name' => $request->get('ch_avp_lname'),
                            'email' => $request->get('ch_avp_email'),
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1]
                    );

                    $boardIdArr = DB::table('board_details')
                        ->select('board_details.board_id')
                        ->orderBy('board_details.board_id', 'DESC')
                        ->limit(1)
                        ->get();
                    $boardId = $boardIdArr[0]->board_id + 1;

                    $board = DB::table('board_details')->insert(
                        ['user_id' => $userId,
                            'board_id' => $boardId,
                            'first_name' => $request->get('ch_avp_fname'),
                            'last_name' => $request->get('ch_avp_lname'),
                            'email' => $request->get('ch_avp_email'),
                            'password' => Hash::make('TempPass4You'),
                            'remember_token' => '',
                            'board_position_id' => 2,
                            'chapter_id' => $chapterId,
                            'street_address' => $request->get('ch_avp_street'),
                            'city' => $request->get('ch_avp_city'),
                            'state' => $request->get('ch_avp_state'),
                            'zip' => $request->get('ch_avp_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_avp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                            'is_active' => 1]
                    );
                }
            }
            // }
            //MVP Info
            //if($request->get('ch_mvp_fname') !='' && $request->get('ch_mvp_lname') !='' && $request->get('ch_mvp_email') !='')
            //{
            $MVPDetails = DB::table('board_details')
                ->select('board_id', 'user_id')
                ->where('chapter_id', '=', $chapterId)
                ->where('board_position_id', '=', '3')
                ->get();
            if (count($MVPDetails) != 0) {
                $userId = $MVPDetails[0]->user_id;
                $boardId = $MVPDetails[0]->board_id;
                if ($request->get('MVPVacant') == 'on') {
                    //Delete Details of Board memebers
                    DB::table('board_details')
                        ->where('board_id', $boardId)
                        ->delete();
                    //Delete Details of Board memebers from users table
                    DB::table('users')
                        ->where('id', $userId)
                        ->delete();
                } else {
                    $user = User::find($userId);
                    $user->first_name = $request->get('ch_mvp_fname');
                    $user->last_name = $request->get('ch_mvp_lname');
                    $user->email = $request->get('ch_mvp_email');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    DB::table('board_details')
                        ->where('board_id', $boardId)
                        ->update(['first_name' => $request->get('ch_mvp_fname'),
                            'last_name' => $request->get('ch_mvp_lname'),
                            'email' => $request->get('ch_mvp_email'),
                            'street_address' => $request->get('ch_mvp_street'),
                            'city' => $request->get('ch_mvp_city'),
                            'state' => $request->get('ch_mvp_state'),
                            'zip' => $request->get('ch_mvp_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_mvp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                }
            } else {
                if ($request->get('MVPVacant') != 'on') {
                    $userId = DB::table('users')->insertGetId(
                        ['first_name' => $request->get('ch_mvp_fname'),
                            'last_name' => $request->get('ch_mvp_lname'),
                            'email' => $request->get('ch_mvp_email'),
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1]
                    );

                    $boardIdArr = DB::table('board_details')
                        ->select('board_details.board_id')
                        ->orderBy('board_details.board_id', 'DESC')
                        ->limit(1)
                        ->get();
                    $boardId = $boardIdArr[0]->board_id + 1;

                    $board = DB::table('board_details')->insert(
                        ['user_id' => $userId,
                            'board_id' => $boardId,
                            'first_name' => $request->get('ch_mvp_fname'),
                            'last_name' => $request->get('ch_mvp_lname'),
                            'email' => $request->get('ch_mvp_email'),
                            'password' => Hash::make('TempPass4You'),
                            'remember_token' => '',
                            'board_position_id' => 3,
                            'chapter_id' => $chapterId,
                            'street_address' => $request->get('ch_mvp_street'),
                            'city' => $request->get('ch_mvp_city'),
                            'state' => $request->get('ch_mvp_state'),
                            'zip' => $request->get('ch_mvp_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_mvp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                            'is_active' => 1]
                    );
                }
            }
            //}
            //TRS Info
            //if($request->get('ch_trs_fname') !='' && $request->get('ch_trs_lname') !='' && $request->get('ch_trs_email') !='')
            //{
            $TRSDetails = DB::table('board_details')
                ->select('board_id', 'user_id')
                ->where('chapter_id', '=', $chapterId)
                ->where('board_position_id', '=', '4')
                ->get();
            if (count($TRSDetails) != 0) {
                $userId = $TRSDetails[0]->user_id;
                $boardId = $TRSDetails[0]->board_id;
                if ($request->get('TreasVacant') == 'on') {
                    //Delete Details of Board memebers
                    DB::table('board_details')
                        ->where('board_id', $boardId)
                        ->delete();
                    //Delete Details of Board memebers from users table
                    DB::table('users')
                        ->where('id', $userId)
                        ->delete();
                } else {
                    $user = User::find($userId);
                    $user->first_name = $request->get('ch_trs_fname');
                    $user->last_name = $request->get('ch_trs_lname');
                    $user->email = $request->get('ch_trs_email');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    DB::table('board_details')
                        ->where('board_id', $boardId)
                        ->update(['first_name' => $request->get('ch_trs_fname'),
                            'last_name' => $request->get('ch_trs_lname'),
                            'email' => $request->get('ch_trs_email'),
                            'street_address' => $request->get('ch_trs_street'),
                            'city' => $request->get('ch_trs_city'),
                            'state' => $request->get('ch_trs_state'),
                            'zip' => $request->get('ch_trs_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_trs_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                }
            } else {
                if ($request->get('TreasVacant') != 'on') {
                    $userId = DB::table('users')->insertGetId(
                        ['first_name' => $request->get('ch_trs_fname'),
                            'last_name' => $request->get('ch_trs_lname'),
                            'email' => $request->get('ch_trs_email'),
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1]
                    );

                    $boardIdArr = DB::table('board_details')
                        ->select('board_details.board_id')
                        ->orderBy('board_details.board_id', 'DESC')
                        ->limit(1)
                        ->get();
                    $boardId = $boardIdArr[0]->board_id + 1;

                    $board = DB::table('board_details')->insert(
                        ['user_id' => $userId,
                            'board_id' => $boardId,
                            'first_name' => $request->get('ch_trs_fname'),
                            'last_name' => $request->get('ch_trs_lname'),
                            'email' => $request->get('ch_trs_email'),
                            'password' => Hash::make('TempPass4You'),
                            'remember_token' => '',
                            'board_position_id' => 4,
                            'chapter_id' => $chapterId,
                            'street_address' => $request->get('ch_trs_street'),
                            'city' => $request->get('ch_trs_city'),
                            'state' => $request->get('ch_trs_state'),
                            'zip' => $request->get('ch_trs_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_trs_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                            'is_active' => 1]
                    );
                }
            }
            //}
            //SEC Info
            //if($request->get('ch_sec_fname') !='' && $request->get('ch_sec_lname') !='' && $request->get('ch_sec_email') !='')
            //{
            $SECDetails = DB::table('board_details')
                ->select('board_id', 'user_id')
                ->where('chapter_id', '=', $chapterId)
                ->where('board_position_id', '=', '5')
                ->get();
            if (count($SECDetails) != 0) {
                $userId = $SECDetails[0]->user_id;
                $boardId = $SECDetails[0]->board_id;
                if ($request->get('SecVacant') == 'on') {
                    //Delete Details of Board memebers
                    DB::table('board_details')
                        ->where('board_id', $boardId)
                        ->delete();
                    //Delete Details of Board memebers from users table
                    DB::table('users')
                        ->where('id', $userId)
                        ->delete();
                } else {
                    $user = User::find($userId);
                    $user->first_name = $request->get('ch_sec_fname');
                    $user->last_name = $request->get('ch_sec_lname');
                    $user->email = $request->get('ch_sec_email');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    DB::table('board_details')
                        ->where('board_id', $boardId)
                        ->update(['first_name' => $request->get('ch_sec_fname'),
                            'last_name' => $request->get('ch_sec_lname'),
                            'email' => $request->get('ch_sec_email'),
                            'street_address' => $request->get('ch_sec_street'),
                            'city' => $request->get('ch_sec_city'),
                            'state' => $request->get('ch_sec_state'),
                            'zip' => $request->get('ch_sec_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_sec_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                }
            } else {
                if ($request->get('SecVacant') != 'on') {
                    $userId = DB::table('users')->insertGetId(
                        ['first_name' => $request->get('ch_sec_fname'),
                            'last_name' => $request->get('ch_sec_lname'),
                            'email' => $request->get('ch_sec_email'),
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1]
                    );

                    $boardIdArr = DB::table('board_details')
                        ->select('board_details.board_id')
                        ->orderBy('board_details.board_id', 'DESC')
                        ->limit(1)
                        ->get();
                    $boardId = $boardIdArr[0]->board_id + 1;

                    $board = DB::table('board_details')->insert(
                        ['user_id' => $userId,
                            'board_id' => $boardId,
                            'first_name' => $request->get('ch_sec_fname'),
                            'last_name' => $request->get('ch_sec_lname'),
                            'email' => $request->get('ch_sec_email'),
                            'password' => Hash::make('TempPass4You'),
                            'remember_token' => '',
                            'board_position_id' => 5,
                            'chapter_id' => $chapterId,
                            'street_address' => $request->get('ch_sec_street'),
                            'city' => $request->get('ch_sec_city'),
                            'state' => $request->get('ch_sec_state'),
                            'zip' => $request->get('ch_sec_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_sec_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                            'is_active' => 1]
                    );
                }
            }
            //}

            //change primary coordinator email
            $cor_details = db::table('coordinator_details')
                ->select('email')
                ->where('conference_id', $corConfId)
                ->where('position_id', 9)
                ->where('is_active', 1)
                ->get();
            $row_count = count($cor_details);

            $PREemail = DB::table('board_details')
                ->select('email')
                ->where('board_position_id', 1)
                ->where('chapter_id', $id)
                ->where('is_active', 1)
                ->get();
            $to_email3 = [$PREemail[0]->email];

            $cc_details = db::table('coordinator_details')
                ->select('email')
                ->where('conference_id', $corConfId)
                ->where('coordinator_id', $corId)
                ->where('is_active', 1)
                ->get();

            $to_email1 = $cc_details[0]->email;

            $coremail = DB::table('coordinator_details')
                ->select('email')
                ->where('is_active', '=', '1')
                ->where('coordinator_id', $request->get('ch_primarycor'))
                ->get();
            $coremail = $coremail[0]->email;

            if ($row_count == 0) {
                $to_email = $cc_details[0]->email;

            } else {
                $to_email = $cor_details[0]->email;

            }

            if ($request->get('ch_primarycor') != $request->get('ch_hid_primarycor')) {
                $corename = DB::table('coordinator_details')
                    ->select('first_name')
                    ->where('is_active', '=', '1')
                    ->where('coordinator_id', $request->get('ch_primarycor'))
                    ->get();
                $corename = $corename[0]->first_name;

                $corename1 = DB::table('coordinator_details')
                    ->select('last_name')
                    ->where('is_active', '=', '1')
                    ->where('coordinator_id', $request->get('ch_primarycor'))
                    ->get();
                $corename1 = $corename1[0]->last_name;

                $coreemail1 = DB::table('coordinator_details')
                    ->select('email')
                    ->where('is_active', '=', '1')
                    ->where('coordinator_id', $request->get('ch_primarycor'))
                    ->get();
                $coreemail1 = $coreemail1[0]->email;

                $chapterDetails = Chapter::find($chapterId);
                $stateArr = DB::table('state')
                    ->select('state.*')
                    ->orderBy('id', 'ASC')
                    ->get();

                $chapterState = DB::table('state')
                    ->select('state_short_name')
                    ->where('id', '=', $chapterDetails->state)
                    ->get();
                $chapterState = $chapterState[0]->state_short_name;

                $to_email = array_merge((array) $coremail, $to_email3);
                $mailData = [
                    'chapter_name' => $request->get('ch_name'),
                    'chapter_state' => $chapterState,
                    'name1' => $corename,
                    'name2' => $corename1,
                    'email1' => $coreemail1,
                ];

                Mail::send('emails.primarychange', $mailData, function ($message) use ($to_email) {
                    $message->to($to_email, 'MOMS Club')->subject('Primary Coordinator Change');
                });
            }

            //Update Chapter MailData//
            $presInfoUpd = DB::table('chapters')
                ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'bd.street_address as street', 'bd.city as city', 'bd.zip as zip', 'st.state_short_name as state')
                ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.id', $chapterId)
                ->orderBy('chapters.id', 'DESC')
                ->get();

            $AVPInfoUpd = DB::table('chapters')
                ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '2')
                ->where('chapters.id', $chapterId)
                ->get();

            $MVPInfoUpd = DB::table('chapters')
                ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '3')
                ->where('chapters.id', $chapterId)
                ->get();

            $tresInfoUpd = DB::table('chapters')
                ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '4')
                ->where('chapters.id', $chapterId)
                ->get();

            $secInfoUpd = DB::table('chapters')
                ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '5')
                ->where('chapters.id', $chapterId)
                ->get();

            $mailDataPres = [
                'chapterNameUpd' => $presInfoUpd[0]->name,
                'chapterStateUpd' => $presInfoUpd[0]->state,
                'cor_fnameUpd' => $presInfoUpd[0]->cor_f_name,
                'cor_lnameUpd' => $presInfoUpd[0]->cor_l_name,
                'updated_byUpd' => $presInfoUpd[0]->last_updated_date,
                'chapfnameUpd' => $presInfoUpd[0]->bor_f_name,
                'chaplnameUpd' => $presInfoUpd[0]->bor_l_name,
                'chapteremailUpd' => $presInfoUpd[0]->bor_email,
                'streetUpd' => $presInfoUpd[0]->street,
                'cityUpd' => $presInfoUpd[0]->city,
                'stateUpd' => $presInfoUpd[0]->state,
                'zipUpd' => $presInfoUpd[0]->zip,
                'countryUpd' => $presInfoUpd[0]->country,
                'phoneUpd' => $presInfoUpd[0]->phone,
                'inConUpd' => $presInfoUpd[0]->inquiries_contact,
                'einUpd' => $presInfoUpd[0]->ein,
                'einLetterUpd' => $presInfoUpd[0]->ein_letter_path,
                'inNoteUpd' => $presInfoUpd[0]->inquiries_note,
                'chapemailUpd' => $presInfoUpd[0]->email,
                'poBoxUpd' => $presInfoUpd[0]->po_box,
                'webUrlUpd' => $presInfoUpd[0]->website_url,
                'weblinkStatusUpd' => $presInfoUpd[0]->website_link_status,
                'egroupUpd' => $presInfoUpd[0]->egroup,
                'boundUpd' => $presInfoUpd[0]->territory,
                'addInfoUpd' => $presInfoUpd[0]->additional_info,
                'chapstatusUpd' => $presInfoUpd[0]->status,
                'chapNoteUpd' => $presInfoUpd[0]->notes,
                'chapterNamePre' => $presInfoPre[0]->name,
                'chapterStatePre' => $presInfoPre[0]->state,
                'cor_fnamePre' => $presInfoPre[0]->cor_f_name,
                'cor_lnamePre' => $presInfoPre[0]->cor_l_name,
                'updated_byPre' => $presInfoPre[0]->last_updated_date,
                'chapfnamePre' => $presInfoPre[0]->bor_f_name,
                'chaplnamePre' => $presInfoPre[0]->bor_l_name,
                'chapteremailPre' => $presInfoPre[0]->bor_email,
                'streetPre' => $presInfoPre[0]->street,
                'cityPre' => $presInfoPre[0]->city,
                'statePre' => $presInfoPre[0]->state,
                'zipPre' => $presInfoPre[0]->zip,
                'countryPre' => $presInfoPre[0]->country,
                'phonePre' => $presInfoPre[0]->phone,
                'inConPre' => $presInfoPre[0]->inquiries_contact,
                'einPre' => $presInfoPre[0]->ein,
                'einLetterPre' => $presInfoPre[0]->ein_letter_path,
                'inNotePre' => $presInfoPre[0]->inquiries_note,
                'chapemailPre' => $presInfoPre[0]->email,
                'poBoxPre' => $presInfoPre[0]->po_box,
                'webUrlPre' => $presInfoPre[0]->website_url,
                'weblinkStatusPre' => $presInfoPre[0]->website_link_status,
                'egroupPre' => $presInfoPre[0]->egroup,
                'boundPre' => $presInfoPre[0]->territory,
                'addInfoPre' => $presInfoPre[0]->additional_info,
                'chapstatusPre' => $presInfoPre[0]->status,
                'chapNotePre' => $presInfoPre[0]->notes,

            ];
            $mailData = array_merge($mailDataPres);
            if ($AVPInfoUpd !== null && count($AVPInfoUpd) > 0) {
                $mailDataAvp = ['avpfnameUpd' => $AVPInfoUpd[0]->bor_f_name,
                    'avplnameUpd' => $AVPInfoUpd[0]->bor_l_name,
                    'avpemailUpd' => $AVPInfoUpd[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDataAvp);
            } else {
                $mailDataAvp = ['avpfnameUpd' => '',
                    'avplnameUpd' => '',
                    'avpemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDataAvp);
            }
            if ($MVPInfoUpd !== null && count($MVPInfoUpd) > 0) {
                $mailDataMvp = ['mvpfnameUpd' => $MVPInfoUpd[0]->bor_f_name,
                    'mvplnameUpd' => $MVPInfoUpd[0]->bor_l_name,
                    'mvpemailUpd' => $MVPInfoUpd[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDataMvp);
            } else {
                $mailDataMvp = ['mvpfnameUpd' => '',
                    'mvplnameUpd' => '',
                    'mvpemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDataMvp);
            }
            if ($tresInfoUpd !== null && count($tresInfoUpd) > 0) {
                $mailDatatres = ['tresfnameUpd' => $tresInfoUpd[0]->bor_f_name,
                    'treslnameUpd' => $tresInfoUpd[0]->bor_l_name,
                    'tresemailUpd' => $tresInfoUpd[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDatatres);
            } else {
                $mailDatatres = ['tresfnameUpd' => '',
                    'treslnameUpd' => '',
                    'tresemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDatatres);
            }
            if ($secInfoUpd !== null && count($secInfoUpd) > 0) {
                $mailDataSec = ['secfnameUpd' => $secInfoUpd[0]->bor_f_name,
                    'seclnameUpd' => $secInfoUpd[0]->bor_l_name,
                    'secemailUpd' => $secInfoUpd[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDataSec);
            } else {
                $mailDataSec = ['secfnameUpd' => '',
                    'seclnameUpd' => '',
                    'secemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDataSec);
            }
            if ($AVPInfoPre !== null && count($AVPInfoPre) > 0) {
                $mailDataAvpp = ['avpfnamePre' => $AVPInfoPre[0]->bor_f_name,
                    'avplnamePre' => $AVPInfoPre[0]->bor_l_name,
                    'avpemailPre' => $AVPInfoPre[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDataAvpp);
            } else {
                $mailDataAvpp = ['avpfnamePre' => '',
                    'avplnamePre' => '',
                    'avpemailPre' => '', ];
                $mailData = array_merge($mailData, $mailDataAvpp);
            }
            if ($MVPInfoPre !== null && count($MVPInfoPre) > 0) {
                $mailDataMvpp = ['mvpfnamePre' => $MVPInfoPre[0]->bor_f_name,
                    'mvplnamePre' => $MVPInfoPre[0]->bor_l_name,
                    'mvpemailPre' => $MVPInfoPre[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDataMvpp);
            } else {
                $mailDataMvpp = ['mvpfnamePre' => '',
                    'mvplnamePre' => '',
                    'mvpemailPre' => '', ];
                $mailData = array_merge($mailData, $mailDataMvpp);
            }
            if ($tresInfoPre !== null && count($tresInfoPre) > 0) {
                $mailDatatresp = ['tresfnamePre' => $tresInfoPre[0]->bor_f_name,
                    'treslnamePre' => $tresInfoPre[0]->bor_l_name,
                    'tresemailPre' => $tresInfoPre[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDatatresp);
            } else {
                $mailDatatresp = ['tresfnamePre' => '',
                    'treslnamePre' => '',
                    'tresemailPre' => '', ];
                $mailData = array_merge($mailData, $mailDatatresp);
            }
            if ($secInfoPre !== null && count($secInfoPre) > 0) {
                $mailDataSecp = ['secfnamePre' => $secInfoPre[0]->bor_f_name,
                    'seclnamePre' => $secInfoPre[0]->bor_l_name,
                    'secemailPre' => $secInfoPre[0]->bor_email, ];
                $mailData = array_merge($mailData, $mailDataSecp);
            } else {
                $mailDataSecp = ['secfnamePre' => '',
                    'seclnamePre' => '',
                    'secemailPre' => '', ];
                $mailData = array_merge($mailData, $mailDataSecp);
            }

            //Primary Coordinator Notification//
            $to_email = $presInfoUpd[0]->cor_email;

            Mail::to($to_email, 'MOMS Club')
                ->send(new ChapersUpdatePrimaryCoor($mailData));

            //List Admin Notification//
            $to_email2 = 'listadmin@momsclub.org';

            if ($presInfoUpd[0]->email != $presInfoPre[0]->email || $presInfoUpd[0]->bor_email != $presInfoPre[0]->bor_email || $mailDataAvpp['avpemailPre'] != $mailDataAvp['avpemailUpd'] || $mailDataMvpp['mvpemailPre'] != $mailDataMvp['mvpemailUpd'] || $mailDatatresp['tresemailPre'] != $mailDatatres['tresemailUpd'] || $mailDataSecp['secemailPre'] != $mailDataSec['secemailUpd']) {

                Mail::to($to_email2, 'MOMS Club')
                    ->send(new ChapersUpdateListAdmin($mailData));
            }

            //website notifications//
            $cor_details = db::table('coordinator_details')
                ->select('email')
                ->where('conference_id', $corConfId)
                ->where('position_id', 9)
                ->where('is_active', 1)
                ->get();
            $row_count = count($cor_details);
            if ($row_count == 0) {
                $cc_details = db::table('coordinator_details')
                    ->select('email')
                    ->where('conference_id', $corConfId)
                    ->where('coordinator_id', $corId)
                    ->where('is_active', 1)
                    ->get();
                $to_email4 = $cc_details[0]->email;
            } else {
                $to_email4 = $cor_details[0]->email;
            }

            if ($presInfoUpd[0]->website_link_status != $presInfoPre[0]->website_link_status) {
                if ($request->get('ch_linkstatus') == 1) {
                    $link_url = $request->get('ch_website');
                    $chname = $request->get('ch_name');
                    $chstate = $request->get('ch_state ');
                    $mail_message = '<p>The MOMS Club of '.$chname.' has updated their chapter website.</p>';
                    $mail_message .= "<p>The new website address is: <a href='$link_url'>".$link_url.'</a></p>';
                    $mail_message .= '<p>The site has been reviewed and will automatically be linked to the main website.</p>';
                    $mailData = ['content' => $mail_message];
                    Mail::send('emails.chapterwebsite', $mailData, function ($message4) use ($to_email4) {
                        $message4->to($to_email4, 'MOMS Club')->subject('Chapter Website Linked');
                    });
                }

                if ($request->get('ch_linkstatus') == 2) {
                    $link_url = $request->get('ch_website');
                    $chname = $request->get('ch_name');
                    $chstate = $request->get('ch_state ');
                    $mail_message = '<p>The MOMS Club of '.$chname.' has updated their chapter website.</p>';
                    $mail_message .= "<p>The new website address is: <a href='$link_url'>".$link_url.'</a></p>';
                    $mail_message .= "<p>Once the site has been reviewed and is ready to be linked, please mark it as 'Linked' and it will automatically be added to our main website.</p>";

                    $mailData = ['content' => $mail_message];
                    Mail::send('emails.chapterwebsite', $mailData, function ($message4) use ($to_email4) {
                        $message4->to($to_email4, 'MOMS Club')->subject('Chapter Website Needs Review');
                    });
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            echo $e->getMessage();
            exit();
            DB::rollback();

            // Log the error
            Log::error($e);

            return redirect('/chapter/list')->with('fail', 'Something went wrong, Please try again..');
        }

        return redirect('/chapter/list')->with('success', 'Chapter has been updated');
    }

    /**
     * View the Boundary Details
     */
    public function boundaryview($id)
    {

        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('board_details as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id', 'ASC')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id', 'ASC')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name', 'ASC')
            ->get();

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', '=', $corConfId)
            ->where('cd.position_id', '<=', '6')
            ->where('cd.position_id', '>=', '1')
            ->where('cd.is_active', '=', '1')
            ->orWhere('cd.position_id', '=', '25')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name', 'ASC')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $chapterList[0]->start_month_id;

        $data = ['currentMonth' => $currentMonth, 'chapterList' => $chapterList, 'regionList' => $regionList, 'primaryCoordinatorList' => $primaryCoordinatorList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('chapters.boundaryview')->with($data);
    }

    public function updateBoundary(Request $request, $id)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $chapter = Chapter::find($id);
        DB::beginTransaction();
        try {
            $chapter->territory = $request->get('ch_territory');
            $chapter->boundary_issue_resolved = (int) $request->has('ch_resolved');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            // Log the error
            Log::error($e);

            return redirect('/yearreports/boundaryissue')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect('/yearreports/boundaryissue')->with('success', 'Boundary issue has been successfully updated');
    }

    /**
     * Display the International chapter list
     */
    public function showIntChapter()
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $intChapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.conference_id as cor_cid', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city', 'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('st.state_short_name', 'ASC')
            ->orderBy('chapters.name', 'ASC')
            ->get();
        $countList = count($intChapterList);
        $data = ['countList' => $countList, 'intChapterList' => $intChapterList];

        return view('chapters.international')->with($data);
    }

    public function showInquiriesChapter()
    {

        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
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
        if ($positionId == 7 || ($corId == 423 && $positionId == 8)) {
            $inquiriesList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.inquiries_contact as inq_con', 'chapters.territory as terry', 'chapters.status as status', 'chapters.inquiries_note as inq_note', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'cd.email as cd_email', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'st.state_short_name as state')
                ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->orderBy('st.state_short_name', 'ASC')
                ->get();

        } elseif ($positionId == 6 || $positionId == 25 || $positionId == 8 || $secPositionId == 8) {
            $inquiriesList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.inquiries_contact as inq_con', 'chapters.territory as terry', 'chapters.status as status', 'chapters.inquiries_note as inq_note', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'cd.email as cd_email', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'st.state_short_name as state')
                ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.conference', '=', $corConfId)
                ->orderBy('st.state_short_name', 'ASC')
                ->get();
        } else {
            $inquiriesList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.inquiries_contact as inq_con', 'chapters.territory as terry', 'chapters.status as status', 'chapters.inquiries_note as inq_note', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'cd.email as cd_email', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'st.state_short_name as state')
                ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.region', '=', $corRegId)
                ->orderBy('st.state_short_name', 'ASC')
                ->get();
        }

        $data = ['inquiriesList' => $inquiriesList, 'corConfId' => $corConfId, 'corRegId' => $corRegId];

        return view('chapters.inquiries')->with($data);
    }

    public function zappedInquiriesChapter()
    {

        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
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

        $inquiriesList = DB::table('chapters')
            ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.territory as terry', 'chapters.zap_date as zap_date', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '0')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.conference', '=', $corConfId)
            ->orderBy('chapters.zap_date', 'DESC')
            ->get();

        $data = ['inquiriesList' => $inquiriesList, 'corConfId' => $corConfId];

        return view('chapters.inquirieszapped')->with($data);
    }

    public function inquiriesview($id)
    {

        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('board_details as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id', 'ASC')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id', 'ASC')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name', 'ASC')
            ->get();

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', '=', $corConfId)
            ->where('cd.position_id', '<=', '6')
            ->where('cd.position_id', '>=', '1')
            ->orWhere('cd.position_id', '=', '25')
            ->where('cd.is_active', '=', '1')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name', 'ASC')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $chapterList[0]->start_month_id;

        $data = ['currentMonth' => $currentMonth, 'chapterList' => $chapterList, 'regionList' => $regionList, 'primaryCoordinatorList' => $primaryCoordinatorList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('chapters.inquiriesview')->with($data);
    }

    public function showWebsiteChapter()
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        if ($positionId == 7) {
            $websiteList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.website_url as web', 'wb.status as web_status', 'chapters.website_notes as web_notes', 'chapters.egroup as egroup', 'st.state_short_name as state')
                ->join('state as st', 'chapters.state', '=', 'st.id')
                ->leftJoin('website_link_status as wb', 'chapters.website_link_status', '=', 'wb.id')
                ->where('chapters.is_active', '=', '1')
                ->orderBy('st.state_short_name', 'ASC')
                ->orderBy('chapters.name', 'ASC')
                ->get();
        } elseif ($positionId == 6 || $positionId == 25) {
            $websiteList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.website_url as web', 'wb.status as web_status', 'chapters.website_notes as web_notes', 'chapters.egroup as egroup', 'st.state_short_name as state')
                ->join('state as st', 'chapters.state', '=', 'st.id')
                ->leftJoin('website_link_status as wb', 'chapters.website_link_status', '=', 'wb.id')
                ->where('chapters.is_active', '=', '1')
                ->where('chapters.conference', '=', $corConfId)
                ->orderBy('st.state_short_name', 'ASC')
                ->orderBy('chapters.name', 'ASC')
                ->get();
        } else {
            $websiteList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.website_url as web', 'wb.status as web_status', 'chapters.website_notes as web_notes', 'chapters.egroup as egroup', 'st.state_short_name as state')
                ->join('state as st', 'chapters.state', '=', 'st.id')
                ->leftJoin('website_link_status as wb', 'chapters.website_link_status', '=', 'wb.id')
                ->where('chapters.is_active', '=', '1')
                ->where('chapters.region', '=', $corRegId)
                ->orderBy('st.state_short_name', 'ASC')
                ->orderBy('chapters.name', 'ASC')
                ->get();
        }
        $data = ['websiteList' => $websiteList];

        return view('chapters.website')->with($data);
    }

    /**
     * Display the Zapped chapter list mapped with Conference Region
     */
    public function showZappedChapter()
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        if ($positionId == 6 || $positionId == 25 || $secPositionId == 25) {
            $chapterList = DB::table('chapters')
                ->select('chapters.*', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city', 'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
                ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
                ->where('chapters.is_active', '=', '0')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.conference', '=', $corConfId)
                ->orderBy('chapters.zap_date', 'DESC')
                ->get();
        } else {
            if ($positionId == 7) {
                $chapterList = DB::table('chapters')
                    ->select('chapters.*', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city', 'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
                    ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                    ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
                    ->where('chapters.is_active', '=', '0')
                    ->where('bd.board_position_id', '=', '1')
                    ->orderBy('chapters.zap_date', 'DESC')
                    ->get();
            } else {
                $chapterList = DB::table('chapters')
                    ->select('chapters.*', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city', 'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
                    ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                    ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                    ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                    ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
                    ->where('chapters.is_active', '=', '0')
                    ->where('bd.board_position_id', '=', '1')
                    ->where('chapters.region', '=', $corRegId)
                    ->orderBy('chapters.zap_date', 'DESC')
                    ->get();
            }
        }

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('chapters.zapped')->with($data);
    }

    /**
     * Display the International Zapped chapter list
     */
    public function showIntZappedChapter()
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'rg.short_name as reg_name', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add', 'bd.city as pre_city', 'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftjoin('region as rg', 'chapters.region', '=', 'rg.id')
            ->where('chapters.is_active', '=', '0')
            ->where('bd.board_position_id', '=', '1')
            ->orderBy('chapters.zap_date', 'DESC')
            ->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('chapters.intzapped')->with($data);
    }

    /**
     * View the Zapped chapter list
     */
    public function showZappedChapterView($id)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $financial_report_array = FinancialReport::find($id);
        //$reviewComplete = $financial_report_array['review_complete'];
        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('board_details as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '0')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();

        $AVPDetails = DB::table('board_details as bd')
            ->select('bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id', 'bd.street_address as avp_addr', 'bd.city as avp_city', 'bd.zip as avp_zip', 'bd.phone as avp_phone', 'bd.state as avp_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '2')
            ->get();
        if (count($AVPDetails) == 0) {
            $AVPDetails[0] = ['avp_fname' => '', 'avp_lname' => '', 'avp_email' => '', 'avp_addr' => '', 'avp_city' => '', 'avp_zip' => '', 'avp_phone' => '', 'avp_state' => '', 'user_id' => ''];
            $AVPDetails = json_decode(json_encode($AVPDetails));
        }

        $MVPDetails = DB::table('board_details as bd')
            ->select('bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id', 'bd.street_address as mvp_addr', 'bd.city as mvp_city', 'bd.zip as mvp_zip', 'bd.phone as mvp_phone', 'bd.state as mvp_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '3')
            ->get();
        if (count($MVPDetails) == 0) {
            $MVPDetails[0] = ['mvp_fname' => '', 'mvp_lname' => '', 'mvp_email' => '', 'mvp_addr' => '', 'mvp_city' => '', 'mvp_zip' => '', 'mvp_phone' => '', 'mvp_state' => '', 'user_id' => ''];
            $MVPDetails = json_decode(json_encode($MVPDetails));
        }

        $TRSDetails = DB::table('board_details as bd')
            ->select('bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id', 'bd.street_address as trs_addr', 'bd.city as trs_city', 'bd.zip as trs_zip', 'bd.phone as trs_phone', 'bd.state as trs_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '4')
            ->get();
        if (count($TRSDetails) == 0) {
            $TRSDetails[0] = ['trs_fname' => '', 'trs_lname' => '', 'trs_email' => '', 'trs_addr' => '', 'trs_city' => '', 'trs_zip' => '', 'trs_phone' => '', 'trs_state' => '', 'user_id' => ''];
            $TRSDetails = json_decode(json_encode($TRSDetails));
        }

        $SECDetails = DB::table('board_details as bd')
            ->select('bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.board_position_id', 'bd.street_address as sec_addr', 'bd.city as sec_city', 'bd.zip as sec_zip', 'bd.phone as sec_phone', 'bd.state as sec_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '5')
            ->get();
        if (count($SECDetails) == 0) {
            $SECDetails[0] = ['sec_fname' => '', 'sec_lname' => '', 'sec_email' => '', 'sec_addr' => '', 'sec_city' => '', 'sec_zip' => '', 'sec_phone' => '', 'sec_state' => '', 'user_id' => ''];
            $SECDetails = json_decode(json_encode($SECDetails));
        }

        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id', 'ASC')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id', 'ASC')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name', 'ASC')
            ->get();

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', '=', $corConfId)
            ->where('cd.position_id', '<=', '6')
            ->where('cd.position_id', '>=', '1')
            ->orWhere('cd.position_id', '=', '25')
            ->where('cd.is_active', '=', '1')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name', 'ASC')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $chapterList[0]->start_month_id;
        $data = ['corId' => $corId, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'chapterList' => $chapterList, 'regionList' => $regionList, 'primaryCoordinatorList' => $primaryCoordinatorList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth, 'currentMonth' => $currentMonth];

        return view('chapters.zapview')->with($data);
    }

    /**
     * View the International Zapped chapter list
     */
    public function showIntZappedChapterView($id)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $financial_report_array = FinancialReport::find($id);
        //$reviewComplete = $financial_report_array['review_complete'];
        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('board_details as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '0')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();

        $AVPDetails = DB::table('board_details as bd')
            ->select('bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id', 'bd.street_address as avp_addr', 'bd.city as avp_city', 'bd.zip as avp_zip', 'bd.phone as avp_phone', 'bd.state as avp_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '2')
            ->get();
        if (count($AVPDetails) == 0) {
            $AVPDetails[0] = ['avp_fname' => '', 'avp_lname' => '', 'avp_email' => '', 'avp_addr' => '', 'avp_city' => '', 'avp_zip' => '', 'avp_phone' => '', 'avp_state' => '', 'user_id' => ''];
            $AVPDetails = json_decode(json_encode($AVPDetails));
        }

        $MVPDetails = DB::table('board_details as bd')
            ->select('bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id', 'bd.street_address as mvp_addr', 'bd.city as mvp_city', 'bd.zip as mvp_zip', 'bd.phone as mvp_phone', 'bd.state as mvp_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '3')
            ->get();
        if (count($MVPDetails) == 0) {
            $MVPDetails[0] = ['mvp_fname' => '', 'mvp_lname' => '', 'mvp_email' => '', 'mvp_addr' => '', 'mvp_city' => '', 'mvp_zip' => '', 'mvp_phone' => '', 'mvp_state' => '', 'user_id' => ''];
            $MVPDetails = json_decode(json_encode($MVPDetails));
        }

        $TRSDetails = DB::table('board_details as bd')
            ->select('bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id', 'bd.street_address as trs_addr', 'bd.city as trs_city', 'bd.zip as trs_zip', 'bd.phone as trs_phone', 'bd.state as trs_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '4')
            ->get();
        if (count($TRSDetails) == 0) {
            $TRSDetails[0] = ['trs_fname' => '', 'trs_lname' => '', 'trs_email' => '', 'trs_addr' => '', 'trs_city' => '', 'trs_zip' => '', 'trs_phone' => '', 'trs_state' => '', 'user_id' => ''];
            $TRSDetails = json_decode(json_encode($TRSDetails));
        }

        $SECDetails = DB::table('board_details as bd')
            ->select('bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.board_position_id', 'bd.street_address as sec_addr', 'bd.city as sec_city', 'bd.zip as sec_zip', 'bd.phone as sec_phone', 'bd.state as sec_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '5')
            ->get();
        if (count($SECDetails) == 0) {
            $SECDetails[0] = ['sec_fname' => '', 'sec_lname' => '', 'sec_email' => '', 'sec_addr' => '', 'sec_city' => '', 'sec_zip' => '', 'sec_phone' => '', 'sec_state' => '', 'user_id' => ''];
            $SECDetails = json_decode(json_encode($SECDetails));
        }

        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id', 'ASC')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id', 'ASC')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name', 'ASC')
            ->get();

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', '=', $corConfId)
            ->where('cd.position_id', '<=', '6')
            ->where('cd.position_id', '>=', '1')
            ->where('cd.is_active', '=', '1')
            ->orWhere('cd.position_id', '=', '25')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name', 'ASC')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $chapterList[0]->start_month_id;
        $data = ['corId' => $corId, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'chapterList' => $chapterList, 'regionList' => $regionList, 'primaryCoordinatorList' => $primaryCoordinatorList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth, 'currentMonth' => $currentMonth];

        return view('chapters.intzapview')->with($data);
    }

    public function editWebsite($id)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('board_details as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id', 'ASC')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id', 'ASC')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name', 'ASC')
            ->get();

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', '=', $corConfId)
            ->where('cd.position_id', '<=', '6')
            ->where('cd.position_id', '>=', '1')
            ->where('cd.is_active', '=', '1')
            ->orWhere('cd.position_id', '=', '25')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name', 'ASC')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $chapterList[0]->start_month_id;

        $data = ['currentMonth' => $currentMonth, 'chapterList' => $chapterList, 'regionList' => $regionList, 'primaryCoordinatorList' => $primaryCoordinatorList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('chapters.editweb')->with($data);
    }

    public function updateWebsite(Request $request, $id)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $positionId = $corDetails['position_id'];
        $coordinatorId = $id;
        $cor_details = db::table('coordinator_details')
            ->select('email')
            ->where('conference_id', $corConfId)
            ->where('position_id', 9)
            ->where('is_active', 1)
            ->get();
        $row_count = count($cor_details);
        if ($row_count == 0) {
            $cc_details = db::table('coordinator_details')
                ->select('email')
                ->where('conference_id', $corConfId)
                ->where('coordinator_id', $corId)
                ->where('is_active', 1)
                ->get();

            $to_email = $cc_details[0]->email;
        } else {
            $to_email = $cor_details[0]->email;
        }

        if ($request->get('WebStatus') == 1) {
            $link_url = $request->get('Website');
            $chname = $request->get('ch_name');
            $chstate = $request->get('ch_state');
            $mail_message = '<p>The MOMS Club of '.$chname.' has updated their chapter website.</p>';
            $mail_message .= "<p>The new website address is: <a href='$link_url'>".$link_url.'</a></p>';
            $mail_message .= '<p>The site has been reviewed and will automatically be linked to the main website.</p>';
            $mailData = ['content' => $mail_message];
            Mail::send('emails.chapterwebsite', $mailData, function ($message) use ($to_email) {
                $message->to($to_email, 'MOMS Club')->subject('Chapter Website Linked');
            });
        }

        if ($request->get('WebStatus') == 2) {
            $link_url = $request->get('Website');
            $chname = $request->get('ch_name');
            $chstate = $request->get('ch_state');
            $mail_message = '<p>The MOMS Club of '.$chname.' has updated their chapter website.</p>';
            $mail_message .= "<p>The new website address is: <a href='$link_url'>".$link_url.'</a></p>';
            $mail_message .= "<p>Once the site has been reviewed and is ready to be linked, please mark it as 'Linked' and it will automatically be added to our main website.</p>";

            $mailData = ['content' => $mail_message];
            Mail::send('emails.chapterwebsite', $mailData, function ($message) use ($to_email) {
                $message->to($to_email, 'MOMS Club')->subject('Chapter Website Needs Review');
            });

        }

        if ($request->get('WebStatus') == 3) {
            $chname = $request->get('ch_name');
            $chstate = $request->get('ch_state');
            $mail_message = '<p>Website link has automatically been removed for '.$chname.'.</p>';

            $mailData = ['content' => $mail_message];
            Mail::send('emails.chapterwebsite', $mailData, function ($message) use ($to_email) {
                $message->to($to_email, 'MOMS Club')->subject('Chapter Website Link Removed');
            });
        }
        DB::table('chapters')
            ->where('id', $coordinatorId)
            ->update(['website_url' => $request->get('Website'),
                'website_link_status' => $request->get('WebStatus'),
                'egroup' => $request->get('ch_onlinediss'),
                'social1' => $request->get('ch_social1'),
                'social2' => $request->get('ch_social2'),
                'social3' => $request->get('ch_social3'),
                'website_notes' => $request->get('ch_notes')]);

        return redirect('/chapter/website')->with('success', 'Chapter Website has been changed successfully.');
    }

    //Function for checking Email is registerd or not
    public function checkEmail($email)
    {
        $isExists = \App\User::where('email', $email)->first();
        if ($isExists) {
            return response()->json(['exists' => true]);
        }
    }

    public function addToPrezList($chapterId)
    {
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.id', $chapterId)
            ->orderBy('chapters.id', 'DESC')
            ->get();
        $chapterName = $chapterList[0]->name;
        $chapterState = $chapterList[0]->state;
        $chapterPreFname = $chapterList[0]->bor_f_name;
        $chapterPreLname = $chapterList[0]->bor_l_name;
        $chapterPreEmail = $chapterList[0]->bor_email;
        $chapterStatus = $chapterList[0]->status;
        $chapterCordEmail = $chapterList[0]->cor_email;

        //         if($chapterStatus==1){
        //             $to_email='listadmin@momsclub.org';
        //             //$to_email='neha.purwar@otssolutions.com';
        //      }
        //      else{
        //             $to_email=$chapterCordEmail;
        //             //$to_email='neha.purwar@otssolutions.com';
        //         }
        $to_email = $chapterCordEmail;
        $mailData = [
            'chapterPreEmail' => $chapterPreEmail,
            'chapterPreFname' => $chapterPreFname,
            'chapterPreLname' => $chapterPreLname,
            'chapterName' => $chapterName,
            'chapterState' => $chapterState,
            'content' => 'A request has been received to add the following president to the Prez Only Group:',
        ];

        Mail::send('emails.chapterprezlist', $mailData, function ($message) use ($to_email) {
            $message->to($to_email, 'MOMS Club')->subject('PrezList Add Request');
        });

    }

    //Function for getting Reporting Hierarchy of Chapter
    public function checkReportId($id)
    {
        $reportingList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('id', '=', $id)
            ->get();

        if ($reportingList->isNotEmpty()) {
            $reportingList = (array) $reportingList[0];
            $filterReportingList = array_filter($reportingList);
            unset($filterReportingList['id']);
            unset($filterReportingList['layer0']);
            $filterReportingList = array_reverse($filterReportingList);

            $str = '<table>';
            $i = 0;
            foreach ($filterReportingList as $key => $val) {
                $corList = DB::table('coordinator_details as cd')
                    ->select('cd.first_name as fname', 'cd.last_name as lname', 'cd.email as email', 'cp.short_title as pos')
                    ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                    ->where('cd.coordinator_id', '=', $val)
                    ->where('cd.is_active', '=', 1)
                    ->get();

                if (count($corList) > 0) {
                    $name = $corList[0]->fname.' '.$corList[0]->lname;
                    $email = $corList[0]->email;
                    $pos = '('.$corList[0]->pos.')';

                    if ($i == 0) {
                        $str .= "<tr>
                        <td><b>Primary Coordinator &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </b></td>
                        <td><b><a href='mailto:$email' target='_top'>$name </a>$pos</b></td>
                        </tr>";
                    } elseif ($i == 1) {
                        $str .= "<tr>
                        <td><b>Secondary Coordinator : </b></td>
                        <td><b><a href='mailto:$email' target='_top'>$name </a>$pos</b></td>
                        </tr>";
                    } elseif ($i >= 2) {
                        if ($i == 2) {
                            $str .= "<tr>
                            <td><b>Additional Coordinator : </b></td>
                            <td><b><a href='mailto:$email' target='_top'>$name </a>$pos</b></td>
                            </tr>";
                        } else {
                            $str .= "<tr>
                            <td></td>
                            <td><b><a href='mailto:$email' target='_top'>$name </a>$pos</b></td>
                            </tr>";
                        }
                    } else {
                        $str .= '<tr><td></td></tr>';
                    }

                    $i++;
                }
            }

            $str .= '</table>';
            echo $str;
        } else {
            echo 'No reporting data found for the given ID.';
        }
    }

    //Function for Zapped the Chapter
    public function chapterDisband(Request $request)
    {
        $input = request()->all();
        $chapterid = $input['chpaterid'];
        $disbandReason = $input['reason'];
        //  DB::beginTransaction();
        try {
            DB::table('chapters')
                ->where('id', $chapterid)
                ->update(['is_active' => 0, 'disband_reason' => $disbandReason, 'zap_date' => date('Y-m-d')]);

            $userRelatedChpaterList = DB::table('board_details as bd')
                ->select('bd.user_id')
                ->where('bd.chapter_id', '=', $chapterid)
                ->get();
            if (count($userRelatedChpaterList) > 0) {
                foreach ($userRelatedChpaterList as $list) {
                    $userId = $list->user_id;
                    DB::table('users')
                        ->where('id', $userId)
                        ->update(['is_active' => 0]);
                }
            }
            DB::table('board_details')
                ->where('chapter_id', $chapterid)
                ->update(['is_active' => 0]);
            $chapterList = DB::table('chapters')
                ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
                ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.is_Active', '=', '0')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.id', $chapterid)
                ->orderBy('chapters.id', 'DESC')
                ->get();

            $chapterName = $chapterList[0]->name;
            $chapterState = $chapterList[0]->state;
            $chapterEmail = $chapterList[0]->email;
            $chapterPreFname = $chapterList[0]->bor_f_name;
            $chapterPreLname = $chapterList[0]->bor_l_name;
            $chapterPreEmail = $chapterList[0]->bor_email;
            $chapterStatus = $chapterList[0]->status;
            $chapterCordEmail = $chapterList[0]->cor_email;
            //Avp board info detail(Rishi)
            $preinfo = DB::table('board_details')
                ->select('first_name', 'last_name', 'email')
                ->where('chapter_id', $chapterid)
                ->where('board_position_id', '=', '1')
                ->get();

            if (count($preinfo) > 0) {
                $prefirst = $preinfo[0]->first_name;
                $presecond = $preinfo[0]->last_name;
                $preemail = $preinfo[0]->email;
            } else {
                $prefirst = '';
                $presecond = '';
                $preemail = '';
            }
            //Avpinfo
            $avpinfo = DB::table('board_details')
                ->select('first_name', 'last_name', 'email')
                ->where('chapter_id', $chapterid)
                ->where('board_position_id', '=', '2')
                ->get();
            if (count($avpinfo) > 0) {
                $avpfirst = $avpinfo[0]->first_name;
                $avpsecond = $avpinfo[0]->last_name;
                $avpemail = $avpinfo[0]->email;
            } else {
                $avpfirst = '';
                $avpsecond = '';
                $avpemail = '';
            }
            //Mvp info

            $mvpinfo = DB::table('board_details')
                ->select('first_name', 'last_name', 'email')
                ->where('chapter_id', $chapterid)
                ->where('board_position_id', '=', '3')
                ->get();
            if (count($mvpinfo) > 0) {
                $mvpfirst = $mvpinfo[0]->first_name;
                $mvpsecond = $mvpinfo[0]->last_name;
                $mvpemail = $mvpinfo[0]->email;
            } else {
                $mvpfirst = '';
                $mvpsecond = '';
                $mvpemail = '';
            }
            //triinfo
            $triinfo = DB::table('board_details')
                ->select('first_name', 'last_name', 'email')
                ->where('chapter_id', $chapterid)
                ->where('board_position_id', '=', '4')
                ->get();
            if (count($triinfo) > 0) {
                $trifirst = $triinfo[0]->first_name;
                $trisecond = $triinfo[0]->last_name;
                $triemail = $triinfo[0]->email;
            } else {
                $trifirst = '';
                $trisecond = '';
                $triemail = '';
            }
            //secinfo
            $secinfo = DB::table('board_details')
                ->select('first_name', 'last_name', 'email')
                ->where('chapter_id', $chapterid)
                ->where('board_position_id', '=', '5')
                ->get();
            if (count($secinfo) > 0) {
                $secfirst = $secinfo[0]->first_name;
                $secscond = $secinfo[0]->last_name;
                $secemail = $secinfo[0]->email;
            } else {
                $secfirst = '';
                $secscond = '';
                $secemail = '';
            }
            //conferenceinfo
            $coninfo = DB::table('chapters')
                ->select('chapters.*', 'conference')
                ->where('id', $chapterid)
                ->get();
            $conf = $coninfo[0]->conference;
            $to_email = 'listadmin@momsclub.org';
            if ($chapterStatus == 1) {
            }

            $mailData = [
                'chapterPreEmail' => $chapterPreEmail,
                'chapterPreFname' => $chapterPreFname,
                'chapterPreLname' => $chapterPreLname,
                'chapterName' => $chapterName,
                'chapterEmail' => $chapterEmail,
                'chapterState' => $chapterState,
                'pfirst' => $prefirst,
                'plast' => $presecond,
                'pemail' => $preemail,
                'afirst' => $avpfirst,
                'alast' => $avpsecond,
                'aemail' => $avpemail,
                'mfirst' => $mvpfirst,
                'mlast' => $mvpsecond,
                'memail' => $mvpemail,
                'tfirst' => $trifirst,
                'tlast' => $trisecond,
                'temail' => $triemail,
                'sfirst' => $secfirst,
                'slast' => $secscond,
                'semail' => $secemail,
                'conf' => $conf,
                'content' => 'The following chapter has disbanded',
            ];

            Mail::send('emails.chapterdisband', $mailData, function ($message) use ($to_email) {
                $message->to($to_email, 'MOMS Club')->subject('Chapter Removal Request');
            });
            //Session::flash('success', 'Chapter has been successfully Disband');
            //   DB::commit();
            return redirect('/chapter/zapped')->with('success', 'Chapter has been successfully Zapped');
            //return redirect('/home');

            //return;

        } catch (\Exception $e) {
            echo $e->getMessage();
            // Rollback Transaction
            //   DB::rollback();
            exit;
        }
    }

    public function unZappedChapter($id)
    {
        $chapterid = $id;
        DB::table('chapters')
            ->where('id', $chapterid)
            ->update(['is_active' => 1, 'disband_reason' => '', 'zap_date' => null]);

        $userRelatedChpaterList = DB::table('board_details as bd')
            ->select('bd.user_id')
            ->where('bd.chapter_id', '=', $chapterid)
            ->get();
        if (count($userRelatedChpaterList) > 0) {
            foreach ($userRelatedChpaterList as $list) {
                $userId = $list->user_id;
                DB::table('users')
                    ->where('id', $userId)
                    ->update(['is_active' => 1]);
            }
        }
        DB::table('board_details')
            ->where('chapter_id', $chapterid)
            ->update(['is_active' => 1]);

        return redirect('/chapter/list')->with('success', 'Chapter has been successfully Unzapped');
    }

    public function updateZappedChapter(Request $request, $id)
    {

        $presInfoPre = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'bd.street_address as street', 'bd.city as city', 'bd.zip as zip', 'st.state_short_name as state')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_Active', '=', '0')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.id', $id)
            ->orderBy('chapters.id', 'DESC')
            ->get();

        $chapterId = $id;

        //President Info Rishi
        if ($request->get('ch_pre_email') != '') {
            $PREDetails = DB::table('board_details')
                ->select('board_id', 'user_id')
                ->where('chapter_id', '=', $chapterId)
                ->where('board_position_id', '=', '1')
                ->get();
            if (count($PREDetails) != 0) {
                $userId = $PREDetails[0]->user_id;
                $boardId = $PREDetails[0]->board_id;

                $user = User::find($userId);
                $user->first_name = $request->get('ch_pre_fname');
                $user->last_name = $request->get('ch_pre_lname');
                $user->email = $request->get('ch_pre_email');
                $user->updated_at = date('Y-m-d H:i:s');
                $user->save();

                DB::table('board_details')
                    ->where('board_id', $boardId)
                    ->update(['email' => $request->get('ch_pre_email')]);
            }
        }

        return redirect('/chapter/zapped')->with('success', 'Chapter has been updated');
    }

    public function chapterResetPassword(Request $request)
    {

        $input = request()->all();
        $pswd = $input['pswd'];
        $userId = $input['user_id'];
        $newPswd = Hash::make($pswd);
        DB::table('users')
            ->where('id', $userId)
            ->update(['password' => $newPswd]);

    }

    // For M2M Payments
    public function showDonation($id)
    {
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.conference_id as cor_confid', 'cd.email as cor_email', 'bd.email as bor_email', 'st.state_short_name as statename')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.id', $id)
            ->get();
        $maxDateLimit = date('Y-m-d');
        $minDateLimit = date('Y-m-d', strtotime('first day of january this year'));
        $minDateLimit = '';
        $data = ['chapterList' => $chapterList, 'maxDateLimit' => $maxDateLimit, 'minDateLimit' => $minDateLimit];

        return view('chapters.m2mdonation')->with($data);
    }

    public function createDonation(Request $request, $id)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $primaryCordEmail = $request->get('ch_pc_email');
        $boardPresEmail = $request->get('ch_pre_email');
        $chapter = Chapter::find($id);
        DB::beginTransaction();
        try {
            $chapter->m2m_date = $request->get('ch_m2m_date');
            $chapter->m2m_payment = $request->get('ch_m2m_payment');
            $chapter->sustaining_date = $request->get('ch_sustaining_date');
            $chapter->sustaining_donation = $request->get('ch_sustaining_donation');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();
            if ($request->get('ch_thanks') == 'on') {
                $to_email = $boardPresEmail;
                $cc_email = $primaryCordEmail;
                $mailData = [
                    'chapterName' => $request->get('ch_name'),
                    'chapterState' => $request->get('ch_state'),
                    'chapterPreEmail' => $request->get('ch_pre_email'),
                    'chapterAmount' => $request->get('ch_m2m_payment'),
                    'cordFname' => $request->get('ch_pc_fname'),
                    'cordLname' => $request->get('ch_pc_lname'),
                    'cordConf' => $request->get('ch_pc_confid'),
                ];

                //M2M Donation Thank You Email//
                Mail::to($to_email)
                    ->cc($cc_email, 'MOMS Club')
                    ->send(new PaymentsM2MChapterThankYou($mailData));
            }

            if ($request->get('ch_sustaining') == 'on') {
                $to_email = $boardPresEmail;
                $cc_email = $primaryCordEmail;
                $mailData = [
                    'chapterName' => $request->get('ch_name'),
                    'chapterState' => $request->get('ch_state'),
                    'chapterPreEmail' => $request->get('ch_pre_email'),
                    'chapterTotal' => $request->get('ch_sustaining_donation'),
                    'cordFname' => $request->get('ch_pc_fname'),
                    'cordLname' => $request->get('ch_pc_lname'),
                    'cordConf' => $request->get('ch_pc_confid'),
                ];

                //Sustaining Chapter Thank You Email//
                Mail::to($to_email)
                    ->cc($cc_email, 'MOMS Club')
                    ->send(new PaymentsSustainingChapterThankYou($mailData));
            }

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            // Log the error
            Log::error($e);

            return redirect('/reports/m2mdonation')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect('/reports/m2mdonation')->with('success', 'Donation has been successfully saved');
    }

    // For Re-Registrations
    public function showReRegistration()
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

        Session::put('positionid', $positionId);
        Session::put('secpositionid', $secPositionId);

        $all_chapters = false;
        if ($positionId == 6 || $positionId == 7 || $positionId == 10 || $secPositionId == 10 || $positionId == 25 || $secPositionId == 25 || ($corId == 423 && $positionId == 8)) {
            $full_list = true;
        } else {
            $full_list = false;
        }

        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $all_chapters = true;
                $reChapterList = $this->load_reregistration_chapter_list($corId, $corConfId, $corRegId, $full_list, $all_chapters, $positionId);
            } else {
                $reChapterList = $this->load_reregistration_chapter_list($corId, $corConfId, $corRegId, $full_list, $all_chapters, $positionId);
            }

        } else {
            $checkBoxStatus = '';

            $reChapterList = $this->load_reregistration_chapter_list($corId, $corConfId, $corRegId, $full_list, $all_chapters, $positionId);
        }
        $data = ['reChapterList' => $reChapterList, 'checkBoxStatus' => $checkBoxStatus, 'corConfId' => $corConfId];

        return view('chapters.reregistration')->with($data);
    }

    public function load_reregistration_chapter_list($corId, $conference_id, $region_id, $full_list, $all_chapters, $positionId)
    {
        if ($positionId == 7 || ($corId == 423 && $positionId == 8)) {
            $conference_clause = '';
        } else {
            $conference_clause = ' AND chapters.conference='.$conference_id;
        }
        if ($all_chapters) {
            $chapter_array = DB::select(DB::raw('SELECT chapters.id, chapters.primary_coordinator_id,chapters.conference, state.state_short_name, chapters.name, state.state_long_name as state,chapters.dues_last_paid, chapters.start_month_id, db_month.month_short_name, chapters.next_renewal_year, chapters.reg_notes
            FROM chapters
            INNER JOIN state
            ON chapters.state=state.id
            INNER JOIN db_month
            ON chapters.start_month_id=db_month.id
            WHERE chapters.is_active = 1'.
            $conference_clause.' '.
            ' ORDER BY chapters.state, chapters.name'));
        } else {
            if ($full_list) {
                $currentYear = date('Y');
                $currentMonth = date('m');
                $chapter_array = DB::select(DB::raw("SELECT chapters.id,chapters.conference, chapters.primary_coordinator_id,state.state_short_name, chapters.name, state.state_long_name as state,chapters.dues_last_paid, chapters.start_month_id, db_month.month_short_name, chapters.next_renewal_year, chapters.reg_notes
                FROM chapters
                INNER JOIN state
                ON chapters.state=state.id
                INNER JOIN db_month
                ON chapters.start_month_id=db_month.id
                WHERE (chapters.next_renewal_year < $currentYear
                OR (chapters.next_renewal_year = $currentYear AND chapters.start_month_id <= $currentMonth))".
                $conference_clause.' '.
                ' AND chapters.is_active = 1
                ORDER BY chapters.next_renewal_year DESC,chapters.start_month_id DESC'));
            } else {
                $currentYear = date('Y');
                $currentMonth = date('m');
                $chapter_array = DB::select(DB::raw("SELECT chapters.id, chapters.primary_coordinator_id,chapters.conference, state.state_short_name, state.state_long_name as state, chapters.name, chapters.dues_last_paid, chapters.start_month_id, db_month.month_short_name, chapters.next_renewal_year, chapters.reg_notes
                FROM chapters
                INNER JOIN state
                ON chapters.state=state.id
                INNER JOIN db_month
                ON chapters.start_month_id=db_month.id
                WHERE (chapters.next_renewal_year < $currentYear
                OR (chapters.next_renewal_year = $currentYear AND chapters.start_month_id <= $currentMonth))".
                $conference_clause.' '.
                " AND chapters.region = $region_id
                AND chapters.is_active = 1
                ORDER BY chapters.next_renewal_year DESC ,chapters.start_month_id DESC"));
            }
        }

        return $chapter_array;
    }

    public function showReRegNotes($id)
    {
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.conference_id as cor_confid', 'cd.email as cor_email', 'bd.email as bor_email', 'st.state_short_name as statename')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.id', $id)
            ->get();
        $maxDateLimit = date('Y-m-d');
        $minDateLimit = date('Y-m-d', strtotime('first day of january this year'));
        $minDateLimit = '';
        $data = ['chapterList' => $chapterList, 'maxDateLimit' => $maxDateLimit, 'minDateLimit' => $minDateLimit];

        return view('chapters.re-regnotes')->with($data);
    }

    public function makeReRegNotes(Request $request, $id)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $nextRenewalYear = $request->get('ch_nxt_renewalyear');

        //$nextRenewalYear = date('Y');
        $primaryCordEmail = $request->get('ch_pc_email');
        $boardPresEmail = $request->get('ch_pre_email');
        $chapter = Chapter::find($id);
        DB::beginTransaction();
        try {

            $chapter->reg_notes = $request->get('ch_regnotes');

            $chapter->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            // Log the error
            Log::error($e);

            return redirect('/chapter/re-registration')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect('/chapter/re-registration')->with('success', 'Your Re-Regisration Notes have been saved');
    }

    public function showPayment($id)
    {
        $chapterList = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.conference_id as cor_confid', 'cd.email as cor_email', 'bd.email as bor_email', 'st.state_short_name as statename')
            ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.id', $id)
            ->get();
        $maxDateLimit = date('Y-m-d');
        $minDateLimit = date('Y-m-d', strtotime('first day of january this year'));
        $minDateLimit = '';
        $data = ['chapterList' => $chapterList, 'maxDateLimit' => $maxDateLimit, 'minDateLimit' => $minDateLimit];

        return view('chapters.payment')->with($data);
    }

    public function makePayment(Request $request, $id)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $nextRenewalYear = $request->get('ch_nxt_renewalyear');

        //$nextRenewalYear = date('Y');
        $primaryCordEmail = $request->get('ch_pc_email');
        $boardPresEmail = $request->get('ch_pre_email');
        $chapter = Chapter::find($id);
        DB::beginTransaction();
        try {
            $chapter->dues_last_paid = $request->get('PaymentDate');
            $chapter->members_paid_for = $request->get('MembersPaidFor');
            $chapter->reg_notes = $request->get('ch_regnotes');
            $chapter->next_renewal_year = $nextRenewalYear + 1;
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();
            if ($request->get('ch_notify') == 'on') {

                $mailData = [
                    'chapterName' => $request->get('ch_name'),
                    'chapterState' => $request->get('ch_state'),
                    'chapterPreEmail' => $request->get('ch_pre_email'),
                    'chapterDate' => $request->get('PaymentDate'),
                    'chapterMembers' => $request->get('MembersPaidFor'),
                    'cordFname' => $request->get('ch_pc_fname'),
                    'cordLname' => $request->get('ch_pc_lname'),
                    'cordConf' => $request->get('ch_pc_confid'),
                ];

                //Payment Thank You Email//
                $to_email = $boardPresEmail;
                $cc_email = $primaryCordEmail;

                Mail::to($to_email, 'MOMS Club')
                    ->cc($cc_email)
                    ->send(new PaymentsReRegChapterThankYou($mailData));
            }

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            // Log the error
            Log::error($e);

            return redirect('/chapter/re-registration')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect('/chapter/re-registration')->with('success', 'Payment has been successfully payment saved');
    }

    public function reminderReRegistration()
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corName = $corDetails['first_name'].' '.$corDetails['last_name'];

        $month = date('m');
        $year = date('Y');
        $lastMonth = $month - 1;
        $lastYear = $year - 1;

        if ($month == 1) {
            $lastMonth = 12;
            $lastYear = $year - 1;
        }

        $startDate = \Carbon\Carbon::create($year, $month, 30);
        $lastYearDate = \Carbon\Carbon::create($lastYear, $lastMonth, 1);
        $dueDate = \Carbon\Carbon::create($year, $lastMonth, 10);

        // Convert $month to words
        $monthInWords = strftime('%B', strtotime("2000-$month-01"));

        // Format dates as "mm-dd-yyyy"
        $startDateFormatted = date('m-d-Y', strtotime($startDate));
        $lastYearDateFormatted = date('m-d-Y', strtotime($lastYearDate));
        $dueDateFormatted = date('m-d-Y', strtotime($dueDate));

        $chapters = Chapter::select('chapters.name as chapter_name', 'state.state_short_name as chapter_state', 'board_details.email as bor_email',
            'chapters.primary_coordinator_id as pcid', 'chapters.email as ch_email', 'chapters.start_month_id as start_month',
            'board_details.board_position_id')
            ->join('state', 'chapters.state', '=', 'state.id')
            ->join('board_details', 'chapters.id', '=', 'board_details.chapter_id')
            ->whereIn('board_details.board_position_id', [1, 2, 3, 4, 5])
            ->where('chapters.conference', $corConfId)
            ->where('chapters.start_month_id', $month)
            ->where('chapters.next_renewal_year', $year)
            ->where('chapters.is_active', 1)
            ->get();

        $cc_email = [];
        $chapterEmails = []; // Store email addresses for each chapter
        $coordinatorEmails = []; // Store coordinator email addresses by chapter
        $chapterChEmails = []; // Store ch_email addresses by chapter
        $firstChapter = $chapters->first(); // Get the first chapter to fetch the shared data

        if ($firstChapter) {
            $sharedChapterState = $firstChapter->chapter_state;

            foreach ($chapters as $chapter) {
                if ($chapter->bor_email) {
                    $chapterEmails[$chapter->chapter_name][] = $chapter->bor_email; // Store emails by chapter name

                    $cc_email1 = $this->getCCMail($chapter->pcid);
                    $cc_email1 = array_filter($cc_email1);

                    if (! empty($cc_email1)) {
                        $coordinatorEmails[$chapter->chapter_name] = $cc_email1; // Store coordinator emails by chapter
                    }
                }

                // Check if ch_email is not null before adding it to the chapterChEmails array
                if ($chapter->ch_email) {
                    $chapterChEmails[$chapter->chapter_name] = $chapter->ch_email;
                }
            }
        }
        foreach ($chapterEmails as $chapterName => $emailRecipients) {
            $mailData = [
                'chapterName' => $chapterName,
                'chapterState' => $sharedChapterState,
                'lastYearDate' => $lastYearDateFormatted,
                'startMonth' => $monthInWords,
                'reRegDate' => $startDateFormatted,
                'dueDate' => $dueDateFormatted,
            ];

            $cc_email = $coordinatorEmails[$chapterName] ?? []; // Get coordinator emails for this chapter

            // Check if chapterChEmails exists before adding it to the email recipients array
            if (isset($chapterChEmails[$chapterName])) {
                $emailRecipients[] = $chapterChEmails[$chapterName];
            }

            // Re-Registration Reminder Email for this chapter
            if (! empty($emailRecipients)) {
                Mail::to($emailRecipients)
                    ->cc($cc_email)
                    ->send(new PaymentsReRegReminder($mailData));
            }
        }

        try {
            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            echo $e->getMessage();
            exit();

            // Log the error
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect('/chapter/re-registration')->with('success', 'Re-Registration Reminders have been successfully sent.');

    }

    public function lateReRegistration()
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corName = $corDetails['first_name'].' '.$corDetails['last_name'];

        $month = date('m');
        $year = date('Y');
        $startMonth = $month - 1;
        $lastMonth = $month - 2;
        $lastYear = $year - 1;

        if ($month == 1) {
            $lastMonth = 12;
            $lastYear = $year - 1;
        }

        $startDate = \Carbon\Carbon::create($year, $startMonth, 30);
        $lastYearDate = \Carbon\Carbon::create($lastYear, $lastMonth, 1);

        // Convert $month to words
        $startMonthInWords = strftime('%B', strtotime("2000-$startMonth-01"));
        $dueMonthInWords = strftime('%B', strtotime("2000-$month-01"));

        // Format dates as "mm-dd-yyyy"
        $startDateFormatted = date('m-d-Y', strtotime($startDate));
        $lastYearDateFormatted = date('m-d-Y', strtotime($lastYearDate));

        $chapters = Chapter::select('chapters.name as chapter_name', 'state.state_short_name as chapter_state', 'board_details.email as bor_email',
            'chapters.primary_coordinator_id as pcid', 'chapters.email as ch_email', 'chapters.start_month_id as start_month',
            'board_details.board_position_id')
            ->join('state', 'chapters.state', '=', 'state.id')
            ->join('board_details', 'chapters.id', '=', 'board_details.chapter_id')
            ->whereIn('board_details.board_position_id', [1, 2, 3, 4, 5])
            ->where('chapters.conference', $corConfId)
            ->where('chapters.start_month_id', $month - 1)
            ->where('chapters.next_renewal_year', $year)
            ->where('chapters.is_active', 1)
            ->get();

        $cc_email = [];
        $chapterEmails = []; // Store email addresses for each chapter
        $coordinatorEmails = []; // Store coordinator email addresses by chapter
        $chapterChEmails = []; // Store ch_email addresses by chapter
        $firstChapter = $chapters->first(); // Get the first chapter to fetch the shared data

        if ($firstChapter) {
            $sharedChapterState = $firstChapter->chapter_state;

            foreach ($chapters as $chapter) {
                if ($chapter->bor_email) {
                    $chapterEmails[$chapter->chapter_name][] = $chapter->bor_email; // Store emails by chapter name

                    $cc_email1 = $this->getCCMail($chapter->pcid);
                    $cc_email1 = array_filter($cc_email1);

                    if (! empty($cc_email1)) {
                        $coordinatorEmails[$chapter->chapter_name] = $cc_email1; // Store coordinator emails by chapter
                    }
                }

                // Check if ch_email is not null before adding it to the chapterChEmails array
                if ($chapter->ch_email) {
                    $chapterChEmails[$chapter->chapter_name] = $chapter->ch_email;
                }
            }
        }

        foreach ($chapterEmails as $chapterName => $emailRecipients) {
            $mailData = [
                'chapterName' => $chapterName,
                'chapterState' => $sharedChapterState,
                'lastYearDate' => $lastYearDateFormatted,
                'reRegDate' => $startDateFormatted,
                'startMonth' => $startMonthInWords,
                'dueMonth' => $dueMonthInWords,
            ];

            $cc_email = $coordinatorEmails[$chapterName] ?? []; // Get coordinator emails for this chapter

            // Check if chapterChEmails exists before adding it to the email recipients array
            if (isset($chapterChEmails[$chapterName])) {
                $emailRecipients[] = $chapterChEmails[$chapterName];
            }

            // Re-Registration Reminder Email for this chapter
            if (! empty($emailRecipients)) {
                Mail::to($emailRecipients)
                    ->cc($cc_email)
                    ->send(new PaymentsReRegLate($mailData));
            }
        }

        try {
            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            echo $e->getMessage();
            exit();

            // Log the error
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect('/chapter/re-registration')->with('success', 'Re-Registration Late Notices have been successfully sent.');

    }

    public function getCCMail($pcid)
    {
        $reportingList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('id', '=', $pcid)
            ->get();
        foreach ($reportingList as $key => $value) {
            $reportingList[$key] = (array) $value;
        }
        $filterReportingList = array_filter($reportingList[0]);
        unset($filterReportingList['id']);
        unset($filterReportingList['layer0']);
        $filterReportingList = array_reverse($filterReportingList);
        $str = '';
        $array_rows = count($filterReportingList);
        $down_line_email = [];
        foreach ($filterReportingList as $key => $val) {
            //if($corId != $val && $val >1){
            if ($val > 1) {
                $corList = DB::table('coordinator_details as cd')
                    ->select('cd.email as cord_email')
                    ->where('cd.coordinator_id', '=', $val)
                    ->where('cd.is_active', '=', 1)
                    ->get();
                if (count($corList) > 0) {
                    if ($down_line_email == '') {
                        $down_line_email[] = $corList[0]->cord_email;
                    } else {
                        $down_line_email[] = $corList[0]->cord_email;
                    }
                }
            }
        }

        return $down_line_email;
    }

    /**
     * View the Award Details
     */
    public function awardsView($id)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $financial_report_array = FinancialReport::find($id);

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('board_details as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id', 'ASC')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id', 'ASC')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name', 'ASC')
            ->get();

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', '=', $corConfId)
            ->where('cd.position_id', '<=', '6')
            ->where('cd.position_id', '>=', '1')
            ->where('cd.is_active', '=', '1')
            ->orWhere('cd.position_id', '=', '25')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name', 'ASC')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $chapterList[0]->start_month_id;

        $data = ['currentMonth' => $currentMonth, 'chapterList' => $chapterList, 'regionList' => $regionList, 'primaryCoordinatorList' => $primaryCoordinatorList, 'financial_report_array' => $financial_report_array, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('chapters.awardsview')->with($data);
    }

    public function updateAwards(Request $request, $id)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $report = FinancialReport::find($id);
        DB::beginTransaction();
        try {
            $report->award_1_nomination_type = $request->get('NominationType1');
            $report->award_1_outstanding_project_desc = $request->get('AwardDesc1');
            $report->check_award_1_approved = (int) $request->has('approved_1');
            $report->award_2_nomination_type = $request->get('NominationType2');
            $report->award_2_outstanding_project_desc = $request->get('AwardDesc2');
            $report->check_award_2_approved = (int) $request->has('approved_2');
            $report->award_3_nomination_type = $request->get('NominationType3');
            $report->award_3_outstanding_project_desc = $request->get('AwardDesc3');
            $report->check_award_3_approved = (int) $request->has('approved_3');
            $report->award_4_nomination_type = $request->get('NominationType4');
            $report->award_4_outstanding_project_desc = $request->get('AwardDesc4');
            $report->check_award_4_approved = (int) $request->has('approved_4');
            $report->award_5_nomination_type = $request->get('NominationType5');
            $report->award_5_outstanding_project_desc = $request->get('AwardDesc5');
            $report->check_award_5_approved = (int) $request->has('approved_5');
            $report->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            // Log the error
            Log::error($e);

            return redirect('/yearreports/chapterawards')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect('/yearreports/chapterawards')->with('success', 'Chapter Awards have been successfully updated');
    }

    //For Board Info Received
    public function showBoardInfo($chapterId)
    {
        $chapterDetails = Chapter::find($chapterId);
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id', 'ASC')
            ->get();

        $chapterState = DB::table('state')
            ->select('state_short_name')
            ->where('id', '=', $chapterDetails->state)
            ->get();
        $chapterState = $chapterState[0]->state_short_name;
        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $chapterDetails->start_month_id;

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('board_details as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '1')
            ->get();

        $PREDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.board_position_id', 'bd.street_address as pre_addr', 'bd.city as pre_city', 'bd.zip as pre_zip', 'bd.phone as pre_phone', 'bd.state as pre_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        if (count($PREDetails) == 0) {
            $PREDetails[0] = ['pre_fname' => '', 'pre_lname' => '', 'pre_email' => '', 'pre_addr' => '', 'pre_city' => '', 'pre_zip' => '', 'pre_phone' => '', 'pre_state' => '', 'ibd_id' => ''];
            $PREDetails = json_decode(json_encode($PREDetails));
        }

        $AVPDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id', 'bd.street_address as avp_addr', 'bd.city as avp_city', 'bd.zip as avp_zip', 'bd.phone as avp_phone', 'bd.state as avp_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '2')
            ->get();
        if (count($AVPDetails) == 0) {
            $AVPDetails[0] = ['avp_fname' => '', 'avp_lname' => '', 'avp_email' => '', 'avp_addr' => '', 'avp_city' => '', 'avp_zip' => '', 'avp_phone' => '', 'avp_state' => '', 'ibd_id' => ''];
            $AVPDetails = json_decode(json_encode($AVPDetails));
        }

        $MVPDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id', 'bd.street_address as mvp_addr', 'bd.city as mvp_city', 'bd.zip as mvp_zip', 'bd.phone as mvp_phone', 'bd.state as mvp_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '3')
            ->get();
        if (count($MVPDetails) == 0) {
            $MVPDetails[0] = ['mvp_fname' => '', 'mvp_lname' => '', 'mvp_email' => '', 'mvp_addr' => '', 'mvp_city' => '', 'mvp_zip' => '', 'mvp_phone' => '', 'mvp_state' => '', 'ibd_id' => ''];
            $MVPDetails = json_decode(json_encode($MVPDetails));
        }

        $TRSDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id', 'bd.street_address as trs_addr', 'bd.city as trs_city', 'bd.zip as trs_zip', 'bd.phone as trs_phone', 'bd.state as trs_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '4')
            ->get();
        if (count($TRSDetails) == 0) {
            $TRSDetails[0] = ['trs_fname' => '', 'trs_lname' => '', 'trs_email' => '', 'trs_addr' => '', 'trs_city' => '', 'trs_zip' => '', 'trs_phone' => '', 'trs_state' => '', 'ibd_id' => ''];
            $TRSDetails = json_decode(json_encode($TRSDetails));
        }

        $SECDetails = DB::table('incoming_board_member as bd')
            ->select('bd.first_name as sec_fname', 'bd.last_name as sec_lname', 'bd.email as sec_email', 'bd.board_position_id', 'bd.street_address as sec_addr', 'bd.city as sec_city', 'bd.zip as sec_zip', 'bd.phone as sec_phone', 'bd.state as sec_state', 'bd.id as ibd_id')
            ->where('bd.chapter_id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '5')
            ->get();
        if (count($SECDetails) == 0) {
            $SECDetails[0] = ['sec_fname' => '', 'sec_lname' => '', 'sec_email' => '', 'sec_addr' => '', 'sec_city' => '', 'sec_zip' => '', 'sec_phone' => '', 'sec_state' => '', 'ibd_id' => ''];
            $SECDetails = json_decode(json_encode($SECDetails));
        }

        $data = ['chapterState' => $chapterState, 'currentMonth' => $currentMonth, 'foundedMonth' => $foundedMonth, 'stateArr' => $stateArr, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'PREDetails' => $PREDetails, 'chapterList' => $chapterList];

        return view('chapters.boardinfo')->with($data);
    }

    public function createBoardInfo(Request $request, $chapter_id)
    {
        $user = Auth::user();
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;
        if ($request->get('submit_type') == 'activate_board') {
            $status = $this->activateBoard($chapter_id, $lastUpdatedBy);
            if ($status == 'success') {
                return redirect('/yearreports/boardinfo')->with('success', 'Board Info has been successfully activated');
            } elseif ($status == 'fail') {
                return redirect('/yearreports/boardinfo')->with('fail', 'Something went wrong, Please try again.');
            }/*else{
                return redirect('/home')->with('fail', 'Email already used in the system. Please try with new one.');
            }
            exit;*/
        }

        $chapter = Chapter::find($chapter_id);
        $boundaryStatus = $request->get('BoundaryStatus');
        $issue_note = $request->get('BoundaryIssue');
        //Boundary Issues Correct 0 | Not Correct 1
        if ($boundaryStatus == 0) {
            $issue_note = '';
        }

        DB::beginTransaction();
        try {
            $chapter->inquiries_contact = $request->get('InquiriesContact');
            $chapter->boundary_issues = $request->get('BoundaryStatus');
            $chapter->boundary_issue_notes = $issue_note;
            $chapter->new_board_submitted = 1;
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();

            //President Info
            if ($request->get('ch_pre_fname') != '' && $request->get('ch_pre_lname') != '' && $request->get('ch_pre_email') != '') {
                $PREDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $chapter_id)
                    ->where('board_position_id', '=', '1')
                    ->get();
                $id = $request->get('presID');
                if (count($PREDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $id)
                        ->update(['first_name' => $request->get('ch_pre_fname'),
                            'last_name' => $request->get('ch_pre_lname'),
                            'email' => $request->get('ch_pre_email'),
                            'street_address' => $request->get('ch_pre_street'),
                            'city' => $request->get('ch_pre_city'),
                            'state' => $request->get('ch_pre_state'),
                            'zip' => $request->get('ch_pre_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_pre_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                } else {
                    $board = DB::table('incoming_board_member')->insert(
                        ['first_name' => $request->get('ch_pre_fname'),
                            'last_name' => $request->get('ch_pre_lname'),
                            'email' => $request->get('ch_pre_email'),
                            'board_position_id' => 1,
                            'chapter_id' => $chapter_id,
                            'street_address' => $request->get('ch_pre_street'),
                            'city' => $request->get('ch_pre_city'),
                            'state' => $request->get('ch_pre_state'),
                            'zip' => $request->get('ch_pre_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_pre_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }
            //AVP Info
            if ($request->get('AVPVacant') == 'on') {
                $id = $request->get('avpID');
                DB::table('incoming_board_member')
                    ->where('id', $id)
                    ->delete();
            }
            if ($request->get('ch_avp_fname') != '' && $request->get('ch_avp_lname') != '' && $request->get('ch_avp_email') != '') {
                $AVPDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $chapter_id)
                    ->where('board_position_id', '=', '2')
                    ->get();
                $id = $request->get('avpID');
                if (count($AVPDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $id)
                        ->update(['first_name' => $request->get('ch_avp_fname'),
                            'last_name' => $request->get('ch_avp_lname'),
                            'email' => $request->get('ch_avp_email'),
                            'street_address' => $request->get('ch_avp_street'),
                            'city' => $request->get('ch_avp_city'),
                            'state' => $request->get('ch_avp_state'),
                            'zip' => $request->get('ch_avp_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_avp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                } else {

                    $board = DB::table('incoming_board_member')->insert(
                        [
                            'first_name' => $request->get('ch_avp_fname'),
                            'last_name' => $request->get('ch_avp_lname'),
                            'email' => $request->get('ch_avp_email'),
                            'board_position_id' => 2,
                            'chapter_id' => $chapter_id,
                            'street_address' => $request->get('ch_avp_street'),
                            'city' => $request->get('ch_avp_city'),
                            'state' => $request->get('ch_avp_state'),
                            'zip' => $request->get('ch_avp_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_avp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }
            //MVP Info
            if ($request->get('MVPVacant') == 'on') {
                $id = $request->get('mvpID');
                DB::table('incoming_board_member')
                    ->where('id', $id)
                    ->delete();
            }
            if ($request->get('ch_mvp_fname') != '' && $request->get('ch_mvp_lname') != '' && $request->get('ch_mvp_email') != '') {
                $MVPDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $chapter_id)
                    ->where('board_position_id', '=', '3')
                    ->get();
                $id = $request->get('mvpID');
                if (count($MVPDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $id)
                        ->update(['first_name' => $request->get('ch_mvp_fname'),
                            'last_name' => $request->get('ch_mvp_lname'),
                            'email' => $request->get('ch_mvp_email'),
                            'street_address' => $request->get('ch_mvp_street'),
                            'city' => $request->get('ch_mvp_city'),
                            'state' => $request->get('ch_mvp_state'),
                            'zip' => $request->get('ch_mvp_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_mvp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                } else {

                    $board = DB::table('incoming_board_member')->insert(
                        [
                            'first_name' => $request->get('ch_mvp_fname'),
                            'last_name' => $request->get('ch_mvp_lname'),
                            'email' => $request->get('ch_mvp_email'),
                            'board_position_id' => 3,
                            'chapter_id' => $chapter_id,
                            'street_address' => $request->get('ch_mvp_street'),
                            'city' => $request->get('ch_mvp_city'),
                            'state' => $request->get('ch_mvp_state'),
                            'zip' => $request->get('ch_mvp_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_mvp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }
            //TRS Info
            if ($request->get('TreasVacant') == 'on') {
                $id = $request->get('trsID');
                DB::table('incoming_board_member')
                    ->where('id', $id)
                    ->delete();
            }
            if ($request->get('ch_trs_fname') != '' && $request->get('ch_trs_lname') != '' && $request->get('ch_trs_email') != '') {
                $TRSDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $chapter_id)
                    ->where('board_position_id', '=', '4')
                    ->get();
                $id = $request->get('trsID');
                if (count($TRSDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $id)
                        ->update(['first_name' => $request->get('ch_trs_fname'),
                            'last_name' => $request->get('ch_trs_lname'),
                            'email' => $request->get('ch_trs_email'),
                            'street_address' => $request->get('ch_trs_street'),
                            'city' => $request->get('ch_trs_city'),
                            'state' => $request->get('ch_trs_state'),
                            'zip' => $request->get('ch_trs_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_trs_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);

                } else {

                    $board = DB::table('incoming_board_member')->insert(
                        [
                            'first_name' => $request->get('ch_trs_fname'),
                            'last_name' => $request->get('ch_trs_lname'),
                            'email' => $request->get('ch_trs_email'),
                            'board_position_id' => 4,
                            'chapter_id' => $chapter_id,
                            'street_address' => $request->get('ch_trs_street'),
                            'city' => $request->get('ch_trs_city'),
                            'state' => $request->get('ch_trs_state'),
                            'zip' => $request->get('ch_trs_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_trs_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }
            //SEC Info
            if ($request->get('SecVacant') == 'on') {
                $id = $request->get('secID');
                DB::table('incoming_board_member')
                    ->where('id', $id)
                    ->delete();
            }
            if ($request->get('ch_sec_fname') != '' && $request->get('ch_sec_lname') != '' && $request->get('ch_sec_email') != '') {
                $SECDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $chapter_id)
                    ->where('board_position_id', '=', '5')
                    ->get();
                $id = $request->get('secID');
                if (count($SECDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $id)
                        ->update(['first_name' => $request->get('ch_sec_fname'),
                            'last_name' => $request->get('ch_sec_lname'),
                            'email' => $request->get('ch_sec_email'),
                            'street_address' => $request->get('ch_sec_street'),
                            'city' => $request->get('ch_sec_city'),
                            'state' => $request->get('ch_sec_state'),
                            'zip' => $request->get('ch_sec_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_sec_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);

                } else {
                    $board = DB::table('incoming_board_member')->insert(
                        [
                            'first_name' => $request->get('ch_sec_fname'),
                            'last_name' => $request->get('ch_sec_lname'),
                            'email' => $request->get('ch_sec_email'),
                            'board_position_id' => 5,
                            'chapter_id' => $chapter_id,
                            'street_address' => $request->get('ch_sec_street'),
                            'city' => $request->get('ch_sec_city'),
                            'state' => $request->get('ch_sec_state'),
                            'zip' => $request->get('ch_sec_zip'),
                            'country' => 'USA',
                            'phone' => $request->get('ch_sec_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            // Log the error
            Log::error($e);

            return redirect('/chapter/list')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect('/chapter/list')->with('success', 'Board Info has been Saved');
    }

    public function activateBoard($chapter_id, $lastUpdatedBy)
    {
        $message = '';
        //Fetching New Board Info from Incoming Board Members
        $incomingBoardDetails = DB::table('incoming_board_member')
            ->select('*')
            ->where('chapter_id', '=', $chapter_id)
            ->orderBy('board_position_id', 'ASC')
            ->get();
        $countIncomingBoardDetails = count($incomingBoardDetails);
        if ($countIncomingBoardDetails > 0) {
            DB::beginTransaction();
            try {
                //Fetching Existing Board Members from Board Details
                $boardDetails = DB::table('board_details')
                    ->select('*')
                    ->where('chapter_id', '=', $chapter_id)
                    ->get();
                $countBoardDetails = count($boardDetails);
                if ($countBoardDetails > 0) {
                    //Insert Outgoing Board Members
                    for ($i = 0; $i < $countBoardDetails; $i++) {
                        $board = DB::table('outgoing_board_member')->insert(
                            ['first_name' => $boardDetails[$i]->first_name,
                                'last_name' => $boardDetails[$i]->last_name,
                                'email' => $boardDetails[$i]->email,
                                'password' => $boardDetails[$i]->password,
                                'board_position_id' => $boardDetails[$i]->board_position_id,
                                'chapter_id' => $chapter_id,
                                'street_address' => $boardDetails[$i]->street_address,
                                'city' => $boardDetails[$i]->city,
                                'state' => $boardDetails[$i]->state,
                                'zip' => $boardDetails[$i]->zip,
                                'country' => $boardDetails[$i]->country,
                                'phone' => $boardDetails[$i]->phone,
                                'last_updated_by' => $lastUpdatedBy,
                                'last_updated_date' => date('Y-m-d H:i:s'),
                                'board_id' => $boardDetails[$i]->board_id,
                                'user_id' => $boardDetails[$i]->user_id]);

                        //Delete Details of Board memebers from users table
                        DB::table('users')
                            ->where('id', $boardDetails[$i]->user_id)
                            ->delete();
                    }
                }
                //Delete Details of Board memebers from Board Detials table
                DB::table('board_details')
                    ->where('chapter_id', $chapter_id)
                    ->delete();

                //Create & Activate Details of Board memebers from Incoming Board Members
                for ($i = 0; $i < $countIncomingBoardDetails; $i++) {
                    $userId = DB::table('users')->insertGetId(
                        ['first_name' => $incomingBoardDetails[$i]->first_name,
                            'last_name' => $incomingBoardDetails[$i]->last_name,
                            'email' => $incomingBoardDetails[$i]->email,
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1]
                    );

                    $boardIdArr = DB::table('board_details')
                        ->select('board_details.board_id')
                        ->orderBy('board_details.board_id', 'DESC')
                        ->limit(1)
                        ->get();
                    $boardId = $boardIdArr[0]->board_id + 1;

                    $board = DB::table('board_details')->insert(
                        ['user_id' => $userId,
                            'board_id' => $boardId,
                            'first_name' => $incomingBoardDetails[$i]->first_name,
                            'last_name' => $incomingBoardDetails[$i]->last_name,
                            'email' => $incomingBoardDetails[$i]->email,
                            'password' => Hash::make('TempPass4You'),
                            'remember_token' => '',
                            'board_position_id' => $incomingBoardDetails[$i]->board_position_id,
                            'chapter_id' => $chapter_id,
                            'street_address' => $incomingBoardDetails[$i]->street_address,
                            'city' => $incomingBoardDetails[$i]->city,
                            'state' => $incomingBoardDetails[$i]->state,
                            'zip' => $incomingBoardDetails[$i]->zip,
                            'country' => 'USA',
                            'phone' => $incomingBoardDetails[$i]->phone,
                            //'vacant' => $input['ch_pre_fname'],
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                            'is_active' => 1]
                    );
                }
                //Update Chapter after Board Active
                DB::update('UPDATE chapters SET new_board_active = ? where id = ?', [1, $chapter_id]);

                DB::table('incoming_board_member')
                    ->where('chapter_id', $chapter_id)
                    ->delete();

                DB::commit();
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollback();
                $errorCode = $e->errorInfo[1];
                if ($errorCode == 1062) {
                    return $message = $e->errorInfo[2];
                } else {
                    return $message = 'fail';
                }
            }

            return $message = 'success';
        }
    }

    //Financial Report for Coordinator side for Reviewing of Chapters
    public function showFinancialReport($chapterId)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $loggedInName = $corDetails['first_name'].' '.$corDetails['last_name'];
        $positionId = $corDetails['position_id'];
        Session::put('positionid', $positionId);

        $financial_report_array = FinancialReport::find($chapterId);
        $chapterDetails = DB::table('chapters')
            ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.financial_report_received as financial_report_received', 'chapters.primary_coordinator_id as pcid', 'chapters.balance as balance', 'st.state_short_name as state')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('chapters.id', '=', $chapterId)
            ->get();

        $emailListCord = '';
        $cc_string = '';

        $submitted = $chapterDetails[0]->financial_report_received;
        $balance = $chapterDetails[0]->balance;
        $pcid = $chapterDetails[0]->pcid;
        $reportingList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('id', '=', $pcid)
            ->get();
        foreach ($reportingList as $key => $value) {
            $reportingList[$key] = (array) $value;
        }
        $filterReportingList = array_filter($reportingList[0]);
        unset($filterReportingList['id']);
        unset($filterReportingList['layer0']);
        $filterReportingList = array_reverse($filterReportingList);
        $filterReportingList = array_reverse($filterReportingList);
        $array_rows = count($filterReportingList);
        //var_dump($filterReportingList);

        foreach ($filterReportingList as $key => $val) {
            $corList = DB::table('coordinator_details as cd')
                ->select('cd.coordinator_id as cid', 'cd.first_name as fname', 'cd.last_name as lname', 'cp.short_title as pos')
                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                ->where('cd.coordinator_id', '=', $val)
                ->where('cd.is_active', '=', 1)
                ->get();
            if (count($corList) != 0) {
                $reviewerList[] = ['cid' => $corList[0]->cid, 'cname' => $corList[0]->fname.' '.$corList[0]->lname.' ('.$corList[0]->pos.')'];
            }
        }
        // var_dump($reviewerList);
        //print $financial_report_array['reviewer_id'];
        $data = ['reviewerList' => $reviewerList, 'chapterid' => $chapterId, 'financial_report_array' => $financial_report_array, 'emailListCord' => $emailListCord, 'cc_string' => $cc_string, 'loggedInName' => $loggedInName, 'balance' => $balance, 'submitted' => $submitted, 'chapterDetails' => $chapterDetails];

        return view('chapters.financial')->with($data);
    }

    public function storeFinancialReport(Request $request, $chapter_id)
    {
        //Basic Settings
        //$target_dir = $uploadedFilePath = "F:/xampp/htdocs/momsclub-dev/uploads/";  //Windows
        $target_dir = $uploadedFilePath = '/home/momsclub/public_html/mimi/uploads/';  //Live
        //Configure Dropbox Application
        $dropboxKey = '7naxrobjsc02z0c';
        $dropboxSecret = 'd3xuzouwlv0p7rm';
        $dropboxToken = '__DhDVdXdTIAAAAAAAAAAQY4Muzhmj5mMaz0k6zXInYJU8CMG8m5HzRxmBI_d27t';

        $input = request()->all();
        $farthest_step_visited_coord = $input['FurthestStep'];
        $reviewer_id = $input['AssignedReviewer'];
        $reportReceived = $input['submitted'];
        $submitType = $input['submit_type'];
        if (! $reportReceived && $submitType == 'UnSubmit') {
            DB::update('UPDATE chapters SET financial_report_received = ? where id = ?', [null, $chapter_id]);
            DB::update('UPDATE financial_report SET farthest_step_visited_coord = ? where chapter_id = ?', [13, $chapter_id]);
            DB::update('UPDATE financial_report SET reviewer_id = ? where chapter_id = ?', [null, $chapter_id]);

            return redirect()->back()->with('success', 'Report has been successfully Unsubmitted');
            exit;
        }

        $step_1_notes_log = $input['Step1_Log'];
        $step_2_notes_log = $input['Step2_Log'];
        $step_3_notes_log = $input['Step3_Log'];
        $step_4_notes_log = $input['Step4_Log'];
        $step_5_notes_log = $input['Step5_Log'];
        $step_6_notes_log = $input['Step6_Log'];
        $step_7_notes_log = $input['Step7_Log'];
        $step_8_notes_log = $input['Step8_Log'];
        $post_balance = $input['post_balance'];
        $step_9_notes_log = $input['Step9_Log'];
        $step_10_notes_log = $input['Step10_Log'];
        $step_11_notes_log = $input['Step11_Log'];

        // Step 1
        if (isset($input['checkRosterAttached']) && $input['checkRosterAttached'] == 'no') {
            $input['check_roster_attached'] = 0;
        } elseif (isset($input['checkRosterAttached'])) {
            $input['check_roster_attached'] = 1;
        } else {
            $input['check_roster_attached'] = null;
        }

        if (isset($input['checkRenewalSeemsRight']) && $input['checkRenewalSeemsRight'] == 'no') {
            $input['check_renewal_seems_right'] = 0;
        } elseif (isset($input['checkRenewalSeemsRight'])) {
            $input['check_renewal_seems_right'] = 1;
        } else {
            $input['check_renewal_seems_right'] = null;
        }

        $check_roster_attached = $input['check_roster_attached'];
        $check_renewal_seems_right = $input['check_renewal_seems_right'];

        // Step 3
        if (isset($input['checkServiceProject']) && $input['checkServiceProject'] == 'no') {
            $input['check_minimum_service_project'] = 0;
        } elseif (isset($input['checkServiceProject'])) {
            $input['check_minimum_service_project'] = 1;
        } else {
            $input['check_minimum_service_project'] = null;
        }

        if (isset($input['checkM2MDonation']) && $input['checkM2MDonation'] == 'no') {
            $input['check_m2m_donation'] = 0;
        } elseif (isset($input['checkM2MDonation'])) {
            $input['check_m2m_donation'] = 1;
        } else {
            $input['check_m2m_donation'] = null;
        }

        if (isset($input['checkMCGeneralFund']) && $input['checkMCGeneralFund'] == 'no') {
            $input['check_mc_general_fund'] = 0;
        } elseif (isset($input['checkMCGeneralFund'])) {
            $input['check_mc_general_fund'] = 1;
        } else {
            $input['check_mc_general_fund'] = null;
        }

        $check_minimum_service_project = $input['check_minimum_service_project'];
        $check_m2m_donation = $input['check_m2m_donation'];
        $check_mc_general_fund = $input['check_mc_general_fund'];

        // Step 5
        if (isset($input['checkAttendedTraining']) && $input['checkAttendedTraining'] == 'no') {
            $input['check_attended_training'] = 0;
        } elseif (isset($input['checkAttendedTraining'])) {
            $input['check_attended_training'] = 1;
        } else {
            $input['check_attended_training'] = null;
        }

        if (isset($input['checkAttendedLuncheon']) && $input['checkAttendedLuncheon'] == 'no') {
            $input['check_attended_luncheon'] = 0;
        } elseif (isset($input['checkAttendedLuncheon'])) {
            $input['check_attended_luncheon'] = 1;
        } else {
            $input['check_attended_luncheon'] = null;
        }

        $check_attended_training = $input['check_attended_training'];
        $check_attended_luncheon = $input['check_attended_luncheon'];

        // Step 8
        if (isset($input['checkBankStatementMatches']) && $input['checkBankStatementMatches'] == 'no') {
            $input['check_bank_statement_matches'] = 0;
        } elseif (isset($input['checkBankStatementMatches'])) {
            $input['check_bank_statement_matches'] = 1;
        } else {
            $input['check_bank_statement_matches'] = null;
        }

        if (isset($input['checkBankStatementIncluded']) && $input['checkBankStatementIncluded'] == 'no') {
            $input['check_bank_statement_included'] = 0;
        } elseif (isset($input['checkBankStatementIncluded'])) {
            $input['check_bank_statement_included'] = 1;
        } else {
            $input['check_bank_statement_included'] = null;
        }

        $check_bank_statement_matches = $input['check_bank_statement_matches'];
        $check_bank_statement_included = $input['check_bank_statement_included'];

        // Step 9
        if (isset($input['checkPurchasedPins']) && $input['checkPurchasedPins'] == 'no') {
            $input['check_purchased_pins'] = 0;
        } elseif (isset($input['checkPurchasedPins'])) {
            $input['check_purchased_pins'] = 1;
        } else {
            $input['check_purchased_pins'] = null;
        }

        if (isset($input['checkPurchasedMCMerch']) && $input['checkPurchasedMCMerch'] == 'no') {
            $input['check_purchased_mc_merch'] = 0;
        } elseif (isset($input['checkPurchasedMCMerch'])) {
            $input['check_purchased_mc_merch'] = 1;
        } else {
            $input['check_purchased_mc_merch'] = null;
        }

        if (isset($input['checkOfferedMerch']) && $input['checkOfferedMerch'] == 'no') {
            $input['check_offered_merch'] = 0;
        } elseif (isset($input['checkOfferedMerch'])) {
            $input['check_offered_merch'] = 1;
        } else {
            $input['check_offered_merch'] = null;
        }

        if (isset($input['checkBylawsMadeAvailable']) && $input['checkBylawsMadeAvailable'] == 'no') {
            $input['check_bylaws_available'] = 0;
        } elseif (isset($input['checkBylawsMadeAvailable'])) {
            $input['check_bylaws_available'] = 1;
        } else {
            $input['check_bylaws_available'] = null;
        }

        if (isset($input['checkCurrent990NAttached']) && $input['checkCurrent990NAttached'] == 'no') {
            $input['check_current_990N_included'] = 0;
        } elseif (isset($input['checkCurrent990NAttached'])) {
            $input['check_current_990N_included'] = 1;
        } else {
            $input['check_current_990N_included'] = null;
        }

        $check_purchased_pins = $input['check_purchased_pins'];
        $check_purchased_mc_merch = $input['check_purchased_mc_merch'];
        $check_offered_merch = $input['check_offered_merch'];
        $check_bylaws_available = $input['check_bylaws_available'];
        $check_current_990N_included = $input['check_current_990N_included'];

        // Step 10
        if (isset($input['checkTotalIncome']) && $input['checkTotalIncome'] == 'no') {
            $input['check_total_income_less'] = 0;
        } elseif (isset($_POST['checkTotalIncome'])) {
            $input['check_total_income_less'] = 1;
        } else {
            $input['check_total_income_less'] = null;
        }

        if (isset($input['checkSisteredAnotherChapter']) && $input['checkSisteredAnotherChapter'] == 'no') {
            $input['check_sistered_another_chapter'] = 0;
        } elseif (isset($input['checkSisteredAnotherChapter'])) {
            $input['check_sistered_another_chapter'] = 1;
        } else {
            $input['check_sistered_another_chapter'] = null;
        }

        $check_total_income_less = $input['check_total_income_less'];
        $check_sistered_another_chapter = $input['check_sistered_another_chapter'];

        // Step 11 //
        if (isset($input['sumcheckAward1Approved']) && $input['sumcheckAward1Approved'] == 'no') {
            $input['check_award_1_approved'] = 0;
        } elseif (isset($input['sumcheckAward1Approved'])) {
            $input['check_award_1_approved'] = 1;
        } else {
            $input['check_award_1_approved'] = null;
        }

        if (isset($input['sumcheckAward2Approved']) && $input['sumcheckAward2Approved'] == 'no') {
            $input['check_award_2_approved'] = 0;
        } elseif (isset($input['sumcheckAward2Approved'])) {
            $input['check_award_2_approved'] = 1;
        } else {
            $input['check_award_2_approved'] = null;
        }

        if (isset($input['sumcheckAward3Approved']) && $input['sumcheckAward3Approved'] == 'no') {
            $input['check_award_3_approved'] = 0;
        } elseif (isset($input['sumcheckAward3Approved'])) {
            $input['check_award_3_approved'] = 1;
        } else {
            $input['check_award_3_approved'] = null;
        }

        if (isset($input['sumcheckAward4Approved']) && $input['sumcheckAward4Approved'] == 'no') {
            $input['check_award_4_approved'] = 0;
        } elseif (isset($input['sumcheckAward4Approved'])) {
            $input['check_award_4_approved'] = 1;
        } else {
            $input['check_award_4_approved'] = null;
        }

        if (isset($input['sumcheckAward5Approved']) && $input['sumcheckAward5Approved'] == 'no') {
            $input['check_award_5_approved'] = 0;
        } elseif (isset($input['sumcheckAward5Approved'])) {
            $input['check_award_5_approved'] = 1;
        } else {
            $input['check_award_5_approved'] = null;
        }

        $check_award_1_approved = $input['check_award_1_approved'];
        $check_award_2_approved = $input['check_award_2_approved'];
        $check_award_3_approved = $input['check_award_3_approved'];
        $check_award_4_approved = $input['check_award_4_approved'];
        $check_award_5_approved = $input['check_award_5_approved'];

        $chapterDetails = DB::table('chapters')
            ->select('chapters.*', 'st.state_short_name as state_short_name')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.id', '=', $chapter_id)
            ->get();
        $chapter_conf = $chapterDetails[0]->conference;
        $chapter_state = $chapterDetails[0]->state_short_name;
        $chapter_name = $chapterDetails[0]->name;
        $chapter_country = $chapterDetails[0]->country;

        $Reviewer = DB::table('coordinator_details')
            ->select('coordinator_details.*')
            ->where('coordinator_details.coordinator_id', '=', $reviewer_id)
            ->get();

        $ReviewerEmail = $Reviewer[0]->email;

        /////////////////  Roster File Upload  //////////////////////

        if (basename($_FILES['RosterFile']['name'] != '')) {
            $target_file = $target_dir.basename($_FILES['RosterFile']['name']);
            $uploadOk = 1;
            $uploadedFileObj = $request->file('RosterFile');
            $uploadedFileName = $request->file('RosterFile')->getClientOriginalName();
            $uploadedFileSize = $request->file('RosterFile')->getSize();

            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            // Check file size
            if ($uploadedFileSize > 5000000) {
                $uploadOk = 0;

                return redirect()->back()->with('fail', 'Sorry, your file is too large - file not uploaded.');
            }
            // Allow certain file formats
            if ($imageFileType != 'xls' && $imageFileType != 'xlsx' && $imageFileType != 'jpg' && $imageFileType != 'jpeg' && $imageFileType != 'pdf') {
                $uploadOk = 0;

                return redirect()->back()->with('fail', 'Sorry, only xls, xlsx, jpg, jpeg & pdf files are allowed.');
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                return redirect()->back()->with('fail', 'Sorry, your file was not uploaded.');
            } else {
                $uploadedFileObj->move($uploadedFilePath, $uploadedFileName);
            }

            if ($uploadOk) {
                $file = fopen($target_file, 'rb') or exit("can't open file");
                $app = new DropboxApp($dropboxKey, $dropboxSecret, $dropboxToken);
                $dropbox = new Dropbox($app);

                if ($chapter_state == '**') { //this is an internatioanl chapter, set state to intl_state
                    $db_chapter_folder = '/Conference '.$chapter_conf.'/International/'.$chapter_country.'/'.trim($chapter_name);
                } else {
                    $db_chapter_folder = '/Conference '.$chapter_conf.'/'.$chapter_state.'/'.trim($chapter_name);
                }
                try {
                    $dropbox->createFolder($db_chapter_folder);
                } catch (\Exception $e) {
                    // just ignore
                }
                $dropboxFile = new DropboxFile($target_file);
                $dbfile = $dropbox->upload($dropboxFile, $db_chapter_folder.'/Roster.'.$imageFileType, ['autorename' => true]);

                //Uploaded File
                $file_name_assigned = $db_chapter_folder.'/'.$dbfile->getName();

                $share_existed = false;
                try {
                    $response = $dropbox->postToAPI('/sharing/create_shared_link_with_settings', ['path' => $file_name_assigned, 'settings' => ['requested_visibility' => 'public']]);
                } catch (\Exception $e) { //Share link already exists
                    $response = $dropbox->postToAPI('/sharing/list_shared_links', ['path' => $file_name_assigned]);
                    $share_existed = true;
                }
                $decoded = $response->getDecodedBody();

                if ($share_existed) {
                    $roster_path = $decoded['links'][0]['url'];
                } else {
                    $roster_path = $decoded['url'];
                }
            } else {
                $roster_path = '';
            }
        } else {
            $roster_path = $input['RosterPath'];
        }

        //////////////////  990N File Upload  ////////////////////

        if (basename($_FILES['990NFiling']['name'] != '')) {
            $target_file = $target_dir.basename($_FILES['990NFiling']['name']);
            $uploadOk = 1;
            $uploadedFileObj = $request->file('990NFiling');
            $uploadedFileName = $request->file('990NFiling')->getClientOriginalName();
            $uploadedFileSize = $request->file('990NFiling')->getSize();

            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            // Check file size
            if ($uploadedFileSize > 5000000) {
                $uploadOk = 0;

                return redirect()->back()->with('fail', 'Sorry, your file is too large - file not uploaded.');
            }
            // Allow certain file formats
            if ($imageFileType != 'jpg' && $imageFileType != 'jpeg' && $imageFileType != 'pdf') {
                $uploadOk = 0;

                return redirect()->back()->with('fail', 'Sorry, only JPG, JPEG & PDF files are allowed.');
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                return redirect()->back()->with('fail', 'Sorry, your file was not uploaded.');
            } else {
                $uploadedFileObj->move($uploadedFilePath, $uploadedFileName);
            }

            if ($uploadOk) {
                $file = fopen($target_file, 'rb') or exit("can't open file");

                $app = new DropboxApp($dropboxKey, $dropboxSecret, $dropboxToken);
                $dropbox = new Dropbox($app);

                if ($chapter_state == '**') { //this is an internatioanl chapter, set state to intl_state
                    $db_chapter_folder = '/Conference '.$chapter_conf.'/International/'.$chapter_country.'/'.trim($chapter_name);
                } else {
                    $db_chapter_folder = '/Conference '.$chapter_conf.'/'.$chapter_state.'/'.trim($chapter_name);
                }
                try {
                    $dropbox->createFolder($db_chapter_folder);
                } catch (\Exception $e) {
                    // just ignore
                }
                $dropboxFile = new DropboxFile($target_file);
                $dbfile = $dropbox->upload($dropboxFile, $db_chapter_folder.'/990N.'.$imageFileType, ['autorename' => true]);

                //Uploaded File
                $file_name_assigned = $db_chapter_folder.'/'.$dbfile->getName();

                $share_existed = false;
                try {
                    $response = $dropbox->postToAPI('/sharing/create_shared_link_with_settings', ['path' => $file_name_assigned, 'settings' => ['requested_visibility' => 'public']]);
                } catch (\Exception $e) { //Share link already exists
                    $response = $dropbox->postToAPI('/sharing/list_shared_links', ['path' => $file_name_assigned]);
                    $share_existed = true;
                }
                $decoded = $response->getDecodedBody();

                if ($share_existed) {
                    $file_irs_path = $decoded['links'][0]['url'];
                } else {
                    $file_irs_path = $decoded['url'];
                }
            } else {
                $file_irs_path = '';
            }
        } else {
            $file_irs_path = $input['990NPath'];
        }
        /////////////////// Bank Statements ///////////////////
        if (basename($_FILES['StatementFile']['name'] != '')) {
            $target_file = $target_dir.basename($_FILES['StatementFile']['name']);
            $uploadOk = 1;
            $uploadedFileObj = $request->file('StatementFile');
            $uploadedFileName = $request->file('StatementFile')->getClientOriginalName();
            $uploadedFileSize = $request->file('StatementFile')->getSize();

            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            // Check file size
            if ($uploadedFileSize > 5000000) {
                $uploadOk = 0;

                return redirect()->back()->with('fail', 'Sorry, your file is too large - file not uploaded.');
            }
            // Allow certain file formats
            if ($imageFileType != 'jpg' && $imageFileType != 'jpeg' && $imageFileType != 'pdf') {
                $uploadOk = 0;

                return redirect()->back()->with('fail', 'Sorry, only jpg, jpeg & pdf files are allowed.');
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                return redirect()->back()->with('fail', 'Sorry, your file was not uploaded.');
            } else {
                $uploadedFileObj->move($uploadedFilePath, $uploadedFileName);
            }

            if ($uploadOk) {
                $file = fopen($target_file, 'rb') or exit("can't open file");

                $app = new DropboxApp($dropboxKey, $dropboxSecret, $dropboxToken);
                $dropbox = new Dropbox($app);

                if ($chapter_state == '**') { //this is an internatioanl chapter, set state to intl_state
                    $db_chapter_folder = '/Conference '.$chapter_conf.'/International/'.$chapter_country.'/'.trim($chapter_name);
                } else {
                    $db_chapter_folder = '/Conference '.$chapter_conf.'/'.$chapter_state.'/'.trim($chapter_name);
                }
                try {
                    $dropbox->createFolder($db_chapter_folder);
                } catch (\Exception $e) {
                    // just ignore
                }
                $dropboxFile = new DropboxFile($target_file);
                $dbfile = $dropbox->upload($dropboxFile, $db_chapter_folder.'/Statement1.'.$imageFileType, ['autorename' => true]);

                //Uploaded File
                $file_name_assigned = $db_chapter_folder.'/'.$dbfile->getName();

                $share_existed = false;
                try {
                    $response = $dropbox->postToAPI('/sharing/create_shared_link_with_settings', ['path' => $file_name_assigned, 'settings' => ['requested_visibility' => 'public']]);
                } catch (\Exception $e) { //Share link already exists
                    $response = $dropbox->postToAPI('/sharing/list_shared_links', ['path' => $file_name_assigned]);
                    $share_existed = true;
                }
                $decoded = $response->getDecodedBody();

                if ($share_existed) {
                    $bank_statement_included_path = $decoded['links'][0]['url'];
                } else {
                    $bank_statement_included_path = $decoded['url'];
                }
            } else {
                $bank_statement_included_path = '';
            }
        } else {
            $bank_statement_included_path = $input['StatementPath'];
        }

        if (basename($_FILES['Statement2File']['name'] != '')) {
            $target_file = $target_dir.basename($_FILES['Statement2File']['name']);
            $uploadOk = 1;
            $uploadedFileObj = $request->file('Statement2File');
            $uploadedFileName = $request->file('Statement2File')->getClientOriginalName();
            $uploadedFileSize = $request->file('Statement2File')->getSize();

            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            // Check file size
            if ($uploadedFileSize > 5000000) {
                $uploadOk = 0;

                return redirect()->back()->with('fail', 'Sorry, your file is too large - file not uploaded.');
            }
            // Allow certain file formats
            if ($imageFileType != 'jpg' && $imageFileType != 'jpeg' && $imageFileType != 'pdf') {
                $uploadOk = 0;

                return redirect()->back()->with('fail', 'Sorry, only jpg, jpeg & pdf files are allowed.');
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                return redirect()->back()->with('fail', 'Sorry, your file was not uploaded.');
            } else {
                $uploadedFileObj->move($uploadedFilePath, $uploadedFileName);
            }

            if ($uploadOk) {
                $file = fopen($target_file, 'rb') or exit("can't open file");

                $app = new DropboxApp($dropboxKey, $dropboxSecret, $dropboxToken);
                $dropbox = new Dropbox($app);

                if ($chapter_state == '**') { //this is an internatioanl chapter, set state to intl_state
                    $db_chapter_folder = '/Conference '.$chapter_conf.'/International/'.$chapter_country.'/'.trim($chapter_name);
                } else {
                    $db_chapter_folder = '/Conference '.$chapter_conf.'/'.$chapter_state.'/'.trim($chapter_name);
                }
                try {
                    $dropbox->createFolder($db_chapter_folder);
                } catch (\Exception $e) {
                    // just ignore
                }
                $dropboxFile = new DropboxFile($target_file);
                $dbfile = $dropbox->upload($dropboxFile, $db_chapter_folder.'/Statement2.'.$imageFileType, ['autorename' => true]);

                //Uploaded File
                $file_name_assigned = $db_chapter_folder.'/'.$dbfile->getName();

                $share_existed = false;
                try {
                    $response = $dropbox->postToAPI('/sharing/create_shared_link_with_settings', ['path' => $file_name_assigned, 'settings' => ['requested_visibility' => 'public']]);
                } catch (\Exception $e) { //Share link already exists
                    $response = $dropbox->postToAPI('/sharing/list_shared_links', ['path' => $file_name_assigned]);
                    $share_existed = true;
                }
                $decoded = $response->getDecodedBody();

                if ($share_existed) {
                    $bank_statement_2_included_path = $decoded['links'][0]['url'];
                } else {
                    $bank_statement_2_included_path = $decoded['url'];
                }
            } else {
                $bank_statement_2_included_path = '';
            }
        } else {
            $bank_statement_2_included_path = $input['Statement2Path'];
        }

        DB::beginTransaction();
        try {
            $report = FinancialReport::find($chapter_id);
            $report->roster_path = $roster_path;
            $report->file_irs_path = $file_irs_path;
            $report->bank_statement_included_path = $bank_statement_included_path;
            $report->bank_statement_2_included_path = $bank_statement_2_included_path;
            $report->reviewer_id = $reviewer_id;
            $report->step_1_notes_log = $step_1_notes_log;
            $report->step_2_notes_log = $step_2_notes_log;
            $report->step_3_notes_log = $step_3_notes_log;
            $report->step_4_notes_log = $step_4_notes_log;
            $report->step_5_notes_log = $step_5_notes_log;
            $report->step_6_notes_log = $step_6_notes_log;
            $report->step_7_notes_log = $step_7_notes_log;
            $report->step_8_notes_log = $step_8_notes_log;
            $report->step_9_notes_log = $step_9_notes_log;
            $report->step_10_notes_log = $step_10_notes_log;
            $report->step_11_notes_log = $step_11_notes_log;
            $report->check_roster_attached = $check_roster_attached;
            $report->check_renewal_seems_right = $check_renewal_seems_right;
            $report->check_minimum_service_project = $check_minimum_service_project;
            $report->check_m2m_donation = $check_m2m_donation;
            $report->check_mc_general_fund = $check_mc_general_fund;
            $report->check_attended_training = $check_attended_training;
            $report->check_attended_luncheon = $check_attended_luncheon;
            $report->check_bank_statement_matches = $check_bank_statement_matches;
            $report->check_bank_statement_included = $check_bank_statement_included;
            $report->post_balance = $post_balance;
            $report->check_purchased_pins = $check_purchased_pins;
            $report->check_purchased_mc_merch = $check_purchased_mc_merch;
            $report->check_offered_merch = $check_offered_merch;
            $report->check_bylaws_available = $check_bylaws_available;
            $report->check_current_990N_included = $check_current_990N_included;
            $report->check_total_income_less = $check_total_income_less;
            $report->check_sistered_another_chapter = $check_sistered_another_chapter;
            $report->check_award_1_approved = $check_award_1_approved;
            $report->check_award_2_approved = $check_award_2_approved;
            $report->check_award_3_approved = $check_award_3_approved;
            $report->check_award_4_approved = $check_award_4_approved;
            $report->check_award_5_approved = $check_award_5_approved;
            $report->farthest_step_visited_coord = $farthest_step_visited_coord;
            if ($submitType == 'review_complete') {
                $report->review_complete = date('Y-m-d H:i:s');
            }
            if ($submitType == 'review_clear') {
                $report->review_complete = null;
            }

            if ($roster_path != null) {
                $roster = $roster_path;
            } else {
                $roster = 'No Roster Included';
            }

            if ($bank_statement_included_path != null) {
                $bank_statement_path = $bank_statement_included_path;
            } else {
                $bank_statement_path = 'No Bank Statement Inclded';
            }

            if ($bank_statement_2_included_path != null) {
                $bank_statemet_2_path = $bank_statement_2_included_path;
            } else {
                $bank_statemet_2_path = 'No Additional Bank Statment';
            }

            if ($file_irs_path != null) {
                $irs_path = $file_irs_path;
            } else {
                $irs_path = 'No 990N Confirmation File';
            }

            $to_email = $ReviewerEmail;
            $mailData = ['chapter_name' => $chapter_name,
                'chapter_state' => $chapter_state,
                'roster' => $roster,
                'bank_statement_path' => $bank_statement_path,
                'bank_statemet_2_path' => $bank_statemet_2_path,
                'irs_path' => $irs_path];
            if ($report->isDirty('reviewer_id')) {
                Mail::send('emails.reviewerassigned', $mailData, function ($message) use ($to_email) {
                    $message->to($to_email, 'MOMS Club')->subject('Financial Reviewer Assigned');
                });
            }

            $report->save();

            $chapter = Chapter::find($chapter_id);

            if ($submitType == 'review_complete') {
                $chapter->financial_report_complete = 1;
            }
            if ($submitType == 'review_clear') {
                $chapter->financial_report_complete = null;
            }

            $chapter->save();

            DB::commit();
            if ($submitType == 'review_complete') {
                return redirect()->back()->with('success', 'Report has been successfully Marked as Review Complete');
            } elseif ($submitType == 'review_clear') {
                return redirect()->back()->with('success', 'Review Complete has been successfully cleared');
            } else {
                return redirect()->back()->with('success', 'Report has been successfully Updated');
            }
        } catch (\Exception $e) {
            DB::rollback();

            // Log the error
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

    }

    /**
     * View the Report Status Details
     */
    public function statusView($id)
    {

        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('board_details as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id', 'ASC')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id', 'ASC')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->get();

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', '=', $corConfId)
            ->where('cd.position_id', '<=', '6')
            ->where('cd.position_id', '>=', '1')
            ->where('cd.is_active', '=', '1')
            ->orWhere('cd.position_id', '=', '25')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name', 'ASC')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $chapterList[0]->start_month_id;

        $data = ['currentMonth' => $currentMonth, 'chapterList' => $chapterList, 'regionList' => $regionList, 'primaryCoordinatorList' => $primaryCoordinatorList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('chapters.reportstatus')->with($data);
    }

    public function updateStatus(Request $request, $id)
    {
        $corDetails = User::find(Auth::user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $chapter = Chapter::find($id);
        DB::beginTransaction();
        try {
            $chapter->new_board_submitted = (int) $request->has('ch_board_submitted');
            $chapter->new_board_active = (int) $request->has('ch_board_active');
            $chapter->financial_report_received = (int) $request->has('ch_financial_received');
            $chapter->financial_report_complete = (int) $request->has('ch_financial_complete');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();

            $report = FinancialReport::find($id);
            if ($request->has('ch_financial_complete') != null) {
                $report->review_complete = date('Y-m-d H:i:s');
            }
            if ($request->has('ch_financial_complete') == null) {
                $report->review_complete = null;
            }
            $report->reviewer_id = null;
            $report->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            // Log the error
            Log::error($e);

            return redirect('/yearreports/eoystatus')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect('/yearreports/eoystatus')->with('success', 'Report status successfully updated');
    }

    public function chapterLinks()
    {
        $link_array_intl_q = DB::select("SELECT id, intl_state, country, name, status, website_link_status, website_url FROM chapters WHERE state='52' AND is_active ='1' ORDER BY country, intl_state, name");
        $link_array_usa_q = DB::select("SELECT chapters.id, chapters.state, chapters.intl_state, chapters.country, chapters.name, chapters.status, chapters.website_link_status, chapters.website_url, state.state_short_name, state.state_long_name
                    FROM chapters
                    INNER JOIN state
                    ON chapters.state=state.id
                    WHERE chapters.state<>52
                    AND is_active ='1'
                    ORDER BY chapters.state, chapters.name");
        $link_array_intl = [];
        foreach ($link_array_intl_q as $key => $value) {
            $link_array_intl[$key]['id'] = $value->id;
            $link_array_intl[$key]['state'] = $value->intl_state;
            $link_array_intl[$key]['country'] = $value->country;
            $link_array_intl[$key]['name'] = $value->name;
            $link_array_intl[$key]['status'] = $value->status;
            $link_array_intl[$key]['link_status'] = $value->website_link_status;
            $link_array_intl[$key]['url'] = $value->website_url;
        }
        $link_array_usa = [];
        foreach ($link_array_usa_q as $key => $value) {
            $link_array_usa[$key]['id'] = $value->id;
            $link_array_usa[$key]['state'] = $value->state;
            $link_array_usa[$key]['state_abr'] = $value->state_short_name;
            $link_array_usa[$key]['state_name'] = $value->state_long_name;
            $link_array_usa[$key]['country'] = $value->country;
            $link_array_usa[$key]['name'] = $value->name;
            $link_array_usa[$key]['status'] = $value->status;
            $link_array_usa[$key]['link_status'] = $value->website_link_status;
            $link_array_usa[$key]['url'] = $value->website_url;
        }

        return view('chapterlinks', compact('link_array_intl', 'link_array_usa'));
    }
}
