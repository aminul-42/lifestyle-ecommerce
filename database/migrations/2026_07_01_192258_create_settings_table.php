<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();       // e.g. site_name, site_logo, primary_color
            $table->longText('value')->nullable();  // stored value (text, path, json string, "1"/"0")
            $table->string('type')->default('text'); // text | textarea | image | boolean | json
            $table->string('group')->default('general'); // general | branding | payment | social | seo
            $table->string('label')->nullable();     // human readable label for admin form
            $table->timestamps();
        });

        // Seed sensible defaults so the store works out of the box
        $defaults = [
            // Branding
            ['key' => 'site_name', 'value' => 'My Lifestyle Store', 'type' => 'text', 'group' => 'branding', 'label' => 'Store Name'],
            ['key' => 'site_tagline', 'value' => 'Fashion for everyone', 'type' => 'text', 'group' => 'branding', 'label' => 'Tagline'],
            ['key' => 'site_logo', 'value' => null, 'type' => 'image', 'group' => 'branding', 'label' => 'Logo'],
            ['key' => 'site_favicon', 'value' => null, 'type' => 'image', 'group' => 'branding', 'label' => 'Favicon'],
            ['key' => 'primary_color', 'value' => '#111111', 'type' => 'text', 'group' => 'branding', 'label' => 'Primary Color'],
            ['key' => 'accent_color', 'value' => '#c9a227', 'type' => 'text', 'group' => 'branding', 'label' => 'Accent Color'],

            // General
            ['key' => 'currency_symbol', 'value' => '৳', 'type' => 'text', 'group' => 'general', 'label' => 'Currency Symbol'],
            ['key' => 'currency_code', 'value' => 'BDT', 'type' => 'text', 'group' => 'general', 'label' => 'Currency Code'],
            ['key' => 'contact_phone', 'value' => '', 'type' => 'text', 'group' => 'general', 'label' => 'Contact Phone'],
            ['key' => 'contact_email', 'value' => '', 'type' => 'text', 'group' => 'general', 'label' => 'Contact Email'],
            ['key' => 'contact_address', 'value' => '', 'type' => 'textarea', 'group' => 'general', 'label' => 'Store Address'],

            // Social
            ['key' => 'facebook_url', 'value' => '', 'type' => 'text', 'group' => 'social', 'label' => 'Facebook URL'],
            ['key' => 'instagram_url', 'value' => '', 'type' => 'text', 'group' => 'social', 'label' => 'Instagram URL'],
            ['key' => 'whatsapp_number', 'value' => '', 'type' => 'text', 'group' => 'social', 'label' => 'WhatsApp Number'],

            // Payment (manual verification)
            ['key' => 'payment_instructions', 'value' => 'Send payment to the number below and enter the Transaction ID at checkout.', 'type' => 'textarea', 'group' => 'payment', 'label' => 'Payment Instructions'],
            ['key' => 'bkash_number', 'value' => '', 'type' => 'text', 'group' => 'payment', 'label' => 'bKash Number'],
            ['key' => 'nagad_number', 'value' => '', 'type' => 'text', 'group' => 'payment', 'label' => 'Nagad Number'],
            ['key' => 'bank_details', 'value' => '', 'type' => 'textarea', 'group' => 'payment', 'label' => 'Bank Account Details'],
            ['key' => 'cod_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'payment', 'label' => 'Enable Cash on Delivery'],

            // SEO
            ['key' => 'meta_title', 'value' => 'My Lifestyle Store', 'type' => 'text', 'group' => 'seo', 'label' => 'Default Meta Title'],
            ['key' => 'meta_description', 'value' => '', 'type' => 'textarea', 'group' => 'seo', 'label' => 'Default Meta Description'],
        ];

        $now = now();
        foreach ($defaults as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }

        \Illuminate\Support\Facades\DB::table('settings')->insert($defaults);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};