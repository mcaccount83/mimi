<?php

namespace App\Policies\Forum;

use TeamTeaTime\Forum\Policies\ThreadPolicy as ForumThreadPolicy;
use TeamTeaTime\Forum\Models\Thread;

use Illuminate\Foundation\Auth\User;

class ThreadPolicy extends ForumThreadPolicy

{
    public function canAccessVollist(User $user, Thread $thread): bool
    {
        if ($user->user_type === 'outgoing'){
            return false; // Hide ALL from outgoing
        }

        if ($thread->category === 'Vollist' && $user->user_type !== 'coordinator') {
            return false; // Hide from everyone except coordinators
        }

        return true; // Default: allow access
    }

    public function view($user, Thread $thread): bool
    {
        return $this->canAccessVollist($user, $thread);
    }

    public function rename($user, Thread $thread): bool
    {
        return $user->getKey() === $thread->author_id;
    }

    public function reply($user, Thread $thread): bool
    {
        return !$thread->locked;
    }

    public function delete($user, Thread $thread): bool
    {
        return $user->getKey() === $thread->author_id;
    }

    public function restore($user, Thread $thread): bool
    {
        return $user->getKey() === $thread->author_id;
    }

    public function deletePosts($user, Thread $thread): bool
    {
        return true;
    }

    public function restorePosts($user, Thread $thread): bool
    {
        return true;
    }
}
