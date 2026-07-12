<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // e.g. ORD-2026-000123
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();

            // Shipping snapshot (kept even if address is later edited/deleted)
            $table->string('full_name');
            $table->string('phone');
            $table->string('address_line');
            $table->string('city')->nullable();
            $table->string('area')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('order_note')->nullable();

            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Payment
            $table->enum('payment_method', ['bkash', 'nagad', 'bank', 'cod'])->default('cod');
            $table->enum('payment_status', ['pending', 'submitted', 'verified', 'rejected'])->default('pending');
            // pending   = COD or not yet submitted proof
            // submitted = customer sent transaction ID, awaiting admin check
            // verified  = admin confirmed payment received
            // rejected  = admin rejected the transaction ID/proof

            // Order fulfillment status
            $table->enum('status', ['processing', 'confirmed', 'shipped', 'delivered', 'cancelled'])->default('processing');
            $table->string('tracking_number')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};