<?php

namespace App\Policies\Forum;

use App\Enums\AdminStatusEnum;
use App\Enums\UserTypeEnum;
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

        if ($category->title == 'CoordinatorList' && $user->type_id != UserTypeEnum::COORD) {
            return false; // Hide CoordinatorList from everyone except coordinators
        }

        return true; // Default: allow access
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
