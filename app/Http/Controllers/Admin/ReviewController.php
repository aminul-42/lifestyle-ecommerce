<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['product:id,name', 'user:id,name,email', 'order:id,order_number']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'    => Review::count(),
            'approved' => Review::where('is_approved', true)->count(),
            'pending'  => Review::where('is_approved', false)->count(),
            'avg_rating' => round(Review::avg('rating') ?? 0, 1),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Review approved and now visible on the storefront.',
        ]);
    }

    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Review unapproved and hidden from the storefront.',
        ]);
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted.',
        ]);
    }
}