<?php

namespace App\Http\Controllers;

use App\Enums\ChapterCheckbox;
use App\Enums\OperatingStatusEnum;
use App\Models\Chapters;
use App\Models\InquiryApplication;
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
        $positionId = $user['cdPositionId'];
        $userName = $user['userName'];
        $userPosition = $user['cdPosition'];
        $userConfName = $user['confName'];
        $userConfDesc = $user['confDesc'];

        $inqDetails = InquiryApplication::with('chapter', 'state', 'region', 'conference', 'country')->find($id);
        $chapterId = $inqDetails->chapter_id;
        $stateId = $inqDetails->state_id;
        $regioniId = $inqDetails->region_id;
        $inqConfId = $inqDetails->conference_id;
        $stateShortName = $inqDetails->state->state_short_name;
        $stateLongtName = $inqDetails->state->state_long_name;
        $regionLongName = $inqDetails->region->long_name;
        $conferenceDescription = $inqDetails->conference->conference_description;
        $inquiryStateShortName = $inqDetails->state->state_short_name;
        $inquiryCountryShortName = $inqDetails->country->short_name;
        $chapterName = $inqDetails->chapter?->name;

        $inqCoord = RegionInquiry::with('region')->find($regioniId);
        $inqCoordName = $inqCoord->inquiries_name;

        $chDetails = Chapters::find($chapterId);

        $stateChapters = Chapters::
            where('active_status', '1')
            ->where('state_id', $stateId)
            ->get();

        $data = ['id' => $id, 'conferenceDescription' => $conferenceDescription, 'stateShortName' => $stateShortName, 'chapterId' => $chapterId,
            'inqDetails' => $inqDetails, 'stateLongtName' => $stateLongtName, 'regionLongName' => $regionLongName, 'stateChapters' => $stateChapters,
            'inquiryStateShortName' => $inquiryStateShortName, 'inquiryCountryShortName' => $inquiryCountryShortName, 'chapterName' => $chapterName,
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
}
