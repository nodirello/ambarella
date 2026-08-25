<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PlatformEvent;
use App\Models\EventRegistration;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        return view('events.index', [
            'upcoming' => PlatformEvent::upcoming()->orderBy('starts_at')->paginate(9),
            'past' => PlatformEvent::where('is_published', true)->where('starts_at', '<', now())->latest()->limit(3)->get(),
        ]);
    }

    public function show(PlatformEvent $event): View
    {
        abort_unless($event->is_published, 404);

        return view('events.show', [
            'event' => $event->loadCount('registrations'),
            'registered' => auth()->check()
                ? EventRegistration::where('platform_event_id', $event->id)->where('user_id', auth()->id())->exists()
                : false,
        ]);
    }

    public function register(Request $request, PlatformEvent $event, ActivityLogger $activity): RedirectResponse
    {
        abort_if(! $event->is_published, 404);
        abort_if($event->starts_at->isPast(), 422, 'Tadbir allaqachon boshlangan.');

        $exists = EventRegistration::where('platform_event_id', $event->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        abort_if($exists, 422, 'Siz allaqachon ro‘yxatdan o‘tgansiz.');

        if ($event->capacity > 0 && $event->registrations()->count() >= $event->capacity) {
            abort(422, 'Tadbir uchun o‘rinlar tugadi.');
        }

        EventRegistration::create([
            'platform_event_id' => $event->id,
            'user_id' => $request->user()->id,
        ]);

        $activity->log($request->user(), 'event_register', "Tadbirga yozildi: {$event->title}");

        return back()->with('success', 'Tadbirga muvaffaqiyatli yozildingiz!');
    }
}
