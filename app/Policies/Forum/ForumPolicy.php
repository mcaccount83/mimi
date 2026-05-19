<?php

namespace App\Policies\Forum;

use Illuminate\Foundation\Auth\User;
use TeamTeaTime\Forum\Policies\ForumPolicy as BaseForumPolicy;

class ForumPolicy extends BaseForumPolicy
{
    protected ForumConditions $forumConditions;

    public function __construct()
    {
        $this->forumConditions = app(ForumConditions::class);
    }

    public function createCategories(User $user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function moveCategories(User $user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function editCategories(User $user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function deleteCategories(User $user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function markThreadsAsRead(User $user): bool
    {
        return true;
    }

    public function approveThreads(User $user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function approvePosts(User $user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function viewTrashedThreads(User $user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function viewTrashedPosts(User $user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }
}
