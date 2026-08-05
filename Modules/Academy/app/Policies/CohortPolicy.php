<?php

namespace Modules\Academy\Policies;

use App\Models\User;
use Modules\Academy\Models\Cohort;
use Modules\Core\Support\RoleGroups;

class CohortPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Cohort $cohort): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function update(User $user, Cohort $cohort): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function delete(User $user, Cohort $cohort): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }
}
