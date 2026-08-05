<div>
    <div class="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Blog</span>
            <h1 class="text-4xl sm:text-5xl font-black text-white mb-4">Insights &amp; Guides</h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">Tips, tutorials and stories to help you earn more online.</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        <div class="bg-card border border-border rounded-2xl p-4 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <x-core::icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search articles..."
                    class="w-full pl-9 pr-4 py-2.5 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                >
            </div>
            <select wire:model.live="categoryId" class="px-3 py-2.5 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                @endforeach
            </select>
        </div>

        @if ($featured)
            <a href="{{ url('/blog/'.$featured['slug']) }}" class="block bg-card border border-border rounded-2xl overflow-hidden hover:shadow-lg transition-shadow">
                <div class="grid sm:grid-cols-2">
                    <div class="aspect-video sm:aspect-auto bg-gradient-to-br from-brand-500 to-navy-600">
                        @if ($featured['thumbnail_url'])
                            <img src="{{ $featured['thumbnail_url'] }}" class="w-full h-full object-cover" alt="">
                        @endif
                    </div>
                    <div class="p-6 flex flex-col justify-center">
                        <span class="text-xs font-semibold text-brand-600 uppercase mb-2">Featured</span>
                        <h2 class="text-2xl font-black mb-2">{{ $featured['title'] }}</h2>
                        <p class="text-sm text-muted-foreground line-clamp-3">{{ $featured['excerpt'] }}</p>
                        <p class="text-xs text-muted-foreground mt-3">{{ $featured['read_time_minutes'] }} min read</p>
                    </div>
                </div>
            </a>
        @endif

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($posts as $post)
                <a wire:key="post-{{ $post['id'] }}" href="{{ url('/blog/'.$post['slug']) }}" class="bg-card border border-border rounded-2xl overflow-hidden flex flex-col hover:shadow-lg transition-shadow">
                    <div class="aspect-video bg-gradient-to-br from-brand-500 to-navy-600">
                        @if ($post['thumbnail_url'])
                            <img src="{{ $post['thumbnail_url'] }}" loading="lazy" class="w-full h-full object-cover" alt="">
                        @endif
                    </div>
                    <div class="p-5 flex flex-col gap-2 flex-1">
                        @if ($post['category'])
                            <span class="text-xs font-semibold uppercase" style="color: {{ $post['category']['color'] ?? '#f97316' }}">{{ $post['category']['name'] }}</span>
                        @endif
                        <h3 class="font-bold line-clamp-2">{{ $post['title'] }}</h3>
                        <p class="text-sm text-muted-foreground line-clamp-2 flex-1">{{ $post['excerpt'] }}</p>
                        <p class="text-xs text-muted-foreground">{{ $post['read_time_minutes'] }} min read</p>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-16 text-center text-muted-foreground">No articles match your search.</div>
            @endforelse
        </div>

        @if ($posts->hasPages())
            <div>{{ $posts->links() }}</div>
        @endif
    </div>
</div>
