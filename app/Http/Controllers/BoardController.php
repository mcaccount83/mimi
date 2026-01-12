<?php

namespace App\Http\Controllers;

use App\Enums\BoardPosition;
use App\Enums\UserTypeEnum;
use App\Enums\UserStatusEnum;
use App\Mail\BorUpdateListNoitce;
use App\Mail\ChapProfileUpdatePCNotice;
use App\Mail\EOYElectionReportSubmitted;
use App\Mail\EOYElectionReportThankYou;
use App\Mail\EOYFinancialReportThankYou;
use App\Mail\EOYFinancialSubmitted;
use App\Mail\NewWebsiteReviewNotice;
use App\Mail\ProbationRptSubmittedCCNotice;
use App\Mail\ProbationRptThankYou;
use App\Models\Boards;
use App\Models\BoardsIncoming;
use App\Models\BoardsOutgoing;
use App\Models\Chapters;
use App\Models\DocumentsEOY;
use App\Models\FinancialReport;
use App\Models\ForumCategorySubscription;
use App\Models\ProbationSubmission;
use App\Models\ResourceCategory;
use App\Models\Resources;
use App\Models\User;
use App\Services\LearnDashService;
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
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BoardController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $positionConditionsService;

    protected $baseBoardController;

    protected $baseChapterController;

    protected $forumSubscriptionController;

    protected $pdfController;

    protected $baseMailDataController;

    protected $emailTableController;

    protected $financialReportController;

    protected $learndashService;

    public function __construct(UserController $userController, BaseBoardController $baseBoardController, PDFController $pdfController, PositionConditionsService $positionConditionsService,
        ForumSubscriptionController $forumSubscriptionController, BaseMailDataController $baseMailDataController, FinancialReportController $financialReportController, EmailTableController $emailTableController,
        BaseChapterController $baseChapterController, LearnDashService $learndashService)
    {
        $this->userController = $userController;
        $this->pdfController = $pdfController;
        $this->baseBoardController = $baseBoardController;
        $this->baseChapterController = $baseChapterController;
        $this->forumSubscriptionController = $forumSubscriptionController;
        $this->positionConditionsService = $positionConditionsService;
        $this->baseMailDataController = $baseMailDataController;
        $this->emailTableController = $emailTableController;
        $this->financialReportController = $financialReportController;
        $this->learndashService = $learndashService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndBoard::class,
        ];
    }

    /**
     * Reset Password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            $user = $request->user();

            // Ensure the current password is correct
            if (! Hash::check($request->current_password, $user->password)) {
                return response()->json(['error' => 'Current password is incorrect'], 400);
            }

            // Update the user's password
            $user->password = Hash::make($request->new_password);
            $user->remember_token = null; // Reset the remember token
            $user->save();

            return response()->json(['message' => 'Password updated successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'An error occurred while updating the password.'], 500);
        }
    }

    /**
     * Verify Current Passwor for Reset
     */
    public function checkCurrentPassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'current_password' => 'required',
            ]);

            $user = $request->user();
            $isValid = Hash::check($request->current_password, $user->password);

            return response()->json(['isValid' => $isValid]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'An error occurred while checking the password.'], 500);
        }
    }

    /**
     * View Board Details Board Member Login
     */
    public function editProfile(Request $request, $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userTypeId = $user['userTypeId'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];
        $chPayments = $baseQuery['chPayments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $chDocuments = $baseQuery['chDocuments'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $boardActive = $chEOYDocuments->new_board_active;
        $probationReason = $baseQuery['probationReason'];

        $allProbation = $baseQuery['allProbation'];
        $allWebLinks = $baseQuery['allWebLinks'];
        $allStates = $baseQuery['allStates'];
        $allCountries = $baseQuery['allCountries'];

        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        if ($userTypeId == UserTypeEnum::COORD) {
            $bdPositionId = '1';
            $borDetails = $PresDetails;
        } else {
            $bdPositionId = $user['bdPositionId'];
            $borDetails = $user['bdDetails'];
        }

        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentMonth = $dateOptions['currentMonth'];
        $start_month = $chDetails->start_month_id;
        $next_renewal_year = $chDetails->next_renewal_year;
        $due_date = Carbon::create($next_renewal_year, $start_month, 1);

        $data = ['chDetails' => $chDetails, 'chFinancialReport' => $chFinancialReport, 'stateShortName' => $stateShortName, 'allStates' => $allStates, 'allWebLinks' => $allWebLinks,
            'PresDetails' => $PresDetails, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'allCountries' => $allCountries,
            'startMonthName' => $startMonthName, 'thisMonth' => $currentMonth, 'due_date' => $due_date, 'userTypeId' => $userTypeId, 'allProbation' => $allProbation, 'userAdmin' => $userAdmin,
            'chDocuments' => $chDocuments, 'probationReason' => $probationReason, 'chPayments' => $chPayments, 'chEOYDocuments' => $chEOYDocuments,
            'bdPositionId' => $bdPositionId, 'borDetails' => $borDetails, 'boardActive' => $boardActive,
        ];

        return view('boards.profile')->with($data);
    }

    /**
     *Update Chapter Board Information
     */
    private function updateBoardMember($chapter, $position, $requestData, $updatedBy, $updatedId, $defaultBoardCategories)
    {
        $positionConfig = [
            'president' => [
                'relation' => 'president',
                'position_id' => BoardPosition::PRES,
                'prefix' => 'ch_pre_',
                'vacant_field' => null, // President is never vacant
            ],
            'avp' => [
                'relation' => 'avp',
                'position_id' => BoardPosition::AVP,
                'prefix' => 'ch_avp_',
                'vacant_field' => 'AVPVacant',
            ],
            'mvp' => [
                'relation' => 'mvp',
                'position_id' => BoardPosition::MVP,
                'prefix' => 'ch_mvp_',
                'vacant_field' => 'MVPVacant',
            ],
            'treasurer' => [
                'relation' => 'treasurer',
                'position_id' => BoardPosition::TRS,
                'prefix' => 'ch_trs_',
                'vacant_field' => 'TreasVacant',
            ],
            'secretary' => [
                'relation' => 'secretary',
                'position_id' => BoardPosition::SEC,
                'prefix' => 'ch_sec_',
                'vacant_field' => 'SecVacant',
            ],
        ];

        if (! isset($positionConfig[$position])) {
            return;
        }

        $config = $positionConfig[$position];
        $relation = $config['relation'];
        $prefix = $config['prefix'];
        $positionId = $config['position_id'];
        $vacantField = $config['vacant_field'];

        $firstName = $requestData->input($prefix.'fname');
        $lastName = $requestData->input($prefix.'lname');
        $email = $requestData->input($prefix.'email');
        $isVacant = $vacantField ? $requestData->input($vacantField) == 'on' : false;

        if ($position == 'president' && (! $firstName || ! $lastName || ! $email)) {
            return;
        }

        // Load current board member with user
        $chapterWithRelation = Chapters::with($relation)->find($chapter->id);
        $boardMember = $chapterWithRelation->$relation;

        if ($boardMember) {
            $user = $boardMember->user;

            if ($isVacant) {
                if ($user) {
                    $this->updateUserToOutgoing($user);
                    $this->removeActiveBoardMember($user);
                }

            } else {
                // Check if replacing person entirely (name + email changed)
                $nameChanged = ($user->first_name != $firstName || $user->last_name != $lastName);
                $emailChanged = ($user->email != $email);

                if ($nameChanged && $emailChanged) {
                    $this->updateUserToOutgoing($user);
                    $this->removeActiveBoardMember($user);
                    // Create new board member in same position
                    $this->createNewBoardMember($chapterWithRelation, $relation, $positionId, $requestData, $prefix, $updatedBy, $updatedId, $defaultBoardCategories);

                } else {
                    // Same user – update fields
                    $this->updateExistingBoardMember($user, $boardMember, $requestData, $prefix, $updatedBy, $updatedId, $defaultBoardCategories);
                }
            }
        } else {
            // No current board member
            if (! $isVacant) {
                $this->createNewBoardMember($chapterWithRelation, $relation, $positionId, $requestData, $prefix, $updatedBy, $updatedId, $defaultBoardCategories);
            }
        }
    }

    private function updateUserToOutgoing($user)
    {
        User::where('id', $user->id)->update([
            'type_id' => UserTypeEnum::OUTGOING,
            'is_active' => UserStatusEnum::INACTIVE,
        ]);
    }

    private function createOutgoingBoardMember($user, $bdDetails, $updatedBy, $updatedId)
    {
        BoardsOutgoing::updateOrCreate(
            [
                'user_id' => $user->id,
                'chapter_id' => $bdDetails->chapter_id,
                'board_position_id' => $bdDetails->board_position_id,
            ],
            [
                'first_name' => $bdDetails->first_name,
                'last_name' => $bdDetails->last_name,
                'email' => $bdDetails->email,
                'phone' => $bdDetails->phone,
                'street_address' => $bdDetails->street_address,
                'city' => $bdDetails->city,
                'state_id' => $bdDetails->state_id,
                'zip' => $bdDetails->zip,
                'country_id' => $bdDetails->country_id,
                'updated_by' => $updatedBy,
            ]
        );
    }

    private function removeActiveBoardMember($user)
    {
        Boards::where('user_id', $user->id)->delete();
        ForumCategorySubscription::where('user_id', $user->id)->delete();
    }

    private function updateExistingBoardMember($user, $boardMember, $requestData, $prefix, $updatedBy, $updatedId, $defaultBoardCategories)
    {
        $firstName = $requestData->input($prefix.'fname');
        $lastName = $requestData->input($prefix.'lname');
        $email = $requestData->input($prefix.'email');
        $stateId = $requestData->input($prefix.'state');
        $countryId = $requestData->input($prefix.'country') ?? '198';

        $user->update([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
        ]);

        $boardMember->update([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'street_address' => $requestData->input($prefix.'street'),
            'city' => $requestData->input($prefix.'city'),
            'state_id' => $stateId,
            'zip' => $requestData->input($prefix.'zip'),
            'country_id' => $countryId,
            'phone' => $requestData->input($prefix.'phone'),
            'updated_by' => $updatedBy,
            'updated_id' => $updatedId,
        ]);

        // Ensure forum subscriptions exist
        foreach ($defaultBoardCategories as $categoryId) {
            $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                ->where('category_id', $categoryId)
                ->first();
            if (! $existingSubscription) {
                ForumCategorySubscription::create([
                    'user_id' => $user->id,
                    'category_id' => $categoryId,
                ]);
            }
        }
    }

    private function createNewBoardMember($chapter, $relation, $positionId, $requestData, $prefix, $updatedBy, $updatedId, $defaultBoardCategories)
    {
        $firstName = $requestData->input($prefix.'fname');
        $lastName = $requestData->input($prefix.'lname');
        $email = $requestData->input($prefix.'email');
        $stateId = $requestData->input($prefix.'state');
        $countryId = $requestData->input($prefix.'country') ?? '198';

        // Check if user with this email already exists and is not on another active board
        $existingUser = User::where('email', $email)
            ->where('type_id', '!=', UserTypeEnum::BOARD)
            ->first();

        if ($existingUser) {
            // Update existing user to board type
            $existingUser->update([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'type_id' => UserTypeEnum::BOARD,
                'is_active' => UserStatusEnum::ACTIVE,
            ]);
            $user = $existingUser;
        } else {
            // Create new user
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => Hash::make('TempPass4You'),
                'type_id' => UserTypeEnum::BOARD,
                'is_active' => UserStatusEnum::ACTIVE,
            ]);
        }

        // Create new board member record
        $chapter->$relation()->create([
            'user_id' => $user->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'board_position_id' => $positionId,
            'street_address' => $requestData->input($prefix.'street'),
            'city' => $requestData->input($prefix.'city'),
            'state_id' => $stateId,
            'zip' => $requestData->input($prefix.'zip'),
            'country_id' => $countryId,
            'phone' => $requestData->input($prefix.'phone'),
            'updated_by' => $updatedBy,
            'updated_id' => $updatedId,
        ]);

        // Add forum subscriptions
        foreach ($defaultBoardCategories as $categoryId) {
            ForumCategorySubscription::updateOrCreate([
                'user_id' => $user->id,
                'category_id' => $categoryId,
            ]);
        }
    }

    public function updateProfile(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $baseQuery = $this->baseBoardController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $pcDetails = $baseQuery['pcDetails'];

        $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($id);
        $PresDetails = $baseActiveBoardQuery['PresDetails'];
        $AVPDetails = $baseActiveBoardQuery['AVPDetails'];
        $MVPDetails = $baseActiveBoardQuery['MVPDetails'];
        $TRSDetails = $baseActiveBoardQuery['TRSDetails'];
        $SECDetails = $baseActiveBoardQuery['SECDetails'];

        $input = $request->all();
        $webStatusPre = $input['ch_hid_webstatus'];

        // Handle web status - allow null values
        $ch_webstatus = $request->input('ch_webstatus') ?: $request->input('ch_hid_webstatus');
        // Only convert to 0 if the website is not null but status is empty
        if (! is_null($request->input('ch_website')) && empty(trim($ch_webstatus))) {
            $ch_webstatus = 0;
        }

        // Handle website URL
        $website = $request->input('ch_website');
        // Only add http:// if the website field is not null or empty
        if (! is_null($website) && ! empty(trim($website))) {
            if (! str_starts_with($website, 'http://') && ! str_starts_with($website, 'https://')) {
                $website = 'http://'.$website;
            }
        }

        $chapter = Chapters::find($id);

        $defaultCategories = $this->forumSubscriptionController->defaultCategories();
        $defaultBoardCategories = $defaultCategories['boardCategories'];

        DB::beginTransaction();
        try {
            $chapter->inquiries_contact = $request->input('ch_inqemailcontact');
            $chapter->email = $request->input('ch_email');
            $chapter->po_box = $request->input('ch_pobox');
            $chapter->website_url = $website;
            $chapter->website_status = $ch_webstatus;
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            // Update all board positions
            $this->updateBoardMember($chapter, 'president', $request, $updatedBy, $updatedId, $defaultBoardCategories);
            $this->updateBoardMember($chapter, 'avp', $request, $updatedBy, $updatedId, $defaultBoardCategories);
            $this->updateBoardMember($chapter, 'mvp', $request, $updatedBy, $updatedId, $defaultBoardCategories);
            $this->updateBoardMember($chapter, 'treasurer', $request, $updatedBy, $updatedId, $defaultBoardCategories);
            $this->updateBoardMember($chapter, 'secretary', $request, $updatedBy, $updatedId, $defaultBoardCategories);

            // Update Chapter MailData//
            $baseQueryUpd = $this->baseBoardController->getChapterDetails($id);
            $chDetailsUpd = $baseQueryUpd['chDetails'];
            $stateShortName = $baseQueryUpd['stateShortName'];
            $chConfId = $baseQueryUpd['chConfId'];
            $chPcId = $baseQueryUpd['chPcId'];
            $webStatusUpd = $ch_webstatus;

            $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($id);
            $PresDetailsUpd = $baseActiveBoardQuery['PresDetails'];
            $AVPDetailsUpd = $baseActiveBoardQuery['AVPDetails'];
            $MVPDetailsUpd = $baseActiveBoardQuery['MVPDetails'];
            $TRSDetailsUpd = $baseActiveBoardQuery['TRSDetails'];
            $SECDetailsUpd = $baseActiveBoardQuery['SECDetails'];

            $emailListChap = $baseQueryUpd['emailListChap'];  // Full Board
            $emailListCoord = $baseQueryUpd['emailListCoord'];  // Full Coordinaor List
            $emailCC = $baseQueryUpd['emailCC'];  // CC Email
            $pcDetailsUpd = $baseQueryUpd['chDetails']->primaryCoordinator;
            $pcEmail = $pcDetailsUpd->email;  // PC Email
            $adminEmail = $this->positionConditionsService->getAdminEmail();
            $einAdmin = $adminEmail['ein_admin'];  // EIN Coor Email

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetailsUpd, $stateShortName),
                // $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getPresData($PresDetails),
                $this->baseMailDataController->getPresUpdatedData($PresDetailsUpd),
                $this->baseMailDataController->getChapterUpdatedData($chDetailsUpd, $pcDetailsUpd),
                $this->baseMailDataController->getBoardEmail($PresDetails, $AVPDetails, $MVPDetails, $TRSDetails, $SECDetails),
                $this->baseMailDataController->getBoardUpdEmail($PresDetailsUpd, $AVPDetailsUpd, $MVPDetailsUpd, $TRSDetailsUpd, $SECDetailsUpd),
                [
                    'ch_website_url' => $website,
                ]
            );

            $mailTableListAdmin = $this->emailTableController->createListAdminUpdateBoardTable($mailData);
            $mailTablePrimary = $this->emailTableController->createPrimaryUpdateBoardTable($mailData);

            $mailData = array_merge($mailData, [
                'mailTableListAdmin' => $mailTableListAdmin,
                'mailTablePrimary' => $mailTablePrimary,
            ]);

            if ($PresDetailsUpd->email != $PresDetails->email || $PresDetailsUpd->first_name != $PresDetails->first_name || $PresDetailsUpd->last_name != $PresDetails->last_name ||
                    $AVPDetailsUpd->email != $AVPDetails->email || $AVPDetailsUpd->first_name != $AVPDetails->first_name || $AVPDetailsUpd->last_name != $AVPDetails->last_name ||
                    $MVPDetailsUpd->email != $MVPDetails->email || $MVPDetailsUpd->first_name != $MVPDetails->first_name || $MVPDetailsUpd->last_name != $MVPDetails->last_name ||
                    $TRSDetailsUpd->email != $TRSDetails->email || $TRSDetailsUpd->first_name != $TRSDetails->first_name || $TRSDetailsUpd->last_name != $TRSDetails->last_name ||
                    $SECDetailsUpd->email != $SECDetails->email || $SECDetailsUpd->first_name != $SECDetails->first_name || $SECDetailsUpd->last_name != $SECDetails->last_name) {
                Mail::to($pcEmail)
                    ->queue(new ChapProfileUpdatePCNotice($mailData));

                // Mail::to($emailPC)
                //     ->queue(new BorUpdatePCNotice($mailData));
            }

            $adminEmail = $this->positionConditionsService->getAdminEmail();
            $listAdmin = $adminEmail['list_admin'];

            if ($PresDetailsUpd->email != $PresDetails->email || $AVPDetailsUpd->email != $AVPDetails->email || $MVPDetailsUpd->email != $MVPDetails->email ||
                    $TRSDetailsUpd->email != $TRSDetails->email || $SECDetailsUpd->email != $SECDetails->email) {
                Mail::to($listAdmin)
                    ->queue(new BorUpdateListNoitce($mailData));
            }

            // Website URL Change Notification//
            if ($webStatusUpd != $webStatusPre) {
                if ($webStatusUpd == 2) {
                    Mail::to($emailCC)
                        ->queue(new NewWebsiteReviewNotice($mailData));
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Chapter has successfully updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/home')->with('fail', 'Something went wrong, Please try again');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Show Manual Order Form All Board Members
     */
    public function editManualOrderForm(Request $request, $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userTypeId = $user['userTypeId'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $chActiveId = $baseQuery['chActiveId'];
        $stateShortName = $baseQuery['stateShortName'];
        $allStates = $baseQuery['allStates'];
        $allCountries = $baseQuery['allCountries'];

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'userTypeId' => $userTypeId, 'userAdmin' => $userAdmin, 'chActiveId' => $chActiveId,
            'allStates' => $allStates, 'allCountries' => $allCountries,
        ];

        return view('boards.manualorder')->with($data);
    }

    /**
     * Show Probation Submission Form All Board Members
     */
    public function editProbationSubmission(Request $request, $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userTypeId = $user['userTypeId'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];

        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentMonth = $dateOptions['currentMonth'];
        $start_month = $chDetails->start_month_id;
        $next_renewal_year = $chDetails->next_renewal_year;
        $due_date = Carbon::create($next_renewal_year, $start_month, 1);
        $rangeEndDate = $due_date->copy()->subMonth()->endOfMonth();
        $rangeStartDate = $rangeEndDate->copy()->startOfMonth()->subYear()->addMonth();

        $rangeStartDateFormatted = $rangeStartDate->format('m/d/Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m/d/Y');

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'userAdmin' => $userAdmin,
            'startMonthName' => $startMonthName, 'endRange' => $rangeEndDateFormatted, 'startRange' => $rangeStartDateFormatted,
            'thisMonth' => $currentMonth, 'due_date' => $due_date, 'userTypeId' => $userTypeId,
        ];

        return view('boards.probation')->with($data);
    }

    /**
     * Update Probation Submission Form All Board Members
     */
    public function updateProbationSubmission(Request $request, $chId): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];
        $emailCC = $baseQuery['emailCC'];

        $input = $request->all();

        $chapter = Chapters::find($chId);
        $probation = ProbationSubmission::find($chId);

        DB::beginTransaction();
        try {
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            if ($probation) {
                $probation->update([
                    $probation->q1_dues = $input['q1_dues'] ?? null,
                    $probation->q1_benefit = $input['q1_benefit'] ?? null,
                    $probation->q2_dues = $input['q2_dues'] ?? null,
                    $probation->q2_benefit = $input['q2_benefit'] ?? null,
                    $probation->q3_dues = $input['q3_dues'] ?? null,
                    $probation->q3_benefit = $input['q3_benefit'] ?? null,
                    $probation->q4_dues = $input['q4_dues'] ?? null,
                    $probation->q4_benefit = $input['q4_benefit'] ?? null,
                ]);
            }

            $mailTable = $this->emailTableController->createProbationSubmissionTable($input);

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getProbationData($input),
                [
                    'mailTable' => $mailTable,
                ]
            );

            Mail::to($emailCC)
                ->queue(new ProbationRptSubmittedCCNotice($mailData));

            Mail::to($emailListChap)
                ->queue(new ProbationRptThankYou($mailData));

            DB::commit();

            return redirect()->back()->with('success', 'Quarterly Report has been Submitted');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Show Chater Resources
     */
    public function viewResources(Request $request, $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userTypeId = $user['userTypeId'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];

        $resources = Resources::with('resourceCategory')->get();
        $resourceCategories = ResourceCategory::all();

        $data = ['stateShortName' => $stateShortName, 'chDetails' => $chDetails, 'resources' => $resources, 'resourceCategories' => $resourceCategories,
            'userTypeId' => $userTypeId, 'userAdmin' => $userAdmin,
        ];

        return view('boards.resources')->with($data);
    }

    /**
     * Show EOY BoardInfo All Board Members
     */
    public function editBoardReport(Request $request, $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userTypeId = $user['userTypeId'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];

        $allWebLinks = $baseQuery['allWebLinks'];
        $allStates = $baseQuery['allStates'];
        $allCountries = $baseQuery['allCountries'];

        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        $data = ['stateShortName' => $stateShortName, 'startMonthName' => $startMonthName, 'allStates' => $allStates, 'SECDetails' => $SECDetails, 'userAdmin' => $userAdmin,
            'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'PresDetails' => $PresDetails, 'chDetails' => $chDetails, 'userTypeId' => $userTypeId,
            'allWebLinks' => $allWebLinks, 'allCountries' => $allCountries,
        ];

        return view('boards.boardinfo')->with($data);
    }

    /**
     * Update EOY BoardInfo All Board Members
     */
    public function updateBoardReport(Request $request, $chId): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];
        $emailCC = $baseQuery['emailCC'];

        $input = $request->all();

        // Handle web status - allow null values
        $ch_webstatus = $request->input('ch_webstatus') ?: $request->input('ch_hid_webstatus');
        if (! is_null($request->input('ch_website')) && empty(trim($ch_webstatus))) {
            $ch_webstatus = 0;
        }

        // Handle website URL
        $website = $request->input('ch_website');
        if (! is_null($website) && ! empty(trim($website))) {
            if (! str_starts_with($website, 'http://') && ! str_starts_with($website, 'https://')) {
                $website = 'http://'.$website;
            }
        }

        $boundaryStatus = $request->input('BoundaryStatus');
        $issue_note = $request->input('BoundaryIssue');
        if ($boundaryStatus == 0) {
            $issue_note = '';
        }

        $chapter = Chapters::find($chId);
        $documentsEOY = DocumentsEOY::find($chId);

        DB::beginTransaction();
        try {
            $chapter->email = $request->input('ch_inqemailcontact');
            $chapter->inquiries_contact = $request->input('ch_email') ?? null;
            $chapter->boundary_issues = $request->input('BoundaryStatus');
            $chapter->boundary_issue_notes = $issue_note;
            $chapter->website_url = $website;
            $chapter->website_status = $ch_webstatus;
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            $documentsEOY->new_board_submitted = 1;
            $documentsEOY->save();

            // President Info - Handle separately since it's required
            if ($request->input('ch_pre_fname') != '' && $request->input('ch_pre_lname') != '' && $request->input('ch_pre_email') != '') {
                $PREDetails = BoardsIncoming::where('chapter_id', $chId)
                    ->where('board_position_id', BoardPosition::PRES)
                    ->get();
                $presId = $request->input('presID');

                if (count($PREDetails) != 0) {
                    BoardsIncoming::where('id', $presId)
                        ->update($this->financialReportController->getBoardMemberData($request, 'ch_pre_', $updatedBy, $updatedId, $userId));
                } else {
                    BoardsIncoming::create(array_merge(
                        ['chapter_id' => $chId, 'board_position_id' => BoardPosition::PRES],
                        $this->financialReportController->getBoardMemberData($request, 'ch_pre_', $updatedBy, $updatedId, $userId)
                    ));
                }
            }

            // Handle other board positions
            $this->financialReportController->updateIncomingBoardMember($chId, BoardPosition::AVP, 'ch_avp_', 'AVPVacant', 'avpID', $request, $updatedBy, $updatedId, $userId);
            $this->financialReportController->updateIncomingBoardMember($chId, BoardPosition::MVP, 'ch_mvp_', 'MVPVacant', 'mvpID', $request, $updatedBy, $updatedId, $userId);
            $this->financialReportController->updateIncomingBoardMember($chId, BoardPosition::TRS, 'ch_trs_', 'TreasVacant', 'trsID', $request, $updatedBy, $updatedId, $userId);
            $this->financialReportController->updateIncomingBoardMember($chId, BoardPosition::SEC, 'ch_sec_', 'SecVacant', 'secID', $request, $updatedBy, $updatedId, $userId);

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
            );

            $EOYOptions = $this->positionConditionsService->getEOYOptions();
            $displayLIVE = $EOYOptions['displayLIVE'];  // Months 5-12 Live Activation
            $displayBoardRptLIVE = $EOYOptions['displayBoardRptLIVE'];  // Months 5-9 Live Activation

            if ($displayBoardRptLIVE) {
                $message = 'Board info has been Submitted';

                Mail::to($emailCC)
                    ->queue(new EOYElectionReportSubmitted($mailData));

                Mail::to($emailListChap)
                    ->queue(new EOYElectionReportThankYou($mailData));
            }

            if ($displayLIVE) {
                $status = $this->financialReportController->activateSingleBoard($request, $chId);

                if ($status == 'success') {
                    $message = 'Board info has been submitted and activated successfully';
                }
            }

            DB::commit();

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        } finally {
            DB::disconnect();
        }
    }

    /**
     * Show EOY Financial Report All Board Members
     */
    public function editFinancialReport(Request $request, $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userTypeId = $user['userTypeId'];
        $userName = $loggedInName = $user['userName'];
        $userEmail = $user['userEmail'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $chActiveId = $baseQuery['chActiveId'];
        $stateShortName = $baseQuery['stateShortName'];
        // $chDocuments = $baseQuery['chDocuments'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $awards = $baseQuery['awards'];
        $allAwards = $baseQuery['allAwards'];

        $resources = Resources::with('resourceCategory')->get();
        $resourceCategories = ResourceCategory::all();

        $data = ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userTypeId' => $userTypeId, 'userAdmin' => $userAdmin,
            'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'stateShortName' => $stateShortName,
            'awards' => $awards, 'allAwards' => $allAwards, 'chActiveId' => $chActiveId, 'resourceCategories' => $resourceCategories, 'chEOYDocuments' => $chEOYDocuments,
        ];

        return view('boards.financial')->with($data);

    }

    /**
     * Save EOY Financial Report All Board Members
     */
    public function updateFinancialReport(Request $request, $chId): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userName = $user['userName'];
        $userEmail = $user['userEmail'];
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $input = $request->all();
        $reportReceived = $input['submitted'] ?? null;

        $financialReport = FinancialReport::find($chId);
        $documentsEOY = DocumentsEOY::find($chId);
        $chapter = Chapters::find($chId);

        DB::beginTransaction();
        try {
            $this->financialReportController->saveAccordionFields($financialReport, $input);

            if ($reportReceived == 1) {
                $financialReport->completed_name = $userName;
                $financialReport->completed_email = $userEmail;
                $financialReport->submitted = Carbon::now();
            }

            $financialReport->save();

            if ($reportReceived == 1) {
                $documentsEOY->financial_report_received = 1;
                $documentsEOY->report_received = Carbon::now();
                $documentsEOY->report_extension = null;
                $documentsEOY->save();
            }

            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            $baseQuery = $this->baseBoardController->getChapterDetails($chId);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $chDocuments = $baseQuery['chDocuments'];
            $chEOYDocuments = $baseQuery['chEOYDocuments'];
            $chFinancialReport = $baseQuery['chFinancialReport'];
            $emailListChap = $baseQuery['emailListChap'];
            $emailListCoord = $baseQuery['emailListCoord'];
            $pcDetails = $baseQuery['pcDetails'];
            $emailCC = $baseQuery['emailCC'];
            $cc_id = $baseQuery['cc_id'];
            $reviewerEmail = $baseQuery['reviewerEmail'];

            $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($chId);
            $PresDetails = $baseActiveBoardQuery['PresDetails'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getPCData($pcDetails),
                $this->baseMailDataController->getPresData($PresDetails),
                $this->baseMailDataController->getFinancialReportData($chEOYDocuments, $chFinancialReport, $reviewer_email_message = null),
            );

            $EOYOptions = $this->positionConditionsService->getEOYOptions();
            $fiscalYear = $EOYOptions['fiscalYear'];

            if ($reportReceived == 1) {
                $pdfPath = $this->pdfController->saveFinancialReport($request, $chId, $PresDetails);   // Generate and Send the PDF
                Mail::to($userEmail)
                    ->cc($emailListChap)
                    ->queue(new EOYFinancialReportThankYou($mailData, $pdfPath, $fiscalYear));

                if ($chFinancialReport->reviewer_id == null) {
                    DB::update('UPDATE financial_report SET reviewer_id = ? where chapter_id = ?', [$cc_id, $chId]);
                    Mail::to($emailCC)
                        ->queue(new EOYFinancialSubmitted($mailData, $pdfPath, $fiscalYear));
                }

                if ($chFinancialReport->reviewer_id != null) {
                    Mail::to($reviewerEmail)
                        ->queue(new EOYFinancialSubmitted($mailData, $pdfPath, $fiscalYear));
                }
            }

            DB::commit();
            if ($reportReceived == 1) {
                return redirect()->back()->with('success', 'Report has been successfully Submitted');
            } else {
                return redirect()->back()->with('success', 'Report has been successfully updated');
            }
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    public function getRosterfile(): BinaryFileResponse
    {
        $filename = 'roster_template.xlsx';

        $file_path = '/home/momsclub/public_html/mimi/storage/app/public';

        return Response::download($file_path, $filename, [
            'Content-Length: '.filesize($file_path),
        ]);
    }

    /**
     * View eLearning Courses
     */
    public function viewELearning(Request $request, $chId): View
    {
        // $user = $this->userController->loadUserInformation($request);
        $user = User::find($request->user()->id);
        $userTypeId = $user['userTypeId'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];

        $boardCourses = $this->learndashService->getCoursesForUserType('board');

        // Add auto-login URLs to each course
        foreach ($boardCourses as &$boardCourse) {
            $boardCourse['auto_login_url'] = $this->learndashService->getAutoLoginUrl($boardCourse, $user);
        }

        // Group by category - store both name and slug
        $boardCoursesByCategory = collect($boardCourses)->groupBy(function ($course) {
            return $course['categories'][0]['slug'] ?? 'uncategorized';
        })->map(function ($courses, $slug) {
            return [
                'name' => $courses->first()['categories'][0]['name'] ?? ucfirst(str_replace('-', ' ', $slug)),
                'courses' => $courses,
            ];
        });

        $data = [
            'chDetails' => $chDetails,
            'stateShortName' => $stateShortName,
            'userTypeId' => $userTypeId,
            'boardCourses' => $boardCourses,
            'boardCoursesByCategory' => $boardCoursesByCategory,
        ];

        return view('boards.elearning')->with($data);
    }

    public function redirectToCourse($courseId, Request $request): RedirectResponse
    {
        $token = $request->query('token');
        $courseUrl = urldecode($request->query('course_url'));

        // $wpAutoLoginUrl = "https://momsclub.org/elearning/wp-json/auth/v1/auto-login?" . http_build_query([
        $wpAutoLoginUrl = 'https://momsclub.org/elearning/wp-json/auth/v1/auto-login?'.http_build_query([
            'token' => $token,
            'course_url' => $courseUrl,
        ]);

        // return redirect($wpAutoLoginUrl);
        return redirect()->to($wpAutoLoginUrl);
    }
}
