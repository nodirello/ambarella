<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user()->load('businessProfile'),
        ]);
    }

    public function update(UpdateProfileRequest $request, ImageService $images): RedirectResponse
    {
        $data = $request->safe();

        $request->user()->update([
            'name' => $data->name,
            'birth_date' => $data->birth_date,
            'gender' => $data->gender,
            'region' => $data->region,
            'city' => $data->city,
            'profession' => $data->profession,
            'bio' => $data->bio,
            'interests' => $data->interests ?? [],
            'avatar' => $images->upload($request->file('avatar'), 'avatars', 512)
                ?? $request->user()->avatar,
        ]);

        return back()->with('success', 'Profil yangilandi.');
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $request->user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Parol yangilandi.');
    }
}
