<div>
    <!-- Page Header -->
    <div class="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Training Programs</span>
            <h1 class="text-4xl sm:text-5xl font-black text-white mb-4">Find the Right Course for Your Future</h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Whether you want to earn online, start a business, improve your career, or master digital technology, we have a course designed to help you succeed.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Search & Filters -->
        <div class="bg-card border border-border rounded-2xl p-4 mb-8 space-y-4">
            <div class="flex gap-3">
                <div class="relative flex-1">
                    <x-core::icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search programs..."
                        class="w-full pl-9 pr-4 py-2.5 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                    >
                    @if ($search)
                        <button wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                            <x-core::icon name="x" class="w-4 h-4" />
                        </button>
                    @endif
                </div>
                <button wire:click="$toggle('filtersOpen')" class="inline-flex items-center gap-2 whitespace-nowrap border border-border rounded-xl px-4 py-2.5 text-sm font-medium hover:bg-muted transition-colors">
                    <x-core::icon name="sliders" class="w-4 h-4" /> Filters
                    @if ($this->hasFilters)
                        <span class="bg-brand-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">!</span>
                    @endif
                </button>
            </div>

            @if ($filtersOpen)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2 border-t border-border">
                    <div>
                        <label class="text-xs font-medium text-muted-foreground mb-1.5 block">Category</label>
                        <select wire:model.live="selectedCategory" class="w-full px-3 py-2 text-sm bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-muted-foreground mb-1.5 block">Level</label>
                        <select wire:model.live="selectedLevel" class="w-full px-3 py-2 text-sm bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="">All Levels</option>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-muted-foreground mb-1.5 block">Format</label>
                        <select wire:model.live="selectedMode" class="w-full px-3 py-2 text-sm bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="">All Formats</option>
                            <option value="online">Online</option>
                            <option value="in_person">In-Person</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-muted-foreground mb-1.5 block">Sort By</label>
                        <select wire:model.live="sortBy" class="w-full px-3 py-2 text-sm bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="order_index">Featured</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                            <option value="rating">Top Rated</option>
                            <option value="enrollment_count">Most Popular</option>
                        </select>
                    </div>
                </div>
            @endif

            @if ($this->hasFilters)
                <div class="flex items-center gap-2 pt-1">
                    <span class="text-xs text-muted-foreground">{{ $programs->total() }} results</span>
                    <button wire:click="clearFilters" class="text-xs text-brand-600 hover:text-brand-700 flex items-center gap-1 ml-auto">
                        <x-core::icon name="x" class="w-3 h-3" /> Clear all filters
                    </button>
                </div>
            @endif
        </div>

        <!-- Category pills -->
        @if (count($categories) > 0)
            <div class="flex gap-2 flex-wrap mb-8">
                <button
                    wire:click="$set('selectedCategory', '')"
                    class="text-sm font-medium px-4 py-2 rounded-full transition-colors {{ ! $selectedCategory ? 'bg-brand-500 text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80' }}"
                >
                    All
                </button>
                @foreach ($categories as $category)
                    <button
                        wire:click="toggleCategory('{{ $category['id'] }}')"
                        class="text-sm font-medium px-4 py-2 rounded-full transition-colors {{ $selectedCategory === $category['id'] ? 'bg-brand-500 text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80' }}"
                    >
                        {{ $category['name'] }}
                    </button>
                @endforeach
            </div>
        @endif

        <!-- Programs grid -->
        <div wire:loading.class="opacity-50" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 transition-opacity">
            @forelse ($programs as $program)
                <x-academy::program-card :program="$program" wire:key="program-{{ $program['id'] }}" />
            @empty
                <div class="col-span-3 text-center py-20">
                    <x-core::icon name="search" class="w-12 h-12 text-muted-foreground/30 mx-auto mb-4" />
                    <h3 class="text-lg font-semibold text-muted-foreground mb-2">No programs found</h3>
                    <p class="text-sm text-muted-foreground mb-4">Try adjusting your filters or search term.</p>
                    <button wire:click="clearFilters" class="inline-flex items-center border border-border rounded-xl px-4 py-2 text-sm font-medium hover:bg-muted transition-colors">Clear Filters</button>
                </div>
            @endforelse
        </div>

        @if ($programs->hasPages())
            <div class="mt-8">{{ $programs->links() }}</div>
        @endif
    </div>
</div>
