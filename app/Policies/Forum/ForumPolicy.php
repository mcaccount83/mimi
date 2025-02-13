<?php

namespace App\Policies\Forum;

use TeamTeaTime\Forum\Policies\ForumPolicy as BaseForumPolicy;

class ForumPolicy extends BaseForumPolicy
{
    protected $forumConditions;

    public function __construct()
    {
        $this->forumConditions = app(ForumConditions::class);
    }

    public function createCategories($user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function moveCategories($user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function editCategories($user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function deleteCategories($user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function markThreadsAsRead($user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function viewTrashedThreads($user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function viewTrashedPosts($user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }
}
