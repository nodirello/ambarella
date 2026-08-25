<?php

declare(strict_types=1);

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessApplicationController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $profile = $request->user()->businessProfile;

        if (! $profile) {
            return redirect()->route('business.register');
        }

        return view('business/applications/index', [
            'applications' => JobApplication::with(['job', 'user'])
                ->whereIn('job_listing_id', $profile->jobs()->select('id'))
                ->latest()
                ->paginate(15),
        ]);
    }

    public function updateStatus(Request $request, JobApplication $application, ActivityLogger $activity): RedirectResponse
    {
        $profile = $request->user()->businessProfile;

        abort_unless(
            $profile && $application->job->business_profile_id === $profile->id,
            403
        );

        $data = $request->validate([
            'status' => ['required', 'in:pending,viewed,shortlisted,accepted,rejected'],
        ]);

        $application->update(['status' => $data['status']]);
        $activity->log($request->user(), 'application_status', "Ariza holati: {$application->user->name} → {$data['status']}");

        return back()->with('success', 'Holat yangilandi.');
    }
}
