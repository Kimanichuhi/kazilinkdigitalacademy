<?php

namespace Modules\Academy\Policies;

use App\Models\User;
use Modules\Academy\Models\ProgramCategory;
use Modules\Core\Support\RoleGroups;

class ProgramCategoryPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, ProgramCategory $category): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function update(User $user, ProgramCategory $category): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function delete(User $user, ProgramCategory $category): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }
}
