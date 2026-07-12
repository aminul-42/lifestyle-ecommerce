<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique()->nullable();
            $table->enum('gender', ['men', 'women', 'kids', 'unisex'])->default('unisex')->index();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();

            // Base price/stock used when the product has NO variants.
            // If variants exist, price/stock come from product_variants instead.
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('has_variants')->default(false);

            $table->decimal('weight', 8, 2)->nullable(); // for shipping calc later
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'gender']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};