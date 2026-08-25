<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TelegramWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_requires_secret_when_configured(): void
    {
        config(['services.telegram.webhook_secret' => 'super-secret']);

        $this->postJson('/webhook/telegram', ['message' => []])
            ->assertForbidden();

        $this->postJson('/webhook/telegram', ['message' => ['text' => '/start', 'chat' => ['id' => 123], 'from' => ['first_name' => 'Ali']]], [
            'X-Telegram-Bot-Api-Secret-Token' => 'super-secret',
        ])->assertOk();
    }

    public function test_webhook_accepts_unauthenticated_updates_when_secret_not_set(): void
    {
        config(['services.telegram.webhook_secret' => null]);

        $this->postJson('/webhook/telegram', [])->assertOk();
    }
}
