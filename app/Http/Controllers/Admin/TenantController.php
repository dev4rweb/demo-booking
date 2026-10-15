<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TenantController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->isPlatformAdmin(), 403);

        $tenants = Tenant::query()
            ->withCount(['staffMembers', 'bookings', 'clients'])
            ->orderBy('name')
            ->get()
            ->map(fn (Tenant $tenant): array => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'slug' => $tenant->slug,
                'plan' => $tenant->plan,
                'staff_members_count' => $tenant->staff_members_count,
                'bookings_count' => $tenant->bookings_count,
                'clients_count' => $tenant->clients_count,
                'booking_url' => locale_route('booking.show', ['slug' => $tenant->slug]),
            ]);

        return Inertia::render('Admin/Tenants', [
            'tenants' => $tenants,
        ]);
    }
}
