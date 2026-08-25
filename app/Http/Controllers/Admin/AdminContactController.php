<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminContactController extends Controller
{
    public function index(): View
    {
        return view('admin.contacts.index', [
            'messages' => ContactMessage::latest()->paginate(15),
        ]);
    }

    public function show(ContactMessage $message): View
    {
        $message->update(['status' => 'read']);

        return view('admin.contacts.show', ['message' => $message]);
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return back()->with('success', 'Murojaat o‘chirildi.');
    }
}
