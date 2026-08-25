<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyCourseController extends Controller
{
    public function index(): View
    {
        $courses = auth()->user()->courses()->withPivot(['progress', 'completed_at', 'certificate_code'])->latest('course_user.created_at')->get();

        return view('my-courses.index', ['courses' => $courses]);
    }

    public function updateProgress(Request $request, int $courseId): RedirectResponse
    {
        $request->validate([
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $enrollment = auth()->user()->courses()->where('course_id', $courseId)->firstOrFail();

        $completed = $request->integer('progress') >= 100;
        $certificateCode = null;

        if ($completed && $enrollment->pivot->completed_at === null) {
            $certificateCode = app(\App\Services\CertificateService::class)->issue(
                $enrollment,
                auth()->user()
            );
        }

        auth()->user()->courses()->updateExistingPivot($courseId, [
            'progress' => $request->integer('progress'),
            'completed_at' => $completed && $enrollment->pivot->completed_at === null ? now() : $enrollment->pivot->completed_at,
            'certificate_code' => $certificateCode ?? $enrollment->pivot->certificate_code,
        ]);

        return back()->with('success', 'Taraqqiyot yangilandi.');
    }
}
