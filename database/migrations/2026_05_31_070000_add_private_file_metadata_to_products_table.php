<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('file_size')->nullable()->after('download_filename');
            $table->string('file_mime_type')->nullable()->after('file_size');
            $table->timestamp('file_uploaded_at')->nullable()->after('file_mime_type');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'file_size',
                'file_mime_type',
                'file_uploaded_at',
            ]);
        });
    }
};
