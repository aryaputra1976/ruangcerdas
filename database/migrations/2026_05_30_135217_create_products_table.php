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

            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('benefits')->nullable();
            $table->longText('contents')->nullable();

            $table->unsignedInteger('normal_price')->default(0);
            $table->unsignedInteger('sale_price')->nullable();
            $table->unsignedInteger('first_buyer_price')->nullable();
            $table->unsignedInteger('first_buyer_quota')->nullable();

            $table->string('cover_image')->nullable();
            $table->string('digital_file_path')->nullable();
            $table->string('download_filename')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('is_featured');
            $table->index('is_active');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};