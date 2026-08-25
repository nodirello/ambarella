<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id', 32)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name', 120)->nullable();
            $table->string('last_name', 120)->nullable();
            $table->string('username', 120)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('locale', 5)->default('uz');
            $table->boolean('is_joined_channel')->default(false);
            $table->timestamp('last_interaction_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'last_interaction_at']);
        });

        Schema::create('telegram_bot_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('reward');
            $table->string('type', 30)->default('text');
            $table->string('channel_url')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('telegram_bot_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('telegram_bot_task_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 30);
            $table->text('payload_text')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->string('telegram_chat_id', 32)->nullable();
            $table->unsignedBigInteger('telegram_message_id')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('telegram_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inviter_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('invitee_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('invitee_phone', 20);
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_referrals');
        Schema::dropIfExists('telegram_bot_submissions');
        Schema::dropIfExists('telegram_bot_tasks');
        Schema::dropIfExists('telegram_users');
    }
};
