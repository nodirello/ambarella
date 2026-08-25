<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('industry', 80)->nullable();
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address')->nullable();
            $table->string('city', 80)->nullable();
            $table->string('verification_status', 20)->default('pending')->index();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('business_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_profile_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->string('category', 80)->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_products');
        Schema::dropIfExists('business_profiles');
    }
};
