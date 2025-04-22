<?php

namespace App\Providers;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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

            // Define conditions using in_array() to check array values
            $ITCondition = ($positionid == 13 || in_array(13, $secpositionid));
            $coordinatorCondition = ($positionid >= 1 && $positionid <= 8);
            $founderCondition = $positionid == 8;
            $conferenceCoordinatorCondition = ($positionid >= 7 && $positionid <= 8);
            $assistConferenceCoordinatorCondition = ($positionid >= 6 && $positionid <= 8);
            $regionalCoordinatorCondition = ($positionid >= 5 && $positionid <= 8);
            $assistRegionalCoordinatorCondition = ($positionid >= 4 && $positionid <= 8);
            $supervisingCoordinatorCondition = ($positionid >= 3 && $positionid <= 8);
            $areaCoordinatorCondition = ($positionid >= 2 && $positionid <= 8);
            $bigSisterCondition = ($positionid >= 1 && $positionid <= 8);

            $eoyTestCondition = ($positionid >= 6 && $positionid <= 8) || ($positionid == 29 || in_array(29, $secpositionid));
            $eoyReportCondition = ($positionid >= 1 && $positionid <= 8) || ($positionid == 19 || in_array(19, $secpositionid)) || ($positionid == 29 || in_array(29, $secpositionid));
            $eoyReportConditionDISABLED = ($positionid == 13 || in_array(13, $secpositionid));
            $inquiriesCondition = ($positionid == 15 || in_array(15, $secpositionid) || $positionid == 18 || in_array(18, $secpositionid));
            $inquiriesInternationalCondition = ($positionid == 18 || in_array(18, $secpositionid));
            $inquiriesConferneceCondition = ($positionid == 15 || in_array(15, $secpositionid));
            $webReviewCondition = ($positionid == 9 || in_array(9, $secpositionid));
            $einCondition = ($positionid == 12 || in_array(12, $secpositionid));
            $m2mCondition = ($positionid == 21 || in_array(21, $secpositionid) || $positionid == 20 || in_array(20, $secpositionid));
            $listAdminCondition = ($positionid == 23 || in_array(23, $secpositionid));

            // Fetch the 'admin' record
            $admin = Admin::orderByDesc('id')
                ->limit(1)
                ->first();
            $display_testing = ($admin->display_testing == 1);
            $display_live = ($admin->display_live == 1);
            $displayTESTING = ($display_testing == true && $display_live != true);
            $displayLIVE = ($display_live == true);

            // Pass conditions and other variables to views
            $view->with(compact(
                'corId',
                'positionid',
                'secpositionid',
                'loggedIn',
                'ITCondition',
                'coordinatorCondition',
                'founderCondition',
                'conferenceCoordinatorCondition',
                'assistConferenceCoordinatorCondition',
                'regionalCoordinatorCondition',
                'assistRegionalCoordinatorCondition',
                'supervisingCoordinatorCondition',
                'areaCoordinatorCondition',
                'bigSisterCondition',
                'eoyTestCondition',
                'eoyReportCondition',
                'eoyReportConditionDISABLED',
                'inquiriesCondition',
                'webReviewCondition',
                'einCondition',
                'm2mCondition',
                'listAdminCondition',
                'displayTESTING',
                'displayLIVE',
                'userAdmin',
                'userModerator'
            ));
        });
    }

    public function register(): void
    {
        //
    }
}
