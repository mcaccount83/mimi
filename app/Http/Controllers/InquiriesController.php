<?php

namespace App\Http\Controllers;

use App\Enums\ChapterCheckbox;
use App\Enums\OperatingStatusEnum;
use App\Models\Chapters;
use App\Models\InquiryApplication;
use App\Models\Region;
use App\Models\RegionInquiry;
use App\Services\PositionConditionsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class InquiriesController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseChapterController;

    protected PositionConditionsService $positionConditionsService;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, PositionConditionsService $positionConditionsService)
    {
        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
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
     * View the Inquiry Applicaitons
     */
    public function showInquiryApplication(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        // Check if checkbox is checked
        $checkBox5Status = $request->has(\App\Enums\ChapterCheckbox::INTERNATIONAL);
        $checkBox7Status = $request->has(\App\Enums\ChapterCheckbox::INQUIRIES);
        $checkBox8Status = $request->has(\App\Enums\ChapterCheckbox::INTERNATIONALINQUIRIES);

        // Use the appropriate query based on checkbox status
        if ($checkBox5Status) {
            $inquiryList = InquiryApplication::with('state', 'region', 'conference', 'country')
                ->orderByDesc('id')
                ->get();
        } elseif ($checkBox7Status) {
            $inquiryList = InquiryApplication::with('state', 'region', 'conference', 'country')
                ->where('conference_id', $confId)
                ->where(function($query) {
                    $query->where('response', '!=', 1)
                        ->orWhereNull('response');
                })
                ->orderByDesc('id')
                ->get();
        } elseif ($checkBox8Status) {
            $inquiryList = InquiryApplication::with('state', 'region', 'conference', 'country')
                ->where(function($query) {
                    $query->where('response', '!=', 1)
                        ->orWhereNull('response');
                })
                ->orderByDesc('id')
                ->get();
        } else {
            $inquiryList = InquiryApplication::with('state', 'region', 'conference', 'country')
                ->where('conference_id', $confId)
                ->orderByDesc('id')
                ->get();
        }

        $data = [
            'inquiryList' => $inquiryList, 'checkBox5Status' => $checkBox5Status,
            'checkBox7Status' => $checkBox7Status, 'checkBox8Status' => $checkBox8Status,
        ];

        return view('inquiries.inquiryapplication')->with($data);
    }

    /**
     *Edit Chapter EIN Notes
     */
    public function editInquiryApplication(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $userName = $user['userName'];
        $userPosition = $user['cdPosition'];
        $userConfName = $user['confName'];
        $userConfDesc = $user['confDesc'];

        $inqDetails = InquiryApplication::with('chapter', 'state', 'region', 'conference', 'country')->find($id);
        $chapterId = $inqDetails->chapter_id;
        $stateId = $inqDetails->state_id;
        $regioniId = $inqDetails->region_id;
        $inqConfId = $inqDetails->conference_id;
        $chapterName = $inqDetails->chapter?->name;

        $inqCoord = RegionInquiry::with('region')->find($regioniId);
        $inqCoordName = $inqCoord->inquiries_name;

        $chDetails = Chapters::find($chapterId);

        $stateChapters = Chapters::
            where('active_status', '1')
            ->where('state_id', $stateId)
            ->get();

        $data = ['id' => $id,  'chapterId' => $chapterId, 'inqDetails' => $inqDetails, 'stateChapters' => $stateChapters, 'chapterName' => $chapterName,
            'inqCoordName' => $inqCoordName, 'chDetails' => $chDetails, 'confId' => $confId, 'inqConfId' => $inqConfId,
            'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
        ];

        return view('inquiries.editinquiryapplication')->with($data);
    }

    /**
     *Update Chapter EIN Notes
     */
    public function updateInquiryApplication(Request $request, $id): RedirectResponse
    {
        $inquiry = InquiryApplication::find($id);

        DB::beginTransaction();
        try {
            $inquiry->available = $request->has('available') ? 1 : 0;
            $inquiry->chapter_id = $request->input('chapter');
            $inquiry->save();

            DB::commit();

            return to_route('inquiries.editinquiryapplication', ['id' => $id])->with('success', 'Inquiry Information has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return to_route('inquiries.editinquiryapplication', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            DB::disconnect();
        }
    }

     public function updateInquiryResponse(Request $request, $id): RedirectResponse
    {
        $inquiry = InquiryApplication::find($id);

        DB::beginTransaction();
        try {
            $inquiry->response = 1;
            $inquiry->save();

            DB::commit();

            return to_route('inquiries.editinquiryapplication', ['id' => $id])->with('success', 'Inquiry Response has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return to_route('inquiries.editinquiryapplication', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            DB::disconnect();
        }
    }

    public function clearInquiryResponse(Request $request, $id): RedirectResponse
    {
        $inquiry = InquiryApplication::find($id);

        DB::beginTransaction();
        try {
            $inquiry->response = 0;
            $inquiry->save();

            DB::commit();

            return to_route('inquiries.editinquiryapplication', ['id' => $id])->with('success', 'Inquiry Response has been cleared');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return to_route('inquiries.editinquiryapplication', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            DB::disconnect();
        }
    }

    public function inquiriesNotify(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $confId = $user['confId'];

        $checkBox5Status = $request->has(\App\Enums\ChapterCheckbox::INTERNATIONAL);

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
        if (!$checkBox5Status) {
            $query->where('region.conference_id', $confId);
        }

        $regList = $query
            ->orderBy('conference.short_name')
            ->orderBy('region.long_name')
            ->select('region.*')
            ->get();

        $data = ['regList' => $regList, 'checkBox5Status' => $checkBox5Status];

        return view('inquiries.inquiriesnotify')->with($data);
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
}
