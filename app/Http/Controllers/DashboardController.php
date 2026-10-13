<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesTenant;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    use ResolvesTenant;

    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_unless($user, 401);

        if ($user->isPlatformAdmin() && ! $user->currentTenant()) {
            return Inertia::render('Admin/Tenants', [
                'tenants' => \App\Models\Tenant::query()->withCount(['staffMembers', 'bookings'])->orderBy('name')->get(),
            ]);
        }

        $tenant = $user->currentTenant();

        if (! $tenant) {
            return Inertia::render('Dashboard', [
                'stats' => [
                    'week_bookings' => 0,
                    'revenue_cents' => 0,
                    'no_shows' => 0,
                    'plan' => 'free',
                ],
                'upcoming' => [],
                'publicUrl' => locale_route('welcome'),
            ]);
        }
        $role = $user->roleIn($tenant);
        $weekStart = now($tenant->timezone)->startOfWeek();
        $weekEnd = now($tenant->timezone)->endOfWeek();

        $bookings = Booking::query()
            ->where('bookings.tenant_id', $tenant->id)
            ->when($role === 'staff', fn ($q) => $q->where('bookings.staff_member_id', $user->staffMember?->id))
            ->where('bookings.status', Booking::STATUS_CONFIRMED)
            ->whereBetween('bookings.starts_at', [$weekStart->copy()->utc(), $weekEnd->copy()->utc()]);

        $weekList = (clone $bookings)->with('service')->get();
        $weekBookings = $weekList->count();
        $revenue = $weekList->sum(fn (Booking $booking): int => (int) $booking->service->price_cents);

        $noShows = Booking::query()
            ->where('tenant_id', $tenant->id)
            ->when($role === 'staff', fn ($q) => $q->where('staff_member_id', $user->staffMember?->id))
            ->where('status', Booking::STATUS_NO_SHOW)
            ->where('starts_at', '>=', now()->subDays(30))
            ->count();

        $upcoming = Booking::query()
            ->with(['service', 'staffMember', 'client'])
            ->where('tenant_id', $tenant->id)
            ->when($role === 'staff', fn ($q) => $q->where('staff_member_id', $user->staffMember?->id))
            ->where('status', Booking::STATUS_CONFIRMED)
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->limit(8)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'week_bookings' => $weekBookings,
                'revenue_cents' => (int) $revenue,
                'no_shows' => $noShows,
                'plan' => $tenant->plan,
            ],
            'upcoming' => $upcoming->map(fn (Booking $booking): array => $this->bookingCard($booking)),
            'publicUrl' => locale_route('booking.show', ['slug' => $tenant->slug]),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function bookingCard(Booking $booking): array
    {
        return [
            'id' => $booking->id,
            'starts_at' => $booking->starts_at->timezone($booking->timezone)->toIso8601String(),
            'service' => $booking->service->name,
            'staff' => $booking->staffMember->name,
            'client' => $booking->client->name,
            'status' => $booking->status,
        ];
    }
}
