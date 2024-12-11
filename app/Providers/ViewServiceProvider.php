<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
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

            if (auth()->check()) {
                $user = auth()->user();

                if ($user->user_type === 'coordinator' && $user->Coordinators) {
                    $corDetails = $user->Coordinators;
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
            $admin = DB::table('admin')
                ->select('admin.*', DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'))
                ->leftJoin('coordinators as cd', 'admin.updated_id', '=', 'cd.id')
                ->orderByDesc('admin.id')
                ->first();

            $eoy_testers = $admin->eoy_testers ?? 0; // Handle null cases
            $eoy_coordinators = $admin->eoy_coordinators ?? 0; // Handle null cases
            // Define testers and coordinators conditions
            $testers_yes = ($eoy_testers == 1);
            $coordinators_yes = ($eoy_coordinators == 1);

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
                'testers_yes',
                'coordinators_yes'
            ));
        });
    }

    public function register(): void
    {
        //
    }
}
