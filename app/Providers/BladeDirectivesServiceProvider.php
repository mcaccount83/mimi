<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeDirectivesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Inject Mask: @initMasks - initializes ALL data-inputmask fields
        Blade::directive('initMasks', function () {
            return <<<'PHP'
            <?php echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                $("[data-inputmask]:visible").inputmask();
            });
            </script>'; ?>
            PHP;
        });

        // Email Hyperlink: @mailto($MVPDetails->email)
        Blade::directive('mailto', function ($expression) {
            return "<?php echo !empty($expression) ? '<a href=\"mailto:' . $expression . '\">' . $expression . '</a>' : ''; ?>";
        });

        // URL Hyperlink: @formatUrl($chapter->website_url)
        Blade::directive('formatUrl', function ($expression) {
            return "<?php echo !empty($expression) ? '<a href=\"' . $expression . '\" target=\"_blank\">' . $expression . '</a>' : ''; ?>";
        });

        // URL Input field with Mask: @urlInput('ch_website', $chDetails->website_url)
        Blade::directive('urlInput', function ($expression) {
            [$name, $value] = array_map('trim', explode(',', $expression, 2));

            return "<?php echo '<input type=\"text\" name=\"' . $name . '\" id=\"' . $name . '\" class=\"form-control http-mask\" value=\"' . (' . $value . ' ?? \'\') . '\" placeholder=\"http://\">'; ?>";
        });

        // Phone Hyperlink: @tel($chapter->phone)
        Blade::directive('tel', function ($expression) {
            return "<?php echo !empty($expression) ? '<a href=\"tel:+1' . preg_replace('/[^0-9]/', '', $expression) . '\">' . $expression . '</a>' : ''; ?>";
        });

        // Phone Display: @formatPhone($member->phone)
        Blade::directive('formatPhone', function ($expression) {
            return "<?php echo !empty($expression) ? preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', preg_replace('/[^0-9]/', '', $expression)) : ''; ?>";
        });

        // Phone Input field with Mask: @phoneInput('ch_avp_phone', $AVPDetails->phone, false)
        Blade::directive('phoneInput', function ($expression) {
            $parts = array_map('trim', explode(',', $expression, 3));
            $name = trim($parts[0], "'\"");
            $value = isset($parts[1]) ? $parts[1] : "''";
            $required = isset($parts[2]) && trim($parts[2]) === 'false' ? '' : 'required';

            return "<?php echo '<input type=\"text\" name=\"{$name}\" id=\"{$name}\" class=\"form-control\" data-inputmask=\'\"mask\": \"(999) 999-9999\"\' data-mask value=\"' . htmlspecialchars({$value} ?? '') . '\" placeholder=\"Phone Number\" {$required}>'; ?>";
        });

        // Date Display: @formatDate($chPayments->rereg_date)
        Blade::directive('formatDate', function ($expression) {
            return "<?php echo $expression ? \\Carbon\\Carbon::parse($expression)->format('m/d/Y') : ''; ?>";
        });

        // Date Input field: @dateInput('PaymentDate', $payment->date)
        Blade::directive('dateInput', function ($expression) {
            $parts = array_map('trim', explode(',', $expression, 2));
            $name = trim($parts[0], "'\"");
            $value = isset($parts[1]) ? $parts[1] : "''";

            return "<?php
                \$_d = {$value} ?? '';
                \$_formatted = \$_d ? \\Carbon\\Carbon::parse(\$_d)->format('m/d/Y') : '';
                echo '<input type=\"text\" name=\"{$name}\" id=\"{$name}\" class=\"form-control\" data-inputmask=\'\"alias\": \"datetime\", \"inputFormat\": \"mm/dd/yyyy\"\' data-mask value=\"' . htmlspecialchars(\$_formatted) . '\" placeholder=\"mm/dd/yyyy\">';
            ?>";
        });

        // Currency Display: @formatCurrency($chFinancialReport->bank_balance_now)
        Blade::directive('formatCurrency', function ($expression) {
            return "<?php echo !empty($expression) ? '$' . number_format((float){$expression}, 2) : '$0.00'; ?>";
        });

        // Currency Input field: @currencyInput('BankBalanceNow', $chFinancialReport->bank_balance_now, false)
        Blade::directive('currencyInput', function ($expression) {
            $parts = array_map('trim', explode(',', $expression, 4));
            $name = trim($parts[0], "'\"");
            $value = isset($parts[1]) ? $parts[1] : "''";
            $readonly = isset($parts[2]) && trim($parts[2]) === 'true' ? 'readonly' : '';
            $oninput = isset($parts[3]) ? trim($parts[3], "'\"") : '';
            $oninputAttr = $oninput ? 'oninput="'.$oninput.'"' : '';

            return "<?php echo '<div class=\"input-group\"><span class=\"input-group-text\">$</span><input type=\"text\" name=\"{$name}\" id=\"{$name}\" class=\"form-control\" {$readonly} {$oninputAttr} data-inputmask=\'\"alias\": \"currency\", \"rightAlign\": false, \"groupSeparator\": \",\", \"digits\": 2, \"digitsOptional\": false, \"placeholder\": \"0\"\' data-mask value=\"' . htmlspecialchars({$value} ?? '') . '\"></div>'; ?>";
        });

        // add more here as needed
    }
}
