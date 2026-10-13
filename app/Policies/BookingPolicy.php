<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isPlatformAdmin() || $user->tenants()->exists();
    }

    public function view(User $user, Booking $booking): bool
    {
        if ($user->isOwnerOf($booking->tenant)) {
            return true;
        }

        return $user->staffMember?->id === $booking->staff_member_id;
    }

    public function update(User $user, Booking $booking): bool
    {
        return $this->view($user, $booking);
    }
}
