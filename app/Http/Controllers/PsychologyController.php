<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Lightweight, evidence-inspired self-assessment tools.
 * Results are educational only — they are not medical diagnostics.
 */
class PsychologyController extends Controller
{
    private const TESTS = [
        'stress' => [
            'title' => 'Stress darajasini aniqlash',
            'questions' => [
                ['key' => 'q1', 'text' => 'So‘nggi 2 haftada asabiylashish tez-tez bo‘lganmi?', 'options' => [0 => 'Hech qachon', 1 => 'Ba’zan', 2 => 'Tez-tez', 3 => 'Deyarli har kuni']],
                ['key' => 'q2', 'text' => 'Ish yoki o‘qish bosimi ostida bo‘lasizmi?', 'options' => [0 => 'Yo‘q', 1 => 'Kam', 2 => 'O‘rtacha', 3 => 'Juda ko‘p']],
                ['key' => 'q3', 'text' => 'Uyqu sifati qanday?', 'options' => [0 => 'Yaxshi', 1 => 'O‘rtacha', 2 => 'Yomon, tez-tez uyg‘onaman', 3 => 'Juda yomon']],
                ['key' => 'q4', 'text' => 'Dam olishga vaqt topasizmi?', 'options' => [0 => 'Har kuni', 1 => 'Haftada bir necha marta', 2 => 'Kamdan-kam', 3 => 'Umuman yo‘q']],
                ['key' => 'q5', 'text' => 'Kelajak haqida xavotir his qilasizmi?', 'options' => [0 => 'Yo‘q', 1 => 'Kamdan-kam', 2 => 'Tez-tez', 3 => 'Doimiy']],
            ],
            'ranges' => [
                [0, 4, 'Past stress — yaxshi holatdasiz. Shu tartibda davom eting!'],
                [5, 9, 'O‘rtacha stress — dam olish va jismoniy faollikni oshiring.'],
                [10, 15, 'Yuqori stress — psixolog bilan suhbat tavsiya etiladi.'],
            ],
        ],
        'burnout' => [
            'title' => 'Professionallikda charchash (burnout) belgilari',
            'questions' => [
                ['key' => 'q1', 'text' => 'Ertalab ishga borish istagi yo‘qolganmi?', 'options' => [0 => 'Yo‘q', 1 => 'Ba’zan', 2 => 'Tez-tez', 3 => 'Har kuni']],
                ['key' => 'q2', 'text' => 'Ish natijalaridan qoniqish his qilmayapsizmi?', 'options' => [0 => 'Qoniqaman', 1 => 'Qisman', 2 => 'Kam', 3 => 'Umuman yo‘q']],
                ['key' => 'q3', 'text' => 'Ish haqida o‘ylash asabni taranglashtiradimi?', 'options' => [0 => 'Yo‘q', 1 => 'Ba’zan', 2 => 'Tez-tez', 3 => 'Doim']],
                ['key' => 'q4', 'text' => 'Ishdan keyin kuchingiz qoladimi?', 'options' => [0 => 'Ha, yetarli', 1 => 'Kam', 2 => 'Juda kam', 3 => 'Umuman yo‘q']],
            ],
            'ranges' => [
                [0, 3, 'Charchash belgilari yo‘q.'],
                [4, 7, 'Dastlabki belgilar — ish yukini qayta ko‘rib chiqing.'],
                [8, 12, 'Kuchli charchash — ta’til va mutaxassis yordami tavsiya etiladi.'],
            ],
        ],
    ];

    public function index(): View
    {
        return view('psychology.index', [
            'tests' => self::TESTS,
        ]);
    }

    public function show(string $slug): View
    {
        abort_unless(isset(self::TESTS[$slug]), 404);

        return view('psychology.test', [
            'slug' => $slug,
            'test' => self::TESTS[$slug],
        ]);
    }

    public function submit(Request $request, string $slug): RedirectResponse
    {
        abort_unless(isset(self::TESTS[$slug]), 404);

        $test = self::TESTS[$slug];
        $score = 0;

        foreach ($test['questions'] as $question) {
            $score += (int) $request->input($question['key'], 0);
        }

        $result = null;
        foreach ($test['ranges'] as [$min, $max, $text]) {
            if ($score >= $min && $score <= $max) {
                $result = $text;
                break;
            }
        }

        if (auth()->check()) {
            app(\App\Services\ActivityLogger::class)->log(
                auth()->user(),
                'psychology_test',
                "Test: {$test['title']} — natija {$score}/".collect($test['questions'])->sum(fn ($q) => 3)
            );
        }

        return back()->with([
            'test_result' => (object) ['score' => $score, 'max' => collect($test['questions'])->sum(fn ($q) => 3), 'text' => $result],
        ]);
    }
}
