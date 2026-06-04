<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('author_name');
            $table->string('title')->nullable();
            $table->text('body');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->boolean('is_visible')->default(true);
            $table->date('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'is_visible']);
            $table->index('reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
