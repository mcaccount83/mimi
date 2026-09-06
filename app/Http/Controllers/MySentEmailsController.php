<?php

namespace App\Http\Controllers;

use App\Enums\CheckboxFilterEnum;
use App\Models\Region;
// use Dcblogdev\LaravelSentEmails\Controllers\SentEmailsController;
use App\Services\PositionConditionsService;
use Dcblogdev\LaravelSentEmails\Models\SentEmail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

// class MySentEmailsController extends SentEmailsController implements HasMiddleware
class MySentEmailsController extends Controller implements HasMiddleware
{
    public function __construct(
        protected UserController $userController,
        protected PositionConditionsService $positionConditionsService,
        protected BaseConditionsController $baseConditionsController,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    public function index(Request $request): View
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

        $checkStatuses = $this->baseConditionsController->getCheckboxStatuses();

        $filterEmails = [];
        $regions = [];

        if ($checkStatuses[CheckboxFilterEnum::ADMIN] === 'checked') {
            $filterEmails = null;
            $regions = Region::with('inquiries')->get();

        } elseif ($checkStatuses[CheckboxFilterEnum::LIST] === 'checked') {
            $filterEmails = [$listAdmin];

        } elseif ($checkStatuses[CheckboxFilterEnum::INQUIRIES] === 'checked' || $checkStatuses[CheckboxFilterEnum::INTERNATIONALINQUIRIES] === 'checked') {
            if ($checkStatuses[CheckboxFilterEnum::INTERNATIONALINQUIRIES] === 'checked' || $inquiriesInternationalCondition) {
                $regions = Region::with('inquiries')->get();
            } elseif (($coordinatorCondition && $conferenceCoordinatorCondition) || $inquiriesCondition) {
                if ($regId && $regId > 0) {
                    $regions = Region::with('inquiries')->where('id', $regId)->get();
                } else {
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

        // Replace everything from "Build queries based on filter" down to the return
        if ($filterEmails === null) {
            $emails = SentEmail::with('attachments')
                ->orderByDesc('id')
                ->applyFilters($request)
                ->paginate(config('sentemails.perPage'))
                ->appends($request->only(['check5', 'check7', 'check57', 'check81']));
        } else {
            $emails = SentEmail::with('attachments')
                ->where(function ($query) use ($filterEmails) {
                    foreach ($filterEmails as $email) {
                        $query->orWhere('from', 'LIKE', '%'.$email.'%')
                            ->orWhere('to', 'LIKE', '%'.$email.'%')
                            ->orWhere('cc', 'LIKE', '%'.$email.'%')
                            ->orWhere('bcc', 'LIKE', '%'.$email.'%');
                    }
                })
                ->orderByDesc('id')
                ->applyFilters($request)
                ->paginate(config('sentemails.perPage'))
                ->appends($request->only(['check5', 'check7', 'check57', 'check81']));
        }

        return view('sentemails::index', compact('emails', 'checkStatuses'));
    }

    public function downloadAttachment(string $id)
    {
        $attachment = \Dcblogdev\LaravelSentEmails\Models\SentEmailAttachment::findOrFail($id);

        $privatePath = storage_path('app/private/'.$attachment->path);
        $legacyPath = storage_path('app/'.$attachment->path);

        if (file_exists($privatePath)) {
            return response()->file($privatePath, ['Content-Type' => 'application/pdf']);
        }

        if (file_exists($legacyPath)) {
            return response()->file($legacyPath, ['Content-Type' => 'application/pdf']);
        }

        return redirect()->route('sentemails')
            ->with('error', 'Attachment file not found.');
    }
}
