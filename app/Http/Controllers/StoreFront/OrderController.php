<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('storefront.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        $order->load(['items', 'coupon', 'paymentProofs']);

        return view('storefront.orders.show', compact('order'));
    }


    public function count()
{
    $count = Order::where('user_id', auth()->id())
        ->whereNotIn('status', ['delivered', 'cancelled'])
        ->count();

    return response()->json(['count' => $count]);
}
}