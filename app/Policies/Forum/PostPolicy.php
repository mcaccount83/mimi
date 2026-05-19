<?php

namespace App\Policies\Forum;

use Illuminate\Foundation\Auth\User;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Policies\PostPolicy as ForumPostPolicy;

class PostPolicy extends ForumPostPolicy
{
    protected ForumConditions $forumConditions;

    public function __construct()
    {
        $this->forumConditions = app(ForumConditions::class);
    }

    public function edit(User $user, Post $post): bool
    {
        return $this->forumConditions->canManageLists($user)
            || $user->getKey() == $post->author_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $this->forumConditions->canManageLists($user)
            || $user->getKey() == $post->author_id;
    }

    public function restore(User $user, Post $post): bool
    {
        return $this->forumConditions->canManageLists($user)
            || $user->getKey() == $post->author_id;
    }
}
