<?php

return [

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'bot_username' => env('TELEGRAM_BOT_USERNAME', 'AmbarellaBot'),
        'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
        'admin_chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
        'required_channel' => env('TELEGRAM_REQUIRED_CHANNEL'),
        'channel_url' => env('TELEGRAM_CHANNEL_URL'),
        'webhook_url' => env('TELEGRAM_WEBHOOK_URL', '/webhook/telegram'),
    ],

];
