<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eco_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('reward');
            $table->string('category');
            $table->string('type', 20)->default('daily');
            $table->boolean('requires_proof')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // Audit-proof ledger: every coin movement is an immutable row.
        Schema::create('greencoin_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('amount');
            $table->string('type', 30);
            $table->string('description');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedInteger('balance_after');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at'], 'coin_tx_user_created');
            $table->index(['type', 'created_at']);
        });

        Schema::create('eco_task_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eco_task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('proof_text', 500)->nullable();
            $table->string('photo_path')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->date('completed_on');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['eco_task_id', 'user_id', 'completed_on'], 'eco_daily_unique');
        });

        Schema::create('daily_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->string('priority', 10)->default('medium');
            $table->boolean('is_done')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'due_date']);
        });

        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('frequency', 10)->default('daily');
            $table->json('days')->nullable();
            $table->unsignedInteger('current_streak')->default(0);
            $table->unsignedInteger('best_streak')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('habit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('completed_on');
            $table->timestamps();

            $table->unique(['habit_id', 'completed_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habit_logs');
        Schema::dropIfExists('habits');
        Schema::dropIfExists('daily_tasks');
        Schema::dropIfExists('eco_task_completions');
        Schema::dropIfExists('greencoin_transactions');
        Schema::dropIfExists('eco_tasks');
    }
};
