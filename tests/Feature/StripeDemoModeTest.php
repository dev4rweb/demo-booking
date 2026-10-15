<?php

use App\Services\PlanService;

test('empty stripe keys enable demo mode and local plan switching', function () {
    expect(stripe_enabled())->toBeFalse();

    $studio = makeStudio();

    $this->actingAs($studio['owner'])
        ->get(route('billing.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Settings/Billing')
            ->where('demoMode', true)
            ->where('currentPlan', 'pro')
        );

    $this->actingAs($studio['owner'])
        ->post(route('billing.checkout'), ['plan' => 'business'])
        ->assertRedirect();

    expect($studio['tenant']->fresh()->plan)->toBe('business');
});

test('plan service refuses demo switch when stripe is configured', function () {
    config(['cashier.key' => 'pk_test_x', 'cashier.secret' => 'sk_test_x']);

    expect(stripe_enabled())->toBeTrue();

    $studio = makeStudio();

    expect(fn () => app(PlanService::class)->switchDemoPlan($studio['tenant'], 'free'))
        ->toThrow(\Illuminate\Validation\ValidationException::class);
});
