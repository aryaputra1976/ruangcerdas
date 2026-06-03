<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->boolean('is_free')->default(false)->after('price');
            $table->unsignedInteger('access_days')->nullable()->default(7)->after('tkp_count');
            $table->unsignedInteger('attempt_limit')->nullable()->default(1)->after('access_days');
            $table->boolean('has_explanation')->default(false)->after('attempt_limit');

            $table->index(['is_active', 'is_free']);
        });

        Schema::create('tryout_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tryout_package_id')->constrained('tryout_packages')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('buyer_email')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('remaining_attempts')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tryout_package_id', 'buyer_email']);
            $table->index(['is_active', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tryout_accesses');

        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'is_free']);
            $table->dropColumn([
                'is_free',
                'access_days',
                'attempt_limit',
                'has_explanation',
            ]);
        });
    }
};
