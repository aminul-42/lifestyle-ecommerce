<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label'];

    protected static function booted(): void
    {
        // Any time a setting is saved or deleted, clear the cache
        // so the storefront/admin instantly reflect the change.
        static::saved(fn () => Cache::forget('app_settings'));
        static::deleted(fn () => Cache::forget('app_settings'));
    }

    /**
     * Get all settings as a flat cached key => value array.
     */
    public static function allCached(): array
    {
        return Cache::rememberForever('app_settings', function () {
            return static::all()->pluck('value', 'key')->toArray();
        });
    }
}