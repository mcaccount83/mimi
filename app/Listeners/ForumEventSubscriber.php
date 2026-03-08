<?php

namespace App\Listeners;

use App\Enums\UserStatusEnum;
use App\Mail\NewForumPost;
use App\Mail\NewForumThread;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use TeamTeaTime\Forum\Events\UserCreatedPost;
use TeamTeaTime\Forum\Events\UserCreatedThread;
use TeamTeaTime\Forum\Events\UserApprovedThread;
use TeamTeaTime\Forum\Events\UserBulkApprovedPosts;
use TeamTeaTime\Forum\Events\UserBulkApprovedThreads;

class ForumEventSubscriber
{

public function handleApprovedPost(UserBulkApprovedPosts $event)
{
    foreach ($event->collection as $post) {
        $thread = $post->thread;
        $category = $thread->category;
        $author = $post->author;
        $authorNameWithPosition = $author ? $author->authorNameWithPosition() : 'Unknown Author';
        $usersToNotify = $this->getUsersToNotify($thread);
        foreach ($usersToNotify->chunk(25) as $userBatch) {
            foreach ($userBatch as $user) {
                Mail::to($user->email)->queue(new NewForumPost($post, $thread, $category, $authorNameWithPosition));
            }
        }
    }
}

public function handleApprovedThread(UserBulkApprovedThreads $event)
{
    foreach ($event->collection as $thread) {
        $post = $thread->firstPost;
        $category = $thread->category;
        $author = $thread->author;
        $authorNameWithPosition = $author ? $author->authorNameWithPosition() : 'Unknown Author';
        $usersToNotify = $this->getUsersToNotify($thread);
        foreach ($usersToNotify->chunk(25) as $userBatch) {
            foreach ($userBatch as $user) {
                Mail::to($user->email)->queue(new NewForumThread($post, $thread, $category, $authorNameWithPosition));
            }
        }
    }
}

public function handleApprovedSingleThread(UserApprovedThread $event)
{
    $thread = $event->thread;
    $post = $thread->firstPost;
    $category = $thread->category;
    $author = $thread->author;
    $authorNameWithPosition = $author ? $author->authorNameWithPosition() : 'Unknown Author';
    $usersToNotify = $this->getUsersToNotify($thread);
    foreach ($usersToNotify->chunk(25) as $userBatch) {
        foreach ($userBatch as $user) {
            Mail::to($user->email)->queue(new NewForumThread($post, $thread, $category, $authorNameWithPosition));
        }
    }
}

    /**
     * Handle new post events.
     */
    public function handleNewPost(UserCreatedPost $event)
    {
         $post = $event->post;
    if (is_null($post->approved_at)) return; // requires approval, wait for approval event

            // Get post data
        $post = $event->post;
        $thread = $post->thread;
        $category = $thread->category;
        $author = $post->author;
        $authorNameWithPosition = $author ? $author->authorNameWithPosition() : 'Unknown Author';

        // Get users to notify
        $usersToNotify = $this->getUsersToNotify($thread);

        // Send email to each user
        foreach ($usersToNotify->chunk(25) as $userBatch) {
            foreach ($userBatch as $user) {
                Mail::to($user->email)->queue(new NewForumPost($post, $thread, $category, $authorNameWithPosition));
            }
        }
    }

    /**
     * Handle new thread events.
     */
    public function handleNewThread(UserCreatedThread $event)
    {
        $thread = $event->thread;
    if (is_null($thread->firstPost?->approved_at)) return; // requires approval, wait for approval event

        // Get thread data
        $thread = $event->thread;
        $post = $thread->firstPost;
        $category = $thread->category;
        $author = $thread->author;
        $authorNameWithPosition = $author ? $author->authorNameWithPosition() : 'Unknown Author';

        // Get users to notify
        $usersToNotify = $this->getUsersToNotify($thread);

        // Chunk the users into smaller groups (e.g., 5-10 users per batch)
        foreach ($usersToNotify->chunk(25) as $userBatch) {
            foreach ($userBatch as $user) {
                Mail::to($user->email)->queue(new NewForumThread($post, $thread, $category, $authorNameWithPosition));
            }
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
            ->where('is_active', UserStatusEnum::ACTIVE)
            ->get();
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe($events)
{
    return [
        UserCreatedPost::class          => 'handleNewPost',
        UserCreatedThread::class        => 'handleNewThread',
        UserBulkApprovedPosts::class    => 'handleApprovedPost',
        UserBulkApprovedThreads::class  => 'handleApprovedThread',
        UserApprovedThread::class       => 'handleApprovedSingleThread',
    ];
}
}
