<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_creatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('template_key', 100);
            $table->string('title');
            $table->string('headline', 255);
            $table->text('body');
            $table->json('bullets')->nullable();
            $table->string('cta_text', 100);
            $table->string('brand_text', 100);
            $table->string('image_path');
            $table->string('format', 20)->default('png');
            $table->unsignedInteger('width')->default(1080);
            $table->unsignedInteger('height')->default(1920);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_creatives');
    }
};
