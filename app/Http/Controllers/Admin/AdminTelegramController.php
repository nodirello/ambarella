<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TelegramBotSubmission;
use App\Models\TelegramBotTask;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\GreenCoinService;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTelegramController extends Controller
{
    public function index(): View
    {
        return view('admin.telegram.dashboard', [
            'stats' => [
                'users' => \App\Models\TelegramUser::count(),
                'linked' => \App\Models\TelegramUser::whereNotNull('user_id')->count(),
                'tasks' => TelegramBotTask::count(),
                'pending' => TelegramBotSubmission::where('status', 'pending')->count(),
                'referrals' => \App\Models\TelegramReferral::where('status', 'pending')->count(),
            ],
        ]);
    }

    public function submissions(): View
    {
        return view('admin.telegram.submissions', [
            'submissions' => TelegramBotSubmission::with(['user', 'task'])->latest()->paginate(20),
        ]);
    }

    public function tasks(): View
    {
        return view('admin.telegram.tasks', [
            'tasks' => TelegramBotTask::withCount('submissions')->latest()->get(),
        ]);
    }

    public function storeTask(Request $request, ActivityLogger $activity): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:2000'],
            'reward' => ['required', 'integer', 'min:1', 'max:100000'],
            'type' => ['required', 'in:subscribe,referral,eco,photo,text'],
            'channel_url' => ['nullable', 'url'],
        ]);

        TelegramBotTask::create($data);
        $activity->log($request->user(), 'telegram_task', "Bot vazifasi: {$data['title']}");

        return back()->with('success', 'Bot vazifasi yaratildi.');
    }

    public function toggleTask(TelegramBotTask $task): RedirectResponse
    {
        $task->update(['is_active' => ! $task->is_active]);

        return back();
    }

    public function approveSubmission(TelegramBotSubmission $submission, GreenCoinService $coins, TelegramService $telegram): RedirectResponse
    {
        abort_if($submission->status !== 'pending', 422, 'Ariza ko‘rib chiqilgan.');

        $submission->update([
            'status' => 'approved',
            'reviewed_by' => request()->user()->id,
            'reviewed_at' => now(),
        ]);

        $reward = (int) ($submission->task?->reward ?? 0);
        if ($reward > 0) {
            $coins->credit($submission->user, $reward, "Bot vazifasi: {$submission->task?->title}", reference: $submission);
            $telegram->sendMessage(
                $submission->telegram_chat_id,
                "✅ Vazifangiz tasdiqlandi! +{$reward} GreenCoin hisobingizga qo‘shildi."
            );
        }

        return back()->with('success', 'Ariza tasdiqlandi.');
    }

    public function rejectSubmission(TelegramBotSubmission $submission, TelegramService $telegram): RedirectResponse
    {
        abort_if($submission->status !== 'pending', 422, 'Ariza ko‘rib chiqilgan.');

        $submission->update([
            'status' => 'rejected',
            'reviewed_by' => request()->user()->id,
            'reviewed_at' => now(),
        ]);

        $telegram->sendMessage($submission->telegram_chat_id, '❌ Arizangiz rad etildi. Batafsil ma’lumot uchun @AmbarellaBot ga yozing.');

        return back()->with('success', 'Ariza rad etildi.');
    }

    public function broadcast(Request $request, TelegramService $telegram, ActivityLogger $activity): RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $sent = 0;
        \App\Models\TelegramUser::whereNotNull('chat_id')->chunkById(200, function ($users) use (&$sent, $telegram, $data): void {
            foreach ($users as $tgUser) {
                if ($telegram->sendMessage($tgUser->chat_id, $data['message']) !== null) {
                    $sent++;
                }
            }
        });

        $activity->log($request->user(), 'telegram_broadcast', "Broadcast: {$sent} ta foydalanuvchiga yuborildi");

        return back()->with('success', "{$sent} ta foydalanuvchiga xabar yuborildi.");
    }

    public function setWebhook(TelegramService $telegram, ActivityLogger $activity): RedirectResponse
    {
        $url = config('app.url').config('services.telegram.webhook_url', '/webhook/telegram');
        $secret = config('services.telegram.webhook_secret');

        abort_if(! $secret, 422, 'TELEGRAM_WEBHOOK_SECRET sozlanmagan.');

        $result = $telegram->setWebhook($url, $secret);

        abort_if($result === null, 422, 'Webhook o‘rnatilmadi — bot tokenini tekshiring.');

        $activity->log(request()->user(), 'telegram_webhook', "Webhook o‘rnatildi: {$url}");

        return back()->with('success', 'Webhook muvaffaqiyatli o‘rnatildi.');
    }
}
