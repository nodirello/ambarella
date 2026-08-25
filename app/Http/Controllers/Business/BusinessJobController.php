<?php

declare(strict_types=1);

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\Job\StoreJobRequest;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessJobController extends Controller
{
    public function index(Request $request): View
    {
        return view('business/jobs/index', [
            'jobs' => $request->user()->businessProfile->jobs()->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('business/jobs/create');
    }

    public function store(StoreJobRequest $request): RedirectResponse
    {
        $this->requireActiveProfile($request);

        Job::create([
            ...$request->safe()->except(['tags']),
            'tags' => $request->tags ?? [],
            'user_id' => $request->user()->id,
            'business_profile_id' => $request->user()->businessProfile->id,
            'company' => $request->company ?: $request->user()->businessProfile->company_name,
        ]);

        return redirect()->route('business.jobs')->with('success', 'Ish e’loni joylandi.');
    }

    public function toggle(Request $request, Job $job): RedirectResponse
    {
        abort_if($job->business_profile_id !== $request->user()->businessProfile?->id, 403);

        $job->update(['is_active' => ! $job->is_active]);

        return back();
    }

    private function requireActiveProfile(Request $request): void
    {
        abort_if(
            ! $request->user()->businessProfile?->isVerified(),
            422,
            'Ish e’lonlari faqat tasdiqlangan bizneslarda mavjud.'
        );
    }
}
