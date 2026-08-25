<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Thin, testable wrapper around the Telegram Bot API.
 * All outbound calls are guarded — a broken bot must never break the web app.
 */
class TelegramService
{
    private const API = 'https://api.telegram.org/bot{token}/{method}';

    private string $token;

    public function __construct(string $token = '')
    {
        $this->token = $token ?: (string) config('services.telegram.bot_token');
    }

    public function enabled(): bool
    {
        return $this->token !== '' && $this->token !== 'null';
    }

    public function token(): string
    {
        return $this->token;
    }

    public function sendMessage(int|string $chatId, string $text, ?array $keyboard = null): ?array
    {
        return $this->call('sendMessage', array_filter([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboard ? json_encode($keyboard) : null,
        ]));
    }

    public function editMessage(int|string $chatId, int $messageId, string $text, ?array $keyboard = null): ?array
    {
        return $this->call('editMessageText', array_filter([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboard ? json_encode($keyboard) : null,
        ]));
    }

    public function sendPhoto(int|string $chatId, string $photoUrl, string $caption = ''): ?array
    {
        return $this->call('sendPhoto', [
            'chat_id' => $chatId,
            'photo' => $photoUrl,
            'caption' => $caption,
            'parse_mode' => 'HTML',
        ]);
    }

    public function answerCallbackQuery(string $callbackQueryId, string $text = ''): ?array
    {
        return $this->call('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
        ]);
    }

    public function setWebhook(string $url, string $secret): ?array
    {
        return $this->call('setWebhook', [
            'url' => $url,
            'secret_token' => $secret,
            'allowed_updates' => json_encode(['message', 'callback_query', 'my_chat_member']),
        ]);
    }

    public function deleteWebhook(): ?array
    {
        return $this->call('deleteWebhook', []);
    }

    public function getMe(): ?array
    {
        return $this->call('getMe', []);
    }

    public function setMyCommands(array $commands): ?array
    {
        return $this->call('setMyCommands', ['commands' => json_encode($commands)]);
    }

    public function getChatMember(int|string $chatId, int|string $userId): ?array
    {
        return $this->call('getChatMember', [
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);
    }

    /**
     * Validates Telegram WebApp initData (HMAC-SHA256, per Bot API docs).
     */
    public function validateInitData(string $initData): ?array
    {
        if ($initData === '' || ! $this->enabled()) {
            return null;
        }

        parse_str($initData, $params);
        $hash = $params['hash'] ?? null;
        unset($params['hash'], $params['signature']);

        if (! $hash) {
            return null;
        }

        ksort($params);
        $dataCheckString = collect($params)
            ->map(fn ($value, $key) => "{$key}={$value}")
            ->implode("\n");

        $secretKey = hash_hmac('sha256', 'WebAppData', $this->token, true);
        $computed = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (! hash_equals($computed, $hash)) {
            return null;
        }

        $user = json_decode((string) ($params['user'] ?? '{}'), true);

        return is_array($user) ? $user : null;
    }

    private function call(string $method, array $payload): ?array
    {
        if (! $this->enabled()) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->post(str_replace(['{token}', '{method}'], [$this->token, $method], self::API), $payload);

            $json = $response->json();

            if (! $response->successful() || ! ($json['ok'] ?? false)) {
                Log::warning('Telegram API error', [
                    'method' => $method,
                    'response' => $json,
                ]);

                return null;
            }

            return $json['result'] ?? null;
        } catch (Throwable $e) {
            Log::warning('Telegram API exception', [
                'method' => $method,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
