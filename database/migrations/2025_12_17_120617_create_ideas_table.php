<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ideas', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['pending', 'in-review', 'approved', 'rejected', 'implemented'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->json('tags')->nullable();
            $table->integer('votes')->default(0);
            $table->timestamps();

            // Indexes
            $table->index('tenant_id');
            $table->index('team_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('priority');
        });

        // Idea votes table
        Schema::create('idea_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idea_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('vote_type', ['up', 'down'])->default('up');
            $table->timestamps();

            $table->unique(['idea_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idea_votes');
        Schema::dropIfExists('ideas');
    }
};