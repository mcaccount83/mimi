<?php

namespace App\Providers;

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
                    // ... (other variables)
                }

                // Additional conditions for other user types (e.g., board members)
                // Modify the conditions based on your application logic

                // Set other variables accordingly

            }

            $view->with(compact('corId', 'positionid', 'secpositionid', 'loggedIn'));
        });
    }

    public function register(): void
    {
        //
    }
}
