<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $corDetails = User::find(auth()->user()->id)->CoordinatorDetails;
                $corId = $corDetails['coordinator_id'];
                $positionid = $corDetails['position_id'];
                $secpositionid = $corDetails['sec_position_id'];
                $loggedIn = $corDetails['first_name'].' '.$corDetails['last_name'];

                $view->with(compact('corId', 'positionid', 'secpositionid', 'loggedIn'));
            } else {
                // Handle the case where the user is not logged in
                $view->with([
                    'corId' => null,
                    'positionid' => null,
                    'secpositionid' => null,
                    'loggedIn' => null,
                ]);
            }
        });
    }

    public function register(): void
    {
        //
    }
}
