<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    protected $model = News::class;

    private static int $sequence = 0;

    public function definition(): array
    {
        self::$sequence++;

        $titles = [
            ['AMBARELLA platformasi ishga tushdi!', 'Platforma'],
            ['GreenCoin tizimi yangilandi', 'Ekologiya'],
            ['Yangi kurslar qo‘shildi', 'Ta’lim'],
            ['100+ ish e’lonlari', 'Bandlik'],
        ];

        [$title, $category] = $titles[self::$sequence % count($titles)];

        return [
            'title' => $title,
            'content' => "{$title}\n\nO‘zbekiston yoshlari uchun yaratilgan AMBARELLA platformasida yangi imkoniyatlar paydo bo‘ldi. Platforma bandlik, ekologiya, ta’lim va mentorlik yo‘nalishlarida xizmat ko‘rsatadi.\n\nBarcha modullar mobil qurilmalarda ham qulay ishlaydi va foydalanuvchilar PWA orqali ilovani telefoniga o‘rnatishi mumkin.",
            'category' => $category,
            'author_name' => 'SADAF DEV',
            'is_published' => true,
            'published_at' => now()->subHours(self::$sequence * 3),
        ];
    }
}
