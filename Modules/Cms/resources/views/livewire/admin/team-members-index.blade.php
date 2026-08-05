<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black">Team Members</h1>
            <p class="text-sm text-muted-foreground">{{ $members->total() }} people — shown on the About page</p>
        </div>
        <button wire:click="openCreate" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">
            <x-core::icon name="plus" class="w-4 h-4" /> Add Member
        </button>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($members as $member)
            <div wire:key="member-{{ $member->id }}" class="bg-card border border-border rounded-2xl p-4 flex items-start justify-between gap-3">
                <div class="min-w-0 flex items-center gap-3">
                    <img src="{{ $member->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode($member->full_name) }}" class="w-11 h-11 rounded-full object-cover flex-shrink-0" alt="">
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <p class="font-semibold text-sm truncate">{{ $member->full_name }}</p>
                            @if (! $member->is_active)
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Hidden</span>
                            @endif
                        </div>
                        <p class="text-xs text-muted-foreground truncate">{{ $member->title }}</p>
                    </div>
                </div>
                <div class="flex gap-1.5 flex-shrink-0">
                    <button wire:click="openEdit('{{ $member->id }}')" class="p-1.5 hover:bg-muted rounded-lg transition-colors text-muted-foreground hover:text-foreground" title="Edit">
                        <x-core::icon name="edit" class="w-3.5 h-3.5" />
                    </button>
                    <button wire:click="delete('{{ $member->id }}')" wire:confirm="Delete this team member?" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors text-muted-foreground hover:text-red-600" title="Delete">
                        <x-core::icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-muted-foreground">No team members yet</div>
        @endforelse
    </div>

    @if ($members->hasPages())
        <div>{{ $members->links() }}</div>
    @endif

    @if ($showForm)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
            <div class="bg-background border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border">
                    <h2 class="font-bold text-lg">{{ $editingId ? 'Edit Member' : 'Add Member' }}</h2>
                    <button wire:click="$set('showForm', false)" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="text-xs font-medium mb-1 block">Full Name *</label>
                        <input wire:model="formData.full_name" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        @error('formData.full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">Title</label>
                            <input wire:model="formData.title" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Department</label>
                            <input wire:model="formData.department" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-medium mb-1 block">Bio</label>
                        <textarea wire:model="formData.bio" rows="3" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">Avatar URL</label>
                            <input wire:model="formData.avatar_url" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Email</label>
                            <input type="email" wire:model="formData.email" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="formData.is_active" class="w-4 h-4 rounded">
                            <span class="text-sm">Active</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="formData.is_featured" class="w-4 h-4 rounded">
                            <span class="text-sm">Featured</span>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-border">
                        <button wire:click="$set('showForm', false)" class="border border-border rounded-xl px-4 py-2 text-sm font-medium hover:bg-muted transition-colors">Cancel</button>
                        <button wire:click="save" class="bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">
                            {{ $editingId ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
