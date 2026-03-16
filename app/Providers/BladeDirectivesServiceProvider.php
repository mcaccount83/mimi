<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Carbon\Carbon;

class BladeDirectivesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
       Blade::directive('formatDate', function ($expression) {
            return "<?php echo $expression ? \\Carbon\\Carbon::parse($expression)->format('m/d/Y') : ''; ?>";
        });
        // example - @formatDate($chPayments->rereg_date)

        Blade::directive('tel', function ($expression) {
            return "<?php echo 'tel:+1' . preg_replace('/[^0-9]/', '', $expression); ?>";
        });
        // example - @tel($chapter->phone)

        // add more here as needed
    }
}
