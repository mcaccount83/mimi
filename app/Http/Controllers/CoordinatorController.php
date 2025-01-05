<?php

namespace App\Http\Controllers;

use App\Mail\CoordinatorRetireAdmin;
use App\Models\CoordinatorPosition;
use App\Models\Coordinators;
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
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->middleware('auth')->except('logout');
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class);
        $this->userController = $userController;
    }

    /**
     * Active Coordinator List Base Query
     */
    public function getActiveBaseQuery($userConfId, $userRegId, $userCdId, $userPositionid, $userSecPositionid)
    {
        $conditions = getPositionConditions($userPositionid, $userSecPositionid);
        if ($conditions['coordinatorCondition']) {
            $coordinatorData = $this->userController->loadReportingTree($userCdId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = Coordinators::with(['state', 'conference', 'region', 'displayPosition', 'mimiPosition', 'secondaryPosition', 'birthdayMonth'])
            ->where('is_active', 1);

        if ($conditions['founderCondition']) {
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('conference_id', '=', $userConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('region_id', '=', $userRegId);
        } else {
            $baseQuery->whereIn('report_id', $inQryArr);
        }

        if (isset($_GET['check']) && $_GET['check'] == 'yes') {
            $checkBoxStatus = 'checked';
            $baseQuery->where('report_id', '=', $userCdId);
        } else {
            $checkBoxStatus = '';
        }

        $baseQuery->orderBy('coordinator_start_date');

        return ['query' => $baseQuery, 'checkBoxStatus' => $checkBoxStatus];

    }

    /**
     * Retired Coordinator List Base Query
     */
    public function getRetiredBaseQuery($userConfId, $userRegId, $userCdId, $userPositionid, $userSecPositionid)
    {
        $conditions = getPositionConditions($userPositionid, $userSecPositionid);
        if ($conditions['coordinatorCondition']) {
            $coordinatorData = $this->userController->loadReportingTree($userCdId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = Coordinators::with(['state', 'conference', 'region', 'displayPosition', 'mimiPosition', 'secondaryPosition', 'birthdayMonth'])
            ->where('is_active', 0);

        if ($conditions['founderCondition']) {
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('conference_id', '=', $userConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('region_id', '=', $userRegId);
        } else {
            $baseQuery->whereIn('report_id', $inQryArr);
        }

        $baseQuery->orderByDesc('zapped_date');

        return ['query' => $baseQuery];
    }

    /**
     * Active Coordinator Details Base Query
     */
    public function getCoordinatorDetails($id)
    {
        $cdDetails = Coordinators::with(['state', 'conference', 'region', 'displayPosition', 'mimiPosition', 'secondaryPosition', 'birthdayMonth',
            'reportsTo'])->find($id);
        $cdId = $cdDetails->id;
        $cdIsActive = $cdDetails->is_active;
        $regionLongName = $cdDetails->region->long_name;
        $conferenceDescription = $cdDetails->conference->conference_description;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdRptId = $cdDetails->report_id;
        $RptFName = $cdDetails->reportsTo->first_name;
        $RptLName = $cdDetails->reportsTo->last_name;
        $displayPosition = $cdDetails->displayPosition;
        $mimiPosition = $cdDetails->mimiPosition;
        $secondaryPosition = $cdDetails->secondaryPosition;

        return ['cdDetails' => $cdDetails, 'cdId' => $cdId, 'cdIsActive' => $cdIsActive, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription, 'cdConfId' => $cdConfId, 'cdRegId' => $cdRegId, 'cdRptId' => $cdRptId,
            'RptFName' => $RptFName, 'RptLName' => $RptLName, 'displayPosition' => $displayPosition, 'mimiPosition' => $mimiPosition,
            'secondaryPosition' => $secondaryPosition,
        ];
    }

    /**
     * Active Coordiantor List
     */
    public function showCoordinators(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $coordinatorList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

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

        $countList = count($coordinatorList);
        $data = ['countList' => $countList, 'coordinatorList' => $coordinatorList, 'checkBoxStatus' => $checkBoxStatus, 'emailListCord' => $emailListCord];

        return view('coordinators.coordlist')->with($data);
    }

    /**
     * Retired Coorinators List
     */
    public function showRetiredCoordinator(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $userDetails = $user->coordinator;
        $userCdId = $userDetails->id;
        $userConfId = $userDetails->conference_id;
        $userRegId = $userDetails->region_id;
        $userPositionid = $userDetails->position_id;
        $userSecPositionid = $userDetails->sec_position_id;

        $baseQuery = $this->getRetiredBaseQuery($userConfId, $userRegId, $userCdId, $userPositionid, $userSecPositionid);
        $retiredCoordinatorList = $baseQuery['query']->get();

        $data = ['retiredCoordinatorList' => $retiredCoordinatorList];

        return view('coordinators.coordretired')->with($data);
    }

    /**
     * International Coordinators List
     */
    public function showIntCoordinator(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $intCoordinatorList = Coordinators::with(['state', 'conference', 'region', 'displayPosition', 'mimiPosition', 'secondaryPosition', 'reportsTo'])
            ->where('is_active', 1)
            ->orderBy('coordinator_start_date')
            ->get();

        $data = ['intCoordinatorList' => $intCoordinatorList];

        return view('international.intcoord')->with($data);
    }

    /**
     * International Retired Coordinator List
     */
    public function showIntCoordinatorRetired(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $intCoordinatorList = Coordinators::with(['state', 'conference', 'region', 'displayPosition', 'mimiPosition', 'secondaryPosition', 'reportsTo'])
            ->where('is_active', 0)
            ->orderByDesc('zapped_date')
            ->get();

        $data = ['intCoordinatorList' => $intCoordinatorList];

        return view('international.intcoordretired')->with($data);
    }

    /**
     * Add New Coordiantor
     */
    public function editCoordNew(Request $request): View
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $userId = $corDetails['id'];
        $corId = $corDetails['id'];
        $corReportTo = $corDetails['first_name'].' '.$corDetails['last_name'];
        $userConfId = $corDetails['conference_id'];
        $userRegId = $corDetails['region_id'];

        $conference = DB::table('conference')
            ->select('conference.*')
            ->where('conference.id', '=', $userConfId)
            ->get();

        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();

        $monthArr = DB::table('month')
            ->select('month.*')
            ->orderBy('id')
            ->get();

        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $userConfId)
            ->orderBy('long_name')
            ->get();

        $region = DB::table('region')
            ->select('id', 'long_name')
            ->where('region.id', '=', $userRegId)
            ->get();

        $primaryCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', $userConfId)
            ->where('cd.position_id', '<', 8)
            ->where('cd.position_id', '>', 1)
            ->where('cd.is_active', 1)
            ->orderBy('cd.position_id')
            ->orderBy('cd.first_name')
            ->orderBy('cd.last_name')
            ->get();

        $data = ['stateArr' => $stateArr, 'monthArr' => $monthArr, 'regionList' => $regionList, 'primaryCoordinatorList' => $primaryCoordinatorList, 'userConfId' => $userConfId,
            'conference' => $conference, 'corId' => $corId, 'corReportTo' => $corReportTo, 'region' => $region];

        return view('coordinators.editnew')->with($data);
    }

    /**
     * Update New Coordiantor
     */
    public function updateCoordNew(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
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

            $cordId = DB::table('coordinators')->insertGetId(
                ['user_id' => $userId,
                    'conference_id' => $corConfId,
                    'region_id' => $input['cord_region'],
                    'layer_id' => $new_layer_id,
                    'first_name' => $input['cord_fname'],
                    'last_name' => $input['cord_lname'],
                    'position_id' => 1,
                    'display_position_id' => 1,
                    'email' => $input['cord_email'],
                    'sec_email' => $input['cord_sec_email'],
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
                    'home_chapter' => $input['cord_chapter'],
                    'coordinator_start_date' => date('Y-m-d H:i:s'),
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1]
            );

            $cordReportingTree = DB::table('coordinator_reporting_tree')
                ->select('layer0', 'layer1', 'layer2', 'layer3', 'layer4', 'layer5', 'layer6', 'layer7', 'layer8')
                ->where('id', '=', $cordId)
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

            return redirect()->to('/')->with('fail', 'Something went wrong, Please try again..');
        }

        return redirect()->to('/coordinator/coordlist')->with('success', 'Coordinator created successfully.');
    }

    /**
     * Create New Coordiantor (store)
     */
    public function updateCoordinatorNew(Request $request): RedirectResponse
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
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

            $cordId = DB::table('coordinators')->insertGetId(
                ['user_id' => $userId,
                    'conference_id' => $corConfId,
                    'region_id' => $corRegId,
                    'layer_id' => $new_layer_id,
                    'first_name' => $input['cord_fname'],
                    'last_name' => $input['cord_lname'],
                    'position_id' => 1,
                    'display_position_id' => 1,
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
                    'home_chapter' => $input['cord_chapter'],
                    'coordinator_start_date' => date('Y-m-d H:i:s'),
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1]
            );

            $cordReportingTree = DB::table('coordinator_reporting_tree')
                ->select('layer0', 'layer1', 'layer2', 'layer3', 'layer4', 'layer5', 'layer6', 'layer7', 'layer8')
                ->where('id', '=', $cordId)
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

            return redirect()->to('/')->with('fail', 'Something went wrong, Please try again..');
        }

        return redirect()->to('/coordinator/coordlist')->with('success', 'Coordinator created successfully.');
    }

    /**
     * Get Region List -- auto updates dropdown menu in top section of update role screen when conference changes.
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
     * Get Reporting List -- auto updates dropdown menu in top section of update role screen when region changes.
     */
    public function getReportingList(): JsonResponse
    {
        $conference_id = $_GET['conf_id'];
        $position_id = $_GET['pos_id'];
        $region_id = $_GET['reg_id'];

        $reportCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', $conference_id)
            ->where('cd.position_id', '>', $position_id)
            ->where('cd.position_id', '>', 1)
            ->where(function ($query) use ($region_id) {
                $query->where('cd.region_id', $region_id)
                    ->orWhereIn('cd.position_id', [6, 7]);
            })
            ->where('cd.is_active', 1)
            ->orderBy('cd.position_id')
            ->orderBy('cd.first_name')
            ->orderBy('cd.last_name')
            ->get();

        $html = '<option value=""></option>';
        foreach ($reportCoordinatorList as $list) {
            $html .= '<option value="'.$list->cid.'">'.$list->cor_f_name.' '.$list->cor_l_name.' ('.$list->pos.')</option>';
        }

        return response()->json(['html' => $html]);
    }

    /**
     * Get Direct Report -- auto updates dropdown menu in bottom section of update role screen when region changes.
     */
    public function getDirectReportingList(): JsonResponse
    {
        $conference_id = $_GET['conf_id'];
        $position_id = $_GET['pos_id'];
        $region_id = $_GET['reg_id'];

        $directReportTo = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', $conference_id)
            ->where('cd.position_id', '<', $position_id)
            ->where(function ($query) use ($region_id) {
                $query->where('cd.region_id', $region_id)
                    ->orWhereIn('cd.position_id', [6, 7]);
            })
            ->where('cd.is_active', 1)
            ->orderBy('cd.position_id')
            ->orderBy('cd.first_name')
            ->orderBy('cd.last_name')
            ->get();

        $html = '<option value=""></option>';
        foreach ($directReportTo as $list) {
            $html .= '<option value="'.$list->cid.'">'.$list->cor_f_name.' '.$list->cor_l_name.' ('.$list->pos.')</option>';
        }

        return response()->json(['html' => $html]);
    }

    /**
     * Get Primary Coordinator -- auto updates dropdown menu in bottom section of update role screen when region changes.
     */
    public function getChapterPrimaryFor(): JsonResponse
    {
        $conference_id = $_GET['conf_id'];
        $position_id = $_GET['pos_id'];
        $region_id = $_GET['reg_id'];

        if ($region_id == 0) {
            $primaryChapterList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'st.state_short_name as state')
                ->join('state as st', 'chapters.state_id', '=', 'st.id')
                ->where('chapters.conference_id', '=', $conference_id)
                ->where('chapters.is_active', '=', '1')
                ->orderBy('st.state_short_name')
                ->get();
        } else {
            $primaryChapterList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'st.state_short_name as state')
                ->join('state as st', 'chapters.state_id', '=', 'st.id')
                ->where('chapters.region_id', '=', $region_id)
                ->where('chapters.is_active', '=', '1')
                ->orderBy('st.state_short_name')
                ->orderBy('chapters.name')
                ->get();
        }

        $html = '<option value=""></option>';
        foreach ($primaryChapterList as $list) {
            $html .= '<option value="'.$list->id.'">'.$list->state.' - '.$list->chapter_name.'</option>';
        }

        return response()->json(['html' => $html]);
    }

    /**
     * Reassign Chapter
     */
    public function ReassignChapter(Request $request, $chapter_id, $coordinator_id, $check_changed = false)
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'];
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
        $corDetails = User::find($request->user()->id)->coordinator;
        $corId = $corDetails['id'] = $coordinator_id;
        $corConfId = $corDetails['conference_id'];
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];
        if ($check_changed) {
            $checkReportIdArr = DB::table('coordinators as cd')
                ->select('cd.report_id as repid')
                ->where('cd.id', '=', $coordinator_id)
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
            $query = $layerId = DB::table('coordinators')
                ->select('layer_id')
                ->where('id', $new_coordinator_id)
                ->limit(1)
                ->get();
            $new_layer_id = $query[0]->layer_id + 1;

            //Update their main report ID & layer
            DB::table('coordinators')
                ->where('id', $coordinator_id)
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
     * View Coordiantor Detais
     */
    public function viewCoordDetails(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $userDetails = $user->coordinator;
        $userCdId = $userDetails->id;
        $userConfId = $userDetails->conference_id;
        $userRegId = $userDetails->region_id;

        $baseQuery = $this->getCoordinatorDetails($id);
        $cdDetails = $baseQuery['cdDetails'];
        $cdId = $baseQuery['cdId'];
        $cdIsActive = $baseQuery['cdIsActive'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $cdConfId = $baseQuery['cdConfId'];
        $cdRptId = $baseQuery['cdRptId'];
        $RptFName = $baseQuery['RptFName'];
        $RptLName = $baseQuery['RptLName'];
        $ReportTo = $RptFName.' '.$RptLName;
        $displayPosition = $baseQuery['displayPosition'];
        $mimiPosition = $baseQuery['mimiPosition'];
        $secondaryPosition = $baseQuery['secondaryPosition'];
        $cdLeave = $baseQuery['cdDetails']->on_leave;

        $directReportTo = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.report_id', '=', $id)
            ->where('cd.is_active', '=', '1')
            ->get();

        $directChapterTo = DB::table('chapters as ch')
            ->select('ch.id as ch_id', 'ch.name as ch_name', 'st.state_short_name as st_name')
            ->join('state as st', 'ch.state_id', '=', 'st.id')
            ->where('ch.primary_coordinator_id', '=', $id)
            ->where('ch.is_active', '=', '1')
            ->get();

        $data = ['cdDetails' => $cdDetails, 'cdConfId' => $cdConfId, 'conferenceDescription' => $conferenceDescription, 'regionLongName' => $regionLongName,
            'cdIsActive' => $cdIsActive, 'userConfId' => $userConfId, 'userId' => $userId, 'cdLeave' => $cdLeave, 'ReportTo' => $ReportTo,
            'directReportTo' => $directReportTo, 'directChapterTo' => $directChapterTo, 'displayPosition' => $displayPosition, 'mimiPosition' => $mimiPosition,
            'secondaryPosition' => $secondaryPosition,
        ];

        return view('coordinators.view')->with($data);
    }

    public function updateCardSent(Request $request): JsonResponse
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $lastUpdatedBy = $corDetails['first_name'].' '.$corDetails['last_name'];

        $coordId = $request->input('id');
        $cardSent = $request->input('card_sent');

        $coordinators = Coordinators::find($coordId);

        DB::beginTransaction();

        try {
            $coordinators->card_sent = $cardSent;
            $coordinators->last_updated_by = $lastUpdatedBy;
            $coordinators->last_updated_date = date('Y-m-d');
            $coordinators->save();

            // DB::beginTransaction();

            // // Update the database using the `id` field instead of `coord_id`
            // DB::table('coordinators')
            //     ->where('id', $coordId)
            //     ->update([
            //         'card_sent' => $cardSent,
            //         'last_updated_by' => $lastUpdatedBy,
            //         'last_updated_date' => now(),
            //     ]);

            DB::commit();

            $message = 'Coordinator Birthday Card Sent date successfully updated';

            return response()->json(['status' => 'success', 'message' => $message, 'redirect' => route('coordinators.view', ['id' => $coordId])]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            $message = 'Something went wrong, please try again.';

            return response()->json(['status' => 'error', 'message' => $message, 'redirect' => route('coordinators.view', ['id' => $coordId])]);
        }
    }

    /**
     * Update Putting a Coordinator on Leave
     */
    public function updateOnLeave(Request $request): JsonResponse
    {
        $userDetails = User::find($request->user()->id)->coordinator;
        $lastUpdatedBy = $userDetails['first_name'].' '.$userDetails['last_name'];

        $input = $request->all();
        $coordId = $input['coord_id'];

        $coordinators = Coordinators::find($coordId);

        DB::beginTransaction();
        try {
            $coordinators->on_leave = 1;
            $coordinators->last_updated_by = $lastUpdatedBy;
            $coordinators->last_updated_date = date('Y-m-d');
            $coordinators->save();

            DB::commit();

            $message = 'Coordinator successfully on leave';

            return response()->json(['status' => 'success', 'message' => $message, 'redirect' => route('coordinators.view', ['id' => $coordId])]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback transaction on exception
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            return response()->json(['status' => 'error', 'message' => $message, 'redirect' => route('coordinators.view', ['id' => $coordId])]);
        }
    }

    /**
     * Update Removing a Coordinator from Leave
     */
    public function updateRemoveLeave(Request $request): JsonResponse
    {
        $userDetails = User::find($request->user()->id)->coordinator;
        $lastUpdatedBy = $userDetails['first_name'].' '.$userDetails['last_name'];

        $input = $request->all();
        $coordId = $input['coord_id'];

        $coordinators = Coordinators::find($coordId);

        DB::beginTransaction();
        try {
            $coordinators->on_leave = 0;
            $coordinators->leave_date = null;
            $coordinators->last_updated_by = $lastUpdatedBy;
            $coordinators->last_updated_date = date('Y-m-d');
            $coordinators->save();

            DB::commit();

            $message = 'Coordinator successfully removed from leave';

            return response()->json(['status' => 'success', 'message' => $message, 'redirect' => route('coordinators.view', ['id' => $coordId])]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback transaction on exception
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            return response()->json(['status' => 'error', 'message' => $message, 'redirect' => route('coordinators.view', ['id' => $coordId])]);
        }
    }

    /**
     * Function for Retiring a Coordinator
     */
    public function updateRetire(Request $request): JsonResponse
    {
        $userDetails = User::find($request->user()->id)->coordinator;
        $lastUpdatedBy = $userDetails['first_name'].' '.$userDetails['last_name'];

        $input = $request->all();
        $coordId = $input['coord_id'];
        $retireReason = $input['reason_retired'];

        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.first_name as fname', 'cd.last_name as lname', 'cd.conference_id as conference', 'cd.email as email')
            ->where('cd.id', '=', $coordId)
            ->get();

        $coordName = $coordinatorDetails[0]->fname.' '.$coordinatorDetails[0]->lname;
        $coordConf = $coordinatorDetails[0]->conference;
        $email = $coordinatorDetails[0]->email;

        try {
            DB::beginTransaction();

            DB::table('coordinators')
                ->where('id', $coordId)
                ->update(['is_active' => 0,
                    'reason_retired' => $retireReason,
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d'),
                    'zapped_date' => date('Y-m-d'),
                ]);

            $userRelatedCoordinatorDetails = DB::table('coordinators as cd')
                ->select('cd.user_id')
                ->where('cd.id', '=', $coordId)
                ->get();
            foreach ($userRelatedCoordinatorDetails as $list) {
                $userId = $list->user_id;
                DB::table('users')
                    ->where('id', $userId)
                    ->update(['is_active' => 0]);
            }

            $mailData = [
                'coordName' => $coordName,
                'confNumber' => $coordConf,
                'email' => $email,
            ];

            $to_email = 'jackie.mchenry@momsclub.org';

            Mail::to($to_email, 'MOMS Club')
                ->queue(new CoordinatorRetireAdmin($mailData));

            // Commit the transaction
            DB::commit();

            $message = 'Coordinator successfully retired';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('coordinators.view', ['id' => $coordId]),
            ]);

        } catch (\Exception $e) {
            // Rollback transaction on exception
            DB::rollback();
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('coordinators.view', ['id' => $coordId]),
            ]);
        }
    }

    /**
     * Function for Retiring a Coordinator
     */
    public function updateUnRetire(Request $request): JsonResponse
    {
        $userDetails = User::find($request->user()->id)->coordinator;
        $lastUpdatedBy = $userDetails['first_name'].' '.$userDetails['last_name'];

        $input = $request->all();
        $coordId = $input['coord_id'];

        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.first_name as fname', 'cd.last_name as lname', 'cd.conference_id as conference', 'cd.email as email')
            ->where('cd.id', '=', $coordId)
            ->get();

        $coordName = $coordinatorDetails[0]->fname.' '.$coordinatorDetails[0]->lname;
        $coordConf = $coordinatorDetails[0]->conference;
        $email = $coordinatorDetails[0]->email;

        try {
            DB::beginTransaction();

            DB::table('coordinators')
                ->where('id', $coordId)
                ->update(['is_active' => 1,
                    'reason_retired' => null,
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d'),
                    'zapped_date' => null,
                ]);

            $userRelatedCoordinatorDetails = DB::table('coordinators as cd')
                ->select('cd.user_id')
                ->where('cd.id', '=', $coordId)
                ->get();
            foreach ($userRelatedCoordinatorDetails as $list) {
                $userId = $list->user_id;
                DB::table('users')
                    ->where('id', $userId)
                    ->update(['is_active' => 1]);
            }

            // $mailData = [
            //     'coordName' => $coordName,
            //     'confNumber' => $coordConf,
            //     'email' => $email,
            // ];

            // $to_email = 'jackie.mchenry@momsclub.org';

            // Mail::to($to_email, 'MOMS Club')
            //     ->queue(new CoordinatorRetireAdmin($mailData));

            // Commit the transaction
            DB::commit();

            $message = 'Coordinator successfully reactivated';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('coordinators.view', ['id' => $coordId]),
            ]);

        } catch (\Exception $e) {
            // Rollback transaction on exception
            DB::rollback();
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('coordinators.view', ['id' => $coordId]),
            ]);
        }
    }

    /**
     * Edit Coordiantor Role
     */
    public function editCoordRole(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $userId = $corDetails['id'];
        $userConfId = $corDetails['conference_id'];
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*', 'st.state_short_name as statename', 'cf.conference_description as confname', 'rg.long_name as regname', 'cp.long_title as position',
                'cp3.long_title as display_position', 'cp2.long_title as sec_position', 'cd2.first_name as report_fname', 'cd2.email as report_email', 'cd2.last_name as report_lname')
            ->leftJoin('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')  // Primary Position
            ->leftJoin('coordinator_position as cp2', 'cd.sec_position_id', '=', 'cp2.id')  //Secondary Position
            ->leftJoin('coordinator_position as cp3', 'cd.display_position_id', '=', 'cp3.id')  //Display Position
            ->leftJoin('coordinators as cd2', 'cd.report_id', '=', 'cd2.id') //Supervising Coordinator
            ->leftJoin('month as mo', 'cd.birthday_month_id', '=', 'mo.id')
            ->leftJoin('state as st', 'cd.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
            ->leftJoin('region as rg', 'cd.region_id', '=', 'rg.id')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $id)
            ->get();

        $conid = $coordinatorDetails[0]->id;
        $corIsActive = $coordinatorDetails[0]->is_active;
        $corIsLeave = $coordinatorDetails[0]->on_leave;
        $position_id = $coordinatorDetails[0]->position_id;
        $conference_id = $corConfId = $coordinatorDetails[0]->conference_id;
        $region_id = $coordinatorDetails[0]->region_id;

        $coordinator_list = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->join('region', 'cd.region_id', '=', 'region.id')
            ->where('cd.report_id', $coordinatorDetails[0]->id)
            ->where('cd.is_active', 1)
            ->get();

        $coordinator_options = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where(function ($query) use ($coordinatorDetails) {
                $query->where('cd.conference_id', $coordinatorDetails[0]->conference_id)
                    ->where('cd.position_id', '>=', 1)
                    ->where('cd.position_id', '<=', 7)
                    ->where('cd.is_active', 1);
            })
            ->orderBy('cd.first_name')
            ->orderBy('cd.last_name')
            ->get();

        $row_count = count($coordinator_list);

        $chapter_list = DB::table('chapters')
            ->select('chapters.id', 'state.state_short_name as state', 'chapters.name as name')
            ->join('state', 'chapters.state_id', '=', 'state.id')
            ->where('primary_coordinator_id', $coordinatorDetails[0]->id)
            ->where('chapters.is_active', 1)
            ->orderBy('state.state_short_name')
            ->orderBy('chapters.name')
            ->get();

        if ($coordinatorDetails[0]->region_id == 0) {
            $coordinator_options = DB::table('coordinators as cd')
                ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                ->where(function ($query) use ($coordinatorDetails) {
                    $query->where('cd.conference_id', $coordinatorDetails[0]->conference_id)
                        ->where('cd.position_id', '>=', 1)
                        ->where('cd.position_id', '<=', 7)
                        ->where('cd.is_active', 1);
                })
                ->orderBy('cd.first_name')
                ->orderBy('cd.last_name')
                ->get();
        } else {
            $coordinator_options = DB::table('coordinators as cd')
                ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                ->where(function ($query) use ($coordinatorDetails) {
                    $query->where('cd.region_id', $coordinatorDetails[0]->region_id)
                        ->where(function ($query) {
                            $query->where('cd.position_id', '>=', 1)
                                ->where('cd.position_id', '<=', 7)
                                ->where('cd.is_active', 1);
                        });
                })
                ->orWhere(function ($query) use ($coordinatorDetails) {
                    $query->where('cd.position_id', 7)
                        ->where('cd.conference_id', $coordinatorDetails[0]->conference_id)
                        ->where('cd.is_active', 1);
                })
                ->orderBy('cd.first_name')
                ->orderBy('cd.last_name')
                ->get();
        }

        $chapter_count = count($coordinator_options);

        /***Query For Report To in Frst Section */
        $primaryCoordinatorList = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', $conference_id)
            ->where('cd.position_id', '>', $position_id)
            ->where('cd.position_id', '>', 1)
            ->where(function ($query) use ($region_id) {
                $query->where('cd.region_id', $region_id)
                    ->orWhereIn('cd.position_id', [6, 7]);
            })
            ->where('cd.is_active', 1)
            ->orderBy('cd.position_id')
            ->orderBy('cd.first_name')
            ->orderBy('cd.last_name')
            ->get();

        /***Query For Direct Report Dropdown in Bottom Section */
        $directReportTo = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.conference_id', $conference_id)
            ->where('cd.position_id', '<', $position_id)
            ->where(function ($query) use ($region_id) {
                $query->where('cd.region_id', $region_id)
                    ->orWhereIn('cd.position_id', [6, 7]);
            })
            ->where('cd.report_id', '!=', $coordinatorDetails[0]->id)
            ->where('cd.is_active', 1)
            ->orderBy('cd.position_id')
            ->orderBy('cd.first_name')
            ->orderBy('cd.last_name')
            ->get();

        /***Query For Primary For Dropdown in Bottom Section */
        if ($region_id == 0) {
            $primaryChapterList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'st.state_short_name as state')
                ->join('state as st', 'chapters.state_id', '=', 'st.id')
                ->where('chapters.conference_id', '=', $conference_id)
                ->where('primary_coordinator_id', '!=', $coordinatorDetails[0]->id)
                ->where('chapters.is_active', '=', '1')
                ->orderBy('st.state_short_name')
                ->get();
        } else {
            $primaryChapterList = DB::table('chapters')
                ->select('chapters.id as id', 'chapters.name as chapter_name', 'st.state_short_name as state')
                ->join('state as st', 'chapters.state_id', '=', 'st.id')
                ->where('chapters.region_id', '=', $region_id)
                ->where('primary_coordinator_id', '!=', $coordinatorDetails[0]->id)
                ->where('chapters.is_active', '=', '1')
                ->orderBy('st.state_short_name')
                ->orderBy('chapters.name')
                ->get();
        }

        $positionList = DB::table('coordinator_position')
            ->select('id', 'long_title', 'level_id')
            ->orderBy('id')
            ->get();

        $regionList = DB::table('region')
            ->select('id', 'long_name')
            ->where('conference_id', '=', $conference_id)
            ->orderBy('long_name')
            ->get();

        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();

        $monthArr = DB::table('month')
            ->select('month.*')
            ->orderBy('id')
            ->get();

        $data = ['coordinatorDetails' => $coordinatorDetails, 'corConfId' => $corConfId, 'chapter_count' => $chapter_count, 'row_count' => $row_count, 'coordinator_options' => $coordinator_options,
            'chapter_list' => $chapter_list, 'coordinator_list' => $coordinator_list, 'corIsActive' => $corIsActive, 'userConfId' => $userConfId, 'userId' => $userId, 'corIsLeave' => $corIsLeave,
            'directReportTo' => $directReportTo, 'primaryChapterList' => $primaryChapterList, 'stateArr' => $stateArr, 'monthArr' => $monthArr, 'conid' => $conid, 'positionList' => $positionList,
            'primaryCoordinatorList' => $primaryCoordinatorList, 'regionList' => $regionList,
        ];

        return view('coordinators.editrole')->with($data);
    }

    /**
     * Update Role, Chapters and Coordinators
     */
    public function updateCoordRole(Request $request, $id): RedirectResponse
    {
        $userDetails = User::find($request->user()->id)->coordinator;
        $userId = $userDetails['id'];
        $userName = $userDetails['first_name'].' '.$userDetails['last_name'];
        $userEmail = $userDetails['email'];
        $userpositionId = $userDetails['position_id'];
        $userposition = CoordinatorPosition::find($userpositionId);
        $userpositionTitle = $userposition['long_title'];
        $lastUpdatedBy = $userName;

        $cordinatorId = $id;

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
            $chapter_field = 'ChapterIDRow'.$i;

            if (! isset($_POST[$coordinator_field]) || ! isset($_POST[$chapter_field])) {
                continue; // Skip if the field doesn't exist
            }

            $coordinator_id = $_POST[$coordinator_field];
            $chapter_id = $_POST[$chapter_field];

            $this->ReassignChapter($request, $chapter_id, $coordinator_id, true);
        }

        //Reassign Report To / Direct Supervisor that Changed
        $coordinator_id = $request->input('coordinator_id');
        $new_coordinator_id = $request->input('cord_report_pc');
        $this->ReassignCoordinator($request, $coordinator_id, $new_coordinator_id, true);

        //Save other changes
        $coordinator = Coordinators::find($cordinatorId);
        DB::beginTransaction();
        try {
            $coordinator->position_id = $request->filled('cord_pos') ? $request->input('cord_pos') : $request->input('OldPosition');
            $coordinator->display_position_id = $request->filled('cord_disp_pos') ? $request->input('cord_disp_pos') : $request->input('OldDisplayPosition');
            $coordinator->sec_position_id = $request->filled('cord_sec_pos') ? $request->input('cord_sec_pos') : $request->input('OldSecPosition');
            $coordinator->last_promoted = $request->input('CoordinatorPromoteDate');
            $coordinator->last_updated_by = $lastUpdatedBy;
            $coordinator->last_updated_date = date('Y-m-d H:i:s');

            $coordinator->save();

            //Insert any notifications here

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            echo $e->getMessage();
            exit();
            DB::rollback();
            // Log the error
            Log::error($e);

            return to_route('coordinators.view', ['id' => $id])->with('fail', 'Something went wrong, Please try again..');
        }

        return to_route('coordinators.view', ['id' => $id])->with('success', 'Chapter Details have been updated');
    }

    /**
     * Edit Coordiantor Details
     */
    public function editCoordDetails(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $userId = $corDetails['id'];
        $userConfId = $corDetails['conference_id'];
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*', 'st.state_short_name as statename', 'cf.conference_description as confname', 'rg.long_name as regname', 'cp.long_title as position',
                'cp3.long_title as display_position', 'cp2.long_title as sec_position', 'cd2.first_name as report_fname', 'cd2.email as report_email', 'cd2.last_name as report_lname')
            ->leftJoin('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')  // Primary Position
            ->leftJoin('coordinator_position as cp2', 'cd.sec_position_id', '=', 'cp2.id')  //Secondary Position
            ->leftJoin('coordinator_position as cp3', 'cd.display_position_id', '=', 'cp3.id')  //Display Position
            ->leftJoin('coordinators as cd2', 'cd.report_id', '=', 'cd2.id') //Supervising Coordinator
            ->leftJoin('month as mo', 'cd.birthday_month_id', '=', 'mo.id')
            ->leftJoin('state as st', 'cd.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
            ->leftJoin('region as rg', 'cd.region_id', '=', 'rg.id')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $id)
            ->get();

        $conid = $coordinatorDetails[0]->id;
        $corIsActive = $coordinatorDetails[0]->is_active;
        $corIsLeave = $coordinatorDetails[0]->on_leave;
        $position_id = $coordinatorDetails[0]->position_id;
        $conference_id = $corConfId = $coordinatorDetails[0]->conference_id;
        $region_id = $coordinatorDetails[0]->region_id;

        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();

        $monthArr = DB::table('month')
            ->select('month.*')
            ->orderBy('id')
            ->get();

        $data = ['coordinatorDetails' => $coordinatorDetails, 'stateArr' => $stateArr, 'monthArr' => $monthArr];

        return view('coordinators.editdetails')->with($data);
    }

    /**
     * Save Coordiantor Details
     */
    public function updateCoordDetails(Request $request, $id): RedirectResponse
    {
        $userDetails = User::find($request->user()->id)->coordinator;
        $userId = $userDetails['id'];
        $userName = $userDetails['first_name'].' '.$userDetails['last_name'];
        $userEmail = $userDetails['email'];
        $userpositionId = $userDetails['position_id'];
        $userposition = CoordinatorPosition::find($userpositionId);
        $userpositionTitle = $userposition['long_title'];
        $lastUpdatedBy = $userName;

        if ($request->input('cord_fname') != '' && $request->input('cord_lname') != '' && $request->input('cord_email') != '') {
            $corDetail = DB::table('coordinators')
                ->select('id', 'user_id')
                ->where('id', '=', $id)
                ->first(); // Use first() to get a single result

            try {
                $userId = $corDetail->user_id;

                $user = User::find($userId);
                $user->first_name = $request->input('cord_fname');
                $user->last_name = $request->input('cord_lname');
                $user->email = $request->input('cord_email');
                $user->updated_at = now();
                $user->save();

                DB::table('coordinators')
                    ->where('id', $id)
                    ->update([
                        'first_name' => $request->input('cord_fname'),
                        'last_name' => $request->input('cord_lname'),
                        'email' => $request->input('cord_email'),
                        'sec_email' => $request->input('cord_sec_email'),
                        'address' => $request->input('cord_addr'),
                        'city' => $request->input('cord_city'),
                        'state' => $request->input('cord_state'),
                        'zip' => $request->input('cord_zip'),
                        'phone' => $request->input('cord_phone'),
                        'alt_phone' => $request->input('cord_altphone'),
                        'birthday_month_id' => $request->input('cord_month'),
                        'birthday_day' => $request->input('cord_day'),
                        'home_chapter' => $request->input('cord_chapter'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                    ]);

                // Commit transaction
                DB::commit();
            } catch (\Exception $e) {
                // Rollback Transaction
                DB::rollback();
                // Log the error
                Log::error($e);

                return to_route('coordinators.editdetails', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
            }
        }

        return to_route('coordinators.editdetails', ['id' => $id])->with('success', 'Coordinator profile updated successfully');
    }

    /**
     * Edit Coordiantor Details
     */
    public function editCoordRecognition(Request $request, $id): View
    {
        $corDetails = User::find($request->user()->id)->coordinator;
        $userId = $corDetails['id'];
        $userConfId = $corDetails['conference_id'];
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*', 'st.state_short_name as statename', 'cf.conference_description as confname', 'rg.long_name as regname', 'cp.long_title as position',
                'cp3.long_title as display_position', 'cp2.long_title as sec_position', 'cd2.first_name as report_fname', 'cd2.email as report_email', 'cd2.last_name as report_lname')
            ->leftJoin('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')  // Primary Position
            ->leftJoin('coordinator_position as cp2', 'cd.sec_position_id', '=', 'cp2.id')  //Secondary Position
            ->leftJoin('coordinator_position as cp3', 'cd.display_position_id', '=', 'cp3.id')  //Display Position
            ->leftJoin('coordinators as cd2', 'cd.report_id', '=', 'cd2.id') //Supervising Coordinator
            ->leftJoin('month as mo', 'cd.birthday_month_id', '=', 'mo.id')
            ->leftJoin('state as st', 'cd.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
            ->leftJoin('region as rg', 'cd.region_id', '=', 'rg.id')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $id)
            ->get();

        $conid = $coordinatorDetails[0]->id;
        $corIsActive = $coordinatorDetails[0]->is_active;
        $corIsLeave = $coordinatorDetails[0]->on_leave;
        $position_id = $coordinatorDetails[0]->position_id;
        $conference_id = $corConfId = $coordinatorDetails[0]->conference_id;
        $region_id = $coordinatorDetails[0]->region_id;

        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();

        $monthArr = DB::table('month')
            ->select('month.*')
            ->orderBy('id')
            ->get();

        $data = ['coordinatorDetails' => $coordinatorDetails, 'stateArr' => $stateArr, 'monthArr' => $monthArr];

        return view('coordinators.editrecognition')->with($data);
    }

    /**
     * Save Coordiantor Details
     */
    public function updateCoordRecognition(Request $request, $id): RedirectResponse
    {
        $userDetails = User::find($request->user()->id)->coordinator;
        $userId = $userDetails['id'];
        $userName = $userDetails['first_name'].' '.$userDetails['last_name'];
        $lastUpdatedBy = $userName;

        $coorDetails = Coordinators::find($id);
        try {
            $coorDetails->recognition_year0 = $request->input('recognition_year0');
            $coorDetails->recognition_year1 = $request->input('recognition_year1');
            $coorDetails->recognition_year2 = $request->input('recognition_year2');
            $coorDetails->recognition_year3 = $request->input('recognition_year3');
            $coorDetails->recognition_year4 = $request->input('recognition_year4');
            $coorDetails->recognition_year5 = $request->input('recognition_year5');
            $coorDetails->recognition_year6 = $request->input('recognition_year6');
            $coorDetails->recognition_year7 = $request->input('recognition_year7');
            $coorDetails->recognition_year8 = $request->input('recognition_year8');
            $coorDetails->recognition_year9 = $request->input('recognition_year9');
            $coorDetails->recognition_toptier = $request->input('recognition_toptier');
            $coorDetails->recognition_necklace = (int) $request->has('recognition_necklace');
            $coorDetails->last_updated_by = $lastUpdatedBy;
            $coorDetails->last_updated_date = now();

            $coorDetails->save();

            // Commit transaction
            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return to_route('coordinators.editrecognition', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        }

        return to_route('coordinators.editrecognition', ['id' => $id])->with('success', 'Coordinator profile updated successfully');
    }

    /**
     * View Coordiantor Profile
     */
    public function viewCoordProfile(Request $request): View
    {
        $corDetails = $request->user()->coordinator;
        $corId = $corDetails['id'];
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*', 'st.state_short_name as statename', 'cf.conference_description as confname', 'rg.long_name as regname', 'cp.long_title as position',
                'mo.month_long_name as birthday_month', 'cp3.long_title as display_position', 'cp2.long_title as sec_position', 'cd2.first_name as report_fname', 'cd2.email as report_email', 'cd2.last_name as report_lname')
            ->leftJoin('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')  // Primary Position
            ->leftJoin('coordinator_position as cp2', 'cd.sec_position_id', '=', 'cp2.id')  //Secondary Position
            ->leftJoin('coordinator_position as cp3', 'cd.display_position_id', '=', 'cp3.id')  //Display Position
            ->leftJoin('coordinators as cd2', 'cd.report_id', '=', 'cd2.id') //Supervising Coordinator
            ->leftJoin('month as mo', 'cd.birthday_month_id', '=', 'mo.id')
            ->leftJoin('state as st', 'cd.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
            ->leftJoin('region as rg', 'cd.region_id', '=', 'rg.id')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $corId)
            ->get();

        $corIsActive = $coordinatorDetails[0]->is_active;
        $corIsLeave = $coordinatorDetails[0]->on_leave;
        $corConfId = $coordinatorDetails[0]->conference_id;

        $directReportTo = DB::table('coordinators as cd')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
            ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
            ->where('cd.report_id', '=', $corId)
            ->where('cd.is_active', '=', '1')
            ->get();

        $directChapterTo = DB::table('chapters as ch')
            ->select('ch.id as ch_id', 'ch.name as ch_name', 'st.state_short_name as st_name')
            ->join('state as st', 'ch.state_id', '=', 'st.id')
            ->where('ch.primary_coordinator_id', '=', $corId)
            ->where('ch.is_active', '=', '1')
            ->get();

        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();

        $monthArr = DB::table('month')
            ->select('month.*')
            ->orderBy('id')
            ->get();

        $data = ['coordinatorDetails' => $coordinatorDetails, 'stateArr' => $stateArr, 'monthArr' => $monthArr, 'directReportTo' => $directReportTo, 'directChapterTo' => $directChapterTo,
            'corConfId' => $corConfId];

        return view('coordinators.viewprofile')->with($data);
    }

    /**
     * Edit Coordiantor Profile
     */
    public function editCoordProfile(Request $request): View
    {
        $corDetails = $request->user()->coordinator;
        $corId = $corDetails['id'];
        $coordinatorDetails = DB::table('coordinators as cd')
            ->select('cd.*', 'st.state_short_name as statename', 'cf.conference_description as confname', 'rg.long_name as regname', 'cp.long_title as position',
                'cp3.long_title as display_position', 'cp2.long_title as sec_position', 'cd2.first_name as report_fname', 'cd2.email as report_email', 'cd2.last_name as report_lname')
            ->leftJoin('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')  // Primary Position
            ->leftJoin('coordinator_position as cp2', 'cd.sec_position_id', '=', 'cp2.id')  //Secondary Position
            ->leftJoin('coordinator_position as cp3', 'cd.display_position_id', '=', 'cp3.id')  //Display Position
            ->leftJoin('coordinators as cd2', 'cd.report_id', '=', 'cd2.id') //Supervising Coordinator
            ->leftJoin('month as mo', 'cd.birthday_month_id', '=', 'mo.id')
            ->leftJoin('state as st', 'cd.state', '=', 'st.id')
            ->leftJoin('conference as cf', 'cd.conference_id', '=', 'cf.id')
            ->leftJoin('region as rg', 'cd.region_id', '=', 'rg.id')
            ->where('cd.is_active', '=', '1')
            ->where('cd.id', '=', $corId)
            ->get();

        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();

        $monthArr = DB::table('month')
            ->select('month.*')
            ->orderBy('id')
            ->get();

        $data = ['coordinatorDetails' => $coordinatorDetails, 'stateArr' => $stateArr, 'monthArr' => $monthArr];

        return view('coordinators.profile')->with($data);
    }

    /**
     * Save Coordiantor Profile
     */
    public function updateCoordProfile(Request $request): RedirectResponse
    {
        $corDetails = $request->user()->coordinator;
        $corId = $corDetails->id;
        $lastUpdatedBy = $corDetails->first_name.' '.$corDetails->last_name;

        if ($request->input('cord_fname') != '' && $request->input('cord_lname') != '' && $request->input('cord_email') != '') {
            $corDetail = DB::table('coordinators')
                ->select('id', 'user_id')
                ->where('id', '=', $corId)
                ->first(); // Use first() to get a single result

            try {
                $userId = $corDetail->user_id;

                $user = User::find($userId);
                $user->first_name = $request->input('cord_fname');
                $user->last_name = $request->input('cord_lname');
                $user->email = $request->input('cord_email');
                $user->updated_at = now();
                $user->save();

                DB::table('coordinators')
                    ->where('id', $corId)
                    ->update([
                        'first_name' => $request->input('cord_fname'),
                        'last_name' => $request->input('cord_lname'),
                        'email' => $request->input('cord_email'),
                        'sec_email' => $request->input('cord_sec_email'),
                        'address' => $request->input('cord_addr'),
                        'city' => $request->input('cord_city'),
                        'state' => $request->input('cord_state'),
                        'zip' => $request->input('cord_zip'),
                        'phone' => $request->input('cord_phone'),
                        'alt_phone' => $request->input('cord_altphone'),
                        'birthday_month_id' => $request->input('cord_month'),
                        'birthday_day' => $request->input('cord_day'),
                        'home_chapter' => $request->input('cord_chapter'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                    ]);

                // Commit transaction
                DB::commit();
            } catch (\Exception $e) {
                // Rollback Transaction
                DB::rollback();
                // Log the error
                Log::error($e);

                return redirect()->to('/coordprofile')->with('fail', 'Something went wrong, Please try again.');
            }
        }

        return redirect()->to('/coordprofile')->with('success', 'Coordinator profile updated successfully');
    }
}
