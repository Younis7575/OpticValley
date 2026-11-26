<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type', 10)->default('percent'); // percent | fixed
            $table->decimal('value', 12, 2);
            $table->decimal('min_order', 12, 2)->default(0);
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('pending'); // pending, confirmed, processing, shipped, delivered, cancelled, refunded
            $table->string('payment_method', 30)->default('cod');
            $table->string('payment_status', 20)->default('unpaid'); // unpaid, paid, refunded, partially_refunded
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('shipping', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->decimal('refunded_amount', 12, 2)->default(0);
            $table->string('coupon_code')->nullable();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 30);
            $table->string('shipping_line1');
            $table->string('shipping_line2')->nullable();
            $table->string('shipping_city', 80);
            $table->string('shipping_province', 80)->nullable();
            $table->string('shipping_postal_code', 20)->nullable();
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('courier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
            $table->index('customer_phone');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('variant_name')->nullable();
            $table->string('lens_name')->nullable();
            $table->decimal('lens_price', 12, 2)->default(0);
            $table->json('prescription')->nullable();
            $table->string('prescription_file')->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20);
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('position', 20)->default('hero'); // hero, promo, strip
            $table->string('eyebrow')->nullable();
            $table->string('title');
            $table->string('subtitle', 500)->nullable();
            $table->string('image')->nullable();
            $table->string('button_text')->nullable();
            $table->string('link')->nullable();
            $table->string('theme', 20)->default('light');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('body');
            $table->string('image')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index('published_at');
        });

        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('location', 30); // header, footer_shop, footer_help
            $table->string('label');
            $table->string('url');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 60);
            $table->nullableMorphs('subject');
            $table->string('description')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['contact_messages', 'activity_logs', 'menu_items', 'settings', 'subscribers', 'posts', 'pages', 'banners', 'order_status_histories', 'order_items', 'orders', 'coupons'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
