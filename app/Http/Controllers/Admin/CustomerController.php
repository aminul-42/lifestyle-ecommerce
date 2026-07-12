<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->where('role', 'customer')
            ->withCount('orders')
            ->withSum(['orders as total_spent' => function ($q) {
                $q->where('payment_status', 'verified');
            }], 'total');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'    => User::where('role', 'customer')->count(),
            'active'   => User::where('role', 'customer')->where('is_active', true)->count(),
            'inactive' => User::where('role', 'customer')->where('is_active', false)->count(),
            'new_this_month' => User::where('role', 'customer')
                                     ->whereMonth('created_at', now()->month)
                                     ->whereYear('created_at', now()->year)
                                     ->count(),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    public function show(User $customer)
    {
        abort_if($customer->role !== 'customer', 404);

        $customer->load([
            'orders' => fn ($q) => $q->latest(),
            'addresses',
            'reviews.product:id,name',
        ]);

        $stats = [
            'orders_count' => $customer->orders->count(),
            'total_spent'  => $customer->orders->where('payment_status', 'verified')->sum('total'),
            'reviews_count' => $customer->reviews->count(),
        ];

        return view('admin.customers.show', compact('customer', 'stats'));
    }

    public function toggleStatus(User $customer)
    {
        abort_if($customer->role !== 'customer', 404);

        $customer->update(['is_active' => ! $customer->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $customer->is_active,
            'message'   => 'Customer status updated.',
        ]);
    }


    public function destroy(User $customer)
{
    abort_if($customer->role !== 'customer', 404);

    if ($customer->orders()->exists()) {
        return response()->json([
            'success' => false,
            'message' => 'This customer has order history and cannot be deleted. Deactivate the account instead.',
        ], 422);
    }

    $customer->delete();

    return response()->json([
        'success' => true,
        'message' => 'Customer deleted successfully.',
    ]);
}
}