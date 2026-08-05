<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Modules\Core\Support\RoleGroups;

/**
 * The exact 8-role redirect partition from the source
 * (app/auth/login/page.tsx:44-49, see MIGRATION-INVENTORY.md §4):
 *
 *   admin-family roles -> /admin
 *   trainer            -> /trainer
 *   everyone else      -> /student
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
