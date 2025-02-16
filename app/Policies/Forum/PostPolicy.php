<?php

namespace App\Policies\Forum;

use TeamTeaTime\Forum\Policies\PostPolicy as ForumPostPolicy;

use TeamTeaTime\Forum\Models\Post;

class PostPolicy extends ForumPostPolicy
{
    protected $forumConditions;

    public function __construct()
    {
        $this->forumConditions = app(ForumConditions::class);
    }

    public function edit($user, Post $post): bool
    {
        return $this->forumConditions->canManageLists($user)
            ||  $user->getKey() === $post->author_id;
    }

    public function delete($user, Post $post): bool
    {
        return $this->forumConditions->canManageLists($user)
            ||  $user->getKey() === $post->author_id;
    }

    public function restore($user, Post $post): bool
    {
        return $this->forumConditions->canManageLists($user)
            ||  $user->getKey() === $post->author_id;
    }
}
