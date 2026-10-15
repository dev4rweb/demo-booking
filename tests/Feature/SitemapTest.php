<?php

use App\Models\Tenant;

test('sitemap lists marketing pages locales and demo studios', function () {
    Tenant::factory()->create(['slug' => 'luna-beauty']);
    Tenant::factory()->create(['slug' => 'peak-fitness']);

    $response = $this->get('/sitemap.xml');

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

    $xml = $response->getContent();

    expect($xml)
        ->toContain('<urlset')
        ->toContain(url('/about'))
        ->toContain(url('/how-to'))
        ->toContain(url('/fr/about'))
        ->toContain(url('/ar/about'))
        ->toContain(url('/ru/about'))
        ->toContain(url('/b/luna-beauty'))
        ->toContain(url('/zh/b/peak-fitness'));
});

test('robots txt points crawlers at the sitemap', function () {
    $robots = file_get_contents(public_path('robots.txt'));

    expect($robots)
        ->toContain('Allow: /')
        ->toContain('Sitemap: https://demo-booking.dev4rweb.com/sitemap.xml');
});
