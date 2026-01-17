<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Wallet\Core\Models\SystemPreference;

if (!function_exists('getOption')) {
    function getOption($key)
    {
        if (Cache::has($key)) {
            return Cache::get($key);
        } else {
            $option = SystemPreference::where("slug", "=", $key)->first();
            if ($option) {
                Cache::put($key, $option->value, 3600);
                return $option->value;
            } else {
                return '';
            }
        }
    }
}

if (!function_exists('cleanPhoneNumber')) {
    function cleanPhoneNumber($phone_number)
    {
        $phone_number = "254" . substr(str_replace(" ", "", $phone_number), -9);
        return $phone_number;
    }
}