<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class SetTelegramWebhook extends Command
{
    protected $signature = 'telegram:set-webhook';

    protected $description = 'Telegram bot webhook va buyruqlarini o‘rnatish';

    public function handle(TelegramService $telegram): int
    {
        if (! $telegram->enabled()) {
            $this->error('TELEGRAM_BOT_TOKEN sozlanmagan.');

            return self::FAILURE;
        }

        $url = config('app.url').config('services.telegram.webhook_url', '/webhook/telegram');
        $secret = (string) config('services.telegram.webhook_secret');

        if ($secret === '') {
            $this->error('TELEGRAM_WEBHOOK_SECRET sozlanmagan.');

            return self::FAILURE;
        }

        $result = $telegram->setWebhook($url, $secret);

        if ($result === null) {
            $this->error('Webhook o‘rnatilmadi — API javobi salbiy.');

            return self::FAILURE;
        }

        $telegram->setMyCommands([
            ['command' => 'start', 'description' => 'Botni ishga tushirish'],
            ['command' => 'menu', 'description' => 'Asosiy menyu'],
        ]);

        $this->info("Webhook o‘rnatildi: {$url}");

        return self::SUCCESS;
    }
}
