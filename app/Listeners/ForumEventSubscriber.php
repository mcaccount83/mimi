<?php

namespace App\Listeners;

use App\Enums\UserStatusEnum;
use App\Mail\NewForumPost;
use App\Mail\NewForumThread;
use App\Models\User;
use Dcblogdev\LaravelSentEmails\Models\SentEmail;
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

            $this->sendForumBroadcast(
                $usersToNotify,
                fn($user) => new NewForumPost($post, $thread, $category, $authorNameWithPosition),
                "{$category->title} | RE:{$thread->title}",
                $usersToNotify->count()
            );
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

            $this->sendForumBroadcast(
                $usersToNotify,
                fn($user) => new NewForumThread($post, $thread, $category, $authorNameWithPosition),
                "{$category->title} | {$thread->title}",
                $usersToNotify->count()
            );
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

        $this->sendForumBroadcast(
            $usersToNotify,
            fn($user) => new NewForumThread($post, $thread, $category, $authorNameWithPosition),
            "{$category->title} | {$thread->title}",
            $usersToNotify->count()
        );
    }

    public function handleNewPost(UserCreatedPost $event)
    {
        $post = $event->post;
        if (is_null($post->approved_at)) return;

        $thread = $post->thread;
        $category = $thread->category;
        $author = $post->author;
        $authorNameWithPosition = $author ? $author->authorNameWithPosition() : 'Unknown Author';
        $usersToNotify = $this->getUsersToNotify($thread);

        $this->sendForumBroadcast(
            $usersToNotify,
            fn($user) => new NewForumPost($post, $thread, $category, $authorNameWithPosition),
            "{$category->title} | RE:{$thread->title}",
            $usersToNotify->count()
        );
    }

    public function handleNewThread(UserCreatedThread $event)
    {
        $thread = $event->thread;
        if (is_null($thread->firstPost?->approved_at)) return;

        $post = $thread->firstPost;
        $category = $thread->category;
        $author = $thread->author;
        $authorNameWithPosition = $author ? $author->authorNameWithPosition() : 'Unknown Author';
        $usersToNotify = $this->getUsersToNotify($thread);

        $this->sendForumBroadcast(
            $usersToNotify,
            fn($user) => new NewForumThread($post, $thread, $category, $authorNameWithPosition),
            "{$category->title} | {$thread->title}",
            $usersToNotify->count()
        );
    }

    /**
     * Queue forum broadcast emails and write a single summary log entry.
     */
   private function sendForumBroadcast($usersToNotify, callable $mailableFactory, string $subject, int $recipientCount): void
{
    foreach ($usersToNotify->chunk(25) as $userBatch) {
        foreach ($userBatch as $user) {
            $mailable = $mailableFactory($user);
            Mail::to($user->email)->queue($mailable);
        }
    }

    SentEmail::create([
        'date'    => date('Y-m-d H:i:s'),
        'from'    => config('mail.from.address'),
        'to'      => "{$recipientCount} forum subscribers",
        'cc'      => null,
        'bcc'     => null,
        'subject' => "[Forum Broadcast] {$subject}",
        'body'    => "Notification queued for {$recipientCount} subscribers.",
    ]);
}

    /**
     * Get users who should be notified about thread updates.
     */
    private function getUsersToNotify($thread)
    {
        $categoryId = $thread->category_id;

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
