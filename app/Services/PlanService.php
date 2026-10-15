<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Validation\ValidationException;

class PlanService
{
    /**
     * @return array<string, mixed>
     */
    public function plans(): array
    {
        return config('booking.plans', []);
    }

    public function switchDemoPlan(Tenant $tenant, string $plan): Tenant
    {
        if (stripe_enabled()) {
            throw ValidationException::withMessages([
                'plan' => __('app.billing.demo_only'),
            ]);
        }

        if (! array_key_exists($plan, $this->plans())) {
            throw ValidationException::withMessages([
                'plan' => __('app.billing.unknown_plan'),
            ]);
        }

        $tenant->update(['plan' => $plan]);

        return $tenant->fresh();
    }

    public function syncPlanFromStripe(Tenant $tenant): void
    {
        if (! stripe_enabled()) {
            return;
        }

        $subscription = $tenant->subscription('default');

        if (! $subscription || ! $subscription->valid()) {
            $tenant->update(['plan' => 'free']);

            return;
        }

        $price = $subscription->stripe_price;
        $matched = 'free';

        foreach ($this->plans() as $key => $config) {
            if (! empty($config['stripe_price']) && $config['stripe_price'] === $price) {
                $matched = $key;
                break;
            }
        }

        $tenant->update(['plan' => $matched]);
    }
}
