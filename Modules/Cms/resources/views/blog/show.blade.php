<x-core::layouts.public :title="$post['title']">
    <div class="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            @if ($post['category'])
                <span class="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">{{ $post['category']['name'] }}</span>
            @endif
            <h1 class="text-3xl sm:text-4xl font-black text-white mb-4">{{ $post['title'] }}</h1>
            <p class="text-gray-400 text-sm">{{ \Illuminate\Support\Carbon::parse($post['published_at'])->format('M j, Y') }} &middot; {{ $post['read_time_minutes'] }} min read</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if ($post['thumbnail_url'])
            <img src="{{ $post['thumbnail_url'] }}" class="w-full aspect-video object-cover rounded-2xl mb-8" alt="">
        @endif

        <div class="prose max-w-none text-sm leading-relaxed">
            {!! nl2br(e($post['content'])) !!}
        </div>

        @if (! empty($post['tags']))
            <div class="flex flex-wrap gap-2 mt-8 pt-6 border-t border-border">
                @foreach ($post['tags'] as $tag)
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-muted text-muted-foreground">#{{ $tag }}</span>
                @endforeach
            </div>
        @endif

        @if ($related->isNotEmpty())
            <div class="mt-12 pt-8 border-t border-border">
                <h2 class="font-bold text-xl mb-5">More Articles</h2>
                <div class="grid sm:grid-cols-3 gap-5">
                    @foreach ($related as $item)
                        <a href="{{ url('/blog/'.$item['slug']) }}" class="bg-card border border-border rounded-2xl p-4 hover:shadow-lg transition-shadow">
                            <h3 class="font-semibold text-sm line-clamp-2">{{ $item['title'] }}</h3>
                            <p class="text-xs text-muted-foreground mt-2">{{ $item['read_time_minutes'] }} min read</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-10">
            <a href="{{ url('/blog') }}" class="inline-flex items-center gap-2 text-sm font-medium text-brand-600 hover:text-brand-700">
                <x-core::icon name="chevron-left" class="w-4 h-4" /> Back to Blog
            </a>
        </div>
    </div>
</x-core::layouts.public>
