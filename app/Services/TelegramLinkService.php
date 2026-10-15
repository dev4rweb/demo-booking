<?php

namespace App\Services;

use App\Models\StaffMember;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TelegramLinkService
{
    public function ensureToken(Model $target): ?string
    {
        if (! telegram_connect_ready()) {
            return null;
        }

        if ($target->telegram_chat_id) {
            return null;
        }

        if (! $target->telegram_connect_token) {
            $target->update([
                'telegram_connect_token' => Str::lower(Str::random(24)),
            ]);
        }

        return $target->telegram_connect_token;
    }

    public function deepLink(?string $token): ?string
    {
        if (! $token || ! telegram_connect_ready()) {
            return null;
        }

        $username = ltrim((string) config('services.telegram.bot_username'), '@');

        return 'https://t.me/'.$username.'?start='.$token;
    }

    public function connectChat(string $token): Tenant|StaffMember|null
    {
        $token = trim($token);

        if ($token === '') {
            return null;
        }

        $target = Tenant::query()->where('telegram_connect_token', $token)->first()
            ?? StaffMember::query()->where('telegram_connect_token', $token)->first();

        return $target;
    }

    /**
     * @return list<string>
     */
    public function chatsFor(\App\Models\Booking $booking): array
    {
        $chats = array_filter([
            $booking->tenant?->telegram_chat_id,
            $booking->staffMember?->telegram_chat_id,
        ]);

        return array_values(array_unique($chats));
    }
}
