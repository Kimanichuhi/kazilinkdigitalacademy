<?php

namespace Modules\Cms\Policies;

use App\Models\User;
use Modules\Core\Support\RoleGroups;

/**
 * Shared policy for every Cms model that mirrors the source's "public
 * SELECT, admin-only write" RLS pattern: blog_categories, blog_posts,
 * testimonials, faqs, resources, pages, page_blocks, nav_menus, nav_items,
 * site_settings, team_members, partners.
 */
class PublicContentPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function update(User $user, $model): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }

    public function delete(User $user, $model): bool
    {
        return $user->hasAnyRole(RoleGroups::adminFamily());
    }
}
