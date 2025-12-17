<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ideas', function (Blueprint $table) {
            // Problem & Goal
            $table->string('problem_short')->nullable()->after('title');
            $table->text('goal')->nullable()->after('problem_short');
            
            // Solution & Pain
            $table->text('solution')->nullable()->after('description');
            $table->integer('pain_score')->default(0)->after('solution'); // 0-10
            
            // Cost & Duration
            $table->decimal('cost_estimate', 10, 2)->nullable()->after('pain_score');
            $table->string('duration_estimate')->nullable()->after('cost_estimate'); // e.g., "3 days", "2 weeks"
            
            // Priorities (calculated)
            $table->decimal('priority_1', 5, 2)->default(0)->after('priority'); // Main priority score
            $table->decimal('priority_2', 5, 2)->default(0)->after('priority_1'); // Secondary priority
            
            // Implementation status
            $table->boolean('in_implementation')->default(false)->after('status');
            $table->date('implementation_date')->nullable()->after('in_implementation');
            
            // User email for follow-up
            $table->string('submitter_email')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('ideas', function (Blueprint $table) {
            $table->dropColumn([
                'problem_short',
                'goal',
                'solution',
                'pain_score',
                'cost_estimate',
                'duration_estimate',
                'priority_1',
                'priority_2',
                'in_implementation',
                'implementation_date',
                'submitter_email'
            ]);
        });
    }
};