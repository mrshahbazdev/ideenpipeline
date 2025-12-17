<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Add platform integration fields
            if (!Schema::hasColumn('tenants', 'platform_subscription_id')) {
                $table->string('platform_subscription_id')->nullable()->after('id');
            }
            
            if (!Schema::hasColumn('tenants', 'platform_user_id')) {
                $table->string('platform_user_id')->nullable()->after('platform_subscription_id');
            }

            if (!Schema::hasColumn('tenants', 'domain')) {
                $table->string('domain')->nullable()->after('subdomain');
            }

            if (!Schema::hasColumn('tenants', 'starts_at')) {
                $table->timestamp('starts_at')->nullable()->after('expires_at');
            }

            // Add indexes
            $table->index('platform_subscription_id');
            $table->index('platform_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex(['platform_subscription_id']);
            $table->dropIndex(['platform_user_id']);
            $table->dropColumn([
                'platform_subscription_id',
                'platform_user_id',
                'domain',
                'starts_at'
            ]);
        });
    }
};