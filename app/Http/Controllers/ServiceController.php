<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesTenant;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    use ResolvesTenant;

    public function index(Request $request): Response
    {
        $tenant = $this->currentTenant($request);
        abort_unless($request->user()?->isOwnerOf($tenant), 403);

        return Inertia::render('Services/Index', [
            'services' => $tenant->services()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->currentTenant($request);
        abort_unless($request->user()?->isOwnerOf($tenant), 403);

        $data = $this->validated($request);
        $tenant->services()->create($data);

        return back()->with('success', __('app.common.saved'));
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $tenant = $this->currentTenant($request);
        abort_unless($request->user()?->isOwnerOf($tenant) && $service->tenant_id === $tenant->id, 403);

        $service->update($this->validated($request));

        return back()->with('success', __('app.common.saved'));
    }

    public function destroy(Request $request, Service $service): RedirectResponse
    {
        $tenant = $this->currentTenant($request);
        abort_unless($request->user()?->isOwnerOf($tenant) && $service->tenant_id === $tenant->id, 403);

        $service->delete();

        return back()->with('success', __('app.common.saved'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'duration_minutes' => ['required', 'integer', 'min:10', 'max:480'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
