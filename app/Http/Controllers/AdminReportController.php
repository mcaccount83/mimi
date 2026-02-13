<?php

namespace App\Http\Controllers;

use App\Enums\CheckboxFilterEnum;
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

use App\Services\PositionConditionsService;
use Dcblogdev\LaravelSentEmails\Models\SentEmail;


class AdminReportController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseChapterController;

    protected $baseCoordinatorController;

    protected $positionConditionsService;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, BaseCoordinatorController $baseCoordinatorController,
             PositionConditionsService $positionConditionsService)
    {
        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->baseCoordinatorController = $baseCoordinatorController;
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
     * View Payment Log List
     */
    public function showPaymentLog(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $confId = $user['confId'];

        $query = PaymentLog::with('board');

        // Check if international checkbox is selected
        $showInternational = $request->has(CheckboxFilterEnum::INTERNATIONAL) &&
                            $request->get(CheckboxFilterEnum::INTERNATIONAL) == 'yes';

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

        return view('adminreports.paymentlog')->with($data);
    }

    /**
     * View Payment Log Transaction Details
     */
    public function showPaymentDetails($id): View
    {
        $log = PaymentLog::findOrFail($id);

        $data = ['log' => $log];

        return view('adminreports.paymentdetails')->with($data);
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
        $query->where(function($q) {
            $q->where('payment_history.payment_type', 'm2m')
              ->orWhere('payment_history.payment_type', 'sustaining');
        });
    }

    // Add conference filter
    if (!$checkBox51Status && !$checkBox58Status) {
        // Not showing international - filter by conference
        $query->where('chapters.conference_id', $confId);
    }
    // If checkBox51Status OR checkBox10Status is true, show all conferences (international)

    $donationsList = $query->orderBy('payment_history.payment_date', 'desc')->get();

    $data = [
        'donationsList' => $donationsList,
        'checkBox51Status' => $checkBox51Status ? 'checked' : '',
        'checkBox8Status' => $checkBox8Status ? 'checked' : '',
        'checkBox58Status' => $checkBox58Status ? 'checked' : '',
    ];

    return view('adminreports.donationlog')->with($data);
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

        return view('adminreports.rereg')->with($data);
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

        return view('adminreports.editrereg')->with($data);
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

            return redirect()->to('/adminreports/reregedit')->with('error', 'Failed to update Re-Reg Info.');
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/adminreports/reregedit')->with('success', 'Re-Reg Info updated successfully.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
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
            }
        ])
        ->join('conference', 'region.conference_id', '=', 'conference.id');

        // Add conference filter if not showing international
        if (!$checkBox51Status) {
            $query->where('region.conference_id', $confId);
        }

        $regList = $query
            ->orderBy('conference.short_name')
            ->orderBy('region.long_name')
            ->select('region.*')
            ->get();

        $data = ['regList' => $regList, 'checkBox51Status' => $checkBox51Status];

        return view('adminreports.inquiriesnotify')->with($data);
    }

   public function updateInquiriesEmail(Request $request, $id)
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
        Log::error('Inquiries validation error: ' . json_encode($e->errors()));

        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Inquiries update error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

/**
     * View the EOY Report Title
     */
    public function getPageTitle(Request $request)
    {
        $titles = [
            'admin_reports' => 'IT Reports',
            'admin_details' => 'Chapter Details',
            'resource_reports' => 'Resources',
            'resource_details' => 'Resource Details',
        ];

        return $titles;
    }

 /**
     * View the Downloads List
     */
    public function showDownloads(Request $request): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['resource_reports'];
        $breadcrumb = 'Download Reports';

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb];

        return view('adminreports.downloads')->with($data);
    }


    //   public function showMailLog(Request $request): View
    // {
    //     $user = $this->userController->loadUserInformation($request);
    //     $confId = $user['confId'];
    //     $regId = $user['regId'] ?? null;
    //     $userEmail = $user['userEmail'];

    //     // Get position conditions
    //     $positionId = $user['cdPositionId'] ?? 0;
    //     $secPositionId = $user['cdSecPositionId'] ?? [];
    //     $coorId = $user['cdId'] ?? null;

    //     // Get emails for position conditions
    //     $conditions = $this->positionConditionsService->getConditionsForUser($positionId, $secPositionId, $coorId);
    //     $coordinatorCondition = $conditions['coordinatorCondition'];
    //     $conferenceCoordinatorCondition = $conditions['conferenceCoordinatorCondition'];
    //     $inquiriesCondition = $conditions['inquiriesCondition'];
    //     $inquiriesInternationalCondition = $conditions['inquiriesInternationalCondition'];

    //     $adminEmail = $this->positionConditionsService->getAdminEmail();
    //     $listAdmin = $adminEmail['list_admin'];

    //     $checkBox5Status = $request->has(\App\Enums\CheckboxFilterEnum::LIST);
    //     $checkBox7Status = $request->has(\App\Enums\CheckboxFilterEnum::INQUIRIES);
    //     $checkBox81Status = $request->has(\App\Enums\CheckboxFilterEnum::ADMIN);
    //     $checkBox57Status = $request->has(\App\Enums\CheckboxFilterEnum::INTERNATIONALINQUIRIES);

    //     // Add view filter based on checkboxes
    //     if ($checkBox81Status) {
    //         // Show ALL emails
    //         $toMaillog = SentEmail::with('attachments')
    //             ->orderby('id', 'desc')
    //             ->get();
    //         $ccMaillog = [];
    //         $fromMaillog = [];

    //     } elseif ($checkBox5Status) {
    //         // Show LIST admin emails only
    //          $toMaillog = SentEmail::with('attachments')
    //             ->where(function($query) use ($listAdmin,) {
    //                 $query->Where('to', 'LIKE', '%' . $listAdmin . '%');
    //             })
    //             ->orderBy('id', 'desc')
    //             ->get();
    //         $ccMaillog = SentEmail::with('attachments')
    //             ->where(function($query) use ($listAdmin,) {
    //                 $query->Where('cc', 'LIKE', '%' . $listAdmin . '%')
    //                     ->orWhere('bcc', 'LIKE', '%' . $listAdmin . '%');
    //             })
    //             ->orderBy('id', 'desc')
    //             ->get();
    //         $fromMaillog = SentEmail::with('attachments')
    //             ->where(function($query) use ($listAdmin,) {
    //                 $query->where('from', 'LIKE', '%' . $listAdmin . '%');
    //             })
    //             ->orderBy('id', 'desc')
    //             ->get();

    //     } elseif ($checkBox7Status) {
    //         $inquiriesEmails = [];
    //             // International Inquiries Coordinator - get ALL inquiries emails
    //             if ($inquiriesInternationalCondition){
    //                 $regions = Region::with('inquiries')->get();

    //                 foreach ($regions as $region) {
    //                     if ($region->inquiries && $region->inquiries->inquiries_email) {
    //                         $inquiriesEmails[] = $region->inquiries->inquiries_email;
    //                     }
    //                 }

    //                 // Remove duplicates
    //                 $inquiriesEmails = array_unique($inquiriesEmails);

    //             } elseif (($coordinatorCondition && $conferenceCoordinatorCondition) || $inquiriesCondition ) {
    //                 if ($regId && $regId > 0) {
    //                     // User has a specific region - get that region's inquiries email
    //                     $region = Region::with('inquiries')->find($regId);
    //                     if ($region && $region->inquiries && $region->inquiries->inquiries_email) {
    //                         $inquiriesEmails[] = $region->inquiries->inquiries_email;
    //                     }
    //                 } else {
    //                     // Conference coordinator - get all inquiries emails for their conference
    //                     $regions = Region::with('inquiries')
    //                         ->where('conference_id', $confId)
    //                         ->get();

    //                     foreach ($regions as $region) {
    //                         if ($region->inquiries && $region->inquiries->inquiries_email) {
    //                             $inquiriesEmails[] = $region->inquiries->inquiries_email;
    //                         }
    //                     }

    //                     // Remove duplicates
    //                     $inquiriesEmails = array_unique($inquiriesEmails);
    //                 }
    //             }
    //         // Show INQUIRIES emails only
    //         $toMaillog = SentEmail::with('attachments')
    //             ->where(function($query) use ($inquiriesEmails) {
    //                 foreach ($inquiriesEmails as $inquiriesEmail) {
    //                     $query->orWhere('to', 'LIKE', '%' . $inquiriesEmail . '%');
    //                 }
    //             })
    //             ->orderBy('id', 'desc')
    //             ->get();
    //         $ccMaillog = SentEmail::with('attachments')
    //             ->where(function($query) use ($inquiriesEmails) {
    //                 foreach ($inquiriesEmails as $inquiriesEmail) {
    //                     $query->orWhere('cc', 'LIKE', '%' . $inquiriesEmail . '%')
    //                         ->orWhere('bcc', 'LIKE', '%' . $inquiriesEmail . '%');
    //                 }
    //             })
    //             ->orderBy('id', 'desc')
    //             ->get();
    //         $fromMaillog = SentEmail::with('attachments')
    //             ->where(function($query) use ($inquiriesEmails) {
    //                 foreach ($inquiriesEmails as $inquiriesEmail) {
    //                     $query->orWhere('from', 'LIKE', '%' . $inquiriesEmail . '%');
    //                 }
    //             })
    //             ->orderBy('id', 'desc')
    //             ->get();

    //     } elseif ($checkBox57Status) {
    //     $inquiriesEmails = [];
    //         // International Inquiries Coordinator - get ALL inquiries emails
    //             $regions = Region::with('inquiries')->get();

    //             foreach ($regions as $region) {
    //                 if ($region->inquiries && $region->inquiries->inquiries_email) {
    //                     $inquiriesEmails[] = $region->inquiries->inquiries_email;
    //                 }
    //             }

    //             // Remove duplicates
    //             $inquiriesEmails = array_unique($inquiriesEmails);

    //         // Show INQUIRIES emails only
    //         $toMaillog = SentEmail::with('attachments')
    //             ->where(function($query) use ($inquiriesEmails) {
    //                 foreach ($inquiriesEmails as $inquiriesEmail) {
    //                     $query->orWhere('to', 'LIKE', '%' . $inquiriesEmail . '%');
    //                 }
    //             })
    //             ->orderBy('id', 'desc')
    //             ->get();
    //         $ccMaillog = SentEmail::with('attachments')
    //             ->where(function($query) use ($inquiriesEmails) {
    //                 foreach ($inquiriesEmails as $inquiriesEmail) {
    //                     $query->orWhere('cc', 'LIKE', '%' . $inquiriesEmail . '%')
    //                         ->orWhere('bcc', 'LIKE', '%' . $inquiriesEmail . '%');
    //                 }
    //             })
    //             ->orderBy('id', 'desc')
    //             ->get();
    //         $fromMaillog = SentEmail::with('attachments')
    //             ->where(function($query) use ($inquiriesEmails) {
    //                 foreach ($inquiriesEmails as $inquiriesEmail) {
    //                     $query->orWhere('from', 'LIKE', '%' . $inquiriesEmail . '%');
    //                 }
    //             })
    //             ->orderBy('id', 'desc')
    //             ->get();

    //     } else {
    //         // Default - show user's personal emails plus position-based emails (your existing logic)
    //         $toMaillog = SentEmail::with('attachments')
    //             ->where(function($query) use ($userEmail,) {
    //                 $query->Where('to', 'LIKE', '%' . $userEmail . '%');
    //             })
    //             ->orderBy('id', 'desc')
    //             ->get();
    //         $ccMaillog = SentEmail::with('attachments')
    //             ->where(function($query) use ($userEmail,) {
    //                 $query->Where('cc', 'LIKE', '%' . $userEmail . '%')
    //                     ->orWhere('bcc', 'LIKE', '%' . $userEmail . '%');
    //             })
    //             ->orderBy('id', 'desc')
    //             ->get();
    //         $fromMaillog = SentEmail::with('attachments')
    //             ->where(function($query) use ($userEmail,) {
    //                 $query->where('from', 'LIKE', '%' . $userEmail . '%');
    //             })
    //             ->orderBy('id', 'desc')
    //             ->get();
    //     }

    //     $data = ['checkBox81Status' => $checkBox81Status, 'checkBox5Status' => $checkBox5Status, 'checkBox7Status' => $checkBox7Status,
    //         'checkBox57Status' => $checkBox57Status, 'toMaillog' => $toMaillog, 'fromMaillog' => $fromMaillog, 'ccMaillog' => $ccMaillog
    //     ];

    //     return view('adminreports.maillog')->with($data);
    // }

    public function showMailLog(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $confId = $user['confId'];
        $regId = $user['regId'] ?? null;
        $userEmail = $user['userEmail'];

        // Get position conditions
        $positionId = $user['cdPositionId'] ?? 0;
        $secPositionId = $user['cdSecPositionId'] ?? [];
        $coorId = $user['cdId'] ?? null;

        $conditions = $this->positionConditionsService->getConditionsForUser($positionId, $secPositionId, $coorId);
        $coordinatorCondition = $conditions['coordinatorCondition'];
        $conferenceCoordinatorCondition = $conditions['conferenceCoordinatorCondition'];
        $inquiriesCondition = $conditions['inquiriesCondition'];
        $inquiriesInternationalCondition = $conditions['inquiriesInternationalCondition'];

        $adminEmail = $this->positionConditionsService->getAdminEmail();
        $listAdmin = $adminEmail['list_admin'];

        $checkBox5Status = $request->has(\App\Enums\CheckboxFilterEnum::LIST);
        $checkBox7Status = $request->has(\App\Enums\CheckboxFilterEnum::INQUIRIES);
        $checkBox81Status = $request->has(\App\Enums\CheckboxFilterEnum::ADMIN);
        $checkBox57Status = $request->has(\App\Enums\CheckboxFilterEnum::INTERNATIONALINQUIRIES);

        // Determine which emails to filter by
        $filterEmails = [];

        if ($checkBox81Status) {
            // Show ALL emails - no filter needed
            $filterEmails = null;

        } elseif ($checkBox5Status) {
            // Show LIST admin emails only
            $filterEmails = [$listAdmin];

        } elseif ($checkBox7Status || $checkBox57Status) {
            // Get inquiries emails based on checkbox
            if ($checkBox57Status || $inquiriesInternationalCondition) {
                // All inquiries emails
                $regions = Region::with('inquiries')->get();
            } elseif (($coordinatorCondition && $conferenceCoordinatorCondition) || $inquiriesCondition) {
                if ($regId && $regId > 0) {
                    // Specific region
                    $regions = Region::with('inquiries')->where('id', $regId)->get();
                } else {
                    // Conference regions
                    $regions = Region::with('inquiries')->where('conference_id', $confId)->get();
                }
            }

            foreach ($regions as $region) {
                if ($region->inquiries && $region->inquiries->inquiries_email) {
                    $filterEmails[] = $region->inquiries->inquiries_email;
                }
            }
            $filterEmails = array_unique($filterEmails);

        } else {
            // Default - user's personal email
            $filterEmails = [$userEmail];
        }

        // Build queries based on filter
        if ($filterEmails === null) {
            // Show all emails
            $toMaillog = SentEmail::with('attachments')->orderBy('id', 'desc')->get();
            $ccMaillog = collect([]);
            $fromMaillog = collect([]);
        } else {
            // Filter by email addresses
            $toMaillog = SentEmail::with('attachments')
                ->where(function($query) use ($filterEmails) {
                    foreach ($filterEmails as $email) {
                        $query->orWhere('to', 'LIKE', '%' . $email . '%');
                    }
                })
                ->orderBy('id', 'desc')
                ->get();

            $ccMaillog = SentEmail::with('attachments')
                ->where(function($query) use ($filterEmails) {
                    foreach ($filterEmails as $email) {
                        $query->orWhere('cc', 'LIKE', '%' . $email . '%')
                            ->orWhere('bcc', 'LIKE', '%' . $email . '%');
                    }
                })
                ->orderBy('id', 'desc')
                ->get();

            $fromMaillog = SentEmail::with('attachments')
                ->where(function($query) use ($filterEmails) {
                    foreach ($filterEmails as $email) {
                        $query->orWhere('from', 'LIKE', '%' . $email . '%');
                    }
                })
                ->orderBy('id', 'desc')
                ->get();
        }

        $data = [
            'checkBox81Status' => $checkBox81Status, 'checkBox5Status' => $checkBox5Status, 'checkBox7Status' => $checkBox7Status,
            'checkBox57Status' => $checkBox57Status, 'toMaillog' => $toMaillog, 'fromMaillog' => $fromMaillog, 'ccMaillog' => $ccMaillog
        ];

        return view('adminreports.maillog')->with($data);
    }

    /**
     * View Mail Log Details
     */
    public function showMailDetails($id): View
    {
        $log = SentEmail::findOrFail($id);

        $data = ['log' => $log];

        return view('adminreports.maildetails')->with($data);
    }


}
