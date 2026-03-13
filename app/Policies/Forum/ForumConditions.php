<?php

namespace App\Policies\Forum;

use App\Enums\AdminStatusEnum;
use App\Enums\UserTypeEnum;
use App\Enums\ForumCategoryEnum;
use App\Models\Coordinators;
use App\Services\PositionConditionsService;
use Illuminate\Foundation\Auth\User;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class ForumConditions
{
    protected $positionService;

    public function __construct(PositionConditionsService $positionService)
    {
        $this->positionService = $positionService;
    }

    /**
     * Who can view Lists
     */
    public function canAccessList(User $user, Category $category): bool
    {
        if ($user->type_id == UserTypeEnum::OUTGOING || $user->type_id == UserTypeEnum::DISBANDED || $user->type_id == UserTypeEnum::PENDING) {
            return false; // Hide ALL from outgoing/disbanded/pending
        }

        if ($category->id == ForumCategoryEnum::COORDLIST && $user->type_id != UserTypeEnum::COORD) {
            return false; // Hide CoordinatorList from everyone except coordinators
        }

        return true; // Default: allow access
    }

    /**
     * Who can post on BoardList //  Board members only
     */
    public function canPostToBoardList(User $user): bool
    {
        return $user->type_id == UserTypeEnum::BOARD;
    }

    /**
     * Who can Manage Lists & Threads //  Admins and Moderators only
     */
    public function canManageLists(User $user): bool
    {
        $isCoordinator = $user->type_id == UserTypeEnum::COORD;
        $userAdmin = $user->is_admin == AdminStatusEnum::ADMIN;
        $userModerator = $user->is_admin == AdminStatusEnum::MODERATOR;

        return $isCoordinator && ($userAdmin || $userModerator);
    }

    // public function canManageThreads($user, Thread $thread): bool
    // {
    //     // $category = $thread->category;

    //     return $this->canManageLists($user);
    // }
}
