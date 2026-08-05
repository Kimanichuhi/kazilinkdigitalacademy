<?php

namespace Modules\User\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\User\Enums\UserRole;
use Spatie\Permission\Models\Role;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (UserRole::cases() as $role) {
            Role::findOrCreate($role->value, 'web');
        }

        foreach (UserRole::cases() as $role) {
            $user = User::firstOrCreate(
                ['email' => "{$role->value}@kazilink.academy"],
                [
                    'name' => $role->label().' Demo',
                    'password' => 'password',
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            $user->syncRoles([$role->value]);
        }
    }
}
