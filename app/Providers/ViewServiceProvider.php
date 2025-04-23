<?php

namespace App\Providers;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\PositionConditionsService;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $corId = null;
            $positionid = null;
            $secpositionid = []; // Changed to an array
            $loggedIn = null;
            $userAdmin = false;
            $userModerator = false;

            if (Auth::check()) {
                $user = Auth::user();

                if ($user->user_type === 'coordinator' && $user->coordinator) {
                    $corDetails = $user->coordinator;
                    $corId = $corDetails['coordinator_id'];
                    $positionid = $corDetails['position_id'];
                    $secpositionid = $corDetails->secondaryPosition->pluck('id')->toArray(); // Get all secondary position IDs
                    $loggedIn = $corDetails['first_name'].' '.$corDetails['last_name'];
                }

                $userAdmin = $user->is_admin == '1';
                $userModerator = $user->is_admin == '2';
                // Additional conditions for other user types can be handled here
            }

            // Use the service to get conditions
            $conditionsService = app(PositionConditionsService::class);
            $conditions = $conditionsService->getConditionsForUser($positionid, $secpositionid);

            // Fetch the 'admin' record
            $admin = Admin::orderByDesc('id')
                ->limit(1)
                ->first();
            $display_testing = ($admin->display_testing == 1);
            $display_live = ($admin->display_live == 1);
            $displayTESTING = ($display_testing == true && $display_live != true);
            $displayLIVE = ($display_live == true);

            // Merge all variables
            $viewVariables = array_merge([
                'corId' => $corId,
                'positionid' => $positionid,
                'secpositionid' => $secpositionid,
                'loggedIn' => $loggedIn,
                'userAdmin' => $userAdmin,
                'userModerator' => $userModerator,
                'displayTESTING' => $displayTESTING,
                'displayLIVE' => $displayLIVE,
            ], $conditions);

            // Pass all variables to views
            $view->with($viewVariables);
        });
    }

    public function register(): void
    {
        //
    }
}
