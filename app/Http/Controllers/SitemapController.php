<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $default = (string) config('localization.default', 'en');
        $locales = config('localization.supported', ['en']);

        $paths = ['/', '/about', '/how-to', '/login'];
        $slugs = Tenant::query()
            ->whereIn('slug', ['luna-beauty', 'peak-fitness'])
            ->pluck('slug');

        $urls = [];

        foreach ($locales as $locale) {
            $prefix = $locale === $default ? '' : '/'.$locale;

            foreach ($paths as $path) {
                $urls[] = url($prefix === '' && $path === '/' ? '/' : $prefix.$path);
            }

            foreach ($slugs as $slug) {
                $urls[] = url($prefix.'/b/'.$slug);
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach (array_unique($urls) as $loc) {
            $xml .= '  <url><loc>'.e($loc).'</loc></url>'."\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
