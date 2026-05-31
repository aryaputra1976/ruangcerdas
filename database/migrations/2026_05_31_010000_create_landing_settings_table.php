<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_badge')->nullable();
            $table->string('primary_cta_text')->nullable();
            $table->string('primary_cta_url')->nullable();
            $table->string('secondary_cta_text')->nullable();
            $table->string('secondary_cta_url')->nullable();
            $table->string('support_title')->nullable();
            $table->text('support_text')->nullable();
            $table->string('support_whatsapp', 30)->nullable();
            $table->string('featured_section_title')->nullable();
            $table->text('featured_section_subtitle')->nullable();
            $table->string('footer_short_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_settings');
    }
};
