<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Note\StoreNoteRequest;
use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NoteController extends Controller
{
    public function index(Request $request): View
    {
        return view('notes.index', [
            'notes' => Note::forUser($request->user()->id)->get(),
        ]);
    }

    public function store(StoreNoteRequest $request): RedirectResponse
    {
        Note::create([
            ...$request->safe()->except('color'),
            'color' => $request->color ?? 'slate',
            'user_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Eslatma saqlandi.');
    }

    public function update(Request $request, Note $note): RedirectResponse
    {
        abort_unless($note->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string', 'max:20000'],
        ]);

        $note->update($data);

        return back()->with('success', 'Eslatma yangilandi.');
    }

    public function destroy(Request $request, Note $note): RedirectResponse
    {
        abort_unless($note->user_id === $request->user()->id, 403);

        $note->delete();

        return back()->with('success', 'Eslatma o‘chirildi.');
    }

    public function togglePin(Request $request, Note $note): RedirectResponse
    {
        abort_unless($note->user_id === $request->user()->id, 403);

        $note->update(['is_pinned' => ! $note->is_pinned]);

        return back();
    }
}
