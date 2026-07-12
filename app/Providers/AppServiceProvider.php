<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.storefront', function ($view) {
            $categories = Category::where('is_active', true)
                ->whereNull('parent_id')
                ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                ->with(['children' => function ($q) {
                    $q->where('is_active', true)
                      ->withCount(['products' => fn ($q2) => $q2->where('is_active', true)])
                      ->orderBy('name');
                }])
                ->orderBy('name')
                ->take(8)
                ->get();

            $view->with('navCategories', $categories);

            $ownerQuery = auth()->check()
                ? Cart::where('user_id', auth()->id())
                : Cart::where('user_id', null)->where('cart_token', request()->cookie('cart_token'));

            $view->with('cartCount', (clone $ownerQuery)->sum('quantity'));
        });
    }
}