<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'parameter',
        'value',
        'keterangan',
    ];
    
    private const CACHE_PREFIX = 'setting:';
    
    private const CACHE_TTL = 3600;
    
    public static function get(string $parameter, mixed $default = null): mixed
    {
        return Cache::remember(self::CACHE_PREFIX.$parameter, self::CACHE_TTL, function () use ($parameter, $default) {
            $setting = self::where('parameter', $parameter)->first();

            return $setting?->value ?? $default;
        });
    }
    
    public static function set(string $parameter, mixed $value, ?string $keterangan = null): self
    {
        $existing = self::where('parameter', $parameter)->first();

        $setting = self::updateOrCreate(
            ['parameter' => $parameter],
            [
                'value' => (string) $value,
                'keterangan' => $keterangan ?? $existing?->keterangan,
            ]
        );

        Cache::put(self::CACHE_PREFIX.$parameter, $setting->value, self::CACHE_TTL);

        return $setting;
    }
    
    public static function getInt(string $parameter, ?int $default = null): ?int
    {
        $value = self::get($parameter);

        return $value !== null ? (int) $value : $default;
    }
    
    public static function getBool(string $parameter, bool $default = false): bool
    {
        $value = self::get($parameter);

        if ($value === null) {
            return $default;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes'], true);
    }
    
    public static function forget(string $parameter): void
    {
        Cache::forget(self::CACHE_PREFIX.$parameter);
    }
}