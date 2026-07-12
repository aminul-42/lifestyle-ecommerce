<?php
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

// Admin login — NOT behind the 'admin' middleware (avoids redirect loop)
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Everything else in the admin panel — protected
Route::prefix('admin')->name('admin.')->middleware(['web', 'admin'])->group(function () {

  Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

  // Categories

  Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
  Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
  Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
  Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
  Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
  Route::patch('categories/{category}/toggle', [CategoryController::class, 'toggleStatus'])->name('categories.toggle');

  // Profile / Change password
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::put('/profile/info', [ProfileController::class, 'updateInfo'])->name('profile.info');
  Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');



  ///Products
  Route::get('products', [ProductController::class, 'index'])->name('products.index');
  Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
  Route::post('products', [ProductController::class, 'store'])->name('products.store');
  Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
  Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
  Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
  Route::patch('products/{product}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');
  Route::delete('product-images/{image}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');

  // Orders
  Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
  Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
  Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

  // Payment verification (acts on payment_proofs, not orders directly)
  Route::patch('payment-proofs/{paymentProof}/verify', [OrderController::class, 'verifyPayment'])->name('orders.payment.verify');
  Route::patch('payment-proofs/{paymentProof}/reject', [OrderController::class, 'rejectPayment'])->name('orders.payment.reject');
  

  // Coupons
Route::get('coupons', [CouponController::class, 'index'])->name('coupons.index');
Route::post('coupons', [CouponController::class, 'store'])->name('coupons.store');
Route::get('coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('coupons.edit');
Route::put('coupons/{coupon}', [CouponController::class, 'update'])->name('coupons.update');
Route::delete('coupons/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy');
Route::patch('coupons/{coupon}/toggle', [CouponController::class, 'toggleStatus'])->name('coupons.toggle');


// Reviews
Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::patch('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
Route::patch('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');


// Customers
Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
Route::patch('customers/{customer}/toggle', [CustomerController::class, 'toggleStatus'])->name('customers.toggle');
Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');


// Store Settings
Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

});