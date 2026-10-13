<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesTenant;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class StaffController extends Controller
{
    use ResolvesTenant;

    public function index(Request $request): Response
    {
        $tenant = $this->currentTenant($request);
        abort_unless($request->user()?->isOwnerOf($tenant), 403);

        $staff = $tenant->staffMembers()
            ->with(['workingHours', 'daysOff', 'services:id'])
            ->orderBy('name')
            ->get();

        return Inertia::render('Staff/Index', [
            'staff' => $staff,
            'services' => $tenant->services()->orderBy('name')->get(['id', 'name']),
            'canAdd' => $tenant->canAddStaff(),
            'staffLimit' => $tenant->staffLimit(),
            'plan' => $tenant->plan,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->currentTenant($request);
        abort_unless($request->user()?->isOwnerOf($tenant), 403);

        if (! $tenant->canAddStaff()) {
            throw ValidationException::withMessages([
                'name' => __('app.staff.limit_reached'),
            ]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email'],
            'service_ids' => ['array'],
            'service_ids.*' => ['integer'],
        ]);

        $user = null;
        if (! empty($data['email'])) {
            $user = User::query()->firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => Hash::make(Str::random(16))],
            );
            $tenant->users()->syncWithoutDetaching([$user->id => ['role' => 'staff']]);
        }

        $staff = $tenant->staffMembers()->create([
            'name' => $data['name'],
            'user_id' => $user?->id,
            'is_active' => true,
        ]);

        $staff->services()->sync($data['service_ids'] ?? []);

        foreach (range(1, 5) as $weekday) {
            $staff->workingHours()->create([
                'weekday' => $weekday,
                'starts_at' => '09:00',
                'ends_at' => '18:00',
            ]);
        }

        return back()->with('success', __('app.common.saved'));
    }

    public function update(Request $request, StaffMember $staff): RedirectResponse
    {
        $tenant = $this->currentTenant($request);
        abort_unless($request->user()?->isOwnerOf($tenant) && $staff->tenant_id === $tenant->id, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'is_active' => ['sometimes', 'boolean'],
            'service_ids' => ['array'],
            'service_ids.*' => ['integer'],
            'hours' => ['array'],
            'hours.*.weekday' => ['required', 'integer', 'min:0', 'max:6'],
            'hours.*.starts_at' => ['required', 'date_format:H:i'],
            'hours.*.ends_at' => ['required', 'date_format:H:i'],
            'days_off' => ['array'],
            'days_off.*' => ['date'],
        ]);

        $staff->update([
            'name' => $data['name'],
            'is_active' => $request->boolean('is_active', $staff->is_active),
        ]);

        if (array_key_exists('service_ids', $data)) {
            $staff->services()->sync($data['service_ids']);
        }

        if (array_key_exists('hours', $data)) {
            $staff->workingHours()->delete();
            foreach ($data['hours'] as $hour) {
                $staff->workingHours()->create($hour);
            }
        }

        if (array_key_exists('days_off', $data)) {
            $staff->daysOff()->delete();
            foreach ($data['days_off'] as $date) {
                $staff->daysOff()->create(['date' => $date]);
            }
        }

        return back()->with('success', __('app.common.saved'));
    }

    public function destroy(Request $request, StaffMember $staff): RedirectResponse
    {
        $tenant = $this->currentTenant($request);
        abort_unless($request->user()?->isOwnerOf($tenant) && $staff->tenant_id === $tenant->id, 403);

        $staff->delete();

        return back()->with('success', __('app.common.saved'));
    }
}
