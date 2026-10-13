<?php

use App\Models\Booking;
use App\Models\Client;
use App\Services\AvailabilityService;
use Carbon\Carbon;

test('slots follow working hours and duration', function () {
    $studio = makeStudio();
    $monday = Carbon::parse('next monday', $studio['tenant']->timezone)->setTime(0, 0);

    $slots = app(AvailabilityService::class)->slotsForDate(
        $studio['staff'],
        $studio['service'],
        $monday,
        $studio['tenant']->timezone,
    );

    expect($slots)->not->toBeEmpty();
    expect($slots[0]['start'])->toContain('T');
    $first = Carbon::parse($slots[0]['start'])->timezone($studio['tenant']->timezone);
    $last = Carbon::parse($slots[array_key_last($slots)]['end'])->timezone($studio['tenant']->timezone);
    expect($first->hour)->toBeGreaterThanOrEqual(9);
    expect($last->hour)->toBeLessThanOrEqual(18);
});

test('existing booking removes overlapping slots', function () {
    $studio = makeStudio();
    $monday = Carbon::parse('next monday 10:00', $studio['tenant']->timezone);
    $client = Client::factory()->create(['tenant_id' => $studio['tenant']->id]);

    Booking::factory()->create([
        'tenant_id' => $studio['tenant']->id,
        'service_id' => $studio['service']->id,
        'staff_member_id' => $studio['staff']->id,
        'client_id' => $client->id,
        'starts_at' => $monday->copy()->utc(),
        'ends_at' => $monday->copy()->addHour()->utc(),
        'timezone' => $studio['tenant']->timezone,
        'status' => Booking::STATUS_CONFIRMED,
    ]);

    $slots = app(AvailabilityService::class)->slotsForDate(
        $studio['staff'],
        $studio['service'],
        $monday,
        $studio['tenant']->timezone,
    );

    $taken = collect($slots)->first(fn ($slot) => Carbon::parse($slot['start'])->equalTo($monday));
    expect($taken)->toBeNull();
});

test('days off yield no slots', function () {
    $studio = makeStudio();
    $monday = Carbon::parse('next monday', $studio['tenant']->timezone);
    $studio['staff']->daysOff()->create(['date' => $monday->toDateString()]);

    $slots = app(AvailabilityService::class)->slotsForDate(
        $studio['staff'],
        $studio['service'],
        $monday,
        $studio['tenant']->timezone,
    );

    expect($slots)->toBeEmpty();
});
