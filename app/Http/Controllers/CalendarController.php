<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesTenant;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller
{
    use ResolvesTenant;

    public function index(Request $request): Response
    {
        $tenant = $this->currentTenant($request);
        $user = $request->user();
        $role = $user->roleIn($tenant);
        $view = $request->string('view')->toString() === 'day' ? 'day' : 'week';
        $date = Carbon::parse($request->get('date', now($tenant->timezone)->toDateString()), $tenant->timezone);

        $rangeStart = $view === 'day' ? $date->copy()->startOfDay() : $date->copy()->startOfWeek();
        $rangeEnd = $view === 'day' ? $date->copy()->endOfDay() : $date->copy()->endOfWeek();

        $query = Booking::query()
            ->with(['service', 'staffMember', 'client'])
            ->where('tenant_id', $tenant->id)
            ->where('starts_at', '<', $rangeEnd->copy()->utc())
            ->where('ends_at', '>', $rangeStart->copy()->utc())
            ->where('status', '!=', Booking::STATUS_CANCELLED);

        if ($role === 'staff') {
            $query->where('staff_member_id', $user->staffMember?->id);
        }

        $events = $query->orderBy('starts_at')->get()->map(fn (Booking $booking): array => [
            'id' => $booking->id,
            'starts_at' => $booking->starts_at->timezone($tenant->timezone)->toIso8601String(),
            'ends_at' => $booking->ends_at->timezone($tenant->timezone)->toIso8601String(),
            'service' => $booking->service->name,
            'staff' => $booking->staffMember->name,
            'staff_member_id' => $booking->staff_member_id,
            'client' => $booking->client->name,
            'status' => $booking->status,
            'price_cents' => $booking->service->price_cents,
        ]);

        return Inertia::render('Calendar/Index', [
            'view' => $view,
            'date' => $date->toDateString(),
            'timezone' => $tenant->timezone,
            'events' => $events,
            'staff' => $role === 'staff' ? [] : $tenant->staffMembers()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
