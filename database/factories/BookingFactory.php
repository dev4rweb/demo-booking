<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Service;
use App\Models\StaffMember;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        $starts = now('UTC')->addDay()->setTime(10, 0);

        return [
            'tenant_id' => Tenant::factory(),
            'service_id' => Service::factory(),
            'staff_member_id' => StaffMember::factory(),
            'client_id' => Client::factory(),
            'starts_at' => $starts,
            'ends_at' => $starts->copy()->addHour(),
            'timezone' => 'Europe/Kyiv',
            'status' => Booking::STATUS_CONFIRMED,
            'manage_token' => Str::random(48),
        ];
    }
}
