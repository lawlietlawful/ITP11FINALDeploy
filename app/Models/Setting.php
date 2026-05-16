<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    /**
     * Get a setting value, pulling from RAM cache if available.
     */
    public static function getCached($key, $default = null)
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('setting_' . $key, function () use ($key, $default) {
            return self::where('key', $key)->value('value') ?? $default;
        });
    }

    /**
     * Clear the cache for a specific key when updated.
     */
    protected static function booted()
    {
        static::saved(function ($setting) {
            \Illuminate\Support\Facades\Cache::forget('setting_' . $setting->key);
        });

        static::deleted(function ($setting) {
            \Illuminate\Support\Facades\Cache::forget('setting_' . $setting->key);
        });
    }
}
