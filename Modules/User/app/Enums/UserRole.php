<?php

namespace Modules\User\Enums;

use Modules\Core\Support\RoleGroups;

/**
 * The 3 roles this app uses, backed by spatie/laravel-permission roles
 * instead of a column. This enum exists for type-safe references in
 * seeders/forms — role storage itself lives in the `roles` /
 * `model_has_roles` tables, not a column on `users`.
 */
enum UserRole: string
{
    case Admin = RoleGroups::ADMIN;
    case Trainer = RoleGroups::TRAINER;
    case Student = RoleGroups::STUDENT;

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Trainer => 'Trainer',
            self::Student => 'Student',
        };
    }
}
