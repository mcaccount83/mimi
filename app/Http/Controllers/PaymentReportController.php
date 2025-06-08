<?php

namespace App\Http\Controllers;

use App\Mail\PaymentsM2MChapterThankYou;
use App\Mail\PaymentsReRegChapterThankYou;
use App\Mail\PaymentsReRegLate;
use App\Mail\PaymentsReRegReminder;
use App\Mail\PaymentsSustainingChapterThankYou;
use App\Models\Chapters;
use App\Models\Payments;
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

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, BaseMailDataController $baseMailDataController)
    {

        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->baseMailDataController = $baseMailDataController;
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
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $currentYear = date('Y');
        $currentMonth = date('m');

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox3Status = $baseQuery['checkBox3Status'];

        if ($checkBox3Status) {
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
        $data = ['countList' => $countList, 'reChapterList' => $reChapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox3Status' => $checkBox3Status];

        return view('chapters.chapreregistration')->with($data);
    }

    /**
     * ReRegistration List
     */
    public function showIntReRegistration(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $currentYear = date('Y');
        $currentMonth = date('m');

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);

        $reChapterList = $baseQuery['query']
            ->where(function ($query) use ($currentYear, $currentMonth) {
                $query->where('next_renewal_year', '<', $currentYear)
                    ->orWhere(function ($query) use ($currentYear, $currentMonth) {
                        $query->where('next_renewal_year', '=', $currentYear)
                            ->where('start_month_id', '<=', $currentMonth);
                    });
            })
            ->get();

        $data = ['reChapterList' => $reChapterList];

        return view('international.intregistration')->with($data);
    }

    /**
     * ReRegistration Reminders Auto Send
     */
    public function createChapterReRegistrationReminder(Request $request): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $confId = $user['user_confId'];

        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;
        $monthInWords = $now->format('F');
        $rangeEndDate = $now->copy()->subMonth()->endOfMonth();
        $rangeStartDate = $rangeEndDate->copy()->startOfMonth()->subYear()->addMonth();

        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        try {
            $chapters = Chapters::with(['state', 'conference', 'region'])
                ->where('conference_id', $confId)
                ->where('start_month_id', $month)
                ->where('next_renewal_year', $year)
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
                    'startMonth' => $monthInWords,
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

            return redirect()->to('/chapter/reregistration')->with('success', 'Re-Registration Reminders have been successfully sent.');
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
        $confId = $user['user_confId'];

        $now = Carbon::now();
        $month = $now->month;
        $lastMonth = $now->copy()->subMonth()->format('m');
        $year = $now->year;
        if ($now->format('m') == '01' && $lastMonth == '12') {
            $year = $now->year - 1;
        }
        $monthInWords = $now->format('F');
        $lastMonthInWords = $now->copy()->subMonth()->format('F');
        $rangeEndDate = $now->copy()->subMonths(2)->endOfMonth();
        $rangeStartDate = $rangeEndDate->copy()->startOfMonth()->subYear()->addMonth();

        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        try {
            $chapters = Chapters::with(['state', 'conference', 'region'])
                ->where('chapters.conference_id', $confId)
                ->where('chapters.start_month_id', $lastMonth)
                ->where('chapters.next_renewal_year', $year)
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
                    'startMonth' => $lastMonthInWords,
                    'dueMonth' => $monthInWords,
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

            return redirect()->to('/chapter/reregistration')->with('success', 'Re-Registration Late Reminders have been successfully sent.');
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
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('chapters.chapdonations')->with($data);
    }

    /**
     * View the International M2M Doantions
     */
    public function showIntdonation(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);
        $chapterList = $baseQuery['query']->get();

        $data = ['chapterList' => $chapterList];

        return view('international.intdonation')->with($data);
    }

    /**
     *Edit Chapter Information
     */
    public function editChapterPayment(Request $request, $id): View
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $chDetails->status->chapter_status;
        $chActiveId = $baseQuery['chActiveId'];
        $chPayments = $baseQuery['chPayments'];

        $data = ['id' => $id, 'chActiveId' => $chActiveId, 'stateShortName' => $stateShortName, 'startMonthName' => $startMonthName, 'chPayments' => $chPayments,
            'chDetails' => $chDetails, 'chapterStatus' => $chapterStatus, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
        ];

        return view('chapters.editpayment')->with($data);
    }

    /**
     *Update Chapter Information
     */
    public function updateChapterPayment(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

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
            $payments->rereg_waivelate = ! isset($input['ch_waive_late']) ? null : ($input['ch_waive_late'] === 'on' ? 1 : 0);
            $payments->save();

            $chapter->last_updated_by = $lastUpdatedBy;
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

            return to_route('chapters.editpayment', ['id' => $id])->with('success', 'Chapter Payments/Donations have been updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('chapters.editpayment', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }
}
