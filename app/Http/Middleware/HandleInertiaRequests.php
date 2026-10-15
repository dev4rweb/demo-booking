<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $locale = app()->getLocale();
        $rtl = config('localization.rtl', ['ar']);
        $hidden = config('localization.hidden_in_selector', []);

        $selectorLocales = collect(config('localization.supported', []))
            ->reject(fn (string $code): bool => in_array($code, $hidden, true))
            ->map(fn (string $code): array => [
                'code' => $code,
                'name' => config("localization.names.$code", $code),
                'flag' => config("localization.flag_codes.$code"),
            ])
            ->values()
            ->all();

        $user = $request->user();
        $tenant = $user?->currentTenant();
        $role = $user?->isPlatformAdmin()
            ? 'platform_admin'
            : $user?->roleIn($tenant);

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_platform_admin' => $user->isPlatformAdmin(),
                    'role' => $role,
                ] : null,
            ],
            'tenant' => $tenant ? [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'slug' => $tenant->slug,
                'plan' => $tenant->plan,
                'timezone' => $tenant->timezone,
            ] : null,
            'demoMode' => ! stripe_enabled(),
            'telegramEnabled' => telegram_enabled(),
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'locale' => $locale,
            'routeLocale' => $request->route('locale'),
            'dir' => in_array($locale, $rtl, true) ? 'rtl' : 'ltr',
            'translations' => trans('app'),
            'locales' => $selectorLocales,
        ];
    }
}
