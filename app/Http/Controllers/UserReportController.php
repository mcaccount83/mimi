<?php

namespace App\Http\Controllers;

use App\Enums\UserStatusEnum;
use App\Enums\UserTypeEnum;
use App\Enums\BoardPosition;
use App\Enums\ChapterStatusEnum;
use App\Models\AdminRole;
use App\Models\Boards;
use App\Models\BoardsDisbanded;
use App\Models\BoardsOutgoing;
use App\Models\BoardsPending;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\User;
use App\Models\UserStatus;
use App\Models\UserType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class UserReportController extends Controller implements HasMiddleware
{
    protected $baseChapterController;

    protected $baseCoordinatorController;

    protected $chapterController;

    protected $forumSubscriptionController;

    protected $baseUserController;

    public function __construct(BaseChapterController $baseChapterController, ChapterController $chapterController,
        ForumSubscriptionController $forumSubscriptionController, BaseUserController $baseUserController, BaseCoordinatorController $baseCoordinatorController)
    {
        $this->baseChapterController = $baseChapterController;
        $this->baseCoordinatorController = $baseCoordinatorController;
        $this->chapterController = $chapterController;
        $this->forumSubscriptionController = $forumSubscriptionController;
        $this->baseUserController = $baseUserController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /**
     * User Admins
     */
    public function showUserAdmin(): View
    {
        $adminList = User::where('is_admin', '!=', '0')
            ->where('is_active', UserStatusEnum::ACTIVE)
            ->get();

        $countList = count($adminList);
        $data = ['countList' => $countList, 'adminList' => $adminList];

        return view('userreports.useradmin')->with($data);
    }

    /**
     * List of Duplicate Users
     */
    public function showDuplicate(): View
    {
        $userData = User::where('is_active', UserStatusEnum::ACTIVE)
            ->groupBy('email')
            ->having(DB::raw('count(email)'), '>', 1)
            ->pluck('email');

        $userList = User::where('is_active', UserStatusEnum::ACTIVE)
            ->whereIn('email', $userData)
            ->get();

        $data = ['userList' => $userList];

        return view('userreports.duplicateuser')->with($data);
    }

    /**
     *List of duplicate Board IDs
     */
    public function showDuplicateId(): View
    {
        $boardData = Boards::groupBy('email')
            ->having(DB::raw('count(email)'), '>', 1)
            ->pluck('email');

        $boardList = Boards::with('user.coordinator')
            ->whereIn('email', $boardData)
            ->get();

        $data = ['boardList' => $boardList];

        return view('userreports.duplicateboardid')->with($data);
    }

    /**
     * Active chaters with no president
     */
    public function showNoPresident(): View
    {
        $PresId = DB::table('boards')
            ->where('board_position_id', '1')
            ->pluck('chapter_id');

        $ChapterPres = DB::table('chapters')
            ->where('active_status', '1')
            ->whereNotIn('id', $PresId)
            ->get();

        $data = ['ChapterPres' => $ChapterPres];

        return view('userreports.nopresident')->with($data);
    }

    /**
     * Inactive chapters with no president
     */
    public function showNoPresidentInactive(): View
    {
        $PresId = DB::table('boards_disbanded')
            ->where('board_position_id', '1')
            ->pluck('chapter_id');

        $ChapterPres = DB::table('chapters')
            ->where('active_status', '0')
            ->whereNotIn('id', $PresId)
            ->get();

        $data = ['ChapterPres' => $ChapterPres];

        return view('userreports.nopresidentinactive')->with($data);
    }

    /**
     *Add New Board
     */
    public function addBoardNew(Request $request, $id): View
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chConfId = $baseQuery['chConfId'];
        $chActiveId = $baseQuery['chActiveId'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chPcId = $baseQuery['chPcId'];
        $chPayments = $baseQuery['chPayments'];
        $chDocuments = $baseQuery['chDocuments'];

        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $baseQuery['chapterStatus'];

        $allStates = $baseQuery['allStates'];
        $allCountries = $baseQuery['allCountries'];

        $data = ['id' => $id, 'chActiveId' => $chActiveId, 'stateShortName' => $stateShortName, 'allCountries' => $allCountries, 'allStates' => $allStates,
            'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription, 'chDetails' => $chDetails,
            'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chPayments' => $chPayments, 'chDocuments' => $chDocuments, 'chapterStatus' => $chapterStatus,
            'startMonthName' => $startMonthName,
        ];

        return view('userreports.addnewboard')->with($data);
    }

    /**
     *Save New Board
     */
    public function updateBoardNew(Request $request, $id): RedirectResponse
    {
        $user = $this->baseUserController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $chapter = Chapters::find($id);
        $relation = 'president';
        $positionId = BoardPosition::PRES;
        $prefix = 'ch_pre_';
        $vacant_field = null; // President is never vacant
        $chStatus = $chapter->active_status;

        if ($chapter->active_status == ChapterStatusEnum::ACTIVE){
            $defaultCategories = $this->forumSubscriptionController->defaultCategories();
            $defaultBoardCategories = $defaultCategories['boardCategories'];
            // $status = '1';
        } else{
            $defaultBoardCategories = null;
            // $status = '0';
        }

        DB::beginTransaction();
        try {
            $this->chapterController->createNewBoardMember($chapter, $relation, $positionId, $request, $prefix, $chStatus, $updatedBy, $updatedId, $defaultBoardCategories);

        DB::commit();
            if($chapter->active_status == ChapterStatusEnum::ACTIVE){
                return redirect()->to('/userreports/nopresident')->with('success', 'Chapter created successfully');
            }else{
                return redirect()->to('/userreports/nopresidentinactive')->with('success', 'Chapter created successfully');
            }
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error
            if($chapter->active_status == '1'){
                return redirect()->to('/userreports/nopresident')->with('fail', 'Something went wrong, Please try again...');
            }else{
                return redirect()->to('/userreports/nopresidentinactive')->with('fail', 'Something went wrong, Please try again...');
            }
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * board member with inactive chapter user
     */
    // public function showNoActiveBoardChapter(): View
    // {
    //     $userId = User::with(['board'])
    //         ->whereHas('board') // This ensures only users WITH a board relationship are included
    //         ->where('type_id', UserTypeEnum::BOARD)
    //         ->where('is_active', UserStatusEnum::ACTIVE)
    //         ->pluck('id');

    //     $bdNoChapterList = DB::table('board')
    //         ->where('active_status', '0')
    //         ->whereNotIn('id', $userId)
    //         ->get();

    //     $countList = count($bdNoChapterList);
    //     $data = ['countList' => $countList, 'bdNoChapterList' => $bdNoChapterList];

    //     return view('userreports.noactivechapter')->with($data);
    // }

    public function showUserNoActiveBoard(): View
    {
        $bdNoChapterList = User::whereDoesntHave('board')
            ->where('type_id', UserTypeEnum::BOARD)
            ->where('is_active', UserStatusEnum::ACTIVE)
            ->with(['boardDisbanded', 'boardOutgoing', 'boardPending']) // Eager load all possible relationships
            ->get()
            ->map(function ($user) {
                // Determine which table they're in and what their type_id should be
                if ($user->boardDisbanded) {
                    $user->incorrect_table = 'disbanded';
                    $user->should_be_type = UserTypeEnum::DISBANDED; // or whatever the correct type is
                } elseif ($user->boardOutgoing) {
                    $user->incorrect_table = 'outgoing';
                    $user->should_be_type = UserTypeEnum::OUTGOING;
                } elseif ($user->boardPending) {
                    $user->incorrect_table = 'pending';
                    $user->should_be_type = UserTypeEnum::PENDING;
                } else {
                    $user->incorrect_table = 'none'; // Truly orphaned
                    $user->should_be_type = null;
                }

                return $user;
            });

        $data = [
            'countList' => $bdNoChapterList->count(),
            'bdNoChapterList' => $bdNoChapterList
        ];

        return view('userreports.usernoactiveboard')->with($data);
    }

    public function showUserNoActiveCoord(): View
    {
        $cdNoChapterList = User::whereDoesntHave('coordinator')
            ->where('type_id', UserTypeEnum::COORD)
            ->where('is_active', UserStatusEnum::ACTIVE)
            ->get();

        $data = [
            'countList' => $cdNoChapterList->count(), // Collection method
            'cdNoChapterList' => $cdNoChapterList
        ];

        return view('userreports.usernoactivecoord')->with($data);
    }

    /**
     *Edit User Information
     */
    public function editUserInformation(Request $request, $id): View
    {
        $userDetails = User::find($id);

        $AllUserStatus = UserStatus::all();
            $AllUserType = UserType::all();
            $AllAdminRole = AdminRole::all();

        $data = [
            'id' => $id, 'userDetails' => $userDetails, 'AllUserStatus' => $AllUserStatus, 'AllUserType' => $AllUserType, 'AllAdminRole' => $AllAdminRole,
        ];

        return view('userreports.edituser')->with($data);
    }

    /**
     *Save User Information
     */
    public function updateUserInformation(Request $request, $id): RedirectResponse
    {
        $input = $request->all();

        $user = User::find($id);

        DB::beginTransaction();
        try {
            $user->first_name = $request->input('fname');
            $user->last_name = $request->input('lname');
            $user->email = $request->input('email');
            $user->type_id = $request->input('type');
            $user->is_admin = $request->input('role');
            $user->is_active = $request->input('status');

            $user->save();

        DB::commit();

            return to_route('userreports.edituser', ['id' => $id])->with('success', 'User Details have been updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('userreports.edituser', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * board member with inactive user
     */
    public function showNoActiveBoard(): View
    {
        $noActiveList = User::with(['board'])
            ->whereHas('board') // This ensures only users WITH a board relationship are included
            ->where('type_id', UserTypeEnum::BOARD)
            ->where('is_active', UserStatusEnum::INACTIVE)
            ->get();

        $countList = count($noActiveList);
        $data = ['countList' => $countList, 'noActiveList' => $noActiveList];

        return view('userreports.noactiveboard')->with($data);
    }

     /**
     *Edit User Information
     */
    public function editUserBoardInformation(Request $request, $id): View
    {

        $baseUserQuery = $this->baseUserController->getUserDetailsById($id);
        $userDetails = $baseUserQuery['userDetails'];
        $bdDetails = $baseUserQuery['bdDetails'];
        $bdChapterId = $baseUserQuery['bdChapterId'];
        $bdPosition = $baseUserQuery['bdPosition'];

        $AllUserStatus = $baseUserQuery['AllUserStatus'];
        $AllUserType = $baseUserQuery['AllUserType'];
        $AllAdminRole = $baseUserQuery['AllAdminRole'];

        $baseChapterQuery = $this->baseChapterController->getChapterDetails($bdChapterId);
        $chDetails = $baseChapterQuery['chDetails'];
        $chConfId = $baseChapterQuery['chConfId'];
            $chActiveId = $baseChapterQuery['chActiveId'];
            $stateShortName = $baseChapterQuery['stateShortName'];
            $regionLongName = $baseChapterQuery['regionLongName'];
            $conferenceDescription = $baseChapterQuery['conferenceDescription'];
            $chPcId = $baseChapterQuery['chPcId'];
            $chPayments = $baseChapterQuery['chPayments'];
            $startMonthName = $baseChapterQuery['startMonthName'];

            $allStates = $baseChapterQuery['allStates'];
            $allCountries = $baseChapterQuery['allCountries'];


        $data = [
            'id' => $id, 'userDetails' => $userDetails, 'allStates' => $allStates, 'allCountries' => $allCountries, 'chDetails' => $chDetails,
            'chActiveId' => $chActiveId, 'stateShortName' => $stateShortName, 'allCountries' => $allCountries, 'bdDetails' => $bdDetails,
                'chPcId' => $chPcId, 'allStates' => $allStates, 'chConfId' => $chConfId, 'chPayments' => $chPayments,
                'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription, 'bdPosition' => $bdPosition,
                'startMonthName' => $startMonthName, 'AllUserStatus' => $AllUserStatus, 'AllUserType' => $AllUserType, 'AllAdminRole' => $AllAdminRole,
        ];

        return view('userreports.edituserboard')->with($data);
    }

    /**
     *Save User Information
     */
    public function updateUserBoardInformation(Request $request, $id): RedirectResponse
    {
        $user = $this->baseUserController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $baseUserQuery = $this->baseUserController->getUserDetailsById($id);
        $userTypeId = $baseUserQuery['userTypeId'];

        $input = $request->all();

        $bdUser = User::find($id);

        // Initialize variables
        $bdDetails = null;

        if ($userTypeId  == UserTypeEnum::BOARD) {
            $bdDetails = Boards::where('user_id', $id)->first();
        }
        if ($userTypeId  == UserTypeEnum::DISBANDED) {
            $bdDetails = BoardsDisbanded::where('user_id', $id)->first();
        }
        if ($userTypeId  == UserTypeEnum::PENDING) {
            $bdDetails = BoardsPending::where('user_id', $id)->first();
        }
        if ($userTypeId  == UserTypeEnum::OUTGOING) {
            $bdDetails = BoardsOutgoing::where('user_id', $id)->first();
        }

        DB::beginTransaction();
        try {
            $bdUser->first_name = $request->input('fname');
            $bdUser->last_name = $request->input('lname');
            $bdUser->email = $request->input('email');
            $bdUser->type_id = $request->input('type');
            $bdUser->is_admin = $request->input('role');
            $bdUser->is_active = $request->input('status');

            $bdUser->save();

            $bdDetails->first_name = $request->input('fname');
            $bdDetails->last_name = $request->input('lname');
            $bdDetails->email = $request->input('email');
            $bdDetails->phone = $request->input('phone');
            $bdDetails->city = $request->input('city');
            $bdDetails->state_id = $request->input('state');
            $bdDetails->zip = $request->input('zip');
            $bdDetails->country_id = $request->input('country') ?? '198';
            $bdDetails->updated_by = $updatedBy;
            $bdDetails->updated_id = $updatedId;

            $bdDetails->save();

         DB::commit();

            return to_route('userreports.edituserboard', ['id' => $id])->with('success', 'User Details have been updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('userreports.edituserboard', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

     /**
     *Edit User Information
     */
    public function editUserCoordInformation(Request $request, $id): View
    {

        $baseUserQuery = $this->baseUserController->getUserDetailsById($id);
        $userDetails = $baseUserQuery['userDetails'];
        $bdDetails = $baseUserQuery['bdDetails'];
        $bdChapterId = $baseUserQuery['bdChapterId'];
        $bdPosition = $baseUserQuery['bdPosition'];

        $AllUserStatus = $baseUserQuery['AllUserStatus'];
        $AllUserType = $baseUserQuery['AllUserType'];
        $AllAdminRole = $baseUserQuery['AllAdminRole'];

        $baseCoordQuery = $this->baseCoordinatorController->getCoordinatorDetails($id);
        $cdDetails = $baseCoordQuery['cdDetails'];
            $cdId = $baseCoordQuery['cdId'];
            $cdActiveId = $baseCoordQuery['cdActiveId'];
            $regionLongName = $baseCoordQuery['regionLongName'];
            $conferenceDescription = $baseCoordQuery['conferenceDescription'];
            $cdConfId = $baseCoordQuery['cdConfId'];
            $cdRptId = $baseCoordQuery['cdRptId'];
            $RptFName = $baseCoordQuery['RptFName'];
            $RptLName = $baseCoordQuery['RptLName'];
            $ReportTo = $RptFName.' '.$RptLName;
            $displayPosition = $baseCoordQuery['displayPosition'];
            $mimiPosition = $baseCoordQuery['mimiPosition'];
            $secondaryPosition = $baseCoordQuery['secondaryPosition'];
            $cdLeave = $baseCoordQuery['cdDetails']->on_leave;

            $allStates = $baseCoordQuery['allStates'];
            $allMonths = $baseCoordQuery['allMonths'];
            $allCountries = $baseCoordQuery['allCountries'];

        $data = [
            'id' => $id, 'userDetails' => $userDetails, 'allStates' => $allStates, 'allCountries' => $allCountries, 'cdDetails' => $cdDetails,
            'cdActiveId' => $cdActiveId, 'allCountries' => $allCountries, 'bdDetails' => $bdDetails, 'allMonths' => $allMonths,
                'ReportTo' => $ReportTo, 'allStates' => $allStates, 'cdConfId' => $cdConfId,
                'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription, 'bdPosition' => $bdPosition,
                'displayPosition' => $displayPosition, 'secondaryPosition' => $secondaryPosition, 'cdLeave' => $cdLeave,
                'AllUserStatus' => $AllUserStatus, 'AllUserType' => $AllUserType, 'AllAdminRole' => $AllAdminRole,
        ];

        return view('userreports.editusercoord')->with($data);
    }

    /**
     *Save User Information
     */
    public function updateUserCoordInformation(Request $request, $id): RedirectResponse
    {
        $user = $this->baseUserController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $baseUserQuery = $this->baseUserController->getUserDetailsById($id);
        $userTypeId = $baseUserQuery['userTypeId'];

        $input = $request->all();

        $cdUser = User::find($id);

        $cdDetails = Coordinators::where('user_id', $id)->first();

        DB::beginTransaction();
        try {
            $cdUser->first_name = $request->input('cord_fname');
            $cdUser->last_name = $request->input('cord_lname');
            $cdUser->email = $request->input('cord_email');
            $cdUser->type_id = $request->input('type');
            $cdUser->is_admin = $request->input('role');
            $cdUser->is_active = $request->input('status');

            $cdUser->save();

            $cdDetails->first_name = $request->input('cord_fname');
            $cdDetails->last_name = $request->input('cord_lname');
            $cdDetails->email = $request->input('cord_email');
            $cdDetails->sec_email = $request->input('cord_sec_email');
            $cdDetails->address = $request->input('cord_addr');
            $cdDetails->city = $request->input('cord_city');
            $cdDetails->state_id = $request->input('cord_state');
            $cdDetails->zip = $request->input('cord_zip');
            $cdDetails->phone = $request->input('cord_phone');
            $cdDetails->alt_phone = $request->input('cord_altphone');
            $cdDetails->birthday_month_id = $request->input('cord_month');
            $cdDetails->birthday_day = $request->input('cord_day');
            $cdDetails->updated_by = $updatedBy;
            $cdDetails->updated_id = $updatedId;

            $cdDetails->save();


         DB::commit();

            return to_route('userreports.editusercoord', ['id' => $id])->with('success', 'User Details have been updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('userreports.editusercoord', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Outgoing Board Members
     */
    public function showOutgoingBoard(): View
    {
        $outgoingList = User::with(['boardOutgoing', 'boardOutgoing.chapters'])
            ->where('type_id', UserTypeEnum::OUTGOING)
            ->where('is_active', UserStatusEnum::ACTIVE)
            ->get();

        $countList = count($outgoingList);
        $data = ['countList' => $countList, 'outgoingList' => $outgoingList];

        return view('userreports.outgoingboard')->with($data);
    }

    /**
     * Disbanded Board Members
     */
    public function showDisbandedBoard(): View
    {
        $disbandedList = User::with(['boardDisbanded', 'boardDisbanded.chapters'])
            ->where('type_id', UserTypeEnum::DISBANDED)
            ->where('is_active', UserStatusEnum::ACTIVE)
            ->get();

        $countList = count($disbandedList);
        $data = ['countList' => $countList, 'disbandedList' => $disbandedList];

        return view('userreports.disbandedboard')->with($data);
    }
}
