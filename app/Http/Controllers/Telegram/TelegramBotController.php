<?php

declare(strict_types=1);

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\TelegramBotSubmission;
use App\Models\TelegramReferral;
use App\Models\TelegramUser;
use App\Models\User;
use App\Services\GreenCoinService;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Throwable;

class TelegramBotController extends Controller
{
    public function __construct(private readonly TelegramService $telegram)
    {
    }

    public function webhook(Request $request): JsonResponse
    {
        $secret = config('services.telegram.webhook_secret');

        if ($secret && $request->header('X-Telegram-Bot-Api-Secret-Token') !== $secret) {
            abort(403);
        }

        $update = $request->json()->all() ?: $request->all();

        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        } elseif (isset($update['callback_query'])) {
            $this->handleCallback($update['callback_query']);
        }

        return response()->json(['ok' => true]);
    }

    private function handleMessage(array $message): void
    {
        try {
            $chatId = (string) ($message['chat']['id'] ?? '');
            $text = trim((string) ($message['text'] ?? ''));
            $from = $message['from'] ?? [];

            $tgUser = TelegramUser::updateOrCreate(
                ['chat_id' => $chatId],
                [
                    'first_name' => $from['first_name'] ?? null,
                    'last_name' => $from['last_name'] ?? null,
                    'username' => $from['username'] ?? null,
                    'last_interaction_at' => now(),
                ]
            );

            if ($text === '') {
                return;
            }

            if ($text === '/start') {
                $this->cmdStart($chatId, $tgUser);

                return;
            }

            if (str_starts_with($text, '/')) {
                $this->cmdMenu($chatId, $tgUser);
                return;
            }

            // Phone number capture
            if ($message['contact'] ?? null) {
                $this->attachPhone($tgUser, (string) ($message['contact']['phone_number'] ?? ''));
                return;
            }

            if (preg_match('/^\+?\d{9,15}$/', $text)) {
                $this->attachPhone($tgUser, $text);
                return;
            }

            $this->cmdMenu($chatId, $tgUser);
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function handleCallback(array $callback): void
    {
        try {
            $data = $callback['data'] ?? '';

            if (str_starts_with($data, 'approve:')) {
                $this->reviewSubmission((int) substr($data, 8), true);
            } elseif (str_starts_with($data, 'reject:')) {
                $this->reviewSubmission((int) substr($data, 8), false);
            }

            $this->telegram->answerCallbackQuery((string) ($callback['id'] ?? ''));
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function cmdStart(string $chatId, TelegramUser $tgUser): void
    {
        $this->telegram->sendMessage($chatId, $this->heroMessage($tgUser), [
            'inline_keyboard' => [[
                ['text' => '🌐 Web App', 'web_app' => ['url' => url('/tg-app')]],
            ], [
                ['text' => '💼 Ish e’lonlari', 'url' => url('/jobs')],
                ['text' => '🌱 Ekologiya', 'url' => url('/eco')],
            ]],
        ]);
    }

    private function cmdMenu(string $chatId, TelegramUser $tgUser): void
    {
        $this->telegram->sendMessage($chatId, 'Bosh menyu. Nima kerak?', [
            'inline_keyboard' => [
                [['text' => '💼 Ish e’lonlari', 'url' => url('/jobs')], ['text' => '🎓 Kurslar', 'url' => url('/academy')]],
                [['text' => '🌱 Eco vazifalar', 'url' => url('/eco')], ['text' => '🪙 GreenCoin', 'url' => url('/greencoin')]],
                [['text' => '🌐 Web App', 'web_app' => ['url' => url('/tg-app')]]],
            ],
        ]);
    }

    private function attachPhone(TelegramUser $tgUser, string $phone): void
    {
        $normalized = preg_replace('/[^0-9]/', '', $phone) ?? '';

        $tgUser->update(['phone' => $normalized]);

        $user = User::where('phone', $normalized)->first();

        if ($user) {
            $tgUser->update(['user_id' => $user->id]);
            $this->telegram->sendMessage(
                $tgUser->chat_id,
                "✅ Raqam ulandi, <b>{$user->name}</b>. Endi saytga kirishingiz mumkin!"
            );

            return;
        }

        $this->telegram->sendMessage(
            $tgUser->chat_id,
            "📱 Raqam qabul qilindi. Saytda ro‘yxatdan o‘tib shu raqamni kiritsangiz, hisob avtomatik ulanadi."
        );
    }

    private function reviewSubmission(int $submissionId, bool $approved): void
    {
        $submission = TelegramBotSubmission::with('task', 'user')->find($submissionId);

        if (! $submission || $submission->status !== 'pending') {
            return;
        }

        $submission->update([
            'status' => $approved ? 'approved' : 'rejected',
            'reviewed_at' => now(),
        ]);

        if (! $approved) {
            $this->telegram->sendMessage($submission->telegram_chat_id ?? '', '❌ Arizangiz rad etildi.');

            return;
        }

        $reward = (int) ($submission->task?->reward ?? 0);
        if ($reward > 0) {
            app(GreenCoinService::class)->credit(
                $submission->user,
                $reward,
                "Bot vazifasi: {$submission->task?->title}",
                reference: $submission
            );
        }

        $this->telegram->sendMessage(
            $submission->telegram_chat_id ?? '',
            "✅ Arizangiz tasdiqlandi! {$reward} GreenCoin qo‘shildi.",
        );
    }

    private function heroMessage(TelegramUser $tgUser): string
    {
        $name = $tgUser->first_name ?: 'do‘stim';

        return "Assalomu alaykum, <b>{$name}</b>! 👋\n\n"
            ."<b>AMBARELLA</b> — O‘zbekiston yoshlari uchun platforma.\n"
            ."💼 Ish, 🎓 kurslar, 🌱 eko-vazifalar va 🪙 GreenCoin — barchasi bitta joyda.\n\n"
            ."🎫 Kanalga obuna bo‘lish: t.me/ambarella_uz";
    }
}
