<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black">Pricing Plans</h1>
            <p class="text-sm text-muted-foreground">{{ $plans->total() }} plans — shown on the Pricing page</p>
        </div>
        <button wire:click="openCreate" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">
            <x-core::icon name="plus" class="w-4 h-4" /> Add Plan
        </button>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($plans as $plan)
            <div wire:key="plan-{{ $plan->id }}" class="bg-card border {{ $plan->is_highlighted ? 'border-brand-500' : 'border-border' }} rounded-2xl p-4 flex flex-col gap-2">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <p class="font-semibold text-sm">{{ $plan->name }}</p>
                            @if ($plan->is_highlighted)
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-brand-50 text-brand-600">Highlighted</span>
                            @endif
                            @if (! $plan->is_published)
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Draft</span>
                            @endif
                        </div>
                        @if ($plan->tag)
                            <p class="text-xs text-muted-foreground">{{ $plan->tag }}</p>
                        @endif
                    </div>
                    <div class="flex gap-1.5 flex-shrink-0">
                        <button wire:click="openEdit('{{ $plan->id }}')" class="p-1.5 hover:bg-muted rounded-lg transition-colors text-muted-foreground hover:text-foreground" title="Edit">
                            <x-core::icon name="edit" class="w-3.5 h-3.5" />
                        </button>
                        <button wire:click="delete('{{ $plan->id }}')" wire:confirm="Delete this plan?" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors text-muted-foreground hover:text-red-600" title="Delete">
                            <x-core::icon name="trash" class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
                <p class="text-xl font-black">{{ $plan->currency }} {{ number_format($plan->price) }}<span class="text-xs font-normal text-muted-foreground">{{ $plan->period ? '/'.$plan->period : '' }}</span></p>
                <p class="text-xs text-muted-foreground line-clamp-2">{{ $plan->description }}</p>
                <p class="text-xs text-muted-foreground">{{ count($plan->features ?? []) }} features</p>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-muted-foreground">No pricing plans yet</div>
        @endforelse
    </div>

    @if ($plans->hasPages())
        <div>{{ $plans->links() }}</div>
    @endif

    @if ($showForm)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
            <div class="bg-background border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border">
                    <h2 class="font-bold text-lg">{{ $editingId ? 'Edit Plan' : 'Add Plan' }}</h2>
                    <button wire:click="$set('showForm', false)" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">Plan Name *</label>
                            <input wire:model="formData.name" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @error('formData.name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Tag</label>
                            <input wire:model="formData.tag" placeholder="e.g. Most Popular" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">Price *</label>
                            <input type="number" step="0.01" wire:model="formData.price" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @error('formData.price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Currency</label>
                            <input wire:model="formData.currency" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Period</label>
                            <input wire:model="formData.period" placeholder="e.g. program" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-medium mb-1 block">Description</label>
                        <textarea wire:model="formData.description" rows="2" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="text-xs font-medium mb-1 block">Features (one per line)</label>
                        <textarea wire:model="formData.features" rows="5" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">CTA Text</label>
                            <input wire:model="formData.cta_text" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">CTA Link</label>
                            <input wire:model="formData.cta_link" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="formData.is_published" class="w-4 h-4 rounded">
                            <span class="text-sm">Published</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="formData.is_highlighted" class="w-4 h-4 rounded">
                            <span class="text-sm">Highlighted</span>
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
