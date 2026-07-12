<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->string('avatar')->nullable()->after('google_id');
            $table->string('phone')->nullable()->after('avatar');
            $table->enum('role', ['customer', 'admin', 'staff'])->default('customer')->after('phone');
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('password')->nullable()->change(); // google users have no password
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'avatar', 'phone', 'role', 'is_active']);
        });
    }
};