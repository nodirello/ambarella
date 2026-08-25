<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Contact\StoreContactRequest;
use App\Models\ContactMessage;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request, TelegramService $telegram): RedirectResponse
    {
        $message = ContactMessage::create($request->safe()->all());

        if ($chatId = config('services.telegram.admin_chat_id')) {
            $telegram->sendMessage(
                $chatId,
                "✉️ <b>Yangi murojaat</b>\n👤 {$message->name} ({$message->email})\n📌 {$message->subject}\n\n{$message->message}"
            );
        }

        return back()->with('success', 'Murojaatingiz qabul qilindi. Tez orada javob beramiz.');
    }
}
