<?php

namespace App\Policies\Forum;

use App\Enums\ForumCategoryEnum;
use Illuminate\Foundation\Auth\User;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Policies\ThreadPolicy as ForumThreadPolicy;

class ThreadPolicy extends ForumThreadPolicy
{
    protected ForumConditions $forumConditions;

    public function __construct()
    {
        $this->forumConditions = app(ForumConditions::class);
    }

    public function view(User $user, Thread $thread): bool
    {
        return $this->forumConditions->canAccessList($user, $thread->category);
    }

    public function rename(User $user, Thread $thread): bool
    {
        return $this->forumConditions->canManageLists($user)
            // || $this->forumConditions->canManageThreads($user, $thread)
            || $user->getKey() == $thread->author_id;
    }

    public function reply(User $user, Thread $thread): bool
    {
        if ($thread->category->id == ForumCategoryEnum::BOARDLIST) {
            return $this->forumConditions->canPostToBoardList($user)
                || $this->forumConditions->canManageLists($user);
        }

        return $this->forumConditions->canManageLists($user)
        || ($this->forumConditions->canAccessList($user, $thread->category) && ! $thread->locked);
    }

    public function replyWithoutApproval(User $user, Thread $thread): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function delete(User $user, Thread $thread): bool
    {
        return $this->forumConditions->canManageLists($user)
            // || $this->forumConditions->canManageThreads($user, $thread)
            || $user->getKey() == $thread->author_id;
    }

    public function restore(User $user, Thread $thread): bool
    {
        return $this->forumConditions->canManageLists($user)
            // || $this->forumConditions->canManageThreads($user, $thread)
            || $user->getKey() == $thread->author_id;
    }

    public function approvePosts(User $user, Thread $thread): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function deletePosts(User $user, Thread $thread): bool
    {
        return $this->forumConditions->canManageLists($user);
        // || $this->forumConditions->canManageThreads($user, $thread);
    }

    public function restorePosts(User $user, Thread $thread): bool
    {
        return $this->forumConditions->canManageLists($user);
        // || $this->forumConditions->canManageThreads($user, $thread);
    }
}
