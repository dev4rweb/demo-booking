<?php

namespace App\Models;

use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;

class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use Billable, HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'timezone',
        'plan',
        'currency',
        'telegram_chat_id',
        'telegram_connect_token',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function owner(): ?User
    {
        return $this->users()->wherePivot('role', 'owner')->first();
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function staffMembers(): HasMany
    {
        return $this->hasMany(StaffMember::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function staffLimit(): ?int
    {
        return config('booking.plans.'.$this->plan.'.staff_limit');
    }

    public function canAddStaff(): bool
    {
        $limit = $this->staffLimit();

        if ($limit === null) {
            return true;
        }

        return $this->staffMembers()->count() < $limit;
    }

    public function planName(): string
    {
        return (string) config('booking.plans.'.$this->plan.'.name', $this->plan);
    }

    public function stripeName(): string
    {
        return $this->name;
    }

    public function stripeEmail(): ?string
    {
        return $this->owner()?->email;
    }
}
