<?php

namespace Modules\User\Enums;

use Modules\Core\Support\RoleGroups;

/**
 * The 8 roles from the source `profiles.role` CHECK constraint, now backed
 * by spatie/laravel-permission roles instead of a column. This enum exists
 * for type-safe references in seeders/forms — role storage itself lives in
 * the `roles` / `model_has_roles` tables, not a column on `users`.
 */
enum UserRole: string
{
    case SuperAdmin = RoleGroups::SUPER_ADMIN;
    case Admin = RoleGroups::ADMIN;
    case Trainer = RoleGroups::TRAINER;
    case ContentManager = RoleGroups::CONTENT_MANAGER;
    case Finance = RoleGroups::FINANCE;
    case Marketing = RoleGroups::MARKETING;
    case Support = RoleGroups::SUPPORT;
    case Student = RoleGroups::STUDENT;

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Trainer => 'Trainer',
            self::ContentManager => 'Content Manager',
            self::Finance => 'Finance',
            self::Marketing => 'Marketing',
            self::Support => 'Support',
            self::Student => 'Student',
        };
    }
}
