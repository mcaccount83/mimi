<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Support\Access\CategoryAccess;

class ForumConditionsService
{
    /**
     * Get unread forum count
     */
    public function getUnreadForumCount(): int
    {
        if (! Auth::check()) {
            return 0;
        }

        $threads = Thread::recent()
            ->with('category')
            ->get()
            ->filter(function ($thread) {
                $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor(Auth::user());

                // If the category isn't private, allow access
                if (! $thread->category->is_private) {
                    return $thread->userReadStatus != null;
                }

                // For private categories, check if user has access via CategoryAccess
                return $thread->userReadStatus != null &&
                       $accessibleCategoryIds->contains($thread->category_id);
            });

        return $threads->whereNull('read_at')->count();
    }

    public function getPendingThreadsCount(): int
    {
        if (!Auth::check()) return 0;
        $user = Auth::user();
        if (!$this->canManageLists($user)) return 0;

        return Thread::pendingApproval()->count();
    }

    public function getPendingPostsCount(): int
    {
        if (!Auth::check()) return 0;
        $user = Auth::user();
        if (!$this->canManageLists($user)) return 0;

        return \TeamTeaTime\Forum\Models\Post::pendingApproval()
            ->notFirstInThread()
            ->count();
    }

    private function canManageLists($user): bool
    {
        return $user->type_id == \App\Enums\UserTypeEnum::COORD &&
            ($user->is_admin == \App\Enums\AdminStatusEnum::ADMIN ||
                $user->is_admin == \App\Enums\AdminStatusEnum::MODERATOR);
    }
}
