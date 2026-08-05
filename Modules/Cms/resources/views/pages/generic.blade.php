<x-core::layouts.public :title="$page['title']">
    <div class="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-14">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl font-black text-white">{{ $page['title'] }}</h1>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        @forelse ($blocks as $block)
            <div>
                @if (!empty($block['content']['heading']))
                    <h2 class="font-bold text-lg mb-2">{{ $block['content']['heading'] }}</h2>
                @endif
                @if (!empty($block['content']['body']))
                    <div class="text-sm text-muted-foreground leading-relaxed whitespace-pre-line">{{ $block['content']['body'] }}</div>
                @endif
            </div>
        @empty
            <p class="text-sm text-muted-foreground">{{ $page['description'] ?? 'This page has no content yet.' }}</p>
        @endforelse
    </div>
</x-core::layouts.public>
