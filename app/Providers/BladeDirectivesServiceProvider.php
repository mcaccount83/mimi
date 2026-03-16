<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Carbon\Carbon;

class BladeDirectivesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::directive('mailto', function ($expression) {
            return "<?php echo !empty($expression) ? '<a href=\"mailto:' . $expression . '\">' . $expression . '</a>' : ''; ?>";
        });
        // example - @mailto($MVPDetails->email)

       Blade::directive('tel', function ($expression) {
            return "<?php echo !empty($expression) ? '<a href=\"tel:+1' . preg_replace('/[^0-9]/', '', $expression) . '\">' . $expression . '</a>' : ''; ?>";
        });
        // example - @formatDate($chPayments->rereg_date)

        Blade::directive('tel', function ($expression) {
            return "<?php echo 'tel:+1' . preg_replace('/[^0-9]/', '', $expression); ?>";
        });
        // example - @tel($chapter->phone)

        // add more here as needed
    }
}
