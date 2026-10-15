<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesTenant;
use App\Models\StaffMember;
use App\Models\Tenant;
use App\Services\TelegramLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TelegramSettingsController extends Controller
{
    use ResolvesTenant;

    public function index(Request $request, TelegramLinkService $links): Response
    {
        [$target, $scope] = $this->target($request);
        $token = $links->ensureToken($target);

        return Inertia::render('Settings/Telegram', [
            'connected' => (bool) $target->telegram_chat_id,
            'deepLink' => $links->deepLink($token),
            'enabled' => telegram_connect_ready(),
            'scope' => $scope,
        ]);
    }

    public function connect(Request $request, TelegramLinkService $links): RedirectResponse
    {
        [$target] = $this->target($request);

        $target->update(['telegram_connect_token' => null]);
        $token = $links->ensureToken($target->fresh());

        return back()->with('telegram_deep_link', $links->deepLink($token));
    }

    public function disconnect(Request $request): RedirectResponse
    {
        [$target] = $this->target($request);

        $target->update([
            'telegram_chat_id' => null,
            'telegram_connect_token' => null,
        ]);

        return back()->with('success', __('app.common.saved'));
    }

    /**
     * @return array{0: Tenant|StaffMember, 1: string}
     */
    private function target(Request $request): array
    {
        $tenant = $this->currentTenant($request);
        $user = $request->user();

        if ($user?->isOwnerOf($tenant)) {
            return [$tenant, 'business'];
        }

        $staff = $tenant->staffMembers()->where('user_id', $user?->id)->first();
        abort_unless($staff, 403);

        return [$staff, 'staff'];
    }
}
