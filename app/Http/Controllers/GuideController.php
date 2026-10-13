<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Inertia\Inertia;
use Inertia\Response;

class GuideController extends Controller
{
    public function index(): Response
    {
        $luna = Tenant::query()->where('slug', 'luna-beauty')->first();

        return Inertia::render('Guide', [
            'bookingUrl' => $luna
                ? locale_route('booking.show', ['slug' => $luna->slug])
                : locale_route('welcome'),
        ]);
    }
}
