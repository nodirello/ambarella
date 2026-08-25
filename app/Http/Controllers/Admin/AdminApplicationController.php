<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminApplicationController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.applications.index', [
            'applications' => JobApplication::with(['job', 'user'])
                ->when($request->status, fn ($q, $s) => $q->where('status', $s))
                ->latest()
                ->paginate(20),
        ]);
    }

    public function updateStatus(Request $request, JobApplication $application): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:pending,viewed,shortlisted,accepted,rejected']]);

        $application->update($data);

        return back()->with('success', 'Ariza holati yangilandi.');
    }
}
