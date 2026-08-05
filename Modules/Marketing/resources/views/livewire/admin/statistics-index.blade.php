@php
    $iconMap = [
        'Users' => 'users', 'BookOpen' => 'book-open', 'TrendingUp' => 'trending-up', 'Globe' => 'globe',
        'Award' => 'award', 'DollarSign' => 'dollar-sign', 'Zap' => 'zap', 'Star' => 'star',
    ];
@endphp
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black">Stats</h1>
            <p class="text-sm text-muted-foreground">{{ $stats->total() }} stats — shown in the homepage stats bar, and by key on the About, Success Stories, and hero sections</p>
        </div>
        <button wire:click="openCreate" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors flex-shrink-0">
            <x-core::icon name="plus" class="w-4 h-4" /> Add Stat
        </button>
    </div>

    <div class="space-y-3">
        @forelse ($stats as $stat)
            <div wire:key="stat-{{ $stat->id }}" class="bg-card border border-border rounded-2xl p-4 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                    <x-core::icon :name="$iconMap[$stat->icon] ?? 'trending-up'" class="w-5 h-5" />
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-bold">{{ $stat->value }}</p>
                        <p class="text-sm text-muted-foreground">{{ $stat->label }}</p>
                        @if ($stat->key)
                            <span class="text-xs font-mono px-1.5 py-0.5 rounded bg-brand-50 text-brand-600">{{ $stat->key }}</span>
                        @endif
                        @if (! $stat->is_active)
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Inactive</span>
                        @endif
                    </div>
                    @if ($stat->description)
                        <p class="text-xs text-muted-foreground line-clamp-1 mt-0.5">{{ $stat->description }}</p>
                    @endif
                </div>
                <div class="flex gap-1.5 flex-shrink-0">
                    <button wire:click="openEdit('{{ $stat->id }}')" class="p-1.5 hover:bg-muted rounded-lg transition-colors text-muted-foreground hover:text-foreground" title="Edit">
                        <x-core::icon name="edit" class="w-3.5 h-3.5" />
                    </button>
                    <button wire:click="delete('{{ $stat->id }}')" wire:confirm="Delete this stat? Any page referencing its key will fall back to a default value." class="p-1.5 hover:bg-red-50 rounded-lg transition-colors text-muted-foreground hover:text-red-600" title="Delete">
                        <x-core::icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-muted-foreground">No stats yet. Add one to show it in the homepage stats bar.</div>
        @endforelse
    </div>

    @if ($stats->hasPages())
        <div>{{ $stats->links() }}</div>
    @endif

    @if ($showForm)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
            <div class="bg-background border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border">
                    <h2 class="font-bold text-lg">{{ $editingId ? 'Edit Stat' : 'Add Stat' }}</h2>
                    <button wire:click="$set('showForm', false)" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">Label *</label>
                            <input wire:model="formData.label" placeholder="Students Trained" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @error('formData.label') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Value *</label>
                            <input wire:model="formData.value" placeholder="12,500+" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @error('formData.value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Icon</label>
                            <select wire:model="formData.icon" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                @foreach ($icons as $icon)
                                    <option value="{{ $icon }}">{{ $icon }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Order</label>
                            <input type="number" wire:model="formData.order_index" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium mb-1 block">Key</label>
                            <input wire:model="formData.key" placeholder="students_trained" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background font-mono focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <p class="text-xs text-muted-foreground mt-1">Binds this stat to specific site sections — e.g. <code>students_trained</code>, <code>success_rate</code>, <code>avg_rating</code> are used on the homepage hero, About, and Success Stories sections. Leave blank to only show in the homepage stats bar.</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium mb-1 block">Description</label>
                            <input wire:model="formData.description" placeholder="Graduates across all programs" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="formData.is_active" class="w-4 h-4 rounded">
                        <span class="text-sm">Active</span>
                    </label>

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
