<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Service;
use App\Models\StaffMember;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * @return list<array{start: string, end: string, staff_member_id: int}>
     */
    public function slotsForDate(StaffMember $staff, Service $service, CarbonInterface $date, string $timezone): array
    {
        $day = Carbon::parse($date->toDateString(), $timezone)->startOfDay();

        if ($staff->daysOff()->whereDate('date', $day->toDateString())->exists()) {
            return [];
        }

        $hours = $staff->workingHours()->where('weekday', $day->dayOfWeek)->get();

        if ($hours->isEmpty()) {
            return [];
        }

        $duration = (int) $service->duration_minutes;
        $step = (int) config('booking.slot_interval_minutes', 15);
        $dayStartUtc = $day->copy()->utc();
        $dayEndUtc = $day->copy()->endOfDay()->utc();

        $existing = Booking::query()
            ->where('staff_member_id', $staff->id)
            ->where('status', Booking::STATUS_CONFIRMED)
            ->where('starts_at', '<', $dayEndUtc)
            ->where('ends_at', '>', $dayStartUtc)
            ->get(['starts_at', 'ends_at']);

        $now = Carbon::now($timezone);
        $slots = [];

        foreach ($hours as $window) {
            $cursor = $day->copy()->setTimeFromTimeString((string) $window->starts_at);
            $windowEnd = $day->copy()->setTimeFromTimeString((string) $window->ends_at);

            while ($cursor->copy()->addMinutes($duration)->lte($windowEnd)) {
                $slotStart = $cursor->copy();
                $slotEnd = $cursor->copy()->addMinutes($duration);

                if ($slotStart->gt($now) && ! $this->overlaps($existing, $slotStart->copy()->utc(), $slotEnd->copy()->utc())) {
                    $slots[] = [
                        'start' => $slotStart->toIso8601String(),
                        'end' => $slotEnd->toIso8601String(),
                        'staff_member_id' => $staff->id,
                    ];
                }

                $cursor->addMinutes($step);
            }
        }

        return $slots;
    }

    /**
     * Collective slots for "any staff" — one entry per start time with first available staff.
     *
     * @param  Collection<int, StaffMember>  $staff
     * @return list<array{start: string, end: string, staff_member_id: int}>
     */
    public function slotsForAny(Collection $staff, Service $service, CarbonInterface $date, string $timezone): array
    {
        $byStart = [];

        foreach ($staff as $member) {
            foreach ($this->slotsForDate($member, $service, $date, $timezone) as $slot) {
                $byStart[$slot['start']] ??= $slot;
            }
        }

        ksort($byStart);

        return array_values($byStart);
    }

    public function isSlotFree(StaffMember $staff, CarbonInterface $start, CarbonInterface $end, ?int $ignoreBookingId = null): bool
    {
        $query = Booking::query()
            ->where('staff_member_id', $staff->id)
            ->where('status', Booking::STATUS_CONFIRMED)
            ->where('starts_at', '<', $end)
            ->where('ends_at', '>', $start);

        if ($ignoreBookingId) {
            $query->where('id', '!=', $ignoreBookingId);
        }

        return ! $query->exists();
    }

    /**
     * @param  Collection<int, Booking>  $existing
     */
    private function overlaps(Collection $existing, CarbonInterface $start, CarbonInterface $end): bool
    {
        return $existing->contains(
            fn (Booking $booking): bool => $booking->starts_at->lt($end) && $booking->ends_at->gt($start)
        );
    }
}
