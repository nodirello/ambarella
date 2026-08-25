<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Job\ApplyJobRequest;
use App\Models\Job;
use App\Models\JobApplication;
use App\Traits\LogsActivity;
use App\Traits\TracksViews;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobController extends Controller
{
    use LogsActivity;
    use TracksViews;

    public function index(Request $request): View
    {
        $jobs = Job::active()
            ->with(['businessProfile', 'publisher'])
            ->filter($request->only(['search', 'type', 'city', 'tag', 'min_salary']))
            ->latest()
            ->paginate(12);

        return view('jobs.index', [
            'jobs' => $jobs,
            'filters' => $request->only(['search', 'type', 'city', 'tag', 'min_salary']),
        ]);
    }

    public function show(Job $job): View
    {
        abort_unless($job->is_active, 404);

        $this->registerView($job, $job->title, route('jobs.show', $job));

        return view('jobs.show', [
            'job' => $job->load('applications.user', 'businessProfile'),
            'hasApplied' => auth()->check()
                ? $job->applications()->where('user_id', auth()->id())->exists()
                : false,
        ]);
    }

    public function apply(ApplyJobRequest $request, Job $job): RedirectResponse
    {
        abort_unless($job->is_active, 404);
        abort_if($job->deadline?->isPast(), 422, 'Ariza topshirish muddati tugagan.');

        $exists = JobApplication::where('job_listing_id', $job->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        abort_if($exists, 422, 'Siz allaqachon ariza topshirgansiz.');

        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes');
        }

        JobApplication::create([
            'job_listing_id' => $job->id,
            'user_id' => $request->user()->id,
            'cover_letter' => $request->cover_letter,
            'resume_path' => $resumePath,
        ]);

        $this->logActivity('job_apply', "Ishga ariza: {$job->title} ({$job->company})");

        return back()->with('success', 'Ariza muvaffaqiyatli yuborildi!');
    }
}
