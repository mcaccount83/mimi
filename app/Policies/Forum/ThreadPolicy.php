<?php

namespace App\Policies\Forum;

use TeamTeaTime\Forum\Policies\ThreadPolicy as ForumThreadPolicy;
use TeamTeaTime\Forum\Models\Thread;

class ThreadPolicy extends ForumThreadPolicy
{
    protected $forumConditions;

    public function __construct()
    {
        $this->forumConditions = app(ForumConditions::class);
    }

    public function view($user, Thread $thread): bool
    {
        return $this->forumConditions->checkPublicAnnouncements($user, $thread);
    }

    public function rename($user, Thread $thread): bool
    {
        if (!$this->forumConditions->checkPublicAnnouncements($user, $thread)) {
            return false;
        }

        return $user->getKey() === $thread->author_id;
    }

    public function reply($user, Thread $thread): bool
    {
        if (!$this->forumConditions->checkPublicAnnouncements($user, $thread)) {
            return false;
        }

        return !$thread->locked;
    }

    public function delete($user, Thread $thread): bool
    {
        if (!$this->forumConditions->checkPublicAnnouncements($user, $thread)) {
            return false;
        }

        return $user->getKey() === $thread->author_id;
    }

    public function restore($user, Thread $thread): bool
    {
        if (!$this->forumConditions->checkPublicAnnouncements($user, $thread)) {
            return false;
        }

        return $user->getKey() === $thread->author_id;
    }

    public function deletePosts($user, Thread $thread): bool
    {
        return $this->forumConditions->checkPublicAnnouncements($user, $thread);
    }

    public function restorePosts($user, Thread $thread): bool
    {
        return $this->forumConditions->checkPublicAnnouncements($user, $thread);
    }
}
