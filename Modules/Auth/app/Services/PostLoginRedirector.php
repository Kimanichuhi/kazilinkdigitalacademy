<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Modules\Core\Support\RoleGroups;

/**
 * Redirect partition across the app's 3 roles:
 *
 *   admin   -> /admin
 *   trainer -> /trainer
 *   student -> /student
 */
class PostLoginRedirector
{
    public function path(User $user): string
    {
        if ($user->hasAnyRole(RoleGroups::adminFamily())) {
            return '/admin';
        }

        if ($user->hasRole(RoleGroups::TRAINER)) {
            return '/trainer';
        }

        return '/student';
    }
}
