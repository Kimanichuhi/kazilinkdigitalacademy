<?php

namespace Modules\Core\Support;

/**
 * Shared role-group constants used by Policies across every module.
 *
 * Mirrors the exact 8-role partition and admin-family grouping observed in
 * the source app's login redirect and admin layout guard (see
 * MIGRATION-INVENTORY.md §4): everything except `trainer` and `student` is
 * treated as "admin family" and gets into /admin.
 */
final class RoleGroups
{
    public const SUPER_ADMIN = 'super_admin';
    public const ADMIN = 'admin';
    public const TRAINER = 'trainer';
    public const CONTENT_MANAGER = 'content_manager';
    public const FINANCE = 'finance';
    public const MARKETING = 'marketing';
    public const SUPPORT = 'support';
    public const STUDENT = 'student';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::TRAINER,
            self::CONTENT_MANAGER,
            self::FINANCE,
            self::MARKETING,
            self::SUPPORT,
            self::STUDENT,
        ];
    }

    /**
     * Roles that redirect to /admin after login and may access /admin/*.
     *
     * @return list<string>
     */
    public static function adminFamily(): array
    {
        return [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::CONTENT_MANAGER,
            self::FINANCE,
            self::MARKETING,
            self::SUPPORT,
        ];
    }

    public static function isAdminFamily(?object $user): bool
    {
        return (bool) $user?->hasAnyRole(self::adminFamily());
    }
}
