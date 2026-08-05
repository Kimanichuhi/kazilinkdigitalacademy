<?php

namespace Modules\Academy\Policies;

use App\Models\User;
use Modules\Academy\Models\Trainer;
use Modules\Core\Support\RoleGroups;

class TrainerPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Trainer $trainer): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function update(User $user, Trainer $trainer): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function delete(User $user, Trainer $trainer): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }
}
