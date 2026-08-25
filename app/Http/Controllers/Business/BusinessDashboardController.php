<?php

declare(strict_types=1);

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessDashboardController extends Controller
{
    public function index(Request $request): RedirectResponse|View
    {
        $profile = $request->user()->businessProfile;

        if (! $profile) {
            return redirect()->route('business.register');
        }

        $jobStats = [
            'total' => $profile->jobs()->count(),
            'active' => $profile->jobs()->where('is_active', true)->count(),
            'applications' => JobApplication::whereIn('job_listing_id', $profile->jobs()->select('id'))->count(),
            'pending' => JobApplication::whereIn('job_listing_id', $profile->jobs()->select('id'))->where('status', 'pending')->count(),
        ];

        return view('business/dashboard', [
            'profile' => $profile,
            'stats' => $jobStats,
            'latestApplications' => JobApplication::with(['job', 'user'])
                ->whereIn('job_listing_id', $profile->jobs()->select('id'))
                ->latest()
                ->take(6)
                ->get(),
        ]);
    }
}
