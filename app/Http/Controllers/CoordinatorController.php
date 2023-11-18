<?php

namespace App\Http\Controllers;

use App\Mail\CoordinatorRetireAdmin;
use App\Models\FinancialReport;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CoordinatorController extends Controller
{
    public function __construct()
    {
        $this->middleware('preventBackHistory');
        $this->middleware('auth')->except('logout');
    }

    /**
     * Coordiantor Listing
     */
    public function index(Request $request): View
    {
        //Get Coordinator Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $sqlLayerId = 'crt.layer'.$corlayerId;
        $positionId = $corDetails['position_id'];

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

        //Get Coordinator List mapped with login coordinator
        if ($positionId == 7) {
            $coordinatorList = DB::table('coordinator_details as cd')
                ->select('cd.coordinator_id as cor_id', 'cd.home_chapter as cor_chapter', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email',
                    'cd.phone as cor_phone', 'cd.report_id as report_id', 'cp.long_title as position',
                    DB::raw('(SELECT cp2.long_title FROM coordinator_position as cp2 WHERE cp2.id = cd.sec_position_id) as sec_pos'), // Subquery to get secondary position
                    'cd.conference_id as conf', 'cd.coordinator_start_date as coordinator_start_date', 'rg.short_name as reg', 'report.first_name as report_fname',
                    'report.last_name as report_lname')
                ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
                ->join('region as rg', 'rg.id', '=', 'cd.region_id')
                ->leftJoin('coordinator_details as report', 'report.coordinator_id', '=', 'cd.report_id')
                ->where('cd.is_active', '=', '1')
                ->whereIn('cd.report_id', $inQryArr)
                ->orderBy('cd.first_name')
                ->get();
        } elseif ($corId == 38) {
            $coordinatorList = DB::table('coordinator_details as cd')
                ->select('cd.coordinator_id as cor_id', 'cd.home_chapter as cor_chapter', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email',
                    'cd.phone as cor_phone', 'cd.report_id as report_id', 'cp.long_title as position',
                    DB::raw('(SELECT cp2.long_title FROM coordinator_position as cp2 WHERE cp2.id = cd.sec_position_id) as sec_pos'), // Subquery to get secondary position
                    'cd.conference_id as conf', 'cd.coordinator_start_date as coordinator_start_date', 'rg.short_name as reg', 'report.first_name as report_fname',
                    'report.last_name as report_lname')
                ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
                ->join('region as rg', 'rg.id', '=', 'cd.region_id')
                ->leftJoin('coordinator_details as report', 'report.coordinator_id', '=', 'cd.report_id')
                ->where('cd.is_active', '=', '1')
                ->where('cd.region_id', '=', '15')
                ->orderBy('cd.first_name')
                ->get();
        } else {
            $coordinatorList = DB::table('coordinator_details as cd')
                ->select('cd.coordinator_id as cor_id', 'cd.home_chapter as cor_chapter', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email',
                    'cd.phone as cor_phone', 'cd.report_id as report_id', 'cp.long_title as position',
                    DB::raw('(SELECT cp2.long_title FROM coordinator_position as cp2 WHERE cp2.id = cd.sec_position_id) as sec_pos'), // Subquery to get secondary position
                    'cd.conference_id as conf', 'cd.coordinator_start_date as coordinator_start_date', 'rg.short_name as reg', 'report.first_name as report_fname',
                    'report.last_name as report_lname')
                ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
                ->join('region as rg', 'rg.id', '=', 'cd.region_id')
                ->leftJoin('coordinator_details as report', 'report.coordinator_id', '=', 'cd.report_id')
                ->where('cd.is_active', '=', '1')
                ->where('cd.conference_id', '=', $corConfId)
                ->whereIn('cd.report_id', $inQryArr)
                ->orderBy('cd.first_name')
                ->get();
        }

        //Get the e-mail addresses for all the listed coordinators
        $emailListCord = '';
        $row_count = count($coordinatorList);
        for ($row = 0; $row < $row_count; $row++) {
            $email = $coordinatorList[$row]->cor_email;
            $escaped_email = str_replace("'", "\\'", $email);

            if ($emailListCord == '') {
                $emailListCord = $escaped_email;
            } else {
                $emailListCord .= ';'.$escaped_email;
            }
        }

        if (isset($_GET['check'])) {
            if ($_GET['check'] == 'yes') {
                $checkBoxStatus = 'checked';
                //Get Coordinator List mapped with login coordinator
                $coordinatorList = DB::table('coordinator_details as cd')
                    ->select('cd.coordinator_id as cor_id', 'cd.home_chapter as cor_chapter', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email',
                        'cd.phone as cor_phone', 'cd.report_id as report_id', 'cp.long_title as position',
                        DB::raw('(SELECT cp2.long_title FROM coordinator_position as cp2 WHERE cp2.id = cd.sec_position_id) as sec_pos'), // Subquery to get secondary position
                        'cd.conference_id as conf', 'cd.coordinator_start_date as coordinator_start_date', 'rg.short_name as reg', 'report.first_name as report_fname',
                        'report.last_name as report_lname')
                    ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
                    ->join('region as rg', 'rg.id', '=', 'cd.region_id')
                    ->leftJoin('coordinator_details as report', 'report.coordinator_id', '=', 'cd.report_id')
                    ->where('cd.is_active', '=', '1')
                    ->where('cd.report_id', $corId)
                    ->orderBy('cd.first_name')
                    ->get();
                //Get the e-mail addresses for all the listed coordinators
                $emailListCord = '';
                $row_count = count($coordinatorList);
                for ($row = 0; $row < $row_count; $row++) {
                    $email = $coordinatorList[$row]->cor_email;
                    $escaped_email = str_replace("'", "\\'", $email);

                    if ($emailListCord == '') {
                        $emailListCord = $escaped_email;
                    } else {
                        $emailListCord .= ';'.$escaped_email;
                    }
                }
            }
        } else {
            $checkBoxStatus = '';
        }
        $countList = count($coordinatorList);
        $data = ['countList' => $countList, 'corId' => $corId, 'coordinatorList' => $coordinatorList, 'checkBoxStatus' => $checkBoxStatus, 'emailListCord' => $emailListCord];

        return view('coordinators.index')->with($data);
    }

    /**
     * Coordiantor New Create
     */
    public function create(Request $request): View
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
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

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];

        $data = ['regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('coordinators.create')->with($data);
    }

    /**
     * Create New Coordiantor (store)
     */
    public function store(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corlayerId = $corDetails['layer_id'];
        $corRegId = $corDetails['region_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $new_layer_id = $corlayerId + 1;
        $input = $request->all();

        DB::beginTransaction();
        try {
            $userId = DB::table('users')->insertGetId(
                ['first_name' => $input['cord_fname'],
                    'last_name' => $input['cord_lname'],
                    'email' => $input['cord_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'coordinator',
                    'is_active' => 1]
            );

            $cordIdArr = DB::table('coordinator_details')
                ->select('coordinator_details.coordinator_id')
                ->orderByDesc('coordinator_details.coordinator_id')
                ->limit(1)
                ->get();
            $cordId = $cordIdArr[0]->coordinator_id + 1;

            $coord = DB::table('coordinator_details')->insert(
                ['user_id' => $userId,
                    'coordinator_id' => $cordId,
                    'conference_id' => $corConfId,
                    'region_id' => $corRegId,
                    'layer_id' => $new_layer_id,
                    'first_name' => $input['cord_fname'],
                    'last_name' => $input['cord_lname'],
                    'position_id' => 1,
                    'email' => $input['cord_email'],
                    'sec_email' => $input['sec_email'],
                    'report_id' => $corId,
                    'address' => $input['cord_addr'],
                    'city' => $input['cord_city'],
                    'state' => $input['cord_state'],
                    'zip' => $input['cord_zip'],
                    'country' => 'USA',
                    'phone' => $input['cord_phone'],
                    'alt_phone' => $input['cord_altphone'],
                    'birthday_month_id' => $input['cord_month'],
                    'birthday_day' => $input['cord_day'],
                    'coordinator_start_date' => date('Y-m-d H:i:s'),
                    'password' => Hash::make('TempPass4You'),
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1]
            );

            $cordReportingTree = DB::table('coordinator_reporting_tree')
                ->select('layer0', 'layer1', 'layer2', 'layer3', 'layer4', 'layer5', 'layer6', 'layer7', 'layer8')
                ->where('id', '=', $corId)
                ->limit(1)
                ->get();
            $layer0 = $cordReportingTree[0]->layer0;
            $layer1 = $cordReportingTree[0]->layer1;
            $layer2 = $cordReportingTree[0]->layer2;
            $layer3 = $cordReportingTree[0]->layer3;
            $layer4 = $cordReportingTree[0]->layer4;
            $layer5 = $cordReportingTree[0]->layer5;
            $layer6 = $cordReportingTree[0]->layer6;
            $layer7 = $cordReportingTree[0]->layer7;
            $layer8 = $cordReportingTree[0]->layer8;
            $coordinator_id = $cordId;
            switch ($new_layer_id) {
                case 0:
                    $layer0 = $coordinator_id;
                    $layer1 = null;
                    $layer2 = null;
                    $layer3 = null;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 1:
                    $layer1 = $coordinator_id;
                    $layer2 = null;
                    $layer3 = null;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 2:
                    $layer2 = $coordinator_id;
                    $layer3 = null;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 3:
                    $layer3 = $coordinator_id;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 4:
                    $layer4 = $coordinator_id;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 5:
                    $layer5 = $coordinator_id;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 6:
                    $layer6 = $coordinator_id;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 7:
                    $layer7 = $coordinator_id;
                    $layer8 = null;
                    break;
                case 7:
                    $layer8 = $coordinator_id;
                    break;
            }
            $coord = DB::table('coordinator_reporting_tree')->insert(
                [
                    'layer0' => $layer0,
                    'layer1' => $layer1,
                    'layer2' => $layer2,
                    'layer3' => $layer3,
                    'layer4' => $layer4,
                    'layer5' => $layer5,
                    'layer6' => $layer6,
                    'layer7' => $layer7,
                    'layer8' => $layer8,
                ]
            );
            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            echo $e->getMessage();
            exit;
            // Log the error
            Log::error($e);

            return redirect()->to('/coordinatorlist')->with('fail', 'Something went wrong, Please try again..');
        }

        return redirect()->to('/coordinatorlist')->with('success', 'Coordinator created successfully.');
    }

    /**
     * Edit Coordiantor
     */
    public function edit(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $id)
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
            ->orderBy('long_name')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->orderBy('conference_name')
            ->get();
        $positionList = DB::table('coordinator_position')
            ->select('id', 'long_title')
            ->orderBy('long_title')
            ->get();

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();
        $directReportTo = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.report_id', '=', $id)
            ->where('cd.is_active', '=', '1')
            ->get();

        $directChapterTo = DB::table('chapters as ch')
            ->select('ch.id as ch_id', 'ch.name as ch_name', 'st.state_short_name as st_name')
            ->join('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.primary_coordinator_id', '=', $id)
            ->where('ch.is_active', '=', '1')
            ->get();
        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG',
            '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $coordinatorDetails[0]->birthday_month_id;

        $data = ['directChapterTo' => $directChapterTo, 'directReportTo' => $directReportTo, 'primaryCoordinatorList' => $primaryCoordinatorList,
            'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails,
            'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth, 'cor_id' => $corId];

        return view('coordinators.edit')->with($data);
    }

    /**
     * Update Coordinator Email (store)
     */
    public function update2(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $name = $corDetails['first_name'];
        $cordinatorId = $id;
        if ($request->get('cord_email') != $request->get('cord_email_chk')) {
            DB::table('coordinator_details')
                ->where('coordinator_id', $cordinatorId)
                ->update(['email' => $request->get('cord_email')]);
        }

        return redirect()->to('/coordinatorlist')->with('success', 'Coordinator email updated successfully.');
    }

    /**
     * Update Coordinator (store)
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $cordinatorId = $id;

        if ($request->get('cord_fname') != '' && $request->get('cord_lname') != '' && $request->get('cord_email') != '') {
            $corDetails = DB::table('coordinator_details')
                ->select('coordinator_id', 'user_id')
                ->where('coordinator_id', '=', $cordinatorId)
                ->get();
            if (count($corDetails) != 0) {
                $userId = $corDetails[0]->user_id;
                $cordId = $corDetails[0]->coordinator_id;

                $user = User::find($userId);
                $user->first_name = $request->get('cord_fname');
                $user->last_name = $request->get('cord_lname');
                $user->email = $request->get('cord_email');
                $user->updated_at = date('Y-m-d H:i:s');
                $user->save();

                DB::table('coordinator_details')
                    ->where('coordinator_id', $cordinatorId)
                    ->update(['first_name' => $request->get('cord_fname'),
                        'last_name' => $request->get('cord_lname'),
                        'email' => $request->get('cord_email'),
                        'sec_email' => $request->get('cord_sec_email'),
                        'address' => $request->get('cord_addr'),
                        'city' => $request->get('cord_city'),
                        'state' => $request->get('cord_state'),
                        'zip' => $request->get('cord_zip'),
                        'country' => 'USA',
                        'phone' => $request->get('cord_phone'),
                        'alt_phone' => $request->get('cord_altphone'),
                        'birthday_month_id' => $request->get('cord_month'),
                        'birthday_day' => $request->get('cord_day'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s')]);
            }
        }

        return redirect()->to('/coordinatorlist')->with('success', 'Coordinator updated successfully.');
    }

    /**
     * Change Coordinator Role
     */
    public function showChangeRole(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corPosId = $corDetails['position_id'];
        $corRegId = $corDetails['region_id'];
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $id)
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
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->where('id', '>=', 0)
            ->orderBy('conference_name')
            ->get();
        $positionList = DB::table('coordinator_position')
            ->select('id', 'long_title')
            ->orderBy('id')
            ->get();

        $conference_id = $coordinatorDetails[0]->conference_id;
        $position_id = $coordinatorDetails[0]->position_id;
        $region_id = $coordinatorDetails[0]->region_id;

        /***Query For Report To in Frst Section */
        if ($region_id > 0 && $position_id < 6) {
            $primaryCoordinatorList = DB::table('coordinator_details as cd')
                ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                ->where(function ($query) use ($conference_id, $position_id, $region_id) {
                    $query->where('cd.conference_id', $conference_id)
                        ->where('cd.position_id', '>', $position_id)
                        ->where('cd.position_id', '>', 1)
                        ->where('cd.region_id', $region_id)
                        ->where('cd.is_active', 1);
                })
                ->orWhere(function ($query) use ($conference_id) {
                    $query->where('cd.position_id', 6)
                        ->where('cd.conference_id', $conference_id)
                        ->where('cd.is_active', 1);
                })
                ->orWhere(function ($query) use ($conference_id) {
                    $query->where('cd.position_id', 25)
                        ->where('cd.conference_id', $conference_id)
                        ->where('cd.is_active', 1);
                })
                ->orderBy('cd.position_id')
                ->orderBy('cd.first_name')
                ->orderBy('cd.last_name')
                ->get();

        } elseif ($conference_id > 0) {
            $primaryCoordinatorList = DB::table('coordinator_details as cd')
                ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                ->where('cd.position_id', '>', 1)
                ->where('cd.conference_id', $conference_id)
                ->where('cd.is_active', 1)
                ->orderBy('cd.position_id')
                ->orderBy('cd.first_name')
                ->orderBy('cd.last_name')
                ->get();

        } else {
            $primaryCoordinatorList = DB::table('coordinator_details as cd')
                ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                ->where('cd.is_active', 1)
                ->orderBy('cd.position_id')
                ->orderBy('cd.first_name')
                ->orderBy('cd.last_name')
                ->get();

        }

        if ($region_id > 0) {
            $directReportTo = DB::table('coordinator_details as cd')
                ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                ->where('cd.region_id', $region_id)
                ->where('cd.position_id', '<', $position_id)
                ->where('cd.is_active', 1)
                ->orderBy('cd.first_name')
                ->orderBy('cd.last_name')
                ->get();

        } elseif ($conference_id > 0) {
            $directReportTo = DB::table('coordinator_details as cd')
                ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                ->where('cd.conference_id', $conference_id)
                ->where('cd.is_active', 1)
                ->orderBy('cd.position_id')
                ->orderBy('cd.first_name')
                ->orderBy('cd.last_name')
                ->get();

        } else {
            $directReportTo = DB::table('coordinator_details as cd')
                ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                ->where('cd.is_active', 1)
                ->orderBy('cd.position_id')
                ->orderBy('cd.first_name')
                ->orderBy('cd.last_name')
                ->get();

        }

        if ($region_id == 0) {
            $primaryChapterList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'st.state_short_name as state')
                ->join('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.conference', '=', $conference_id)
                ->where('chapters.is_active', '=', '1')
                ->orderBy('st.state_short_name')
                ->get();
        } else {
            $primaryChapterList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'st.state_short_name as state')
                ->join('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.region', '=', $region_id)
                ->where('chapters.is_active', '=', '1')
                ->orderBy('st.state_short_name')
                ->orderBy('chapters.name')
                ->get();
        }

        $directReportToHTML = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->join('region as rg', 'cd.region_id', '=', 'rg.id')
            ->where('cd.report_id', '=', $id)
            ->where('cd.is_active', '=', '1')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $coordinatorDetails[0]->birthday_month_id;
        if ($coordinatorDetails[0]->last_promoted == '0000-00-00') {
            $lastPromoted = null;
        } else {
            $lastPromoted = $coordinatorDetails[0]->last_promoted;
        }
        $data = ['lastPromoted' => $lastPromoted, 'directReportToHTML' => $directReportToHTML, 'primaryChapterList' => $primaryChapterList, 'directReportTo' => $directReportTo, 'primaryCoordinatorList' => $primaryCoordinatorList, 'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('coordinators.role')->with($data);

    }

    /**
     * Unretire Coordinator
     */
    public function showUnretiredCoordinator($id): RedirectResponse
    {
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.coordinator_id', '=', $id)
            ->get();
        if (! empty($coordinatorDetails[0]->user_id)) {
            DB::table('coordinator_details')
                ->where('coordinator_id', $id)
                ->update(['reason_retired' => '',
                    'zapped_date' => date('Y-m-d'),
                    'is_active' => 1,
                    'last_updated_by' => date('Y-m-d H:i:s'),
                    'last_updated_date' => date('Y-m-d H:i:s')]);
            DB::update('UPDATE users SET is_active = ? where id = ?', [1, $coordinatorDetails[0]->user_id]);

            DB::commit();
        }

        return redirect()->to('/coordinatorlist')->with('success', 'Coordinator successfully unretired');
    }

    /**
     * Update Coordiantor Role
     * Including LOA & Retiring Cooardinator
     */
    public function updateRole(Request $request, $id)
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $positionId = $corDetails['position_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $cordinatorId = $id;
        $onleave = false;
        $leavedate = null;
        $stat = $corDetails['state'];
        $submit_type = $_POST['submit_type'];

        if ($submit_type == 'Leave') {
            $onleave = true;
            $leavedate = date('Y-m-d');
        }
        if ($submit_type == 'Retire') {
            $reason = $_POST['RetireReason'];
            $userid = $_POST['userid'];
            $coordName = $_POST['coordName'];
            $coordConf = $_POST['coordConf'];
            $email = $_POST['email'];
            DB::beginTransaction();
            try {
                DB::table('coordinator_details')
                    ->where('coordinator_id', $cordinatorId)
                    ->update(['reason_retired' => $reason,
                        'zapped_date' => date('Y-m-d'),
                        'is_active' => 0,
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s')]);
                DB::update('UPDATE users SET is_active = ? where id = ?', [0, $userid]);
                DB::commit();
                $webmstremail = DB::table('coordinator_details')
                    ->select('email')
                    ->where('conference_id', $coordConf)
                    ->where('position_id', 13)
                    ->where('is_active', 1)
                    ->get();
                $mailData = [
                    'coordName' => $coordName,
                    'confNumber' => $coordConf,
                    'email' => $email,
                ];

                $to_email = 'jackie.mchenry@momsclub.org';

                Mail::to($to_email, 'MOMS Club')
                    ->send(new CoordinatorRetireAdmin($mailData));

                return redirect()->to('/coordinator/retired')->with('success', 'Coordinator retired successfully.');
                exit;
            } catch (\Exception $e) {
                // Rollback Transaction
                DB::rollback();
                // Log the error
                Log::error($e);

                return redirect()->to('/coordinatorlist')->with('fail', 'Something went wrong, Please try again.');
            }
        }

        if ($submit_type == 'Leave' || $submit_type == 'RemoveLeave') {
            DB::beginTransaction();
            try {
                DB::table('coordinator_details')
                    ->where('coordinator_id', $cordinatorId)
                    ->update(['on_leave' => $onleave,
                        'leave_date' => $leavedate,
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s')]);

                DB::commit();
                if ($submit_type == 'Leave') {
                    return redirect()->back()->with('success', 'Coordinator has been successfully put on Leave');
                } else {
                    return redirect()->back()->with('success', 'Coordinator has been successfully remove on Leave');
                }
                exit;
                // return true;
            } catch (\Exception $e) {
                // Rollback Transaction
                DB::rollback();

                return false;
            }
        }

        ///Reassign Direct Report Coordinators that Changed
        $rowcountCord = $_POST['CoordinatorCount'];
        for ($i = 0; $i < $rowcountCord; $i++) {
            $new_coordinator_field = 'Report'.$i;
            $new_coordinator_id = $_POST[$new_coordinator_field];

            $coordinator_field = 'CoordinatorIDRow'.$i;
            $coordinator_id = $_POST[$coordinator_field];

            $this->ReassignCoordinator($request, $coordinator_id, $new_coordinator_id, true);
        }

        //Reassign Primary Coordinatory Chapters that Changed
        $rowcountChapter = $_POST['ChapterCount'];
        for ($i = 0; $i < $rowcountChapter; $i++) {
            $coordinator_field = 'PCID'.$i;
            $coordinator_id = $_POST[$coordinator_field];

            $chapter_field = 'ChapterIDRow'.$i;
            $chapter_id = $_POST[$chapter_field];

            $this->ReassignChapter($request, $chapter_id, $coordinator_id, true);
        }

        if ($rowcountCord == 0 && $rowcountChapter == 0) {

        }

        //Reassign Report To / Direct Supervisor that Changed
        $coordinator_id = $request->get('coordinator_id');
        $new_coordinator_id = $request->get('cord_report_pc');
        $this->ReassignCoordinator($request, $coordinator_id, $new_coordinator_id, true);

        //Save other changes
        $position_id = $request->get('cord_pri_pos');
        $sec_position_id = $request->get('cord_sec_pos');
        $old_position_id = $request->get('OldPrimaryPosition');
        $old_sec_position_id = $request->get('OldSecPosition');
        $promote_date = $request->get('CoordinatorPromoteDate');
        //$report_id = $request->get('cord_report');
        if ($promote_date == '0000-00-00') {
            $promote_date = null;
        }
        if ($position_id != $old_position_id || $sec_position_id != $old_sec_position_id) {
            $promote_date = $request->get('CoordinatorPromoteDateNew');
        }
        DB::beginTransaction();
        try {
            DB::table('coordinator_details')
                ->where('coordinator_id', $cordinatorId)
                ->update(['position_id' => $request->get('cord_pri_pos'),
                    'sec_position_id' => $request->get('cord_sec_pos'),
                    'region_id' => $request->get('cord_region'),
                    'conference_id' => $request->get('cord_conf'),
                    'home_chapter' => $request->get('cord_chapter'),
                    //'report_id' => $report_id,
                    'last_promoted' => $promote_date,
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s')]);
            DB::commit();

            return redirect()->back()->with('success', 'Coordinator Role has been changed successfully.');
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }
    }

    /**
     * Reassign Chapter
     */
    public function ReassignChapter(Request $request, $chapter_id, $coordinator_id, $check_changed = false)
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        if ($check_changed) {
            $checkPrimaryIdArr = DB::table('chapters as ch')
                ->select('ch.primary_coordinator_id as chpid')
                ->where('ch.id', '=', $chapter_id)
                ->where('ch.is_active', '=', '1')
                ->get();
            $current_primary = $checkPrimaryIdArr[0]->chpid;
            if ($current_primary == $coordinator_id) {
                return true;
            }
        }
        DB::beginTransaction();
        try {
            DB::table('chapters')
                ->where('id', $chapter_id)
                ->update(['primary_coordinator_id' => $coordinator_id,
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s')]);

            $financial_report_array = FinancialReport::find($chapter_id);
            if (! empty($financial_report_array)) {
                DB::update('UPDATE financial_report SET reviewer_id = ? where chapter_id = ?', [null, $chapter_id]);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            return false;
        }

    }

    /**
     * Reassign Coordinator
     */
    public function ReassignCoordinator(Request $request, $coordinator_id, $new_coordinator_id, $check_changed = false)
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        if ($check_changed) {
            $checkReportIdArr = DB::table('coordinator_details as cd')
                ->select('cd.report_id as repid')
                ->where('cd.coordinator_id', '=', $coordinator_id)
                ->where('cd.is_active', '=', '1')
                ->get();
            $current_report = $checkReportIdArr[0]->repid;
            if ($current_report == $new_coordinator_id) {
                return true;
            }
        }
        DB::beginTransaction();
        try {
            //Find new layer
            $query = $layerId = DB::table('coordinator_details')
                ->select('layer_id')
                ->where('coordinator_id', $new_coordinator_id)
                ->limit(1)
                ->get();
            $new_layer_id = $query[0]->layer_id + 1;

            //Update their main report ID & layer
            DB::table('coordinator_details')
                ->where('coordinator_id', $coordinator_id)
                ->update(['report_id' => $new_coordinator_id,
                    'layer_id' => $new_layer_id,
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s')]);

            //Update the coordinator tree with their new tree relationship
            //Get the current report array
            $cordReportingTree = DB::table('coordinator_reporting_tree')
                ->select('layer0', 'layer1', 'layer2', 'layer3', 'layer4', 'layer5', 'layer6', 'layer7', 'layer8')
                ->where('id', '=', $new_coordinator_id)
                ->limit(1)
                ->get();
            $layer0 = $cordReportingTree[0]->layer0;
            $layer1 = $cordReportingTree[0]->layer1;
            $layer2 = $cordReportingTree[0]->layer2;
            $layer3 = $cordReportingTree[0]->layer3;
            $layer4 = $cordReportingTree[0]->layer4;
            $layer5 = $cordReportingTree[0]->layer5;
            $layer6 = $cordReportingTree[0]->layer6;
            $layer7 = $cordReportingTree[0]->layer7;
            $layer8 = $cordReportingTree[0]->layer8;
            switch ($new_layer_id) {
                case 0:
                    $layer0 = $coordinator_id;
                    $layer1 = null;
                    $layer2 = null;
                    $layer3 = null;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 1:
                    $layer1 = $coordinator_id;
                    $layer2 = null;
                    $layer3 = null;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 2:
                    $layer2 = $coordinator_id;
                    $layer3 = null;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 3:
                    $layer3 = $coordinator_id;
                    $layer4 = null;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 4:
                    $layer4 = $coordinator_id;
                    $layer5 = null;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 5:
                    $layer5 = $coordinator_id;
                    $layer6 = null;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 6:
                    $layer6 = $coordinator_id;
                    $layer7 = null;
                    $layer8 = null;
                    break;
                case 7:
                    $layer7 = $coordinator_id;
                    $layer8 = null;
                    break;
                case 7:
                    $layer8 = $coordinator_id;
                    break;
            }
            DB::table('coordinator_reporting_tree')
                ->where('id', $coordinator_id)
                ->update(['layer0' => $layer0,
                    'layer1' => $layer1,
                    'layer2' => $layer2,
                    'layer3' => $layer3,
                    'layer4' => $layer4,
                    'layer5' => $layer5,
                    'layer6' => $layer6,
                    'layer7' => $layer7,
                    'layer8' => $layer8,
                ]);
            DB::commit();

            return true;
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            return false;
        }
    }

    /**
     * Get Region List
     */
    public function getRegionList($corConfId): JsonResponse
    {
        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $corConfId)
            ->orderBy('long_name')
            ->get();
        $html = '<option value="">Select Region</option><option value="0">None</option>';
        foreach ($regionList as $list) {
            $html .= '<option value="'.$list->id.'">'.$list->long_name.'</option>';
        }

        return response()->json(['html' => $html]);

    }

    /**
     * Get Reporting List
     */
    public function getReportingList(): JsonResponse
    {
        $conference_id = $_GET['conf_id'];
        $position_id = $_GET['pos_id'];
        $region_id = $_GET['reg_id'];

        if ($region_id > 0 && $position_id < 6) {
            $reportCoordinatorList = DB::select(DB::raw("SELECT cd.coordinator_id as cid,cd.first_name as cor_f_name,cd.last_name as cor_l_name,cp.short_title as pos
                                        FROM coordinator_details as cd
                                        INNER JOIN coordinator_position as cp ON cd.position_id=cp.id
                                        WHERE (cd.conference_id = $conference_id AND cd.position_id > $position_id AND cd.position_id > 1 AND cd.region_id = $region_id AND cd.is_active=1)
                                        OR (cd.position_id = 6 AND cd.conference_id = $conference_id AND cd.is_active=1)
                                        OR (cd.position_id = 25 AND cd.conference_id = $conference_id AND cd.is_active=1)
                                        ORDER BY cd.position_id,cd.first_name, cd.last_name"));
        } elseif ($conference_id > 0) {
            $reportCoordinatorList = DB::select(DB::raw("SELECT cd.coordinator_id as cid,cd.first_name as cor_f_name,cd.last_name as cor_l_name,cp.short_title as pos
                                        FROM coordinator_details as cd
                                        INNER JOIN coordinator_position as cp ON cd.position_id=cp.id
                                        WHERE (cd.position_id > 1 AND cd.conference_id = $conference_id AND cd.is_active=1)
                                        ORDER BY cd.position_id,cd.first_name, cd.last_name"));
        } else {
            $reportCoordinatorList = DB::select(DB::raw('SELECT cd.coordinator_id as cid,cd.first_name as cor_f_name,cd.last_name as cor_l_name,cp.short_title as pos
                                        FROM coordinator_details as cd
                                        INNER JOIN coordinator_position as cp ON cd.position_id=cp.id
                                        WHERE cd.is_active=1
                                        ORDER BY cd.position_id,cd.first_name, cd.last_name'));
        }
        $html = '<option value=""></option>';
        foreach ($reportCoordinatorList as $list) {
            $html .= '<option value="'.$list->cid.'">'.$list->cor_f_name.' '.$list->cor_l_name.' ('.$list->pos.')</option>';
        }

        return response()->json(['html' => $html]);
    }

    /**
     * Get Direct Report
     */
    public function getDirectReportingList(): JsonResponse
    {
        $conference_id = $_GET['conf_id'];
        $position_id = $_GET['pos_id'];
        $region_id = $_GET['reg_id'];

        if ($region_id > 0) {
            $directReportTo = DB::select(DB::raw("SELECT cd.coordinator_id as cid,cd.first_name as cor_f_name,cd.last_name as cor_l_name,cp.short_title as pos
                                FROM coordinator_details as cd
                                INNER JOIN coordinator_position as cp ON cd.position_id=cp.id
                                WHERE (cd.region_id = $region_id AND cd.position_id < $position_id AND cd.is_active=1)
                                ORDER BY cd.first_name, cd.last_name"));
        } elseif ($conference_id > 0) {
            $directReportTo = DB::select(DB::raw("SELECT cd.coordinator_id as cid,cd.first_name as cor_f_name,cd.last_name as cor_l_name,cp.short_title as pos
                                FROM coordinator_details as cd
                                INNER JOIN coordinator_position as cp ON cd.position_id=cp.id
                                WHERE (cd.conference_id = $conference_id AND cd.is_active=1)
                                ORDER BY cd.position_id,cd.first_name, cd.last_name"));
        } else {
            $directReportTo = DB::select(DB::raw('SELECT cd.coordinator_id as cid,cd.first_name as cor_f_name,cd.last_name as cor_l_name,cp.short_title as pos
                                FROM coordinator_details as cd
                                INNER JOIN coordinator_position as cp ON cd.position_id=cp.id
                                WHERE cd.is_active=1
                                ORDER BY cd.position_id,cd.first_name, cd.last_name'));
        }

        $html = '<option value=""></option>';
        foreach ($directReportTo as $list) {
            $html .= '<option value="'.$list->cid.'">'.$list->cor_f_name.' '.$list->cor_l_name.' ('.$list->pos.')</option>';
        }

        return response()->json(['html' => $html]);
    }

    /**
     * Get Primary Coordinator
     */
    public function getChapterPrimaryFor(): JsonResponse
    {
        $conference_id = $_GET['conf_id'];
        $position_id = $_GET['pos_id'];
        $region_id = $_GET['reg_id'];

        if ($region_id == 0) {
            $primaryChapterList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'st.state_short_name as state')
                ->join('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.conference', '=', $conference_id)
                ->where('chapters.is_active', '=', '1')
                ->orderBy('st.state_short_name')
                ->get();
        } else {
            $primaryChapterList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'st.state_short_name as state')
                ->join('state as st', 'chapters.state', '=', 'st.id')
                ->where('chapters.region', '=', $region_id)
                ->where('chapters.is_active', '=', '1')
                ->orderBy('st.state_short_name', 'chapters.name', 'ASC')
                ->get();
        }

        $html = '<option value=""></option>';
        foreach ($primaryChapterList as $list) {
            $html .= '<option value="'.$list->id.'">'.$list->state.' - '.$list->chapter_name.'</option>';
        }

        return response()->json(['html' => $html]);
    }

    /**
     * Retired Coorinators
     */
    public function showRetiredCoordinator(Request $request): View
    {
        //Get Coordinator Details
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $positionId = $corDetails['position_id'];

        if ($positionId == 7 || $positionId == 13) {
            $retiredCoordinatorList = DB::table('coordinator_details as cd')
                ->select('cd.coordinator_id as cor_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.reason_retired as cor_reason', 'cd.zapped_date as cor_zapdate', 'cp.long_title as position')
                ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
                ->where('cd.is_active', '=', '0')
                ->orderByDesc('cd.zapped_date')
                ->get();
        } else {
            $retiredCoordinatorList = DB::table('coordinator_details as cd')
                ->select('cd.coordinator_id as cor_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.reason_retired as cor_reason', 'cd.zapped_date as cor_zapdate', 'cp.long_title as position')
                ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
                ->where('cd.is_active', '=', '0')
                ->where('cd.conference_id', $corConfId)
                ->orderByDesc('cd.zapped_date')
                ->get();
        }
        //Get Coordinator List mapped with login coordinator
        $data = ['retiredCoordinatorList' => $retiredCoordinatorList];

        return view('coordinators.retired')->with($data);
    }

    /**
     * Retired Coordinator Details
     */
    public function showRetiredCoordinatorView(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.coordinator_id', '=', $id)
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
            ->orderBy('long_name')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->orderBy('conference_name')
            ->get();
        $positionList = DB::table('coordinator_position')
            ->select('id', 'long_title')
            ->orderBy('long_title')
            ->get();

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();
        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $coordinatorDetails[0]->birthday_month_id;

        $data = ['primaryCoordinatorList' => $primaryCoordinatorList, 'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('coordinators.retiredview')->with($data);
    }

    /**
     * International Coordinators List
     */
    public function showIntCoordinator(): View
    {
        //Get International Coordinator List
        $intCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cor_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.conference_id as cor_cid',
                'rg.short_name as reg_name', 'cp.long_title as position',
                DB::raw('(SELECT cp2.long_title FROM coordinator_position as cp2 WHERE cp2.id = cd.sec_position_id) as sec_pos'), // Subquery to get secondary position
            )
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();
        $data = ['intCoordinatorList' => $intCoordinatorList];

        return view('coordinators.international')->with($data);
    }

    /**
     * International Coordinators Detail
     */
    public function showIntCoordinatorView(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $id)
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
            ->orderBy('long_name')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->orderBy('conference_name')
            ->get();
        $positionList = DB::table('coordinator_position')
            ->select('id', 'long_title')
            ->orderBy('long_title')
            ->get();

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();
        $directReportTo = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.report_id', '=', $id)
            ->where('cd.is_active', '=', '1')
            ->get();

        $directChapterTo = DB::table('chapters as ch')
            ->select('ch.id as ch_id', 'ch.name as ch_name', 'st.state_short_name as st_name')
            ->join('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.primary_coordinator_id', '=', $id)
            ->where('ch.is_active', '=', '1')
            ->get();
        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG',
            '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $coordinatorDetails[0]->birthday_month_id;

        $data = ['directChapterTo' => $directChapterTo, 'directReportTo' => $directReportTo, 'primaryCoordinatorList' => $primaryCoordinatorList,
            'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails,
            'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth, 'cor_id' => $corId];

        return view('coordinators.interview')->with($data);
    }

    /**
     * International Retired Coordinator List
     */
    public function showIntRetCoordinator(): View
    {
        //Get International Coordinator List
        $intCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cor_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'cd.conference_id as cor_cid', 'rg.short_name as reg_name', 'cp.long_title as position', 'cd.sec_position_id as sec_position_id', 'cd.zapped_date as zapdate', 'cd.reason_retired as reason')
            ->join('coordinator_position as cp', 'cp.id', '=', 'cd.position_id')
            ->join('region as rg', 'rg.id', '=', 'cd.region_id')
            ->where('cd.is_active', '=', '0')
            ->orderByDesc('cd.zapped_date')
            ->get();

        $data = ['intCoordinatorList' => $intCoordinatorList];

        return view('coordinators.retiredinternational')->with($data);
    }

    /**
     * International Retired Coordinator Details
     */
    public function showIntRetCoordinatorView(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.coordinator_id', '=', $id)
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
            ->orderBy('long_name')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->orderBy('conference_name')
            ->get();
        $positionList = DB::table('coordinator_position')
            ->select('id', 'long_title')
            ->orderBy('long_title')
            ->get();

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();
        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $coordinatorDetails[0]->birthday_month_id;

        $data = ['primaryCoordinatorList' => $primaryCoordinatorList, 'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('coordinators.retinterview')->with($data);
    }

    /**
     * Coordiantor Appreciation Gifts
     */
    public function appreciation(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $id)
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
            ->orderBy('long_name')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->orderBy('conference_name')
            ->get();
        $positionList = DB::table('coordinator_position')
            ->select('id', 'long_title')
            ->orderBy('long_title')
            ->get();

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();
        $directReportTo = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.report_id', '=', $id)
            ->where('cd.is_active', '=', '1')
            ->get();

        $directChapterTo = DB::table('chapters as ch')
            ->select('ch.id as ch_id', 'ch.name as ch_name', 'st.state_short_name as st_name')
            ->join('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.primary_coordinator_id', '=', $id)
            ->where('ch.is_active', '=', '1')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $coordinatorDetails[0]->birthday_month_id;

        $data = ['directChapterTo' => $directChapterTo, 'directReportTo' => $directReportTo, 'primaryCoordinatorList' => $primaryCoordinatorList, 'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('coordinators.appreciation')->with($data);
    }

    /**
     * Update Coordiantor Appreciation Gifts (store)
     */
    public function updateAppreciation(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $cordinatorId = $id;

        if ($request->get('cord_fname') != '' && $request->get('cord_lname') != '') {
            $corDetails = DB::table('coordinator_details')
                ->select('coordinator_id', 'user_id')
                ->where('coordinator_id', '=', $cordinatorId)
                ->get();
            if (count($corDetails) != 0) {
                try {
                    $userId = $corDetails[0]->user_id;
                    $cordId = $corDetails[0]->coordinator_id;

                    $user = User::find($userId);
                    $user->first_name = $request->get('cord_fname');
                    $user->last_name = $request->get('cord_lname');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    DB::table('coordinator_details')
                        ->where('coordinator_id', $cordinatorId)
                        ->update(['recognition_toptier' => $request->get('cord_toptier'),
                            'recognition_year0' => $request->get('cord_year0'),
                            'recognition_year1' => $request->get('cord_year1'),
                            'recognition_year2' => $request->get('cord_year2'),
                            'recognition_year3' => $request->get('cord_year3'),
                            'recognition_year4' => $request->get('cord_year4'),
                            'recognition_year5' => $request->get('cord_year5'),
                            'recognition_year6' => $request->get('cord_year6'),
                            'recognition_year7' => $request->get('cord_year7'),
                            'recognition_year8' => $request->get('cord_year8'),
                            'recognition_year9' => $request->get('cord_year9'),
                            'recognition_necklace' => (int) $request->has('cord_necklace'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                    DB::commit();
                } catch (\Exception $e) {
                    // Rollback Transaction
                    DB::rollback();
                    // Log the error
                    Log::error($e);

                    return redirect()->to('/reports/appreciation')->with('fail', 'Something went wrong, Please try again.');
                }
            }
        }

        return redirect()->to('/reports/appreciation')->with('success', 'Appreciation gifts updated successfully');

    }

    /**
     * Coordiantor Birthdays
     */
    public function birthday(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $id)
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
            ->orderBy('long_name')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->orderBy('conference_name')
            ->get();
        $positionList = DB::table('coordinator_position')
            ->select('id', 'long_title')
            ->orderBy('long_title')
            ->get();

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.is_active', '=', '1')
            ->orderBy('cd.first_name')
            ->get();
        $directReportTo = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.report_id', '=', $id)
            ->where('cd.is_active', '=', '1')
            ->get();

        $directChapterTo = DB::table('chapters as ch')
            ->select('ch.id as ch_id', 'ch.name as ch_name', 'st.state_short_name as st_name')
            ->join('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.primary_coordinator_id', '=', $id)
            ->where('ch.is_active', '=', '1')
            ->get();

        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $coordinatorDetails[0]->birthday_month_id;

        $data = ['directChapterTo' => $directChapterTo, 'directReportTo' => $directReportTo, 'primaryCoordinatorList' => $primaryCoordinatorList, 'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('coordinators.birthday')->with($data);
    }

    /**
     * Update Coordiantor Birthdays (store)
     */
    public function updateBirthday(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $cordinatorId = $id;

        if ($request->get('cord_fname') != '' && $request->get('cord_lname') != '') {
            $corDetails = DB::table('coordinator_details')
                ->select('coordinator_id', 'user_id')
                ->where('coordinator_id', '=', $cordinatorId)
                ->get();
            if (count($corDetails) != 0) {
                try {
                    $userId = $corDetails[0]->user_id;
                    $cordId = $corDetails[0]->coordinator_id;

                    $user = User::find($userId);
                    $user->first_name = $request->get('cord_fname');
                    $user->last_name = $request->get('cord_lname');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    DB::table('coordinator_details')
                        ->where('coordinator_id', $cordinatorId)
                        ->update(['card_sent' => $request->get('card_sent'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                    DB::commit();
                } catch (\Exception $e) {
                    // Rollback Transaction
                    DB::rollback();
                    // Log the error
                    Log::error($e);

                    return redirect()->to('/reports/birthday')->with('fail', 'Something went wrong, Please try again.');
                }
            }
        }

        return redirect()->to('/reports/birthday')->with('success', 'Appreciation gifts updated successfully');

    }

    /**
     * Coordiantor Dashboard - Login Main Screen
     */
    public function showDashboard(Request $request): View
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $id = $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corReportId = $corDetails['report_id'];
        $positionid = $corDetails['position_id'];
        $secpositionid = $corDetails['sec_position_id'];
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $id)
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
            ->orderBy('long_name')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->orderBy('conference_name')
            ->get();
        $positionList = DB::table('coordinator_position')
            ->select('id', 'long_title')
            ->orderBy('long_title')
            ->get();

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'cd.phone as cor_phone', 'cd.alt_phone as cor_altphone')
            ->where('cd.coordinator_id', '=', $corReportId)
            ->where('cd.is_active', '=', '1')
            ->get();
        if (count($primaryCoordinatorList) == 0) {
            $primaryCoordinatorList[0] = ['cor_f_name' => '', 'cor_l_name' => '', 'cor_email' => '', 'cor_phone' => '', 'cor_altphone' => ''];
            $primaryCoordinatorList = json_decode(json_encode($primaryCoordinatorList));
        }
        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $coordinatorDetails[0]->birthday_month_id;

        $data = ['primaryCoordinatorList' => $primaryCoordinatorList, 'positionid' => $positionid, 'secpositionid' => $secpositionid, 'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('coordinators.dashboard')->with($data);
    }

    public function updateDashboard(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $cordinatorId = $id;

        $corDetails = DB::table('coordinator_details')
            ->select('coordinator_id', 'user_id')
            ->where('coordinator_id', '=', $cordinatorId)
            ->get();
        if (count($corDetails) != 0) {
            try {
                $userId = $corDetails[0]->user_id;
                $cordId = $corDetails[0]->coordinator_id;

                $user = User::find($userId);
                $user->updated_at = date('Y-m-d H:i:s');
                $user->save();

                DB::table('coordinator_details')
                    ->where('coordinator_id', $cordinatorId)
                    ->update([
                        'todo_month' => $request->get('todo_month'),
                        'todo_check_chapters' => $request->has('todo_check_chapters') ? 1 : null,
                        'todo_send_rereg' => $request->has('todo_send_rereg') ? 1 : null,
                        'todo_send_late' => $request->has('todo_send_late') ? 1 : null,
                        'todo_record_rereg' => $request->has('todo_record_rereg') ? 1 : null,
                        'todo_record_m2m' => $request->has('todo_record_m2m') ? 1 : null,
                        'todo_export_reports' => $request->has('todo_export_reports') ? 1 : null,
                        'todo_export_int_reports' => $request->has('todo_export_int_reports') ? 1 : null,
                        'todo_election_faq' => $request->has('todo_election_faq') ? 1 : null,
                        'todo_election_due' => $request->has('todo_election_due') ? 1 : null,
                        'todo_financial_due' => $request->has('todo_financial_due') ? 1 : null,
                        'todo_990_due' => $request->has('todo_990_due') ? 1 : null,
                        'todo_welcome' => $request->has('todo_welcome') ? 1 : null,
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => date('Y-m-d H:i:s')]);
                DB::commit();
            } catch (\Exception $e) {
                // Rollback Transaction
                DB::rollback();

                // Log the error
                Log::error($e);

                return redirect()->to('/coordinator/dashboard')->with('fail', 'Something went wrong, Please try again.');
            }
        }

        return redirect()->to('/coordinator/dashboard')->with('success', 'Coordinator dashboard updated successfully');
    }

    /**
     * Coordiantor Profile
     */
    public function showProfile(Request $request): View
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $id = $corId = $corDetails['coordinator_id'];
        $corConfId = $corDetails['conference_id'];
        $corReportId = $corDetails['report_id'];
        $coordinatorDetails = DB::table('coordinator_details as cd')
            ->select('cd.*')
            ->where('cd.is_active', '=', '1')
            ->where('cd.coordinator_id', '=', $id)
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
            ->orderBy('long_name')
            ->get();
        $confList = DB::table('conference')
            ->select('id', 'conference_name')
            ->orderBy('conference_name')
            ->get();
        $positionList = DB::table('coordinator_position')
            ->select('id', 'long_title')
            ->orderBy('long_title')
            ->get();

        $primaryCoordinatorList = DB::table('coordinator_details as cd')
            ->select('cd.coordinator_id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'cd.phone as cor_phone', 'cd.alt_phone as cor_altphone')
            ->where('cd.coordinator_id', '=', $corReportId)
            ->where('cd.is_active', '=', '1')
            ->get();
        if (count($primaryCoordinatorList) == 0) {
            $primaryCoordinatorList[0] = ['cor_f_name' => '', 'cor_l_name' => '', 'cor_email' => '', 'cor_phone' => '', 'cor_altphone' => ''];
            $primaryCoordinatorList = json_decode(json_encode($primaryCoordinatorList));
        }
        $foundedMonth = ['1' => 'JAN', '2' => 'FEB', '3' => 'MAR', '4' => 'APR', '5' => 'MAY', '6' => 'JUN', '7' => 'JUL', '8' => 'AUG', '9' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DEC'];
        $currentMonth = $coordinatorDetails[0]->birthday_month_id;

        $data = ['primaryCoordinatorList' => $primaryCoordinatorList, 'positionList' => $positionList, 'confList' => $confList, 'currentMonth' => $currentMonth, 'coordinatorDetails' => $coordinatorDetails, 'regionList' => $regionList, 'stateArr' => $stateArr, 'countryArr' => $countryArr, 'foundedMonth' => $foundedMonth];

        return view('coordinators.profile')->with($data);
    }

    /**
     * CUpdate Profile (Store)
     */
    public function updateProfile(Request $request, $id): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->CoordinatorDetails;
        $corId = $corDetails['coordinator_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        $cordinatorId = $id;

        if ($request->get('cord_fname') != '' && $request->get('cord_lname') != '' && $request->get('cord_email') != '') {
            $corDetails = DB::table('coordinator_details')
                ->select('coordinator_id', 'user_id')
                ->where('coordinator_id', '=', $cordinatorId)
                ->get();
            if (count($corDetails) != 0) {
                try {
                    $userId = $corDetails[0]->user_id;
                    $cordId = $corDetails[0]->coordinator_id;

                    $user = User::find($userId);
                    $user->first_name = $request->get('cord_fname');
                    $user->last_name = $request->get('cord_lname');
                    $user->email = $request->get('cord_email');
                    $user->updated_at = date('Y-m-d H:i:s');
                    $user->save();

                    DB::table('coordinator_details')
                        ->where('coordinator_id', $cordinatorId)
                        ->update(['first_name' => $request->get('cord_fname'),
                            'last_name' => $request->get('cord_lname'),
                            'email' => $request->get('cord_email'),
                            'sec_email' => $request->get('cord_sec_email'),
                            'address' => $request->get('cord_addr'),
                            'city' => $request->get('cord_city'),
                            'state' => $request->get('cord_state'),
                            'zip' => $request->get('cord_zip'),
                            'country' => $request->get('cord_country'),
                            'phone' => $request->get('cord_phone'),
                            'alt_phone' => $request->get('cord_altphone'),
                            'birthday_month_id' => $request->get('cord_month'),
                            'birthday_day' => $request->get('cord_day'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                    DB::commit();
                } catch (\Exception $e) {
                    // Rollback Transaction
                    DB::rollback();
                    // Log the error
                    Log::error($e);

                    return redirect()->to('/coordinator/profile')->with('fail', 'Something went wrong, Please try again.');
                }
            }
        }

        return redirect()->to('/coordinator/profile')->with('success', 'Coordinator profile updated successfully');
    }
}
