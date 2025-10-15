<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

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
    Schema::defaultStringLength(191);

    // Register mail markdown views namespace
    $this->loadViewsFrom(resource_path('views/vendor/mail/html'), 'mail');

    // Add global BCC to all emails
    Event::listen(MessageSending::class, function ($event) {
        $bccAddress = config('mail.bcc.address');

        if ($bccAddress) {
            $event->message->bcc($bccAddress);

            // Get all BCC addresses to verify it was added
            $bccList = $event->message->getBcc();
            Log::info('BCC added successfully', [
                'config_bcc' => $bccAddress,
                'message_bcc_list' => $bccList ? array_keys($bccList) : 'EMPTY',
                'subject' => $event->message->getSubject()
            ]);
        }
    });

    $this->bootRoute();
}

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function bootRoute(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
