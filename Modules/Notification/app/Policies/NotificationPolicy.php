<?php

namespace Modules\Notification\Policies;

use App\Models\User;
use Modules\Core\Support\RoleGroups;
use Modules\Notification\Models\Notification;

class NotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Notification $notification): bool
    {
        return $notification->user_id === $user->id || $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function update(User $user, Notification $notification): bool
    {
        return $notification->user_id === $user->id || $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function delete(User $user, Notification $notification): bool
    {
        return $notification->user_id === $user->id || $user->hasAnyRole(RoleGroups::adminFamily());
    }
}
