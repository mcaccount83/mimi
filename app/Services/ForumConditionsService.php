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
                    return $thread->userReadStatus !== null;
                }

                // For private categories, check if user has access via CategoryAccess
                return $thread->userReadStatus !== null &&
                       $accessibleCategoryIds->contains($thread->category_id);
            });

        return $threads->whereNull('read_at')->count();
    }
}
