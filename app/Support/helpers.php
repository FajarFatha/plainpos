<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $parameter, mixed $default = null): mixed
    {
        return Setting::get($parameter, $default);
    }
}