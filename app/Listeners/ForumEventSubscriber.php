<?php

namespace App\Listeners;

use TeamTeaTime\Forum\Events\UserCreatedPost;
use TeamTeaTime\Forum\Events\UserCreatedThread;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewForumPost;
use App\Mail\NewForumThread;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ForumEventSubscriber
{
    /**
     * Handle new post events.
     */
    public function handleNewPost(UserCreatedPost $event)
    {
        // Get post data
        $post = $event->post;
        $thread = $post->thread;
        $author = $post->author;
        $authorNameWithPosition = $author ? $author->authorNameWithPosition() : 'Unknown Author';

        // Get users to notify
        $usersToNotify = $this->getUsersToNotify($thread);

        // Send email to each user
        foreach ($usersToNotify as $user) {
            Mail::to($user->email)->queue(new NewForumPost($post, $thread, $authorNameWithPosition));
        }
    }

    /**
     * Handle new thread events.
     */
    public function handleNewThread(UserCreatedThread $event)
    {
        // Get thread data
        $thread = $event->thread;
        $post = $thread->firstPost;
        $author = $thread->author;
        $authorNameWithPosition = $author ? $author->authorNameWithPosition() : 'Unknown Author';

        // Get users to notify
        $usersToNotify = $this->getUsersToNotify($thread);

        // Send email to each user
        foreach ($usersToNotify as $user) {
            Mail::to($user->email)->queue(new NewForumThread($post, $thread, $authorNameWithPosition));
        }
    }

    /**
     * Get users who should be notified about thread updates.
     */
    private function getUsersToNotify($thread)
    {
        // Get category ID from the thread
        $categoryId = $thread->category_id;

        // Get all users subscribed to this category
        return User::whereHas('categorySubscriptions', function ($query) use ($categoryId) {
            $query->where('category_id', $categoryId);
            })
            ->where('is_active', '1')
            ->get();
        }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe($events)
    {
        return [
            UserCreatedPost::class => 'handleNewPost',
            UserCreatedThread::class => 'handleNewThread',
        ];
    }
}
