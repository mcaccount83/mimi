<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\User;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            $corDetails = User::find(auth()->user()->id)->CoordinatorDetails;
            $corId = $corDetails['coordinator_id'];
            $positionid = $corDetails['position_id'];
            $secpositionid = $corDetails['sec_position_id'];
            $loggedIn = $corDetails['first_name'].' '.$corDetails['last_name'];

            $view->with(compact('corId', 'positionid', 'secpositionid', 'loggedIn'));
        });
    }

    public function register()
    {
        //
    }
}
