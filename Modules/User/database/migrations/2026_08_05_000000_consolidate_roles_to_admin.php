<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * The app now uses only 3 roles (admin/trainer/student). Any user still
 * holding one of the 5 retired roles (super_admin/content_manager/finance/
 * marketing/support) is reassigned to `admin` — mirroring how `admin` has
 * always been a superset of those via RoleGroups::adminFamily(). The
 * retired Role rows are then deleted; spatie's `model_has_roles` FK is
 * `onDelete('cascade')` (see create_permission_tables migration), so this
 * also removes any now-stale pivot rows in one step.
 */
return new class extends Migration
{
    private const RETIRED_ROLES = ['super_admin', 'content_manager', 'finance', 'marketing', 'support'];

    public function up(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $retiredRoleIds = Role::whereIn('name', self::RETIRED_ROLES)
            ->where('guard_name', 'web')
            ->pluck('id', 'name');

        if ($retiredRoleIds->isEmpty()) {
            return;
        }

        $affectedUserIds = DB::table('model_has_roles')
            ->whereIn('role_id', $retiredRoleIds)
            ->where('model_type', \App\Models\User::class)
            ->pluck('model_id')
            ->unique();

        foreach ($affectedUserIds as $userId) {
            DB::table('model_has_roles')->updateOrInsert(
                ['role_id' => $adminRole->id, 'model_id' => $userId, 'model_type' => \App\Models\User::class],
                []
            );
        }

        Role::whereIn('id', $retiredRoleIds)->delete();

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        // Not reversible: which specific retired role each affected user
        // held is discarded once consolidated into `admin`. Recreate the
        // retired role rows (empty, unassigned) so the schema matches the
        // pre-migration shape if ever needed.
        foreach (self::RETIRED_ROLES as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }
};
