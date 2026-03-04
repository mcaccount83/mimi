<?php

namespace App\Http\Controllers;

use App\Enums\CheckboxFilterEnum;
use App\Enums\ChapterStatusEnum;
use App\Mail\PaymentsM2MChapterThankYou;
use App\Mail\PaymentsReRegChapterThankYou;
use App\Mail\PaymentsReRegLate;
use App\Mail\PaymentsReRegReminder;
use App\Mail\PaymentsSustainingChapterThankYou;
use App\Models\Chapters;
use App\Models\GrantRequest;
use App\Models\Payments;
use App\Models\PaymentHistory;
use App\Services\PositionConditionsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PaymentController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseChapterController;

    protected $baseMailDataController;

    protected PositionConditionsService $positionConditionsService;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, BaseMailDataController $baseMailDataController,
        PositionConditionsService $positionConditionsService)
    {

        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->baseMailDataController = $baseMailDataController;
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
     * ReRegistration List
     */
    public function showChapterReRegistration(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentMonth = $dateOptions['currentMonth'];
        $currentYear = $dateOptions['currentYear'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];
        $checkBox56Status = $baseQuery[CheckboxFilterEnum::INTERNATIONALREREG];

        if ($checkBox3Status || $checkBox51Status) {
            $reChapterList = $baseQuery['query']
                ->get();
        } else {
            $reChapterList = $baseQuery['query']
                ->where(function ($query) use ($currentYear, $currentMonth) {
                    $query->where('next_renewal_year', '<', $currentYear)
                        ->orWhere(function ($query) use ($currentYear, $currentMonth) {
                            $query->where('next_renewal_year', '=', $currentYear)
                                ->where('start_month_id', '<=', $currentMonth);
                        });
                })
                ->get();
        }

        $countList = count($reChapterList);
        $data = ['countList' => $countList, 'reChapterList' => $reChapterList, 'checkBox1Status' => $checkBox1Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status, 'checkBox56Status' => $checkBox56Status,
        ];

        return view('coordinators.payment.chapreregistration')->with($data);
    }

    /**
     * ReRegistration Reminders Auto Send
     */
    public function createChapterReRegistrationReminder(Request $request): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $confId = $user['confId'];

        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentDate = $dateOptions['currentDate'];
        $currentYear = $dateOptions['currentYear'];
        $currentMonth = $dateOptions['currentMonth'];
        $currentMonthWords = $dateOptions['currentMonthWords'];
        $rangeEndDate = $currentDate->copy()->subMonth()->endOfMonth();
        $rangeStartDate = $rangeEndDate->copy()->startOfMonth()->subYear()->addMonth();

        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        try {
               $chapters = Chapters::with(['state.conference'])
                    ->whereHas('state.conference', function($q) use ($confId) {
                        $q->where('conference.id', $confId);
                    })
                    ->where('start_month_id', $currentMonth)
                    ->where('next_renewal_year', $currentYear)
                    ->where('active_status', 1)
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

                $chapterName = $chapter->name;
                $stateShortName = $chapter->state->state_short_name;

                if ($chapterName) {
                    $emailData = $this->userController->loadEmailDetails($chapter->id);
                    $emailListChap = $emailData['emailListChap'];
                    $emailListCoord = $emailData['emailListCoord'];

                    $chapterEmails[$chapterName] = $emailListChap;
                    $coordinatorEmails[$chapterName] = $emailListCoord;
                }

                $mailData[$chapterName] = [
                    'chapterName' => $chapterName,
                    'chapterState' => $stateShortName,
                    'startRange' => $rangeStartDateFormatted,
                    'endRange' => $rangeEndDateFormatted,
                    'startMonth' => $currentMonthWords,
                ];
            }

            foreach ($mailData as $chapterName => $data) {
                $to_email = $chapterEmails[$chapterName] ?? [];
                $cc_email = $coordinatorEmails[$chapterName] ?? [];

                if (! empty($to_email)) {
                    Mail::to($to_email)
                        ->cc($cc_email)
                        ->queue(new PaymentsReRegReminder($data));
                }
            }

            DB::commit();

            return redirect()->to('/payment/reregistration')->with('success', 'Re-Registration Reminders have been successfully sent.');
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
     * ReRegistration Late Notices Auto Send
     */
    public function createChapterReRegistrationLateReminder(Request $request): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $confId = $user['confId'];

        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentDate = $dateOptions['currentDate'];
        $currentYear = $dateOptions['currentYear'];
        $currentMonth = $dateOptions['currentMonth'];
        $lastMonth = $dateOptions['lastMonth'];
        if ($currentMonth == '01' && $lastMonth == '12') {
            $currentYear = $currentYear - 1;
        }
        $currentMonthWords = $dateOptions['currentMonthWords'];
        $lastMonthWords = $dateOptions['lastMonthWords'];
        $rangeEndDate = $currentDate->copy()->subMonths(2)->endOfMonth();
        $rangeStartDate = $rangeEndDate->copy()->startOfMonth()->subYear()->addMonth();

        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        try {
            $chapters = Chapters::with(['state.conference'])
                ->whereHas('state.conference', function($q) use ($confId) {
                    $q->where('conference.id', $confId);
                })
                ->where('start_month_id', $lastMonth)
                ->where('next_renewal_year', $currentYear)
                ->where('active_status', 1)
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

                $chapterName = $chapter->name;
                $stateShortName = $chapter->state->state_short_name;

                if ($chapterName) {
                    $emailData = $this->userController->loadEmailDetails($chapter->id);
                    $emailListChap = $emailData['emailListChap'];
                    $emailListCoord = $emailData['emailListCoord'];

                    $chapterEmails[$chapterName] = $emailListChap;
                    $coordinatorEmails[$chapterName] = $emailListCoord;
                }

                $mailData[$chapterName] = [
                    'chapterName' => $chapterName,
                    'chapterState' => $stateShortName,
                    'startRange' => $rangeStartDateFormatted,
                    'endRange' => $rangeEndDateFormatted,
                    'startMonth' => $lastMonthWords,
                    'dueMonth' => $currentMonthWords,
                ];
            }

            foreach ($mailData as $chapterName => $data) {
                $to_email = $chapterEmails[$chapterName] ?? [];
                $cc_email = $coordinatorEmails[$chapterName] ?? [];

                if (! empty($to_email)) {
                    Mail::to($to_email)
                        ->cc($cc_email)
                        ->queue(new PaymentsReRegLate($data));
                }
            }

            DB::commit();

            return redirect()->to('/payment/reregistration')->with('success', 'Re-Registration Late Reminders have been successfully sent.');
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
     * View Doantions List
     */
    public function showRptDonations(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status,
        ];

        return view('coordinators.payment.chapdonations')->with($data);
    }

    /**
     *Edit Chapter Information
     */
    public function editChapterPayment(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chConfId = $baseQuery['chConfId'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $startMonthName = $baseQuery['startMonthName'];
        $startDate = $baseQuery['startDate'];
        $dueDate = $baseQuery['dueDate'];
        $renewalDate = $baseQuery['renewalDate'];
        $chapterStatus = $chDetails->status->chapter_status;
        $chActiveId = $baseQuery['chActiveId'];
        $chPayments = $baseQuery['chPayments'];

        $data = ['id' => $id, 'chActiveId' => $chActiveId, 'stateShortName' => $stateShortName, 'startMonthName' => $startMonthName, 'chPayments' => $chPayments,
            'chDetails' => $chDetails, 'chapterStatus' => $chapterStatus, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'coorId' => $coorId, 'confId' => $confId, 'chConfId' => $chConfId, 'startDate' => $startDate, 'dueDate' => $dueDate, 'renewalDate' => $renewalDate
        ];

        return view('coordinators.payment.editpayment')->with($data);
    }

    /**
     *Update Chapter Information
     */
    public function updateChapterPayment(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $nextRenewalYear = $baseQuery['chDetails']->next_renewal_year;
        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];
        $emailPC = $baseQuery['emailPC'];

        $paymentType = 'Manual Input';

        $input = $request->all();
        $rereg_date = $input['PaymentDate'];
        $m2m_date = $input['M2MPaymentDate'];
        $sustaining_date = $input['SustainingPaymentDate'];

        $chapter = Chapters::find($id);
        $payments = Payments::find($id);

        DB::beginTransaction();
        try {
            $payments->rereg_notes = $input['ch_regnotes'];
            $payments->rereg_waivelate = ! isset($input['ch_waive_late']) ? null : ($input['ch_waive_late'] == 'on' ? 1 : 0);
            $payments->save();

            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            if ($rereg_date != null) {
                $chapter->next_renewal_year = $nextRenewalYear + 1;
                $chapter->save();

               // Archive current re-registration payment to history (if exists)
                if ($payments->rereg_date) {
                        PaymentHistory::create([
                        'chapter_id' => $id,
                        'payment_type' => 'rereg',
                        'payment_amount' => $payments->rereg_payment,
                        'payment_date' => $payments->rereg_date,
                        'rereg_members' => $payments->rereg_members,
                    ]);
                }

                $payments->rereg_date = $rereg_date;
                $payments->rereg_payment = $input['rereg'];
                $payments->rereg_members = $input['members'];
                $payments->save();
            }

            if ($m2m_date != null) {
                // Archive current M2M donation to history (if exists)
                if ($payments->m2m_date) {
                    PaymentHistory::create([
                        'chapter_id' => $id,
                        'payment_type' => 'm2m',
                        'payment_amount' => $payments->m2m_donation,
                        'payment_date' => $payments->m2m_date,
                    ]);
                }

                // PaymentHistory::create([
                //     'chapter_id' => $id,
                //     'payment_type' => 'm2m',
                //     'payment_amount' => $input['m2m'],
                //     'payment_date' => $m2m_date,
                // ]);

                $payments->m2m_date = $m2m_date;
                $payments->m2m_donation = $input['m2m'];
                $payments->save();
            }

            if ($sustaining_date != null) {
                // Archive current sustaining donation to history (if exists)
                if ($payments->sustaining_date) {
                    PaymentHistory::create([
                        'chapter_id' => $id,
                        'payment_type' => 'sustaining',
                        'payment_amount' => $payments->sustaining_donation,
                        'payment_date' => $payments->sustaining_date,
                    ]);
                }

                // PaymentHistory::create([
                //     'chapter_id' => $id,
                //     'payment_type' => 'sustaining',
                //     'payment_amount' => $$input['sustaining'],
                //     'payment_date' => $sustaining_date,
                // ]);

                $payments->sustaining_date = $sustaining_date;
                $payments->sustaining_donation = $input['sustaining'];
                $payments->save();
            }

            $baseQueryUpd = $this->baseChapterController->getChapterDetails($id);
            $chPayments = $baseQueryUpd['chPayments'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getPaymentData($chPayments, $input, $paymentType),
            );

            if ($request->input('ch_notify') == 'on' && $rereg_date != null) {
                Mail::to($emailListChap)
                    ->cc($emailPC)
                    ->queue(new PaymentsReRegChapterThankYou($mailData));
            }

            if ($request->input('ch_thanks') == 'on' && $m2m_date != null) {
                Mail::to($emailListChap)
                    ->cc($emailPC)
                    ->queue(new PaymentsM2MChapterThankYou($mailData));
            }

            if ($request->input('ch_sustaining') == 'on' && $sustaining_date != null) {
                Mail::to($emailListChap)
                    ->cc($emailPC)
                    ->queue(new PaymentsSustainingChapterThankYou($mailData));
            }

            DB::commit();

            return to_route('payment.editpayment', ['id' => $id])->with('success', 'Chapter Payments/Donations have been updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('payment.editpayment', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    public function viewPaymentHistory(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chActiveId = $baseQuery['chActiveId'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chPayments = $baseQuery['chPayments'];

        $startMonthName = $baseQuery['startMonthName'];
        $startDate = $baseQuery['startDate'];
        $dueDate = $baseQuery['dueDate'];
        $renewalDate = $baseQuery['renewalDate'];
        $chapterStatus = $baseQuery['chapterStatus'];

        $chDisbanded = null;

        if ($chActiveId == ChapterStatusEnum::ACTIVE) {
            $baseBoardQuery = $this->baseChapterController->getActiveBoardDetails($id);
        } elseif ($chActiveId == ChapterStatusEnum::ZAPPED) {
            $baseBoardQuery = $this->baseChapterController->getDisbandedBoardDetails($id);
            $chDisbanded = $baseBoardQuery['chDisbanded'];
        }

        // Get all rereg payment history for a chapter
        $reregHistory = PaymentHistory::where('chapter_id', $id)
        ->where('payment_type', 'rereg')
        ->orderBy('payment_date', 'desc')
        ->get();

        $m2mHistory = PaymentHistory::where('chapter_id', $id)
        ->where('payment_type', 'm2m')
        ->orderBy('payment_date', 'desc')
        ->get();

        $sustainingHistory = PaymentHistory::where('chapter_id', $id)
        ->where('payment_type', 'sustaining')
        ->orderBy('payment_date', 'desc')
        ->get();

        $grantRequests = GrantRequest::where('chapter_id', $id)
        ->orderBy('submitted_at', 'desc')
        ->get();

        $data = ['id' => $id, 'chActiveId' => $chActiveId, 'chDetails' => $chDetails, 'conferenceDescription' => $conferenceDescription, 'chDisbanded' => $chDisbanded,
            'startMonthName' => $startMonthName, 'confId' => $confId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chapterStatus' => $chapterStatus,
            'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'chPayments' => $chPayments, 'grantRequests' => $grantRequests,
            'reregHistory' => $reregHistory, 'm2mHistory' => $m2mHistory, 'sustainingHistory' => $sustainingHistory, 'startDate' => $startDate, 'dueDate' => $dueDate, 'renewalDate' => $renewalDate
        ];

        return view('coordinators.payment.paymenthistory')->with($data);
    }

      public function showGrantList(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $checkBox51Status = $request->has(\App\Enums\CheckboxFilterEnum::INTERNATIONAL);

         // Use the appropriate query based on checkbox status
        if ($checkBox51Status) {
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

        $data = ['grantList' => $grantList, 'checkBox51Status' => $checkBox51Status];

        return view('coordinators.payment.grantlist')->with($data);
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

        return view('coordinators.payment.editgrantdetails')->with($data);
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
