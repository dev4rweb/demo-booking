<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Service;
use App\Models\StaffMember;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('demo123456');

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@demo.local'],
            ['name' => 'Vladislav Admin', 'password' => $password, 'is_platform_admin' => true, 'email_verified_at' => now()],
        );
        $admin->forceFill(['is_platform_admin' => true, 'password' => $password])->save();

        $owner = User::query()->updateOrCreate(
            ['email' => 'owner@demo.local'],
            ['name' => 'Oksana Melnyk', 'password' => $password, 'is_platform_admin' => false, 'email_verified_at' => now()],
        );
        $owner->forceFill(['password' => $password])->save();

        $staffUser = User::query()->updateOrCreate(
            ['email' => 'staff@demo.local'],
            ['name' => 'Olena Kravets', 'password' => $password, 'is_platform_admin' => false, 'email_verified_at' => now()],
        );
        $staffUser->forceFill(['password' => $password])->save();

        $luna = $this->business(
            slug: 'luna-beauty',
            name: 'Luna Beauty Studio',
            owner: $owner,
            plan: 'pro',
            services: [
                ['name' => 'Signature haircut', 'duration_minutes' => 45, 'price_cents' => 4500],
                ['name' => 'Color & gloss', 'duration_minutes' => 90, 'price_cents' => 9500],
                ['name' => 'Manicure', 'duration_minutes' => 60, 'price_cents' => 4000],
                ['name' => 'Calm facial', 'duration_minutes' => 75, 'price_cents' => 7000],
            ],
            staff: [
                ['name' => 'Olena Kravets', 'user' => $staffUser, 'days' => [1, 2, 3, 4, 5], 'start' => '09:00', 'end' => '18:00'],
                ['name' => 'Maya Shevchenko', 'user' => null, 'days' => [2, 3, 4, 5, 6], 'start' => '10:00', 'end' => '19:00'],
            ],
            clients: [
                ['Oksana Bondar', '+380501112233', 'oksana.bondar@example.com'],
                ['Andrii Koval', '+380671234567', 'andrii.koval@example.com'],
                ['Iryna Hrytsenko', '+380931112244', 'iryna.hrytsenko@example.com'],
                ['Taras Lysenko', '+380503334455', 'taras.lysenko@example.com'],
                ['Sofia Pavlenko', '+380662223344', 'sofia.pavlenko@example.com'],
            ],
        );

        $peakOwner = User::query()->updateOrCreate(
            ['email' => 'peak.owner@example.com'],
            ['name' => 'Dmytro Savchuk', 'password' => $password, 'email_verified_at' => now()],
        );

        $peak = $this->business(
            slug: 'peak-fitness',
            name: 'Peak Fitness',
            owner: $peakOwner,
            plan: 'business',
            services: [
                ['name' => 'Personal training', 'duration_minutes' => 60, 'price_cents' => 6000],
                ['name' => 'Small group class', 'duration_minutes' => 45, 'price_cents' => 2500],
                ['name' => 'Recovery stretch', 'duration_minutes' => 30, 'price_cents' => 3000],
            ],
            staff: [
                ['name' => 'Taras Honchar', 'user' => null, 'days' => [1, 2, 3, 4, 5], 'start' => '07:00', 'end' => '15:00'],
                ['name' => 'Iryna Volkova', 'user' => null, 'days' => [1, 3, 5, 6], 'start' => '12:00', 'end' => '20:00'],
            ],
            clients: [
                ['Maksym Oliinyk', '+380504445566', 'maksym.oliinyk@example.com'],
                ['Nadiia Petrenko', '+380675556677', 'nadiia.petrenko@example.com'],
                ['Yulia Romanenko', '+380937778899', 'yulia.romanenko@example.com'],
                ['Bohdan Marchuk', '+380509998877', 'bohdan.marchuk@example.com'],
            ],
        );

        $this->seedBookings($luna);
        $this->seedBookings($peak);
    }

    /**
     * @param  list<array<string, mixed>>  $services
     * @param  list<array<string, mixed>>  $staff
     * @param  list<array{0: string, 1: string, 2: string}>  $clients
     */
    private function business(string $slug, string $name, User $owner, string $plan, array $services, array $staff, array $clients): Tenant
    {
        $tenant = Tenant::query()->updateOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'timezone' => 'Europe/Kyiv', 'plan' => $plan, 'currency' => 'USD'],
        );

        $tenant->users()->syncWithoutDetaching([$owner->id => ['role' => 'owner']]);

        $serviceModels = [];
        foreach ($services as $row) {
            $serviceModels[] = Service::query()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $row['name']],
                $row + ['is_active' => true],
            );
        }

        $staffModels = [];
        foreach ($staff as $row) {
            $member = StaffMember::query()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $row['name']],
                ['user_id' => $row['user']?->id, 'is_active' => true],
            );

            if ($row['user']) {
                $tenant->users()->syncWithoutDetaching([$row['user']->id => ['role' => 'staff']]);
            }

            $member->workingHours()->delete();
            foreach ($row['days'] as $weekday) {
                $member->workingHours()->create([
                    'weekday' => $weekday,
                    'starts_at' => $row['start'],
                    'ends_at' => $row['end'],
                ]);
            }

            $member->services()->sync(collect($serviceModels)->pluck('id')->all());
            $staffModels[] = $member;
        }

        foreach ($clients as [$clientName, $phone, $email]) {
            Client::query()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'email' => $email],
                ['name' => $clientName, 'phone' => $phone],
            );
        }

        $tenant->setRelation('services', collect($serviceModels));
        $tenant->setRelation('staffMembers', collect($staffModels));

        return $tenant;
    }

    private function seedBookings(Tenant $tenant): void
    {
        Booking::query()->where('tenant_id', $tenant->id)->delete();

        $services = $tenant->services;
        $staff = $tenant->staffMembers;
        $clients = $tenant->clients()->get();
        $tz = $tenant->timezone;
        $today = Carbon::now($tz)->startOfDay();

        $i = 0;
        for ($day = -7; $day <= 7; $day++) {
            $date = $today->copy()->addDays($day);
            if (in_array($date->dayOfWeek, [0], true)) {
                continue;
            }

            foreach ($staff as $index => $member) {
                $hours = $member->workingHours->firstWhere('weekday', $date->dayOfWeek);
                if (! $hours) {
                    continue;
                }

                $slots = [0, 2, 4];
                foreach ($slots as $offset) {
                    $service = $services[$i % $services->count()];
                    $client = $clients[$i % $clients->count()];
                    $start = $date->copy()->setTimeFromTimeString((string) $hours->starts_at)->addHours($offset);
                    if ($start->copy()->addMinutes($service->duration_minutes)->gt($date->copy()->setTimeFromTimeString((string) $hours->ends_at))) {
                        continue;
                    }

                    $status = Booking::STATUS_CONFIRMED;
                    if ($day < -1 && $i % 11 === 0) {
                        $status = Booking::STATUS_NO_SHOW;
                    } elseif ($day < 0 && $i % 9 === 0) {
                        $status = Booking::STATUS_COMPLETED;
                    } elseif ($i % 13 === 0) {
                        $status = Booking::STATUS_CANCELLED;
                    }

                    Booking::query()->create([
                        'tenant_id' => $tenant->id,
                        'service_id' => $service->id,
                        'staff_member_id' => $member->id,
                        'client_id' => $client->id,
                        'starts_at' => $start->copy()->utc(),
                        'ends_at' => $start->copy()->addMinutes($service->duration_minutes)->utc(),
                        'timezone' => $tz,
                        'status' => $status,
                        'manage_token' => Str::random(48),
                        'cancelled_at' => $status === Booking::STATUS_CANCELLED ? $start->copy()->subDay() : null,
                    ]);
                    $i++;
                }
            }
        }
    }
}
