<?php

test('how-to copy stays user-facing in every locale', function () {
    $banned = '/MAIL_MAILER|TELEGRAM_BOT_TOKEN|Stripe keys empty|STRIPE_/i';

    foreach (['en', 'zh', 'fr', 'es', 'ar', 'pt', 'ru'] as $locale) {
        $guide = trans('app.guide', [], $locale);
        $blob = implode(' ', [
            is_array($guide) ? implode(' ', $guide) : $guide,
            trans('app.booking.success_body', [], $locale),
            trans('app.billing.demo_notice', [], $locale),
            trans('app.telegram.disabled', [], $locale),
            trans('app.telegram.hint', [], $locale),
        ]);

        expect($blob)->not->toMatch($banned)
            ->and(trans('app.guide.client_manage', [], $locale))->not->toBe('app.guide.client_manage')
            ->and(trans('app.guide.owner_telegram', [], $locale))->not->toBe('app.guide.owner_telegram');
    }
});
