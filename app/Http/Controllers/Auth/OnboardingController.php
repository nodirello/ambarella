<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function index(): View
    {
        return view('auth.onboarding', [
            'regions' => $this->regions(),
        ]);
    }

    public function store(Request $request, ImageService $images): RedirectResponse
    {
        $data = $request->validate([
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'region' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'profession' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'interests' => ['nullable', 'array', 'max:10'],
            'interests.*' => ['string', 'max:40'],
            'avatar' => ['nullable', 'image', 'max:4096'],
        ]);

        $user = $request->user();
        $user->update([
            'birth_date' => $data['birth_date'] ?? null,
            'gender' => $data['gender'] ?? null,
            'region' => $data['region'] ?? null,
            'city' => $data['city'] ?? null,
            'profession' => $data['profession'] ?? null,
            'bio' => $data['bio'] ?? null,
            'interests' => $data['interests'] ?? [],
            'avatar' => $images->upload($request->file('avatar'), 'avatars', 512) ?? $user->avatar,
            'profile_completed' => true,
        ]);

        return redirect()->route('dashboard.index')->with('success', 'Profil to‘ldirildi!');
    }

    private function regions(): array
    {
        return [
            'Toshkent shahri', 'Toshkent viloyati', 'Samarqand', 'Buxoro', 'Andijon',
            'Farg‘ona', 'Namangan', 'Qashqadaryo', 'Surxondaryo', 'Jizzax',
            'Sirdaryo', 'Navoiy', 'Xorazm', 'Qoraqalpog‘iston Respublikasi',
        ];
    }
}
