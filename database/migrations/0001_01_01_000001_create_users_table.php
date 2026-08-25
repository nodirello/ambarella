<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable()->unique();
            $table->string('password');

            $table->string('role', 20)->default('user')->index();
            $table->boolean('is_banned')->default(false)->index();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('two_factor_secret')->nullable();
            $table->json('two_factor_recovery_codes')->nullable();

            // Profile
            $table->string('avatar')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('profession', 100)->nullable();
            $table->text('bio')->nullable();
            $table->json('interests')->nullable();
            $table->boolean('profile_completed')->default(false);

            // Referral
            $table->string('referral_code', 12)->nullable()->unique();
            $table->foreignId('referred_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('referral_rewarded')->default(false);
            $table->unsignedInteger('referral_count')->default(0);

            // Gamification
            $table->unsignedInteger('greencoin_balance')->default(0)->index();
            $table->unsignedInteger('login_streak')->default(0);

            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index(['email', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
