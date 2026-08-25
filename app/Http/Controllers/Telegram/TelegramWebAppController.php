<?php

declare(strict_types=1);

namespace App\Http\Controllers\Telegram;

use App\Enums\ModerationStatus;
use App\Http\Controllers\Controller;
use App\Models\EcoTask;
use App\Models\EcoTaskCompletion;
use App\Models\TelegramUser;
use App\Models\User;
use App\Services\GreenCoinService;
use App\Services\TelegramService;
use App\Traits\LogsActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TelegramWebAppController extends Controller
{
    use LogsActivity;

    public function __construct(private readonly TelegramService $telegram)
    {
    }

    public function index(): View
    {
        return view('tg-app.index');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->userFromInitData($request);

        if (! $user) {
            return response()->json(['message' => 'initData tasdiqlanmadi.'], 401);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'greencoin_balance' => $user->greencoin_balance,
                'login_streak' => $user->login_streak,
            ],
        ]);
    }

    public function ecoTasks(Request $request): JsonResponse
    {
        $user = $this->requireUser($request);

        $tasks = EcoTask::active()
            ->withCount(['completions as approved_count' => fn ($q) => $q->where('status', 'approved')])
            ->get()
            ->map(function (EcoTask $task) use ($user) {
                $done = EcoTaskCompletion::where('eco_task_id', $task->id)
                    ->where('user_id', $user->id)
                    ->whereDate('completed_on', today())
                    ->exists();

                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'reward' => $task->reward,
                    'category' => $task->category,
                    'done' => $done,
                ];
            });

        return response()->json(['tasks' => $tasks]);
    }

    public function ecoStats(Request $request): JsonResponse
    {
        $user = $this->requireUser($request);

        return response()->json([
            'balance' => $user->greencoin_balance,
            'completed' => EcoTaskCompletion::where('user_id', $user->id)->where('status', 'approved')->count(),
            'streak' => $user->login_streak,
        ]);
    }

    public function completeEco(Request $request): JsonResponse
    {
        $user = $this->requireUser($request);

        $data = $request->validate([
            'eco_task_id' => ['required', 'integer', 'exists:eco_tasks,id,is_active,1'],
            'proof_text' => ['required', 'string', 'max:500'],
        ]);

        $task = EcoTask::findOrFail($data['eco_task_id']);

        $exists = EcoTaskCompletion::where('eco_task_id', $task->id)
            ->where('user_id', $user->id)
            ->whereDate('completed_on', today())
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Bugun bajarilgan.'], 422);
        }

        $completion = EcoTaskCompletion::create([
            'eco_task_id' => $task->id,
            'user_id' => $user->id,
            'proof_text' => $data['proof_text'],
            'completed_on' => today(),
            'status' => $task->requires_proof ? 'pending' : 'approved',
        ]);

        if (! $task->requires_proof) {
            app(GreenCoinService::class)->credit(
                $user,
                $task->reward,
                "Eko vazifa: {$task->title}",
                reference: $completion
            );
        }

        return response()->json([
            'message' => $completion->status === ModerationStatus::Approved
                ? "+{$task->reward} GreenCoin qo‘shildi"
                : 'Tasdiqlash kutilmoqda',
        ]);
    }

    private function userFromInitData(Request $request): ?User
    {
        $initData = $request->header('X-Telegram-Init-Data') ?? (string) $request->input('init_data', '');

        $chatId = $this->chatIdFromInitData($initData);

        return $chatId ? TelegramUser::where('chat_id', $chatId)->with('user')->first()?->user : null;
    }

    private function requireUser(Request $request): User
    {
        $user = $this->userFromInitData($request);

        abort_if($user === null, 403, 'initData tasdiqlanmadi.');

        return $user;
    }

    private function chatIdFromInitData(string $initData): ?string
    {
        $userData = $this->telegram->validateInitData($initData);

        return $userData ? (string) ($userData['id'] ?? '') : null;
    }
}
