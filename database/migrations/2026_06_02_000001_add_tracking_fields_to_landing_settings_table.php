<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->string('meta_pixel_id', 100)->nullable()->after('og_image_url');
            $table->string('google_analytics_id', 100)->nullable()->after('meta_pixel_id');
            $table->string('google_tag_manager_id', 100)->nullable()->after('google_analytics_id');
            $table->string('whatsapp_cta_text', 100)->nullable()->after('google_tag_manager_id');
            $table->text('whatsapp_default_message')->nullable()->after('whatsapp_cta_text');
        });
    }

    public function down(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->dropColumn([
                'meta_pixel_id',
                'google_analytics_id',
                'google_tag_manager_id',
                'whatsapp_cta_text',
                'whatsapp_default_message',
            ]);
        });
    }
};

