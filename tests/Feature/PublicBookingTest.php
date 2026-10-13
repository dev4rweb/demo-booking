<?php

use App\Models\Booking;
use Carbon\Carbon;

test('public booking page is reachable by slug', function () {
    $studio = makeStudio();

    $this->get('/b/'.$studio['tenant']->slug)
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Booking/Show'));
});

test('guest can confirm a booking and receive a manage token', function () {
    $studio = makeStudio();
    $start = Carbon::parse('next monday 10:00', $studio['tenant']->timezone);

    $this->post('/b/'.$studio['tenant']->slug, [
        'service_id' => $studio['service']->id,
        'staff_member_id' => $studio['staff']->id,
        'starts_at' => $start->toIso8601String(),
        'name' => 'Sofia Pavlenko',
        'phone' => '+380662223344',
        'email' => 'sofia.pavlenko@example.com',
    ])->assertRedirect();

    $booking = Booking::query()->first();
    expect($booking)->not->toBeNull();
    expect($booking->status)->toBe(Booking::STATUS_CONFIRMED);
    expect($booking->manage_token)->not->toBeEmpty();
});
