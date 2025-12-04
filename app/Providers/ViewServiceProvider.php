<?php

namespace App\Providers;

use App\Services\ForumConditionsService;
use App\Services\PositionConditionsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

// use Illuminate\Support\Facades\Log;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $corId = null;
            $positionid = null;
            $secpositionid = []; // Array for secondary positions
            $loggedIn = null;
            $userAdmin = false;
            $userModerator = false;

            if (Auth::check()) {
                $user = Auth::user();

                if ($user->user_type == 'coordinator' && $user->coordinator) {
                    $corDetails = $user->coordinator;
                    $corId = $corDetails['id'];
                    $positionid = $corDetails['position_id'];
                    $secpositionid = $corDetails->secondaryPosition->pluck('id')->toArray(); // Get all secondary position IDs
                    $loggedIn = $corDetails['first_name'].' '.$corDetails['last_name'];
                }

                $userAdmin = $user->is_admin == '1';
                $userModerator = $user->is_admin == '2';
                $coordinator = ($user->user_type == 'coordinator' && $user->coordinator);
                $board = ($user->user_type == 'board' && $user->board);
                $outgoing = ($user->user_type == 'outgoing' && $user->outgoing);
                $disbanded = ($user->user_type == 'disbanded' && $user->disbanded);
                $pending = ($user->user_type == 'pending' && $user->pending);
            }

            $positionConditionsService = app(PositionConditionsService::class);
            $forumConditionsService = app(ForumConditionsService::class);

            $positionConditions = $positionConditionsService->getConditionsForUser($positionid, $secpositionid, $corId);
            $eoyDisplay = $positionConditionsService->getEOYDisplay();
            $forumCount = $forumConditionsService->getUnreadForumCount();

            // Merge all variables
            $viewVariables = array_merge([
                'corId' => $corId,
                'positionid' => $positionid,
                'secpositionid' => $secpositionid,
                'loggedIn' => $loggedIn,
                'userAdmin' => $userAdmin,
                'userModerator' => $userModerator,
                'unreadForumCount' => $forumCount,
                'positionService' => $positionConditionsService,
                'coordinator' => $coordinator,
                'board' => $board,
                'outgoing' => $outgoing,
                'disbanded' => $disbanded,
                'pending' => $pending,
            ],
                $positionConditions,
                $eoyDisplay,
            );

            // Pass all variables to views
            $view->with($viewVariables);
        });
    }

    public function register(): void
    {
        // Register as a singleton - let Laravel auto-resolve dependencies
        $this->app->singleton(PositionConditionsService::class);
        $this->app->singleton(ForumConditionsService::class);
    }
}
