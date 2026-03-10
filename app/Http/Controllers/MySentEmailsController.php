<?php

namespace App\Http\Controllers;

use App\Enums\AdminStatusEnum;
use App\Models\Region;
// use Dcblogdev\LaravelSentEmails\Controllers\SentEmailsController;
use Dcblogdev\LaravelSentEmails\Models\SentEmail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Services\PositionConditionsService;

// class MySentEmailsController extends SentEmailsController implements HasMiddleware
class MySentEmailsController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $positionConditionsService;

    public function __construct(UserController $userController, PositionConditionsService $positionConditionsService)
    {
        $this->userController = $userController;
        $this->positionConditionsService = $positionConditionsService;
    }

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

        // Replace everything from "Build queries based on filter" down to the return
    if ($filterEmails === null) {
        $emails = SentEmail::with('attachments')
            ->orderBy('id', 'desc')
            ->applyFilters($request)
            ->paginate(config('sentemails.perPage'));
    } else {
        $emails = SentEmail::with('attachments')
            ->where(function ($query) use ($filterEmails) {
                foreach ($filterEmails as $email) {
                    $query->orWhere('from', 'LIKE', '%' . $email . '%')
                        ->orWhere('to',   'LIKE', '%' . $email . '%')
                        ->orWhere('cc',   'LIKE', '%' . $email . '%')
                        ->orWhere('bcc',  'LIKE', '%' . $email . '%');
                }
            })
            ->orderBy('id', 'desc')
            ->applyFilters($request)
            ->paginate(config('sentemails.perPage'));
    }

    return view('sentemails::index', compact(
        'emails',
        'checkBox81Status', 'checkBox5Status', 'checkBox7Status', 'checkBox57Status',
        ));
    }

public function downloadAttachment(string $id)
{
    $attachment = \Dcblogdev\LaravelSentEmails\Models\SentEmailAttachment::findOrFail($id);

    $privatePath = storage_path('app/private/' . $attachment->path);
    $legacyPath  = storage_path('app/' . $attachment->path);

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
