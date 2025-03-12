<?php

namespace App\Providers;

use App\Listeners\ForumEventSubscriber;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use TeamTeaTime\Forum\Events\UserCreatedPost;
use TeamTeaTime\Forum\Events\UserCreatedThread;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Schema::defaultStringLength(191);

        // Register forum event listeners
        // Event::listen(UserCreatedPost::class, [ForumEventSubscriber::class, 'handleNewPost']);
        // Event::listen(UserCreatedThread::class, [ForumEventSubscriber::class, 'handleNewThread']);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
