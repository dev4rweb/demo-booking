<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class SetTelegramWebhook extends Command
{
    protected $signature = 'telegram:set-webhook {--url= : Full HTTPS webhook URL}';

    protected $description = 'Register the Telegram bot webhook (secret from TELEGRAM_WEBHOOK_SECRET)';

    public function handle(TelegramService $telegram): int
    {
        if (! $telegram->enabled()) {
            $this->error('Telegram bot is not configured.');

            return self::FAILURE;
        }

        $url = $this->option('url') ?: rtrim((string) config('app.url'), '/').'/telegram/webhook';
        $result = $telegram->setWebhook(is_string($url) ? $url : null);

        if (! is_array($result) || empty($result['ok'])) {
            $this->error('Telegram did not accept the webhook.');

            return self::FAILURE;
        }

        $this->info('Webhook set: '.$url);

        return self::SUCCESS;
    }
}
