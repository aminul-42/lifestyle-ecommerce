<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /** Max quantity allowed per cart line, as a sane ceiling regardless of stock. */
    private const MAX_QTY_PER_LINE = 20;

    private const CART_TOKEN_COOKIE = 'cart_token';
    private const CART_TOKEN_DAYS = 60;

    public function index(Request $request)
    {
        $cartItems = $this->ownedCartQuery()
            ->with(['product.images', 'variant'])
            ->get();

        // Prune lines whose product (or variant's product) has been deleted
        $orphaned = $cartItems->filter(fn($item) => $item->product === null);
        if ($orphaned->isNotEmpty()) {
            Cart::whereIn('id', $orphaned->pluck('id'))->delete();
            $cartItems = $cartItems->diff($orphaned);
            session()->flash('cart_notice', 'Some items in your cart are no longer available and were removed.');
        }

        $coupon = null;
        $discount = 0;
        $subtotal = $cartItems->sum(fn($item) => $item->line_total);

        if ($couponCode = session('cart_coupon_code')) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                // Coupon became invalid (expired/deactivated) since it was applied — clear it silently.
                session()->forget('cart_coupon_code');
                $coupon = null;
            }
        }

        $total = max($subtotal - $discount, 0);

        return view('storefront.cart.index', compact('cartItems', 'coupon', 'discount', 'subtotal', 'total'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $variant = $data['product_variant_id'] ?? null
            ? ProductVariant::findOrFail($data['product_variant_id'])
            : null;

        if (!$product->is_active) {
            return response()->json(['success' => false, 'message' => 'This product is no longer available.'], 422);
        }

        $availableStock = $variant ? $variant->stock : $product->stock;
        if ($availableStock <= 0) {
            return response()->json(['success' => false, 'message' => 'This item is out of stock.'], 422);
        }

        $requestedQty = $data['quantity'] ?? 1;
        $ownerConditions = $this->ownerConditions();

        $existing = Cart::where($ownerConditions)
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variant?->id)
            ->first();

        $newQty = ($existing?->quantity ?? 0) + $requestedQty;
        $newQty = min($newQty, $availableStock, self::MAX_QTY_PER_LINE);

        if ($existing) {
            $existing->update(['quantity' => $newQty]);
        } else {
            Cart::create(array_merge($ownerConditions, [
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity' => $newQty,
            ]));
        }

        return response()->json([
            'success' => true,
            'message' => 'Added to cart.',
            'cart_count' => $this->ownedCartQuery()->sum('quantity'),
        ]);
    }

    public function update(Request $request, $id)
    {
        // 1. Fetch via ID manually instead of breaking implicit binding
        $cart = Cart::findOrFail($id);

        // 2. Validate session boundary permissions
        $this->authorizeCartLine($cart);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $availableStock = $cart->variant ? $cart->variant->stock : $cart->product->stock;
        $newQty = min($data['quantity'], $availableStock, self::MAX_QTY_PER_LINE);

        $cart->update(['quantity' => $newQty]);

        return response()->json([
            'success' => true,
            'quantity' => $newQty,
            'line_total' => $cart->fresh()->line_total,
            'capped' => $newQty < $data['quantity'],
        ]);
    }

    public function destroy($id)
    {
        // 1. Fetch via ID manually
        $cart = Cart::findOrFail($id);

        // 2. Validate session boundary permissions
        $this->authorizeCartLine($cart);

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart.',
            'cart_count' => $this->ownedCartQuery()->sum('quantity'),
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => ['required', 'string']]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'That coupon code doesn\'t exist.'], 422);
        }

        $subtotal = $this->ownedCartQuery()->with(['product', 'variant'])->get()
            ->sum(fn($item) => $item->line_total);

        if (!$coupon->isValid($subtotal)) {
            return response()->json(['success' => false, 'message' => 'This coupon isn\'t valid for your current order.'], 422);
        }

        session(['cart_coupon_code' => $coupon->code]);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied!',
            'discount' => $coupon->calculateDiscount($subtotal),
        ]);
    }

    public function removeCoupon()
    {
        session()->forget('cart_coupon_code');

        return response()->json(['success' => true, 'message' => 'Coupon removed.']);
    }

    public function count()
    {
        return response()->json(['count' => $this->ownedCartQuery()->sum('quantity')]);
    }

    /**
     * Ensures the given cart line actually belongs to the current visitor
     * (logged-in user or guest cart_token) before allowing it to be modified.
     */
    private function authorizeCartLine(Cart $cart): void
    {
        if (auth()->check()) {
            abort_if($cart->user_id !== auth()->id(), 403, 'This cart item does not belong to you.');
            return;
        }

        $token = request()->cookie(self::CART_TOKEN_COOKIE);
        abort_if($cart->user_id !== null || $cart->cart_token !== $token, 403, 'This cart item does not belong to you.');
    }

    /** Base query scoped to the current visitor's cart lines. */
    private function ownedCartQuery()
    {
        return Cart::where($this->ownerConditions());
    }

    /**
     * Resolves the owner-matching conditions for the current visitor:
     * logged-in users match by user_id; guests match by cart_token cookie.
     * Generates and queues a new cart_token cookie for first-time guests.
     */
    private function ownerConditions(): array
    {
        if (auth()->check()) {
            return ['user_id' => auth()->id(), 'cart_token' => null];
        }

        $token = request()->cookie(self::CART_TOKEN_COOKIE);

        // If the browser didn't send a token, check if we queued one in this exact execution thread
        if (!$token) {
            $token = Cookie::get(self::CART_TOKEN_COOKIE) ?? (string) Str::uuid();
            Cookie::queue(self::CART_TOKEN_COOKIE, $token, 60 * 24 * self::CART_TOKEN_DAYS);

            // Inline overwrite to ensure current request lifecycle can read it instantly
            request()->cookies->set(self::CART_TOKEN_COOKIE, $token);
        }

        return ['user_id' => null, 'cart_token' => $token];
    }
}