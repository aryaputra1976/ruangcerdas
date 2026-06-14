<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('question_categories', function (Blueprint $table) {
            $table->string('position_target', 100)->nullable()->after('tryout_type');
            $table->index(['tryout_type', 'position_target'], 'qc_tryout_type_position_target_index');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->string('position_target', 100)->nullable()->after('tryout_type');
            $table->index(['tryout_type', 'position_target'], 'questions_tryout_type_position_target_index');
        });

        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->string('position_target', 100)->nullable()->after('tryout_type');
            $table->index(['tryout_type', 'position_target'], 'tp_tryout_type_position_target_index');
        });
    }

    public function down(): void
    {
        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->dropIndex('tp_tryout_type_position_target_index');
            $table->dropColumn('position_target');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('questions_tryout_type_position_target_index');
            $table->dropColumn('position_target');
        });

        Schema::table('question_categories', function (Blueprint $table) {
            $table->dropIndex('qc_tryout_type_position_target_index');
            $table->dropColumn('position_target');
        });
    }
};
