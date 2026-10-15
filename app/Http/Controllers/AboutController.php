<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('About', [
            'githubUrl' => 'https://github.com/dev4rweb/demo-booking',
            'studioUrl' => 'https://dev4rweb.com',
            'seo' => page_seo(__('app.about.meta_title'), __('app.about.meta_description')),
            'demoTenants' => Tenant::query()
                ->whereIn('slug', ['luna-beauty', 'peak-fitness'])
                ->get(['name', 'slug'])
                ->map(fn (Tenant $tenant): array => [
                    'name' => $tenant->name,
                    'slug' => $tenant->slug,
                ]),
        ]);
    }
}
