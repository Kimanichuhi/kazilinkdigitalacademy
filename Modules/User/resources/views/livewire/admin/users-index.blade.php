@php
    $roleColors = [
        'student' => 'bg-blue-100 text-blue-700', 'trainer' => 'bg-green-100 text-green-700',
        'admin' => 'bg-orange-100 text-orange-700',
    ];
@endphp
<div class="p-6 space-y-6">
    <div class="flex items-center gap-3">
        <div>
            <h1 class="text-2xl font-black">Users & Permissions</h1>
            <p class="text-sm text-muted-foreground">{{ $users->total() }} users</p>
        </div>
    </div>

    <div class="bg-card border border-border rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border bg-muted/30">
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">User</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Role</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Status</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    @php $role = $user->getRoleNames()->first() ?? 'student'; @endphp
                    <tr wire:key="user-{{ $user->id }}" class="border-b border-border hover:bg-muted/20 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-500 flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($user->name ?: $user->email, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium">{{ $user->name ?: '—' }}</p>
                                    <p class="text-xs text-muted-foreground" data-clarity-mask="true">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3"><span class="text-xs font-medium px-2 py-0.5 rounded-full capitalize {{ $roleColors[$role] ?? 'bg-gray-100 text-gray-600' }}">{{ str_replace('_', ' ', $role) }}</span></td>
                        <td class="px-5 py-3"><span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="px-5 py-3">
                            <button wire:click="edit('{{ $user->id }}')" class="flex items-center gap-1 text-xs text-brand-600 hover:underline">
                                <x-core::icon name="edit" class="w-3 h-3" /> Change Role
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-12 text-center text-muted-foreground">No users found</td></tr>
                @endforelse
            </tbody>
        </table>

        @if ($users->hasPages())
            <div class="px-5 py-3 border-t border-border">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    @if ($editingUserId)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="cancel">
            <div class="bg-background border border-border rounded-2xl w-full max-w-sm shadow-2xl p-6">
                <h2 class="font-bold mb-4">Change Role</h2>
                <select wire:model="newRole" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 mb-4">
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}">{{ str_replace('_', ' ', $role->value) }}</option>
                    @endforeach
                </select>
                @error('newRole') <p class="text-red-500 text-xs mb-3">{{ $message }}</p> @enderror
                <div class="flex gap-3">
                    <button wire:click="cancel" class="flex-1 border border-border rounded-xl px-4 py-2 text-sm font-medium hover:bg-muted transition-colors">Cancel</button>
                    <button wire:click="updateRole" class="flex-1 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">Update Role</button>
                </div>
            </div>
        </div>
    @endif
</div>
