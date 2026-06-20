<?php

namespace App\Listeners;

use App\Enums\UserStatusEnum;
use App\Mail\ForumBroadcastSummary;
use App\Mail\ForumNewPost;
use App\Mail\ForumPendingApproval;
use App\Models\User;
use App\Services\PositionConditionsService;
use Dcblogdev\LaravelSentEmails\Models\SentEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use TeamTeaTime\Forum\Events\UserApprovedThread;
use TeamTeaTime\Forum\Events\UserBulkApprovedPosts;
use TeamTeaTime\Forum\Events\UserBulkApprovedThreads;
use TeamTeaTime\Forum\Events\UserCreatedPost;
use TeamTeaTime\Forum\Events\UserCreatedThread;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Category;

class ForumEventSubscriber
{
    public function __construct(
        protected PositionConditionsService $positionConditionsService,
    ) {}

    public function handleApprovedPost(UserBulkApprovedPosts $event)
    {
        foreach ($event->collection as $post) {
            $thread = $post->thread;
            $category = $thread->category;
            $author = $post->author;
            $authorNameWithPosition = $author ? $author->authorNameForDisplay($category->id) : 'Unknown Author';
            $usersToNotify = $this->getUsersToNotify($thread);

            $this->sendForumBroadcast(
                $usersToNotify,
                fn ($user) => new ForumNewPost($post, $thread, $category, $authorNameWithPosition),
                "{$category->title} | RE:{$thread->title}",
                $usersToNotify->count(),
                $post,
                $thread,
                $category,
                $authorNameWithPosition
            );
        }
    }

    public function handleApprovedThread(UserBulkApprovedThreads $event)
    {
        foreach ($event->collection as $thread) {
            $post = $thread->firstPost;
            $category = $thread->category;
            $author = $thread->author;
            $authorNameWithPosition = $author ? $author->authorNameForDisplay($category->id) : 'Unknown Author';
            $usersToNotify = $this->getUsersToNotify($thread);

            $this->sendForumBroadcast(
                $usersToNotify,
                fn ($user) => new ForumNewPost($post, $thread, $category, $authorNameWithPosition),
                "{$category->title} | RE:{$thread->title}",
                $usersToNotify->count(),
                $post,
                $thread,
                $category,
                $authorNameWithPosition
            );
        }
    }

    public function handleApprovedSingleThread(UserApprovedThread $event)
    {
        $thread = $event->thread;
        $post = $thread->firstPost;
        $category = $thread->category;
        $author = $thread->author;
        $authorNameWithPosition = $author ? $author->authorNameForDisplay($category->id) : 'Unknown Author';
        $usersToNotify = $this->getUsersToNotify($thread);

        $this->sendForumBroadcast(
            $usersToNotify,
            fn ($user) => new ForumNewPost($post, $thread, $category, $authorNameWithPosition),
            "{$category->title} | RE:{$thread->title}",
            $usersToNotify->count(),
            $post,
            $thread,
            $category,
            $authorNameWithPosition
        );
    }

    public function handleNewPost(UserCreatedPost $event)
    {
        $post = $event->post;
        $thread = $post->thread;
        $category = $thread->category;
        $author = $post->author;
        $authorNameWithPosition = $author ? $author->authorNameForDisplay($category->id) : 'Unknown Author';

        if (is_null($post->approved_at)) {
            $adminEmails = $this->positionConditionsService->getAdminEmail();
            $listAdmin = $adminEmails['list_admin'];
            if ($listAdmin) {
                Mail::to($listAdmin)->queue(new ForumPendingApproval('reply', $thread, $post, $authorNameWithPosition));
            }
            return;
        }

        $usersToNotify = $this->getUsersToNotify($thread);

        $this->sendForumBroadcast(
            $usersToNotify,
            fn ($user) => new ForumNewPost($post, $thread, $category, $authorNameWithPosition),
            "{$category->title} | RE:{$thread->title}",
            $usersToNotify->count(),
            $post,
            $thread,
            $category,
            $authorNameWithPosition
        );
    }

     public function handleNewThread(UserCreatedThread $event)
    {
        $thread = $event->thread;
        $post = $thread->firstPost;
        $category = $thread->category;
        $author = $thread->author;
        $authorNameWithPosition = $author ? $author->authorNameForDisplay($category->id) : 'Unknown Author';

        if (is_null($post?->approved_at)) {
            $adminEmails = $this->positionConditionsService->getAdminEmail();
            $listAdmin = $adminEmails['list_admin'];
            if ($listAdmin) {
                Mail::to($listAdmin)->queue(new ForumPendingApproval('thread', $thread, $post, $authorNameWithPosition));
            }
            return;
        }

        $usersToNotify = $this->getUsersToNotify($thread);

        $this->sendForumBroadcast(
            $usersToNotify,
            fn ($user) => new ForumNewPost($post, $thread, $category, $authorNameWithPosition),
            "{$category->title} | RE:{$thread->title}",
            $usersToNotify->count(),
            $post,
            $thread,
            $category,
            $authorNameWithPosition
        );
    }

    /**
     * Queue forum broadcast emails and write a single summary log entry.
     */
    private function sendForumBroadcast(Collection $usersToNotify, callable $mailableFactory, string $subject, int $recipientCount,
        Post $post, Thread $thread, Category $category, string $authorNameWithPosition): void
    {
        $delay = 0;

        foreach ($usersToNotify->chunk(25) as $userBatch) {
            foreach ($userBatch as $user) {
                $mailable = $mailableFactory($user);
                Mail::to($user->email)
                    ->later(now()->addSeconds($delay), $mailable);
                $delay += 3;
            }
        }

        $adminEmails = $this->positionConditionsService->getAdminEmail();
        $listAdmin = $adminEmails['list_admin'];

        if ($listAdmin) {
            Mail::to($listAdmin)->queue(new ForumBroadcastSummary(
                $subject, $recipientCount, $post, $thread, $category, $authorNameWithPosition
            ));

        } else {
            SentEmail::create([
                'date' => date('Y-m-d H:i:s'),
                'from' => config('mail.from.address'),
                'to' => "{$recipientCount} forum subscribers",
                'cc' => $listAdmin ?: null,
                'bcc' => null,
                'subject' => "[Forum Broadcast] {$subject}",
                'body' => "Notification queued for {$recipientCount} subscribers.",
            ]);
        }
    }

    /**
     * Get users who should be notified about thread updates.
     */
    private function getUsersToNotify(Thread $thread)
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
            UserCreatedPost::class => 'handleNewPost',
            UserCreatedThread::class => 'handleNewThread',
            UserBulkApprovedPosts::class => 'handleApprovedPost',
            UserBulkApprovedThreads::class => 'handleApprovedThread',
            UserApprovedThread::class => 'handleApprovedSingleThread',
        ];
    }
}



    // public function handleNewPost(UserCreatedPost $event)
    // {
    //     $post = $event->post;
    //     if (is_null($post->approved_at)) {
    //         return;
    //     }

    //     $thread = $post->thread;
    //     $category = $thread->category;
    //     $author = $post->author;
    //     $authorNameWithPosition = $author ? $author->authorNameForDisplay($category->id) : 'Unknown Author';
    //     $usersToNotify = $this->getUsersToNotify($thread);

    //     $this->sendForumBroadcast(
    //         $usersToNotify,
    //         fn ($user) => new ForumNewPost($post, $thread, $category, $authorNameWithPosition),
    //         "{$category->title} | RE:{$thread->title}",
    //         $usersToNotify->count(),
    //         $post,
    //         $thread,
    //         $category,
    //         $authorNameWithPosition
    //     );
    // }

    // public function handleNewThread(UserCreatedThread $event)
    // {
    //     $thread = $event->thread;
    //     if (is_null($thread->firstPost?->approved_at)) {
    //         return;
    //     }

    //     $post = $thread->firstPost;
    //     $category = $thread->category;
    //     $author = $thread->author;
    //     $authorNameWithPosition = $author ? $author->authorNameForDisplay($category->id) : 'Unknown Author';
    //     $usersToNotify = $this->getUsersToNotify($thread);

    //     $this->sendForumBroadcast(
    //         $usersToNotify,
    //         fn ($user) => new ForumNewPost($post, $thread, $category, $authorNameWithPosition),
    //         "{$category->title} | RE:{$thread->title}",
    //         $usersToNotify->count(),
    //         $post,
    //         $thread,
    //         $category,
    //         $authorNameWithPosition
    //     );
    // }
