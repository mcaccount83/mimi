<?php

namespace App\Http\Controllers;

use App\Enums\ChapterCheckbox;
use App\Models\Chapters;
use App\Models\PaymentLog;
use App\Models\Payments;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminReportController extends Controller implements HasMiddleware
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
    // public function intPaymentList(Request $request): View
    // {
    //     $query = PaymentLog::with('board');

    //     // Add filters if needed
    //     if ($request->has('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     if ($request->has('date')) {
    //         $query->whereDate('created_at', $request->date);
    //     }

    //     // $paymentLogs = $query->orderBy('created_at', 'desc')->paginate(100);
    //     $paymentLogs = $query->orderByDesc('created_at')->paginate(100);

    //     return view('adminreports.intpaymentlist', compact('paymentLogs'));
    // }

    /**
     * View Payment Log List
     */
    public function paymentList(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $confId = $user['user_confId'];

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

        return view('adminreports.paymentlist')->with($data);
    }

    /**
     * View Payment Log Transaction Details
     */
    public function paymentDetails($id): View
    {
        $log = PaymentLog::findOrFail($id);

        $data = ['log' => $log];

        return view('adminreports.paymentdetails')->with($data);
    }

    /**
     * View List of ReReg Payments if Dates Need to be Udpated
     */
    public function showReRegDate(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $data = ['chapterList' => $chapterList, 'checkBox5Status' => $checkBox5Status];

        return view('adminreports.reregdate')->with($data);
    }

    /**
     * View List of International ReReg Payments if Dates Need to be Udpated
     */
    // public function showIntReRegDate(Request $request): View
    // {
    //     $user = $this->userController->loadUserInformation($request);
    //     $coorId = $user['user_coorId'];

    //     $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);
    //     $chapterList = $baseQuery['query']->get();

    //     $data = ['chapterList' => $chapterList];

    //     return view('adminreports.intreregdate')->with($data);
    // }

    public function editReRegDate(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chConfId = $baseQuery['chConfId'];
        $stateShortName = $baseQuery['stateShortName'];
        $chPayments = $baseQuery['chPayments'];
        $allMonths = $baseQuery['allMonths'];

        $data = ['id' => $id, 'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'chPayments' => $chPayments, 'allMonths' => $allMonths,
                    'confId' => $confId, 'chConfId' => $chConfId];

        return view('adminreports.editreregdate')->with($data);
    }

    public function updateReRegDate(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $chapter = Chapters::find($id);
        $payments = Payments::find($id);

        DB::beginTransaction();
        try {
            $chapter->start_month_id = $request->input('ch_founddate');
            $chapter->next_renewal_year = $request->input('ch_renewyear');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;

            $chapter->save();

            $payments->rereg_date = $request->input('ch_duespaid');
            $payments->rereg_members = $request->input('ch_members');

            $payments->save();

            DB::commit();

            return redirect()->to('/adminreports/reregdate')->with('error', 'Failed to update Re-Reg Date.');
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/adminreports/reregdate')->with('success', 'Re-Reg Date updated successfully.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }
}
