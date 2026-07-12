<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = $this->cartItemsQuery()->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $cartItems->sum(fn ($item) => $item->line_total);
        [$coupon, $discount] = $this->resolveCoupon($subtotal);

        $shippingFeeAmount = (float) setting('shipping_fee', 0);
        $hasPaidShipping = $shippingFeeAmount > 0;

        // Default view total assumes free shipping until the customer picks otherwise client-side
        $total = max($subtotal - $discount, 0);

        $addresses = Address::where('user_id', auth()->id())
            ->orderByDesc('is_default')->latest()->get();

        $paymentMethods = $this->availablePaymentMethods();

        return view('storefront.checkout.index', compact(
            'cartItems', 'subtotal', 'coupon', 'discount',
            'shippingFeeAmount', 'hasPaidShipping', 'total', 'addresses', 'paymentMethods'
        ));
    }

    public function store(Request $request)
    {
        $paymentMethods = $this->availablePaymentMethods();

        $rules = [
            'address_id' => ['nullable', 'exists:addresses,id'],
            'full_name' => ['nullable', 'required_without:address_id', 'string', 'max:255'],
            'phone' => ['nullable', 'required_without:address_id', 'string', 'max:30'],
            'address_line' => ['nullable', 'required_without:address_id', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_option' => ['nullable', 'in:free,paid'],
            'save_address' => ['nullable', 'boolean'],
            'order_note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:'.implode(',', array_keys($paymentMethods))],
        ];

        if ($request->payment_method !== 'cod') {
            $rules['transaction_id'] = ['required', 'string', 'max:100'];
            $rules['sender_number'] = ['nullable', 'string', 'max:30'];
            $rules['screenshot'] = ['nullable', 'image', 'max:2048'];
        }

        $data = $request->validate($rules);

        $cartItems = $this->cartItemsQuery()->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
        }

        try {
            $order = DB::transaction(function () use ($request, $data, $cartItems) {
                if (! empty($data['address_id'])) {
                    $address = Address::where('user_id', auth()->id())->findOrFail($data['address_id']);
                } else {
                    $address = Address::create([
                        'user_id' => auth()->id(),
                        'full_name' => $data['full_name'],
                        'phone' => $data['phone'],
                        'address_line' => $data['address_line'],
                        'city' => $data['city'] ?? null,
                        'area' => $data['area'] ?? null,
                        'postal_code' => $data['postal_code'] ?? null,
                        'is_default' => Address::where('user_id', auth()->id())->doesntExist(),
                    ]);
                }

                $adjustedItems = [];
                $subtotal = 0;

                foreach ($cartItems as $item) {
                    $availableStock = $item->variant ? $item->variant->stock : $item->product->stock;
                    $qty = min($item->quantity, $availableStock);
                    if ($qty <= 0) continue;

                    $lineTotal = $item->unit_price * $qty;
                    $subtotal += $lineTotal;

                    $adjustedItems[] = [
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'product_name' => $item->product->name,
                        'size' => $item->variant->size ?? null,
                        'color' => $item->variant->color ?? null,
                        'image' => $item->product->images->first()->path ?? null,
                        'price' => $item->unit_price,
                        'quantity' => $qty,
                        'line_total' => $lineTotal,
                    ];
                }

                if (empty($adjustedItems)) {
                    throw ValidationException::withMessages(['cart' => 'All items in your cart are currently out of stock.']);
                }

                [$coupon, $discount] = $this->resolveCoupon($subtotal);

                // Shipping is always recomputed server-side — client dropdown is cosmetic only
                $shippingFeeAmount = (float) setting('shipping_fee', 0);
                $shippingOption = $data['shipping_option'] ?? 'free';
                $shippingFee = ($shippingFeeAmount > 0 && $shippingOption === 'paid') ? $shippingFeeAmount : 0;

                $total = max($subtotal - $discount + $shippingFee, 0);

                $order = Order::create([
                    'user_id' => auth()->id(),
                    'coupon_id' => $coupon?->id,
                    'full_name' => $address->full_name,
                    'phone' => $address->phone,
                    'address_line' => $address->address_line,
                    'city' => $address->city,
                    'area' => $address->area,
                    'postal_code' => $address->postal_code,
                    'order_note' => $data['order_note'] ?? null,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'shipping_fee' => $shippingFee,
                    'total' => $total,
                    'payment_method' => $data['payment_method'],
                    'payment_status' => 'pending',
                ]);

                foreach ($adjustedItems as $itemData) {
                    $order->items()->create($itemData);

                    if ($itemData['product_variant_id']) {
                        ProductVariant::where('id', $itemData['product_variant_id'])->decrement('stock', $itemData['quantity']);
                    } else {
                        Product::where('id', $itemData['product_id'])->decrement('stock', $itemData['quantity']);
                    }
                }

                if ($coupon) {
                    $coupon->increment('used_count');
                }

                if ($data['payment_method'] !== 'cod') {
                    $screenshotPath = $request->hasFile('screenshot')
                        ? $request->file('screenshot')->store('payment_proofs', 'public')
                        : null;

                    PaymentProof::create([
                        'order_id' => $order->id,
                        'method' => $data['payment_method'],
                        'sender_number' => $data['sender_number'] ?? null,
                        'transaction_id' => $data['transaction_id'],
                        'screenshot' => $screenshotPath,
                        'status' => 'pending',
                    ]);
                }

                Cart::where('user_id', auth()->id())->delete();
                session()->forget('cart_coupon_code');

                return $order;
            });
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully!',
            'redirect' => route('checkout.confirmation', $order->order_number),
        ]);
    }

    public function confirmation(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        $order->load(['items', 'coupon', 'paymentProofs']);
        return view('storefront.checkout.confirmation', compact('order'));
    }

    private function cartItemsQuery()
    {
        return Cart::where('user_id', auth()->id())->with(['product.images', 'variant']);
    }

    private function resolveCoupon(float $subtotal): array
    {
        if (! $code = session('cart_coupon_code')) return [null, 0];

        $coupon = Coupon::where('code', $code)->first();
        if ($coupon && $coupon->isValid($subtotal)) {
            return [$coupon, $coupon->calculateDiscount($subtotal)];
        }

        session()->forget('cart_coupon_code');
        return [null, 0];
    }

    private function availablePaymentMethods(): array
    {
        $methods = [];
        if (setting('bkash_number')) $methods['bkash'] = ['label' => 'bKash', 'number' => setting('bkash_number')];
        if (setting('nagad_number')) $methods['nagad'] = ['label' => 'Nagad', 'number' => setting('nagad_number')];
        if (setting('bank_details')) $methods['bank'] = ['label' => 'Bank Transfer', 'details' => setting('bank_details')];
        if (setting('cod_enabled') == '1') $methods['cod'] = ['label' => 'Cash on Delivery'];
        return $methods;
    }
}