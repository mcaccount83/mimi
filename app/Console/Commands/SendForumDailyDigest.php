<?php

namespace App\Console\Commands;

use App\Enums\UserStatusEnum;
use App\Mail\ForumDailyDigest;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use TeamTeaTime\Forum\Models\Thread;

class SendForumDailyDigest extends Command
{
    protected $signature = 'forum:send-daily-digest';
    protected $description = 'Send daily digest emails to subscribers who prefer a daily summary over immediate notifications';

    public function handle(): void
    {
        $users = User::where('is_active', UserStatusEnum::ACTIVE)
            ->whereHas('categorySubscriptions', fn ($q) => $q->where('notification_frequency', 'daily_digest'))
            ->with(['categorySubscriptions' => fn ($q) => $q->where('notification_frequency', 'daily_digest')])
            ->get();

        $delay = 0;

        foreach ($users as $user) {
            $windowStart = $user->last_digest_sent_at ?? now()->subDay();
            $categoryIds = $user->categorySubscriptions->pluck('category_id');

            $threadsByCategory = Thread::whereIn('category_id', $categoryIds)
                ->whereHas('posts', fn ($q) => $q->whereNotNull('approved_at')->where('created_at', '>=', $windowStart))
                ->with([
                    'category',
                    'posts' => fn ($q) => $q->whereNotNull('approved_at')
                        ->where('created_at', '>=', $windowStart)
                        ->with('author'),
                ])
                ->get()
                ->groupBy('category_id');

            if ($threadsByCategory->isEmpty()) {
                continue;
            }

            Mail::to($user->email)->later(now()->addSeconds($delay), new ForumDailyDigest($threadsByCategory, $user));
            $delay += 3;

            $user->update(['last_digest_sent_at' => now()]);
        }
    }
}
