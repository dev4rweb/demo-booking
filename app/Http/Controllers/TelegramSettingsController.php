<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesTenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class TelegramSettingsController extends Controller
{
    use ResolvesTenant;

    public function index(Request $request): Response
    {
        $tenant = $this->currentTenant($request);
        abort_unless($request->user()?->isOwnerOf($tenant), 403);

        return Inertia::render('Settings/Telegram', [
            'connected' => (bool) $tenant->telegram_chat_id,
            'chatId' => $tenant->telegram_chat_id,
            'connectToken' => $tenant->telegram_connect_token,
            'botUsername' => config('services.telegram.bot_username'),
            'enabled' => telegram_enabled(),
        ]);
    }

    public function connect(Request $request): RedirectResponse
    {
        $tenant = $this->currentTenant($request);
        abort_unless($request->user()?->isOwnerOf($tenant), 403);

        $tenant->update([
            'telegram_connect_token' => Str::lower(Str::random(24)),
        ]);

        return back()->with('success', __('app.telegram.token_ready'));
    }

    public function disconnect(Request $request): RedirectResponse
    {
        $tenant = $this->currentTenant($request);
        abort_unless($request->user()?->isOwnerOf($tenant), 403);

        $tenant->update([
            'telegram_chat_id' => null,
            'telegram_connect_token' => null,
        ]);

        return back()->with('success', __('app.common.saved'));
    }
}
