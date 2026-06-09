<?php

use App\Support\TryoutBlueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->string('tryout_type', 50)->default(TryoutBlueprint::TYPE_CPNS)->after('slug');
            $table->json('section_composition')->nullable()->after('tkp_count');
            $table->json('section_thresholds')->nullable()->after('section_composition');
        });

        Schema::table('question_categories', function (Blueprint $table) {
            $table->string('tryout_type', 50)->default(TryoutBlueprint::TYPE_CPNS)->after('slug');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->string('tryout_type', 50)->default(TryoutBlueprint::TYPE_CPNS)->after('question_category_id');
        });

        Schema::table('tryout_sessions', function (Blueprint $table) {
            $table->json('section_scores')->nullable()->after('tkp_score');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE question_categories MODIFY section VARCHAR(100) NOT NULL");
            DB::statement("ALTER TABLE questions MODIFY section VARCHAR(100) NOT NULL");
        }

        DB::table('question_categories')->where('section', 'TWK')->update(['section' => 'twk']);
        DB::table('question_categories')->where('section', 'TIU')->update(['section' => 'tiu']);
        DB::table('question_categories')->where('section', 'TKP')->update(['section' => 'tkp']);

        DB::table('questions')->where('section', 'TWK')->update(['section' => 'twk']);
        DB::table('questions')->where('section', 'TIU')->update(['section' => 'tiu']);
        DB::table('questions')->where('section', 'TKP')->update(['section' => 'tkp']);

        DB::table('tryout_packages')
            ->orderBy('id')
            ->get(['id', 'twk_count', 'tiu_count', 'tkp_count'])
            ->each(function ($package) {
                DB::table('tryout_packages')
                    ->where('id', $package->id)
                    ->update([
                        'tryout_type' => TryoutBlueprint::TYPE_CPNS,
                        'section_composition' => json_encode([
                            [
                                'key' => 'twk',
                                'label' => 'TWK',
                                'count' => (int) $package->twk_count,
                                'scoring_mode' => 'single_correct',
                            ],
                            [
                                'key' => 'tiu',
                                'label' => 'TIU',
                                'count' => (int) $package->tiu_count,
                                'scoring_mode' => 'single_correct',
                            ],
                            [
                                'key' => 'tkp',
                                'label' => 'TKP',
                                'count' => (int) $package->tkp_count,
                                'scoring_mode' => 'weighted',
                            ],
                        ]),
                        'section_thresholds' => json_encode(TryoutBlueprint::defaultThresholds(TryoutBlueprint::TYPE_CPNS)),
                    ]);
            });

        DB::table('question_categories')->update(['tryout_type' => TryoutBlueprint::TYPE_CPNS]);
        DB::table('questions')->update(['tryout_type' => TryoutBlueprint::TYPE_CPNS]);
    }

    public function down(): void
    {
        DB::table('question_categories')->where('section', 'twk')->update(['section' => 'TWK']);
        DB::table('question_categories')->where('section', 'tiu')->update(['section' => 'TIU']);
        DB::table('question_categories')->where('section', 'tkp')->update(['section' => 'TKP']);

        DB::table('questions')->where('section', 'twk')->update(['section' => 'TWK']);
        DB::table('questions')->where('section', 'tiu')->update(['section' => 'TIU']);
        DB::table('questions')->where('section', 'tkp')->update(['section' => 'TKP']);

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE question_categories MODIFY section ENUM('TWK','TIU','TKP') NOT NULL");
            DB::statement("ALTER TABLE questions MODIFY section ENUM('TWK','TIU','TKP') NOT NULL");
        }

        Schema::table('tryout_sessions', function (Blueprint $table) {
            $table->dropColumn('section_scores');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('tryout_type');
        });

        Schema::table('question_categories', function (Blueprint $table) {
            $table->dropColumn('tryout_type');
        });

        Schema::table('tryout_packages', function (Blueprint $table) {
            $table->dropColumn([
                'tryout_type',
                'section_composition',
                'section_thresholds',
            ]);
        });
    }
};
