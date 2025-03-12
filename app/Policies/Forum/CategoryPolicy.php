<?php

namespace App\Policies\Forum;

use Illuminate\Foundation\Auth\User;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Policies\CategoryPolicy as ForumCategoryPolicy;

class CategoryPolicy extends ForumCategoryPolicy
{
    protected $forumConditions;

    public function __construct()
    {
        $this->forumConditions = app(ForumConditions::class);
    }

    public function view(User $user, Category $category): bool
    {
        return $this->forumConditions->canAccessCoordinatorList($user, $category);
    }

    public function edit(User $user, Category $category): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function delete(User $user, Category $category): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function createThreads(User $user, Category $category): bool
    {
        if ($category->title === 'Public Announcements') {
            return $this->forumConditions->canManageLists($user);
        }

        return $this->forumConditions->canAccessCoordinatorList($user, $category);
    }

    public function manageThreads(User $user, Category $category): bool
    {
        return $this->deleteThreads($user, $category)
            || $this->restoreThreads($user, $category)
            || $this->moveThreadsFrom($user, $category)
            || $this->lockThreads($user, $category)
            || $this->pinThreads($user, $category);
    }

    public function deleteThreads(User $user, Category $category): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function restoreThreads(User $user, Category $category): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function moveThreadsFrom(User $user, Category $category): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function moveThreadsTo(User $user, Category $category): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function lockThreads(User $user, Category $category): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function pinThreads(User $user, Category $category): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function markThreadsAsRead(User $user, Category $category): bool
    {
        return $this->forumConditions->canManageLists($user);
    }
}
