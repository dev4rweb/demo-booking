<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public function enabled(): bool
    {
        return telegram_enabled();
    }

    public function sendMessage(string $chatId, string $text): ?array
    {
        if (! $this->enabled()) {
            Log::info('Telegram skipped (no bot token)', ['chat_id' => $chatId, 'text' => $text]);

            return null;
        }

        $token = (string) config('services.telegram.bot_token');

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Telegram sendMessage failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Telegram sendMessage exception', ['message' => $e->getMessage()]);
        }

        return null;
    }
}
