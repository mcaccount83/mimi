<?php

namespace App\Http\Controllers;

use App\Enums\CheckboxFilterEnum;
use App\Http\Requests\AddEmailCampaignAdminReportRequest;
use App\Http\Requests\UpdateEmailCampaignAdminReportRequest;
use App\Models\Chapters;
use App\Models\EmailCampaign;
use App\Models\Month;
use App\Models\PaymentHistory;
use App\Models\PaymentLog;
use App\Models\Payments;
use App\Models\Region;
use App\Models\RegionInquiry;
use App\Services\PositionConditionsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminReportController extends Controller implements HasMiddleware
{
    public function __construct(
        protected UserController $userController,
        protected BaseChapterController $baseChapterController,
        protected BaseCoordinatorController $baseCoordinatorController,
        protected PositionConditionsService $positionConditionsService,
    ) {}

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
        $showInternational = $request->has(CheckboxFilterEnum::INTERNATIONAL) &&
                            $request->input(CheckboxFilterEnum::INTERNATIONAL) == 'yes';

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
        $checkBox51Status = $showInternational ? 'checked' : '';

        $data = [
            'paymentLogs' => $paymentLogs,
            'checkBox51Status' => $checkBox51Status,
        ];

        return view('coordinators.adminreports.paymentlog')->with($data);
    }

    /**
     * View Payment Log Transaction Details
     */
    public function showPaymentDetails(int $id): View
    {
        $log = PaymentLog::findOrFail($id);

        $data = ['log' => $log];

        return view('coordinators.adminreports.paymentdetails')->with($data);
    }

    public function showDonationLog(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $confId = $user['confId'];

        $checkBox51Status = $request->has(\App\Enums\CheckboxFilterEnum::INTERNATIONAL);
        $checkBox8Status = $request->has(\App\Enums\CheckboxFilterEnum::M2M);
        $checkBox58Status = $request->has(\App\Enums\CheckboxFilterEnum::INTERNATIONALM2M);

        // Base query
        $query = PaymentHistory::with('chapter')
            ->join('chapters', 'payment_history.chapter_id', '=', 'chapters.id')
            ->where('chapters.active_status', '1');

        // Add payment type filter based on checkboxes
        if ($checkBox8Status) {
            // Show only M2M donations
            $query->where('payment_history.payment_type', 'm2m');
        } elseif ($checkBox58Status) {
            // Show international M2M donations
            $query->where('payment_history.payment_type', 'm2m');
        } else {
            // Show both M2M and sustaining (default)
            $query->where(function ($q) {
                $q->where('payment_history.payment_type', 'm2m')
                    ->orWhere('payment_history.payment_type', 'sustaining');
            });
        }

        // Add conference filter
        if (! $checkBox51Status && ! $checkBox58Status) {
            // Not showing international - filter by conference
            $query->where('chapters.conference_id', $confId);
        }

        // If checkBox51Status OR checkBox10Status is true, show all conferences (international)

        $donationsList = $query->orderByDesc('payment_history.payment_date')->get();

        $data = [
            'donationsList' => $donationsList,
            'checkBox51Status' => $checkBox51Status ? 'checked' : '',
            'checkBox8Status' => $checkBox8Status ? 'checked' : '',
            'checkBox58Status' => $checkBox58Status ? 'checked' : '',
        ];

        return view('coordinators.adminreports.donationlog')->with($data);
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
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $data = ['chapterList' => $chapterList, 'checkBox51Status' => $checkBox51Status];

        return view('coordinators.adminreports.rereg')->with($data);
    }

    public function editReReg(Request $request, int $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chConfId = $baseQuery['chConfId'];
        $chActiveId = $baseQuery['chActiveId'];
        $chPayments = $baseQuery['chPayments'];

        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chPcId = $baseQuery['chPcId'];

        $startMonthName = $baseQuery['startMonthName'];
        $startDate = $baseQuery['startDate'];
        $dueDate = $baseQuery['dueDate'];
        $renewalDate = $baseQuery['renewalDate'];
        $chapterStatus = $baseQuery['chapterStatus'];

        $allMonths = $baseQuery['allMonths'];

        $data = ['id' => $id, 'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'chPayments' => $chPayments, 'allMonths' => $allMonths,
            'confId' => $confId, 'chConfId' => $chConfId, 'conferenceDescription' => $conferenceDescription,  'regionLongName' => $regionLongName,
            'startDate' => $startDate, 'dueDate' => $dueDate, 'renewalDate' => $renewalDate,  'chapterStatus' => $chapterStatus, 'startMonthName' => $startMonthName,
        ];

        return view('coordinators.adminreports.editrereg')->with($data);
    }

    public function updateReReg(Request $request, int $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $chapter = Chapters::findOrFail($id);
        $payments = Payments::findOrFail($id);

        DB::beginTransaction();
        try {
            $chapter->start_month_id = $request->ch_founddate;
            $chapter->next_renewal_year = $request->ch_renewyear;
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            $payments->rereg_date = $request->ch_duespaid ? Carbon::createFromFormat('m/d/Y', $request->ch_duespaid)->format('Y-m-d') : null;
            $payments->rereg_payment = isset($request->ch_payment) ? preg_replace('/[^\d.]/', '', $request->ch_payment) : null;
            $payments->rereg_members = $request->ch_members;
            $payments->save();

            DB::commit();

            return redirect()->route('adminreports.editrereg', $id)->with('success', 'Re-Reg Info updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('adminreports.editrereg', $id)->with('error', 'Failed to update Re-Reg Info.');
        }
    }

    public function inquiriesNotify(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $confId = $user['confId'];

        $checkBox51Status = $request->has(\App\Enums\CheckboxFilterEnum::INTERNATIONAL);

        // Base query
        $query = Region::with([
            'inquiries',
            'conference',
            'states' => function ($query) {
                $query->orderBy('state_short_name');
            },
        ])
            ->join('conference', 'region.conference_id', '=', 'conference.id');

        // Add conference filter if not showing international
        if (! $checkBox51Status) {
            $query->where('region.conference_id', $confId);
        }

        $regList = $query
            ->orderBy('conference.short_name')
            ->orderBy('region.long_name')
            ->select('region.*')
            ->get();

        $data = ['regList' => $regList, 'checkBox51Status' => $checkBox51Status];

        return view('coordinators.adminreports.inquiriesnotify')->with($data);
    }

    public function updateInquiriesEmail(Request $request, int $id): JsonResponse
    {
        try {
            $region = Region::findOrFail($id);

            // Find or create the RegionInquiry record
            $inquiries = RegionInquiry::firstOrNew(['region_id' => $region->id]);

            $inquiries->inquiries_email = $request->inquiries_email;
            $inquiries->save();

            return response()->json([
                'success' => true,
                'message' => 'Inquiries information updated successfully!',
                'email' => $request->inquiries_email,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Inquiries validation error: '.json_encode($e->errors()));

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Inquiries update error: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    public function inquiriesMap(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $confId = $user['confId'];

        $checkBox51Status = $request->has(\App\Enums\CheckboxFilterEnum::INTERNATIONAL);

        // Base query
        $query = Region::with([
            'inquiries',
            'conference',
            'states' => function ($query) {
                $query->orderBy('state_short_name');
            },
        ])
            ->join('conference', 'region.conference_id', '=', 'conference.id');

        // Add conference filter if not showing international
        if (! $checkBox51Status) {
            $query->where('region.conference_id', $confId);
        }

        $regList = $query
            ->orderBy('conference.short_name')
            ->orderBy('region.long_name')
            ->select('region.*')
            ->get();

        $data = ['regList' => $regList, 'checkBox51Status' => $checkBox51Status];

        return view('coordinators.adminreports.inquiriesmap')->with($data);
    }

    public function updateInquiriesMap(Request $request, int $id): JsonResponse
    {
        try {
            $region = Region::findOrFail($id);

            // Find or create the RegionInquiry record
            $inquiries = RegionInquiry::firstOrNew(['region_id' => $region->id]);

            $inquiries->inquiries_map_link = $request->inquiries_link;
            $inquiries->save();

            return response()->json([
                'success' => true,
                'message' => 'Inquiries information updated successfully!',
                'link' => $request->inquiries_link,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Inquiries validation error: '.json_encode($e->errors()));

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Inquiries update error: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * View the Downloads List
     */
    public function showDownloads(Request $request): View
    {
        $data = [];

        return view('coordinators.adminreports.downloads')->with($data);
    }

    /**
     * View the Email Campaigns List
     */
    public function showEmailCampaigns(): View
    {
        $campaigns = EmailCampaign::orderBy('month')->orderBy('label')->get()->groupBy('month');

        $allMonths = Month::orderBy('id')->pluck('month_long_name', 'id');

        // Reorder starting from July (7) through June (6) to match fiscal year
        $monthNames = $allMonths->slice(6)->union($allMonths->slice(0, 6));

        $data = ['campaigns' => $campaigns, 'monthNames' => $monthNames];

        return view('coordinators.adminreports.emailcampaigns')->with($data);
    }

    public function addEmailCampaigns(AddEmailCampaignAdminReportRequest $request): JsonResponse
    {
        try {
            $campaign = new EmailCampaign;
            $campaign->campaign = $request->campaign;
            $campaign->label = $request->label;
            $campaign->month = $request->month;
            $campaign->route_name = $request->route_name;
            $campaign->confirm_fn = $request->confirm_fn;
            $campaign->preview_slug = $request->preview_slug;
            $campaign->attachments = $request->filled('attachments')
    ? array_filter(array_map('trim', explode(',', $request->attachments)))
    : null;
            $campaign->active = $request->boolean('active');
            $campaign->save();

            return response()->json([
                'success' => true,
                'message' => 'Email campaign added successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Email campaign creation error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error adding campaign. Please try again.',
            ], 500);
        }
    }

    public function updateEmailCampaigns(UpdateEmailCampaignAdminReportRequest $request, int $id): JsonResponse
    {
        try {
            $campaign = EmailCampaign::findOrFail($id);
            $campaign->campaign = $request->campaign;
            $campaign->label = $request->label;
            $campaign->month = $request->month;
            $campaign->route_name = $request->route_name;
            $campaign->confirm_fn = $request->confirm_fn;
            $campaign->preview_slug = $request->preview_slug;
            $campaign->attachments = $request->filled('attachments')
    ? array_filter(array_map('trim', explode(',', $request->attachments)))
    : null;
            $campaign->active = $request->boolean('active');
            $campaign->save();

            return response()->json([
                'success' => true,
                'message' => 'Email campaign updated successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Email campaign update error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating campaign. Please try again.',
            ], 500);
        }
    }

    public function deleteEmailCampaigns(Request $request): JsonResponse
    {
        try {
            EmailCampaign::where('id', $request->input('campaignId'))->delete();

            return response()->json(['success' => 'Email campaign successfully deleted.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['fail' => 'Something went wrong, Please try again.'], 500);
        }
    }
}
