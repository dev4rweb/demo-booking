<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\StaffMember;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ManageBookingController extends Controller
{
    public function show(Request $request, Booking $booking): Response
    {
        $this->assertToken($request, $booking);
        $booking->load(['service', 'staffMember', 'client', 'tenant']);

        return Inertia::render('Booking/Manage', [
            'booking' => $this->payload($booking),
            'staff' => $booking->tenant->staffMembers()
                ->where('is_active', true)
                ->whereHas('services', fn ($q) => $q->where('services.id', $booking->service_id))
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function slots(Request $request, Booking $booking, AvailabilityService $availability): JsonResponse
    {
        $this->assertToken($request, $booking);

        $data = $request->validate([
            'staff_member_id' => ['required', 'integer'],
            'date' => ['required', 'date'],
        ]);

        $staff = StaffMember::query()
            ->where('tenant_id', $booking->tenant_id)
            ->findOrFail($data['staff_member_id']);

        $date = Carbon::parse($data['date'], $booking->timezone);
        $slots = $availability->slotsForDate($staff, $booking->service, $date, $booking->timezone);

        return response()->json(['slots' => $slots]);
    }

    public function cancel(Request $request, Booking $booking, BookingService $bookings): RedirectResponse
    {
        $this->assertToken($request, $booking);
        $bookings->cancel($booking);

        return back()->with('success', __('app.booking.cancelled'));
    }

    public function reschedule(Request $request, Booking $booking, BookingService $bookings): RedirectResponse
    {
        $this->assertToken($request, $booking);

        $data = $request->validate([
            'staff_member_id' => ['required', 'integer'],
            'starts_at' => ['required', 'date'],
        ]);

        $staff = StaffMember::query()
            ->where('tenant_id', $booking->tenant_id)
            ->findOrFail($data['staff_member_id']);

        $bookings->reschedule($booking, $staff, Carbon::parse($data['starts_at']));

        return back()->with('success', __('app.booking.rescheduled'));
    }

    private function assertToken(Request $request, Booking $booking): void
    {
        abort_unless(hash_equals($booking->manage_token, (string) $request->query('token')), 403);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Booking $booking): array
    {
        return [
            'id' => $booking->id,
            'status' => $booking->status,
            'service' => $booking->service->name,
            'staff' => $booking->staffMember->name,
            'staff_member_id' => $booking->staff_member_id,
            'starts_at' => $booking->starts_at->timezone($booking->timezone)->toIso8601String(),
            'timezone' => $booking->timezone,
            'client' => $booking->client->only(['name', 'email', 'phone']),
            'token' => $booking->manage_token,
        ];
    }
}
