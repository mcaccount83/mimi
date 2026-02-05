<?php

namespace App\Http\Controllers;

use App\Enums\ChapterCheckbox;
use App\Models\Chapters;
use App\Models\Conference;
use App\Models\GrantRequest;
use App\Models\PaymentLog;
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
    public function paymentList(Request $request): View
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
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $data = ['chapterList' => $chapterList, 'checkBox5Status' => $checkBox5Status];

        return view('adminreports.reregdate')->with($data);
    }

    public function editReRegDate(Request $request, $id): View
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

        return view('adminreports.editreregdate')->with($data);
    }

    public function updateReRegDate(Request $request, $id): RedirectResponse
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

    public function inquiriesNotify(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $checkBox5Status = $request->has(\App\Enums\ChapterCheckbox::INTERNATIONAL);

         // Use the appropriate query based on checkbox status
        if ($checkBox5Status) {
            $regList = Region::with([
                'inquiries',
                'conference',
                'states' => function ($query) {
                    $query->orderBy('state_short_name');
                }
            ])
            ->join('conference', 'region.conference_id', '=', 'conference.id')
            ->orderBy('conference.short_name')
            ->orderBy('region.long_name')
            ->select('region.*')
            ->get();

        } else {
            $regList = Region::with([
                'inquiries',
                'conference',
                'states' => function ($query) {
                    $query->orderBy('state_short_name');
                }
            ])
            ->join('conference', 'region.conference_id', '=', 'conference.id')
            ->where('conference_id', $confId)
            ->orderBy('conference.short_name')
            ->orderBy('region.long_name')
            ->select('region.*')
            ->get();
        }

        $data = ['regList' => $regList, 'checkBox5Status' => $checkBox5Status];

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

     public function conferenceList(Request $request): View
    {
        $confList = Conference::with([
                'regions' => function ($query) {
                    $query->orderBy('long_name');
                },
                'states' => function ($query) {
                    $query->orderBy('state_short_name');
                }
            ])
            ->orderBy('short_name')
            ->get();

        $data = ['confList' => $confList];

        return view('adminreports.conferencelist')->with($data);
    }

    public function regionList(Request $request): View
    {
        $regList = Region::with([
                'conference',
                'states' => function ($query) {
                    $query->orderBy('state_short_name');
                }
            ])
            ->join('conference', 'region.conference_id', '=', 'conference.id')
            ->orderBy('conference.short_name')
            ->orderBy('region.long_name')
            ->select('region.*')
            ->get();

        // Get all conferences for dropdown
        $conferenceList = Conference::orderBy('short_name')->get();

        $data = [
            'regList' => $regList,
            'conferenceList' => $conferenceList
        ];

        return view('adminreports.regionlist')->with($data);
    }

    public function updateRegion(Request $request, $id)
    {
        try {
            $region = Region::findOrFail($id);
            $region->conference_id = $request->conference_id;
            $region->save();

            $conference = Conference::find($request->conference_id);

            return response()->json([
                'success' => true,
                'message' => 'Region conference updated successfully!',
                'conference_name' => $conference->short_name
            ]);
        } catch (\Exception $e) {
                Log::error('Region conference update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating region conference. Please try again.'
            ], 500);
        }
    }

    public function stateList(Request $request): View
    {
        $stateList = State::with('conference', 'region')
            ->orderBy('state_short_name')
            ->get();

        // Get all conferences and regions for dropdowns
        $conferenceList = Conference::orderBy('short_name')->get();
        $regionList = Region::orderBy('long_name')->get();

        $data = [
            'stateList' => $stateList,
            'conferenceList' => $conferenceList,
            'regionList' => $regionList
        ];

        return view('adminreports.statelist')->with($data);
    }

    public function updateState(Request $request, $id)
    {
        try {
            $state = State::findOrFail($id);

            // Verify that the region belongs to the selected conference
            $region = Region::where('id', $request->region_id)
                ->where('conference_id', $request->conference_id)
                ->first();

            if (!$region) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected region does not belong to the selected conference.'
                ], 400);
            }

            $state->conference_id = $request->conference_id;
            $state->region_id = $request->region_id;
            $state->save();

            return response()->json([
                'success' => true,
                'message' => 'State assignment updated successfully!',
                'conference_name' => $region->conference->short_name,
                'region_name' => $region->long_name
            ]);
        } catch (\Exception $e) {
                Log::error('State assignment update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()  // Return actual error for debugging
            ], 500);
        }
    }

    public function viewGrantList(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $checkBox5Status = $request->has(\App\Enums\ChapterCheckbox::INTERNATIONAL);

         // Use the appropriate query based on checkbox status
        if ($checkBox5Status) {
            $grantList = GrantRequest::with('chapters')
                ->orderBy('submitted_at')
                ->get();

        } else {
            $grantList = GrantRequest::with('chapters')
                ->whereHas('chapters', function ($query) use ($confId) {
                    $query->where('conference_id', $confId);
                })
                ->orderBy('submitted_at')
                ->get();
            }

        $data = ['grantList' => $grantList, 'checkBox5Status' => $checkBox5Status];

        return view('adminreports.grantlist')->with($data);
    }

    public function editGrantDetails(Request $request, $grantId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $grantDetails = GrantRequest::with('chapters', 'state', 'country')
            ->find($grantId);
        $chapterId = $grantDetails->chapter_id;

        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
        $chDetails = $baseQuery['chDetails'];
        $chConfId = $baseQuery['chConfId'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];

        $grList = $this->userController->loadGrantReviewerList($chConfId) ?? null;

        $data = ['id' => $grantId, 'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription, 'confId' => $confId, 'chConfId' => $chConfId, 'grantDetails' => $grantDetails,
            'grList' => $grList
        ];

        return view('adminreports.editgrantdetails')->with($data);
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
            $grantRequest->review_notes = $input['review_notes'] ?? null;
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
