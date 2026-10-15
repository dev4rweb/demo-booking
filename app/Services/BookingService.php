<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Service;
use App\Models\StaffMember;
use App\Models\Tenant;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        private AvailabilityService $availability,
        private NotificationService $notifications,
    ) {}

    /**
     * @param  array{name: string, phone: string, email: string}  $guest
     */
    public function book(
        Tenant $tenant,
        Service $service,
        StaffMember $staff,
        CarbonInterface $startsAt,
        array $guest,
        ?string $notes = null,
    ): Booking {
        $this->assertSameTenant($tenant, $service, $staff);

        $startsAt = Carbon::parse($startsAt)->utc();
        $endsAt = $startsAt->copy()->addMinutes($service->duration_minutes);

        $booking = DB::transaction(function () use ($tenant, $service, $staff, $startsAt, $endsAt, $guest, $notes) {
            StaffMember::query()->whereKey($staff->id)->lockForUpdate()->first();

            if (! $this->availability->isSlotFree($staff, $startsAt, $endsAt)) {
                throw ValidationException::withMessages([
                    'starts_at' => __('app.booking.slot_taken'),
                ]);
            }

            if (! $this->fitsWorkingHours($staff, $startsAt, $endsAt, $tenant->timezone)) {
                throw ValidationException::withMessages([
                    'starts_at' => __('app.booking.outside_hours'),
                ]);
            }

            $client = Client::query()->firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'email' => $guest['email'],
                ],
                [
                    'name' => $guest['name'],
                    'phone' => $guest['phone'],
                ],
            );

            $client->fill([
                'name' => $guest['name'],
                'phone' => $guest['phone'],
            ])->save();

            return Booking::query()->create([
                'tenant_id' => $tenant->id,
                'service_id' => $service->id,
                'staff_member_id' => $staff->id,
                'client_id' => $client->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'timezone' => $tenant->timezone,
                'locale' => app()->getLocale(),
                'status' => Booking::STATUS_CONFIRMED,
                'notes' => $notes,
            ]);
        });

        $booking->load(['service', 'staffMember', 'client', 'tenant']);
        $this->notifications->bookingCreated($booking);

        return $booking;
    }

    public function cancel(Booking $booking): Booking
    {
        if ($booking->status === Booking::STATUS_CANCELLED) {
            return $booking;
        }

        $booking->update([
            'status' => Booking::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);

        $booking->load(['service', 'staffMember', 'client', 'tenant']);
        $this->notifications->bookingCancelled($booking);

        return $booking;
    }

    public function reschedule(Booking $booking, StaffMember $staff, CarbonInterface $startsAt): Booking
    {
        $service = $booking->service;
        $startsAt = Carbon::parse($startsAt)->utc();
        $endsAt = $startsAt->copy()->addMinutes($service->duration_minutes);

        DB::transaction(function () use ($booking, $staff, $startsAt, $endsAt, $service) {
            StaffMember::query()->whereKey($staff->id)->lockForUpdate()->first();

            if (! $this->availability->isSlotFree($staff, $startsAt, $endsAt, $booking->id)) {
                throw ValidationException::withMessages([
                    'starts_at' => __('app.booking.slot_taken'),
                ]);
            }

            if (! $this->fitsWorkingHours($staff, $startsAt, $endsAt, $booking->timezone)) {
                throw ValidationException::withMessages([
                    'starts_at' => __('app.booking.outside_hours'),
                ]);
            }

            $booking->update([
                'staff_member_id' => $staff->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => Booking::STATUS_CONFIRMED,
                'cancelled_at' => null,
            ]);
        });

        return $booking->fresh(['service', 'staffMember', 'client', 'tenant']);
    }

    public function markStatus(Booking $booking, string $status): Booking
    {
        $booking->update(['status' => $status]);

        return $booking;
    }

    private function fitsWorkingHours(StaffMember $staff, CarbonInterface $startUtc, CarbonInterface $endUtc, string $timezone): bool
    {
        $localStart = Carbon::parse($startUtc)->timezone($timezone);
        $localEnd = Carbon::parse($endUtc)->timezone($timezone);

        if ($staff->daysOff()->whereDate('date', $localStart->toDateString())->exists()) {
            return false;
        }

        return $staff->workingHours()
            ->where('weekday', $localStart->dayOfWeek)
            ->get()
            ->contains(function ($window) use ($localStart, $localEnd): bool {
                $day = $localStart->copy()->startOfDay();
                $from = $day->copy()->setTimeFromTimeString((string) $window->starts_at);
                $to = $day->copy()->setTimeFromTimeString((string) $window->ends_at);

                return $localStart->gte($from) && $localEnd->lte($to);
            });
    }

    private function assertSameTenant(Tenant $tenant, Service $service, StaffMember $staff): void
    {
        if ((int) $service->tenant_id !== (int) $tenant->id || (int) $staff->tenant_id !== (int) $tenant->id) {
            throw ValidationException::withMessages([
                'service' => __('app.booking.invalid_selection'),
            ]);
        }
    }
}
