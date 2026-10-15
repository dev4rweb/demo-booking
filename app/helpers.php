<?php

if (! function_exists('locale_route')) {
    /**
     * Named route that keeps the current locale prefix when present.
     *
     * @param  array<string, mixed>  $params
     */
    function locale_route(string $name, array $params = [], bool $absolute = true): string
    {
        $locale = request()->route('locale');

        if (is_string($locale) && $locale !== '') {
            return route('locale.'.$name, ['locale' => $locale] + $params, $absolute);
        }

        return route($name, $params, $absolute);
    }
}

if (! function_exists('stripe_enabled')) {
    function stripe_enabled(): bool
    {
        $key = (string) config('cashier.key');
        $secret = (string) config('cashier.secret');

        return $key !== '' && $secret !== '' && ! str_contains($key, 'your-') && ! str_contains($secret, 'your-');
    }
}

if (! function_exists('telegram_enabled')) {
    function telegram_enabled(): bool
    {
        $token = (string) config('services.telegram.bot_token');

        return $token !== '' && $token !== 'your_bot_token_here';
    }
}
