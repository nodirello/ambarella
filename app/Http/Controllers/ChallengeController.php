<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeParticipant;
use App\Services\GreenCoinService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChallengeController extends Controller
{
    public function index(): View
    {
        return view('challenges.index', [
            'challenges' => Challenge::active()->latest()->paginate(9),
            'joinedIds' => auth()->check()
                ? ChallengeParticipant::where('user_id', auth()->id())->pluck('challenge_id')->all()
                : [],
        ]);
    }

    public function join(Request $request, Challenge $challenge, GreenCoinService $coins): RedirectResponse
    {
        abort_if(! $challenge->is_active, 404);
        abort_if($challenge->ends_at?->isPast(), 422, 'Musobaqa tugagan.');

        $exists = ChallengeParticipant::where('challenge_id', $challenge->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        abort_if($exists, 422, 'Siz allaqachon qatnashyapsiz.');

        ChallengeParticipant::create([
            'challenge_id' => $challenge->id,
            'user_id' => $request->user()->id,
        ]);

        $coins->credit($request->user(), $challenge->reward, "Challenge: {$challenge->title}", reference: $challenge);

        return back()->with('success', "Musobaqaga qo‘shildingiz! +{$challenge->reward} GreenCoin.");
    }
}
