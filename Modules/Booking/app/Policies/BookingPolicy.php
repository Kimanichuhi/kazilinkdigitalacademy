<?php

namespace Modules\Booking\Policies;

use App\Models\User;
use Modules\Booking\Models\Booking;
use Modules\Core\Support\RoleGroups;

class BookingPolicy
{
    /**
     * The booking wizard requires no login (source: /booking is public).
     */
    public function create(?User $user): bool
    {
        return true;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Booking $booking): bool
    {
        return $booking->user_id === $user->id || $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }
}
