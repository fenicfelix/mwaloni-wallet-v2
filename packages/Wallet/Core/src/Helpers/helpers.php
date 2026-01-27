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

if (!function_exists('cleanAccountName')) {
    function cleanAccountName($accountName)
    {
        return str_replace(['\'', '"', ',', ';', '<', '>', '(', ')', '-'], ' ', $accountName);
    }
}

if (!function_exists('getBalance')) {
    function getBalance($str, $type): float
    {
        // preg_match('/' . $type . '=([\d\.]+)/', $str, $matches);
        // $basicAmount = $matches[1] ?? 0;
        // return (float) $basicAmount;
        $array = explode("&", $str);
        $balance = 0;
        for ($i = 0; $i < sizeof($array); $i++) {
            $item_array = explode("|", $array[$i]);
            for ($j = 0; $j < sizeof($item_array); $j++) {
                if ($item_array[0] == $type && $j == 2) {
                    $balance = $item_array[$j];
                    break;
                }
            }
        }
        return $balance;
    }
}

if (!function_exists('getElapsedTime')) {
    function getElapsedTime($time)
    {
        $time = strtotime($time);
        $time_now = strtotime(date('Y-m-d H:i:s'));
        $minutes = round(($time_now - $time) / 60);

        return $minutes;
    }
}
