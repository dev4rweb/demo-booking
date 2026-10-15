<?php

use App\Models\Tenant;

test('about page renders with product copy and demo studios', function () {
    Tenant::factory()->create(['name' => 'Luna Beauty Studio', 'slug' => 'luna-beauty']);
    Tenant::factory()->create(['name' => 'Peak Fitness', 'slug' => 'peak-fitness']);

    $this->get('/about')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('About')
            ->where('locale', 'en')
            ->where('seo.title', __('app.about.meta_title'))
            ->where('seo.description', __('app.about.meta_description'))
            ->where('studioUrl', 'https://dev4rweb.com')
            ->has('demoTenants', 2)
            ->where('demoTenants.0.slug', 'luna-beauty')
        );
});

test('about page is available in public locales including hidden russian', function () {
    $this->get('/fr/about')->assertOk()->assertInertia(fn ($page) => $page
        ->component('About')
        ->where('locale', 'fr')
        ->where('dir', 'ltr')
        ->where('seo.title', trans('app.about.meta_title', [], 'fr'))
    );

    $this->get('/ar/about')->assertOk()->assertInertia(fn ($page) => $page
        ->component('About')
        ->where('locale', 'ar')
        ->where('dir', 'rtl')
    );

    $this->get('/ru/about')->assertOk()->assertInertia(fn ($page) => $page
        ->component('About')
        ->where('locale', 'ru')
    );
});

test('about html includes per-page title description and social tags', function () {
    $html = $this->get('/about')->assertOk()->getContent();

    expect($html)
        ->toContain('About Bookly')
        ->toContain('Bookly helps small businesses take bookings online')
        ->toContain('property="og:title"')
        ->toContain('name="twitter:title"')
        ->toContain('name="description"');
});

test('welcome and how-to pages expose their own seo tags', function () {
    $this->get('/')->assertOk()->assertInertia(fn ($page) => $page
        ->where('seo.title', __('app.welcome.meta_title'))
    );

    $this->get('/how-to')->assertOk()->assertInertia(fn ($page) => $page
        ->where('seo.title', __('app.guide.meta_title'))
        ->where('seo.description', __('app.guide.meta_description'))
    );
});

test('booking phone hint is international and never mentions ukrainian', function () {
    foreach (['en', 'zh', 'fr', 'es', 'ar', 'pt', 'ru'] as $locale) {
        $hint = trans('app.booking.phone_hint', [], $locale);
        $confirm = trans('app.guide.client_confirm', [], $locale);

        expect($hint)->toContain('+380')
            ->and($hint.$confirm)->not->toMatch('/ukrain/i');
    }
});

test('public booking page uses the studio name in seo', function () {
    $tenant = Tenant::factory()->create(['name' => 'Luna Beauty Studio', 'slug' => 'luna-beauty']);

    $this->get('/b/luna-beauty')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('seo.title', 'Luna Beauty Studio')
            ->where('seo.description', __('app.booking.meta_description', ['name' => $tenant->name]))
        );
});
