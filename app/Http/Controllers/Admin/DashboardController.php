<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_payments' => Order::where('payment_status', 'submitted')->count(),
            'total_products' => Product::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_revenue' => Order::where('payment_status', 'verified')->sum('total'),
        ];

        $recentOrders = Order::with('user')->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}