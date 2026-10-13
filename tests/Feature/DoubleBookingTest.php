<?php

use App\Models\Booking;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

test('the same slot cannot be booked twice', function () {
    $studio = makeStudio();
    $start = Carbon::parse('next monday 11:00', $studio['tenant']->timezone);
    $guest = ['name' => 'Oksana Bondar', 'phone' => '+380501112233', 'email' => 'oksana.bondar@example.com'];

    $first = app(BookingService::class)->book(
        $studio['tenant'],
        $studio['service'],
        $studio['staff'],
        $start,
        $guest,
    );

    expect($first)->toBeInstanceOf(Booking::class);

    expect(fn () => app(BookingService::class)->book(
        $studio['tenant'],
        $studio['service'],
        $studio['staff'],
        $start,
        ['name' => 'Andrii Koval', 'phone' => '+380671234567', 'email' => 'andrii.koval@example.com'],
    ))->toThrow(ValidationException::class);

    expect(Booking::query()->where('status', Booking::STATUS_CONFIRMED)->count())->toBe(1);
});
