<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\JobType;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    private static int $sequence = 0;

    public function definition(): array
    {
        self::$sequence++;

        return [
            'title' => ['Frontend Developer', 'Backend Developer', 'UX/UI Dizayner', 'Data Analyst', 'Marketing mutaxassisi', 'DevOps Engineer'][self::$sequence % 6],
            'company' => ['TechCorp UZ', 'DataFlow', 'GreenFuture', 'StartupHub', 'FinTech UZ', 'CloudTech'][self::$sequence % 6],
            'location' => ['Toshkent', 'Samarqand', 'Masofaviy', 'Buxoro'][self::$sequence % 4],
            'type' => [JobType::Remote, JobType::FullTime, JobType::PartTime, JobType::Internship][self::$sequence % 4],
            'salary_min' => 4_000_000,
            'salary_max' => 9_000_000,
            'tags' => ['Laravel', 'React', 'SQL', 'Figma', 'Python', 'Docker'],
            'description' => "Jamoa bilan zamonaviy mahsulot ustida ishlash. Mas'uliyatli va o'rganishga intiluvchan nomzodlar kutamiz.\n\nVazifalar:\n- Yangi funksiyalar ishlab chiqish\n- Kod sifatini saqlash\n- Jamoaviy muloqot",
            'requirements' => "Talablar:\n- 1+ yil tajriba\n- Ingliz tili B1+",
            'benefits' => 'Bepul kurslar, moslashuvchan grafik, sport zali.',
            'contact_email' => 'hr@example.uz',
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
