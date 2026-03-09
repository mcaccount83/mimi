<?php

namespace App\Providers;

use App\Listeners\EmailLogger;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class SentEmailsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(MessageSending::class, EmailLogger::class);

        $viewsPath = resource_path('views/vendor/sentemails');
        if (!is_dir($viewsPath)) {
            $viewsPath = base_path('vendor/dcblogdev/laravel-sent-emails/src/resources/views');
        }
        $this->loadViewsFrom($viewsPath, 'sentemails');

        $this->loadRoutesFrom(
            base_path('vendor/dcblogdev/laravel-sent-emails/src/routes/web.php')
        );
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            base_path('vendor/dcblogdev/laravel-sent-emails/config/sentemails.php'),
            'sentemails'
        );
    }
}
