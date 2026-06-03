<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tryout_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedInteger('duration_minutes')->default(100);
            $table->unsignedInteger('twk_count')->default(30);
            $table->unsignedInteger('tiu_count')->default(35);
            $table->unsignedInteger('tkp_count')->default(45);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });

        Schema::create('question_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('section', ['TWK', 'TIU', 'TKP']);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['section', 'is_active']);
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_category_id')->nullable()->constrained('question_categories')->nullOnDelete();
            $table->enum('section', ['TWK', 'TIU', 'TKP']);
            $table->longText('question_text');
            $table->longText('explanation')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['section', 'difficulty', 'is_active']);
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->char('option_label', 1);
            $table->longText('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('score')->default(0);
            $table->timestamps();

            $table->unique(['question_id', 'option_label']);
            $table->index(['question_id', 'is_correct']);
        });

        Schema::create('tryout_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('tryout_package_id')->nullable()->constrained('tryout_packages')->nullOnDelete();
            $table->string('participant_name')->nullable();
            $table->string('participant_email')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('duration_minutes')->default(100);
            $table->enum('status', ['draft', 'ongoing', 'finished'])->default('draft');
            $table->unsignedInteger('twk_score')->default(0);
            $table->unsignedInteger('tiu_score')->default(0);
            $table->unsignedInteger('tkp_score')->default(0);
            $table->unsignedInteger('total_score')->default(0);
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        Schema::create('tryout_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tryout_session_id')->constrained('tryout_sessions')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->foreignId('question_option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $table->boolean('is_marked')->default(false);
            $table->unsignedInteger('score')->default(0);
            $table->timestamps();

            $table->unique(['tryout_session_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tryout_answers');
        Schema::dropIfExists('tryout_sessions');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_categories');
        Schema::dropIfExists('tryout_packages');
    }
};
