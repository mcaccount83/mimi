<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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

        // Fix https mixed content issue on live for vite
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Register mail markdown views namespace
        $this->loadViewsFrom(resource_path('views/vendor/mail/html'), 'mail');

        // Custom Blade directives
        Blade::directive('emailValidation', function () {
            return <<<'JS'
            <script>
            function isValidEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }

            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('input[type="email"]').forEach(function(field) {
                    field.addEventListener('blur', function() {
                        const value = this.value.trim();

                        // Remove any existing error message
                        const existing = this.parentNode.querySelector('.email-error');
                        if (existing) existing.remove();

                        if (value !== '' && !isValidEmail(value)) {
                            this.classList.add('is-invalid');
                            const msg = document.createElement('div');
                            msg.classList.add('email-error');
                            msg.style.color = '#dc3545';
                            msg.style.fontSize = '0.875em';
                            msg.textContent = 'Please enter a valid email address.';
                            this.parentNode.appendChild(msg);
                        } else {
                            this.classList.remove('is-invalid');
                        }
                    });
                });
            });
            </script>
            JS;
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
