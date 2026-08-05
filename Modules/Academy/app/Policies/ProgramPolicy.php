<?php

namespace Modules\Academy\Policies;

use App\Models\User;
use Modules\Academy\Models\Program;
use Modules\Core\Support\RoleGroups;

class ProgramPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Program $program): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function update(User $user, Program $program): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function delete(User $user, Program $program): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }
}
