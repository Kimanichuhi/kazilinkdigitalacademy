<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black">FAQs</h1>
            <p class="text-sm text-muted-foreground">{{ $faqs->total() }} questions</p>
        </div>
        <button wire:click="openCreate" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">
            <x-core::icon name="plus" class="w-4 h-4" /> Add FAQ
        </button>
    </div>

    <div class="bg-card border border-border rounded-2xl p-4">
        <div class="relative">
            <x-core::icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search questions..."
                class="w-full pl-9 pr-4 py-2.5 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
            >
        </div>
    </div>

    <div class="space-y-3">
        @forelse ($faqs as $faq)
            <div wire:key="faq-{{ $faq->id }}" class="bg-card border border-border rounded-2xl p-4 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-brand-50 text-brand-600 capitalize">{{ $faq->category }}</span>
                        @if (! $faq->is_published)
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Draft</span>
                        @endif
                        @if ($faq->is_popular)
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-amber-50 text-amber-600">Popular</span>
                        @endif
                    </div>
                    <p class="font-semibold text-sm">{{ $faq->question }}</p>
                    <p class="text-xs text-muted-foreground line-clamp-1 mt-0.5">{{ $faq->answer }}</p>
                </div>
                <div class="flex gap-1.5 flex-shrink-0">
                    <button wire:click="openEdit('{{ $faq->id }}')" class="p-1.5 hover:bg-muted rounded-lg transition-colors text-muted-foreground hover:text-foreground" title="Edit">
                        <x-core::icon name="edit" class="w-3.5 h-3.5" />
                    </button>
                    <button wire:click="delete('{{ $faq->id }}')" wire:confirm="Delete this FAQ?" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors text-muted-foreground hover:text-red-600" title="Delete">
                        <x-core::icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-muted-foreground">No FAQs yet</div>
        @endforelse
    </div>

    @if ($faqs->hasPages())
        <div>{{ $faqs->links() }}</div>
    @endif

    @if ($showForm)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
            <div class="bg-background border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border">
                    <h2 class="font-bold text-lg">{{ $editingId ? 'Edit FAQ' : 'Add FAQ' }}</h2>
                    <button wire:click="$set('showForm', false)" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">Category</label>
                            <input wire:model="formData.category" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Order</label>
                            <input type="number" wire:model="formData.order_index" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-medium mb-1 block">Question *</label>
                        <input wire:model="formData.question" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        @error('formData.question') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium mb-1 block">Answer *</label>
                        <textarea wire:model="formData.answer" rows="5" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                        @error('formData.answer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="formData.is_published" class="w-4 h-4 rounded">
                            <span class="text-sm">Published</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="formData.is_popular" class="w-4 h-4 rounded">
                            <span class="text-sm">Popular question</span>
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
