<div>
    <div class="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Free Resources</span>
            <h1 class="text-4xl sm:text-5xl font-black text-white mb-4">Downloads &amp; Guides</h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">Free templates, guides and videos to help you get started earning online.</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
        <div class="bg-card border border-border rounded-2xl p-4 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <x-core::icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search resources..."
                    class="w-full pl-9 pr-4 py-2.5 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                >
            </div>
            <select wire:model.live="type" class="px-3 py-2.5 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
                <option value="">All Types</option>
                <option value="pdf">PDF</option>
                <option value="video">Video</option>
                <option value="template">Template</option>
                <option value="guide">Guide</option>
            </select>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($resources as $resource)
                <div wire:key="resource-{{ $resource['id'] }}" class="bg-card border border-border rounded-2xl p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-brand-50 text-brand-600 uppercase">
                            <x-core::icon name="{{ $resource['type'] === 'video' ? 'play' : ($resource['type'] === 'template' ? 'copy' : 'file-text') }}" class="w-3.5 h-3.5" />
                            {{ $resource['type'] }}
                        </span>
                        @if ($resource['is_paid'] ?? false)
                            <span class="text-xs font-medium text-brand-600">Premium</span>
                        @elseif ($resource['is_free'])
                            <span class="text-xs font-medium text-green-600">Free</span>
                        @endif
                    </div>
                    <h3 class="font-bold">{{ $resource['title'] }}</h3>
                    <p class="text-sm text-muted-foreground line-clamp-2 flex-1">{{ $resource['description'] }}</p>
                    <div class="flex items-center justify-between pt-2 border-t border-border">
                        <span class="text-xs text-muted-foreground">{{ $resource['download_count'] }} downloads</span>
                        <livewire:cms::resource-purchase-dialog :resource-id="$resource['id']" :key="'purchase-'.$resource['id']" />
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-muted-foreground">No resources match your search.</div>
            @endforelse
        </div>

        @if ($resources->hasPages())
            <div>{{ $resources->links() }}</div>
        @endif
    </div>
</div>
