<?php
use App\Http\Controllers\Storefront\OrderController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');



// Storefront — product browsing
Route::get('/shop', [ProductController::class, 'index'])->name('shop.index');
Route::get('/shop/{product:slug}', [ProductController::class, 'show'])->name('shop.show');



//Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::post('/cart/{id}/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/{id}/delete', [CartController::class, 'destroy'])->name('cart.destroy');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');


//Checkout an Orders


Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/confirmation/{order:order_number}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order:order_number}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/count', [OrderController::class, 'count'])->name('orders.count');
});


// Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Logout
Route::post('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');



Route::get('/login', function () {
    return view('auth.login');
})->name('login');