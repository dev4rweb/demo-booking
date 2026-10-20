<?php

namespace App\Http\Controllers;

use App\Services\TelegramLinkService;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request, TelegramService $telegram, TelegramLinkService $links): JsonResponse
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

        App::setLocale(telegram_locale(data_get($request->all(), 'message.from.language_code')));

        $token = $this->startPayload($text);
        if ($token === '') {
            $telegram->sendMessage(
                (string) $chatId,
                __('app.telegram.bot_welcome', ['connect' => __('app.telegram.connect')]),
                [
                    'inline_keyboard' => [[
                        [
                            'text' => __('app.telegram.bot_open_demo'),
                            'url' => 'https://demo-booking.dev4rweb.com',
                        ],
                        [
                            'text' => __('app.telegram.bot_open_site'),
                            'url' => 'https://dev4rweb.com',
                        ],
                    ]],
                ],
            );

            return response()->json(['ok' => true]);
        }

        $target = $links->connectChat($token);

        if (! $target) {
            $telegram->sendMessage((string) $chatId, __('app.telegram.bot_unknown'));

            return response()->json(['ok' => true]);
        }

        $target->update([
            'telegram_chat_id' => (string) $chatId,
            'telegram_connect_token' => null,
        ]);

        $telegram->sendMessage((string) $chatId, __('app.telegram.bot_connected', ['name' => $target->name]));

        return response()->json(['ok' => true]);
    }

    private function startPayload(string $text): string
    {
        $token = trim(substr($text, strlen('/start')));

        if (str_starts_with($token, '@')) {
            $token = trim((string) preg_replace('/^@\S+/', '', $token));
        }

        return $token;
    }
}
