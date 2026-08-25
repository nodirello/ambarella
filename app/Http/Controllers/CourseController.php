<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        return view('academy.index', [
            'courses' => Course::published()
                ->orderByDesc('is_featured')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function show(Course $course): View
    {
        abort_unless($course->is_published, 404);

        return view('academy.show', [
            'course' => $course,
            'enrolled' => auth()->check()
                ? auth()->user()->courses()->where('course_id', $course->id)->exists()
                : false,
        ]);
    }

    public function enroll(Request $request, Course $course): RedirectResponse
    {
        abort_unless($course->is_published, 404);

        if (! $request->user()->courses()->where('course_id', $course->id)->exists()) {
            $request->user()->courses()->attach($course->id);
        }

        return redirect()->route('my-courses.index')->with('success', "Kursga yozildingiz: {$course->title}");
    }
}
