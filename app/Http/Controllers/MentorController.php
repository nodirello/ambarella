<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Mentor;
use App\Models\MentorRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MentorController extends Controller
{
    public function index(): View
    {
        return view('mentor.index', [
            'mentors' => Mentor::available()->with('user')->latest()->paginate(12),
        ]);
    }

    public function show(Mentor $mentor): View
    {
        abort_unless($mentor->is_available, 404);

        return view('mentor.show', [
            'mentor' => $mentor,
            'hasRequested' => auth()->check()
                ? $mentor->requests()->where('user_id', auth()->id())->exists()
                : false,
        ]);
    }

    public function requestSession(Request $request, Mentor $mentor): RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $exists = MentorRequest::where('mentor_id', $mentor->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        abort_if($exists, 422, 'Siz bu mentorga allaqachon so‘rov yuborgansiz.');

        MentorRequest::create([
            'mentor_id' => $mentor->id,
            'user_id' => $request->user()->id,
            'message' => $data['message'],
        ]);

        return back()->with('success', 'So‘rov yuborildi. Mentor javob beradi.');
    }
}
