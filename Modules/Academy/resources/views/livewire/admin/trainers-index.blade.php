<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div><h1 class="text-2xl font-black">Trainers</h1><p class="text-sm text-muted-foreground">{{ $trainers->total() }} {{ $showTrashed ? 'deleted' : '' }} trainers</p></div>
        <div class="flex items-center gap-2">
            <button wire:click="$toggle('showTrashed')" class="inline-flex items-center gap-2 border border-border rounded-xl px-4 py-2 text-sm font-medium hover:bg-muted transition-colors">
                <x-core::icon name="trash" class="w-4 h-4" /> {{ $showTrashed ? 'Back to Active' : 'Deleted Trainers' }}
            </button>
            @if (! $showTrashed)
                <button wire:click="openCreate" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">
                    <x-core::icon name="plus" class="w-4 h-4" /> Add Trainer
                </button>
            @endif
        </div>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($trainers as $trainer)
            <div wire:key="trainer-{{ $trainer->id }}" class="bg-card border border-border rounded-2xl p-5">
                <div class="flex items-start gap-3 mb-3">
                    @if ($trainer->avatar_url)
                        <img src="{{ $trainer->avatar_url }}" alt="" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                    @else
                        <div class="w-12 h-12 rounded-full bg-brand-500 text-white font-bold flex items-center justify-center flex-shrink-0">{{ strtoupper(substr($trainer->full_name, 0, 1)) }}</div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="font-bold truncate">{{ $trainer->full_name }}</p>
                        @if ($trainer->title)
                            <p class="text-xs text-brand-600">{{ $trainer->title }}</p>
                        @endif
                    </div>
                    <div class="flex gap-1 flex-shrink-0">
                        @if ($trainer->trashed())
                            <button wire:click="restore('{{ $trainer->id }}')" wire:confirm="Restore this trainer?" class="p-1.5 hover:bg-green-50 rounded-lg" title="Restore"><x-core::icon name="refresh-cw" class="w-3.5 h-3.5 text-muted-foreground" /></button>
                            @if ($canForceDelete)
                                <button wire:click="forceDelete('{{ $trainer->id }}')" wire:confirm="Permanently delete this trainer? This cannot be undone." class="p-1.5 hover:bg-red-50 rounded-lg" title="Delete Permanently"><x-core::icon name="x" class="w-3.5 h-3.5 text-red-600" /></button>
                            @endif
                        @else
                            <button wire:click="openEdit('{{ $trainer->id }}')" class="p-1.5 hover:bg-muted rounded-lg"><x-core::icon name="edit" class="w-3.5 h-3.5 text-muted-foreground" /></button>
                            <button wire:click="delete('{{ $trainer->id }}')" wire:confirm="Delete this trainer?" class="p-1.5 hover:bg-red-50 rounded-lg"><x-core::icon name="trash" class="w-3.5 h-3.5 text-muted-foreground" /></button>
                        @endif
                    </div>
                </div>
                @if (! empty($trainer->specializations))
                    <div class="flex flex-wrap gap-1">
                        @foreach (array_slice($trainer->specializations, 0, 3) as $s)
                            <span class="text-[10px] bg-brand-50 text-brand-700 px-2 py-0.5 rounded-full">{{ $s }}</span>
                        @endforeach
                    </div>
                @endif
                <div class="flex items-center justify-between mt-3 text-xs text-muted-foreground">
                    @if ($trainer->rating > 0)
                        <span class="flex items-center gap-1 text-amber-500"><x-core::icon name="star" class="w-3 h-3 fill-current" />{{ number_format($trainer->rating, 1) }}</span>
                    @endif
                    <span class="px-2 py-0.5 rounded-full {{ $trainer->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $trainer->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12 text-muted-foreground">No trainers found</div>
        @endforelse
    </div>

    @if ($trainers->hasPages())
        <div>{{ $trainers->links() }}</div>
    @endif

    @if ($showForm)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
            <div class="bg-background border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border">
                    <h2 class="font-bold">{{ $editingId ? 'Edit' : 'New' }} Trainer</h2>
                    <button wire:click="$set('showForm', false)" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-3">
                    <livewire:core::image-upload wire:model="formData.avatar_url" folder="trainers" label="Avatar Photo" shape="circle" :key="'avatar-'.($editingId ?? 'new')" />

                    <div>
                        <label class="text-xs font-medium mb-1 block">Full Name *</label>
                        <input wire:model="formData.full_name" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        @error('formData.full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium mb-1 block">Title/Role</label>
                        <input wire:model="formData.title" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium mb-1 block">Email</label>
                        <input wire:model="formData.email" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium mb-1 block">Phone</label>
                        <input wire:model="formData.phone" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium mb-1 block">Specializations (comma-separated)</label>
                        <input wire:model="formData.specializations" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium mb-1 block">Bio</label>
                        <textarea wire:model="formData.bio" rows="3" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                    </div>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="checkbox" wire:model="formData.is_featured" class="w-4 h-4 rounded">Featured</label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm"><input type="checkbox" wire:model="formData.is_active" class="w-4 h-4 rounded">Active</label>
                    </div>
                    <div class="flex justify-end gap-3 pt-2 border-t border-border">
                        <button wire:click="$set('showForm', false)" class="border border-border rounded-xl px-4 py-2 text-sm font-medium hover:bg-muted transition-colors">Cancel</button>
                        <button wire:click="save" class="bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">{{ $editingId ? 'Update' : 'Create' }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
