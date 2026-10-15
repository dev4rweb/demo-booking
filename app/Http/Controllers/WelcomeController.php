<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Welcome', [
            'githubUrl' => 'https://github.com/dev4rweb/demo-booking',
            'seo' => page_seo(__('app.welcome.meta_title'), __('app.welcome.meta_description')),
            'demoTenants' => Tenant::query()
                ->whereIn('slug', ['luna-beauty', 'peak-fitness'])
                ->get(['name', 'slug'])
                ->map(fn (Tenant $tenant): array => [
                    'name' => $tenant->name,
                    'slug' => $tenant->slug,
                    'url' => locale_route('booking.show', ['slug' => $tenant->slug]),
                ]),
        ]);
    }
}
