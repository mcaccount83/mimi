<?php

namespace App\Http\Controllers;

use App\Enums\ChapterCheckbox;
use App\Mail\PaymentsM2MChapterThankYou;
use App\Mail\PaymentsReRegChapterThankYou;
use App\Mail\PaymentsReRegLate;
use App\Mail\PaymentsReRegReminder;
use App\Mail\PaymentsSustainingChapterThankYou;
use App\Models\Chapters;
use App\Models\Payments;
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

class PaymentReportController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseChapterController;

    protected $baseMailDataController;

    protected PositionConditionsService $positionConditionsService;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, BaseMailDataController $baseMailDataController,
        PositionConditionsService $positionConditionsService  )
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
        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];
        $checkBox6Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONALREREG];

        if ($checkBox3Status || $checkBox5Status) {
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
        $data = ['countList' => $countList, 'reChapterList' => $reChapterList, 'checkBoxStatus' => $checkBoxStatus,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status, 'checkBox6Status' => $checkBox6Status,
        ];

        return view('payment.chapreregistration')->with($data);
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
            $chapters = Chapters::with(['state', 'conference', 'region'])
                ->where('conference_id', $confId)
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
            $chapters = Chapters::with(['state', 'conference', 'region'])
                ->where('chapters.conference_id', $confId)
                ->where('chapters.start_month_id', $lastMonth)
                ->where('chapters.next_renewal_year', $currentYear)
                ->where('chapters.active_status', 1)
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
        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status,
        ];

        return view('payment.chapdonations')->with($data);
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
        $chapterStatus = $chDetails->status->chapter_status;
        $chActiveId = $baseQuery['chActiveId'];
        $chPayments = $baseQuery['chPayments'];

        $data = ['id' => $id, 'chActiveId' => $chActiveId, 'stateShortName' => $stateShortName, 'startMonthName' => $startMonthName, 'chPayments' => $chPayments,
            'chDetails' => $chDetails, 'chapterStatus' => $chapterStatus, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'coorId' => $coorId, 'confId' => $confId, 'chConfId' => $chConfId,
        ];

        return view('payment.editpayment')->with($data);
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

                $payments->rereg_date = $rereg_date;
                $payments->rereg_members = $input['members'];
                $payments->save();
            }

            if ($m2m_date != null) {
                $payments->m2m_date = $m2m_date;
                $payments->m2m_donation = $input['m2m'];
                $payments->save();
            }

            if ($sustaining_date != null) {
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
}
