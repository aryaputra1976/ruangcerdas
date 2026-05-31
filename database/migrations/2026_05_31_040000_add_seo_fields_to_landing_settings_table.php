<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->string('seo_title')->nullable()->after('footer_short_text');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('seo_keywords', 255)->nullable()->after('seo_description');
            $table->string('og_image_url', 255)->nullable()->after('seo_keywords');
        });
    }

    public function down(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'seo_description', 'seo_keywords', 'og_image_url']);
        });
    }
};
