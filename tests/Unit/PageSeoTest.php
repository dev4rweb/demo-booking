<?php

test('page_seo falls back to the default meta copy', function () {
    $seo = page_seo();

    expect($seo['title'])->toBe(__('app.meta.title'))
        ->and($seo['description'])->toBe(__('app.meta.description'));
});

test('page_seo keeps a custom title and description', function () {
    $seo = page_seo('About Bookly', 'A short description.');

    expect($seo)->toMatchArray([
        'title' => 'About Bookly',
        'description' => 'A short description.',
    ]);
});
