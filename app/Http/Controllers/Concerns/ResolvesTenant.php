<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Tenant;
use Illuminate\Http\Request;

trait ResolvesTenant
{
    protected function currentTenant(Request $request): Tenant
    {
        $user = $request->user();
        abort_unless($user, 401);

        $tenant = $user->currentTenant();
        abort_unless($tenant, 403);

        return $tenant;
    }
}
