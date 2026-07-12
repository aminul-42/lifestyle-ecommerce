<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

if (! function_exists('setting')) {
    /**
     * Get a setting value by key, with an optional fallback default.
     *
     * Usage:
     *   setting('site_name')
     *   setting('primary_color', '#111111')
     */
    function setting(string $key, mixed $default = null): mixed
    {
        $all = Setting::allCached();

        return $all[$key] ?? $default;
    }
}

if (! function_exists('setting_image')) {
    /**
     * Get the public URL for an image-type setting (logo, favicon, etc).
     * Falls back to a placeholder if not set.
     */
    function setting_image(string $key, ?string $fallback = null): string
    {
        $path = setting($key);

        if (! $path) {
            return $fallback ?? asset('images/placeholder.png');
        }

        // Stored paths are relative to storage/app/public
        return Storage::disk('public')->url($path);
    }
}

if (! function_exists('money')) {
    /**
     * Format a numeric amount using the store's configured currency symbol.
     * Usage: money($product->price)  => "৳ 1,250.00"
     */
    function money(float|int|null $amount): string
    {
        $symbol = setting('currency_symbol', '৳');

        return $symbol.' '.number_format((float) $amount, 2);
    }
}