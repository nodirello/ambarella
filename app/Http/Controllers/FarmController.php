<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BusinessProduct;
use Illuminate\View\View;

/**
 * Public farmers' marketplace built on verified business products.
 */
class FarmController extends Controller
{
    public function index(): View
    {
        return view('farm.index', [
            'products' => BusinessProduct::where('is_active', true)
                ->with('profile')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function show(BusinessProduct $product): View
    {
        abort_unless($product->is_active, 404);

        return view('farm.show', [
            'product' => $product->load('profile'),
        ]);
    }
}
