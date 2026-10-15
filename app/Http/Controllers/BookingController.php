<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesTenant;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    use ResolvesTenant;

    public function index(Request $request): Response
    {
        $tenant = $this->currentTenant($request);
        $user = $request->user();
        $role = $user->roleIn($tenant);
        $status = $request->string('status')->toString();

        $bookings = Booking::query()
            ->with(['service', 'staffMember', 'client'])
            ->where('tenant_id', $tenant->id)
            ->when($role === 'staff', fn ($q) => $q->where('staff_member_id', $user->staffMember?->id))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->orderByDesc('starts_at')
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Booking $booking): array => [
                'id' => $booking->id,
                'starts_at' => $booking->starts_at->timezone($tenant->timezone)->toIso8601String(),
                'service' => $booking->service->name,
                'staff' => $booking->staffMember->name,
                'client' => $booking->client->name,
                'email' => $booking->client->email,
                'phone' => $booking->client->phone,
                'status' => $booking->status,
                'price_cents' => $booking->service->price_cents,
            ]);

        return Inertia::render('Bookings/Index', [
            'bookings' => $bookings,
            'filters' => ['status' => $status],
        ]);
    }

    public function updateStatus(Request $request, Booking $booking, BookingService $bookings): RedirectResponse
    {
        $this->authorize('update', $booking);

        $data = $request->validate([
            'status' => ['required', 'in:confirmed,cancelled,completed,no_show'],
        ]);

        if ($data['status'] === Booking::STATUS_CANCELLED) {
            $bookings->cancel($booking);
        } else {
            $bookings->markStatus($booking, $data['status']);
        }

        return back()->with('success', __('app.common.saved'));
    }
}
