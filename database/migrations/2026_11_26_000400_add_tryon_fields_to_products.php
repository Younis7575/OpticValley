<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('tryon_enabled')->default(false)->after('prescription_enabled');
            $table->string('tryon_overlay')->nullable()->after('tryon_enabled');
            $table->decimal('tryon_scale', 4, 2)->default(1.00)->after('tryon_overlay');
            $table->decimal('tryon_offset_x', 4, 2)->default(0)->after('tryon_scale');
            $table->decimal('tryon_offset_y', 4, 2)->default(0)->after('tryon_offset_x');
            $table->index('tryon_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['tryon_enabled']);
            $table->dropColumn(['tryon_enabled', 'tryon_overlay', 'tryon_scale', 'tryon_offset_x', 'tryon_offset_y']);
        });
    }
};
