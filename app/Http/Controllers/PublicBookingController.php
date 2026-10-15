<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\StaffMember;
use App\Models\Tenant;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicBookingController extends Controller
{
    public function show(string $slug): Response
    {
        $tenant = $this->tenant($slug);

        $services = $tenant->services()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'duration_minutes', 'price_cents']);

        $staff = $tenant->staffMembers()
            ->where('is_active', true)
            ->with(['services:id'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Booking/Show', [
            'seo' => page_seo(
                $tenant->name,
                __('app.booking.meta_description', ['name' => $tenant->name]),
            ),
            'business' => [
                'name' => $tenant->name,
                'slug' => $tenant->slug,
                'timezone' => $tenant->timezone,
            ],
            'services' => $services,
            'staff' => $staff->map(fn (StaffMember $member): array => [
                'id' => $member->id,
                'name' => $member->name,
                'service_ids' => $member->services->pluck('id')->all(),
            ]),
        ]);
    }

    public function slots(Request $request, string $slug, AvailabilityService $availability): JsonResponse
    {
        $tenant = $this->tenant($slug);

        $data = $request->validate([
            'service_id' => ['required', 'integer'],
            'staff_member_id' => ['nullable', 'integer'],
            'date' => ['required', 'date'],
        ]);

        $service = Service::query()
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->findOrFail($data['service_id']);

        $date = Carbon::parse($data['date'], $tenant->timezone);

        $staffQuery = $tenant->staffMembers()->where('is_active', true)->whereHas('services', fn ($q) => $q->where('services.id', $service->id));

        if (! empty($data['staff_member_id'])) {
            $staff = $staffQuery->whereKey($data['staff_member_id'])->get();
        } else {
            $staff = $staffQuery->get();
        }

        $slots = $availability->slotsForAny($staff, $service, $date, $tenant->timezone);

        return response()->json(['slots' => $slots]);
    }

    public function store(Request $request, string $slug, BookingService $bookings): RedirectResponse
    {
        $tenant = $this->tenant($slug);

        $data = $request->validate([
            'service_id' => ['required', 'integer'],
            'staff_member_id' => ['required', 'integer'],
            'starts_at' => ['required', 'date'],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:120'],
        ]);

        $service = Service::query()->where('tenant_id', $tenant->id)->findOrFail($data['service_id']);
        $staff = StaffMember::query()->where('tenant_id', $tenant->id)->findOrFail($data['staff_member_id']);

        $booking = $bookings->book(
            $tenant,
            $service,
            $staff,
            Carbon::parse($data['starts_at']),
            [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
            ],
        );

        return redirect()
            ->to(locale_route('booking.success', ['slug' => $tenant->slug, 'booking' => $booking->id]))
            ->with('success', __('app.booking.confirmed'));
    }

    public function success(string $slug, int $booking): Response
    {
        $tenant = $this->tenant($slug);
        $record = $tenant->bookings()->with(['service', 'staffMember', 'client'])->findOrFail($booking);

        return Inertia::render('Booking/Success', [
            'seo' => page_seo(
                __('app.booking.success_title'),
                __('app.booking.meta_description', ['name' => $tenant->name]),
            ),
            'business' => ['name' => $tenant->name, 'slug' => $tenant->slug],
            'booking' => [
                'id' => $record->id,
                'service' => $record->service->name,
                'staff' => $record->staffMember->name,
                'starts_at' => $record->starts_at->timezone($record->timezone)->toIso8601String(),
                'timezone' => $record->timezone,
                'client' => $record->client->name,
                'manage_url' => \Illuminate\Support\Facades\URL::temporarySignedRoute(
                    'bookings.manage',
                    now()->addDays(30),
                    ['booking' => $record->id, 'token' => $record->manage_token],
                ),
            ],
        ]);
    }

    private function tenant(string $slug): Tenant
    {
        return Tenant::query()->where('slug', $slug)->firstOrFail();
    }
}
