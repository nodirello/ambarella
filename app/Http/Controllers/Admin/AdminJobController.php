<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Job\StoreJobRequest;
use App\Models\Job;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminJobController extends Controller
{
    public function index(): View
    {
        return view('admin.jobs.index', [
            'jobs' => Job::with(['publisher', 'businessProfile'])
                ->withCount('applications')
                ->orderByDesc('is_active')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.jobs.form', ['job' => null]);
    }

    public function store(StoreJobRequest $request, ActivityLogger $activity): RedirectResponse
    {
        Job::create([
            ...$request->safe()->except('tags'),
            'tags' => $request->tags ?? [],
        ]);

        $activity->log($request->user(), 'job_create', "Ish e’loni yaratildi: {$request->title}");

        return redirect()->route('admin.jobs.index')->with('success', 'Ish e’loni yaratildi.');
    }

    public function edit(Job $job): View
    {
        return view('admin.jobs.form', ['job' => $job]);
    }

    public function update(StoreJobRequest $request, Job $job): RedirectResponse
    {
        $job->update([
            ...$request->safe()->except('tags'),
            'tags' => $request->tags ?? [],
        ]);

        return back()->with('success', 'Ish e’loni yangilandi.');
    }

    public function destroy(Job $job): RedirectResponse
    {
        $job->delete();

        return back()->with('success', 'Ish e’loni o‘chirildi.');
    }

    public function toggle(Job $job): RedirectResponse
    {
        $job->update(['is_active' => ! $job->is_active]);

        return back();
    }
}
