<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesTenant;
use App\Services\PlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Cashier\Exceptions\IncompletePayment;

class BillingController extends Controller
{
    use ResolvesTenant;

    public function index(Request $request, PlanService $plans): Response
    {
        $tenant = $this->currentTenant($request);
        abort_unless($request->user()?->isOwnerOf($tenant), 403);

        return Inertia::render('Settings/Billing', [
            'plans' => $plans->plans(),
            'currentPlan' => $tenant->plan,
            'demoMode' => ! stripe_enabled(),
        ]);
    }

    public function checkout(Request $request, PlanService $plans): RedirectResponse
    {
        $tenant = $this->currentTenant($request);
        abort_unless($request->user()?->isOwnerOf($tenant), 403);

        $data = $request->validate([
            'plan' => ['required', 'string'],
        ]);

        if (! stripe_enabled()) {
            $plans->switchDemoPlan($tenant, $data['plan']);

            return back()->with('success', __('app.billing.demo_switched'));
        }

        $config = $plans->plans()[$data['plan']] ?? null;
        abort_unless($config && ! empty($config['stripe_price']), 422);

        if ($data['plan'] === 'free') {
            $tenant->subscription('default')?->cancel();
            $tenant->update(['plan' => 'free']);

            return back()->with('success', __('app.billing.demo_switched'));
        }

        try {
            $checkout = $tenant->newSubscription('default', $config['stripe_price'])
                ->checkout([
                    'success_url' => locale_route('billing.index').'?checkout=success',
                    'cancel_url' => locale_route('billing.index').'?checkout=cancel',
                    'customer_email' => $request->user()->email,
                ]);

            return redirect()->away($checkout->url);
        } catch (IncompletePayment|\Throwable $e) {
            Log::warning('Stripe checkout failed', ['message' => $e->getMessage()]);

            return back()->with('error', __('app.billing.checkout_failed'));
        }
    }
}
