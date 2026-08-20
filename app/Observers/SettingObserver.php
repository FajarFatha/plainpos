<?php

namespace App\Observers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingObserver
{
    private const CACHE_PREFIX = 'setting:';

    public function saved(Setting $setting): void
    {
        Cache::put(self::CACHE_PREFIX.$setting->parameter, $setting->value, 3600);
    }

    public function deleted(Setting $setting): void
    {
        Cache::forget(self::CACHE_PREFIX.$setting->parameter);
    }
}