<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('venue')->nullable();
            $table->string('city', 80)->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('capacity')->default(0);
            $table->unsignedInteger('price')->default(0);
            $table->string('cover_image')->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['starts_at', 'is_published']);
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('registered');
            $table->string('ticket_code', 24)->nullable()->unique();
            $table->timestamps();

            $table->unique(['platform_event_id', 'user_id']);
        });

        Schema::create('startups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description');
            $table->string('category', 80);
            $table->string('stage', 20)->default('idea');
            $table->unsignedTinyInteger('team_size')->default(1);
            $table->string('looking_for', 200)->nullable();
            $table->string('website')->nullable();
            $table->unsignedInteger('votes_count')->default(0);
            $table->boolean('is_approved')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('startup_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('startup_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['startup_id', 'user_id']);
        });

        Schema::create('volunteer_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('organization');
            $table->string('city', 80)->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('hours_expected')->default(0);
            $table->unsignedInteger('capacity')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('volunteer_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('pending');
            $table->unsignedInteger('hours')->default(0);
            $table->timestamps();

            $table->unique(['volunteer_project_id', 'user_id']);
        });

        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('reward');
            $table->string('category', 60);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('challenge_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('active');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['challenge_id', 'user_id']);
        });

        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('allow_multiple')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('poll_option_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['poll_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poll_votes');
        Schema::dropIfExists('poll_options');
        Schema::dropIfExists('polls');
        Schema::dropIfExists('challenge_participants');
        Schema::dropIfExists('challenges');
        Schema::dropIfExists('volunteer_applications');
        Schema::dropIfExists('volunteer_projects');
        Schema::dropIfExists('startup_votes');
        Schema::dropIfExists('startups');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('platform_events');
    }
};
