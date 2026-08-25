<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EcoTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EcoTask>
 */
class EcoTaskFactory extends Factory
{
    protected $model = EcoTask::class;

    private static int $sequence = 0;

    public function definition(): array
    {
        self::$sequence++;

        $tasks = [
            ['Plastik chiqindilarni saralash', 'Chiqindi', 5],
            ['Velosipedda yurish', 'Transport', 10],
            ['Chiroqlarni o‘chirish', 'Energiya', 3],
            ['Suv tejash', 'Energiya', 5],
            ['Daraxt ekish', 'Kundalik', 20],
            ['Jamoat transporti', 'Transport', 8],
        ];

        [$title, $category, $reward] = $tasks[self::$sequence % count($tasks)];

        return [
            'title' => $title,
            'description' => "Bugun shu vazifani bajaring va {$reward} GreenCoin qo‘lga kiriting. Isbot sifatida qisqacha matn yozing.",
            'reward' => $reward,
            'category' => $category,
            'type' => 'daily',
            'requires_proof' => false,
            'is_active' => true,
        ];
    }
}
