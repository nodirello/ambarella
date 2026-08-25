<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    private static int $sequence = 0;

    public function definition(): array
    {
        self::$sequence++;

        $courses = [
            ['Python dasturlash asoslari', 'Dasturlash', 0, 'Boshlang‘ich'],
            ['Web development (Full-Stack)', 'Dasturlash', 500000, 'O‘rta'],
            ['Grafik dizayn', 'Dizayn', 0, 'Boshlang‘ich'],
            ['Digital marketing', 'Marketing', 300000, 'Boshlang‘ich'],
            ['Flutter mobil ilova', 'Dasturlash', 400000, 'O‘rta'],
        ];

        [$title, $category, $price, $level] = $courses[self::$sequence % count($courses)];

        return [
            'title' => $title,
            'description' => "Ushbu kurs orqali {$title} yo‘nalishida amaliy ko‘nikmalarga ega bo‘lasiz. Kurs video darslar, amaliy topshiriqlar va sertifikatni o‘z ichiga oladi.",
            'instructor' => ['Abdullo Rahimov', 'Sardor Karimov', 'Nilufar Toshmatova', 'Jasur Alimov', 'Bekzod Tursunov'][self::$sequence % 5],
            'instructor_role' => 'Senior mutaxassis',
            'duration' => ['2 oy', '3 oy', '6 oy'][self::$sequence % 3],
            'price' => $price,
            'level' => $level,
            'rating' => 4.7,
            'category' => $category,
            'tags' => ['IT', 'Amaliy'],
            'is_published' => true,
        ];
    }
}
