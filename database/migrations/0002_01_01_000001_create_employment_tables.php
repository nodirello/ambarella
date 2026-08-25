<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('business_profile_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->string('company');
            $table->string('location', 120)->nullable();
            $table->string('type', 20);
            $table->unsignedInteger('salary_min')->nullable();
            $table->unsignedInteger('salary_max')->nullable();
            $table->string('currency', 3)->default('UZS');
            $table->json('tags')->nullable();
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            $table->string('contact_email')->nullable();
            $table->date('deadline')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['is_active', 'created_at']);
            $table->index(['company', 'location']);
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('cover_letter')->nullable();
            $table->string('resume_path')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();

            $table->unique(['job_listing_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_listings');
    }
};
