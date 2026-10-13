<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;

class TenantPolicy
{
    public function view(User $user, Tenant $tenant): bool
    {
        return $user->isStaffOf($tenant);
    }

    public function update(User $user, Tenant $tenant): bool
    {
        return $user->isOwnerOf($tenant);
    }

    public function manageBilling(User $user, Tenant $tenant): bool
    {
        return $user->isOwnerOf($tenant);
    }
}
