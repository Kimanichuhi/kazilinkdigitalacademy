<div class="p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-black">Pages</h1>
        <p class="text-sm text-muted-foreground">Content blocks for About, Pricing, Privacy, Terms &amp; Refund</p>
    </div>

    @if (session('pages-saved') && ! $editingSlug)
        <div class="bg-green-50 text-green-700 text-sm rounded-xl px-4 py-3">{{ session('pages-saved') }}</div>
    @endif

    <div class="space-y-3">
        @foreach ($pages as $page)
            <div wire:key="page-{{ $page->id }}" class="bg-card border border-border rounded-2xl p-4 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <p class="font-semibold text-sm">{{ $page->title }}</p>
                        @if (! $page->is_published)
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Draft</span>
                        @endif
                    </div>
                    <p class="text-xs text-muted-foreground">/{{ $page->slug }} &middot; {{ $page->blocks_count }} content blocks</p>
                </div>
                <button wire:click="openEdit('{{ $page->slug }}')" class="inline-flex items-center gap-2 border border-border rounded-xl px-4 py-2 text-sm font-medium hover:bg-muted transition-colors flex-shrink-0">
                    <x-core::icon name="edit" class="w-4 h-4" /> Manage Content
                </button>
            </div>
        @endforeach
    </div>

    @if ($editingSlug)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="close">
            <div class="bg-background border border-border rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border sticky top-0 bg-background">
                    <h2 class="font-bold text-lg">Edit /{{ $editingSlug }}</h2>
                    <button wire:click="close" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-5">
                    @if (session('pages-saved'))
                        <div class="bg-green-50 text-green-700 text-sm rounded-xl px-4 py-3">{{ session('pages-saved') }}</div>
                    @endif

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">Page Title *</label>
                            <input wire:model="pageMeta.title" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @error('pageMeta.title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer mt-6">
                            <input type="checkbox" wire:model="pageMeta.is_published" class="w-4 h-4 rounded">
                            <span class="text-sm">Published</span>
                        </label>
                    </div>

                    <datalist id="block-types-{{ $editingSlug }}">
                        @foreach ($blockTypes as $type)
                            <option value="{{ $type }}"></option>
                        @endforeach
                    </datalist>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-sm">Content Blocks ({{ count($blocks) }})</h3>
                            <button wire:click="addBlock" class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-600 hover:text-brand-700">
                                <x-core::icon name="plus" class="w-4 h-4" /> Add Block
                            </button>
                        </div>

                        @forelse ($blocks as $index => $block)
                            <div wire:key="block-{{ $index }}" class="border border-border rounded-xl p-4 space-y-3 bg-muted/30">
                                <div class="flex items-start gap-3">
                                    <div class="flex-1 grid sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-xs font-medium mb-1 block">Type</label>
                                            <input list="block-types-{{ $editingSlug }}" wire:model="blocks.{{ $index }}.type" class="w-full px-3 py-1.5 text-sm border border-border rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                        </div>
                                        <label class="flex items-center gap-2 cursor-pointer mt-5">
                                            <input type="checkbox" wire:model="blocks.{{ $index }}.is_active" class="w-4 h-4 rounded">
                                            <span class="text-sm">Active</span>
                                        </label>
                                    </div>
                                    <button wire:click="removeBlock({{ $index }})" wire:confirm="Remove this block?" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors text-muted-foreground hover:text-red-600 flex-shrink-0" title="Remove">
                                        <x-core::icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                                <div class="grid sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs font-medium mb-1 block">Heading</label>
                                        <input wire:model="blocks.{{ $index }}.heading" class="w-full px-3 py-1.5 text-sm border border-border rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium mb-1 block">Subtitle</label>
                                        <input wire:model="blocks.{{ $index }}.subtitle" class="w-full px-3 py-1.5 text-sm border border-border rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-medium mb-1 block">Body</label>
                                    <textarea wire:model="blocks.{{ $index }}.body" rows="3" class="w-full px-3 py-1.5 text-sm border border-border rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="text-xs font-medium mb-1 block">Extra fields (JSON, optional — e.g. <code>{"icon": "star"}</code> or <code>{"price": 500}</code>)</label>
                                    <textarea wire:model="blocks.{{ $index }}.meta_json" rows="2" class="w-full px-3 py-1.5 text-xs font-mono border border-border rounded-lg bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-muted-foreground text-center py-6">No blocks yet — click "Add Block" to create one.</p>
                        @endforelse
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-border">
                        <button wire:click="close" class="border border-border rounded-xl px-4 py-2 text-sm font-medium hover:bg-muted transition-colors">Close</button>
                        <button wire:click="save" class="bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">Save Content</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
