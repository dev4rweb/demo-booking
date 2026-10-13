<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request, TelegramService $telegram): JsonResponse
    {
        $secret = (string) config('services.telegram.webhook_secret');
        if ($secret !== '') {
            abort_unless(hash_equals($secret, (string) $request->header('X-Telegram-Bot-Api-Secret-Token')), 401);
        }

        $text = (string) data_get($request->all(), 'message.text', '');
        $chatId = data_get($request->all(), 'message.chat.id');

        if (! $chatId || ! str_starts_with($text, '/start')) {
            return response()->json(['ok' => true]);
        }

        $token = trim(substr($text, 6));
        if ($token === '') {
            $telegram->sendMessage((string) $chatId, 'Send /start YOUR_TOKEN from the Bookly dashboard to connect this chat.');

            return response()->json(['ok' => true]);
        }

        $tenant = Tenant::query()->where('telegram_connect_token', $token)->first();

        if (! $tenant) {
            $telegram->sendMessage((string) $chatId, 'Unknown connect token.');

            return response()->json(['ok' => true]);
        }

        $tenant->update([
            'telegram_chat_id' => (string) $chatId,
            'telegram_connect_token' => null,
        ]);

        $telegram->sendMessage((string) $chatId, "Connected to {$tenant->name}. You will receive booking alerts here.");

        return response()->json(['ok' => true]);
    }
}
