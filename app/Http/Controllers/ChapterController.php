<?php

namespace App\Http\Controllers;

use App\Enums\ActiveStatusEnum;
use App\Enums\BoardPosition;
use App\Enums\ChapterCheckbox;
use App\Enums\ChapterStatusEnum;
use App\Enums\CoordinatorPosition;
use App\Enums\OperatingStatusEnum;
use App\Enums\UserStatusEnum;
use App\Enums\UserTypeEnum;
use App\Mail\BorUpdateListNoitce;
use App\Mail\BorUpdatePCNotice;
use App\Mail\ChapDetailsUpdatePCNotice;
use App\Mail\DisbandChapListNotice;
use App\Mail\NewChapApproveAdminNotice;
use App\Mail\NewChapApproveGSuiteNotice;
use App\Mail\NewChapListNotice;
use App\Mail\NewChapPCNotice;
use App\Mail\NewWebsiteApproveChapNotice;
use App\Mail\NewWebsiteApproveCoordNotice;
use App\Mail\NewWebsiteReviewNotice;
use App\Mail\PCChangeChapNotice;
use App\Mail\PCChangePCNotice;
use App\Mail\UnZapChapListNotice;
use App\Mail\WebsiteUpdatePCNotice;
use App\Models\Boards;
use App\Models\BoardsDisbanded;
use App\Models\BoardsOutgoing;
use App\Models\BoardsPending;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\Country;
use App\Models\DisbandedChecklist;
use App\Models\Documents;
use App\Models\DocumentsEOY;
use App\Models\FinancialReport;
use App\Models\FinancialReportFinal;
use App\Models\ForumCategorySubscription;
use App\Models\Payments;
use App\Models\ProbationSubmission;
use App\Models\Region;
use App\Models\Resources;
use App\Models\State;
use App\Models\Status;
use App\Models\User;
use App\Models\Website;
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

class ChapterController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $positionConditionsService;

    protected $pdfController;

    protected $baseChapterController;

    protected $forumSubscriptionController;

    protected $baseMailDataController;

    protected $emailTableController;

    protected $emailController;

    public function __construct(UserController $userController, PDFController $pdfController, BaseChapterController $baseChapterController,
        ForumSubscriptionController $forumSubscriptionController, BaseMailDataController $baseMailDataController, EmailController $emailController,
        EmailTableController $emailTableController, PositionConditionsService $positionConditionsService, )
    {
        $this->userController = $userController;
        $this->pdfController = $pdfController;
        $this->baseChapterController = $baseChapterController;
        $this->forumSubscriptionController = $forumSubscriptionController;
        $this->baseMailDataController = $baseMailDataController;
        $this->emailTableController = $emailTableController;
        $this->emailController = $emailController;
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
     * Display the Pending New chapter list mapped with login coordinator
     */
    public function showPendingChapters(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];
        $userName = $user['userName'];
        $userPosition = $user['cdPosition'];
        $userConfName = $user['confName'];
        $userConfDesc = $user['confDesc'];

        // Pending chapters
        $baseQuery = $this->baseChapterController->getBaseQuery(2, $coorId, $confId, $regId, $positionId, $secPositionId);

        $chapterList = $baseQuery['query']->get();
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $countList = $chapterList->count();
        $data = [
            'countList' => $countList,
            'chapterList' => $chapterList,
            'checkBox5Status' => $checkBox5Status,
            'userName' => $userName,
            'userPosition' => $userPosition,
            'userConfName' => $userConfName,
            'userConfDesc' => $userConfDesc,
        ];

        return view('chapters.chaplistpending')->with($data);
    }

    /**
     * Display the Pending New chapter list mapped with login coordinator
     */
    public function showNotApprovedChapters(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        // Not Approved chapters
        $baseQuery = $this->baseChapterController->getBaseQuery(3, $coorId, $confId, $regId, $positionId, $secPositionId);

        $chapterList = $baseQuery['query']->get();
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $countList = $chapterList->count();
        $data = [
            'countList' => $countList,
            'chapterList' => $chapterList,
            'checkBox5Status' => $checkBox5Status,
        ];

        return view('chapters.chaplistdeclined')->with($data);
    }

    /**
     * Display the Active chapter list mapped with login coordinator
     */
    public function showChapters(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];
        $userName = $user['userName'];
        $userPosition = $user['cdPosition'];
        $userConfName = $user['confName'];
        $userConfDesc = $user['confDesc'];

        // Active chapters
        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $countList = $chapterList->count();
        $data = [
            'countList' => $countList,
            'chapterList' => $chapterList,
            'checkBoxStatus' => $checkBoxStatus,
            'checkBox3Status' => $checkBox3Status,
            'checkBox5Status' => $checkBox5Status,
            'userName' => $userName,
            'userPosition' => $userPosition,
            'userConfName' => $userConfName,
            'userConfDesc' => $userConfDesc,
        ];

        return view('chapters.chaplist')->with($data);
    }

    /**
     * Display the Zapped chapter list mapped with Conference Region
     */
    public function showZappedChapter(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        // Zapped chapters
        $baseQuery = $this->baseChapterController->getBaseQuery(0, $coorId, $confId, $regId, $positionId, $secPositionId);

        $chapterList = $baseQuery['query']->get();
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $countList = $chapterList->count();
        $data = [
            'countList' => $countList,
            'chapterList' => $chapterList,
            'checkBox5Status' => $checkBox5Status,
        ];

        return view('chapters.chapzapped')->with($data);
    }

    /**
     * Display the Inquiries Chapter list
     */
    public function showChapterInquiries(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        // Active chapters
        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);

        $chapterList = $baseQuery['query']->get();
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $countList = $chapterList->count();
        $data = [
            'countList' => $countList,
            'chapterList' => $chapterList,
            'checkBox5Status' => $checkBox5Status,
        ];

        return view('chapters.chapinquiries')->with($data);
    }

    /**
     * Display the Zapped Inquiries list
     */
    public function showZappedChapterInquiries(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        // Zapped chapters
        $baseQuery = $this->baseChapterController->getBaseQuery(0, $coorId, $confId, $regId, $positionId, $secPositionId);

        $chapterList = $baseQuery['query']->get();
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $countList = $chapterList->count();
        $data = [
            'countList' => $countList,
            'chapterList' => $chapterList,
            'checkBox5Status' => $checkBox5Status,
        ];

        return view('chapters.chapinquirieszapped')->with($data);
    }

    /**
     * Display the Chapter Details for ALL lists - Active, Zapped, Inquiries, International
     */
    public function viewChapterDetails(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $positionId = $user['cdPositionId'];
        $userName = $user['userName'];
        $userPosition = $user['cdPosition'];
        $userConfName = $user['confName'];
        $userConfDesc = $user['confDesc'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chActiveId = $baseQuery['chActiveId'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chPayments = $baseQuery['chPayments'];
        $chDocuments = $baseQuery['chDocuments'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $reviewComplete = $baseQuery['reviewComplete'];

        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $baseQuery['chapterStatus'];
        $probationReason = $baseQuery['probationReason'];
        $websiteLink = $baseQuery['websiteLink'];

        $chDisbanded = null;

        if ($chActiveId == ChapterStatusEnum::ACTIVE) {
            $baseBoardQuery = $this->baseChapterController->getActiveBoardDetails($id);
        } elseif ($chActiveId == ChapterStatusEnum::ZAPPED) {
            $baseBoardQuery = $this->baseChapterController->getDisbandedBoardDetails($id);
            $chDisbanded = $baseBoardQuery['chDisbanded'];
        }

        $PresDetails = $baseBoardQuery['PresDetails'];
        $AVPDetails = $baseBoardQuery['AVPDetails'];
        $MVPDetails = $baseBoardQuery['MVPDetails'];
        $TRSDetails = $baseBoardQuery['TRSDetails'];
        $SECDetails = $baseBoardQuery['SECDetails'];

        $resources = Resources::with('resourceCategory')->get();

        $dateOptions = $this->positionConditionsService->getDateOptions();
        $threeMonthsAgo = $dateOptions['threeMonthsAgo'];
        $startMonthId = $chDetails->start_month_id;
        $startYear = $chDetails->start_year;
        $startDate = Carbon::createFromDate($startYear, $startMonthId, 1);

        $data = ['id' => $id, 'chActiveId' => $chActiveId, 'positionId' => $positionId, 'coorId' => $coorId, 'reviewComplete' => $reviewComplete, 'threeMonthsAgo' => $threeMonthsAgo,
            'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'PresDetails' => $PresDetails, 'chDetails' => $chDetails, 'websiteLink' => $websiteLink,
            'startMonthName' => $startMonthName, 'confId' => $confId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chapterStatus' => $chapterStatus, 'startDate' => $startDate, 'probationReason' => $probationReason,
            'chFinancialReport' => $chFinancialReport, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'chPayments' => $chPayments,
            'conferenceDescription' => $conferenceDescription, 'chDisbanded' => $chDisbanded, 'chEOYDocuments' => $chEOYDocuments,
            'resources' => $resources, 'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,

        ];

        return view('chapters.view')->with($data);
    }

    /**
     * Check to see if there is already an EIN on file
     */
    public function checkEIN(Request $request): JsonResponse
    {
        $chapterId = $request->input('chapter_id');
        $chapter = DB::table('chapters')->where('id', $chapterId)->first();

        return response()->json([
            'ein' => $chapter->ein ?? null,
        ]);
    }

    /**
     * Update EIN number
     */
    public function updateEIN(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $input = $request->all();
        $chapterid = $input['chapterid'];
        $ein = $input['ein'];
        $chapterEIN = $input['notify'];

        $chapter = Chapters::find($chapterid);

        try {
            DB::beginTransaction();

            $chapter->ein = $ein;
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            if ($chapterEIN == 1) {
                $this->emailController->sendChapterEIN($request, $chapterid);
            }

            DB::commit();

            return response()->json([
                'status' => 'success', 'message' => 'Chapter EIN successfully updated',
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return response()->json([
                'status' => 'error', 'message' => 'Something went wrong, Please try again.',
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    public function updateChapterDisband(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $input = $request->all();
        $chapterid = $input['chapterid'];
        $disbandReason = $input['reason'];
        $disbandLetter = $input['letter'];
        $letterType = $input['letterType'];

        $chapter = Chapters::find($chapterid);
        $documents = Documents::find($chapterid);
        $preBalance = $documentsEOY->pre_balance ?? null;

        try {
            DB::beginTransaction();

            $chapter->active_status = ChapterStatusEnum::ZAPPED;
            $chapter->disband_reason = $disbandReason;
            $chapter->zap_date = Carbon::now();
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            $documents->disband_letter = $disbandLetter;
            $documents->save();

            DisbandedChecklist::create([
                'chapter_id' => $chapterid,
            ]);

            FinancialReportFinal::create([
                'chapter_id' => $chapterid,
                'pre_balance' => $preBalance,
            ]);

            $boardDetails = Boards::where('chapter_id', $chapterid)->get();
            $userIds = $boardDetails->pluck('user_id')->toArray();

            User::whereIn('id', $userIds)->update([
                'type_id' => UserTypeEnum::DISBANDED,
            ]);

            foreach ($boardDetails as $boardDetail) {
                BoardsDisbanded::create([
                    'user_id' => $boardDetail->user_id,
                    'chapter_id' => $boardDetail->chapter_id,
                    'board_position_id' => $boardDetail->board_position_id,
                    'first_name' => $boardDetail->first_name,
                    'last_name' => $boardDetail->last_name,
                    'email' => $boardDetail->email,
                    'phone' => $boardDetail->phone,
                    'street_address' => $boardDetail->street_address,
                    'city' => $boardDetail->city,
                    'state_id' => $boardDetail->state_id,
                    'zip' => $boardDetail->zip,
                    'country_id' => $boardDetail->country_id,
                    'updated_by' => $updatedBy,
                ]);
            }

            ForumCategorySubscription::whereIn('user_id', $userIds)->delete();
            Boards::where('chapter_id', $chapterid)->delete();

            // Update Chapter MailData for ListAdmin Notice//
            $baseQuery = $this->baseChapterController->getChapterDetails($chapterid);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $chConfId = $baseQuery['chConfId'];

            $baseDisbandedBoardQuery = $this->baseChapterController->getDisbandedBoardDetails($chapterid);
            $PresDetails = $baseDisbandedBoardQuery['PresDetails'];
            $AVPDetails = $baseDisbandedBoardQuery['AVPDetails'];
            $MVPDetails = $baseDisbandedBoardQuery['MVPDetails'];
            $TRSDetails = $baseDisbandedBoardQuery['TRSDetails'];
            $SECDetails = $baseDisbandedBoardQuery['SECDetails'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getBoardEmail($PresDetails, $AVPDetails, $MVPDetails, $TRSDetails, $SECDetails),
            );

            // ListAdmin Notification//
            $adminEmail = $this->positionConditionsService->getAdminEmail();
            $listAdmin = $adminEmail['list_admin'];

            Mail::to($listAdmin)
                ->queue(new DisbandChapListNotice($mailData));

            // Generate and Send the PDF Disbanding Letter & Notification to Board and Coordinators//
            if ($disbandLetter == 1) {
                $pdfPath = $this->pdfController->saveDisbandLetter($request, $chapterid, $letterType);
            }

            // Commit the transaction
            DB::commit();

            $message = 'Chapter successfully zapped';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    public function updateChapterUnZap(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $input = $request->all();
        $chapterid = $input['chapterid'];

        $chapter = Chapters::find($chapterid);
        $documents = Documents::find($chapterid);
        $documentsEOY = DocumentsEOY::find($chapterid);

        $defaultCategories = $this->forumSubscriptionController->defaultCategories();
        $defaultBoardCategories = $defaultCategories['boardCategories'];

        try {
            DB::beginTransaction();

            $chapter->active_status = '1';
            $chapter->disband_reason = null;
            $chapter->zap_date = null;
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            $documents->disband_letter = null;
            $documents->save();

            $documentsEOY->final_report_received = null;
            $documentsEOY->save();

            DisbandedChecklist::where('chapter_id', $chapterid)->delete();
            FinancialReportFinal::where('chapter_id', $chapterid)->delete();

            $boardDetails = BoardsDisbanded::where('chapter_id', $chapterid)->get();
            $userIds = $boardDetails->pluck('user_id')->toArray();

            User::whereIn('id', $userIds)->update([
                'type_id' => UserTypeEnum::BOARD,
            ]);

            foreach ($boardDetails as $boardDetail) {
                Boards::create([
                    'user_id' => $boardDetail->user_id,
                    'chapter_id' => $boardDetail->chapter_id,
                    'board_position_id' => $boardDetail->board_position_id,
                    'first_name' => $boardDetail->first_name,
                    'last_name' => $boardDetail->last_name,
                    'email' => $boardDetail->email,
                    'phone' => $boardDetail->phone,
                    'street_address' => $boardDetail->street_address,
                    'city' => $boardDetail->city,
                    'state_id' => $boardDetail->state_id,
                    'zip' => $boardDetail->zip,
                    'country_id' => $boardDetail->country_id,
                    'updated_by' => $updatedBy,
                ]);
            }

            foreach ($userIds as $userId) {
                foreach ($defaultBoardCategories as $categoryId) {
                    ForumCategorySubscription::create([
                        'user_id' => $userId,
                        'category_id' => $categoryId,
                    ]);
                }
            }

            BoardsDisbanded::where('chapter_id', $chapterid)->delete();

            // Update Chapter MailData//
            $baseQuery = $this->baseChapterController->getChapterDetails($chapterid);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $chConfId = $baseQuery['chConfId'];

            $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($chapterid);
            $PresDetails = $baseActiveBoardQuery['PresDetails'];
            $AVPDetails = $baseActiveBoardQuery['AVPDetails'];
            $MVPDetails = $baseActiveBoardQuery['MVPDetails'];
            $TRSDetails = $baseActiveBoardQuery['TRSDetails'];
            $SECDetails = $baseActiveBoardQuery['SECDetails'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getBoardEmail($PresDetails, $AVPDetails, $MVPDetails, $TRSDetails, $SECDetails),
            );

            $mailTable = $this->emailTableController->createListAdminBoardTable($mailData);

            $mailData = array_merge($mailData, [
                'mailTable' => $mailTable,
            ]);

            // Primary Coordinator Notification//
            $adminEmail = $this->positionConditionsService->getAdminEmail();
            $listAdmin = $adminEmail['list_admin'];

            Mail::to($listAdmin)
                ->queue(new UnZapChapListNotice($mailData));

            DB::commit();

            $message = 'Chapter successfully unzapped';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     *Add New Chapter
     */
    public function addChapterNew(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $confId = $user['confId'];

        $allStates = State::all();  // Full List for Dropdown Menu
        $allCountries = Country::all();  // Full List for Dropdown Menu
        $allRegions = Region::with('conference')  // Full List for Dropdown Menu based on Conference
            ->where('conference_id', $confId)
            ->get();
        $allStatuses = Status::all();  // Full List for Dropdown Menu
        $chConfId = $confId;

        $pcList = Coordinators::with(['displayPosition', 'secondaryPosition'])
            ->where('conference_id', $chConfId)
            ->whereBetween('position_id', [CoordinatorPosition::BS, CoordinatorPosition::CC])
            // ->whereBetween('position_id', [1, 7])
            ->where('active_status', ActiveStatusEnum::ACTIVE)
            ->where('on_leave', '!=', '1')
            ->get();

        $pcDetails = $pcList->map(function ($coordinator) {
            $mainTitle = $coordinator->displayPosition->short_title ?? '';
            $secondaryTitles = '';

            if (! empty($coordinator->secondaryPosition) && $coordinator->secondaryPosition->count() > 0) {
                $secondaryTitles = $coordinator->secondaryPosition->pluck('short_title')->implode('/');
            }

            $combinedTitle = $mainTitle;
            if (! empty($secondaryTitles)) {
                $combinedTitle .= '/'.$secondaryTitles;
            }

            $cpos = "({$combinedTitle})";

            return [
                'cid' => $coordinator->id,
                'cname' => "{$coordinator->first_name} {$coordinator->last_name}",
                'dpos' => $mainTitle,
                'cpos' => $cpos,
                'regid' => $coordinator->region_id,
            ];
        });

        $pcDetails = $pcDetails->unique('cid');  // Remove duplicates based on the 'cid' field

        $data = ['allRegions' => $allRegions, 'allStatuses' => $allStatuses, 'pcDetails' => $pcDetails,
            'allStates' => $allStates, 'allCountries' => $allCountries,
        ];

        return view('chapters.addnew')->with($data);
    }

    /**
     *Save New Chapter as Pending
     */
    public function updateChapterNew(Request $request): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];
        $conference = $user['confId'];

        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentMonth = $dateOptions['currentMonth'];
        $currentYear = $dateOptions['currentYear'];

        $input = $request->all();
        $sanitizedName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|', '.', ' '], '-', $input['ch_name']);

        DB::beginTransaction();
        try {
            $chapter = Chapters::create([
                'name' => $input['ch_name'],
                'sanitized_name' => $sanitizedName,
                'state_id' => $input['ch_state'],
                'country_id' => $input['ch_country'] ?? '198',
                'conference_id' => $conference,
                'region_id' => $input['ch_region'],
                'ein' => $input['ch_ein'],
                'status_id' => $input['ch_status'],
                'territory' => $input['ch_boundariesterry'],
                'inquiries_contact' => $input['ch_inqemailcontact'],
                'start_month_id' => $currentMonth,
                'start_year' => $currentYear,
                'next_renewal_year' => $currentYear + 1,
                'primary_coordinator_id' => $input['ch_primarycor'],
                'founders_name' => $input['ch_pre_fname'].' '.$input['ch_pre_lname'],
                'updated_by' => $updatedBy,
                'updated_id' => $updatedId,
                'active_status' => ChapterStatusEnum::PENDING,
            ]);

            $chId = $chapter->id;

            // Founder Info
            if (isset($input['ch_pre_fname']) && isset($input['ch_pre_lname']) && isset($input['ch_pre_email'])) {
                $userId = User::create([
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make('TempPass4You'),
                    'type_id' => UserTypeEnum::PENDING,
                    'is_active' => UserStatusEnum::ACTIVE,
                ])->id;

                BoardsPending::create([
                    'user_id' => $userId,
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'board_position_id' => 1,
                    'chapter_id' => $chId,
                    'street_address' => $input['ch_pre_street'],
                    'city' => $input['ch_pre_city'],
                    'state_id' => $input['ch_pre_state'],
                    'zip' => $input['ch_pre_zip'],
                    'country_id' => $input['ch_pre_country'] ?? '198',
                    'phone' => $input['ch_pre_phone'],
                    'updated_by' => $updatedBy,
                    'updated_id' => $updatedId,
                ])->id;
            }

            DB::commit();

            return redirect()->to('/chapter/chapterlist')->with('success', 'Chapter created successfully');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/chapter/chapterlist')->with('fail', 'Something went wrong, Please try again...');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     *Add New Chapter
     */
    public function addChapterNewInt(Request $request): View
    {
        $allStates = State::all();  // Full List for Dropdown Menu
        $allCountries = Country::all();  // Full List for Dropdown Menu

        $data = ['allStates' => $allStates, 'allCountries' => $allCountries,
        ];

        return view('chapters.addnewint')->with($data);
    }

    /**
     *Save New Chapter as Pending
     */
    public function updateChapterNewInt(Request $request): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentMonth = $dateOptions['currentMonth'];
        $currentYear = $dateOptions['currentYear'];

        $input = $request->all();
        $name = $input['ch_name'];
        $sanitizedName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|', '.', ' '], '-', $input['ch_name']);
        $stateId = $input['ch_state'];
        $state = State::find($stateId);
        $stateShortName = $state->state_short_name;

        $regId = '0';
        $statusId = OperatingStatusEnum::OK;
        $activeStatus = ChapterStatusEnum::PENDING;
        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentMonth = $dateOptions['currentMonth'];
        $currentYear = $dateOptions['currentYear'];

        $confId = null;
        if (in_array($stateShortName, ['AK', 'HI', 'ID', 'MN', 'MT', 'ND', 'OR', 'SD', 'WA', 'WI', 'WY', '**', 'AA', 'AE', 'AP'])) {
            $confId = '1';
        } elseif (in_array($stateShortName, ['AZ', 'CA', 'CO', 'NM', 'NV', 'OK', 'TX', 'UT'])) {
            $confId = '2';
        } elseif (in_array($stateShortName, ['AL', 'AR', 'DC', 'FL', 'GA', 'KY', 'LA', 'MD', 'MS', 'NC', 'SC', 'TN', 'VA', 'WV'])) {
            $confId = '3';
        } elseif (in_array($stateShortName, ['CT', 'DE', 'MA', 'ME', 'NH', 'NJ', 'NY', 'PA', 'RI', 'VT'])) {
            $confId = '4';
        } elseif (in_array($stateShortName, ['IA', 'IL', 'IN', 'KS', 'MI', 'MO', 'NE', 'OH'])) {
            $confId = '5';
        }

        $ccDetails = Coordinators::with(['displayPosition', 'secondaryPosition'])
            ->where('conference_id', $confId)
            ->where('position_id', CoordinatorPosition::CC)
            ->where('active_status', ChapterStatusEnum::ACTIVE)
            ->where('on_leave', '!=', '1')
            ->first();
        $pcId = $ccDetails ? $ccDetails->id : null;

        DB::beginTransaction();
        try {
            $chapter = Chapters::create([
                'name' => $input['ch_name'],
                'sanitized_name' => $sanitizedName,
                'state_id' => $input['ch_state'],
                'country_id' => $input['ch_country'] ?? '198',
                'conference_id' => $confId,
                'region_id' => $regId,
                'status_id' => $statusId,
                'territory' => $input['ch_boundariesterry'],
                'inquiries_contact' => $input['ch_inqemailcontact'],
                'start_month_id' => $currentMonth,
                'start_year' => $currentYear,
                'next_renewal_year' => $currentYear + 1,
                'primary_coordinator_id' => $pcId,
                'founders_name' => $input['ch_pre_fname'].' '.$input['ch_pre_lname'],
                'active_status' => $activeStatus,
                'updated_by' => $updatedBy,
                'updated_id' => $updatedId,
            ]);

            $chId = $chapter->id;

            // Founder Info
            if (isset($input['ch_pre_fname']) && isset($input['ch_pre_lname']) && isset($input['ch_pre_email'])) {
                $userId = User::create([
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make('TempPass4You'),
                    'type_id' => UserTypeEnum::PENDING,
                    'is_active' => UserStatusEnum::ACTIVE,
                ])->id;

                BoardsPending::create([
                    'user_id' => $userId,
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'board_position_id' => 1,
                    'chapter_id' => $chId,
                    'street_address' => $input['ch_pre_street'],
                    'city' => $input['ch_pre_city'],
                    'state_id' => $input['ch_pre_state'],
                    'zip' => $input['ch_pre_zip'],
                    'country_id' => $input['ch_pre_country'] ?? '198',
                    'phone' => $input['ch_pre_phone'],
                    'updated_by' => $input['ch_pre_fname'].' '.$input['ch_pre_lname'],
                    'updated_id' => $userId,
                ])->id;
            }

            DB::commit();

            return redirect()->to('/chapter/chapterlist')->with('success', 'Chapter created successfully');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/chapter/chapterlist')->with('fail', 'Something went wrong, Please try again...');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     *Edit Chapter Information
     */
    public function editChapterDetails(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chConfId = $baseQuery['chConfId'];
        $chActiveId = $baseQuery['chActiveId'];
        $chDocuments = $baseQuery['chDocuments'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $chPayments = $baseQuery['chPayments'];

        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chPcId = $baseQuery['chPcId'];
        $reviewComplete = $baseQuery['reviewComplete'];

        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $baseQuery['chapterStatus'];
        $probationReason = $baseQuery['probationReason'];
        $websiteLink = $baseQuery['websiteLink'];

        $allStatuses = $baseQuery['allStatuses'];
        $allProbation = $baseQuery['allProbation'];
        $allWebLinks = $baseQuery['allWebLinks'];

        $pcList = $baseQuery['pcList'];

        $data = ['id' => $id, 'chActiveId' => $chActiveId, 'reviewComplete' => $reviewComplete, 'coorId' => $coorId, 'chEOYDocuments' => $chEOYDocuments,
            'chDetails' => $chDetails, 'websiteLink' => $websiteLink, 'chDocuments' => $chDocuments, 'allProbation' => $allProbation,
            'startMonthName' => $startMonthName, 'chPcId' => $chPcId, 'chapterStatus' => $chapterStatus, 'probationReason' => $probationReason,
            'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'chPayments' => $chPayments,
            'conferenceDescription' => $conferenceDescription, 'allStatuses' => $allStatuses, 'allWebLinks' => $allWebLinks,
            'pcList' => $pcList, 'confId' => $confId, 'chConfId' => $chConfId,
        ];

        return view('chapters.edit')->with($data);
    }

    /**
     *Update Chapter Information
     */
    public function updateChapterDetails(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $pcDetails = $baseQuery['pcDetails'];

        $input = $request->all();

        $ch_webstatus = $request->input('ch_webstatus') ?: $request->input('ch_hid_webstatus');
        if (empty(trim($ch_webstatus))) {
            $ch_webstatus = 0; // Set it to 0 if it's blank
        }

        $website = $request->input('ch_website');
        // Ensure it starts with "http://" or "https://"
        if (! str_starts_with($website, 'http://') && ! str_starts_with($website, 'https://')) {
            $website = 'http://'.$website;
        }

        $status_id = $request->filled('ch_status') ? $request->input('ch_status') : $request->input('ch_hid_status');
        if ($status_id == OperatingStatusEnum::OK) {
            $probation_id = null;
        } else {
            $probation_id = $request->filled('ch_probation') ? $request->input('ch_probation') : $request->input('ch_hid_probation');
        }

        $chapter = Chapters::find($id);
        $chapterId = $id;
        $documents = Documents::find($id);
        $probation = ProbationSubmission::find($id);

        DB::beginTransaction();
        try {
            $chapterName = $request->filled('ch_name') ? $request->input('ch_name') : $request->input('ch_hid_name');
            $chapter->name = $chapterName;
            $chapter->sanitized_name = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|', '.', ' '], '-', $chapterName);
            $chapter->former_name = $request->filled('ch_preknown') ? $request->input('ch_preknown') : $request->input('ch_hid_preknown');
            $chapter->sistered_by = $request->filled('ch_sistered') ? $request->input('ch_sistered') : $request->input('ch_hid_sistered');
            $chapter->territory = $request->filled('ch_boundariesterry') ? $request->input('ch_boundariesterry') : $request->input('ch_hid_boundariesterry');
            $chapter->status_id = $status_id;
            $chapter->probation_id = $probation_id;
            $chapter->notes = $request->input('ch_notes');
            $chapter->inquiries_contact = $request->input('ch_inqemailcontact');
            $chapter->inquiries_note = $request->input('ch_inqnote');
            $chapter->email = $request->input('ch_email');
            $chapter->po_box = $request->input('ch_pobox');
            $chapter->additional_info = $request->input('ch_addinfo');
            $chapter->website_url = $website;
            $chapter->website_status = $request->input('ch_webstatus');
            $chapter->website_notes = $request->input('ch_webnotes');
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->primary_coordinator_id = $request->filled('ch_primarycor') ? $request->input('ch_primarycor') : $request->input('ch_hid_primarycor');
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            $documents->ein_notes = $request->input('ein_notes');
            $documents->save();

            if ($chapter->probation_id == 3 && ! $probation) {
                ProbationSubmission::create([
                    'chapter_id' => $id,
                ]);
            }

            // Update Chapter MailData//
            $baseQueryUpd = $this->baseChapterController->getChapterDetails($id);
            $chDetailsUpd = $baseQueryUpd['chDetails'];
            $stateShortName = $baseQueryUpd['stateShortName'];
            $chConfId = $baseQueryUpd['chConfId'];
            $emailListChap = $baseQueryUpd['emailListChap'];  // Full Board
            $emailListCoord = $baseQueryUpd['emailListCoord'];  // Full Coordinaor List
            $emailCC = $baseQueryUpd['emailCC'];  // CC Email
            $pcDetailsUpd = $baseQueryUpd['chDetails']->primaryCoordinator;
            $pcEmail = $pcDetailsUpd->email;  // PC Email
            $adminEmail = $this->positionConditionsService->getAdminEmail();
            $einAdmin = $adminEmail['ein_admin'];  // EIN Coor Email

            $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($id);
            $PresDetails = $baseActiveBoardQuery['PresDetails'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getPresData($PresDetails),
                $this->baseMailDataController->getChapterUpdatedData($chDetailsUpd, $pcDetailsUpd),
                $this->baseMailDataController->getPCData($pcDetails),
                $this->baseMailDataController->getPCUpdatedData($pcDetailsUpd),
                [
                    'chapterWebsiteUrl' => $website,
                ]
            );

            $mailTablePrimary = $this->emailTableController->createPrimaryUpdateChapterInfoTable($mailData);
            $mailTable = $this->emailTableController->createPresidentEmailTable($mailData);
            $mailTablePC = $this->emailTableController->createPrimaryCoordEmailTable($mailData);

            $mailData = array_merge($mailData, [
                'mailTablePrimary' => $mailTablePrimary,
                'mailTable' => $mailTable,
                'mailTablePC' => $mailTablePC,
            ]);

            // Primary Coordinator Update Notification//
            if ($chDetailsUpd->name != $chDetails->name || $chDetailsUpd->email != $chDetails->email || $chDetailsUpd->territory != $chDetails->territory ||
                    $chDetailsUpd->inquiries_contact != $chDetails->inquiries_contact || $chDetailsUpd->status_id != $chDetails->status_id ||
                    $chDetailsUpd->notes != $chDetails->notes || $chDetailsUpd->website_url != $chDetails->website_url ||
                    $chDetailsUpd->website_status != $chDetails->website_status || $chDetailsUpd->egroup != $chDetails->egroup) {
                Mail::to($pcEmail)
                    ->queue(new ChapDetailsUpdatePCNotice($mailData));
            }

            // Name Change Notification//
            if ($chDetailsUpd->name != $chDetails->name) {
                $chNamePrev = $chDetails->name;
                $pdfPath = $this->pdfController->saveNameChangeLetter($request, $chapterId, $chNamePrev);
            }

            // PC Change Notification//
            if ($chDetailsUpd->primary_coordinator_id != $chDetails->primary_coordinator_id) {
                Mail::to($emailListChap)
                    ->queue(new PCChangeChapNotice($mailData));

                Mail::to($pcEmail)
                    ->queue(new PCChangePCNotice($mailData));
            }

            // Website URL Change Notification//
            if ($chDetailsUpd->website_status != $chDetails->website_status) {
                if ($chDetailsUpd->website_status == 1) {
                    Mail::to($emailCC)
                        ->queue(new NewWebsiteApproveCoordNotice($mailData));

                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new NewWebsiteApproveChapNotice($mailData));
                }

                if ($chDetailsUpd->website_status == 2) {
                    Mail::to($emailCC)
                        ->queue(new NewWebsiteReviewNotice($mailData));
                }
            }

            DB::commit();

            return to_route('chapters.view', ['id' => $id])->with('success', 'Chapter Details have been updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('chapters.view', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     *Edit Chapter Board Information
     */
    public function editChapterBoard(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chConfId = $baseQuery['chConfId'];
        $chActiveId = $baseQuery['chActiveId'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chPcId = $baseQuery['chPcId'];

        $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($id);
        $PresDetails = $baseActiveBoardQuery['PresDetails'];
        $AVPDetails = $baseActiveBoardQuery['AVPDetails'];
        $MVPDetails = $baseActiveBoardQuery['MVPDetails'];
        $TRSDetails = $baseActiveBoardQuery['TRSDetails'];
        $SECDetails = $baseActiveBoardQuery['SECDetails'];

        $allStates = $baseQuery['allStates'];
        $allCountries = $baseQuery['allCountries'];

        $data = ['id' => $id, 'chActiveId' => $chActiveId, 'stateShortName' => $stateShortName, 'allCountries' => $allCountries,
            'chDetails' => $chDetails, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails,
            'chPcId' => $chPcId, 'allStates' => $allStates, 'PresDetails' => $PresDetails, 'confId' => $confId, 'chConfId' => $chConfId,
            'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
        ];

        return view('chapters.editboard')->with($data);
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
                'status' => UserStatusEnum::ACTIVE,
                'vacant_field' => null, // President is never vacant
            ],
            'avp' => [
                'relation' => 'avp',
                'position_id' => BoardPosition::AVP,
                'prefix' => 'ch_avp_',
                'status' => UserStatusEnum::ACTIVE,
                'vacant_field' => 'AVPVacant',
            ],
            'mvp' => [
                'relation' => 'mvp',
                'position_id' => BoardPosition::MVP,
                'prefix' => 'ch_mvp_',
                'status' => UserStatusEnum::ACTIVE,
                'vacant_field' => 'MVPVacant',
            ],
            'treasurer' => [
                'relation' => 'treasurer',
                'position_id' => BoardPosition::TRS,
                'prefix' => 'ch_trs_',
                'status' => UserStatusEnum::ACTIVE,
                'vacant_field' => 'TreasVacant',
            ],
            'secretary' => [
                'relation' => 'secretary',
                'position_id' => BoardPosition::SEC,
                'prefix' => 'ch_sec_',
                'status' => UserStatusEnum::ACTIVE,
                'vacant_field' => 'SecVacant',
            ],
        ];

        if (! isset($positionConfig[$position])) {
            return;
        }

        $config = $positionConfig[$position];
        $relation = $config['relation'];
        $prefix = $config['prefix'];
        $status = $config['status'];
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
                    $this->createNewBoardMember($chapterWithRelation, $relation, $positionId, $requestData, $prefix, $status, $updatedBy, $updatedId, $defaultBoardCategories);

                } else {
                    // Same user – update fields
                    $this->updateExistingBoardMember($user, $boardMember, $requestData, $prefix, $status, $updatedBy, $updatedId, $defaultBoardCategories);
                }
            }
        } else {
            // No current board member
            if (! $isVacant) {
                $this->createNewBoardMember($chapterWithRelation, $relation, $positionId, $requestData, $prefix, $status, $updatedBy, $updatedId, $defaultBoardCategories);
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

    private function updateExistingBoardMember($user, $boardMember, $requestData, $prefix, $chStatus, $updatedBy, $updatedId, $defaultBoardCategories)
    {
        $firstName = $requestData->input($prefix.'fname');
        $lastName = $requestData->input($prefix.'lname');
        $email = $requestData->input($prefix.'email');
        $stateId = $requestData->input($prefix.'state');
        $countryId = $requestData->input($prefix.'country') ?? '198';

        if ($chStatus == ChapterStatusEnum::ACTIVE || ChapterStatusEnum::PENDING) {
            $isActive = UserStatusEnum::ACTIVE;
        } elseif ($chStatus == ChapterStatusEnum::ZAPPED || ChapterStatusEnum::NOTAPPROVED) {
            $isActive = UserStatusEnum::INACTIVE;
        }

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

    public function createNewBoardMember($chapter, $relation, $positionId, $requestData, $prefix, $chStatus, $updatedBy, $updatedId, $defaultBoardCategories)
    {
        $firstName = $requestData->input($prefix.'fname');
        $lastName = $requestData->input($prefix.'lname');
        $email = $requestData->input($prefix.'email');
        $stateId = $requestData->input($prefix.'state');
        $countryId = $requestData->input($prefix.'country') ?? '198';
        $chapterId = $chapter->id;

        if ($chStatus == ChapterStatusEnum::ACTIVE || ChapterStatusEnum::PENDING) {
            $isActive = UserStatusEnum::ACTIVE;
        } elseif ($chStatus == ChapterStatusEnum::ZAPPED || ChapterStatusEnum::NOTAPPROVED) {
            $isActive = UserStatusEnum::INACTIVE;
        }

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
                'is_active' => $isActive,
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
                'is_active' => $isActive,
            ]);
        }

        if ($chStatus == ChapterStatusEnum::ZAPPED) {
            // Create new board member record
            BoardsDisbanded::create([
                'chapter_id' => $chapterId,
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
        } elseif ($chStatus == ChapterStatusEnum::ACTIVE) {
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
        } elseif ($chStatus == ChapterStatusEnum::PENDING) {
            // Create new board member record
            BoardsPending::create([
                'chapter_id' => $chapterId,
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
        }
    }

    // Updated main method with better error handling:
    public function updateChapterBoard(Request $request, $id)
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];

        $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($id);
        $PresDetails = $baseActiveBoardQuery['PresDetails'];
        $AVPDetails = $baseActiveBoardQuery['AVPDetails'];
        $MVPDetails = $baseActiveBoardQuery['MVPDetails'];
        $TRSDetails = $baseActiveBoardQuery['TRSDetails'];
        $SECDetails = $baseActiveBoardQuery['SECDetails'];

        $chapter = Chapters::find($id);

        $defaultCategories = $this->forumSubscriptionController->defaultCategories();
        $defaultBoardCategories = $defaultCategories['boardCategories'];

        DB::beginTransaction();

        try {
            $chapter->updated_by = $updatedBy;
            $chapter->updated_by = $updatedId;
            $chapter->save();

            // Update all board positions
            $this->updateBoardMember($chapter, 'president', $request, $updatedBy, $updatedId, $defaultBoardCategories);
            $this->updateBoardMember($chapter, 'avp', $request, $updatedBy, $updatedId, $defaultBoardCategories);
            $this->updateBoardMember($chapter, 'mvp', $request, $updatedBy, $updatedId, $defaultBoardCategories);
            $this->updateBoardMember($chapter, 'treasurer', $request, $updatedBy, $updatedId, $defaultBoardCategories);
            $this->updateBoardMember($chapter, 'secretary', $request, $updatedBy, $updatedId, $defaultBoardCategories);
            // Update Chapter MailData//
            $baseQueryUpd = $this->baseChapterController->getChapterDetails($id);
            $chDetailsUpd = $baseQueryUpd['chDetails'];
            $stateShortName = $baseQueryUpd['stateShortName'];
            $chConfId = $baseQueryUpd['chConfId'];
            $emailPC = $baseQueryUpd['emailPC'];

            $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($id);
            $PresDetailsUpd = $baseActiveBoardQuery['PresDetails'];
            $AVPDetailsUpd = $baseActiveBoardQuery['AVPDetails'];
            $MVPDetailsUpd = $baseActiveBoardQuery['MVPDetails'];
            $TRSDetailsUpd = $baseActiveBoardQuery['TRSDetails'];
            $SECDetailsUpd = $baseActiveBoardQuery['SECDetails'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetailsUpd, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getPresData($PresDetails),
                $this->baseMailDataController->getPresUpdatedData($PresDetailsUpd),
                $this->baseMailDataController->getBoardEmail($PresDetails, $AVPDetails, $MVPDetails, $TRSDetails, $SECDetails),
                $this->baseMailDataController->getBoardUpdEmail($PresDetailsUpd, $AVPDetailsUpd, $MVPDetailsUpd, $TRSDetailsUpd, $SECDetailsUpd)
            );

            $mailTableListAdmin = $this->emailTableController->createListAdminUpdateBoardTable($mailData);
            $mailTablePrimary = $this->emailTableController->createPrimaryUpdateBoardInfoTable($mailData);

            $mailData = array_merge($mailData, [
                'mailTableListAdmin' => $mailTableListAdmin,
                'mailTablePrimary' => $mailTablePrimary,
            ]);

            if ($PresDetailsUpd->email != $PresDetails->email || $PresDetailsUpd->first_name != $PresDetails->first_name || $PresDetailsUpd->last_name != $PresDetails->last_name ||
                    $AVPDetailsUpd->email != $AVPDetails->email || $AVPDetailsUpd->first_name != $AVPDetails->first_name || $AVPDetailsUpd->last_name != $AVPDetails->last_name ||
                    $MVPDetailsUpd->email != $MVPDetails->email || $MVPDetailsUpd->first_name != $MVPDetails->first_name || $MVPDetailsUpd->last_name != $MVPDetails->last_name ||
                    $TRSDetailsUpd->email != $TRSDetails->email || $TRSDetailsUpd->first_name != $TRSDetails->first_name || $TRSDetailsUpd->last_name != $TRSDetails->last_name ||
                    $SECDetailsUpd->email != $SECDetails->email || $SECDetailsUpd->first_name != $SECDetails->first_name || $SECDetailsUpd->last_name != $SECDetails->last_name) {
                Mail::to($emailPC)
                    ->queue(new BorUpdatePCNotice($mailData));
            }

            // List Admin Notification
            $adminEmail = $this->positionConditionsService->getAdminEmail();
            $listAdmin = $adminEmail['list_admin'];

            if ($PresDetailsUpd->email != $PresDetails->email || $AVPDetailsUpd->email != $AVPDetails->email || $MVPDetailsUpd->email != $MVPDetails->email ||
                    $TRSDetailsUpd->email != $TRSDetails->email || $SECDetailsUpd->email != $SECDetails->email) {
                Mail::to($listAdmin)
                    ->queue(new BorUpdateListNoitce($mailData));
            }

            DB::commit();

            return to_route('chapters.view', ['id' => $id])->with('success', 'Board Details have been updated');

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('chapters.view', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            DB::disconnect();
        }
    }

    /**
     * Display the Website Details
     */
    public function showChapterWebsite(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];
        $userName = $user['userName'];
        $userPosition = $user['cdPosition'];
        $userConfName = $user['confName'];
        $userConfDesc = $user['confDesc'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $websiteList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $data = ['websiteList' => $websiteList, 'checkBoxStatus' => $checkBoxStatus,
            'checkBox3Status' => $checkBox3Status,
            'checkBox5Status' => $checkBox5Status, 'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
        ];

        return view('chapters.chapwebsite')->with($data);
    }

    /**
     * Display the Social Media Information
     */
    public function showRptSocialMedia(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus,
            'checkBox3Status' => $checkBox3Status,
            'checkBox5Status' => $checkBox5Status,
        ];

        return view('chapters.chapsocialmedia')->with($data);
    }

    /**
     *Edit Website & Social Information
     */
    public function editChapterWebsite(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $userName = $user['userName'];
        $userPosition = $user['cdPosition'];
        $userConfName = $user['confName'];
        $userConfDesc = $user['confDesc'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chConfId = $baseQuery['chConfId'];
        $chActiveId = $baseQuery['chActiveId'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chPcId = $baseQuery['chPcId'];

        $allWebLinks = Website::all();  // Full List for Dropdown Menu

        $data = ['id' => $id, 'chActiveId' => $chActiveId, 'stateShortName' => $stateShortName, 'conferenceDescription' => $conferenceDescription,
            'chDetails' => $chDetails, 'allWebLinks' => $allWebLinks, 'chPcId' => $chPcId, 'regionLongName' => $regionLongName, 'confId' => $confId, 'chConfId' => $chConfId,
            'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
        ];

        return view('chapters.editwebsite')->with($data);
    }

    /**
     *Update Website & Social Media Information
     */
    public function updateChapterWebsite(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedBy = $user['userName'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $pcDetails = $baseQuery['pcDetails'];

        $ch_webstatus = $request->input('ch_webstatus') ?: $request->input('ch_hid_webstatus');
        if (empty(trim($ch_webstatus))) {
            $ch_webstatus = 0; // Set it to 0 if it's blank
        }

        $website = $request->input('ch_website');
        // Ensure it starts with "http://" or "https://"
        if (! str_starts_with($website, 'http://') && ! str_starts_with($website, 'https://')) {
            $website = 'http://'.$website;
        }

        $chapter = Chapters::find($id);

        DB::beginTransaction();
        try {
            $chapter->website_url = $website;
            $chapter->website_status = $request->input('ch_webstatus');
            $chapter->website_notes = $request->input('ch_webnotes');
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->updated_by = $updatedBy;

            $chapter->save();

            // Update Chapter MailData//
            $baseQueryUpd = $this->baseChapterController->getChapterDetails($id);
            $chDetailsUpd = $baseQueryUpd['chDetails'];
            $stateShortName = $baseQueryUpd['stateShortName'];
            $pcDetailsUpd = $baseQueryUpd['pcDetails'];
            $emailListChap = $baseQueryUpd['emailListChap'];  // Full Board
            $emailListCoord = $baseQueryUpd['emailListCoord']; // Full Coord List
            $emailCC = $baseQueryUpd['emailCC'];  // CC Email
            $emailPC = $baseQueryUpd['emailPC'];

            if ($request->input('ch_webstatus') != $request->input('ch_hid_webstatus')) {
                $mailData = array_merge(
                    $this->baseMailDataController->getChapterData($chDetailsUpd, $stateShortName),
                    [
                        'chapterWebsiteUrl' => $website,
                    ]
                );

                if ($request->input('ch_webstatus') == 1) {
                    Mail::to($emailCC)
                        ->queue(new NewWebsiteApproveCoordNotice($mailData));

                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new NewWebsiteApproveChapNotice($mailData));
                }

                if ($request->input('ch_webstatus') == 2) {
                    Mail::to($emailCC)
                        ->queue(new NewWebsiteReviewNotice($mailData));
                }
            }

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetailsUpd, $stateShortName),
                $this->baseMailDataController->getChapterUpdatedData($chDetailsUpd, $pcDetailsUpd),
            );

            $mailTablePrimary = $this->emailTableController->createPrimaryUpdateWebsiteTable($mailData);

            $mailData = array_merge($mailData, [
                'mailTablePrimary' => $mailTablePrimary,
            ]);

            if ($chDetailsUpd->website_url != $chDetails->website_url || $chDetailsUpd->website_status != $chDetails->website_status) {
                Mail::to($emailPC)
                    ->queue(new WebsiteUpdatePCNotice($mailData));

            }

            DB::commit();

            return to_route('chapters.editwebsite', ['id' => $id])->with('success', 'Chapter Website & Social Meida has been updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('chapters.editwebsite', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     *Edit Pending New Chapter Information
     */
    public function editPendingChapterDetails(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $positionId = $user['cdPositionId'];
        $userName = $user['userName'];
        $userPosition = $user['cdPosition'];
        $userConfName = $user['confName'];
        $userConfDesc = $user['confDesc'];
        $updatedBy = $user['userName'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chActiveId = $baseQuery['chActiveId'];
        $chConfId = $baseQuery['chConfId'];
        $chapterId = $id;

        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chPcId = $baseQuery['chPcId'];

        $basePendingBoardQuery = $this->baseChapterController->getPendingBoardDetails($id);
        $PresDetails = $basePendingBoardQuery['PresDetails'];
        $AVPDetails = $basePendingBoardQuery['AVPDetails'];
        $MVPDetails = $basePendingBoardQuery['MVPDetails'];
        $TRSDetails = $basePendingBoardQuery['TRSDetails'];
        $SECDetails = $basePendingBoardQuery['SECDetails'];

        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $baseQuery['chapterStatus'];

        $allActive = $baseQuery['allActive'];
        $allStates = $baseQuery['allStates'];
        $allCountries = $baseQuery['allCountries'];
        $allRegions = $baseQuery['allRegions'];

        $pcList = Coordinators::with(['displayPosition', 'secondaryPosition'])
            ->where('conference_id', $chConfId)
            ->whereBetween('position_id', [CoordinatorPosition::BS, CoordinatorPosition::CC])
            // ->whereBetween('position_id', [1, 7])
            ->where('active_status', 1)
            ->where('on_leave', '!=', '1')
            ->get();

        $pcDetails = $pcList->map(function ($coordinator) {
            $mainTitle = $coordinator->displayPosition->short_title ?? '';
            $secondaryTitles = '';

            if (! empty($coordinator->secondaryPosition) && $coordinator->secondaryPosition->count() > 0) {
                $secondaryTitles = $coordinator->secondaryPosition->pluck('short_title')->implode('/');
            }

            $combinedTitle = $mainTitle;
            if (! empty($secondaryTitles)) {
                $combinedTitle .= '/'.$secondaryTitles;
            }

            $cpos = "({$combinedTitle})";

            return [
                'cid' => $coordinator->id,
                'cname' => "{$coordinator->first_name} {$coordinator->last_name}",
                'dpos' => $mainTitle,
                'cpos' => $cpos,
                'regid' => $coordinator->region_id,
            ];
        });

        $pcDetails = $pcDetails->unique('cid');

        $data = ['id' => $id, 'chActiveId' => $chActiveId, 'chConfId' => $chConfId, 'chapterId' => $chapterId,
            'chDetails' => $chDetails, 'allActive' => $allActive, 'coorId' => $coorId,
            'startMonthName' => $startMonthName, 'chPcId' => $chPcId, 'chapterStatus' => $chapterStatus,
            'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription, 'allStates' => $allStates,
            'pcList' => $pcList, 'allRegions' => $allRegions, 'pcDetails' => $pcDetails, 'allCountries' => $allCountries,
            'PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails,
            'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails, 'confId' => $confId,
            'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
        ];

        return view('chapters.editpending')->with($data);
    }

    /**
     *Update Pending New Chapter Information
     */
    public function updatePendingChapterDetails(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedBy = $user['userName'];

        $input = $request->all();
        $chapterName = $request->input('ch_name');

        $chapter = Chapters::with('pendingPresident')->find($id);
        $president = $chapter->pendingPresident;
        $user = $president->user;

        DB::beginTransaction();
        try {
            $chapter->name = $chapterName;
            $chapter->sanitized_name = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|', '.', ' '], '-', $chapterName);
            $chapter->territory = $request->input('ch-territory');
            $chapter->state_id = $request->input('ch_state');
            $chapter->region_id = $request->input('ch_region');
            $chapter->inquiries_contact = $request->input('ch_inqemailcontact');
            $chapter->inquiries_note = $request->input('ch_inqnote');
            $chapter->email = $request->input('ch_email');
            $chapter->po_box = $request->input('ch_pobox');
            $chapter->primary_coordinator_id = $request->filled('ch_primarycor') ? $request->input('ch_primarycor') : $request->input('ch_hid_primarycor');
            $chapter->updated_by = $updatedBy;

            $chapter->save();

            $user->update([   // Update user details
                'first_name' => $request->input('ch_pre_fname'),
                'last_name' => $request->input('ch_pre_lname'),
                'email' => $request->input('ch_pre_email'),
            ]);

            $president->update([   // Update board details
                'first_name' => $request->input('ch_pre_fname'),
                'last_name' => $request->input('ch_pre_lname'),
                'email' => $request->input('ch_pre_email'),
                'street_address' => $request->input('ch_pre_street'),
                'city' => $request->input('ch_pre_city'),
                'state_id' => $request->input('ch_pre_state'),
                'zip' => $request->input('ch_pre_zip'),
                'country_id' => $request->input('ch_pre_country') ?? '198',
                'phone' => $request->input('ch_pre_phone'),
                'updated_by' => $updatedBy,
            ]);

            DB::commit();

            return to_route('chapters.editpending', ['id' => $id])->with('success', 'Chapter Details have been updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('chapters.editpending', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     *Update Pending New Chapter Information
     */
    public function updateApproveChapter(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedBy = $user['userName'];
        $activeStatus = '1';

        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentMonth = $dateOptions['currentMonth'];
        $currentYear = $dateOptions['currentYear'];

        $input = $request->all();
        $id = $input['chapter_id'];

        $defaultCategories = $this->forumSubscriptionController->defaultCategories();
        $defaultBoardCategories = $defaultCategories['boardCategories'];

        $chapter = Chapters::with('pendingPresident')->find($id);
        $chapterId = $id;
        $president = $chapter->pendingPresident;
        $user = $president->user;

        DB::beginTransaction();
        try {
            $chapter->active_status = $activeStatus;
            $chapter->start_month_id = $currentMonth;
            $chapter->start_year = $currentYear;
            $chapter->updated_by = $updatedBy;
            $chapter->save();

            FinancialReport::create([
                'chapter_id' => $chapterId,
            ]);

            Documents::create([
                'chapter_id' => $chapterId,
            ]);

            DocumentsEOY::create([
                'chapter_id' => $chapterId,
            ]);

            Payments::create([
                'chapter_id' => $chapterId,
            ]);

            foreach ($defaultBoardCategories as $categoryId) {
                ForumCategorySubscription::create([
                    'user_id' => $president->user_id,
                    'category_id' => $categoryId,
                ]);
            }

            User::where('id', $president->user_id)->update([
                'type_id' => UserTypeEnum::BOARD,
            ]);
            Boards::create([
                'user_id' => $president->user_id,
                'chapter_id' => $president->chapter_id,
                'board_position_id' => $president->board_position_id,
                'first_name' => $president->first_name,
                'last_name' => $president->last_name,
                'email' => $president->email,
                'phone' => $president->phone,
                'street_address' => $president->street_address,
                'city' => $president->city,
                'state_id' => $president->state_id,
                'zip' => $president->zip,
                'country_id' => $president->country_id,
                'updated_by' => $updatedBy,
            ]);
            BoardsPending::where('user_id', $president->user_id)->delete();

            // Load Chapter MailData//
            $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $pcDetails = $baseQuery['pcDetails'];
            $emailCC = $baseQuery['emailCC'];

            $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($id);
            $PresDetails = $baseActiveBoardQuery['PresDetails'];

            $user = $this->userController->loadUserInformation($request);
            $adminEmail = $this->positionConditionsService->getAdminEmail();
            $listAdmin = $adminEmail['list_admin'];
            $paymentsAdmin = $adminEmail['payments_admin'];
            $gsuiteAdmin = $adminEmail['gsuite_admin'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getPCData($pcDetails),
                $this->baseMailDataController->getPresData($PresDetails),
            );

            $mailTableNewChapter = $this->emailTableController->createNewChapterTable($mailData);
            $mailTable = $this->emailTableController->createNewChapterApprovedTable($mailData);
            $mailTableNewEmail = $this->emailTableController->createNewChapterEmailTable($mailData);

            $mailData = array_merge($mailData, [
                'mailTableNewChapter' => $mailTableNewChapter,
                'mailTableNewEmail' => $mailTableNewEmail,
                'mailTable' => $mailTable,
            ]);

            Mail::to($paymentsAdmin)
                ->queue(new NewChapApproveAdminNotice($mailData));

            Mail::to($emailCC)
                ->queue(new NewChapApproveGSuiteNotice($mailData));

            Mail::to($pcDetails->email)
                ->queue(new NewChapPCNotice($mailData));

            Mail::to($listAdmin)
                ->queue(new NewChapListNotice($mailData));

            DB::commit();

            // Return JSON response for AJAX
            return response()->json([
                'success' => true,
                'message' => 'Chapter approved successfully.',
                'redirect' => route('chapters.view', ['id' => $id]),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            // Return JSON error response for AJAX
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, Please try again.',
            ], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     *Update Pending New Chapter Information
     */
    public function updateDeclineChapter(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedBy = $user['userName'];
        $activeStatus = '3';

        $input = $request->all();
        $id = $input['chapter_id'];
        $disbandReason = $input['disband_reason'];

        $chapter = Chapters::with('pendingPresident')->find($id);
        $president = $chapter->pendingPresident;
        $user = $president->user;

        DB::beginTransaction();
        try {
            $chapter->active_status = $activeStatus;
            $chapter->disband_reason = $disbandReason;
            $chapter->zap_date = Carbon::now();
            $chapter->updated_by = $updatedBy;
            $chapter->save();

            User::where('id', $president->user_id)->update([
                'is_active' => UserStatusEnum::INACTIVE,
            ]);

            DB::commit();

            // Return JSON response for AJAX
            return response()->json([
                'success' => true,
                'message' => 'Coordinator application rejected.',
                // 'redirect' => route('coordinators.coordrejected')
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            // Return JSON error response for AJAX
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, Please try again.',
            ], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }
}
