<?php

namespace Modules\Core\Support;

/**
 * Shared role-group constants used by Policies across every module.
 *
 * Only 3 roles exist: `admin`, `trainer`, `student`. The former 8-role
 * partition (super_admin/content_manager/finance/marketing/support) was
 * consolidated into `admin` — see the
 * `2026_08_05_000000_consolidate_roles_to_admin` migration.
 */
final class RoleGroups
{
    public const ADMIN = 'admin';
    public const TRAINER = 'trainer';
    public const STUDENT = 'student';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::ADMIN,
            self::TRAINER,
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
            self::ADMIN,
        ];
    }

    public static function isAdminFamily(?object $user): bool
    {
        return (bool) $user?->hasAnyRole(self::adminFamily());
    }
}
