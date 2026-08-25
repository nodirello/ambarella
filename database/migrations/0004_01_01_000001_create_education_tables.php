<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('instructor');
            $table->string('instructor_role')->nullable();
            $table->string('duration', 50)->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->string('level', 20)->default('beginner');
            $table->decimal('rating', 3, 2)->default(0);
            $table->string('category', 60)->index();
            $table->json('tags')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('course_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->string('certificate_code')->nullable()->unique();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
        });

        Schema::create('mentors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('role');
            $table->string('company')->nullable();
            $table->unsignedInteger('experience_years')->default(0);
            $table->json('skills')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->unsignedInteger('hourly_rate')->nullable();
            $table->boolean('is_available')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('mentor_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->string('status', 20)->default('pending');
            $table->timestamps();

            $table->unique(['mentor_id', 'user_id']);
        });

        Schema::create('mentorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('pending')->index();
            $table->text('goal')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });

        Schema::create('mentor_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentorship_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor_tasks');
        Schema::dropIfExists('mentorships');
        Schema::dropIfExists('mentor_requests');
        Schema::dropIfExists('mentors');
        Schema::dropIfExists('course_user');
        Schema::dropIfExists('courses');
    }
};
