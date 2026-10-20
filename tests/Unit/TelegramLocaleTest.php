<?php

test('telegram_locale maps language_code to a supported locale', function (mixed $code, string $expected) {
    expect(telegram_locale($code))->toBe($expected);
})->with([
    ['en', 'en'],
    ['en-US', 'en'],
    ['zh-hans', 'zh'],
    ['RU', 'ru'],
    ['pt_BR', 'pt'],
    ['fr-FR', 'fr'],
    ['es', 'es'],
    ['ar', 'ar'],
    ['de', 'en'],
    [null, 'en'],
    ['', 'en'],
    [123, 'en'],
]);
