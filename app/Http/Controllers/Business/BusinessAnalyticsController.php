<?php

declare(strict_types=1);

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessAnalyticsController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $profile = $request->user()->businessProfile;
        if (! $profile) {
            return redirect()->route('business.register');
        }

        $jobIds = $profile->jobs()->select('id');

        $applications = JobApplication::whereIn('job_listing_id', $jobIds);

        $byStatus = (clone $applications)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $last14Days = (clone $applications)
            ->where('created_at', '>=', now()->subDays(14))
            ->selectRaw('date(created_at) as day, count(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        return view('business/analytics', [
            'profile' => $profile,
            'totals' => [
                'views' => (int) $profile->jobs()->sum('views_count'),
                'applications' => $applications->count(),
                'conversion' => $applications->count() > 0
                    ? round($applications->count() / max(1, (int) $profile->jobs()->sum('views_count')) * 100, 1)
                    : 0,
            ],
            'byStatus' => $byStatus,
            'last14Days' => $last14Days,
        ]);
    }
}
