<?php

declare(strict_types=1);

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessProduct;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessProductController extends Controller
{
    public function index(Request $request): View
    {
        return view('business/products/index', [
            'products' => $request->user()->businessProfile?->products()->latest()->get() ?? collect(),
        ]);
    }

    public function store(Request $request, ImageService $images): RedirectResponse
    {
        $profile = $request->user()->businessProfile;
        abort_unless($profile, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:3000'],
            'price' => ['required', 'integer', 'min:0'],
            'category' => ['nullable', 'string', 'max:80'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        BusinessProduct::create([
            ...$data,
            'image_path' => $images->upload($request->file('image'), 'products', 1200),
            'business_profile_id' => $profile->id,
        ]);

        return back()->with('success', 'Mahsulot qo‘shildi.');
    }
}
