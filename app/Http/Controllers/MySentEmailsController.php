<?php
// app/Http/Controllers/MySentEmailsController.php

namespace App\Http\Controllers;

use App\Enums\AdminStatusEnum;
use App\Models\Region;
use Dcblogdev\LaravelSentEmails\Controllers\SentEmailsController;
use Dcblogdev\LaravelSentEmails\Models\SentEmail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Services\PositionConditionsService;

class MySentEmailsController extends SentEmailsController implements HasMiddleware
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

    // public function index(Request $request): View
    // {
    //     // $user = $request->user();
    //     $user = $this->userController->loadUserInformation($request);
    //     $confId = $user['confId'];
    //     $userAdmin= $user['userAdmin'];
    //     $confId = $user['confId'];
    //     $regId = $user['regId'] ?? null;
    //     $userEmail = $user['userEmail'];

    //     // Get position conditions
    //     $positionId = $userInfo['cdPositionId'] ?? 0;
    //     $secPositionId = $userInfo['cdSecPositionId'] ?? [];
    //     $coorId = $userInfo['cdId'] ?? null;

    //     $checkBox81Status = $request->has(\App\Enums\CheckboxFilterEnum::ADMIN);

    //     // Add view filter based on checkboxes
    //     if ($checkBox81Status) {
    //         $emails = SentEmail::with('attachments')->orderby('id', 'desc')
    //             ->applyFilters($request)
    //             ->paginate(config('sentemails.perPage'));

    //         return view('sentemails::index', compact('emails', 'checkBox81Status'));

    //     } else {

    //     // If user is admin, show all emails (existing behavior)
    //     // if ($user->is_admin == AdminStatusEnum::ADMIN) {
    //     //     $emails = SentEmail::with('attachments')->orderby('id', 'desc')
    //     //         ->applyFilters($request)
    //     //         ->paginate(config('sentemails.perPage'));

    //     //     return view('sentemails::index', compact('emails'));
    //     // }

    //     // Get emails for position conditions
    //     $conditions = $this->positionConditionsService->getConditionsForUser($positionId, $secPositionId, $coorId);
    //     $coordinatorCondition = $conditions['coordinatorCondition'];
    //     $conferenceCoordinatorCondition = $conditions['conferenceCoordinatorCondition'];
    //     $inquiriesCondition = $conditions['inquiriesCondition'];
    //     $inquiriesInternationalCondition = $conditions['inquiriesInternationalCondition'];

    //     $founderCondition = $conditions['founderCondition'];
    //     $einCondition = $conditions['einCondition'];
    //     $m2mCondition = $conditions['m2mCondition'];
    //     $listAdminCondition = $conditions['listAdminCondition'];
    //     $ITCondition = $conditions['ITCondition'];

    //     $adminEmail = $this->positionConditionsService->getAdminEmail();
    //     $listAdmin = $adminEmail['list_admin'];
    //     $paymentsAdmin = $adminEmail['payments_admin'];
    //     $einAdmin = $adminEmail['ein_admin'];
    //     $gsuiteAdmin = $adminEmail['gsuite_admin'];
    //     $mimiAdmin = $adminEmail['mimi_admin'];
    //     $grantAdmin = $adminEmail['grant_admin'];

    //     // Get admin emails ONLY if conditions are met
    //     $adminEmails = [];

    //     if ($listAdminCondition) {
    //         $adminEmails[] = $listAdmin;
    //     }

    //     if ($einCondition) {
    //         $adminEmails[] = $einAdmin;
    //     }

    //     if ($m2mCondition) {
    //         $adminEmails[] = $grantAdmin;
    //     }

    //     if ($founderCondition) {
    //         $adminEmails[] = $paymentsAdmin;
    //     }

    //     if ($ITCondition) {
    //         $adminEmails[] = $gsuiteAdmin;
    //         $adminEmails[] = $mimiAdmin;
    //     }

    //     // Remove duplicates and empty values
    //     $adminEmails = array_filter(array_unique($adminEmails));

    //     // Get inquiries emails ONLY if conditions are met
    //     $inquiriesEmails = [];
    //         // International Inquiries Coordinator - get ALL inquiries emails
    //         if ($inquiriesInternationalCondition){
    //             $regions = Region::with('inquiries')->get();

    //             foreach ($regions as $region) {
    //                 if ($region->inquiries && $region->inquiries->inquiries_email) {
    //                     $inquiriesEmails[] = $region->inquiries->inquiries_email;
    //                 }
    //             }

    //             // Remove duplicates
    //             $inquiriesEmails = array_unique($inquiriesEmails);

    //     } elseif (($coordinatorCondition && $conferenceCoordinatorCondition) || $inquiriesCondition ) {
    //             if ($regId && $regId > 0) {
    //                 // User has a specific region - get that region's inquiries email
    //                 $region = Region::with('inquiries')->find($regId);
    //                 if ($region && $region->inquiries && $region->inquiries->inquiries_email) {
    //                     $inquiriesEmails[] = $region->inquiries->inquiries_email;
    //                 }
    //             } else {
    //                 // Conference coordinator - get all inquiries emails for their conference
    //                 $regions = Region::with('inquiries')
    //                     ->where('conference_id', $confId)
    //                     ->get();

    //                 foreach ($regions as $region) {
    //                     if ($region->inquiries && $region->inquiries->inquiries_email) {
    //                         $inquiriesEmails[] = $region->inquiries->inquiries_email;
    //                     }
    //                 }

    //                 // Remove duplicates
    //                 $inquiriesEmails = array_unique($inquiriesEmails);
    //             }
    //         }

    //     // For regular users, filter to their emails and their region/conference inquiries emails
    //     $emails = SentEmail::with('attachments')
    //         ->where(function($query) use ($userEmail, $inquiriesEmails, $adminEmails) {
    //             $query->where('from', 'LIKE', '%' . $userEmail . '%')
    //                 ->orWhere('to', 'LIKE', '%' . $userEmail . '%')
    //                 ->orWhere('cc', 'LIKE', '%' . $userEmail . '%')
    //                 ->orWhere('bcc', 'LIKE', '%' . $userEmail . '%');

    //             // Add each inquiries email
    //             foreach ($inquiriesEmails as $inquiriesEmail) {
    //                 $query->orWhere('from', 'LIKE', '%' . $inquiriesEmail . '%')
    //                     ->orWhere('to', 'LIKE', '%' . $inquiriesEmail . '%')
    //                     ->orWhere('cc', 'LIKE', '%' . $inquiriesEmail . '%')
    //                     ->orWhere('bcc', 'LIKE', '%' . $inquiriesEmail . '%');
    //             }

    //             // Add each admin email
    //             foreach ($adminEmails as $email) {  // Changed from $adminEmail to $email
    //                 $query->orWhere('from', 'LIKE', '%' . $email . '%')
    //                     ->orWhere('to', 'LIKE', '%' . $email . '%')
    //                     ->orWhere('cc', 'LIKE', '%' . $email . '%')
    //                     ->orWhere('bcc', 'LIKE', '%' . $email . '%');
    //             }
    //         })
    //         ->orderBy('id', 'desc')
    //         ->applyFilters($request)
    //         ->paginate(config('sentemails.perPage'));
    //             return view('sentemails::index', compact('emails', 'checkBox81Status'));
    //         }
    // }

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
}
