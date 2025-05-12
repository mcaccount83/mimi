<?php

namespace App\Http\Controllers;

use App\Mail\ChapersUpdateListAdmin;
use App\Mail\ChapterAddListAdmin;
use App\Mail\ChapterAddPrimaryCoor;
use App\Mail\ChapterReAddListAdmin;
use App\Mail\ChapterRemoveListAdmin;
use App\Mail\ChaptersPrimaryCoordinatorChange;
use App\Mail\ChaptersPrimaryCoordinatorChangePCNotice;
use App\Mail\ChaptersUpdatePrimaryCoorBoard;
use App\Mail\ChaptersUpdatePrimaryCoorChapter;
use App\Mail\WebsiteAddNoticeAdmin;
use App\Mail\WebsiteAddNoticeChapter;
use App\Mail\WebsiteReviewNotice;
use App\Mail\WebsiteUpdatePrimaryCoor;
use App\Models\Boards;
use App\Models\BoardsDisbanded;
use App\Models\BoardsOutgoing;
use App\Models\BoardsPending;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\DisbandedChecklist;
use App\Models\Documents;
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

    protected $pdfController;

    protected $baseChapterController;

    protected $forumSubscriptionController;

    protected $baseMailDataController;

    protected $emailTableController;

    protected $emailController;

    public function __construct(UserController $userController, PDFController $pdfController, BaseChapterController $baseChapterController,
        ForumSubscriptionController $forumSubscriptionController, BaseMailDataController $baseMailDataController, EmailController $emailController,
        EmailTableController $emailTableController)
    {

        $this->userController = $userController;
        $this->pdfController = $pdfController;
        $this->baseChapterController = $baseChapterController;
        $this->forumSubscriptionController = $forumSubscriptionController;
        $this->baseMailDataController = $baseMailDataController;
        $this->emailTableController = $emailTableController;
        $this->emailController = $emailController;
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
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getPendingBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('chapters.chaplistpending')->with($data);
    }

    /**
     * Display the Pending New chapter list mapped with login coordinator
     */
    public function showNotApprovedChapters(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getNotApprovedBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('chapters.chaplistdeclined')->with($data);
    }

    /**
     * Display the Active chapter list mapped with login coordinator
     */
    public function showChapters(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $countList = $chapterList->count();
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('chapters.chaplist')->with($data);
    }

    /**
     * Display the Zapped chapter list mapped with Conference Region
     */
    public function showZappedChapter(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getZappedBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('chapters.chapzapped')->with($data);
    }

    /**
     * Display the Inquiries Chapter list
     */
    public function showChapterInquiries(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveInquiriesBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $countList = $chapterList->count();
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('chapters.chapinquiries')->with($data);
    }

    /**
     * Display the Zapped Inquiries list
     */
    public function showZappedChapterInquiries(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getZappedInquiriesBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('chapters.chapinquirieszapped')->with($data);
    }

    /**
     * Display the International chapter list
     */
    public function showIntChapter(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);
        $chapterList = $baseQuery['query']->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('international.intchapter')->with($data);
    }

    /**
     * Display the International Zapped chapter list
     */
    public function showIntZappedChapter(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseChapterController->getZappedInternationalBaseQuery($coorId);
        $chapterList = $baseQuery['query']->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('international.intchapterzapped')->with($data);
    }

    /**
     * Display the Chapter Details for ALL lists - Active, Zapped, Inquiries, International
     */
    public function viewChapterDetails(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $positionId = $user['user_positionId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chIsActive = $baseQuery['chIsActive'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chPayments = $baseQuery['chPayments'];
        $chDocuments = $baseQuery['chDocuments'];
        $chDisbanded = $baseQuery['chDisbanded'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $reviewComplete = $baseQuery['reviewComplete'];
        $displayEOY = $baseQuery['displayEOY'];
        $displayTESTING = $displayEOY['displayTESTING'];
        $displayLIVE = $displayEOY['displayLIVE'];

        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $baseQuery['chapterStatus'];
        $probationReason = $baseQuery['probationReason'];
        $websiteLink = $baseQuery['websiteLink'];

        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        $PresDisbandedDetails = $baseQuery['PresDisbandedDetails'];
        $AVPDisbandedDetails = $baseQuery['AVPDisbandedDetails'];
        $MVPDisbandedDetails = $baseQuery['MVPDisbandedDetails'];
        $TRSDisbandedDetails = $baseQuery['TRSDisbandedDetails'];
        $SECDisbandedDetails = $baseQuery['SECDisbandedDetails'];

        $resources = Resources::with('resourceCategory')->get();

        $now = Carbon::now();
        $threeMonthsAgo = $now->copy()->subMonths(3);
        $startMonthId = $chDetails->start_month_id;
        $startYear = $chDetails->start_year;
        $startDate = Carbon::createFromDate($startYear, $startMonthId, 1);

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'positionId' => $positionId, 'coorId' => $coorId, 'reviewComplete' => $reviewComplete, 'threeMonthsAgo' => $threeMonthsAgo,
            'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'PresDetails' => $PresDetails, 'chDetails' => $chDetails, 'websiteLink' => $websiteLink,
            'startMonthName' => $startMonthName, 'confId' => $confId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chapterStatus' => $chapterStatus, 'startDate' => $startDate, 'probationReason' => $probationReason,
            'chFinancialReport' => $chFinancialReport, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'chPayments' => $chPayments,
            'conferenceDescription' => $conferenceDescription, 'displayTESTING' => $displayTESTING, 'displayLIVE' => $displayLIVE, 'chDisbanded' => $chDisbanded, 'PresDisbandedDetails' => $PresDisbandedDetails,
            'AVPDisbandedDetails' => $AVPDisbandedDetails, 'MVPDisbandedDetails' => $MVPDisbandedDetails, 'TRSDisbandedDetails' => $TRSDisbandedDetails, 'SECDisbandedDetails' => $SECDisbandedDetails,
            'resources' => $resources,
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
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $input = $request->all();
        $chapterid = $input['chapterid'];
        $ein = $input['ein'];
        $chapterEIN = $input['notify'];

        $chapter = Chapters::find($chapterid);

        try {
            DB::beginTransaction();

            $chapter->ein = $ein;
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
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
            DB::rollback();
            Log::error($e);

            return response()->json([
                'status' => 'error', 'message' => 'Something went wrong, Please try again.',
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);
        }
    }

    public function updateChapterDisband(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $input = $request->all();
        $chapterid = $input['chapterid'];
        $disbandReason = $input['reason'];
        $disbandLetter = $input['letter'];
        $letterType = $input['letterType'];

        $chapter = Chapters::find($chapterid);
        $documents = Documents::find($chapterid);
        $preBalance = $documents->balance ?? null;

        try {
            DB::beginTransaction();

            $chapter->active_status = '0';
            $chapter->disband_reason = $disbandReason;
            $chapter->zap_date = date('Y-m-d');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
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
                'user_type' => 'disbanded',
                'updated_at' => $lastupdatedDate,
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
                    'state' => $boardDetail->state,
                    'zip' => $boardDetail->zip,
                    'country' => $boardDetail->country,
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => $lastupdatedDate,
                ]);
            }

            ForumCategorySubscription::whereIn('user_id', $userIds)->delete();
            Boards::where('chapter_id', $chapterid)->delete();

            // Update Chapter MailData for ListAdmin Notice//
            $baseQuery = $this->baseChapterController->getChapterDetails($chapterid);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $chConfId = $baseQuery['chConfId'];
            $PresDetails = $baseQuery['PresDisbandedDetails'];
            $AVPDetails = $baseQuery['AVPDisbandedDetails'];
            $MVPDetails = $baseQuery['MVPDisbandedDetails'];
            $TRSDetails = $baseQuery['TRSDisbandedDetails'];
            $SECDetails = $baseQuery['SECDisbandedDetails'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                [
                    'presName' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'presEmail' => $PresDetails->email,
                    'avpName' => $AVPDetails->first_name.' '.$AVPDetails->last_name,
                    'avpEmail' => $AVPDetails->email,
                    'mvpName' => $MVPDetails->first_name.' '.$MVPDetails->last_name,
                    'mvpEmail' => $MVPDetails->email,
                    'trsName' => $TRSDetails->first_name.' '.$TRSDetails->last_name,
                    'trsEmail' => $TRSDetails->email,
                    'secName' => $SECDetails->first_name.' '.$SECDetails->last_name,
                    'secEmail' => $SECDetails->email,
                ]
            );

            // ListAdmin Notification//
            $to_email = 'listadmin@momsclub.org';
            Mail::to($to_email)
                ->queue(new ChapterRemoveListAdmin($mailData));

            // Generate and Send the PDF Disbanding Letter & Notification to Board and Coordinators//
            if ($disbandLetter == 1) {
                $pdfPath = $this->pdfController->saveDisbandLetter($request, $chapterid, $letterType);
            }

            // Commit the transaction
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

    public function updateChapterUnZap(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $input = $request->all();
        $chapterid = $input['chapterid'];

        $chapter = Chapters::find($chapterid);
        $documents = Documents::find($chapterid);

        $defaultCategories = $this->forumSubscriptionController->defaultCategories();
        $defaultBoardCategories = $defaultCategories['boardCategories'];

        try {
            DB::beginTransaction();

            $chapter->active_status = '1';
            $chapter->disband_reason = null;
            $chapter->zap_date = null;
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            $documents->disband_letter = null;
            $documents->final_report_received = null;
            $documents->save();

            DisbandedChecklist::where('chapter_id', $chapterid)->delete();
            FinancialReportFinal::where('chapter_id', $chapterid)->delete();

            $boardDetails = BoardsDisbanded::where('chapter_id', $chapterid)->get();
            $userIds = $boardDetails->pluck('user_id')->toArray();

            User::whereIn('id', $userIds)->update([
                'user_type' => 'board',
                'updated_at' => $lastupdatedDate,
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
                    'state' => $boardDetail->state,
                    'zip' => $boardDetail->zip,
                    'country' => $boardDetail->country,
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => $lastupdatedDate,
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
            $PresDetails = $baseQuery['PresDetails'];
            $AVPDetails = $baseQuery['AVPDetails'];
            $MVPDetails = $baseQuery['MVPDetails'];
            $TRSDetails = $baseQuery['TRSDetails'];
            $SECDetails = $baseQuery['SECDetails'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                [
                    'presName' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'presEmail' => $PresDetails->email,
                    'avpName' => $AVPDetails->first_name.' '.$AVPDetails->last_name,
                    'avpEmail' => $AVPDetails->email,
                    'mvpName' => $MVPDetails->first_name.' '.$MVPDetails->last_name,
                    'mvpEmail' => $MVPDetails->email,
                    'trsName' => $TRSDetails->first_name.' '.$TRSDetails->last_name,
                    'trsEmail' => $TRSDetails->email,
                    'secName' => $SECDetails->first_name.' '.$SECDetails->last_name,
                    'secEmail' => $SECDetails->email,
                ]
            );

            // Primary Coordinator Notification//
            $to_email = 'listadmin@momsclub.org';
            Mail::to($to_email)
                ->queue(new ChapterReAddListAdmin($mailData));

            // Commit the transaction
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
        $confId = $user['user_confId'];

        $allStates = State::all();  // Full List for Dropdown Menu
        $allRegions = Region::with('conference')  // Full List for Dropdown Menu based on Conference
            ->where('conference_id', $confId)
            ->get();
        $allStatuses = Status::all();  // Full List for Dropdown Menu
        $chConfId = $confId;

        $pcList = Coordinators::with(['displayPosition', 'secondaryPosition'])
            ->where('conference_id', $chConfId)
            ->whereBetween('position_id', [1, 7])
            ->where('is_active', 1)
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
            'allStates' => $allStates,
        ];

        return view('chapters.addnew')->with($data);
    }

    /**
     *Save New Chapter
     */
    public function updateChapterNew(Request $request): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');
        $conference = $user['user_confId'];
        $country = 'USA';
        $currentMonth = date('m');
        $currentYear = date('Y');

        $input = $request->all();

        $sanitizedName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|', '.', ' '], '-', $input['ch_name']);

        $defaultCategories = $this->forumSubscriptionController->defaultCategories();
        $defaultBoardCategories = $defaultCategories['boardCategories'];

        DB::beginTransaction();
        try {
            $chapterId = Chapters::create([
                'name' => $input['ch_name'],
                'sanitized_name' => $sanitizedName,
                'state_id' => $input['ch_state'],
                'country_short_name' => $country,
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
                'last_updated_by' => $lastUpdatedBy,
                'last_updated_date' => $lastupdatedDate,
                'created_at' => $lastupdatedDate,
                'active_status' => 1,
            ])->id;

            FinancialReport::create([
                'chapter_id' => $chapterId,
            ]);

            Documents::create([
                'chapter_id' => $chapterId,
            ]);

            Payments::create([
                'chapter_id' => $chapterId,
            ]);

            // President Info
            if (isset($input['ch_pre_fname']) && isset($input['ch_pre_lname']) && isset($input['ch_pre_email'])) {
                $userId = User::create([
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1,
                ])->id;
                Boards::create([
                    'user_id' => $userId,
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'board_position_id' => 1,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_pre_street'],
                    'city' => $input['ch_pre_city'],
                    'state' => $input['ch_pre_state'],
                    'zip' => $input['ch_pre_zip'],
                    'country' => $country,
                    'phone' => $input['ch_pre_phone'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => $lastupdatedDate,
                ])->id;
                foreach ($defaultBoardCategories as $categoryId) {
                    ForumCategorySubscription::create([
                        'user_id' => $userId,
                        'category_id' => $categoryId,
                    ]);
                }
            }

            // AVP Info
            if ($request->input('AVPVacant') != 'on') {
                $userId = User::create([
                    'first_name' => $input['ch_avp_fname'],
                    'last_name' => $input['ch_avp_lname'],
                    'email' => $input['ch_avp_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1,
                ])->id;
                Boards::create([
                    'user_id' => $userId,
                    'first_name' => $input['ch_avp_fname'],
                    'last_name' => $input['ch_avp_lname'],
                    'email' => $input['ch_avp_email'],
                    'board_position_id' => 2,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_avp_street'],
                    'city' => $input['ch_avp_city'],
                    'state' => $input['ch_avp_state'],
                    'zip' => $input['ch_avp_zip'],
                    'country' => $country,
                    'phone' => $input['ch_avp_phone'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => $lastupdatedDate,
                ])->id;
                foreach ($defaultBoardCategories as $categoryId) {
                    ForumCategorySubscription::create([
                        'user_id' => $userId,
                        'category_id' => $categoryId,
                    ]);
                }
            }

            // MVP Info
            if ($request->input('MVPVacant') != 'on') {
                $userId = User::create([
                    'first_name' => $input['ch_mvp_fname'],
                    'last_name' => $input['ch_mvp_lname'],
                    'email' => $input['ch_mvp_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1,
                ])->id;
                Boards::create([
                    'user_id' => $userId,
                    'first_name' => $input['ch_mvp_fname'],
                    'last_name' => $input['ch_mvp_lname'],
                    'email' => $input['ch_mvp_email'],
                    'board_position_id' => 3,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_mvp_street'],
                    'city' => $input['ch_mvp_city'],
                    'state' => $input['ch_mvp_state'],
                    'zip' => $input['ch_mvp_zip'],
                    'country' => $country,
                    'phone' => $input['ch_mvp_phone'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => $lastupdatedDate,
                ])->id;
                foreach ($defaultBoardCategories as $categoryId) {
                    ForumCategorySubscription::create([
                        'user_id' => $userId,
                        'category_id' => $categoryId,
                    ]);
                }
            }

            // TRS Info
            if ($request->input('TreasVacant') != 'on') {
                $userId = User::create([
                    'first_name' => $input['ch_trs_fname'],
                    'last_name' => $input['ch_trs_lname'],
                    'email' => $input['ch_trs_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1,
                ])->id;
                Boards::create([
                    'user_id' => $userId,
                    'first_name' => $input['ch_trs_fname'],
                    'last_name' => $input['ch_trs_lname'],
                    'email' => $input['ch_trs_email'],
                    'board_position_id' => 4,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_trs_street'],
                    'city' => $input['ch_trs_city'],
                    'state' => $input['ch_trs_state'],
                    'zip' => $input['ch_trs_zip'],
                    'country' => $country,
                    'phone' => $input['ch_trs_phone'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => $lastupdatedDate,
                ])->id;
                foreach ($defaultBoardCategories as $categoryId) {
                    ForumCategorySubscription::create([
                        'user_id' => $userId,
                        'category_id' => $categoryId,
                    ]);
                }
            }

            // SEC Info
            if ($request->input('SecVacant') != 'on') {
                $userId = User::create([
                    'first_name' => $input['ch_sec_fname'],
                    'last_name' => $input['ch_sec_lname'],
                    'email' => $input['ch_sec_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1,
                ])->id;
                Boards::create([
                    'user_id' => $userId,
                    'first_name' => $input['ch_sec_fname'],
                    'last_name' => $input['ch_sec_lname'],
                    'email' => $input['ch_sec_email'],
                    'board_position_id' => 5,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_sec_street'],
                    'city' => $input['ch_sec_city'],
                    'state' => $input['ch_sec_state'],
                    'zip' => $input['ch_sec_zip'],
                    'country' => $country,
                    'phone' => $input['ch_sec_phone'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => $lastupdatedDate,
                ])->id;
                foreach ($defaultBoardCategories as $categoryId) {
                    ForumCategorySubscription::create([
                        'user_id' => $userId,
                        'category_id' => $categoryId,
                    ]);
                }
            }

            // Load Chapter MailData//
            $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $pcDetails = $baseQuery['pcDetails'];
            $PresDetails = $baseQuery['PresDetails'];
            $AVPDetails = $baseQuery['AVPDetails'];
            $MVPDetails = $baseQuery['MVPDetails'];
            $TRSDetails = $baseQuery['TRSDetails'];
            $SECDetails = $baseQuery['SECDetails'];

            //  Load User Information for Signing Email & PDFs
            $user = $this->userController->loadUserInformation($request);

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getPCData($pcDetails),
                [
                    'presName' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'presEmail' => $PresDetails->email,
                    'avpName' => $AVPDetails->first_name.' '.$AVPDetails->last_name,
                    'avpEmail' => $AVPDetails->email,
                    'mvpName' => $MVPDetails->first_name.' '.$MVPDetails->last_name,
                    'mvpEmail' => $MVPDetails->email,
                    'trsName' => $TRSDetails->first_name.' '.$TRSDetails->last_name,
                    'trsEmail' => $TRSDetails->email,
                    'secName' => $SECDetails->first_name.' '.$SECDetails->last_name,
                    'secEmail' => $SECDetails->email,
                ]
            );

            // Primary Coordinator Notification//
            Mail::to($pcDetails->email)
                ->queue(new ChapterAddPrimaryCoor($mailData));

            // List Admin Notification//
            $listAdminEmail = 'listadmin@momsclub.org';
            Mail::to($listAdminEmail)
                ->queue(new ChapterAddListAdmin($mailData));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/chapter/chapterlist')->with('fail', 'Something went wrong, Please try again...');
        }

        return redirect()->to('/chapter/chapterlist')->with('success', 'Chapter created successfully');
    }

    /**
     *Edit Chapter Information
     */
    public function editChapterDetails(Request $request, $id): View
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chIsActive = $baseQuery['chIsActive'];
        $chDocuments = $baseQuery['chDocuments'];
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

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'reviewComplete' => $reviewComplete,
            'chDetails' => $chDetails, 'websiteLink' => $websiteLink, 'chDocuments' => $chDocuments, 'allProbation' => $allProbation,
            'startMonthName' => $startMonthName, 'chPcId' => $chPcId, 'chapterStatus' => $chapterStatus, 'probationReason' => $probationReason,
            'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'chPayments' => $chPayments,
            'conferenceDescription' => $conferenceDescription, 'allStatuses' => $allStatuses, 'allWebLinks' => $allWebLinks,
            'pcList' => $pcList,
        ];

        return view('chapters.edit')->with($data);
    }

    /**
     *Update Chapter Information
     */
    public function updateChapterDetails(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

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
        if ($status_id == 1) {
            $probation_id = null;
        } else {
            $probation_id = $request->filled('ch_probation') ? $request->input('ch_probation') : $request->input('ch_hid_probation');
        }

        $chapter = Chapters::find($id);
        $chapterId = $id;
        $probation = ProbationSubmission::find($id);

        DB::beginTransaction();
        try {
            $chapterName = $request->filled('ch_name') ? $request->input('ch_name') : $request->input('ch_hid_name');
            $chapter->name = $chapterName;
            $chapter->sanitized_name = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|', '.', ' '], '-', $chapterName);
            // $chapter->name = $request->filled('ch_name') ? $request->input('ch_name') : $request->input('ch_hid_name');
            $chapter->notes = $request->input('ch_einnotes');
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
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;

            $chapter->save();

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
            $PresDetails = $baseQueryUpd['PresDetails'];
            $emailListChap = $baseQueryUpd['emailListChap'];  // Full Board
            $emailListCoord = $baseQueryUpd['emailListCoord'];  // Full Coordinaor List
            $emailCC = $baseQueryUpd['emailCC'];  // CC Email
            $pcDetailsUpd = $baseQueryUpd['chDetails']->primaryCoordinator;
            $pcEmail = $pcDetailsUpd->email;  // PC Email
            $EINCordEmail = 'jackie.mchenry@momsclub.org';  // EIN Coor Email

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

            $mailData = array_merge($mailData, [
                'mailTablePrimary' => $mailTablePrimary,
            ]);

            // Primary Coordinator Notification//
            if ($chDetailsUpd->name != $chDetails->name || $chDetailsUpd->inquiries_contact != $chDetails->inquiries_contact || $chDetailsUpd->inquiries_note != $chDetails->inquiries_note ||
                    $chDetailsUpd->email != $chDetails->email || $chDetailsUpd->po_box != $chDetails->po_box || $chDetailsUpd->website_url != $chDetails->website_url ||
                    $chDetailsUpd->website_status != $chDetails->website_status || $chDetailsUpd->egroup != $chDetails->egroup || $chDetailsUpd->territory != $chDetails->territory ||
                    $chDetailsUpd->additional_info != $chDetails->additional_info || $chDetailsUpd->status_id != $chDetails->status_id || $chDetailsUpd->notes != $chDetails->notes) {
                Mail::to($pcEmail)
                    ->queue(new ChaptersUpdatePrimaryCoorChapter($mailData));
            }

            // Name Change Notification//
            if ($chDetailsUpd->name != $chDetails->name) {
                $chNamePrev = $chDetails->name;
                $pdfPath = $this->pdfController->saveNameChangeLetter($request, $chapterId, $chNamePrev);
            }

            // PC Change Notification//
            if ($chDetailsUpd->primary_coordinator_id != $chDetails->primary_coordinator_id) {
                Mail::to($emailListChap)
                    ->queue(new ChaptersPrimaryCoordinatorChange($mailData));

                Mail::to($pcEmail)
                    ->queue(new ChaptersPrimaryCoordinatorChangePCNotice($mailData));
            }

            // Website URL Change Notification//
            if ($chDetailsUpd->website_status != $chDetails->website_status) {
                if ($chDetailsUpd->website_status == 1) {
                    Mail::to($emailCC)
                        ->queue(new WebsiteAddNoticeAdmin($mailData));

                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new WebsiteAddNoticeChapter($mailData));
                }

                if ($chDetailsUpd->website_status == 2) {
                    Mail::to($emailCC)
                        ->queue(new WebsiteReviewNotice($mailData));
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
        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chIsActive = $baseQuery['chIsActive'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chPcId = $baseQuery['chPcId'];

        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        $allStates = $baseQuery['allStates'];

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'stateShortName' => $stateShortName,
            'chDetails' => $chDetails, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails,
            'chPcId' => $chPcId, 'allStates' => $allStates, 'PresDetails' => $PresDetails,
            'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
        ];

        return view('chapters.editboard')->with($data);
    }

    /**
     *Update Chapter Board Information
     */
    public function updateChapterBoard(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        $chapter = Chapters::find($id);

        $defaultCategories = $this->forumSubscriptionController->defaultCategories();
        $defaultBoardCategories = $defaultCategories['boardCategories'];

        DB::beginTransaction();
        try {
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();

            // President Info
            if ($request->input('ch_pre_fname') != '' && $request->input('ch_pre_lname') != '' && $request->input('ch_pre_email') != '') {
                $chapter = Chapters::with('president')->find($id);
                $president = $chapter->president;
                $user = $president->user;

                $user->update([   // Update user details
                    'first_name' => $request->input('ch_pre_fname'),
                    'last_name' => $request->input('ch_pre_lname'),
                    'email' => $request->input('ch_pre_email'),
                    'updated_at' => now(),
                ]);
                $president->update([   // Update board details
                    'first_name' => $request->input('ch_pre_fname'),
                    'last_name' => $request->input('ch_pre_lname'),
                    'email' => $request->input('ch_pre_email'),
                    'street_address' => $request->input('ch_pre_street'),
                    'city' => $request->input('ch_pre_city'),
                    'state' => $request->input('ch_pre_state'),
                    'zip' => $request->input('ch_pre_zip'),
                    'country' => 'USA',
                    'phone' => $request->input('ch_pre_phone'),
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => now(),
                ]);
                // Check if subscription already exists
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

            // AVP Info
            $chapter = Chapters::with('avp')->find($id);
            $avp = $chapter->avp;
            if ($avp) {
                $user = $avp->user;
                $bdDetails = $avp;
                if ($request->input('AVPVacant') == 'on') {
                    if ($user) {
                        User::where('id', $user->id)->update([
                            'user_type' => 'outgoing',
                            'updated_at' => $lastupdatedDate,
                        ]);
                        BoardsOutgoing::create([
                            'user_id' => $user->id,
                            'chapter_id' => $bdDetails->chapter_id,
                            'board_position_id' => $bdDetails->board_position_id,
                            'first_name' => $bdDetails->first_name,
                            'last_name' => $bdDetails->last_name,
                            'email' => $bdDetails->email,
                            'phone' => $bdDetails->phone,
                            'street_address' => $bdDetails->street_address,
                            'city' => $bdDetails->city,
                            'state' => $bdDetails->state,
                            'zip' => $bdDetails->zip,
                            'country' => $bdDetails->country,
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => $lastupdatedDate,
                        ]);
                        Boards::where('user_id', $user->id)->delete();
                        ForumCategorySubscription::where('user_id', $user->id)->delete();
                    }
                } else {
                    $user->update([   // Update user details if alrady exists
                        'first_name' => $request->input('ch_avp_fname'),
                        'last_name' => $request->input('ch_avp_lname'),
                        'email' => $request->input('ch_avp_email'),
                        'updated_at' => now(),
                    ]);
                    $avp->update([   // Update board details if alrady exists
                        'first_name' => $request->input('ch_avp_fname'),
                        'last_name' => $request->input('ch_avp_lname'),
                        'email' => $request->input('ch_avp_email'),
                        'street_address' => $request->input('ch_avp_street'),
                        'city' => $request->input('ch_avp_city'),
                        'state' => $request->input('ch_avp_state'),
                        'zip' => $request->input('ch_avp_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_avp_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                    ]);
                    // Check if subscription already exists
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
            } else {
                if ($request->input('AVPVacant') != 'on') {
                    $user = User::create([  // Create user details if new
                        'first_name' => $request->input('ch_avp_fname'),
                        'last_name' => $request->input('ch_avp_lname'),
                        'email' => $request->input('ch_avp_email'),
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1,
                    ]);
                    $chapter->avp()->create([  // Create board details if new
                        'user_id' => $user->id,
                        'first_name' => $request->input('ch_avp_fname'),
                        'last_name' => $request->input('ch_avp_lname'),
                        'email' => $request->input('ch_avp_email'),
                        'board_position_id' => 2,
                        'street_address' => $request->input('ch_avp_street'),
                        'city' => $request->input('ch_avp_city'),
                        'state' => $request->input('ch_avp_state'),
                        'zip' => $request->input('ch_avp_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_avp_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                    ]);
                    foreach ($defaultBoardCategories as $categoryId) {
                        ForumCategorySubscription::create([
                            'user_id' => $user->id,
                            'category_id' => $categoryId,
                        ]);
                    }
                }
            }

            // MVP Info
            $chapter = Chapters::with('mvp')->find($id);
            $mvp = $chapter->mvp;
            if ($mvp) {
                $user = $mvp->user;
                $bdDetails = $mvp;
                if ($request->input('MVPVacant') == 'on') {
                    if ($user) {
                        User::where('id', $user->id)->update([
                            'user_type' => 'outgoing',
                            'updated_at' => $lastupdatedDate,
                        ]);
                        BoardsOutgoing::create([
                            'user_id' => $user->id,
                            'chapter_id' => $bdDetails->chapter_id,
                            'board_position_id' => $bdDetails->board_position_id,
                            'first_name' => $bdDetails->first_name,
                            'last_name' => $bdDetails->last_name,
                            'email' => $bdDetails->email,
                            'phone' => $bdDetails->phone,
                            'street_address' => $bdDetails->street_address,
                            'city' => $bdDetails->city,
                            'state' => $bdDetails->state,
                            'zip' => $bdDetails->zip,
                            'country' => $bdDetails->country,
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => $lastupdatedDate,
                        ]);
                        Boards::where('user_id', $user->id)->delete();
                        ForumCategorySubscription::where('user_id', $user->id)->delete();
                    }
                } else {
                    $user->update([   // Update user details if alrady exists
                        'first_name' => $request->input('ch_mvp_fname'),
                        'last_name' => $request->input('ch_mvp_lname'),
                        'email' => $request->input('ch_mvp_email'),
                        'updated_at' => now(),
                    ]);
                    $mvp->update([   // Update board details if alrady exists
                        'first_name' => $request->input('ch_mvp_fname'),
                        'last_name' => $request->input('ch_mvp_lname'),
                        'email' => $request->input('ch_mvp_email'),
                        'street_address' => $request->input('ch_mvp_street'),
                        'city' => $request->input('ch_mvp_city'),
                        'state' => $request->input('ch_mvp_state'),
                        'zip' => $request->input('ch_mvp_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_mvp_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                    ]);
                    // Check if subscription already exists
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
            } else {
                if ($request->input('MVPVacant') != 'on') {
                    $user = User::create([  // Create user details if new
                        'first_name' => $request->input('ch_mvp_fname'),
                        'last_name' => $request->input('ch_mvp_lname'),
                        'email' => $request->input('ch_mvp_email'),
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1,
                    ]);
                    $chapter->mvp()->create([  // Create board details if new
                        'user_id' => $user->id,
                        'first_name' => $request->input('ch_mvp_fname'),
                        'last_name' => $request->input('ch_mvp_lname'),
                        'email' => $request->input('ch_mvp_email'),
                        'board_position_id' => 3,
                        'street_address' => $request->input('ch_mvp_street'),
                        'city' => $request->input('ch_mvp_city'),
                        'state' => $request->input('ch_mvp_state'),
                        'zip' => $request->input('ch_mvp_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_mvp_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                    ]);

                    foreach ($defaultBoardCategories as $categoryId) {
                        ForumCategorySubscription::create([
                            'user_id' => $user->id,
                            'category_id' => $categoryId,
                        ]);
                    }
                }
            }

            // TRS Info
            $chapter = Chapters::with('treasurer')->find($id);
            $treasurer = $chapter->treasurer;
            if ($treasurer) {
                $user = $treasurer->user;
                $bdDetails = $treasurer;
                if ($request->input('TreasVacant') == 'on') {
                    if ($user) {
                        User::where('id', $user->id)->update([
                            'user_type' => 'outgoing',
                            'updated_at' => $lastupdatedDate,
                        ]);
                        BoardsOutgoing::create([
                            'user_id' => $user->id,
                            'chapter_id' => $bdDetails->chapter_id,
                            'board_position_id' => $bdDetails->board_position_id,
                            'first_name' => $bdDetails->first_name,
                            'last_name' => $bdDetails->last_name,
                            'email' => $bdDetails->email,
                            'phone' => $bdDetails->phone,
                            'street_address' => $bdDetails->street_address,
                            'city' => $bdDetails->city,
                            'state' => $bdDetails->state,
                            'zip' => $bdDetails->zip,
                            'country' => $bdDetails->country,
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => $lastupdatedDate,
                        ]);
                        Boards::where('user_id', $user->id)->delete();
                        ForumCategorySubscription::where('user_id', $user->id)->delete();
                    }
                } else {
                    $user->update([   // Update user details if alrady exists
                        'first_name' => $request->input('ch_trs_fname'),
                        'last_name' => $request->input('ch_trs_lname'),
                        'email' => $request->input('ch_trs_email'),
                        'updated_at' => now(),
                    ]);
                    $treasurer->update([   // Update board details if alrady exists
                        'first_name' => $request->input('ch_trs_fname'),
                        'last_name' => $request->input('ch_trs_lname'),
                        'email' => $request->input('ch_trs_email'),
                        'street_address' => $request->input('ch_trs_street'),
                        'city' => $request->input('ch_trs_city'),
                        'state' => $request->input('ch_trs_state'),
                        'zip' => $request->input('ch_trs_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_trs_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                    ]);
                    // Check if subscription already exists
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
            } else {
                if ($request->input('TreasVacant') != 'on') {
                    $user = User::create([  // Create user details if new
                        'first_name' => $request->input('ch_trs_fname'),
                        'last_name' => $request->input('ch_trs_lname'),
                        'email' => $request->input('ch_trs_email'),
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1,
                    ]);
                    $chapter->treasurer()->create([  // Create board details if new
                        'user_id' => $user->id,
                        'first_name' => $request->input('ch_trs_fname'),
                        'last_name' => $request->input('ch_trs_lname'),
                        'email' => $request->input('ch_trs_email'),
                        'board_position_id' => 4,
                        'street_address' => $request->input('ch_trs_street'),
                        'city' => $request->input('ch_trs_city'),
                        'state' => $request->input('ch_trs_state'),
                        'zip' => $request->input('ch_trs_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_trs_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                    ]);

                    foreach ($defaultBoardCategories as $categoryId) {
                        ForumCategorySubscription::create([
                            'user_id' => $user->id,
                            'category_id' => $categoryId,
                        ]);
                    }
                }
            }

            // SEC Info
            $chapter = Chapters::with('secretary')->find($id);
            $secretary = $chapter->secretary;
            ForumCategorySubscription::where('user_id', $user)->delete();
            if ($secretary) {
                $user = $secretary->user;
                $bdDetails = $secretary;
                if ($request->input('SecVacant') == 'on') {
                    if ($user) {
                        User::where('id', $user->id)->update([
                            'user_type' => 'outgoing',
                            'updated_at' => $lastupdatedDate,
                        ]);
                        BoardsOutgoing::create([
                            'user_id' => $user->id,
                            'chapter_id' => $bdDetails->chapter_id,
                            'board_position_id' => $bdDetails->board_position_id,
                            'first_name' => $bdDetails->first_name,
                            'last_name' => $bdDetails->last_name,
                            'email' => $bdDetails->email,
                            'phone' => $bdDetails->phone,
                            'street_address' => $bdDetails->street_address,
                            'city' => $bdDetails->city,
                            'state' => $bdDetails->state,
                            'zip' => $bdDetails->zip,
                            'country' => $bdDetails->country,
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => $lastupdatedDate,
                        ]);
                        Boards::where('user_id', $user->id)->delete();
                        ForumCategorySubscription::where('user_id', $user->id)->delete();
                    }
                } else {
                    $user->update([   // Update user details if alrady exists
                        'first_name' => $request->input('ch_sec_fname'),
                        'last_name' => $request->input('ch_sec_lname'),
                        'email' => $request->input('ch_sec_email'),
                        'updated_at' => now(),
                    ]);
                    $secretary->update([   // Update board details if alrady exists
                        'first_name' => $request->input('ch_sec_fname'),
                        'last_name' => $request->input('ch_sec_lname'),
                        'email' => $request->input('ch_sec_email'),
                        'street_address' => $request->input('ch_sec_street'),
                        'city' => $request->input('ch_sec_city'),
                        'state' => $request->input('ch_sec_state'),
                        'zip' => $request->input('ch_sec_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_sec_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                    ]);
                    // Check if subscription already exists
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
            } else {
                if ($request->input('SecVacant') != 'on') {
                    $user = User::create([  // Create user details if new
                        'first_name' => $request->input('ch_sec_fname'),
                        'last_name' => $request->input('ch_sec_lname'),
                        'email' => $request->input('ch_sec_email'),
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1,
                    ]);
                    $chapter->secretary()->create([  // Create board details if new
                        'user_id' => $user->id,
                        'first_name' => $request->input('ch_sec_fname'),
                        'last_name' => $request->input('ch_sec_lname'),
                        'email' => $request->input('ch_sec_email'),
                        'board_position_id' => 5,
                        'street_address' => $request->input('ch_sec_street'),
                        'city' => $request->input('ch_sec_city'),
                        'state' => $request->input('ch_sec_state'),
                        'zip' => $request->input('ch_sec_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_sec_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                    ]);

                    foreach ($defaultBoardCategories as $categoryId) {
                        ForumCategorySubscription::create([
                            'user_id' => $user->id,
                            'category_id' => $categoryId,
                        ]);
                    }
                }
            }

            // Update Chapter MailData//
            $baseQueryUpd = $this->baseChapterController->getChapterDetails($id);
            $chDetailsUpd = $baseQueryUpd['chDetails'];
            $stateShortName = $baseQueryUpd['stateShortName'];
            $chConfId = $baseQueryUpd['chConfId'];
            $emailPC = $baseQueryUpd['emailPC'];
            $PresDetailsUpd = $baseQueryUpd['PresDetails'];
            $AVPDetailsUpd = $baseQueryUpd['AVPDetails'];
            $MVPDetailsUpd = $baseQueryUpd['MVPDetails'];
            $TRSDetailsUpd = $baseQueryUpd['TRSDetails'];
            $SECDetailsUpd = $baseQueryUpd['SECDetails'];

            $mailDataPres = array_merge(
                $this->baseMailDataController->getChapterData($chDetailsUpd, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getPresData($PresDetails),
                $this->baseMailDataController->getPresUpdatedData($PresDetailsUpd),
            );

            $mailData = array_merge($mailDataPres);
            if ($AVPDetailsUpd !== null) {
                $mailDataAvp = ['avpNameUpd' => $AVPDetailsUpd->first_name.' '.$AVPDetailsUpd->last_name,
                    // 'avplnameUpd' => $AVPDetailsUpd->last_name,
                    'avpemailUpd' => $AVPDetailsUpd->email, ];
                $mailData = array_merge($mailData, $mailDataAvp);
            } else {
                $mailDataAvp = ['avpNameUpd' => '',
                    // 'avplnameUpd' => '',
                    'avpemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDataAvp);
            }
            if ($MVPDetailsUpd !== null) {
                $mailDataMvp = ['mvpNameUpd' => $MVPDetailsUpd->first_name.' '.$MVPDetailsUpd->last_name,
                    // 'mvplnameUpd' => $MVPDetailsUpd->last_name,
                    'mvpemailUpd' => $MVPDetailsUpd->email, ];
                $mailData = array_merge($mailData, $mailDataMvp);
            } else {
                $mailDataMvp = ['mvpNameUpd' => '',
                    // 'mvplnameUpd' => '',
                    'mvpemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDataMvp);
            }
            if ($TRSDetailsUpd !== null) {
                $mailDatatres = ['tresNameUpd' => $TRSDetailsUpd->first_name.' '.$TRSDetailsUpd->last_name,
                    // 'treslnameUpd' => $TRSDetailsUpd->last_name,
                    'tresemailUpd' => $TRSDetailsUpd->email, ];
                $mailData = array_merge($mailData, $mailDatatres);
            } else {
                $mailDatatres = ['tresNameUpd' => '',
                    // 'treslnameUpd' => '',
                    'tresemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDatatres);
            }
            if ($SECDetailsUpd !== null) {
                $mailDataSec = ['secNameUpd' => $SECDetailsUpd->first_name.' '.$SECDetailsUpd->last_name,
                    // 'seclnameUpd' => $SECDetailsUpd->last_name,
                    'secemailUpd' => $SECDetailsUpd->email, ];
                $mailData = array_merge($mailData, $mailDataSec);
            } else {
                $mailDataSec = ['secNameUpd' => '',
                    // 'seclnameUpd' => '',
                    'secemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDataSec);
            }

            if ($AVPDetails !== null) {
                $mailDataAvpp = ['avpName' => $AVPDetails->first_name.' '.$AVPDetails->last_name,
                    // 'avplname' => $AVPDetails->last_name,
                    'avpemail' => $AVPDetails->email, ];
                $mailData = array_merge($mailData, $mailDataAvpp);
            } else {
                $mailDataAvpp = ['avpName' => '',
                    // 'avplname' => '',
                    'avpemail' => '', ];
                $mailData = array_merge($mailData, $mailDataAvpp);
            }
            if ($MVPDetails !== null) {
                $mailDataMvpp = ['mvpName' => $MVPDetails->first_name.' '.$MVPDetails->last_name,
                    // 'mvplname' => $MVPDetails->last_name,
                    'mvpemail' => $MVPDetails->email, ];
                $mailData = array_merge($mailData, $mailDataMvpp);
            } else {
                $mailDataMvpp = ['mvpName' => '',
                    // 'mvplname' => '',
                    'mvpemail' => '', ];
                $mailData = array_merge($mailData, $mailDataMvpp);
            }
            if ($TRSDetails !== null) {
                $mailDatatresp = ['tresName' => $TRSDetails->first_name.' '.$TRSDetails->last_name,
                    // 'treslname' => $TRSDetails->last_name,
                    'tresemail' => $TRSDetails->email, ];
                $mailData = array_merge($mailData, $mailDatatresp);
            } else {
                $mailDatatresp = ['tresName' => '',
                    // 'treslname' => '',
                    'tresemail' => '', ];
                $mailData = array_merge($mailData, $mailDatatresp);
            }
            if ($SECDetails !== null) {
                $mailDataSecp = ['secName' => $SECDetails->first_name.' '.$SECDetails->last_name,
                    // 'seclname' => $SECDetails->last_name,
                    'secemail' => $SECDetails->email, ];
                $mailData = array_merge($mailData, $mailDataSecp);
            } else {
                $mailDataSecp = ['secName' => '',
                    // 'seclname' => '',
                    'secemail' => '', ];
                $mailData = array_merge($mailData, $mailDataSecp);
            }

            $mailTableListAdmin = $this->emailTableController->createListAdminUpdateBoardTable($mailData);
            $mailTablePrimary = $this->emailTableController->createPrimaryUpdateBoardInfoTable($mailData);

            $mailData = array_merge($mailData, [
                'mailTableListAdmin' => $mailTableListAdmin,
                'mailTablePrimary' => $mailTablePrimary,
            ]);

            if ($chDetailsUpd->name != $chDetails->name || $PresDetailsUpd->bor_email != $PresDetails->bor_email || $PresDetailsUpd->street_address != $PresDetails->street_address || $PresDetailsUpd->city != $PresDetails->city ||
                    $PresDetailsUpd->state != $PresDetails->state || $PresDetailsUpd->first_name != $PresDetails->first_name || $PresDetailsUpd->last_name != $PresDetails->last_name ||
                    $PresDetailsUpd->zip != $PresDetails->zip || $PresDetailsUpd->phone != $PresDetails->phone || $PresDetailsUpd->inquiries_contact != $PresDetails->inquiries_contact ||
                    $PresDetailsUpd->ein != $chDetails->ein || $chDetailsUpd->ein_letter_path != $chDetails->ein_letter_path || $PresDetailsUpd->inquiries_note != $PresDetails->inquiries_note ||
                    $chDetailsUpd->email != $chDetails->email || $chDetailsUpd->po_box != $chDetails->po_box || $chDetailsUpd->website_url != $chDetails->website_url ||
                    $chDetailsUpd->website_status != $chDetails->website_status || $chDetailsUpd->egroup != $chDetails->egroup || $chDetailsUpd->territory != $chDetails->territory ||
                    $chDetailsUpd->additional_info != $chDetails->additional_info || $chDetailsUpd->status_id != $chDetails->status_id || $chDetailsUpd->notes != $chDetails->notes ||
                    $mailDataAvpp['avpName'] != $mailDataAvp['avpNameUpd'] || $mailDataAvpp['avpemail'] != $mailDataAvp['avpemailUpd'] ||
                    $mailDataMvpp['mvpName'] != $mailDataMvp['mvpNameUpd'] || $mailDataMvpp['mvpemail'] != $mailDataMvp['mvpemailUpd'] ||
                    $mailDatatresp['tresName'] != $mailDatatres['tresNameUpd'] || $mailDatatresp['tresemail'] != $mailDatatres['tresemailUpd'] ||
                    $mailDataSecp['secName'] != $mailDataSec['secNameUpd'] || $mailDataSecp['secemail'] != $mailDataSec['secemailUpd']) {

                Mail::to($emailPC)
                    ->queue(new ChaptersUpdatePrimaryCoorBoard($mailData));
                // ->queue(new ChaptersUpdatePrimaryCoorPresident($mailData));
            }

            // //List Admin Notification//
            $to_email2 = 'listadmin@momsclub.org';

            if ($PresDetailsUpd->email != $PresDetails->email || $PresDetailsUpd->email != $PresDetails->email || $mailDataAvpp['avpemail'] != $mailDataAvp['avpemailUpd'] ||
                        $mailDataMvpp['mvpemail'] != $mailDataMvp['mvpemailUpd'] || $mailDatatresp['tresemail'] != $mailDatatres['tresemailUpd'] ||
                        $mailDataSecp['secemail'] != $mailDataSec['secemailUpd']) {

                Mail::to($to_email2)
                    ->queue(new ChapersUpdateListAdmin($mailData));
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
     *Edit Chapter EIN Notes
     */
    public function editChapterIRS(Request $request, $id): View
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $startMonthName = $baseQuery['startMonthName'];
        $chIsActive = $baseQuery['chIsActive'];
        $chPcId = $baseQuery['chPcId'];
        $chDocuments = $baseQuery['chDocuments'];

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'conferenceDescription' => $conferenceDescription,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'startMonthName' => $startMonthName,
            'chPcId' => $chPcId, 'chDocuments' => $chDocuments,
        ];

        return view('chapters.editirs')->with($data);
    }

    /**
     *Update Chapter EIN Notes
     */
    public function updateChapterIRS(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $chapter = Chapters::find($id);
        $documents = Documents::find($id);

        DB::beginTransaction();
        try {
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            $documents->ein_letter = $request->has('ch_ein_letter') ? 1 : 0;
            $documents->irs_verified = $request->has('irs_verified') ? 1 : 0;
            $documents->irs_notes = $request->input('irs_notes');
            $documents->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('chapters.editirs', ['id' => $id])->with('fail', 'Something went wrong, Please try again..');
        }

        return to_route('chapters.editirs', ['id' => $id])->with('success', 'Chapter IRS Information has been updated');
    }

    /**
     * Display the Website Details
     */
    public function showChapterWebsite(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $websiteList = $baseQuery['query']->get();

        $data = ['websiteList' => $websiteList];

        return view('chapters.chapwebsite')->with($data);
    }

    /**
     * Display the Website Details
     */
    public function showIntWebsite(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);
        $websiteList = $baseQuery['query']->get();

        $data = ['websiteList' => $websiteList];

        return view('international.intchapwebsite')->with($data);
    }

    /**
     * Display the Social Media Information
     */
    public function showRptSocialMedia(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();

        $data = ['chapterList' => $chapterList];

        return view('chapreports.chaprptsocialmedia')->with($data);
    }

    /**
     * Display the Social Media Information
     */
    public function showIntSocialMedia(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);
        $chapterList = $baseQuery['query']->get();

        $data = ['chapterList' => $chapterList];

        return view('international.intchapsocialmedia')->with($data);
    }

    /**
     *Edit Website & Social Information
     */
    public function editChapterWebsite(Request $request, $id): View
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chIsActive = $baseQuery['chIsActive'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chPcId = $baseQuery['chPcId'];

        $allWebLinks = Website::all();  // Full List for Dropdown Menu

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'stateShortName' => $stateShortName, 'conferenceDescription' => $conferenceDescription,
            'chDetails' => $chDetails, 'allWebLinks' => $allWebLinks,
            'chPcId' => $chPcId, 'regionLongName' => $regionLongName,
        ];

        return view('chapters.editwebsite')->with($data);
    }

    /**
     *Update Website & Social Media Information
     */
    public function updateChapterWebsite(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

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
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;

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
                        ->queue(new WebsiteAddNoticeAdmin($mailData));

                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new WebsiteAddNoticeChapter($mailData));
                }

                if ($request->input('ch_webstatus') == 2) {
                    Mail::to($emailCC)
                        ->queue(new WebsiteReviewNotice($mailData));
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
                    ->queue(new WebsiteUpdatePrimaryCoor($mailData));
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
     * BoardList
     */
    public function showChapterBoardlist(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $activeChapterList = $baseQuery['query']->get();

        $countList = count($activeChapterList);
        $data = ['countList' => $countList, 'activeChapterList' => $activeChapterList];

        return view('chapters.chapboardlist')->with($data);
    }

    /**
     *Edit Pending New Chapter Information
     */
    public function editPendingChapterDetails(Request $request, $id): View
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chIsActive = $baseQuery['chIsActive'];
        $chConfId = $baseQuery['chConfId'];

        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chPcId = $baseQuery['chPcId'];

        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $baseQuery['chapterStatus'];

        $allActive = $baseQuery['allActive'];
        $allStates = $baseQuery['allStates'];  // Full List for Dropdown Menu
        $allRegions = $baseQuery['allRegions'];

        $pcList = Coordinators::with(['displayPosition', 'secondaryPosition'])
            ->where('conference_id', $chConfId)
            ->whereBetween('position_id', [1, 7])
            ->where('is_active', 1)
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

        $data = ['id' => $id, 'chIsActive' => $chIsActive,
            'chDetails' => $chDetails, 'allActive' => $allActive,
            'startMonthName' => $startMonthName, 'chPcId' => $chPcId, 'chapterStatus' => $chapterStatus,
            'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription, 'allStates' => $allStates,
            'pcList' => $pcList, 'allRegions' => $allRegions, 'pcDetails' => $pcDetails,
        ];

        return view('chapters.editpending')->with($data);
    }

    /**
     *Update Pending New Chapter Information
     */
    public function updatePendingChapterDetails(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $pcDetails = $baseQuery['pcDetails'];

        $input = $request->all();
        $chapterName = $request->input('ch_name');

        $chapter = Chapters::with('pendingPresident')->find($id);
        $chapterId = $id;
        $president = $chapter->pendingPresident;
        $user = $president->user;
        $userId = $president->id;

        $defaultCategories = $this->forumSubscriptionController->defaultCategories();
        $defaultBoardCategories = $defaultCategories['boardCategories'];

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
            $chapter->active_status = $request->input('ch_active');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;

            $chapter->save();

            if ($chapter->active_status == 3) {
                $chapter->zap_date = now();
                $chapter->disband_reason = $request->input('disband_reason');
                $chapter->save();
            }

            $user->update([   // Update user details
                'first_name' => $request->input('ch_pre_fname'),
                'last_name' => $request->input('ch_pre_lname'),
                'email' => $request->input('ch_pre_email'),
                'updated_at' => now(),
            ]);

            $president->update([   // Update board details
                'first_name' => $request->input('ch_pre_fname'),
                'last_name' => $request->input('ch_pre_lname'),
                'email' => $request->input('ch_pre_email'),
                'street_address' => $request->input('ch_pre_street'),
                'city' => $request->input('ch_pre_city'),
                'state' => $request->input('ch_pre_state'),
                'zip' => $request->input('ch_pre_zip'),
                'country' => 'USA',
                'phone' => $request->input('ch_pre_phone'),
                'last_updated_by' => $lastUpdatedBy,
                'last_updated_date' => now(),
            ]);

            if ($chapter->active_status == 1) {
                FinancialReport::create([
                    'chapter_id' => $chapterId,
                ]);

                Documents::create([
                    'chapter_id' => $chapterId,
                ]);

                Payments::create([
                    'chapter_id' => $chapterId,
                ]);

                foreach ($defaultBoardCategories as $categoryId) {
                    ForumCategorySubscription::create([
                        'user_id' => $user->id,
                        'category_id' => $categoryId,
                    ]);
                }

                User::where('id', $user->id)->update([
                    'user_type' => 'board',
                ]);
                Boards::create([
                    'user_id' => $user->id,
                    'chapter_id' => $president->chapter_id,
                    'board_position_id' => $president->board_position_id,
                    'first_name' => $president->first_name,
                    'last_name' => $president->last_name,
                    'email' => $president->email,
                    'phone' => $president->phone,
                    'street_address' => $president->street_address,
                    'city' => $president->city,
                    'state' => $president->state,
                    'zip' => $president->zip,
                    'country' => $president->country,
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => $lastupdatedDate,
                ]);
                BoardsPending::where('user_id', $user->id)->delete();

                // Load Chapter MailData//
                $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
                $chDetails = $baseQuery['chDetails'];
                $stateShortName = $baseQuery['stateShortName'];
                $pcDetails = $baseQuery['pcDetails'];
                $PresDetails = $baseQuery['PresDetails'];

                //  Load User Information for Signing Email & PDFs
                $user = $this->userController->loadUserInformation($request);

                $mailData = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getUserData($user),
                    $this->baseMailDataController->getPCData($pcDetails),
                    $this->baseMailDataController->getPresData($PresDetails),
                );

                $mailTableNewChapter = $this->emailTableController->createNewChapterTable($mailData);

                $mailData = array_merge($mailData, [
                    'mailTableNewChapter' => $mailTableNewChapter,
                ]);

                // Primary Coordinator Notification//
                Mail::to($pcDetails->email)
                    ->queue(new ChapterAddPrimaryCoor($mailData));

                // List Admin Notification//
                $listAdminEmail = 'listadmin@momsclub.org';
                Mail::to($listAdminEmail)
                    ->queue(new ChapterAddListAdmin($mailData));
            }

            DB::commit();
            if ($chapter->active_status == 1) {
                return to_route('chapters.view', ['id' => $id])->with('success', 'Chapter Details have been updated');
            } else {
                return to_route('chapters.editpending', ['id' => $id])->with('success', 'Chapter Details have been updated');
            }
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('chapters.editpending', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }
}
