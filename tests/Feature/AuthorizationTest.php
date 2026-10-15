<?php

use App\Models\Tenant;
use App\Models\User;

test('staff cannot open owner-only services page', function () {
    $studio = makeStudio();

    $this->actingAs($studio['staffUser'])
        ->get(route('services.index'))
        ->assertForbidden();
});

test('owner can open services and staff pages', function () {
    $studio = makeStudio();

    $this->actingAs($studio['owner'])->get(route('services.index'))->assertOk();
    $this->actingAs($studio['owner'])->get(route('staff.index'))->assertOk();
});

test('staff can open calendar and only their tenant dashboard', function () {
    $studio = makeStudio();

    $this->actingAs($studio['staffUser'])->get(route('calendar'))->assertOk();
    $this->actingAs($studio['staffUser'])->get(route('dashboard'))->assertOk();
});

test('platform admin can list tenants', function () {
    $admin = User::factory()->create(['is_platform_admin' => true]);
    Tenant::factory()->create();

    $this->actingAs($admin)->get(route('admin.tenants'))->assertOk();
});

test('regular owner cannot open platform admin tenants', function () {
    $studio = makeStudio();

    $this->actingAs($studio['owner'])->get(route('admin.tenants'))->assertForbidden();
});
