<?php

namespace App\Http\Controllers;

use App\Mail\BigSisterWelcome;
use App\Mail\CoordinatorRetireAdmin;
use App\Models\Chapters;
use App\Models\CoordinatorRecognition;
use App\Models\Coordinators;
use App\Models\CoordinatorTree;
use App\Models\ForumCategorySubscription;
use App\Models\Month;
use App\Models\Region;
use App\Models\State;
use App\Models\Country;
use App\Models\User;
use App\Services\PositionConditionsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CoordinatorController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $positionConditionsService;

    protected $baseCoordinatorController;

    protected $forumSubscriptionController;

    public function __construct(UserController $userController, BaseCoordinatorController $baseCoordinatorController, ForumSubscriptionController $forumSubscriptionController,
        PositionConditionsService $positionConditionsService,)
    {

        $this->userController = $userController;
        $this->baseCoordinatorController = $baseCoordinatorController;
        $this->forumSubscriptionController = $forumSubscriptionController;
                        $this->positionConditionsService = $positionConditionsService;

    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /**
     * Active Coordiantor List
     */
    public function showCoordinators(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $coordinatorList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $emailListCord = $coordinatorList->pluck('email')->filter()->implode(';');

        $countList = count($coordinatorList);
        $data = ['countList' => $countList, 'coordinatorList' => $coordinatorList, 'checkBoxStatus' => $checkBoxStatus, 'emailListCord' => $emailListCord];

        return view('coordinators.coordlist')->with($data);
    }

    /**
     * Retired Coorinators List
     */
    public function showRetiredCoordinator(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getRetiredBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $coordinatorList = $baseQuery['query']->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('coordinators.coordretired')->with($data);
    }

    /**
     * International Coordinators List
     */
    public function showIntCoordinator(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseCoordinatorController->getActiveInternationalBaseQuery($coorId);
        $coordinatorList = $baseQuery['query']->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('international.intcoord')->with($data);
    }

    /**
     * International Retired Coordinator List
     */
    public function showIntCoordinatorRetired(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseCoordinatorController->getRetiredInternationalBaseQuery($coorId);
        $coordinatorList = $baseQuery['query']->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('international.intcoordretired')->with($data);
    }

    /**
     * Add New Coordiantor
     */
    public function addCoordNew(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userName = $user['user_name'];
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $conference = $user['user_conference'];
        $confLongName = $conference->conference_name;
        $confDescription = $conference->conference_description;
        $regId = $user['user_regId'];
        $region = $user['user_region'];
        $regLongName = $region->long_name;

        $allStates = State::all();  // Full List for Dropdown Menu
        $allCountries = Country::all();  // Full List for Dropdown Menu
        $allRegions = Region::with('conference')  // Full List for Dropdown Menu based on Conference
            ->where('conference_id', $confId)
            ->get();
        $allMonths = Month::all();  // Full List for Dropdown Menu

        $data = ['allStates' => $allStates, 'allMonths' => $allMonths, 'allRegions' => $allRegions, 'userName' => $userName, 'coorId' => $coorId, 'allCountries' => $allCountries,
            'confId' => $confId, 'regId' => $regId, 'confLongName' => $confLongName, 'regLongName' => $regLongName, 'confDescription' => $confDescription,
        ];

        return view('coordinators.editnew')->with($data);
    }

    /**
     * Update New Coordiantor
     */
    public function updateCoordNew(Request $request): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $confId = $user['user_confId'];
        $reportsTo = $user['user_coorId'];
        $userLayerId = $user['user_layerId'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $new_layer_id = $userLayerId + 1;
        $input = $request->all();

        $defaultCategories = $this->forumSubscriptionController->defaultCategories();
        $defaultCoordinatorCategories = $defaultCategories['coordinatorCategories'];

        DB::beginTransaction();
        try {
            $userId = DB::table('users')->insertGetId(
                ['first_name' => $input['cord_fname'],
                    'last_name' => $input['cord_lname'],
                    'email' => $input['cord_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'coordinator',
                    'is_admin' => 0,
                    'is_active' => 1]
            );

            $cordId = DB::table('coordinators')->insertGetId(
                ['user_id' => $userId,
                    'conference_id' => $confId,
                    'region_id' => $input['cord_region'],
                    'layer_id' => $new_layer_id,
                    'first_name' => $input['cord_fname'],
                    'last_name' => $input['cord_lname'],
                    'position_id' => 1,
                    'display_position_id' => 1,
                    'email' => $input['cord_email'],
                    'sec_email' => $input['cord_sec_email'],
                    'report_id' => $reportsTo,
                    'address' => $input['cord_addr'],
                    'city' => $input['cord_city'],
                    'state_id' => $input['cord_state'],
                    'zip' => $input['cord_zip'],
                    'country_id' => $input['cord_country'] ?? '198',
                    'phone' => $input['cord_phone'],
                    'alt_phone' => $input['cord_altphone'],
                    'birthday_month_id' => $input['cord_month'],
                    'birthday_day' => $input['cord_day'],
                    'home_chapter' => $input['cord_chapter'],
                    'coordinator_start_date' => $lastupdatedDate,
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => $lastupdatedDate,
                    'is_active' => 1]
            );

            CoordinatorRecognition::create([
                'coordinator_id' => $cordId,
            ]);

            $reportingUpline = CoordinatorTree::where('coordinator_id', $reportsTo)->first();  // Get reporting coordinator's upline data

            $treeData = [
                'coordinator_id' => $cordId,
            ];

            // Use the reporting coordinator's upline data
            for ($i = 0; $i < $new_layer_id; $i++) {
                $layerKey = "layer$i";
                $treeData[$layerKey] = $reportingUpline->{$layerKey};
            }

            $treeData["layer$new_layer_id"] = $cordId;  // Place new coordinator at their layer

            // Set remaining layers to null
            for ($i = $new_layer_id + 1; $i <= 8; $i++) {
                $treeData["layer$i"] = null;
            }

            $coordTree = CoordinatorTree::insert($treeData);

            foreach ($defaultCoordinatorCategories as $categoryId) {
                ForumCategorySubscription::create([
                    'user_id' => $userId,
                    'category_id' => $categoryId,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/')->with('fail', 'Something went wrong, Please try again..');
        }

        return redirect()->to('/coordinator/coordlist')->with('success', 'Coordinator created successfully.');
    }

    /**
     * View Coordiantor Detais
     */
    public function viewCoordDetails(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $confId = $user['user_confId'];

        $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($id);
        $cdDetails = $baseQuery['cdDetails'];
        $cdId = $baseQuery['cdId'];
        $cdPositionid = $baseQuery['cdPositionid'];
        $cdIsActive = $baseQuery['cdIsActive'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $cdConfId = $baseQuery['cdConfId'];
        $RptFName = $baseQuery['RptFName'];
        $RptLName = $baseQuery['RptLName'];
        $ReportTo = $RptFName.' '.$RptLName;
        $displayPosition = $baseQuery['displayPosition'];
        $mimiPosition = $baseQuery['mimiPosition'];
        $secondaryPosition = $baseQuery['secondaryPosition'];
        $cdLeave = $baseQuery['cdDetails']->on_leave;
        $cdUserAdmin = $baseQuery['cdUserAdmin'];
        $cdAdminRole = $baseQuery['cdAdminRole'];

        $now = Carbon::now();
        $threeMonthsAgo = $now->copy()->subMonths(3);
        $startDate = $cdDetails->coordinator_start_date;
        $startDate = Carbon::parse($startDate);

        $drList = Coordinators::with('displayPosition')
            ->where('report_id', $cdId)  // DirectReport Harcoaded List
            ->where('is_active', 1)
            ->get();

        $chList = Chapters::with('state')
            ->where('primary_coordinator_id', $cdId)  // Chapter Harcoaded List
            ->where('active_status', 1)
            ->get();

        $data = ['cdDetails' => $cdDetails, 'cdConfId' => $cdConfId, 'conferenceDescription' => $conferenceDescription, 'regionLongName' => $regionLongName,
            'cdIsActive' => $cdIsActive, 'confId' => $confId, 'cdLeave' => $cdLeave, 'ReportTo' => $ReportTo, 'cdUserAdmin' => $cdUserAdmin,
            'drList' => $drList, 'chList' => $chList, 'displayPosition' => $displayPosition, 'mimiPosition' => $mimiPosition, 'startDate' => $startDate,
            'secondaryPosition' => $secondaryPosition, 'threeMonthsAgo' => $threeMonthsAgo, 'cdPositionid' => $cdPositionid, 'cdAdminRole' => $cdAdminRole,
        ];

        return view('coordinators.view')->with($data);
    }

    /**
     * Function for sending New Chapter Email with Attachments
     */
    public function sendBigSisterEmail(Request $request): JsonResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetailsUser = $user->coordinator;
        $cdIdUser = $cdDetailsUser->id;
        $cdNameUser = $cdDetailsUser->first_name.' '.$cdDetailsUser->last_name;
        $cdEmailUser = $cdDetailsUser->email;
        $cdPositionUser = $cdDetailsUser->displayPosition->long_title;
        $cdConfIdUser = $cdDetailsUser->conference_id;
        $cdCoferenceDescriptionUser = $cdDetailsUser->conference->conference_description;

        $input = $request->all();
        $id = $input['chapterid'];

        // Load Chapter MailData//
        $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($id);
        $cdDetails = $baseQuery['cdDetails'];
        $cdId = $baseQuery['cdId'];
        $cdName = $cdDetails->first_name.' '.$cdDetails->last_name;
        $cdEmail = $cdDetails->email;
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $cdConfId = $baseQuery['cdConfId'];
        $cdRptId = $baseQuery['cdRptId'];
        $RptFName = $baseQuery['RptFName'];
        $RptLName = $baseQuery['RptLName'];
        $ReportTo = $baseQuery['RptFName'].' '.$baseQuery['RptLName'];
        $ReportEmail = $cdDetails->reportsTo?->email;
        $ReportPhone = $cdDetails->reportsTo?->phone;

        $chList = Chapters::with('state')
            ->where('primary_coordinator_id', $cdId)  // Chapter Harcoaded List
            ->where('is_active', 1)
            ->get();

        try {
            DB::beginTransaction();

            $mailData = [
                'conf_name' => $conferenceDescription,
                'reg_name' => $regionLongName,
                'cdName' => $cdName,
                'cor_fname' => $RptFName,
                'cor_lname' => $RptLName,
                'cor_name' => $ReportTo,
                'cor_email' => $ReportEmail,
                'cor_phone' => $ReportPhone,
                'email' => $cdEmail,
                'chapters' => $chList,
                'userName' => $cdNameUser,
                'userEmail' => $cdEmailUser,
                'positionTitle' => $cdPositionUser,
                'conf' => $cdConfIdUser,
                'conf_name' => $cdCoferenceDescriptionUser,
            ];

            Mail::to($cdEmail)
                ->cc($ReportEmail, $cdEmailUser)
                ->queue(new BigSisterWelcome($mailData));

            // Commit the transaction
            DB::commit();

            $message = 'Big Sister Welcome email successfully sent';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('coordinators.view', ['id' => $id]),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('coordinators.view', ['id' => $id]),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Change birthday card sent date
     */
    public function updateCardSent(Request $request): JsonResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetailsUser = $user->coordinator;
        $cdIdUser = $cdDetailsUser->id;
        $lastUpdatedBy = $cdDetailsUser->first_name.' '.$cdDetailsUser->last_name;

        $coordId = $request->input('id');
        $cardSent = $request->input('card_sent');

        $coordinators = Coordinators::find($coordId);

        DB::beginTransaction();
        try {
            $coordinators->card_sent = $cardSent;
            $coordinators->last_updated_by = $lastUpdatedBy;
            $coordinators->last_updated_date = date('Y-m-d');

            $coordinators->save();

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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetailsUser = $user->coordinator;
        $cdIdUser = $cdDetailsUser->id;
        $lastUpdatedBy = $cdDetailsUser->first_name.' '.$cdDetailsUser->last_name;

        $input = $request->all();
        $coordId = $input['coord_id'];

        $coordinators = Coordinators::find($coordId);
        $coordUserId = $coordinators->user_id;

        DB::beginTransaction();
        try {
            $coordinators->on_leave = 1;
            $coordinators->last_updated_by = $lastUpdatedBy;
            $coordinators->last_updated_date = date('Y-m-d');
            $coordinators->save();

            ForumCategorySubscription::where('user_id', $coordUserId)->delete();

            DB::commit();

            $message = 'Coordinator successfully put on leave and removed from all subscriptions';

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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetailsUser = $user->coordinator;
        $cdIdUser = $cdDetailsUser->id;
        $lastUpdatedBy = $cdDetailsUser->first_name.' '.$cdDetailsUser->last_name;

        $input = $request->all();
        $coordId = $input['coord_id'];

        $coordinators = Coordinators::find($coordId);
        $coordUserId = $coordinators->user_id;

        $defaultCategories = $this->forumSubscriptionController->defaultCategories();
        $defaultCoordinatorCategories = $defaultCategories['coordinatorCategories'];

        DB::beginTransaction();
        try {
            $coordinators->on_leave = 0;
            $coordinators->leave_date = null;
            $coordinators->last_updated_by = $lastUpdatedBy;
            $coordinators->last_updated_date = date('Y-m-d');
            $coordinators->save();

            foreach ($defaultCoordinatorCategories as $categoryId) {
                ForumCategorySubscription::create([
                    'user_id' => $coordUserId,
                    'category_id' => $categoryId,
                ]);
            }

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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetailsUser = $user->coordinator;
        $cdIdUser = $cdDetailsUser->id;
        $lastUpdatedBy = $cdDetailsUser->first_name.' '.$cdDetailsUser->last_name;

        $input = $request->all();
        $coordId = $input['coord_id'];
        $retireReason = $input['reason_retired'];

        $coordinator = Coordinators::find($coordId);
        $cdUserId = $coordinator->user_id;
        $user = User::find($cdUserId);

        DB::beginTransaction();
        try {
            $coordinator->is_active = 0;
            $coordinator->reason_retired = $retireReason;
            $coordinator->zapped_date = date('Y-m-d');
            $coordinator->last_updated_by = $lastUpdatedBy;
            $coordinator->last_updated_date = date('Y-m-d');

            $coordinator->save();

            $user->is_active = 0;
            $user->updated_at = date('Y-m-d');

            $user->save();

            ForumCategorySubscription::where('user_id', $cdUserId)->delete();

            // Get Mail Data
            $coordName = $coordinator->fisrt_name.' '.$coordinator->last_name;
            $coordConf = $coordinator->conference_id;
            $email = $coordinator->email;

            $mailData = [
                'coordName' => $coordName,
                'confNumber' => $coordConf,
                'email' => $email,
            ];

            // $to_email = 'jackie.mchenry@momsclub.org';
            $adminEmail = $this->positionConditionsService->getAdminEmail();
            $gsuiteAdmin = $adminEmail['gsuite_admin'];  // Gsuite Coor Email

            Mail::to($gsuiteAdmin, 'MOMS Club')
                ->queue(new CoordinatorRetireAdmin($mailData));

            DB::commit();

            $message = 'Coordinator successfully retired';

            return response()->json(['status' => 'success', 'message' => $message, 'redirect' => route('coordinators.view', ['id' => $coordId])]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            return response()->json(['status' => 'error', 'message' => $message, 'redirect' => route('coordinators.view', ['id' => $coordId])]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Function for Retiring a Coordinator
     */
    public function updateUnRetire(Request $request): JsonResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetailsUser = $user->coordinator;
        $cdIdUser = $cdDetailsUser->id;
        $lastUpdatedBy = $cdDetailsUser->first_name.' '.$cdDetailsUser->last_name;

        $input = $request->all();
        $coordId = $input['coord_id'];

        $coordinator = Coordinators::find($coordId);
        $cdUserId = $coordinator->user_id;
        $user = User::find($cdUserId);

        $defaultCategories = $this->forumSubscriptionController->defaultCategories();
        $defaultCoordinatorCategories = $defaultCategories['coordinatorCategories'];

        DB::beginTransaction();
        try {
            $coordinator->is_active = 1;
            $coordinator->reason_retired = null;
            $coordinator->zapped_date = null;
            $coordinator->last_updated_by = $lastUpdatedBy;
            $coordinator->last_updated_date = date('Y-m-d');

            $coordinator->save();

            $user->is_active = 1;
            $user->updated_at = date('Y-m-d');

            $user->save();

            foreach ($defaultCoordinatorCategories as $categoryId) {
                ForumCategorySubscription::create([
                    'user_id' => $cdUserId,
                    'category_id' => $categoryId,
                ]);
            }

            // Get Mail Data
            $coordName = $coordinator->fisrt_name.' '.$coordinator->last_name;
            $coordConf = $coordinator->conference_id;
            $email = $coordinator->email;

            // $mailData = [
            //     'coordName' => $coordName,
            //     'confNumber' => $coordConf,
            //     'email' => $email,
            // ];

            // $to_email = 'jackie.mchenry@momsclub.org';
            // $adminEmail = $this->positionConditionsService->getAdminEmail();
            // $gsuiteAdmin = $adminEmail['gsuite_admin'];  // Gsuite Coor Email

            // Mail::to($gsuiteAdmin, 'MOMS Club')
            //     ->queue(new CoordinatorRetireAdmin($mailData));

            DB::commit();

            $message = 'Coordinator successfully reactivated';

            return response()->json(['status' => 'success', 'message' => $message, 'redirect' => route('coordinators.view', ['id' => $coordId])]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback transaction on exception
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            return response()->json(['status' => 'error', 'message' => $message, 'redirect' => route('coordinators.view', ['id' => $coordId])]);
        }
    }

    /**
     * Edit Coordiantor Role
     */
    public function editCoordRole(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetailsUser = $user->coordinator;
        $cdIdUser = $cdDetailsUser->id;
        $cdConfIdUser = $cdDetailsUser->conference_id;

        $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($id);
        $cdDetails = $baseQuery['cdDetails'];
        $cdId = $baseQuery['cdId'];
        $cdPositionid = $baseQuery['cdPositionid'];
        $cdIsActive = $baseQuery['cdIsActive'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $cdConfId = $baseQuery['cdConfId'];
        $cdRegId = $baseQuery['cdConfId'];
        $cdLeave = $baseQuery['cdDetails']->on_leave;
        $cdUserAdmin = $baseQuery['cdUserAdmin'];
        $cdAdminRole = $baseQuery['cdAdminRole'];

        $allRegions = $baseQuery['allRegions'];
        $allPositions = $baseQuery['allPositions'];
        $allAdminRoles = $baseQuery['allAdminRoles'];

        $rcDetails = $baseQuery['rcDetails'];  // ReportsTo Dropdown List

        $drList = Coordinators::where('report_id', $cdId)  // DirectReport Harcoaded List
            ->where('is_active', 1)
            ->get();

        $drRowCount = count($drList);

        // $drDetails = $baseQuery['drDetails'];  // DirectReport Selection List
        $drDetails = Coordinators::where('conference_id', $cdConfId)  // DirectReport Dropdown List
            ->whereBetween('position_id', [1, 6])
            ->where('position_id', '<=', $cdPositionid)
            ->where('id', '!=', $cdId)
            ->where('is_active', 1)
            ->where('on_leave', '!=', '1')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $drOptions = Coordinators::where('conference_id', $cdConfId)  // DirectReport Dropdown List
            ->whereBetween('position_id', [1, 7])
            ->where('is_active', 1)
            ->where('on_leave', '!=', '1')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $chList = Chapters::with('state')
            ->where('primary_coordinator_id', $cdId)  // Chapter Harcoaded List
            ->where('active_status', 1)
            ->get();

        $chDetails = Chapters::with('state')   // Chapter Selection List
            ->where('conference_id', $cdConfId)
            ->where('active_status', 1)
            ->orderBy('state_id')
            ->orderBy('name')
            ->get();

        $pcOptions = Coordinators::where('conference_id', $cdConfId)  // Primary Coordinator Dropdown List
            ->whereBetween('position_id', [1, 7])
            ->where('is_active', 1)
            ->where('on_leave', '!=', '1')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $pcRowCount = count($pcOptions);

        $data = ['cdDetails' => $cdDetails, 'cdConfId' => $cdConfId, 'drOptions' => $drOptions, 'rcDetails' => $rcDetails, 'allRegions' => $allRegions,
            'chList' => $chList, 'drList' => $drList, 'cdIsActive' => $cdIsActive, 'cdConfIdUser' => $cdConfIdUser, 'userId' => $userId, 'cdLeave' => $cdLeave,
            'pcOptions' => $pcOptions, 'cdId' => $cdId, 'allPositions' => $allPositions, 'chDetails' => $chDetails, 'drDetails' => $drDetails, 'cdUserAdmin' => $cdUserAdmin,
            'conferenceDescription' => $conferenceDescription, 'regionLongName' => $regionLongName, 'pcRowCount' => $pcRowCount, 'drRowCount' => $drRowCount,
            'allAdminRoles' => $allAdminRoles, 'cdAdminRole' => $cdAdminRole,
        ];

        return view('coordinators.editrole')->with($data);
    }

    /**
     * Reassign Chapter
     */
    public function ReassignChapter(Request $request, $chapter_id, $coordinator_id, $check_changed = false)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetailsUser = $user->coordinator;
        $cdIdUser = $cdDetailsUser->id;
        $lastUpdatedBy = $cdDetailsUser->first_name.' '.$cdDetailsUser->last_name;

        if ($check_changed) {
            $checkPrimaryIdArr = Chapters::find($chapter_id);
            $current_primary = $checkPrimaryIdArr->primary_coordinator_id;
            if ($current_primary == $coordinator_id) {
                return true;
            }
        }

        $chapter = Chapters::find($chapter_id);

        DB::beginTransaction();
        try {
            $chapter->primary_coordinator_id = $coordinator_id;
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');

            $chapter->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return false;
        }

    }

    /**
     * Reassign Coordinator
     */
    public function ReassignCoordinator(Request $request, $coordinator_id, $new_coordinator_id, $check_changed = false)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetailsUser = $user->coordinator;
        $cdIdUser = $cdDetailsUser->id;
        $lastUpdatedBy = $cdDetailsUser->first_name.' '.$cdDetailsUser->last_name;

        if ($check_changed) {
            $checkReportIdArr = Coordinators::find($coordinator_id);
            $current_report = $checkReportIdArr->report_id;
            if ($current_report == $new_coordinator_id) {
                return true;
            }
        }

        $newCoordinator = Coordinators::find($new_coordinator_id);  // Find new layer
        $new_layer_id = $newCoordinator->layer_id + 1;

        $coordinator = Coordinators::find($coordinator_id);

        DB::beginTransaction();
        try {
            // Update their main report ID & layer
            $coordinator->report_id = $new_coordinator_id;
            $coordinator->layer_id = $new_layer_id;
            $coordinator->last_updated_by = $lastUpdatedBy;
            $coordinator->last_updated_date = date('Y-m-d H:i:s');

            $coordinator->save();

            $reportingUpline = CoordinatorTree::where('coordinator_id', $new_coordinator_id)->first();  // Get reporting coordinator's upline data

            $treeData = [
                'coordinator_id' => $coordinator_id,
            ];

            // Use the reporting coordinator's upline data
            for ($i = 0; $i < $new_layer_id; $i++) {
                $layerKey = "layer$i";
                $treeData[$layerKey] = $reportingUpline->{$layerKey};
            }

            $treeData["layer$new_layer_id"] = $coordinator_id;  // Place new coordinator at their layer

            // Set remaining layers to null
            for ($i = $new_layer_id + 1; $i <= 8; $i++) {
                $treeData["layer$i"] = null;
            }

            $coordTree = CoordinatorTree::where('coordinator_id', $coordinator_id)
                ->update($treeData);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return false;
        }
    }

    /**
     * Update Role, Chapters and Coordinators
     */
    public function updateCoordRole(Request $request, $id): RedirectResponse
    {
        $admin = $this->userController->loadUserInformation($request);
        $userAdmin = $admin['userAdmin'];

        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetailsUser = $user->coordinator;
        $cdIdUser = $cdDetailsUser->id;
        $lastUpdatedBy = $cdDetailsUser->first_name.' '.$cdDetailsUser->last_name;

        // /Reassign Direct Report Coordinators that Changed
        $rowcountCord = $_POST['CoordinatorCount'];
        for ($i = 0; $i < $rowcountCord; $i++) {
            $new_coordinator_field = 'Report'.$i;
            $new_coordinator_id = $_POST[$new_coordinator_field];

            $coordinator_field = 'CoordinatorIDRow'.$i;
            $coordinator_id = $_POST[$coordinator_field];

            $this->ReassignCoordinator($request, $coordinator_id, $new_coordinator_id, true);
        }

        // Reassign Primary Coordinatory Chapters that Changed
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

        // Reassign Report To / Direct Supervisor that Changed
        $coordinator_id = $request->input('coordinator_id');
        $new_coordinator_id = $request->input('cord_report_pc');
        $this->ReassignCoordinator($request, $coordinator_id, $new_coordinator_id, true);

        $coordinator = Coordinators::find($id);
        $coorUserId = $coordinator->user_id;
        $userIsAdmin = User::find($coorUserId);

        DB::beginTransaction();
        try {
            $coordinator->position_id = $request->input('cord_pos');
            $coordinator->display_position_id = $request->input('cord_disp_pos');
            $coordinator->last_promoted = $request->input('CoordinatorPromoteDate');
            $coordinator->last_updated_by = $lastUpdatedBy;
            $coordinator->last_updated_date = date('Y-m-d H:i:s');

            $coordinator->save();

            if ($request->has('cord_sec_pos') && is_array($request->cord_sec_pos)) {
                // Filter out any empty values
                $validPositionIds = array_filter($request->cord_sec_pos, function ($value) {
                    return ! empty($value) && is_numeric($value);
                });

                if (! empty($validPositionIds)) {
                    $coordinator->secondaryPosition()->sync($validPositionIds);
                } else {
                    $coordinator->secondaryPosition()->detach();
                }
            } else {
                $coordinator->secondaryPosition()->detach();
            }

            if ($userAdmin) {
                $userIsAdmin->is_admin = $request->input('is_admin');
            } else {
                $userIsAdmin->is_admin = $request->input('OldAdmin');
            }

            $userIsAdmin->save();

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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetailsUser = $user->coordinator;
        $cdIdUser = $cdDetailsUser->id;

        $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($id);
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
        $cdUserAdmin = $baseQuery['cdUserAdmin'];
        $cdAdminRole = $baseQuery['cdAdminRole'];

        $allStates = $baseQuery['allStates'];
        $allMonths = $baseQuery['allMonths'];
        $allCountries = $baseQuery['allCountries'];

        $data = ['cdDetails' => $cdDetails, 'conferenceDescription' => $conferenceDescription, 'regionLongName' => $regionLongName,
            'cdIsActive' => $cdIsActive, 'cdLeave' => $cdLeave, 'ReportTo' => $ReportTo, 'cdUserAdmin' => $cdUserAdmin,
            'displayPosition' => $displayPosition, 'mimiPosition' => $mimiPosition, 'secondaryPosition' => $secondaryPosition,
            'allStates' => $allStates, 'allMonths' => $allMonths, 'cdAdminRole' => $cdAdminRole, 'allCountries' => $allCountries,
        ];

        return view('coordinators.editdetails')->with($data);
    }

    /**
     * Save Coordiantor Details
     */
    public function updateCoordDetails(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetailsUser = $user->coordinator;
        $cdIdUser = $cdDetailsUser->id;
        $lastUpdatedBy = $cdDetailsUser->first_name.' '.$cdDetailsUser->last_name;

        $coordinator = Coordinators::find($id);
        $cdUserId = $coordinator->user_id;
        $user = User::find($cdUserId);

        DB::beginTransaction();
        try {
            $user->first_name = $request->input('cord_fname');
            $user->last_name = $request->input('cord_lname');
            $user->email = $request->input('cord_email');
            $user->updated_at = now();

            $user->save();

            $coordinator->first_name = $request->input('cord_fname');
            $coordinator->last_name = $request->input('cord_lname');
            $coordinator->email = $request->input('cord_email');
            $coordinator->sec_email = $request->input('cord_sec_email');
            $coordinator->address = $request->input('cord_addr');
            $coordinator->city = $request->input('cord_city');
            $coordinator->state_id = $request->input('cord_state');
            $coordinator->zip = $request->input('cord_zip');
            $coordinator->phone = $request->input('cord_phone');
            $coordinator->alt_phone = $request->input('cord_altphone');
            $coordinator->birthday_month_id = $request->input('cord_month');
            $coordinator->birthday_day = $request->input('cord_day');
            $coordinator->home_chapter = $request->input('cord_chapter');
            $coordinator->last_updated_by = $lastUpdatedBy;
            $coordinator->last_updated_date = now();

            $coordinator->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            echo $e->getMessage();
            exit();
            DB::rollback();
            // Log the error
            Log::error($e);

            return to_route('coordinators.editdetails', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        }

        return to_route('coordinators.editdetails', ['id' => $id])->with('success', 'Coordinator profile updated successfully');
    }

    /**
     * Edit Coordiantor Details
     */
    public function editCoordRecognition(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetailsUser = $user->coordinator;
        $cdIdUser = $cdDetailsUser->id;

        $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($id);
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
        $cdUserAdmin = $baseQuery['cdUserAdmin'];
        $cdAdminRole = $baseQuery['cdAdminRole'];

        $allRecognitionGifts = $baseQuery['allRecognitionGifts'];

        $data = ['cdDetails' => $cdDetails, 'conferenceDescription' => $conferenceDescription, 'regionLongName' => $regionLongName,
            'cdIsActive' => $cdIsActive, 'cdLeave' => $cdLeave, 'ReportTo' => $ReportTo, 'cdUserAdmin' => $cdUserAdmin,
            'displayPosition' => $displayPosition, 'mimiPosition' => $mimiPosition, 'secondaryPosition' => $secondaryPosition, 'cdAdminRole' => $cdAdminRole,
            'allRecognitionGifts' => $allRecognitionGifts,
        ];

        return view('coordinators.editrecognition')->with($data);
    }

    /**
     * Save Coordiantor Details
     */
    public function updateCoordRecognition(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetailsUser = $user->coordinator;
        $cdIdUser = $cdDetailsUser->id;
        $lastUpdatedBy = $cdDetailsUser->first_name.' '.$cdDetailsUser->last_name;

        $coordinator = Coordinators::find($id);
        $coordinatorRecognition = CoordinatorRecognition::find($id);

        DB::beginTransaction();
        try {

            $coordinator->last_updated_by = $lastUpdatedBy;
            $coordinator->last_updated_date = now();

            $coordinator->save();

            $coordinatorRecognition->recognition0 = $request->input('recognition0');
            $coordinatorRecognition->recognition1 = $request->input('recognition1');
            $coordinatorRecognition->recognition2 = $request->input('recognition2');
            $coordinatorRecognition->recognition3 = $request->input('recognition3');
            $coordinatorRecognition->recognition4 = $request->input('recognition4');
            $coordinatorRecognition->recognition5 = $request->input('recognition5');
            $coordinatorRecognition->recognition6 = $request->input('recognition6');
            $coordinatorRecognition->recognition7 = $request->input('recognition7');
            $coordinatorRecognition->recognition8 = $request->input('recognition8');
            $coordinatorRecognition->recognition9 = $request->input('recognition9');
            $coordinatorRecognition->year0 = $request->input('year0');
            $coordinatorRecognition->year1 = $request->input('year1');
            $coordinatorRecognition->year2 = $request->input('year2');
            $coordinatorRecognition->year3 = $request->input('year3');
            $coordinatorRecognition->year4 = $request->input('year4');
            $coordinatorRecognition->year5 = $request->input('year5');
            $coordinatorRecognition->year6 = $request->input('year6');
            $coordinatorRecognition->year7 = $request->input('year7');
            $coordinatorRecognition->year8 = $request->input('year8');
            $coordinatorRecognition->year9 = $request->input('year9');
            $coordinatorRecognition->recognition_toptier = $request->input('recognition_toptier');
            $coordinatorRecognition->recognition_necklace = (int) $request->has('recognition_necklace');
            $coordinatorRecognition->recognition_pin = (int) $request->has('recognition_pin');

            $coordinatorRecognition->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('coordinators.editrecognition', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        }

        return to_route('coordinators.editrecognition', ['id' => $id])->with('success', 'Coordinator profile updated successfully');
    }

    /**
     * View Coordiantor Profile
     */
    public function viewCoordProfile(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
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

        $drList = Coordinators::with('displayPosition')
            ->where('report_id', $cdId)  // DirectReport Harcoaded List
            ->where('is_active', 1)
            ->get();

        $chList = Chapters::with('state')
            ->where('primary_coordinator_id', $cdId)  // Chapter Harcoaded List
            ->where('active_status', 1)
            ->get();

        $data = ['cdDetails' => $cdDetails, 'drList' => $drList, 'chList' => $chList,
            'cdConfId' => $cdConfId, 'conferenceDescription' => $conferenceDescription, 'regionLongName' => $regionLongName,
            'displayPosition' => $displayPosition, 'secondaryPosition' => $secondaryPosition, 'ReportTo' => $ReportTo,
        ];

        return view('coordinators.viewprofile')->with($data);
    }

    /**
     * Edit Coordiantor Profile/Dashboard
     */
    public function editCoordProfile(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
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

        $allStates = $baseQuery['allStates'];
        $allCountries = $baseQuery['allCountries'];
        $allMonths = $baseQuery['allMonths'];

        $data = ['cdDetails' => $cdDetails, 'allStates' => $allStates, 'allMonths' => $allMonths, 'allCountries' => $allCountries,
            'cdConfId' => $cdConfId, 'conferenceDescription' => $conferenceDescription, 'regionLongName' => $regionLongName,
            'displayPosition' => $displayPosition, 'secondaryPosition' => $secondaryPosition, 'ReportTo' => $ReportTo,
        ];

        return view('coordinators.profile')->with($data);
    }

    /**
     * Save Coordiantor Profile
     */
    public function updateCoordProfile(Request $request): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;

        $coordinator = Coordinators::find($cdId);
        $cdUserId = $coordinator->user_id;
        $user = User::find($cdUserId);

        try {
            $user->first_name = $request->input('cord_fname');
            $user->last_name = $request->input('cord_lname');
            $user->email = $request->input('cord_email');
            $user->updated_at = now();

            $user->save();

            $coordinator->first_name = $request->input('cord_fname');
            $coordinator->last_name = $request->input('cord_lname');
            $coordinator->email = $request->input('cord_email');
            $coordinator->sec_email = $request->input('cord_sec_email');
            $coordinator->address = $request->input('cord_addr');
            $coordinator->city = $request->input('cord_city');
            $coordinator->state_id = $request->input('cord_state');
            $coordinator->zip = $request->input('cord_zip');
            $coordinator->phone = $request->input('cord_phone');
            $coordinator->alt_phone = $request->input('cord_altphone');
            $coordinator->birthday_month_id = $request->input('cord_month');
            $coordinator->birthday_day = $request->input('cord_day');
            $coordinator->home_chapter = $request->input('cord_chapter');
            $coordinator->last_updated_by = $lastUpdatedBy;
            $coordinator->last_updated_date = now();

            $coordinator->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/coordprofile')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/coordprofile')->with('success', 'Coordinator profile updated successfully');
    }
}
