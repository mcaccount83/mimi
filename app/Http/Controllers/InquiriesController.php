<?php

namespace App\Http\Controllers;

use App\Enums\CheckboxFilterEnum;
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
        $checkBox51Status = $request->has(\App\Enums\CheckboxFilterEnum::INTERNATIONAL);
        $checkBox7Status = $request->has(\App\Enums\CheckboxFilterEnum::INQUIRIES);
        $checkBox57Status = $request->has(\App\Enums\CheckboxFilterEnum::INTERNATIONALINQUIRIES);

        // Use the appropriate query based on checkbox status
        if ($checkBox51Status) {
            $inquiryList = InquiryApplication::with('state', 'country')
                ->orderByDesc('id')
                ->get();
        } elseif ($checkBox7Status) {
            $inquiryList = InquiryApplication::with('state', 'country')
                ->select('inquiry_application.*')  // Add this to be explicit about which columns
                ->join('state', 'inquiry_application.state_id', '=', 'state.id')  // Also be explicit in join
                ->where('state.conference_id', $confId)
                ->where(function($query) {
                    $query->where('response', '!=', 1)
                        ->orWhereNull('response');
                })
                ->orderByDesc('id')
                ->get();
        } elseif ($checkBox57Status) {
            $inquiryList = InquiryApplication::with('state', 'country')
                ->where(function($query) {
                    $query->where('response', '!=', 1)
                        ->orWhereNull('response');
                })
                ->orderByDesc('id')
                ->get();
        } else {
            $inquiryList = InquiryApplication::with('state', 'country')
                ->select('inquiry_application.*')  // Add this to be explicit about which columns
                ->join('state', 'inquiry_application.state_id', '=', 'state.id')  // Also be explicit in join
                ->where('state.conference_id', $confId)
                ->orderByDesc('inquiry_application.id')  // Specify which id
                ->get();
        }

        $data = [
            'inquiryList' => $inquiryList, 'checkBox51Status' => $checkBox51Status,
            'checkBox7Status' => $checkBox7Status, 'checkBox57Status' => $checkBox57Status,
        ];

        return view('coordinators.inquiries.inquiryapplication')->with($data);
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

        $inqDetails = InquiryApplication::with('chapter', 'state', 'country', 'regioninquiry')->find($id);
        $chapterId = $inqDetails->chapter_id;
        $stateId = $inqDetails->state_id;
        $regioniId = $inqDetails->state->region_id;
        $inqConfId = $inqDetails->state->conference_id;
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

        return view('coordinators.inquiries.editinquiryapplication')->with($data);
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


}
