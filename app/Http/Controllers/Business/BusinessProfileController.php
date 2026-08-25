<?php

declare(strict_types=1);

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\StoreBusinessRequest;
use App\Models\BusinessProfile;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessProfileController extends Controller
{
    public function show(Request $request): RedirectResponse|View
    {
        $profile = $request->user()->businessProfile;

        if (! $profile) {
            return redirect()->route('business.register');
        }

        return view('business/profile', ['profile' => $profile]);
    }

    public function register(): View
    {
        abort_if(auth()->user()->businessProfile, 403, 'Biznes profili allaqachon mavjud.');

        return view('business/register');
    }

    public function store(StoreBusinessRequest $request, ImageService $images): RedirectResponse
    {
        abort_if($request->user()->businessProfile, 403);

        BusinessProfile::create([
            'user_id' => $request->user()->id,
            'company_name' => $request->company_name,
            'description' => $request->description,
            'industry' => $request->industry,
            'website' => $request->website,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'logo_path' => $images->upload($request->file('logo'), 'business', 512),
        ]);

        // Business users get the role automatically.
        $request->user()->update(['role' => \App\Enums\Role::Business]);

        return redirect()->route('business.dashboard')->with('success', 'Biznes profili yuborildi. Tekshiruvdan so‘ng faollashadi.');
    }

    public function update(StoreBusinessRequest $request, ImageService $images): RedirectResponse
    {
        $profile = $request->user()->businessProfile;

        abort_unless($profile, 404);

        $profile->update([
            'company_name' => $request->company_name,
            'description' => $request->description,
            'industry' => $request->industry,
            'website' => $request->website,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'logo_path' => $images->upload($request->file('logo'), 'business', 512) ?? $profile->logo_path,
        ]);

        return back()->with('success', 'Profil yangilandi.');
    }
}
