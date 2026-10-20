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

    /**
     * @param  array<string, mixed>|null  $replyMarkup
     */
    public function sendMessage(string $chatId, string $text, ?array $replyMarkup = null): ?array
    {
        if (! $this->enabled()) {
            Log::info('Telegram skipped (no bot token)', ['chat_id' => $chatId, 'text' => $text]);

            return null;
        }

        $token = (string) config('services.telegram.bot_token');

        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($replyMarkup !== null) {
            $payload['reply_markup'] = json_encode($replyMarkup, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", $payload);

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

    /**
     * @return array<string, mixed>|null
     */
    public function setWebhook(?string $url = null): ?array
    {
        if (! $this->enabled()) {
            return null;
        }

        $token = (string) config('services.telegram.bot_token');
        $endpoint = $url ?: rtrim((string) config('app.url'), '/').'/telegram/webhook';
        $secret = (string) config('services.telegram.webhook_secret');

        $payload = ['url' => $endpoint];

        if ($secret !== '') {
            $payload['secret_token'] = $secret;
        }

        $response = Http::post("https://api.telegram.org/bot{$token}/setWebhook", $payload);

        if (! $response->successful()) {
            Log::warning('Telegram setWebhook failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }

        return $response->json();
    }
}
