<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->with(['children' => fn ($q) => $q->where('is_active', true)
                ->withCount(['products' => fn ($q2) => $q2->where('is_active', true)])])
            ->take(6)
            ->get()
            ->map(function ($cat) {
                $cat->total_count = $cat->products_count + $cat->children->sum('products_count');
                return $cat;
            })
            ->sortByDesc('total_count')
            ->values();

        $featuredProducts = Product::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with(['images', 'variants'])
            ->latest()
            ->take(8)
            ->get();

        $avgRating = round(Review::approved()->avg('rating') ?? 0, 1);
        $reviewsCount = Review::approved()->count();
        $totalProducts = Product::where('is_active', true)->count();

        return view('storefront.home', compact('categories', 'featuredProducts', 'avgRating', 'reviewsCount', 'totalProducts'));
    }
}