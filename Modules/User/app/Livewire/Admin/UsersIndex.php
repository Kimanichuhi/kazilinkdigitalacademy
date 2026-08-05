<?php

namespace Modules\User\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Core\Support\RoleGroups;
use Modules\User\Enums\UserRole;
use Modules\User\Events\UserRoleChanged;

#[Layout('admin::components.layouts.admin', ['title' => 'Users & Roles'])]
class UsersIndex extends Component
{
    use WithPagination;

    public ?string $editingUserId = null;

    public string $newRole = '';

    /**
     * Granting roles (including admin itself) is the single most sensitive
     * action in the admin area, so it's restricted to admin only rather
     * than the whole admin-family — every other admin screen uses the
     * broader admin.family route gate, this one needs tighter (though with
     * only 3 roles now, admin family and admin are the same set).
     */
    public function mount(): void
    {
        abort_unless(auth()->user()?->hasRole(RoleGroups::ADMIN), 403);
    }

    public function edit(string $userId): void
    {
        $this->editingUserId = $userId;
        $this->newRole = User::findOrFail($userId)->getRoleNames()->first() ?? 'student';
    }

    public function updateRole(): void
    {
        if (! $this->editingUserId || ! $this->newRole) {
            return;
        }

        // $newRole is a plain public Livewire property — validate it's a
        // real role rather than trusting whatever the client last sent.
        if (! in_array($this->newRole, array_column(UserRole::cases(), 'value'), true)) {
            return;
        }

        $user = User::findOrFail($this->editingUserId);

        $isLastAdmin = $user->hasRole(RoleGroups::ADMIN)
            && $this->newRole !== RoleGroups::ADMIN
            && User::role(RoleGroups::ADMIN)->count() <= 1;

        if ($isLastAdmin) {
            $this->addError('newRole', 'Cannot remove the last remaining Admin.');

            return;
        }

        $oldRole = $user->getRoleNames()->first();

        $user->syncRoles([$this->newRole]);

        UserRoleChanged::dispatch(
            $user->id,
            $oldRole,
            $this->newRole,
            auth()->id(),
            request()->ip(),
            request()->userAgent(),
        );

        $this->editingUserId = null;
        $this->newRole = '';
    }

    public function cancel(): void
    {
        $this->editingUserId = null;
    }

    public function render()
    {
        return view('user::livewire.admin.users-index', [
            'users' => User::with('roles')->orderByDesc('created_at')->paginate(25),
            'roles' => UserRole::cases(),
        ]);
    }
}
