<?php

namespace App\Providers;

use App\Enums\AdminStatusEnum;
use App\Enums\UserTypeEnum;
use App\Models\Chapters;
use App\Services\ForumConditionsService;
use App\Services\PendingConditionsService;
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
            $userTypeId = null;
            $userAdmin = false;
            $userModerator = false;
            $coordinator = false;
            $board = false;
            $outgoing = false;
            $disbanded = false;
            $pending = false;
            $chDetails = null;
            $confId = null;

            if (Auth::check()) {
                $user = Auth::user();
                $userTypeId = $user->type_id;

                if ($userTypeId == UserTypeEnum::COORD && $user->coordinator) {
                    $corDetails = $user->coordinator;
                    $corId = $corDetails['id'];
                    $positionid = $corDetails['position_id'];
                    $secpositionid = $corDetails->secondaryPosition->pluck('id')->toArray(); // Get all secondary position IDs
                    $loggedIn = $corDetails['first_name'].' '.$corDetails['last_name'];
                    $confId = $corDetails->state->conference_id ?? null;
                }

                if ($userTypeId == UserTypeEnum::BOARD && $user->board) {
                    $bdDetails = $user->board;
                    $chId = $bdDetails['chapter_id'];
                    $chDetails = Chapters::find($chId);
                }

                $userAdmin = $user->is_admin == AdminStatusEnum::ADMIN;
                $userModerator = $user->is_admin == AdminStatusEnum::MODERATOR;
                $coordinator = ($userTypeId == UserTypeEnum::COORD && $user->coordinator);
                $board = ($userTypeId == UserTypeEnum::BOARD && $user->board);
                $outgoing = ($userTypeId == UserTypeEnum::OUTGOING && $user->outgoing);
                $disbanded = ($userTypeId == UserTypeEnum::DISBANDED && $user->disbanded);
                $pending = ($userTypeId == UserTypeEnum::PENDING && $user->pending);
            }

            $positionConditionsService = app(PositionConditionsService::class);
            $forumConditionsService = app(ForumConditionsService::class);
            $PendingConditionsService = app(PendingConditionsService::class);

            $positionConditions = Auth::check() ? $positionConditionsService->getConditionsForUser($positionid, $secpositionid, $corId) : [];
            $EOYOptions = Auth::check() ? $positionConditionsService->getEOYOptions() : [];
            $forumCount = Auth::check() ? $forumConditionsService->getUnreadForumCount() : 0;
            $pendingThreadsCount = Auth::check() ? $forumConditionsService->getPendingThreadsCount() : 0;
            $pendingPostsCount = Auth::check() ? $forumConditionsService->getPendingPostsCount() : 0;
            $pendingInquiryCount = ($confId) ? $PendingConditionsService->getPendingInquiryCount($confId) : 0;
            $pendingNewChapterCount = ($confId) ? $PendingConditionsService->getpendingNewChapterCount($confId) : 0;
            $pendingNewCoordCount = ($confId) ? $PendingConditionsService->getpendingNewCoordCount($confId) : 0;
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
                'pendingThreadsCount' => $pendingThreadsCount,
                'pendingPostsCount' => $pendingPostsCount,
                'positionService' => $positionConditionsService,
                'pendingInquiryCount' => $pendingInquiryCount,
                'pendingNewChapterCount' => $pendingNewChapterCount,
                'pendingNewCoordCount' => $pendingNewCoordCount,
                'userTypeId' => $userTypeId,
                'coordinator' => $coordinator,
                'board' => $board,
                'outgoing' => $outgoing,
                'disbanded' => $disbanded,
                'pending' => $pending,
            ],
                $positionConditions,
                $EOYOptions,
                $dateOptions,
                ($userTypeId == UserTypeEnum::BOARD ? ['chDetails' => $chDetails] : []),
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
