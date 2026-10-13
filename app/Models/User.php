<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_platform_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_platform_admin' => 'boolean',
        ];
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function staffMember(): HasOne
    {
        return $this->hasOne(StaffMember::class);
    }

    public function isPlatformAdmin(): bool
    {
        return (bool) $this->is_platform_admin;
    }

    public function currentTenant(): ?Tenant
    {
        if ($this->relationLoaded('tenants')) {
            return $this->tenants->first();
        }

        return $this->tenants()->first();
    }

    public function roleIn(?Tenant $tenant): ?string
    {
        if (! $tenant) {
            return $this->isPlatformAdmin() ? 'platform_admin' : null;
        }

        if ($this->isPlatformAdmin()) {
            return 'platform_admin';
        }

        $membership = $this->tenants->firstWhere('id', $tenant->id)
            ?? $this->tenants()->where('tenants.id', $tenant->id)->first();

        return $membership?->pivot?->role;
    }

    public function isOwnerOf(?Tenant $tenant): bool
    {
        return $this->roleIn($tenant) === 'owner' || $this->isPlatformAdmin();
    }

    public function isStaffOf(?Tenant $tenant): bool
    {
        return in_array($this->roleIn($tenant), ['owner', 'staff', 'platform_admin'], true);
    }
}
