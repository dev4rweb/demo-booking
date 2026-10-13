<?php

test('welcome page is english by default', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Welcome')
            ->where('locale', 'en')
            ->where('dir', 'ltr')
        );
});

test('locale prefix switches language and rtl', function () {
    $this->get('/ar')->assertOk()->assertInertia(fn ($page) => $page
        ->component('Welcome')
        ->where('locale', 'ar')
        ->where('dir', 'rtl')
    );

    $this->get('/fr/how-to')->assertOk()->assertInertia(fn ($page) => $page
        ->component('Guide')
        ->where('locale', 'fr')
    );
});

test('hidden russian is available by direct url', function () {
    $this->get('/ru')->assertOk()->assertInertia(fn ($page) => $page
        ->where('locale', 'ru')
    );
});

test('language selector does not include russian', function () {
    $this->get('/')->assertInertia(fn ($page) => $page
        ->where('locales', fn ($locales) => collect($locales)->pluck('code')->doesntContain('ru')
            && collect($locales)->pluck('code')->contains('en'))
    );
});

test('accept language can select french but not russian', function () {
    $this->withHeader('Accept-Language', 'fr-FR,fr;q=0.9')
        ->get('/')
        ->assertInertia(fn ($page) => $page->where('locale', 'fr'));

    $this->withHeader('Accept-Language', 'ru-RU,ru;q=0.9,en;q=0.8')
        ->get('/')
        ->assertInertia(fn ($page) => $page->where('locale', 'en'));
});
