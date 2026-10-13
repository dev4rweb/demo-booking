<?php

use App\Models\Service;
use App\Models\StaffMember;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/**
 * @return array{owner: User, staffUser: User, tenant: Tenant, staff: StaffMember, service: Service}
 */
function makeStudio(): array
{
    $owner = User::factory()->create(['email' => 'owner-'.uniqid().'@example.com']);
    $staffUser = User::factory()->create(['email' => 'staff-'.uniqid().'@example.com']);
    $tenant = Tenant::factory()->create(['timezone' => 'Europe/Kyiv', 'plan' => 'pro']);
    $tenant->users()->attach($owner->id, ['role' => 'owner']);
    $tenant->users()->attach($staffUser->id, ['role' => 'staff']);

    $staff = StaffMember::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $staffUser->id,
        'name' => 'Olena',
    ]);

    foreach (range(1, 5) as $weekday) {
        $staff->workingHours()->create([
            'weekday' => $weekday,
            'starts_at' => '09:00',
            'ends_at' => '18:00',
        ]);
    }

    $service = Service::factory()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Haircut',
        'duration_minutes' => 60,
        'price_cents' => 4500,
    ]);
    $staff->services()->attach($service->id);

    return compact('owner', 'staffUser', 'tenant', 'staff', 'service');
}
