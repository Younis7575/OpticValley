<?php

use App\Models\Setting;

if (! function_exists('money')) {
    function money($amount): string
    {
        $symbol = Setting::get('currency_symbol', 'Rs.');

        return $symbol.' '.number_format((float) $amount, 0);
    }
}

if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}
