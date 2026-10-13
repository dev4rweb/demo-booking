<?php

use App\Models\Booking;
use App\Models\Client;
use App\Models\Service;

test('owner cannot see another tenant service by id', function () {
    $a = makeStudio();
    $b = makeStudio();
    $foreign = Service::factory()->create(['tenant_id' => $b['tenant']->id]);

    $this->actingAs($a['owner'])
        ->patch(route('services.update', $foreign), [
            'name' => 'Hacked',
            'duration_minutes' => 30,
            'price_cents' => 1,
        ])
        ->assertForbidden();

    expect($foreign->fresh()->name)->not->toBe('Hacked');
});

test('bookings list is scoped to the owner tenant', function () {
    $a = makeStudio();
    $b = makeStudio();
    $clientB = Client::factory()->create(['tenant_id' => $b['tenant']->id]);

    Booking::factory()->create([
        'tenant_id' => $b['tenant']->id,
        'service_id' => $b['service']->id,
        'staff_member_id' => $b['staff']->id,
        'client_id' => $clientB->id,
    ]);

    $this->actingAs($a['owner'])
        ->get(route('bookings.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Bookings/Index')
            ->where('bookings.data', [])
        );
});
