<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('platform_subscription_id')->nullable();
            $table->unsignedBigInteger('platform_user_id')->nullable();
            $table->string('admin_name');
            $table->string('admin_email');
            $table->string('package_name');
            $table->string('subdomain')->unique();
            $table->string('domain')->nullable()->unique();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};