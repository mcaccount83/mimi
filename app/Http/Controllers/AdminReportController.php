<?php

namespace App\Http\Controllers;

use App\Enums\CoordinatorPosition;
use App\Models\Admin;
use App\Models\AdminEmail;
use App\Models\Boards;
use App\Models\BoardsDisbanded;
use App\Models\BoardsIncoming;
use App\Models\BoardsOutgoing;
use App\Models\BoardsPending;
use App\Models\Chapters;
use App\Models\Conference;
use App\Models\CoordinatorApplication;
use App\Models\CoordinatorRecognition;
use App\Models\Coordinators;
use App\Models\CoordinatorTree;
use App\Models\Documents;
use App\Models\FinancialReport;
use App\Models\ForumCategorySubscription;
use App\Models\GoogleDrive;
use App\Models\PaymentLog;
use App\Models\Payments;
use App\Models\ProbationSubmission;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
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
    public function intPaymentList(Request $request): View
    {
        $query = PaymentLog::with('board');

        // Add filters if needed
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // $paymentLogs = $query->orderBy('created_at', 'desc')->paginate(100);
        $paymentLogs = $query->orderByDesc('created_at')->paginate(100);

        return view('adminreports.intpaymentlist', compact('paymentLogs'));
    }

    /**
     * View Payment Log List
     */
    public function paymentList(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $confId = $user['user_confId'];

        $query = PaymentLog::with('board');

        // Always filter by the user's conference ID
        $query = PaymentLog::with('board')->where('conf', $confId);

        // Add additional filters if needed
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // $paymentLogs = $query->orderBy('created_at', 'desc')->paginate(100);
        $paymentLogs = $query->orderByDesc('created_at')->paginate(100);

        return view('adminreports.paymentlist', compact('paymentLogs'));
    }

    /**
     * View Payment Log Transaction Details
     */
    public function paymentDetails($id): View
    {
        $log = PaymentLog::findOrFail($id);

        return view('adminreports.paymentdetails', compact('log'));
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

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();

        $data = ['chapterList' => $chapterList];

        return view('adminreports.reregdate')->with($data);
    }

    /**
     * View List of International ReReg Payments if Dates Need to be Udpated
     */
    public function showIntReRegDate(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);
        $chapterList = $baseQuery['query']->get();

        $data = ['chapterList' => $chapterList];

        return view('adminreports.intreregdate')->with($data);
    }

    public function editReRegDate(Request $request, $id): View
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $chPayments = $baseQuery['chPayments'];
        $allMonths = $baseQuery['allMonths'];

        $data = ['id' => $id, 'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'chPayments' => $chPayments, 'allMonths' => $allMonths];

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
