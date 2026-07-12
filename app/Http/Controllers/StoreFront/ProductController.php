<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->where('is_active', true)
            ->with(['images', 'variants', 'category:id,name']);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('category')) {

    $category = Category::with('children')->find($request->category);

    if ($category) {
        $categoryIds = collect([$category->id])
            ->merge($category->children->pluck('id'))
            ->toArray();

        $query->whereIn('category_id', $categoryIds);
    }
}

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        match ($request->input('sort')) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'popular' => $query->orderByDesc('is_featured')->orderByDesc('views'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('storefront.products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        abort_if(! $product->is_active, 404);

        $product->load([
            'images',
            'variants' => fn ($q) => $q->where('is_active', true),
            'category:id,name',
        ]);

        $reviews = Review::where('product_id', $product->id)
            ->approved()
            ->with('user:id,name')
            ->latest()
            ->paginate(6);

        $avgRating = round(
            Review::where('product_id', $product->id)->approved()->avg('rating') ?? 0,
            1
        );
        $reviewsCount = Review::where('product_id', $product->id)->approved()->count();

        $related = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['images', 'variants'])
            ->take(4)
            ->get();

        return view('storefront.products.show', compact(
            'product', 'reviews', 'avgRating', 'reviewsCount', 'related'
        ));
    }
}