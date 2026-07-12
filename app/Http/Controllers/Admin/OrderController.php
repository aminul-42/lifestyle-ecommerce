<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->with('user');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        // Small counters for filter badges / stat cards
        $counts = [
            'total' => Order::count(),
            'awaiting_verification' => Order::where('payment_status', 'submitted')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts'));
    }

    public function show(Order $order)
    {
        $order->load([
            'user',
            'coupon',
            'items.product',
            'items.variant',
            'paymentProofs' => fn ($q) => $q->latest(),
            'paymentProofs.verifier',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:processing,confirmed,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $order->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.',
            'status' => $order->status,
            'status_badge' => $order->statusBadgeColor(),
            'tracking_number' => $order->tracking_number,
        ]);
    }

    public function verifyPayment(Request $request, PaymentProof $paymentProof)
    {
        if ($paymentProof->status === 'verified') {
            return response()->json([
                'success' => false,
                'message' => 'This payment proof is already verified.',
            ], 422);
        }

        $paymentProof->markVerified($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Payment verified. Order marked as confirmed.',
            'payment_status' => $paymentProof->order->payment_status,
            'order_status' => $paymentProof->order->status,
        ]);
    }

    public function rejectPayment(Request $request, PaymentProof $paymentProof)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($paymentProof->status === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'This payment proof is already rejected.',
            ], 422);
        }

        $paymentProof->markRejected($request->user(), $request->reason);

        return response()->json([
            'success' => true,
            'message' => 'Payment proof rejected.',
            'payment_status' => $paymentProof->order->payment_status,
        ]);
    }
}