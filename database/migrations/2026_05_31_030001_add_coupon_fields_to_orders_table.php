<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')
                ->nullable()
                ->after('product_id')
                ->constrained('coupons')
                ->nullOnDelete();
            $table->string('coupon_code', 50)->nullable()->after('payment_method');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('coupon_code');
            $table->decimal('original_price', 12, 2)->nullable()->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_id');
            $table->dropColumn(['coupon_code', 'discount_amount', 'original_price']);
        });
    }
};
