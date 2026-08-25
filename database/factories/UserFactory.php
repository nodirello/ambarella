<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    private static int $sequence = 0;

    public function definition(): array
    {
        self::$sequence++;

        $names = ['Jasur Toshmatov', 'Nilufar Azimova', 'Sardor Karimov', 'Malika Rahimova', 'Bekzod Tursunov', 'Dildora Yusupova'];
        $regions = ['Toshkent', 'Samarqand', 'Buxoro', 'Farg‘ona', 'Andijon', 'Namangan'];

        return [
            'name' => $names[self::$sequence % count($names)],
            'email' => 'user'.self::$sequence.'@example.uz',
            'phone' => '+9989'.str_pad((string) self::$sequence, 8, '0', STR_PAD_LEFT),
            'password' => Hash::make('Password123'),
            'role' => Role::User,
            'email_verified_at' => now(),
            'profile_completed' => true,
            'greencoin_balance' => 0,
            'region' => $regions[self::$sequence % count($regions)],
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => Role::Admin]);
    }

    public function incompleteProfile(): static
    {
        return $this->state(fn () => ['profile_completed' => false]);
    }
}
