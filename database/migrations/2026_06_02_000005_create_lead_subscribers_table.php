<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_magnet_id')->nullable()->constrained('lead_magnets')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('whatsapp')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->dateTime('downloaded_at')->nullable();
            $table->timestamps();

            $table->index(['lead_magnet_id', 'downloaded_at']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_subscribers');
    }
};
