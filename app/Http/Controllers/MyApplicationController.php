<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class MyApplicationController extends Controller
{
    public function index(): View
    {
        return view('my-applications.index', [
            'applications' => auth()->user()
                ->applications()
                ->with('job')
                ->latest()
                ->paginate(10),
        ]);
    }
}
