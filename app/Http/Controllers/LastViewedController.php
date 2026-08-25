<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class LastViewedController extends Controller
{
    public function index(): View
    {
        return view('last-viewed.index', [
            'items' => session('last_viewed', []),
        ]);
    }
}
