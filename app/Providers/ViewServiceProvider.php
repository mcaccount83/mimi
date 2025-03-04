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
            $secpositionid = null;
            $loggedIn = null;

            if (Auth::check()) {
                $user = Auth::user();

                if ($user->user_type === 'coordinator' && $user->coordinator) {
                    $corDetails = $user->coordinator;
                    $corId = $corDetails['coordinator_id'];
                    $positionid = $corDetails['position_id'];
                    $secpositionid = $corDetails['sec_position_id'];
                    $loggedIn = $corDetails['first_name'].' '.$corDetails['last_name'];
                }

                // Additional conditions for other user types can be handled here
            }

            // Define conditions
            $ITCondition = ($positionid == 13 || $secpositionid == 13); // IT Coordinator
            $coordinatorCondition = ($positionid >= 1 && $positionid <= 8); // BS-Founder
            $founderCondition = $positionid == 8; // Founder
            $conferenceCoordinatorCondition = ($positionid >= 7 && $positionid <= 8); // CC-Founder
            $assistConferenceCoordinatorCondition = ($positionid >= 6 && $positionid <= 8); // ACC-Founder
            $regionalCoordinatorCondition = ($positionid >= 5 && $positionid <= 8); // RC-Founder
            $assistRegionalCoordinatorCondition = ($positionid >= 4 && $positionid <= 8); // ARC-Founder
            $supervisingCoordinatorCondition = ($positionid >= 3 && $positionid <= 8); // SC-Founder
            $areaCoordinatorCondition = ($positionid >= 2 && $positionid <= 8); // AC-Founder
            $bigSisterCondition = ($positionid >= 1 && $positionid <= 8); // BS-Founder

            $eoyTestCondition = ($positionid >= 6 && $positionid <= 8) || ($positionid == 29 || $secpositionid == 29);  //ACC-Founder, AR Tester
            $eoyReportCondition = ($positionid >= 1 && $positionid <= 8) || ($positionid == 19 || $secpositionid == 19) || ($positionid == 29 || $secpositionid == 29);  //*BS-Founder, AR Reviewer, AR Tester
            $eoyReportConditionDISABLED = ($positionid == 13 || $secpositionid == 13);  //*IT Coordinator
            $inquiriesCondition = ($positionid == 15 || $secpositionid == 15 || $positionid == 18 || $secpositionid == 18);  //*Inquiries Coordinator
            $inquiriesInternationalCondition = ($positionid == 18 || $secpositionid == 18);  //*International Inquiries Coordinator
            $inquiriesConferneceCondition = ($positionid == 15 || $secpositionid == 15);  //*Conference Inquiries Coordinator
            $webReviewCondition = ($positionid == 9 || $secpositionid == 9);  //*Website Reviewer
            $einCondition = ($positionid == 12 || $secpositionid == 12);  //*EIN Coordinator
            $adminReportCondition = ($positionid == 13 || $secpositionid == 13);  //*IT Coordinator
            $m2mCondition = ($positionid == 21 || $secpositionid == 21 || $positionid == 20 || $secpositionid == 20);  //*M2M Committee
            $listAdminCondition = ($positionid == 23 || $secpositionid == 23);  //*ListAdmin

            // Fetch the 'admin' record
            $admin = Admin::orderBy('id', 'desc')
                ->limit(1)
                ->first();
            $display_testing = ($admin->display_testing == 1);
            $display_live = ($admin->display_live == 1);
            $displayTESTING = ($display_testing == true && $display_live != true);
            $displayLIVE = ($display_live == true);

            // $display_testing = $admin->display_testing ?? 0; // Handle null cases
            // $display_live = $admin->display_live ?? 0; // Handle null cases
            // $eoy_coordinators = $admin->eoy_coordinators ?? 0; // Handle null cases
            // Define testers and coordinators conditions
            // $displayTESTING = ($display_testing == 1);
            // $displayLIVE = ($display_live == 1);
            // $coordinators_yes = ($eoy_coordinators == 1);

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
                'adminReportCondition',
                'm2mCondition',
                'listAdminCondition',
                'displayTESTING',
                'displayLIVE'
            ));
        });
    }

    public function register(): void
    {
        //
    }
}
