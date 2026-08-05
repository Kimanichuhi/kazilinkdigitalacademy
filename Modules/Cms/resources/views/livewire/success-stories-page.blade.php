<div>
    <div class="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Real Results</span>
            <h1 class="text-4xl sm:text-5xl font-black text-white mb-4">Success Stories</h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">Real students, real income, real transformations.</p>
        </div>
    </div>

    @if ($stats->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach (['students_trained' => 'Students Trained', 'avg_rating' => 'Average Rating', 'success_rate' => 'Success Rate', 'avg_income_increase' => 'Avg. Income Increase'] as $key => $label)
                    <div class="bg-card border border-border rounded-2xl p-4 text-center shadow-sm">
                        <p class="text-2xl font-black text-brand-600">{{ $stats[$key]['value'] ?? '—' }}</p>
                        <p class="text-xs text-muted-foreground mt-1">{{ $stats[$key]['label'] ?? $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
        <div class="bg-card border border-border rounded-2xl p-4 flex items-center gap-3">
            <span class="text-sm font-medium text-muted-foreground">Filter by rating:</span>
            <div class="flex gap-2">
                <button wire:click="$set('minRating', '')" class="text-xs font-medium px-3 py-1.5 rounded-full transition-colors {{ $minRating === '' ? 'bg-brand-500 text-white' : 'bg-muted text-muted-foreground hover:bg-border' }}">All</button>
                @foreach ([5, 4, 3] as $rating)
                    <button wire:click="$set('minRating', '{{ $rating }}')" class="text-xs font-medium px-3 py-1.5 rounded-full transition-colors {{ $minRating == $rating ? 'bg-brand-500 text-white' : 'bg-muted text-muted-foreground hover:bg-border' }}">{{ $rating }}+ Stars</button>
                @endforeach
            </div>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($testimonials as $testimonial)
                <div wire:key="testimonial-{{ $testimonial->id }}" class="bg-card border border-border rounded-2xl p-5 flex flex-col gap-3">
                    <x-core::icon name="quote" class="w-6 h-6 text-brand-300" />
                    <p class="text-sm text-muted-foreground flex-1">{{ $testimonial->content }}</p>
                    <div class="flex items-center gap-1">
                        @for ($i = 0; $i < 5; $i++)
                            <x-core::icon name="star" class="w-3.5 h-3.5 {{ $i < (int) $testimonial->rating ? 'text-yellow-400' : 'text-gray-300' }}" />
                        @endfor
                    </div>
                    <div class="flex items-center gap-3 pt-2 border-t border-border">
                        <img src="{{ $testimonial->student_avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode($testimonial->student_name) }}" loading="lazy" class="w-10 h-10 rounded-full object-cover" alt="">
                        <div class="min-w-0">
                            <p class="font-semibold text-sm truncate">{{ $testimonial->student_name }}</p>
                            <p class="text-xs text-muted-foreground truncate">{{ $testimonial->student_title }}</p>
                        </div>
                    </div>
                    @if ($testimonial->income_before && $testimonial->income_after)
                        <p class="text-xs text-green-600 font-medium">{{ $testimonial->income_before }} → {{ $testimonial->income_after }}/mo</p>
                    @endif
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-muted-foreground">No stories match this filter yet.</div>
            @endforelse
        </div>

        @if ($testimonials->hasPages())
            <div>{{ $testimonials->links() }}</div>
        @endif

        <div class="bg-gradient-to-br from-brand-500 to-navy-600 rounded-2xl p-8 text-center text-white mt-6">
            <h3 class="font-bold text-xl mb-2">Ready to write your own success story?</h3>
            <a href="{{ url('/programs') }}" class="inline-flex items-center gap-2 bg-white text-navy-900 rounded-xl px-5 py-2.5 text-sm font-semibold hover:bg-gray-100 transition-colors mt-2">
                Explore Programs <x-core::icon name="arrow-right" class="w-4 h-4" />
            </a>
        </div>
    </div>
</div>
