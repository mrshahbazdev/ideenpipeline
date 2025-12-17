<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            if (!Schema::hasColumn('team_user', 'created_at')) {
                $table->timestamp('created_at')->nullable()->after('joined_at');
            }
            if (!Schema::hasColumn('team_user', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }
};