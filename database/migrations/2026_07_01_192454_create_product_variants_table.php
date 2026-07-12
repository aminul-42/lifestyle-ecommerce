<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('size')->nullable();   // e.g. S, M, L, XL, 38, 40, 42
            $table->string('color')->nullable();  // e.g. Black, Red
            $table->string('sku')->unique()->nullable();
            $table->decimal('price', 10, 2)->nullable();          // overrides product price if set
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['product_id', 'size', 'color'], 'unique_variant_combo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};