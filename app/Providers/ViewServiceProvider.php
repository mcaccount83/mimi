<?php

namespace App\Providers;

use App\Enums\UserTypeEnum;
use App\Enums\AdminStatusEnum;
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
            $coordinator = false;
            $board = false;
            $outgoing = false;
            $disbanded = false;
            $pending = false;

            if (Auth::check()) {
                $user = Auth::user();

                if ($user->type_id ==  UserTypeEnum::COORD && $user->coordinator) {
                    $corDetails = $user->coordinator;
                    $corId = $corDetails['id'];
                    $positionid = $corDetails['position_id'];
                    $secpositionid = $corDetails->secondaryPosition->pluck('id')->toArray(); // Get all secondary position IDs
                    $loggedIn = $corDetails['first_name'].' '.$corDetails['last_name'];
                }

                $userAdmin = $user->is_admin == AdminStatusEnum::ADMIN;
                $userModerator = $user->is_admin == AdminStatusEnum::MODERATOR;
                $coordinator = ($user->type_id ==  UserTypeEnum::COORD && $user->coordinator);
                $board = ($user->type_id ==  UserTypeEnum::BOARD && $user->board);
                $outgoing = ($user->type_id ==  UserTypeEnum::OUTGOING && $user->outgoing);
                $disbanded = ($user->type_id ==  UserTypeEnum::DISBANDED && $user->disbanded);
                $pending = ($user->type_id ==  UserTypeEnum::PENDING && $user->pending);
            }

            $positionConditionsService = app(PositionConditionsService::class);
            $forumConditionsService = app(ForumConditionsService::class);

            $positionConditions = $positionConditionsService->getConditionsForUser($positionid, $secpositionid, $corId);
            $EOYOptions = $positionConditionsService->getEOYOptions();
            $forumCount = $forumConditionsService->getUnreadForumCount();
            $dateOptions = $positionConditionsService->getDateOptions();

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
                $EOYOptions,
                $dateOptions,
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
