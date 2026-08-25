<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\VolunteerApplication;
use App\Models\VolunteerProject;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VolunteerController extends Controller
{
    public function index(): View
    {
        return view('volunteer.index', [
            'projects' => VolunteerProject::active()->withCount('applications')->latest()->paginate(9),
            'stats' => [
                'projects' => VolunteerProject::active()->count(),
                'participants' => VolunteerApplication::distinct('user_id')->count('user_id'),
                'hours' => VolunteerApplication::where('status', 'completed')->sum('hours'),
            ],
        ]);
    }

    public function apply(Request $request, VolunteerProject $project, ActivityLogger $activity): RedirectResponse
    {
        abort_if(! $project->is_active, 404);

        $exists = VolunteerApplication::where('volunteer_project_id', $project->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        abort_if($exists, 422, 'Siz allaqachon ariza topshirgansiz.');

        $data = $request->validate(['message' => ['nullable', 'string', 'max:1000']]);

        VolunteerApplication::create([
            'volunteer_project_id' => $project->id,
            'user_id' => $request->user()->id,
            'message' => $data['message'] ?? null,
        ]);

        $activity->log($request->user(), 'volunteer_apply', "Ko‘ngilli bo‘ldi: {$project->title}");

        return back()->with('success', 'Arizangiz qabul qilindi!');
    }
}
