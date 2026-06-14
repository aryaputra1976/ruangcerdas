<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        try {
            $this->normalizeSectionColumn('question_categories');
            $this->normalizeSectionColumn('questions');
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    public function down(): void
    {
        // Tidak mengembalikan ke ENUM lama agar data section umum tetap aman.
    }

    private function normalizeSectionColumn(string $table): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'section')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildSqliteTable($table);

            return;
        }

        DB::statement("ALTER TABLE {$table} MODIFY section VARCHAR(100) NOT NULL");
    }

    private function rebuildSqliteTable(string $table): void
    {
        $tempTable = $table . '_section_tmp';

        Schema::create($tempTable, function (Blueprint $blueprint) use ($table) {
            if ($table === 'question_categories') {
                $blueprint->id();
                $blueprint->string('name');
                $blueprint->string('slug')->unique();
                $blueprint->string('tryout_type', 50)->default('cpns');
                $blueprint->string('section', 100);
                $blueprint->text('description')->nullable();
                $blueprint->boolean('is_active')->default(true);
                $blueprint->timestamps();
                $blueprint->softDeletes();
                $blueprint->index(['section', 'is_active']);
            }

            if ($table === 'questions') {
                $blueprint->id();
                $blueprint->foreignId('question_category_id')->nullable()->constrained('question_categories')->nullOnDelete();
                $blueprint->string('tryout_type', 50)->default('cpns');
                $blueprint->string('section', 100);
                $blueprint->longText('question_text');
                $blueprint->longText('explanation')->nullable();
                $blueprint->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
                $blueprint->boolean('is_active')->default(true);
                $blueprint->timestamps();
                $blueprint->softDeletes();
                $blueprint->index(['section', 'difficulty', 'is_active']);
            }
        });

        if ($table === 'question_categories') {
            DB::table($tempTable)->insertUsing(
                ['id', 'name', 'slug', 'tryout_type', 'section', 'description', 'is_active', 'created_at', 'updated_at', 'deleted_at'],
                DB::table($table)->select([
                    'id',
                    'name',
                    'slug',
                    'tryout_type',
                    'section',
                    'description',
                    'is_active',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ])
            );
        }

        if ($table === 'questions') {
            DB::table($tempTable)->insertUsing(
                ['id', 'question_category_id', 'tryout_type', 'section', 'question_text', 'explanation', 'difficulty', 'is_active', 'created_at', 'updated_at', 'deleted_at'],
                DB::table($table)->select([
                    'id',
                    'question_category_id',
                    'tryout_type',
                    'section',
                    'question_text',
                    'explanation',
                    'difficulty',
                    'is_active',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ])
            );
        }

        Schema::drop($table);
        Schema::rename($tempTable, $table);
    }
};
