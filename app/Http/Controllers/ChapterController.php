<?php

namespace App\Http\Controllers;

use App\Mail\ChapersUpdateEINCoor;
use App\Mail\ChapersUpdateListAdmin;
use App\Mail\ChapersUpdatePrimaryCoor;
use App\Mail\ChapterAddListAdmin;
use App\Mail\ChapterAddPrimaryCoor;
use App\Mail\ChapterDisbandLetter;
use App\Mail\ChapterReAddListAdmin;
use App\Mail\ChapterRemoveListAdmin;
use App\Mail\ChaptersPrimaryCoordinatorChange;
use App\Mail\ChaptersPrimaryCoordinatorChangePCNotice;
use App\Mail\PaymentsReRegChapterThankYou;
use App\Mail\PaymentsReRegLate;
use App\Mail\PaymentsReRegReminder;
use App\Mail\WebsiteAddNoticeAdmin;
use App\Mail\WebsiteReviewNotice;
use App\Models\Chapter;
use App\Models\FinancialReport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ChapterController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->middleware('auth')->except('logout');
        $this->userController = $userController;
    }

    /**
     * Display the Active chapter list mapped with login coordinator
     */
    public function showChapters(Request $request): View
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        $request->session()->put('corconfid', $corConfId);
        $request->session()->put('corregid', $corRegId);

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
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
        }

        $baseQuery = DB::table('chapters as ch')
                ->select('ch.id', 'ch.name', 'ch.state', 'ch.ein', 'ch.primary_coordinator_id', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone',
                    'st.state_short_name as state', 'cd.region_id', 'ch.region', 'rg.short_name as reg', 'cf.short_name as conf')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'ch.id')
                ->leftJoin('state as st', 'ch.state', '=', 'st.id')
                ->leftJoin('conference as cf', 'ch.conference', '=', 'cf.id')
                ->leftJoin('region as rg', 'ch.region', '=', 'rg.id')
                ->where('ch.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1');

            if ($conditions['founderCondition']) {
                    $baseQuery;
            } elseif ($conditions['assistConferenceCoordinatorCondition']) {
                    $baseQuery->where('ch.conference', '=', $corConfId);
                } elseif ($conditions['regionalCoordinatorCondition']) {
                    $baseQuery->where('ch.region', '=', $corRegId);
            } else {
                $baseQuery->whereIn('ch.primary_coordinator_id', $inQryArr);
            }

            if (isset($_GET['check']) && $_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                $baseQuery->where('ch.primary_coordinator_id', '=', $corId)
                    ->orderBy('st.state_short_name')
                    ->orderBy('ch.name');
            } else {
                $checkBoxStatus = '';
                $baseQuery->orderBy('st.state_short_name')
                    ->orderBy('ch.name');
            }

            $chapterList = $baseQuery->get();


        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

        return view('chapters.chaplist')->with($data);
    }

    /**
     * Add New chapter list (View)
     */
    public function showChapterNew(Request $request)
    {
        $user = User::find($request->user()->id);
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->Coordinators;
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];

        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name')
            ->get();
        $primaryCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos', 'pos2.short_title as sec_pos')
            ->join('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
            ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
            ->where('cd.conference_id', '=', $corConfId)
            ->where('cd.position_id', '<=', '7')
            ->where('cd.position_id', '>=', '1')
            ->where('cd.is_active', '=', '1')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = date('m');
        $firstCharacter = $currentMonth[0];
        if ($firstCharacter == '0') {
            $currentMonth = $currentMonth[1];
        }

        $currentYear = date('Y');
        $data = ['currentMonth' => $currentMonth, 'currentYear' => $currentYear, 'regionList' => $regionList, 'primaryCoordinatorList' => $primaryCoordinatorList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('chapters.chapnew')->with($data);
    }

    /**
     * Add New chapter list (Store)
     */
    public function updateChapterNew(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $input = $request->all();

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
                    'email' => $input['ch_email'],
                    'inquiries_contact' => $input['ch_inqemailcontact'],
                    'inquiries_note' => $input['ch_inqnote'],
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

                $boardId = DB::table('boards')->insertGetId(
                    ['user_id' => $userId,
                        'first_name' => $input['ch_pre_fname'],
                        'last_name' => $input['ch_pre_lname'],
                        'email' => $input['ch_pre_email'],
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

                $boardId = DB::table('boards')->insertGetId(
                    ['user_id' => $userId,
                        'first_name' => $input['ch_avp_fname'],
                        'last_name' => $input['ch_avp_lname'],
                        'email' => $input['required|ch_avp_email'],
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

                $boardId = DB::table('boards')->insertGetId(
                    ['user_id' => $userId,
                        'first_name' => $input['ch_mvp_fname'],
                        'last_name' => $input['ch_mvp_lname'],
                        'email' => $input['ch_mvp_email'],
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

                $boardId = DB::table('boards')->insertGetId(
                    ['user_id' => $userId,
                        'first_name' => $input['ch_trs_fname'],
                        'last_name' => $input['ch_trs_lname'],
                        'email' => $input['ch_trs_email'],
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

                $boardId = DB::table('boards')->insertGetId(
                    ['user_id' => $userId,
                        'first_name' => $input['ch_sec_fname'],
                        'last_name' => $input['ch_sec_lname'],
                        'email' => $input['ch_sec_email'],
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

            $cordInfo = DB::table('coordinators')
                ->select('first_name', 'last_name', 'email')
                ->where('is_active', '=', '1')
                ->where('id', $input['ch_primarycor'])
                ->get();
            $state = DB::table('state')
                ->select('state_short_name')
                ->where('id', $input['ch_state'])
                ->get();

            $mailData = [
                'chapter_name' => $input['ch_name'],
                'chapter_state' => $state[0]->state_short_name,
                'cor_fname' => $cordInfo[0]->first_name,
                'cor_lname' => $cordInfo[0]->last_name,
                'updated_by' => date('Y-m-d H:i:s'),
                'email' => $input['ch_email'],
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
            ];

            //Primary Coordinator Notification//
            $to_email = $cordInfo[0]->email;

            Mail::to($to_email)
                ->queue(new ChapterAddPrimaryCoor($mailData));

            //List Admin Notification//
            $to_email2 = 'listadmin@momsclub.org';

            Mail::to($to_email2)
                ->queue(new ChapterAddListAdmin($mailData));

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->to('/chapter/chapterlist')->with('fail', 'Something went wrong, Please try again...');
        }

        return redirect()->to('/chapter/chapterlist')->with('success', 'Chapter created successfully');
    }

    /**
     * Edit Chapter
     */
    public function showChapterView(Request $request, $id)
    {
        $user = User::find($request->user()->id);
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->Coordinators;
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $corConfId = $corDetails['conference_id'];
        $corId = $corDetails['id'];
        $positionid = $corDetails['position_id'];
        $financial_report_array = FinancialReport::find($id);
        if ($financial_report_array) {
            $reviewComplete = $financial_report_array['review_complete'];
        } else {
            $reviewComplete = null;
        }
        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        $corConfId = $chapterList[0]->conference;
        $corId = $chapterList[0]->primary_coordinator_id;
        $AVPDetails = DB::table('boards as bd')
            ->select('bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id', 'bd.street_address as avp_addr', 'bd.city as avp_city', 'bd.zip as avp_zip', 'bd.phone as avp_phone', 'bd.state as avp_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '2')
            ->get();
        if (count($AVPDetails) == 0) {
            $AVPDetails[0] = ['avp_fname' => '', 'avp_lname' => '', 'avp_email' => '', 'avp_addr' => '', 'avp_city' => '', 'avp_zip' => '', 'avp_phone' => '', 'avp_state' => '', 'user_id' => ''];
            $AVPDetails = json_decode(json_encode($AVPDetails));
        }

        $MVPDetails = DB::table('boards as bd')
            ->select('bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id', 'bd.street_address as mvp_addr', 'bd.city as mvp_city', 'bd.zip as mvp_zip', 'bd.phone as mvp_phone', 'bd.state as mvp_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '3')
            ->get();
        if (count($MVPDetails) == 0) {
            $MVPDetails[0] = ['mvp_fname' => '', 'mvp_lname' => '', 'mvp_email' => '', 'mvp_addr' => '', 'mvp_city' => '', 'mvp_zip' => '', 'mvp_phone' => '', 'mvp_state' => '', 'user_id' => ''];
            $MVPDetails = json_decode(json_encode($MVPDetails));
        }

        $TRSDetails = DB::table('boards as bd')
            ->select('bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id', 'bd.street_address as trs_addr', 'bd.city as trs_city', 'bd.zip as trs_zip', 'bd.phone as trs_phone', 'bd.state as trs_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '4')
            ->get();
        if (count($TRSDetails) == 0) {
            $TRSDetails[0] = ['trs_fname' => '', 'trs_lname' => '', 'trs_email' => '', 'trs_addr' => '', 'trs_city' => '', 'trs_zip' => '', 'trs_phone' => '', 'trs_state' => '', 'user_id' => ''];
            $TRSDetails = json_decode(json_encode($TRSDetails));
        }

        $SECDetails = DB::table('boards as bd')
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
            ->orderBy('id')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->where('id', '>=', 0)
            ->orderBy('conference_name')
            ->get();

        // Load Board and Coordinators for Sending Email
        $chId = $chapterList[0]->id;

        $emailData = $this->userController->loadEmailDetails($chId);
        $emailListChap = $emailData['emailListChap'];
        $emailListCoord = $emailData['emailListCoord'];

        $primaryCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos', 'pos2.short_title as sec_pos')
            ->join('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
            ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
            ->where('cd.conference_id', '=', $corConfId)
            ->where('cd.position_id', '<=', '7')
            ->where('cd.position_id', '>=', '1')
            ->where('cd.is_active', '=', '1')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $chapterList[0]->start_month_id;

        $data = ['id' => $id, 'positionid' => $positionid, 'corId' => $corId, 'reviewComplete' => $reviewComplete, 'emailListCoord' => $emailListCoord, 'emailListChap' => $emailListChap, 'currentMonth' => $currentMonth,
            'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'chapterList' => $chapterList, 'regionList' => $regionList, 'confList' => $confList,
            'primaryCoordinatorList' => $primaryCoordinatorList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('chapters.chapview')->with($data);
    }

    /**
     *Update Chapter
     */
    public function updateChapter(Request $request, $id): RedirectResponse
    {
        $presInfoPre = DB::table('chapters')
            ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'bd.street_address as street', 'bd.city as city', 'bd.zip as zip', 'st.state_short_name as state')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.id', $id)
            ->orderByDesc('chapters.id')
            ->get();

        $AVPInfoPre = DB::table('chapters')
            ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '2')
            ->where('chapters.id', $id)
            ->get();

        $MVPInfoPre = DB::table('chapters')
            ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '3')
            ->where('chapters.id', $id)
            ->get();

        $tresInfoPre = DB::table('chapters')
            ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '4')
            ->where('chapters.id', $id)
            ->get();

        $secInfoPre = DB::table('chapters')
            ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->where('chapters.is_Active', '=', '1')
            ->where('bd.board_position_id', '=', '5')
            ->where('chapters.id', $id)
            ->get();

        $chapterId = $id;
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['regionalCoordinatorCondition']) {
            $ch_state = $request->input('ch_hid_state');
            $ch_country = $request->input('ch_hid_country');
            $ch_region = $request->input('ch_hid_region');
            $ch_status = $request->input('ch_hid_status');
            $ch_webstatus = $request->input('ch_hid_webstatus');
            $ch_pcid = $request->input('ch_hid_primarycor');
        } else {
            $ch_state = $request->input('ch_state');
            $ch_country = $request->input('ch_country');
            $ch_region = $request->input('ch_region');
            $ch_status = $request->input('ch_status');
            $ch_webstatus = $request->input('ch_webstatus');
            $ch_pcid = $request->input('ch_primarycor');
        }
        if ($conditions['founderCondition']) {
            $ch_month = $request->input('ch_founddate');
            $ch_foundyear = $request->input('ch_foundyear');
        } else {
            $ch_month = $request->input('ch_hid_founddate');
            $ch_foundyear = $request->input('ch_hid_foundyear');
        }

        if (empty(trim($ch_webstatus))) {
            $ch_webstatus = 0; // Set it to 0 if it's blank
        }

        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $chapter = Chapter::find($chapterId);
        DB::beginTransaction();
        try {
            $chapter->name = $request->input('ch_name');
            $chapter->state = $ch_state;
            $chapter->country = $ch_country;
            $chapter->region = $ch_region;
            $chapter->ein = $request->input('ch_ein');
            $chapter->ein_letter_path = $request->input('ch_ein_letter_path');
            $chapter->status = $ch_status;
            $chapter->territory = $request->input('ch_boundariesterry');
            $chapter->additional_info = $request->input('ch_addinfo');
            $chapter->website_url = $request->input('ch_website');
            $chapter->website_status = $ch_webstatus;
            $chapter->email = $request->input('ch_email');
            $chapter->inquiries_contact = $request->input('ch_inqemailcontact');
            $chapter->inquiries_note = $request->input('ch_inqnote');
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->po_box = $request->input('ch_pobox');
            $chapter->notes = $request->input('ch_notes');
            $chapter->reg_notes = $request->input('ch_regnotes');
            $chapter->po_box = $request->input('ch_pobox');
            $chapter->former_name = $request->input('ch_preknown');
            $chapter->sistered_by = $request->input('ch_sistered');
            $chapter->start_month_id = $ch_month;
            $chapter->start_year = $ch_foundyear;
            $chapter->primary_coordinator_id = $ch_pcid;
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');

            $chapter->save();

            //President Info
            if ($request->input('ch_pre_fname') != '' && $request->input('ch_pre_lname') != '' && $request->input('ch_pre_email') != '') {
                $PREDetails = DB::table('boards')
                    ->select('id', 'user_id')
                    ->where('chapter_id', '=', $chapterId)
                    ->where('board_position_id', '=', '1')
                    ->get();
                if (count($PREDetails) != 0) {
                    $userId = $PREDetails[0]->user_id;
                    $boardId = $PREDetails[0]->id;

                    $user = User::find($userId);
                    $user->first_name = $request->input('ch_pre_fname');
                    $user->last_name = $request->input('ch_pre_lname');
                    $user->email = $request->input('ch_pre_email');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    DB::table('boards')
                        ->where('id', $boardId)
                        ->update(['first_name' => $request->input('ch_pre_fname'),
                            'last_name' => $request->input('ch_pre_lname'),
                            'email' => $request->input('ch_pre_email'),
                            'street_address' => $request->input('ch_pre_street'),
                            'city' => $request->input('ch_pre_city'),
                            'state' => $request->input('ch_pre_state'),
                            'zip' => $request->input('ch_pre_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_pre_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                }
            }
            //AVP Info
            $AVPDetails = DB::table('boards')
                ->select('id', 'user_id')
                ->where('chapter_id', '=', $chapterId)
                ->where('board_position_id', '=', '2')
                ->get();
            if (count($AVPDetails) != 0) {
                $userId = $AVPDetails[0]->user_id;
                $boardId = $AVPDetails[0]->id;
                if ($request->input('AVPVacant') == 'on') {
                    //Delete Details of Board memebers
                    DB::table('boards')
                        ->where('id', $boardId)
                        ->delete();
                    //Delete Details of Board memebers from users table
                    DB::table('users')
                        ->where('id', $userId)
                        ->delete();
                } else {
                    $user = User::find($userId);
                    $user->first_name = $request->input('ch_avp_fname');
                    $user->last_name = $request->input('ch_avp_lname');
                    $user->email = $request->input('ch_avp_email');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    DB::table('boards')
                        ->where('id', $boardId)
                        ->update(['first_name' => $request->input('ch_avp_fname'),
                            'last_name' => $request->input('ch_avp_lname'),
                            'email' => $request->input('ch_avp_email'),
                            'street_address' => $request->input('ch_avp_street'),
                            'city' => $request->input('ch_avp_city'),
                            'state' => $request->input('ch_avp_state'),
                            'zip' => $request->input('ch_avp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_avp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                }
            } else {
                if ($request->input('AVPVacant') != 'on') {
                    $userId = DB::table('users')->insertGetId(
                        ['first_name' => $request->input('ch_avp_fname'),
                            'last_name' => $request->input('ch_avp_lname'),
                            'email' => $request->input('ch_avp_email'),
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1]
                    );

                    $boardId = DB::table('boards')->insertGetId(
                        ['user_id' => $userId,
                            'first_name' => $request->input('ch_avp_fname'),
                            'last_name' => $request->input('ch_avp_lname'),
                            'email' => $request->input('ch_avp_email'),
                            'board_position_id' => 2,
                            'chapter_id' => $chapterId,
                            'street_address' => $request->input('ch_avp_street'),
                            'city' => $request->input('ch_avp_city'),
                            'state' => $request->input('ch_avp_state'),
                            'zip' => $request->input('ch_avp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_avp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                            'is_active' => 1]
                    );
                }
            }
            //MVP Info
            $MVPDetails = DB::table('boards')
                ->select('id', 'user_id')
                ->where('chapter_id', '=', $chapterId)
                ->where('board_position_id', '=', '3')
                ->get();
            if (count($MVPDetails) != 0) {
                $userId = $MVPDetails[0]->user_id;
                $boardId = $MVPDetails[0]->id;
                if ($request->input('MVPVacant') == 'on') {
                    //Delete Details of Board memebers
                    DB::table('boards')
                        ->where('id', $boardId)
                        ->delete();
                    //Delete Details of Board memebers from users table
                    DB::table('users')
                        ->where('id', $userId)
                        ->delete();
                } else {
                    $user = User::find($userId);
                    $user->first_name = $request->input('ch_mvp_fname');
                    $user->last_name = $request->input('ch_mvp_lname');
                    $user->email = $request->input('ch_mvp_email');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    DB::table('boards')
                        ->where('id', $boardId)
                        ->update(['first_name' => $request->input('ch_mvp_fname'),
                            'last_name' => $request->input('ch_mvp_lname'),
                            'email' => $request->input('ch_mvp_email'),
                            'street_address' => $request->input('ch_mvp_street'),
                            'city' => $request->input('ch_mvp_city'),
                            'state' => $request->input('ch_mvp_state'),
                            'zip' => $request->input('ch_mvp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_mvp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                }
            } else {
                if ($request->input('MVPVacant') != 'on') {
                    $userId = DB::table('users')->insertGetId(
                        ['first_name' => $request->input('ch_mvp_fname'),
                            'last_name' => $request->input('ch_mvp_lname'),
                            'email' => $request->input('ch_mvp_email'),
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1]
                    );

                    $boardId = DB::table('boards')->insertGetId(
                        ['user_id' => $userId,
                            'first_name' => $request->input('ch_mvp_fname'),
                            'last_name' => $request->input('ch_mvp_lname'),
                            'email' => $request->input('ch_mvp_email'),
                            'board_position_id' => 3,
                            'chapter_id' => $chapterId,
                            'street_address' => $request->input('ch_mvp_street'),
                            'city' => $request->input('ch_mvp_city'),
                            'state' => $request->input('ch_mvp_state'),
                            'zip' => $request->input('ch_mvp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_mvp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                            'is_active' => 1]
                    );
                }
            }

            //TRS Info
            $TRSDetails = DB::table('boards')
                ->select('id', 'user_id')
                ->where('chapter_id', '=', $chapterId)
                ->where('board_position_id', '=', '4')
                ->get();
            if (count($TRSDetails) != 0) {
                $userId = $TRSDetails[0]->user_id;
                $boardId = $TRSDetails[0]->id;
                if ($request->input('TreasVacant') == 'on') {
                    //Delete Details of Board memebers
                    DB::table('boards')
                        ->where('id', $boardId)
                        ->delete();
                    //Delete Details of Board memebers from users table
                    DB::table('users')
                        ->where('id', $userId)
                        ->delete();
                } else {
                    $user = User::find($userId);
                    $user->first_name = $request->input('ch_trs_fname');
                    $user->last_name = $request->input('ch_trs_lname');
                    $user->email = $request->input('ch_trs_email');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    DB::table('boards')
                        ->where('id', $boardId)
                        ->update(['first_name' => $request->input('ch_trs_fname'),
                            'last_name' => $request->input('ch_trs_lname'),
                            'email' => $request->input('ch_trs_email'),
                            'street_address' => $request->input('ch_trs_street'),
                            'city' => $request->input('ch_trs_city'),
                            'state' => $request->input('ch_trs_state'),
                            'zip' => $request->input('ch_trs_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_trs_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                }
            } else {
                if ($request->input('TreasVacant') != 'on') {
                    $userId = DB::table('users')->insertGetId(
                        ['first_name' => $request->input('ch_trs_fname'),
                            'last_name' => $request->input('ch_trs_lname'),
                            'email' => $request->input('ch_trs_email'),
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1]
                    );

                $boardId = DB::table('boards')->insertGetId(
                        ['user_id' => $userId,
                            'first_name' => $request->input('ch_trs_fname'),
                            'last_name' => $request->input('ch_trs_lname'),
                            'email' => $request->input('ch_trs_email'),
                            'board_position_id' => 4,
                            'chapter_id' => $chapterId,
                            'street_address' => $request->input('ch_trs_street'),
                            'city' => $request->input('ch_trs_city'),
                            'state' => $request->input('ch_trs_state'),
                            'zip' => $request->input('ch_trs_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_trs_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                            'is_active' => 1]
                    );
                }
            }
            //SEC Info
            $SECDetails = DB::table('boards')
                ->select('id', 'user_id')
                ->where('chapter_id', '=', $chapterId)
                ->where('board_position_id', '=', '5')
                ->get();
            if (count($SECDetails) != 0) {
                $userId = $SECDetails[0]->user_id;
                $boardId = $SECDetails[0]->id;
                if ($request->input('SecVacant') == 'on') {
                    //Delete Details of Board memebers
                    DB::table('boards')
                        ->where('id', $boardId)
                        ->delete();
                    //Delete Details of Board memebers from users table
                    DB::table('users')
                        ->where('id', $userId)
                        ->delete();
                } else {
                    $user = User::find($userId);
                    $user->first_name = $request->input('ch_sec_fname');
                    $user->last_name = $request->input('ch_sec_lname');
                    $user->email = $request->input('ch_sec_email');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    DB::table('boards')
                        ->where('id', $boardId)
                        ->update(['first_name' => $request->input('ch_sec_fname'),
                            'last_name' => $request->input('ch_sec_lname'),
                            'email' => $request->input('ch_sec_email'),
                            'street_address' => $request->input('ch_sec_street'),
                            'city' => $request->input('ch_sec_city'),
                            'state' => $request->input('ch_sec_state'),
                            'zip' => $request->input('ch_sec_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_sec_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                }
            } else {
                if ($request->input('SecVacant') != 'on') {
                    $userId = DB::table('users')->insertGetId(
                        ['first_name' => $request->input('ch_sec_fname'),
                            'last_name' => $request->input('ch_sec_lname'),
                            'email' => $request->input('ch_sec_email'),
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1]
                    );

                    $boardId = DB::table('boards')->insertGetId(
                        ['user_id' => $userId,
                            'first_name' => $request->input('ch_sec_fname'),
                            'last_name' => $request->input('ch_sec_lname'),
                            'email' => $request->input('ch_sec_email'),
                            'board_position_id' => 5,
                            'chapter_id' => $chapterId,
                            'street_address' => $request->input('ch_sec_street'),
                            'city' => $request->input('ch_sec_city'),
                            'state' => $request->input('ch_sec_state'),
                            'zip' => $request->input('ch_sec_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_sec_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                            'is_active' => 1]
                    );
                }
            }

            //Change Primary Coordinator Notifications//
            $coremail = DB::table('coordinators')
                ->select('email')
                ->where('is_active', '=', '1')
                ->where('id', $request->input('ch_primarycor'))
                ->get();
            $coremail = $coremail[0]->email;

            $PREemail = DB::table('boards')
                ->select('email')
                ->where('board_position_id', 1)
                ->where('chapter_id', $id)
                ->where('is_active', 1)
                ->get();
            $to_email3 = [$PREemail[0]->email];

            if ($request->input('ch_primarycor') != $request->input('ch_hid_primarycor')) {
                $corename = DB::table('coordinators')
                    ->select('first_name')
                    ->where('is_active', '=', '1')
                    ->where('id', $request->input('ch_primarycor'))
                    ->get();
                $corename = $corename[0]->first_name;

                $corename1 = DB::table('coordinators')
                    ->select('last_name')
                    ->where('is_active', '=', '1')
                    ->where('id', $request->input('ch_primarycor'))
                    ->get();
                $corename1 = $corename1[0]->last_name;

                $coreemail1 = DB::table('coordinators')
                    ->select('email')
                    ->where('is_active', '=', '1')
                    ->where('id', $request->input('ch_primarycor'))
                    ->get();
                $coreemail1 = $coreemail1[0]->email;

                $chapterDetails = Chapter::find($chapterId);
                $stateArr = DB::table('state')
                    ->select('state.*')
                    ->orderBy('id')
                    ->get();

                $chapterState = DB::table('state')
                    ->select('state_short_name')
                    ->where('id', '=', $chapterDetails->state)
                    ->get();
                $chapterState = $chapterState[0]->state_short_name;

                $mailData = [
                    'chapter_name' => $request->input('ch_name'),
                    'chapter_state' => $chapterState,
                    'ch_pre_fname' => $request->input('ch_pre_fname'),
                    'ch_pre_lname' => $request->input('ch_pre_lname'),
                    'ch_pre_email' => $request->input('ch_pre_email'),
                    'name1' => $corename,
                    'name2' => $corename1,
                    'email1' => $coreemail1,
                ];

                //Chapter Notification//
                $to_email = $to_email3;
                Mail::to($to_email)
                    ->queue(new ChaptersPrimaryCoordinatorChange($mailData));

                //Primary Coordinator Notification//
                $to_email = $coremail;
                Mail::to($to_email)
                    ->queue(new ChaptersPrimaryCoordinatorChangePCNotice($mailData));
            }

            //Website Notifications//
            $cor_details = db::table('coordinators')
                ->select('email')
                ->where('conference_id', $corConfId)
                ->where('position_id', 9)
                ->where('is_active', 1)
                ->get();
            $row_count = count($cor_details);
            if ($row_count == 0) {
                $cc_details = db::table('coordinators')
                    ->select('email')
                    ->where('conference_id', $corConfId)
                    ->where('position_id', 7)
                    ->where('is_active', 1)
                    ->get();
                $to_email4 = $cc_details[0]->email;   //conference coordinator
            } else {
                $to_email4 = $cor_details[0]->email;  //website reviewer if conf has one
            }

            if ($request->input('ch_webstatus') != $request->input('ch_hid_webstatus')) {
                $chapterDetails = Chapter::find($chapterId);
                $stateArr = DB::table('state')
                    ->select('state.*')
                    ->orderBy('id')
                    ->get();

                $chapterState = DB::table('state')
                    ->select('state_short_name')
                    ->where('id', '=', $chapterDetails->state)
                    ->get();
                $chapterState = $chapterState[0]->state_short_name;

                $mailData = [
                    'chapter_name' => $request->input('ch_name'),
                    'chapter_state' => $chapterState,
                    'ch_website_url' => $request->input('ch_website'),
                ];

                if ($request->input('ch_webstatus') == 1) {
                    Mail::to($to_email4)
                        ->queue(new WebsiteAddNoticeAdmin($mailData));
                }

                if ($request->input('ch_webstatus') == 2) {
                    Mail::to($to_email4)
                        ->queue(new WebsiteReviewNotice($mailData));
                }
            }

            //Update Chapter MailData//
            $presInfoUpd = DB::table('chapters')
                ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'bd.street_address as street', 'bd.city as city', 'bd.zip as zip', 'st.state_short_name as state')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.id', $chapterId)
                ->orderByDesc('chapters.id')
                ->get();

            $AVPInfoUpd = DB::table('chapters')
                ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '2')
                ->where('chapters.id', $chapterId)
                ->get();

            $MVPInfoUpd = DB::table('chapters')
                ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '3')
                ->where('chapters.id', $chapterId)
                ->get();

            $tresInfoUpd = DB::table('chapters')
                ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '4')
                ->where('chapters.id', $chapterId)
                ->get();

            $secInfoUpd = DB::table('chapters')
                ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '5')
                ->where('chapters.id', $chapterId)
                ->get();

            $mailDataPres = [
                'conference' => $corConfId,
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
                'webStatusUpd' => $presInfoUpd[0]->website_status,
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
                'webStatusPre' => $presInfoPre[0]->website_status,
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

            if ($presInfoUpd[0]->name != $presInfoPre[0]->name || $presInfoUpd[0]->bor_email != $presInfoPre[0]->bor_email || $presInfoUpd[0]->street != $presInfoPre[0]->street || $presInfoUpd[0]->city != $presInfoPre[0]->city ||
                    $presInfoUpd[0]->state != $presInfoPre[0]->state || $presInfoUpd[0]->bor_f_name != $presInfoPre[0]->bor_f_name || $presInfoUpd[0]->bor_l_name != $presInfoPre[0]->bor_l_name ||
                    $presInfoUpd[0]->zip != $presInfoPre[0]->zip || $presInfoUpd[0]->phone != $presInfoPre[0]->phone || $presInfoUpd[0]->inquiries_contact != $presInfoPre[0]->inquiries_contact ||
                    $presInfoUpd[0]->ein != $presInfoPre[0]->ein || $presInfoUpd[0]->ein_letter_path != $presInfoPre[0]->ein_letter_path || $presInfoUpd[0]->inquiries_note != $presInfoPre[0]->inquiries_note ||
                    $presInfoUpd[0]->email != $presInfoPre[0]->email || $presInfoUpd[0]->po_box != $presInfoPre[0]->po_box || $presInfoUpd[0]->website_url != $presInfoPre[0]->website_url ||
                    $presInfoUpd[0]->website_status != $presInfoPre[0]->website_status || $presInfoUpd[0]->egroup != $presInfoPre[0]->egroup || $presInfoUpd[0]->territory != $presInfoPre[0]->territory ||
                    $presInfoUpd[0]->additional_info != $presInfoPre[0]->additional_info || $presInfoUpd[0]->status != $presInfoPre[0]->status || $presInfoUpd[0]->notes != $presInfoPre[0]->notes ||
                    $mailDataAvpp['avpfnamePre'] != $mailDataAvp['avpfnameUpd'] || $mailDataAvpp['avplnamePre'] != $mailDataAvp['avplnameUpd'] || $mailDataAvpp['avpemailPre'] != $mailDataAvp['avpemailUpd'] ||
                    $mailDataMvpp['mvpfnamePre'] != $mailDataMvp['mvpfnameUpd'] || $mailDataMvpp['mvplnamePre'] != $mailDataMvp['mvplnameUpd'] || $mailDataMvpp['mvpemailPre'] != $mailDataMvp['mvpemailUpd'] ||
                    $mailDatatresp['tresfnamePre'] != $mailDatatres['tresfnameUpd'] || $mailDatatresp['treslnamePre'] != $mailDatatres['treslnameUpd'] || $mailDatatresp['tresemailPre'] != $mailDatatres['tresemailUpd'] ||
                    $mailDataSecp['secfnamePre'] != $mailDataSec['secfnameUpd'] || $mailDataSecp['seclnamePre'] != $mailDataSec['seclnameUpd'] || $mailDataSecp['secemailPre'] != $mailDataSec['secemailUpd']) {

                Mail::to($to_email)
                    ->queue(new ChapersUpdatePrimaryCoor($mailData));
            }

            //List Admin Notification//
            $to_email2 = 'listadmin@momsclub.org';

            if ($presInfoUpd[0]->email != $presInfoPre[0]->email || $presInfoUpd[0]->bor_email != $presInfoPre[0]->bor_email || $mailDataAvpp['avpemailPre'] != $mailDataAvp['avpemailUpd'] ||
                        $mailDataMvpp['mvpemailPre'] != $mailDataMvp['mvpemailUpd'] || $mailDatatresp['tresemailPre'] != $mailDatatres['tresemailUpd'] ||
                        $mailDataSecp['secemailPre'] != $mailDataSec['secemailUpd']) {

                Mail::to($to_email2)
                    ->queue(new ChapersUpdateListAdmin($mailData));
            }

            //EIN Coor Notification//
            $to_email3 = 'jackie.mchenry@momsclub.org';

            if ($presInfoUpd[0]->name != $presInfoPre[0]->name) {

                Mail::to($to_email3)
                    ->queue(new ChapersUpdateEINCoor($mailData));
            }

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            echo $e->getMessage();
            exit();
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->to('/chapter/chapterlist')->with('fail', 'Something went wrong, Please try again..');
        }

        return redirect()->to('/chapter/chapterlist')->with('success', 'Chapter has been updated');
    }

    /**
     * Function for Zapping a Chapter (store)
     */
    public function updateChapterDisband(Request $request)
    {
        $input = $request->all();
        $chapterid = $input['chapterid'];
        $disbandReason = $input['reason'];
        $disbandLetter = $input['letter'];

        try {
            DB::beginTransaction();

            DB::table('chapters')
                ->where('id', $chapterid)
                ->update(['is_active' => 0, 'disband_reason' => $disbandReason, 'disband_letter' => $disbandLetter, 'zap_date' => date('Y-m-d')]);

            $userRelatedChpaterList = DB::table('boards as bd')
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
            DB::table('boards')
                ->where('chapter_id', $chapterid)
                ->update(['is_active' => 0]);

            $chapterList = DB::table('chapters')
                ->select('chapters.*', 'chapters.primary_coordinator_id as pcid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                    'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state', 'chapters.conference as conf')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.is_Active', '=', '0')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.id', $chapterid)
                ->orderByDesc('chapters.id')
                ->get();

            $chPcid = $chapterList[0]->pcid;

            $chapterName = $chapterList[0]->name;
            $chapterState = $chapterList[0]->state;
            $chapterEmail = $chapterList[0]->email;
            $chapterStatus = $chapterList[0]->status;
            //President Info
            $preinfo = DB::table('boards')
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
            //Avp info
            $avpinfo = DB::table('boards')
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

            $mvpinfo = DB::table('boards')
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
            //Treasurere info
            $triinfo = DB::table('boards')
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
            //secretary info
            $secinfo = DB::table('boards')
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
            //conference info
            $coninfo = DB::table('chapters')
                ->select('chapters.*', 'conference')
                ->where('id', $chapterid)
                ->get();
            $conf = $coninfo[0]->conference;

            // Load Board and Coordinators for Sending Email
            $chId = $chapterList[0]->id;

            $emailData = $this->userController->loadEmailDetails($chId);
            $emailListChap = $emailData['emailListChap'];
            $emailListCoord = $emailData['emailListCoord'];

            // Load Conference Coordinators information for signing email
            $chConf = $chapterList[0]->conf;
            $chPcid = $chapterList[0]->pcid;

            $coordinatorData = $this->userController->loadConferenceCoord($chConf, $chPcid);
            $cc_fname = $coordinatorData['cc_fname'];
            $cc_lname = $coordinatorData['cc_lname'];
            $cc_pos = $coordinatorData['cc_pos'];
            $cc_conf = $coordinatorData['cc_conf'];
            $cc_conf_desc = $coordinatorData['cc_conf_desc'];
            $cc_email = $coordinatorData['cc_email'];

            $mailData = [
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
                'cc_fname' => $cc_fname,
                'cc_lname' => $cc_lname,
                'cc_pos' => $cc_pos,
                'cc_conf' => $cc_conf,
                'cc_conf_desc' => $cc_conf_desc,
                'cc_email' => $cc_email,
            ];

            //Primary Coordinator Notification//
            $to_email = 'listadmin@momsclub.org';
            Mail::to($to_email)
                ->queue(new ChapterRemoveListAdmin($mailData));

            //Standard Disbanding Letter Send to Board & Coordinators//
            $to_email2 = explode(',', $emailListChap);
            $cc_email2 = explode(',', $emailListCoord);
            if ($disbandLetter == 1) {
                $pdfPath = $this->generateAndSaveDisbandLetter($chapterid);   // Generate and save the PDF
                Mail::to($to_email2)
                    ->cc($cc_email2)
                    ->queue(new ChapterDisbandLetter($mailData, $pdfPath));
            }

            // Commit the transaction
            DB::commit();

            $message = 'Chapter disbanded successfully';

            // Determine response based on request type
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'redirect' => url('/chapter/zapped'),
                ]);
            } else {
                return redirect()->to('/chapter/zapped')->with('success', $message);
            }
        } catch (\Exception $e) {
            // Rollback transaction on exception
            DB::rollback();
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            // Determine response based on request type
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $message,
                    'redirect' => url('/chapter/zapped'),
                ]);
            } else {
                return redirect()->to('/chapter/zapped')->with('error', $message);
            }
        }
    }

    public function generateAndSaveDisbandLetter($chapterid)
    {
        $chapterDetails = DB::table('chapters')
            ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.ein as ein', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name',
                'st.state_short_name as state', 'bd.first_name as pres_fname', 'bd.last_name as pres_lname', 'bd.street_address as pres_addr', 'bd.city as pres_city', 'bd.state as pres_state',
                'bd.zip as pres_zip', 'chapters.conference as conf', 'cf.conference_name as conf_name', 'cf.conference_description as conf_desc', 'chapters.primary_coordinator_id as pcid')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            // ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.id', '=', $chapterid)
            ->get();

        // Load Conference Coordinators information for signing letter
        $chName = $chapterDetails[0]->chapter_name;
        $chState = $chapterDetails[0]->state;
        $chConf = $chapterDetails[0]->conf;
        $chPcid = $chapterDetails[0]->pcid;

        $coordinatorData = $this->userController->loadConferenceCoord($chConf, $chPcid);
        $cc_fname = $coordinatorData['cc_fname'];
        $cc_lname = $coordinatorData['cc_lname'];
        $cc_pos = $coordinatorData['cc_pos'];

        $pdfData = [
            'chapter_name' => $chapterDetails[0]->chapter_name,
            'state' => $chapterDetails[0]->state,
            'conf' => $chapterDetails[0]->conf,
            'conf_name' => $chapterDetails[0]->conf_name,
            'conf_desc' => $chapterDetails[0]->conf_desc,
            'ein' => $chapterDetails[0]->ein,
            'pres_fname' => $chapterDetails[0]->pres_fname,
            'pres_lname' => $chapterDetails[0]->pres_lname,
            'pres_addr' => $chapterDetails[0]->pres_addr,
            'pres_city' => $chapterDetails[0]->pres_city,
            'pres_state' => $chapterDetails[0]->pres_state,
            'pres_zip' => $chapterDetails[0]->pres_zip,
            'cc_fname' => $cc_fname,
            'cc_lname' => $cc_lname,
            'cc_pos' => $cc_pos,
        ];

        $pdf = Pdf::loadView('pdf.disbandletter', compact('pdfData'));

        $chapterName = str_replace('/', '', $pdfData['chapter_name']); // Remove any slashes from chapter name
        $filename = $pdfData['state'].'_'.$chapterName.'_Disband_Letter.pdf'; // Use sanitized chapter name

        $pdfPath = storage_path('app/pdf_reports/'.$filename);
        $pdf->save($pdfPath);

        $googleClient = new Client;
        $client_id = \config('services.google.client_id');
        $client_secret = \config('services.google.client_secret');
        $refresh_token = \config('services.google.refresh_token');
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        $chapterName = str_replace('/', '', $pdfData['chapter_name']); // Remove any slashes from chapter name
        $accessToken = json_decode((string) $response->getBody(), true)['access_token'];

        $sharedDriveId = '1PlBi8BE2ESqUbLPTkQXzt1dKhwonyU_9';   //Shared Drive -> Disband Letters

        // Set parent IDs for the file
        $fileMetadata = [
            'name' => $filename,
            'mimeType' => 'application/pdf',
            'parents' => [$sharedDriveId],
        ];

        // Upload the file
        $fileContent = file_get_contents($pdfPath);
        $fileContentBase64 = base64_encode($fileContent);
        $metadataJson = json_encode($fileMetadata);

        $response = $googleClient->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
            ],
            'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
        ]);

        if ($response->getStatusCode() === 200) { // Check for a successful status code
            $pdf_file_id = json_decode($response->getBody()->getContents(), true)['id'];
            $chapter = Chapter::find($chapterid);
            $chapter->disband_letter_path = $pdf_file_id;
            $chapter->save();

            return $pdfPath;  // Return the full local stored path
        }
    }

    /**
     * Display the Zapped chapter list mapped with Conference Region
     */
    public function showZappedChapter(Request $request)
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
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
        }

        $baseQuery = DB::table('chapters as ch')
                ->select('ch.id', 'ch.state', 'ch.name', 'ch.ein', 'ch.zap_date', 'ch.disband_reason', 'st.state_short_name as state',
                    'cf.short_name as conf', 'rg.short_name as reg')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'ch.id')
                ->leftJoin('state as st', 'ch.state', '=', 'st.id')
                ->leftjoin('region as rg', 'ch.region', '=', 'rg.id')
                ->leftJoin('conference as cf', 'ch.conference', '=', 'cf.id')
                ->where('ch.is_active', '=', '0')
                ->where('bd.board_position_id', '=', '1')
                ->orderByDesc('ch.zap_date');

        if ($conditions['founderCondition']) {
            $baseQuery;
    } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('ch.conference', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('ch.region', '=', $corRegId);
    } else {
        $baseQuery->whereIn('ch.primary_coordinator_id', $inQryArr);
    }

    $chapterList = $baseQuery->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('chapters.chapzapped')->with($data);
    }

    /**
     * View the Zapped chapter list
     */
    public function showZappedChapterView(Request $request, $id)
    {
        $user = User::find($request->user()->id);
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->Coordinators;
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $financial_report_array = FinancialReport::find($id);
        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address', 'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '0')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        $corConfId = $chapterList[0]->conference;
        $corId = $chapterList[0]->primary_coordinator_id;
        $AVPDetails = DB::table('boards as bd')
            ->select('bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.board_position_id', 'bd.street_address as avp_addr', 'bd.city as avp_city', 'bd.zip as avp_zip', 'bd.phone as avp_phone', 'bd.state as avp_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '2')
            ->get();
        if (count($AVPDetails) == 0) {
            $AVPDetails[0] = ['avp_fname' => '', 'avp_lname' => '', 'avp_email' => '', 'avp_addr' => '', 'avp_city' => '', 'avp_zip' => '', 'avp_phone' => '', 'avp_state' => '', 'user_id' => ''];
            $AVPDetails = json_decode(json_encode($AVPDetails));
        }

        $MVPDetails = DB::table('boards as bd')
            ->select('bd.first_name as mvp_fname', 'bd.last_name as mvp_lname', 'bd.email as mvp_email', 'bd.board_position_id', 'bd.street_address as mvp_addr', 'bd.city as mvp_city', 'bd.zip as mvp_zip', 'bd.phone as mvp_phone', 'bd.state as mvp_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '3')
            ->get();
        if (count($MVPDetails) == 0) {
            $MVPDetails[0] = ['mvp_fname' => '', 'mvp_lname' => '', 'mvp_email' => '', 'mvp_addr' => '', 'mvp_city' => '', 'mvp_zip' => '', 'mvp_phone' => '', 'mvp_state' => '', 'user_id' => ''];
            $MVPDetails = json_decode(json_encode($MVPDetails));
        }

        $TRSDetails = DB::table('boards as bd')
            ->select('bd.first_name as trs_fname', 'bd.last_name as trs_lname', 'bd.email as trs_email', 'bd.board_position_id', 'bd.street_address as trs_addr', 'bd.city as trs_city', 'bd.zip as trs_zip', 'bd.phone as trs_phone', 'bd.state as trs_state', 'bd.user_id as user_id')
            ->where('bd.chapter_id', '=', $id)
            ->where('bd.board_position_id', '=', '4')
            ->get();
        if (count($TRSDetails) == 0) {
            $TRSDetails[0] = ['trs_fname' => '', 'trs_lname' => '', 'trs_email' => '', 'trs_addr' => '', 'trs_city' => '', 'trs_zip' => '', 'trs_phone' => '', 'trs_state' => '', 'user_id' => ''];
            $TRSDetails = json_decode(json_encode($TRSDetails));
        }

        $SECDetails = DB::table('boards as bd')
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
            ->orderBy('id')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->where('id', '>=', 0)
            ->orderBy('conference_name')
            ->get();


        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $chapterList[0]->start_month_id;
        $data = ['corId' => $corId, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'chapterList' => $chapterList, 'regionList' => $regionList, 'confList' => $confList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth, 'currentMonth' => $currentMonth];

        return view('chapters.chapzappedview')->with($data);
    }

     /**
     * Function for updating a Zapped Chapter Email (store)
     */
    public function updateZappedChapter(Request $request, $id): RedirectResponse
    {
        $chapterId = $id;
        DB::beginTransaction();
        try {
            //President Info
            $PREDetails = DB::table('boards')
                ->select('id', 'user_id')
                ->where('chapter_id', '=', $chapterId)
                ->where('board_position_id', '=', '1')
                ->get();

            $userId = $PREDetails[0]->user_id;
            $boardId = $PREDetails[0]->id;

            $user = User::find($userId);
            $user->email = $request->input('ch_pre_email');
            $user->save();

            DB::table('boards')
                ->where('id', $boardId)
                ->update(['email' => $request->input('ch_pre_email')]);

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->to('/chapter/zapped')->with('fail', 'Something went wrong, Please try again...');
        }

        return redirect()->to('/chapter/zapped')->with('success', 'President Email has been updated');
    }

    /**
     * Function for unZapping a Chapter (store)
     */
    public function updateChapterUnZap($id): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $chapterid = $id;
            DB::table('chapters')
                ->where('id', $chapterid)
                ->update(['is_active' => 1, 'disband_reason' => '', 'disband_letter' => null, 'zap_date' => null]);

            $userRelatedChpaterList = DB::table('boards as bd')
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
            DB::table('boards')
                ->where('chapter_id', $chapterid)
                ->update(['is_active' => 1]);

            $chapterList = DB::table('chapters')
                ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name as state')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.is_Active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.id', $chapterid)
                ->orderByDesc('chapters.id')
                ->get();

            $chapterName = $chapterList[0]->name;
            $chapterState = $chapterList[0]->state;
            $chapterEmail = $chapterList[0]->email;
            $chapterStatus = $chapterList[0]->status;
            //President Info
            $preinfo = DB::table('boards')
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
            //Avp info
            $avpinfo = DB::table('boards')
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

            $mvpinfo = DB::table('boards')
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
            //Treasurere info
            $triinfo = DB::table('boards')
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
            //secretary info
            $secinfo = DB::table('boards')
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
            //conference info
            $coninfo = DB::table('chapters')
                ->select('chapters.*', 'conference')
                ->where('id', $chapterid)
                ->get();
            $conf = $coninfo[0]->conference;

            $mailData = [
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
            ];

            //Primary Coordinator Notification//
            $to_email = 'listadmin@momsclub.org';

            Mail::to($to_email)
                ->queue(new ChapterReAddListAdmin($mailData));

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->to('/chapter/chapterlist')->with('fail', 'Something went wrong, Please try again..');
        }

        return redirect()->to('/chapter/chapterlist')->with('success', 'Chapter was successfully unzapped');
    }

   /**
     * ReRegistration List
     */
    public function showChapterReRegistration(Request $request)
    {
        //$corDetails = User::find($request->user()->id)->Coordinators;
        $user = User::find($request->user()->id);
        // Check if user is not found
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->Coordinators;
        // Check if BoardDetails is not found for the user
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $currentYear = date('Y');
        $currentMonth = date('m');
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];
        $request->session()->put('positionid', $positionId);
        $request->session()->put('secpositionid', $secPositionId);
        $request->session()->put('corconfid', $corConfId);
        $request->session()->put('corregid', $corRegId);

         // Get the conditions
         $conditions = getPositionConditions($positionId, $secPositionId);

        if ($conditions['coordinatorCondition']) {
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
        }

        $baseQuery = DB::table('chapters as ch')
            ->select(
                'ch.id', 'ch.notes', 'ch.name', 'ch.state', 'ch.reg_notes', 'ch.next_renewal_year', 'ch.dues_last_paid', 'ch.start_month_id',
                'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'bd.first_name as bor_f_name', 'bd.last_name as bor_l_name',
                'bd.email as bor_email', 'bd.phone as phone', 'st.state_short_name', 'db.month_short_name', 'cf.short_name as conf', 'rg.short_name as reg')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'ch.id')
            ->leftJoin('conference as cf', 'ch.conference', '=', 'cf.id')
            ->leftJoin('region as rg', 'ch.region', '=', 'rg.id')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->leftJoin('db_month as db', 'ch.start_month_id', '=', 'db.id')
            ->where('ch.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1');

            if ($conditions['founderCondition']) {
                $baseQuery;
               }   elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('ch.conference', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('ch.region', '=', $corRegId);
        } else {
            $baseQuery->whereIn('ch.primary_coordinator_id', $inQryArr);
        }

        // If checkbox is not checked, apply the additional year and month filtering
        if (! isset($_GET['check']) || $_GET['check'] !== 'yes') {
            $baseQuery->where(function ($query) use ($currentYear, $currentMonth) {
                $query->where('ch.next_renewal_year', '<', $currentYear)
                    ->orWhere(function ($query) use ($currentYear, $currentMonth) {
                        $query->where('ch.next_renewal_year', '=', $currentYear)
                            ->where('ch.start_month_id', '<=', $currentMonth);
                    });
            });
        }

        // Apply sorting based on checkbox status -- show All Chapters
        if (isset($_GET['check']) && $_GET['check'] == 'yes') {
            $checkBoxStatus = 'checked';
            $baseQuery
                ->orderBy('ch.conference')
                ->orderBy('st.state_short_name')
                ->orderBy('ch.name');
        } else {
            $checkBoxStatus = '';
            $baseQuery
                ->orderByDesc('ch.next_renewal_year')
                ->orderByDesc('ch.start_month_id')
                ->orderBy('ch.conference')
                ->orderBy('st.state_short_name')
                ->orderBy('ch.name');
        }

        $reChapterList = $baseQuery->get();

        $countList = count($reChapterList);

        $data = ['countList' => $countList, 'reChapterList' => $reChapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $corId];

        return view('chapters.chapreregistration')->with($data);
    }

    /**
     * ReRegistration Payment
     */
    public function showChapterReRegistrationPayment($id): View
    {
        $chapterList = DB::table('chapters as ch')
            ->select('ch.id', 'ch.next_renewal_year', 'ch.name', 'ch.dues_last_paid', 'ch.reg_notes',
                'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.conference_id as cor_confid', 'cd.email as cor_email', 'bd.email as bor_email', 'st.state_short_name as statename')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'ch.id')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('ch.id', $id)
            ->get();
        $maxDateLimit = Carbon::now()->format('Y-m-d');
        $minDateLimit = Carbon::now()->subYear()->format('Y-m-d');
        // $minDateLimit = '';
        $data = ['chapterList' => $chapterList, 'maxDateLimit' => $maxDateLimit, 'minDateLimit' => $minDateLimit];

        return view('chapters.chapreregpayment')->with($data);
    }

    /**
     * ReRegistration Payment (store)
     */
    public function updateChapterReRegistrationPayment(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $nextRenewalYear = $request->input('ch_nxt_renewalyear');
        $primaryCordEmail = $request->input('ch_pc_email');
        $boardPresEmail = $request->input('ch_pre_email');

        $chapter = Chapter::find($id);
        $chId = $chapter['id'];

        $emailData = $this->userController->loadEmailDetails($chId);
        $chapEmail = $emailData['chapEmail'];
        $emailListChap = $emailData['emailListChap'];
        $emailListCoord = $emailData['emailListCoord'];

        $to_email1 = $emailListChap;
        $to_email2 = $chapEmail;
        $to_email = array_merge((array)$to_email1, (array)$to_email2);
        $cc_email = $primaryCordEmail;

        DB::beginTransaction();
        try {
            $chapter->dues_last_paid = $request->input('PaymentDate');
            $chapter->members_paid_for = $request->input('MembersPaidFor');
            $chapter->reg_notes = $request->input('ch_regnotes');
            $chapter->next_renewal_year = $nextRenewalYear + 1;
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();
            if ($request->input('ch_notify') == 'on') {

                $mailData = [
                    'chapterName' => $request->input('ch_name'),
                    'chapterState' => $request->input('ch_state'),
                    'chapterPreEmail' => $request->input('ch_pre_email'),
                    'chapterDate' => $request->input('PaymentDate'),
                    'chapterMembers' => $request->input('MembersPaidFor'),
                    'cordFname' => $request->input('ch_pc_fname'),
                    'cordLname' => $request->input('ch_pc_lname'),
                    'cordConf' => $request->input('ch_pc_confid'),
                ];

                // Payment Thank You Email
                Mail::to($to_email)
                    ->cc($cc_email)
                    ->queue(new PaymentsReRegChapterThankYou($mailData));
            }

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->to('/chapter/reregistration')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/chapter/reregistration')->with('success', 'Payment has been successfully payment saved');
    }

    /**
     * ReRegistration Notes
     */
    public function showChapterReRegistrationNotes(Request $request, $id)
    {
        //$corDetails = User::find($request->user()->id)->Coordinators;
        $user = User::find($request->user()->id);
        // Check if user is not found
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->Coordinators;
        // Check if BoardDetails is not found for the user
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $chapterList = DB::table('chapters as ch')
            ->select('ch.id', 'ch.name', 'ch.dues_last_paid', 'ch.reg_notes', 'ch.next_renewal_year',
                'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.conference_id as cor_confid', 'cd.email as cor_email', 'bd.email as bor_email', 'st.state_short_name as statename')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'ch.id')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('ch.id', $id)
            ->get();
        $maxDateLimit = Carbon::now()->format('Y-m-d');
        $minDateLimit = Carbon::now()->subYear()->format('Y-m-d');
        // $minDateLimit = '';
        $data = ['chapterList' => $chapterList, 'maxDateLimit' => $maxDateLimit, 'minDateLimit' => $minDateLimit];

        return view('chapters.chapreregnotes')->with($data);
    }

    /**
     * ReRegistration Notes (store)
     */
    public function updateChapterReRegistrationNotes(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $nextRenewalYear = $request->input('ch_nxt_renewalyear');

        //$nextRenewalYear = date('Y');
        $primaryCordEmail = $request->input('ch_pc_email');
        $boardPresEmail = $request->input('ch_pre_email');
        $chapter = Chapter::find($id);
        DB::beginTransaction();
        try {

            $chapter->reg_notes = $request->input('ch_regnotes');

            $chapter->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->to('/chapter/reregistrationn')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/chapter/reregistration')->with('success', 'Your Re-Regisration Notes have been saved');
    }

    /**
     * ReRegistration Reminders Auto Send
     */
    public function createChapterReRegistrationReminder(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corName = $corDetails['first_name'].' '.$corDetails['last_name'];

        $month = date('m');
        $year = date('Y');
        $monthRangeStart = $month;
        $monthRangeEnd = $month - 1;
        $lastYear = $year - 1;
        $thisyear = $year;

        if ($month == 1) {
            $monthRangeStart = 12;
            $lastYear = $lastYear - 1;
        }
        if ($month == 1) {
            $monthRangeEnd = 12;
            $thisyear = $year - 1;
        }

        $rangeStartDate = Carbon::create($lastYear, $monthRangeStart, 1);
        $rangeEndDate = Carbon::create($thisyear, $monthRangeEnd, 1)->endOfMonth();

        // Convert $month to words
        $monthInWords = Carbon::createFromFormat('m', $month)->format('F');

        // Format dates as "mm-dd-yyyy"
        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        $chapters = Chapter::select('chapters.*', 'chapters.name as chapter_name', 'state.state_short_name as chapter_state',
            'chapters.primary_coordinator_id as pcid', 'chapters.email as ch_email', 'chapters.start_month_id as start_month',)
            ->join('state', 'chapters.state', '=', 'state.id')
            ->where('chapters.conference', $corConfId)
            ->where('chapters.start_month_id', $month)
            ->where('chapters.next_renewal_year', $year)
            ->where('chapters.is_active', 1)
            ->get();

            if ($chapters->isEmpty()) {
                return redirect()->back()->with('info', 'There are no Chapters with Registrations Due.');
            }

            $chapterIds = [];
            $chapterEmails = [];
            $coordinatorEmails = [];
            $mailData = [];

            foreach ($chapters as $chapter) {
                $chapterIds[] = $chapter->id;

                if ($chapter->name) {
                    $emailData = $this->userController->loadEmailDetails($chapter->id);
                    $chapEmail = $emailData['chapEmail'];
                    $emailListChap = $emailData['emailListChap'];
                    $emailListCoord = $emailData['emailListCoord'];

                    $to_email1 = $emailListChap;
                    $to_email2 = $chapEmail;
                    $chapterEmails[$chapter->name] = array_merge((array)$to_email1, (array)$to_email2);

                    $cc_email = $emailListCoord;
                    $coordinatorEmails[$chapter->name] = $cc_email;
                }

                $chapterState = $chapter->chapter_state;

                $mailData[$chapter->chapter_name] = [
                    'chapterName' => $chapter->chapter_name,
                    'chapterState' => $chapterState,
                    'startRange' => $rangeStartDateFormatted,
                    'endRange' => $rangeEndDateFormatted,
                    'startMonth' => $monthInWords,
                ];

               }

    foreach ($mailData as $chapterName => $data) {
        $to_email = $chapterEmails[$chapterName] ?? [];
        $cc_email = $coordinatorEmails[$chapterName] ?? [];

        if (!empty($to_email)) {
            Mail::to($to_email)
                ->cc($cc_email)
                        ->queue(new PaymentsReRegReminder($data));
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

        return redirect()->to('/chapter/reregistration')->with('success', 'Re-Registration Reminders have been successfully sent.');
    }

    /**
     * ReRegistration Late Notices Auto Send
     */
    public function createChapterReRegistrationLateReminder(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corName = $corDetails['first_name'].' '.$corDetails['last_name'];

        $month = date('m');
        $year = date('Y');
        $lastMonth = $month - 1;
        $monthRangeStart = $month - 1;
        $monthRangeEnd = $month - 2;
        $lastYear = $year - 1;
        $thisyear = $year;

        if ($month == 1) {
            $monthRangeStart = 11;
            $lastYear = $lastYear - 1;
        } elseif ($month == 2) {
            $monthRangeStart = 12;
            $lastYear = $lastYear - 1;
        }

        if ($month == 1) {
            $monthRangeEnd = 11;
            $thisyear = $year - 1;
        } elseif ($month == 2) {
            $monthRangeEnd = 12;
            $thisyear = $year - 1;
        }

        $rangeStartDate = Carbon::create($lastYear, $monthRangeStart, 1);
        $rangeEndDate = Carbon::create($thisyear, $monthRangeEnd, 1)->endOfMonth();

        // Convert $month to words
        $monthInWords = Carbon::createFromFormat('m', $month)->format('F');
        $lastMonthInWords = Carbon::createFromFormat('m', $lastMonth)->format('F');

        // Format dates as "mm-dd-yyyy"
        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        $chapters = Chapter::select('chapters.*', 'chapters.name as chapter_name', 'state.state_short_name as chapter_state', 'boards.email as bor_email',
            'chapters.primary_coordinator_id as pcid', 'chapters.email as ch_email', 'chapters.start_month_id as start_month',
            'boards.board_position_id')
            ->join('state', 'chapters.state', '=', 'state.id')
            ->join('boards', 'chapters.id', '=', 'boards.chapter_id')
            ->whereIn('boards.board_position_id', [1, 2, 3, 4, 5])
            ->where('chapters.conference', $corConfId)
            ->where(function ($query) use ($month, $year) {
                if ($month == 1) {
                    // January, so get chapters with December start_month_id
                    $query->where('chapters.start_month_id', 12)
                        ->where('chapters.next_renewal_year', $year - 1);
                } else {
                    // Any other month, get chapters with $month - 1 start_month_id
                    $query->where('chapters.start_month_id', $month - 1)
                        ->where('chapters.next_renewal_year', $year);
                }
            })
            ->where('chapters.is_active', 1)
            ->get();

            if ($chapters->isEmpty()) {
                return redirect()->back()->with('info', 'There are no Chapters with Late Registrations Due.');
            }

            $chapterIds = [];
            $chapterEmails = [];
            $coordinatorEmails = [];
            $mailData = [];

            foreach ($chapters as $chapter) {
                $chapterIds[] = $chapter->id;

                if ($chapter->name) {
                    $emailData = $this->userController->loadEmailDetails($chapter->id);
                    $chapEmail = $emailData['chapEmail'];
                    $emailListChap = $emailData['emailListChap'];
                    $emailListCoord = $emailData['emailListCoord'];

                    $to_email1 = $emailListChap;
                    $to_email2 = $chapEmail;
                    $chapterEmails[$chapter->name] = array_merge((array)$to_email1, (array)$to_email2);

                    $cc_email = $emailListCoord;
                    $coordinatorEmails[$chapter->name] = $cc_email;
                }

                $chapterState = $chapter->chapter_state;

                $mailData[$chapter->chapter_name] = [
                    'chapterName' => $chapter->chapter_name,
                    'chapterState' => $chapterState,
                    'startRange' => $rangeStartDateFormatted,
                    'endRange' => $rangeEndDateFormatted,
                    'startMonth' => $lastMonthInWords,
                    'dueMonth' => $monthInWords,
                ];

            }

            foreach ($mailData as $chapterName => $data) {
                $to_email = $chapterEmails[$chapterName] ?? [];
                $cc_email = $coordinatorEmails[$chapterName] ?? [];

                if (!empty($to_email)) {
                    Mail::to($to_email)
                        ->cc($cc_email)
                        ->queue(new PaymentsReRegLate($data));
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

        return redirect()->to('/chapter/reregistration')->with('success', 'Re-Registration Late Reminders have been successfully sent.');
    }

    /**
     * Display the Inquiries Chapter list
     */
    public function showChapterInquiries(Request $request)
    {
        $user = User::find($request->user()->id);
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->Coordinators;
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

        // Get the conditions
        $conditions = getPositionConditions($positionId, $secPositionId);

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
        if ($conditions['founderCondition'] || $conditions['inquiriesInternationalCondition']) {
            $inquiriesList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.inquiries_contact as inq_con', 'chapters.territory as terry', 'chapters.status as status', 'chapters.inquiries_note as inq_note', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'cd.email as cd_email', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'st.state_short_name as state')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->orderBy('st.state_short_name')
                ->get();

        } elseif ($conditions['assistConferenceCoordinatorCondition'] || $conditions['inquiriesConferneceCondition']) {
                    $inquiriesList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.inquiries_contact as inq_con', 'chapters.territory as terry', 'chapters.status as status', 'chapters.inquiries_note as inq_note', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'cd.email as cd_email', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'st.state_short_name as state')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.conference', '=', $corConfId)
                ->orderBy('st.state_short_name')
                ->get();
        } else {
            $inquiriesList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.inquiries_contact as inq_con', 'chapters.territory as terry', 'chapters.status as status', 'chapters.inquiries_note as inq_note', 'cd.first_name as cd_fname', 'cd.last_name as cd_lname', 'cd.email as cd_email', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'st.state_short_name as state')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
                ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.is_active', '=', '1')
                ->where('bd.board_position_id', '=', '1')
                ->where('chapters.region', '=', $corRegId)
                ->orderBy('st.state_short_name')
                ->get();
        }

        $data = ['inquiriesList' => $inquiriesList, 'corConfId' => $corConfId, 'corRegId' => $corRegId];

        return view('chapters.chapinquiries')->with($data);
    }

    /**
     * Display the Inquiries Detailed Chapter View
     */
    public function showChapterInquiriesView(Request $request, $id)
    {
        $user = User::find($request->user()->id);
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->Coordinators;
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];

        $chapterList = DB::table('chapters as ch')
            ->select('ch.id', 'ch.conference', 'ch.country', 'ch.name', 'ch.state', 'ch.region', 'ch.territory', 'ch.status', 'ch.notes', 'ch.inquiries_contact', 'ch.inquiries_note',
                'ch.primary_coordinator_id', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.phone as phone', 'bd.board_position_id')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name')
            ->get();

        $data = ['chapterList' => $chapterList, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr];

        return view('chapters.chapinquiriesview')->with($data);
    }

     /**
     * Display the Zapped Inquiries list
     */
    public function showZappedChapterInquiries(Request $request)
    {
        $user = User::find($request->user()->id);
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->Coordinators;
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $corId = $corDetails['id'];
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
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '0')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.conference', '=', $corConfId)
            ->orderByDesc('chapters.zap_date')
            ->get();

        $data = ['inquiriesList' => $inquiriesList, 'corConfId' => $corConfId];

        return view('chapters.chapinquirieszapped')->with($data);
    }

    /**
     * Display the Website Details
     */
    public function showChapterWebsite(Request $request)
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $corRegId = $corDetails['region_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];
        $secPositionId = $corDetails['sec_position_id'];

       // Get the conditions
       $conditions = getPositionConditions($positionId, $secPositionId);

       if ($conditions['coordinatorCondition']) {
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
       }

        $baseQuery = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.website_url as web', 'chapters.website_status as status', 'chapters.website_notes as web_notes', 'chapters.egroup as egroup',
                    'st.state_short_name as state', 'cd.region_id', 'chapters.region', 'rg.short_name as reg', 'cf.short_name as conf')
                ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
                ->join('state as st', 'chapters.state', '=', 'st.id')
                ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
                ->leftJoin('region as rg', 'chapters.region', '=', 'rg.id')
                ->where('chapters.is_active', '=', '1')
                ->orderBy('st.state_short_name')
                ->orderBy('chapters.name');

        if ($conditions['founderCondition']) {
            $baseQuery;
    } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('chapters.conference', '=', $corConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('chapters.region', '=', $corRegId);
    } else {
        $baseQuery->whereIn('chapters.primary_coordinator_id', $inQryArr);
    }

    $websiteList = $baseQuery->get();

        $data = ['websiteList' => $websiteList];

        return view('chapters.chapwebsite')->with($data);
    }

    /**
     * Edit Website Details
     */
    public function showChapterWebsiteView(Request $request, $id)
    {
        $user = User::find($request->user()->id);
        if (! $user) {
            return redirect()->route('home');
        }

        $corDetails = $user->Coordinators;
        if (! $corDetails) {
            return redirect()->route('home');
        }

        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];

        $chapterList = DB::table('chapters as ch')
            ->select('ch.id', 'ch.name', 'ch.conference', 'ch.state', 'ch.region', 'ch.status', 'ch.website_url', 'ch.website_status', 'ch.egroup', 'ch.social1', 'ch.social2', 'ch.social3',
                'ch.website_notes', 'ch.primary_coordinator_id', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.phone')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->where('bd.board_position_id', '=', '1')
            ->get();
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();
        $countryArr = DB::table('country')
            ->select('country.*')
            ->orderBy('id')
            ->get();
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name')
            ->get();

        $primaryCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos', 'pos2.short_title as sec_pos')
            ->join('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
            ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
            ->where('cd.conference_id', '=', $corConfId)
            ->where('cd.position_id', '<=', '7')
            ->where('cd.position_id', '>=', '1')
            ->where('cd.is_active', '=', '1')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();

        $data = ['chapterList' => $chapterList, 'regionList' => $regionList, 'primaryCoordinatorList' => $primaryCoordinatorList, 'stateArr' => $stateArr, 'countryArr' => $countryArr];

        return view('chapters.chapwebsiteview')->with($data);
    }

    /**
     * Udaate Website Details (store)
     */
    public function updateChapterWebsite(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
        $corConfId = $corDetails['conference_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $positionId = $corDetails['position_id'];

        $ch_webstatus = $request->input('ch_webstatus');
        if (empty(trim($ch_webstatus))) {
            $ch_webstatus = 0; // Set it to 0 if it's blank
        }

        DB::table('chapters')
            ->where('id', $id)
            ->update(['website_url' => $request->input('ch_website'),
                'website_status' => $ch_webstatus,
                'egroup' => $request->input('ch_onlinediss'),
                'social1' => $request->input('ch_social1'),
                'social2' => $request->input('ch_social2'),
                'social3' => $request->input('ch_social3'),
                'website_notes' => $request->input('ch_notes')]);

        //Website Notifications//
        $cor_details = db::table('coordinators')
            ->select('email')
            ->where('conference_id', $corConfId)
            ->where('position_id', 9)
            ->where('is_active', 1)
            ->get();
        $row_count = count($cor_details);
        if ($row_count == 0) {
            $cc_details = db::table('coordinators')
                ->select('email')
                ->where('conference_id', $corConfId)
                ->where('position_id', 7)
                ->where('is_active', 1)
                ->get();
            $to_email4 = $cc_details[0]->email;    //conference coordinator
        } else {
            $to_email4 = $cor_details[0]->email;   //website reviewer if conf has one
        }

        if ($request->input('ch_webstatus') != $request->input('ch_hid_webstatus')) {
            $chapterDetails = Chapter::find($id);
            $stateArr = DB::table('state')
                ->select('state.*')
                ->orderBy('id')
                ->get();

            $chapterState = DB::table('state')
                ->select('state_short_name')
                ->where('id', '=', $chapterDetails->state)
                ->get();
            $chapterState = $chapterState[0]->state_short_name;

            $mailData = [
                'chapter_name' => $request->input('ch_name'),
                'chapter_state' => $chapterState,
                'ch_website_url' => $request->input('ch_website'),
            ];

            if ($request->input('ch_webstatus') == 1) {
                Mail::to($to_email4)
                    ->queue(new WebsiteAddNoticeAdmin($mailData));
            }

            if ($request->input('ch_webstatus') == 2) {
                Mail::to($to_email4)
                    ->queue(new WebsiteReviewNotice($mailData));
            }
        }

        return redirect()->to('/chapter/website')->with('success', 'Chapter Website has been changed successfully.');
    }

    /**
     * BoardList
     */
    public function showChapterBoardlist(Request $request): View
    {
        $corDetails = User::find($request->user()->id)->Coordinators;
        $corId = $corDetails['id'];
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
        $activeChapterList = DB::table('chapters')
            ->select('chapters.*', 'chapters.conference as conf', 'chapters.email as chapter_email', 'bd.first_name as pre_fname', 'bd.last_name as pre_lname', 'bd.email as pre_email', 'bd.street_address as pre_add',
                'bd.city as pre_city', 'bd.state as pre_state', 'bd.zip as pre_zip', 'bd.country as pre_country', 'bd.phone as pre_phone', 'st.state_short_name as state', 'rg.short_name as reg', 'cf.short_name as conf')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->leftJoin('region as rg', 'rg.id', '=', 'chapters.region')
            ->leftJoin('conference as cf', 'chapters.conference', '=', 'cf.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
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
                    ->select('chapters.id', 'bd.first_name as avp_fname', 'bd.last_name as avp_lname', 'bd.email as avp_email', 'bd.phone as avp_phone', 'bd.board_position_id as positionid')
                    ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
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
                    ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
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
                    ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
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
                    ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
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

            $countList = count($activeChapterList);
            $data = ['countList' => $countList, 'activeChapterList' => $activeChapterList, 'avpDeatils' => $avpDeatils, 'mvpDeatils' => $mvpDeatils, 'secDeatils' => $secDeatils, 'trsDeatils' => $trsDeatils];

            return view('chapters.chapboardlist')->with($data);
        }
    }

    /**
     * Reset Password
     */
    // public function updatePassword(Request $request)
    // {
    //     $request->validate([
    //         'new_password' => 'required',
    //     ]);

    //     $userId = $request->input('user_id');
    //     $newPassword = $request->input('new_password');

    //     $user = User::find($userId);
    //     if ($user) {
    //         $user->password = Hash::make($newPassword);
    //         $user->remember_token = null;
    //         $user->save();

    //         return response()->json(['message' => 'Password reset successfully.<br>Password is reset to default "TempPass4You" for this user.']);
    //     }
    //     return response()->json(['error' => 'User not found.'], 404);
    // }


        /**
     * Downline for Emailing Chapter and Coordintors
     */
    // public function getEmailDetails($id)
    // {
    //     $chapterList = DB::table('chapters')
    //         ->select('chapters.id as id', 'chapters.primary_coordinator_id as primary_coordinator_id', 'chapters.financial_report_received as report_received',
    //             'chapters.new_board_submitted as board_submitted', 'chapters.ein_letter as ein_letter', 'chapters.name as name', 'st.state_short_name as state')
    //         ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
    //         ->where('chapters.id', '=', $id)
    //         ->first();

    //     $chapterEmailList = DB::table('boards as bd')
    //         ->select('bd.email as bor_email')
    //         ->where('bd.chapter_id', '=', $chapterList->id)
    //         ->get();

    //     $emailListCord = '';
    //     foreach ($chapterEmailList as $val) {
    //         $email = $val->bor_email;
    //         $escaped_email = str_replace("'", "\\'", $email);
    //         if ($emailListCord == '') {
    //             $emailListCord = $escaped_email;
    //         } else {
    //             $emailListCord .= ';'.$escaped_email;
    //         }
    //     }
    //     $cc_string = '';
    //     $reportingList = DB::table('coordinator_reporting_tree')
    //         ->select('*')
    //         ->where('id', '=', $chapterList->primary_coordinator_id)
    //         ->get();
    //     foreach ($reportingList as $key => $value) {
    //         $reportingList[$key] = (array) $value;
    //     }
    //     $filterReportingList = array_filter($reportingList[0]);
    //     unset($filterReportingList['id']);
    //     unset($filterReportingList['layer0']);
    //     $filterReportingList = array_reverse($filterReportingList);
    //     $str = '';
    //     $array_rows = count($filterReportingList);
    //     $down_line_email = '';
    //     foreach ($filterReportingList as $key => $val) {
    //         if ($val > 1) {
    //             $corList = DB::table('coordinators as cd')
    //                 ->select('cd.email as cord_email')
    //                 ->where('cd.id', '=', $val)
    //                 ->where('cd.is_active', '=', 1)
    //                 ->get();
    //             if ($down_line_email == '') {
    //                 if (isset($corList[0])) {
    //                     $down_line_email = $corList[0]->cord_email;
    //                 }
    //             } else {
    //                 if (isset($corList[0])) {
    //                     $down_line_email .= ';'.$corList[0]->cord_email;
    //                 }
    //             }

    //         }
    //     }
    //     $cc_string = '?cc='.$down_line_email;

    //     return [
    //         'emailListCord' => $emailListCord,
    //         'cc_string' => $cc_string,
    //         'board_submitted' => $chapterList->board_submitted,
    //         'report_received' => $chapterList->report_received,
    //         'ein_letter' => $chapterList->ein_letter,
    //         'name' => $chapterList->name,
    //         'state' => $chapterList->state,
    //     ];

    // }

    // public function activateBoard($chapter_id, $lastUpdatedBy)
    // {
    //     // Fetching New Board Info from Incoming Board Members
    //     $incomingBoardDetails = DB::table('incoming_board_member')
    //         ->select('*')
    //         ->where('chapter_id', '=', $chapter_id)
    //         ->orderBy('board_position_id')
    //         ->get();
    //     $countIncomingBoardDetails = count($incomingBoardDetails);

    //     if ($countIncomingBoardDetails > 0) {
    //         DB::beginTransaction();
    //         try {
    //             // Fetching Existing Board Members from Board Details
    //             $boardDetails = DB::table('boards')
    //                 ->select('*')
    //                 ->where('chapter_id', '=', $chapter_id)
    //                 ->get();
    //             $countBoardDetails = count($boardDetails);

    //             if ($countBoardDetails > 0) {
    //                 // Mark ALL existing board members as outgoing
    //                 foreach ($boardDetails as $record) {
    //                     DB::table('users')
    //                         ->where('id', $record->user_id)
    //                         ->update([
    //                             'user_type' => 'outgoing',
    //                             'updated_at' => now(),
    //                         ]);
    //                 }

    //                 // Delete all board members associated with the chapter from boards table
    //                 DB::table('boards')
    //                     ->where('chapter_id', $chapter_id)
    //                     ->delete();
    //             }

    //             // Create & Activate Details of Board members from Incoming Board Members
    //             foreach ($incomingBoardDetails as $incomingRecord) {
    //                 // Check if user already exists
    //                 $existingUser = DB::table('users')->where('email', $incomingRecord->email)->first();

    //                 if ($existingUser) {
    //                     // If the user exists, update all necessary fields, including is_active and user_type
    //                     DB::table('users')
    //                         ->where('id', $existingUser->id)
    //                         ->update([
    //                             'first_name' => $incomingRecord->first_name,
    //                             'last_name' => $incomingRecord->last_name,
    //                             'email' => $incomingRecord->email,
    //                             'is_active' => 1,
    //                             'user_type' => 'board',
    //                             'updated_at' => now(),
    //                         ]);

    //                     $userId = $existingUser->id;
    //                 } else {
    //                     // Insert new user
    //                     $userId = DB::table('users')->insertGetId([
    //                         'first_name' => $incomingRecord->first_name,
    //                         'last_name' => $incomingRecord->last_name,
    //                         'email' => $incomingRecord->email,
    //                         'password' => Hash::make('TempPass4You'),
    //                         'user_type' => 'board',
    //                         'is_active' => 1,
    //                         'updated_at' => now(),
    //                     ]);
    //                 }

    //                 // Prepare board details data
    //                 $boardId = [
    //                     'user_id' => $userId,
    //                     'first_name' => $incomingRecord->first_name,
    //                     'last_name' => $incomingRecord->last_name,
    //                     'email' => $incomingRecord->email,
    //                     'board_position_id' => $incomingRecord->board_position_id,
    //                     'chapter_id' => $chapter_id,
    //                     'street_address' => $incomingRecord->street_address,
    //                     'city' => $incomingRecord->city,
    //                     'state' => $incomingRecord->state,
    //                     'zip' => $incomingRecord->zip,
    //                     'country' => 'USA',
    //                     'phone' => $incomingRecord->phone,
    //                     'last_updated_by' => $lastUpdatedBy,
    //                     'last_updated_date' => now(),
    //                     'is_active' => 1,
    //                 ];

    //                 // Upsert board details (update if the user and chapter already exist)
    //                 DB::table('boards')->upsert(
    //                     [$boardId], // The values to insert or update
    //                     ['user_id', 'chapter_id'], // The unique constraints for upsert
    //                     array_keys($boardId) // The columns to update if a conflict occurs
    //                 );

    //             }

    //             // Update Chapter after Board Active
    //             DB::update('UPDATE chapters SET new_board_active = ? WHERE id = ?', [1, $chapter_id]);

    //             // Delete all board members associated with the chapter from incoming_boards table
    //             DB::table('incoming_board_member')
    //                 ->where('chapter_id', $chapter_id)
    //                 ->delete();

    //             DB::commit();
    //         } catch (\Exception $e) {
    //             DB::rollback();
    //             // Log the exception or print it out for debugging
    //             Log::error('Error activating board: ' . $e->getMessage());
    //             throw $e;
    //         }
    //     }
    // }

    // /**
    //  * Unsubmit Report
    //  */
    // public function updateUnsubmit(Request $request, $chapter_id): RedirectResponse
    // {
    //     $corDetails = User::find($request->user()->id)->Coordinators;
    //     $userId = $corDetails['user_id'];
    //     $corId = $corDetails['id'];
    //     $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

    //     $chapter = Chapter::find($chapter_id);
    //     DB::beginTransaction();
    //     try {
    //         $chapter->financial_report_received = null;
    //         $chapter->last_updated_by = $lastUpdatedBy;
    //         $chapter->last_updated_date = date('Y-m-d H:i:s');
    //         $chapter->save();

    //         $report = FinancialReport::find($chapter_id);
    //         $report->farthest_step_visited_coord = '13';
    //         $report->submitted = null;
    //         $report->save();

    //         DB::commit();
    //     } catch (\Exception $e) {
    //         // Rollback Transaction
    //         DB::rollback();

    //         // Log the error
    //         Log::error($e);

    //         return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
    //     }
    //         return redirect()->back()->with('success', 'Report has been successfully Unsubmitted.');
    // }

    // /**
    //  * Clear Report Review
    //  */
    // public function updateClearReview(Request $request, $chapter_id): RedirectResponse
    // {
    //     $corDetails = User::find($request->user()->id)->Coordinators;
    //     $userId = $corDetails['user_id'];
    //     $corId = $corDetails['id'];
    //     $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

    //     $chapter = Chapter::find($chapter_id);
    //     DB::beginTransaction();
    //     try {
    //         $chapter->financial_report_received = '1';
    //         $chapter->financial_report_complete = null;
    //         $chapter->last_updated_by = $lastUpdatedBy;
    //         $chapter->last_updated_date = date('Y-m-d H:i:s');
    //         $chapter->save();

    //         $report = FinancialReport::find($chapter_id);
    //         $report->farthest_step_visited_coord = '13';
    //         $report->review_complete = null;
    //         $report->save();

    //         DB::commit();
    //     } catch (\Exception $e) {
    //         // Rollback Transaction
    //         DB::rollback();

    //         // Log the error
    //         Log::error($e);

    //         return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
    //     }
    //         return redirect()->back()->with('success', 'Review Complete has been successfully Cleared.');
    // }

    /**
     * Function for checking Email is registerd or not
     */
    // public function checkEmail($email): JsonResponse
    // {
    //     $isExists = \App\Models\User::where('email', $email)->first();
    //     if ($isExists) {
    //         return response()->json(['exists' => true]);
    //     }
    // }

    /**
     * Function for getting Reporting Hierarchy of Chapter
     */
    // public function checkReportId($id)
    // {
    //     $reportingList = DB::table('coordinator_reporting_tree')
    //         ->select('*')
    //         ->where('id', '=', $id)
    //         ->get();

    //     if ($reportingList->isNotEmpty()) {
    //         $reportingList = (array) $reportingList[0];
    //         $filterReportingList = array_filter($reportingList);
    //         unset($filterReportingList['id']);
    //         unset($filterReportingList['layer0']);
    //         $filterReportingList = array_reverse($filterReportingList);

    //         $str = '<table>';
    //         $i = 0;
    //         foreach ($filterReportingList as $key => $val) {
    //             $corList = DB::table('coordinators as cd')
    //                 ->select('cd.first_name as fname', 'cd.last_name as lname', 'cd.email as email', 'cp.short_title as pos', 'pos2.short_title as sec_pos')
    //                 ->join('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
    //                 ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
    //                 ->where('cd.id', '=', $val)
    //                 ->where('cd.is_active', '=', 1)
    //                 ->get();

    //             if (count($corList) > 0) {
    //                 $name = $corList[0]->fname.' '.$corList[0]->lname;
    //                 $email = $corList[0]->email;
    //                 if (!empty($corList[0]->sec_pos)) {
    //                     $pos = '('.$corList[0]->pos.'/'.$corList[0]->sec_pos.')';
    //                 } else {
    //                     $pos = '('.$corList[0]->pos.')';
    //                 }

    //                 if ($i == 0) {
    //                     $str .= "<tr>
    //                     <td><b>Primary Coordinator &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </b></td>
    //                     <td><b><a href='mailto:$email' target='_top'>$name </a>$pos</b></td>
    //                     </tr>";
    //                 } elseif ($i == 1) {
    //                     $str .= "<tr>
    //                     <td><b>Secondary Coordinator : </b></td>
    //                     <td><b><a href='mailto:$email' target='_top'>$name </a>$pos</b></td>
    //                     </tr>";
    //                 } elseif ($i >= 2) {
    //                     if ($i == 2) {
    //                         $str .= "<tr>
    //                         <td><b>Additional Coordinator : </b></td>
    //                         <td><b><a href='mailto:$email' target='_top'>$name </a>$pos</b></td>
    //                         </tr>";
    //                     } else {
    //                         $str .= "<tr>
    //                         <td></td>
    //                         <td><b><a href='mailto:$email' target='_top'>$name </a>$pos</b></td>
    //                         </tr>";
    //                     }
    //                 } else {
    //                     $str .= '<tr><td></td></tr>';
    //                 }

    //                 $i++;
    //             }
    //         }

    //         $str .= '</table>';
    //         echo $str;
    //     } else {
    //         echo 'No reporting data found for the given ID.';
    //     }
    // }


    /**
     * Load Conference Coordinators for Each Conference
     */
    // public function load_cc_coordinators($chConf, $chPcid)
    // {
    //     $reportingList = DB::table('coordinator_reporting_tree')
    //         ->select('*')
    //         ->where('id', '=', $chPcid)
    //         ->get();

    //     foreach ($reportingList as $key => $value) {
    //         $reportingList[$key] = (array) $value;
    //     }
    //     $filterReportingList = array_filter($reportingList[0]);
    //     unset($filterReportingList['id']);
    //     unset($filterReportingList['layer0']);
    //     $filterReportingList = array_reverse($filterReportingList);
    //     $str = '';
    //     $array_rows = count($filterReportingList);
    //     $i = 0;
    //     $coordinator_array = [];
    //     foreach ($filterReportingList as $key => $val) {
    //         $corList = DB::table('coordinators as cd')
    //             ->select('cd.id as cid', 'cd.first_name as fname', 'cd.last_name as lname', 'cp.long_title as pos', 'cd.email as email',
    //                 'cd.conference_id as conf', 'cf.conference_description as conf_desc', )
    //             ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
    //             ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
    //             ->where('cd.id', '=', $val)
    //             ->get();
    //         $coordinator_array[$i] = ['id' => $corList[0]->cid,
    //             'first_name' => $corList[0]->fname,
    //             'last_name' => $corList[0]->lname,
    //             'pos' => $corList[0]->pos,
    //             'conf' => $corList[0]->conf,
    //             'conf_desc' => $corList[0]->conf_desc,
    //             'email' => $corList[0]->email,
    //         ];

    //         $i++;
    //     }
    //     $coordinator_count = count($coordinator_array);

    //     for ($i = 0; $i < $coordinator_count; $i++) {
    //         $cc_fname = $coordinator_array[$i]['first_name'];
    //         $cc_lname = $coordinator_array[$i]['last_name'];
    //         $cc_pos = $coordinator_array[$i]['pos'];
    //         $cc_conf = $coordinator_array[$i]['conf'];
    //         $cc_conf_desc = $coordinator_array[$i]['conf_desc'];
    //         $cc_email = $coordinator_array[$i]['email'];
    //     }

    //     switch ($chConf) {
    //         case 1:   //Conference 1
    //             $cc_fname = $cc_fname;
    //             $cc_lname = $cc_lname;
    //             $cc_pos = $cc_pos;
    //             $cc_conf = $cc_conf;
    //             $cc_conf_desc = $cc_conf_desc;
    //             $cc_email = $cc_email;
    //             break;
    //         case 2:  //Conference 2
    //             $cc_fname = $cc_fname;
    //             $cc_lname = $cc_lname;
    //             $cc_pos = $cc_pos;
    //             $cc_conf = $cc_conf;
    //             $cc_conf_desc = $cc_conf_desc;
    //             $cc_email = $cc_email;
    //             break;
    //         case 3:  //Conference 3
    //             $cc_fname = $cc_fname;
    //             $cc_lname = $cc_lname;
    //             $cc_pos = $cc_pos;
    //             $cc_conf = $cc_conf;
    //             $cc_conf_desc = $cc_conf_desc;
    //             $cc_email = $cc_email;
    //             break;
    //         case 4:  //Conference 4
    //             $cc_fname = $cc_fname;
    //             $cc_lname = $cc_lname;
    //             $cc_pos = $cc_pos;
    //             $cc_conf = $cc_conf;
    //             $cc_conf_desc = $cc_conf_desc;
    //             $cc_email = $cc_email;
    //             break;
    //         case 5:  //Conference 5
    //             $cc_fname = $cc_fname;
    //             $cc_lname = $cc_lname;
    //             $cc_pos = $cc_pos;
    //             $cc_conf = $cc_conf;
    //             $cc_conf_desc = $cc_conf_desc;
    //             $cc_email = $cc_email;
    //             break;
    //     }

    //     return [
    //         'cc_fname' => $cc_fname,
    //         'cc_lname' => $cc_lname,
    //         'cc_pos' => $cc_pos,
    //         'cc_conf' => $cc_conf,
    //         'cc_conf_desc' => $cc_conf_desc,
    //         'cc_email' => $cc_email,
    //         // 'coordinator_array' => $coordinator_array,
    //     ];
    // }




    /**
     * get Coordinator Email -- Mail for full Coordinator Downline
     */
    // public function getCoordMail($pcid)
    // {
    //     $reportingList = DB::table('coordinator_reporting_tree')
    //         ->select('*')
    //         ->where('id', '=', $pcid)
    //         ->get();
    //     foreach ($reportingList as $key => $value) {
    //         $reportingList[$key] = (array) $value;
    //     }
    //     $filterReportingList = array_filter($reportingList[0]);
    //     unset($filterReportingList['id']);
    //     unset($filterReportingList['layer0']);
    //     $filterReportingList = array_reverse($filterReportingList);
    //     $str = '';
    //     $array_rows = count($filterReportingList);
    //     $down_line_email = [];
    //     foreach ($filterReportingList as $key => $val) {
    //         //if($corId != $val && $val >1){
    //         if ($val > 1) {
    //             $corList = DB::table('coordinators as cd')
    //                 ->select('cd.email as cord_email')
    //                 ->where('cd.id', '=', $val)
    //                 ->where('cd.is_active', '=', 1)
    //                 ->get();
    //             if (count($corList) > 0) {
    //                 if ($down_line_email == '') {
    //                     $down_line_email[] = $corList[0]->cord_email;
    //                 } else {
    //                     $down_line_email[] = $corList[0]->cord_email;
    //                 }
    //             }
    //         }
    //     }

    //     return $down_line_email;
    // }




}
