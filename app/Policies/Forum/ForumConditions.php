<?php

namespace App\Policies\Forum;

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
        if ($user->user_type == 'outgoing' || $user->user_type == 'disbanded' || $user->user_type == 'pending') {
            return false; // Hide ALL from outgoing/disbanded/pending
        }

        if ($category->title == 'CoordinatorList' && $user->user_type != 'coordinator') {
            return false; // Hide CoordinatorList from everyone except coordinators
        }

        return true; // Default: allow access
    }

    /**
     * Who can Manage Lists & Threads //  Admins and Moderators only
     */
    public function canManageLists(User $user): bool
    {
        $isCoordinator = $user->user_type == 'coordinator';
        $userAdmin = $user->is_admin == '1';
        $userModerator = $user->is_admin == '2';

        return $isCoordinator && ($userAdmin || $userModerator);
    }

    // public function canManageThreads($user, Thread $thread): bool
    // {
    //     // $category = $thread->category;

    //     return $this->canManageLists($user);
    // }
}
