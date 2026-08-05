<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black">CTA Manager</h1>
            <p class="text-sm text-muted-foreground">{{ $ctas->total() }} CTAs</p>
        </div>
        <button wire:click="openCreate" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">
            <x-core::icon name="plus" class="w-4 h-4" /> New CTA
        </button>
    </div>

    <div class="space-y-3">
        @forelse ($ctas as $cta)
            <div wire:key="cta-{{ $cta->id }}" class="bg-card border border-border rounded-2xl p-4 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="font-semibold text-sm">{{ $cta->name }}</p>
                    <p class="text-xs text-muted-foreground line-clamp-1">{{ $cta->title }}</p>
                    <p class="text-xs text-muted-foreground mt-1">Placements: {{ implode(', ', $cta->placement ?? []) }} | Priority: {{ $cta->priority }}</p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $cta->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $cta->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <button wire:click="openEdit('{{ $cta->id }}')" class="p-1.5 hover:bg-muted rounded-lg transition-colors text-muted-foreground hover:text-foreground" title="Edit">
                        <x-core::icon name="edit" class="w-3.5 h-3.5" />
                    </button>
                    <button wire:click="delete('{{ $cta->id }}')" wire:confirm="Delete?" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors text-muted-foreground hover:text-red-600" title="Delete">
                        <x-core::icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-muted-foreground">No CTAs yet</div>
        @endforelse
    </div>

    @if ($ctas->hasPages())
        <div>{{ $ctas->links() }}</div>
    @endif

    @if ($showForm)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
            <div class="bg-background border border-border rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border">
                    <h2 class="font-bold text-lg">{{ $editingId ? 'Edit CTA' : 'New CTA' }}</h2>
                    <button wire:click="$set('showForm', false)" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">Name *</label>
                            <input wire:model="formData.name" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @error('formData.name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Priority</label>
                            <input wire:model="formData.priority" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium mb-1 block">Title *</label>
                            <input wire:model="formData.title" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @error('formData.title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium mb-1 block">Subtitle</label>
                            <input wire:model="formData.subtitle" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium mb-1 block">Description</label>
                            <textarea wire:model="formData.description" rows="2" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Button 1 Text</label>
                            <input wire:model="formData.button_one_text" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Button 1 Link</label>
                            <input wire:model="formData.button_one_link" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Button 2 Text</label>
                            <input wire:model="formData.button_two_text" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Button 2 Link</label>
                            <input wire:model="formData.button_two_link" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium mb-1 block">Background Color</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model="formData.background_color" class="w-10 h-10 rounded-lg border border-border cursor-pointer">
                                <input type="text" wire:model="formData.background_color" class="flex-1 px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium mb-1.5 block">Placement</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($placements as $placement)
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox" wire:model="formData.placement" value="{{ $placement }}" class="w-4 h-4 rounded">
                                    {{ ucfirst($placement) }}
                                </label>
                            @endforeach
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
