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

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('invoice_number')->unique();

            $table->string('buyer_name');
            $table->string('buyer_email');
            $table->string('buyer_whatsapp');

            $table->unsignedInteger('price')->default(0);

            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->timestamp('payment_uploaded_at')->nullable();
            $table->text('payment_note')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->string('download_token')->nullable();
            $table->timestamp('download_expires_at')->nullable();
            $table->unsignedInteger('download_count')->default(0);

            $table->text('admin_notes')->nullable();

            $table->timestamps();

            $table->index('product_id');
            $table->index('buyer_email');
            $table->index('buyer_whatsapp');
            $table->index('status');
            $table->index('paid_at');
            $table->index('approved_at');
            $table->index('download_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};