<?php

namespace App\Http\Controllers;

use App\Enums\ChapterCheckbox;
use App\Models\Chapters;
use App\Models\Conference;
use App\Models\GrantRequest;
use App\Models\PaymentLog;
use App\Models\PaymentHistory;
use App\Models\Payments;
use App\Models\Region;
use App\Models\RegionInquiry;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentReportController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseChapterController;

    protected $baseCoordinatorController;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, BaseCoordinatorController $baseCoordinatorController)
    {
        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->baseCoordinatorController = $baseCoordinatorController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /**
     * View Payment Log List
     */
    public function showPaymentLog(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $confId = $user['confId'];

        $query = PaymentLog::with('board');

        // Check if international checkbox is selected
        $showInternational = $request->has(ChapterCheckbox::INTERNATIONAL) &&
                            $request->get(ChapterCheckbox::INTERNATIONAL) == 'yes';

        // Filter by conference unless international is selected
        if (! $showInternational) {
            $query->where('conf', $confId);
        }

        // Add additional filters if needed
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $paymentLogs = $query->orderByDesc('created_at')->paginate(100);

        // Set checkbox status based on URL parameter
        $checkBox5Status = $showInternational ? 'checked' : '';

        $data = [
            'paymentLogs' => $paymentLogs,
            'checkBox5Status' => $checkBox5Status,
        ];

        return view('paymentreports.paymentlog')->with($data);
    }

    /**
     * View Payment Log Transaction Details
     */
    public function showPaymentDetails($id): View
    {
        $log = PaymentLog::findOrFail($id);

        $data = ['log' => $log];

        return view('paymentreports.paymentdetails')->with($data);
    }

  public function showDonationLog(Request $request): View
{
    $user = $this->userController->loadUserInformation($request);
    $confId = $user['confId'];

    $checkBox5Status = $request->has(\App\Enums\ChapterCheckbox::INTERNATIONAL);
    $checkBox9Status = $request->has(\App\Enums\ChapterCheckbox::M2MDONATIONS);
    $checkBox10Status = $request->has(\App\Enums\ChapterCheckbox::INTERNATIONALM2MDONATIONS);

    // Base query
    $query = PaymentHistory::with('chapter')
        ->join('chapters', 'payment_history.chapter_id', '=', 'chapters.id')
        ->where('chapters.active_status', '1');

    // Add payment type filter based on checkboxes
    if ($checkBox9Status) {
        // Show only M2M donations
        $query->where('payment_history.payment_type', 'm2m');
    } elseif ($checkBox10Status) {
        // Show international M2M donations
        $query->where('payment_history.payment_type', 'm2m');
    } else {
        // Show both M2M and sustaining (default)
        $query->where(function($q) {
            $q->where('payment_history.payment_type', 'm2m')
              ->orWhere('payment_history.payment_type', 'sustaining');
        });
    }

    // Add conference filter
    if (!$checkBox5Status && !$checkBox10Status) {
        // Not showing international - filter by conference
        $query->where('chapters.conference_id', $confId);
    }
    // If checkBox5Status OR checkBox10Status is true, show all conferences (international)

    $donationsList = $query->orderBy('payment_history.payment_date', 'desc')->get();

    $data = [
        'donationsList' => $donationsList,
        'checkBox5Status' => $checkBox5Status ? 'checked' : '',
        'checkBox9Status' => $checkBox9Status ? 'checked' : '',
        'checkBox10Status' => $checkBox10Status ? 'checked' : '',
    ];

    return view('paymentreports.donationlog')->with($data);
}

    /**
     * View List of ReReg Payments if Dates Need to be Udpated
     */
    public function showReReg(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $data = ['chapterList' => $chapterList, 'checkBox5Status' => $checkBox5Status];

        return view('paymentreports.rereg')->with($data);
    }

    public function editReReg(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chConfId = $baseQuery['chConfId'];
        $stateShortName = $baseQuery['stateShortName'];
        $chPayments = $baseQuery['chPayments'];
        $allMonths = $baseQuery['allMonths'];

        $data = ['id' => $id, 'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'chPayments' => $chPayments, 'allMonths' => $allMonths,
            'confId' => $confId, 'chConfId' => $chConfId,
        ];

        return view('paymentreports.editrereg')->with($data);
    }

    public function updateReReg(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $chapter = Chapters::find($id);
        $payments = Payments::find($id);

        DB::beginTransaction();
        try {
            $chapter->start_month_id = $request->input('ch_founddate');
            $chapter->next_renewal_year = $request->input('ch_renewyear');
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;

            $chapter->save();

            $payments->rereg_date = $request->input('ch_duespaid');
            $payments->rereg_payment = $request->input('ch_payment');
            $payments->rereg_members = $request->input('ch_members');

            $payments->save();

            DB::commit();

            return redirect()->to('/paymentreports/reregedit')->with('error', 'Failed to update Re-Reg Info.');
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/paymentreports/reregedit')->with('success', 'Re-Reg Info updated successfully.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    public function showGrantList(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $checkBox5Status = $request->has(\App\Enums\ChapterCheckbox::INTERNATIONAL);

         // Use the appropriate query based on checkbox status
        if ($checkBox5Status) {
            $grantList = GrantRequest::with('chapters', 'chapterstate')
                ->orderBy('submitted_at', 'desc')
                ->get();

        } else {
            $grantList = GrantRequest::with('chapters', 'chapterstate')
                ->whereHas('chapterstate', function ($query) use ($confId) {
                    $query->where('conference_id', $confId);
                })
                ->orderBy('submitted_at', 'desc')
                ->get();
            }

        $data = ['grantList' => $grantList, 'checkBox5Status' => $checkBox5Status];

        return view('paymentreports.grantlist')->with($data);
    }

    public function editGrantDetails(Request $request, $grantId): View
    {
        $user = $this->userController->loadUserInformation($request);
    $coorId = $user['cdId'];
    $confId = $user['confId'];
            $loggedInName = $user['userName'];

    $grantDetails = GrantRequest::with('chapters', 'chapterstate', 'state', 'country')
        ->find($grantId);
    $chapterId = $grantDetails->chapter_id;

    // Only get chapter details if chapter_id is not null
    if ($chapterId) {
        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
        $chDetails = $baseQuery['chDetails'];
        $chConfId = $baseQuery['chConfId'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
    } else {
        // Set defaults when there's no chapter
        $chDetails = null;
        $chConfId = $grantDetails->chapterstate->conference_id;
        $stateShortName = $grantDetails->chapterstate->state_long_name;
        $regionLongName = $grantDetails->chapterstate->region->long_name;
        $conferenceDescription = $grantDetails->chapterstate->conference->description;
    }

    // $stateName = $grantDetails->chapterstate->state_long_name;
    // $regionName = $grantDetails->chapterstate->region->long_name;
    // $conferenceName = $grantDetails->chapterstate->conference->description;

    $grList = $chConfId ? $this->userController->loadGrantReviewerList($chConfId) : null;

        $data = ['id' => $grantId, 'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription, 'confId' => $confId, 'chConfId' => $chConfId, 'grantDetails' => $grantDetails,
            'grList' => $grList, 'loggedInName' => $loggedInName,
        ];

        return view('paymentreports.editgrantdetails')->with($data);
    }

    public function updateGrantDetails(Request $request, $grantId): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];

        $input = $request->all();
        $submitType = $input['submit_type'];
        $reviewer_id = isset($input['reviewer_id']) && ! empty($input['reviewer_id']) ? $input['reviewer_id'] : $coorId;

        $grantRequest = GrantRequest::find($grantId);

        DB::beginTransaction();
        try {
            $grantRequest->reviewer_id = $reviewer_id ?? $coorId;
            // $grantRequest->review_notes = $input['review_notes'] ?? null;
                    $grantRequest->review_notes = $input['Review_Log'] ?? null;  // Changed to Review_Log

            $grantRequest->review_description = $input['review_description'] ?? null;
            $grantRequest->amount_awarded = $input['amount_awarded'] ?? null;
            $grantRequest->grant_approved = $input['grant_approved'] ?? null;

            // If submitting the grant
            if ($submitType == 'review_complete') {
                $grantRequest->review_complete = 1;
                $grantRequest->completed_at = Carbon::now();
            }

            $grantRequest->save();

            DB::commit();

            if ($submitType == 'review_complete') {
                return redirect()->back()->with('success', 'Grant has been successfully Marked as Review Complete');
            } else {
                return redirect()->back()->with('success', 'Grant has been successfully Updated');
            }
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    public function updateUnsubmitGrantRequest(Request $request, $grantId): RedirectResponse
    {
        $grantRequest = GrantRequest::find($grantId);

        DB::beginTransaction();
        try {
            $grantRequest->submitted = null;
            $grantRequest->submitted_at = null;
            $grantRequest->save();

            DB::commit();

            return redirect()->back()->with('success', 'Grant Request has been successfully Unsubmitted.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }
    }

    public function updateClearGrantReview(Request $request, $grantId): RedirectResponse
    {
        $grantRequest = GrantRequest::find($grantId);

        DB::beginTransaction();
        try {
            $grantRequest->review_complete = null;
            $grantRequest->completed_at = null;
            $grantRequest->save();

            DB::commit();

            return redirect()->back()->with('success', 'Review Complete has been successfully Cleared.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }
    }
}
