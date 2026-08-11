<div>
    <div class="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Real Results</span>
            <h1 class="text-4xl sm:text-5xl font-black text-white mb-4">Real People.<br>Real Skills.<br>Real Success.</h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">Every success story began with a single decision—to learn.</p>
        </div>
    </div>

    @if ($stats->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach (['students_trained' => 'Students Trained', 'certificates_awarded' => 'Certificates Awarded', 'freelancers_started' => 'Freelancers Started'] as $key => $label)
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

        <!-- STUDENT GALLERY -->
        <div>
            <h2 class="font-black text-2xl mb-6 text-center">Student Gallery</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ([
                    ['icon' => 'calendar', 'label' => 'Live Zoom Classes'],
                    ['icon' => 'book-open', 'label' => 'Students Working on Laptops'],
                    ['icon' => 'users', 'label' => 'Community Meetups'],
                    ['icon' => 'message-square', 'label' => 'Video Testimonials'],
                ] as $item)
                    <div class="bg-card border border-border rounded-2xl p-6 text-center aspect-square flex flex-col items-center justify-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center">
                            <x-core::icon :name="$item['icon']" class="w-5 h-5 text-brand-600" />
                        </div>
                        <p class="text-sm font-semibold">{{ $item['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- WHERE I AM AFTER TRAINING -->
        <div>
            <h2 class="font-black text-2xl mb-6 text-center">Where I Am After Training</h2>
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ([
                    ['icon' => 'sparkles', 'label' => 'Self-employed'],
                    ['icon' => 'globe', 'label' => 'Remote Worker'],
                    ['icon' => 'user', 'label' => 'Freelancer'],
                    ['icon' => 'dollar-sign', 'label' => 'Small Business Owner'],
                    ['icon' => 'layout-dashboard', 'label' => 'Digital Agency Employee'],
                    ['icon' => 'shield', 'label' => 'NGO Professional'],
                ] as $item)
                    <div class="bg-card border border-border rounded-2xl p-5 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center flex-shrink-0">
                            <x-core::icon :name="$item['icon']" class="w-4 h-4 text-brand-600" />
                        </div>
                        <p class="text-sm font-semibold">{{ $item['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- WALL OF LOVE -->
        @if ($testimonials->total() > 0)
            <div>
                <h2 class="font-black text-2xl mb-6 text-center">Wall of Love</h2>
                <div class="columns-1 sm:columns-2 lg:columns-3 gap-4 [&>div]:mb-4 [&>div]:break-inside-avoid">
                    @foreach ($testimonials as $testimonial)
                        <div class="bg-card border border-border rounded-2xl p-4">
                            <p class="text-sm text-muted-foreground mb-2">&ldquo;{{ \Illuminate\Support\Str::limit($testimonial->content, 120) }}&rdquo;</p>
                            <p class="text-xs font-semibold">{{ $testimonial->student_name }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- FINAL CTA -->
        <div class="bg-gradient-to-br from-brand-500 to-navy-600 rounded-2xl p-8 text-center text-white mt-6">
            <h3 class="font-bold text-2xl mb-3">Your Success Story Could Be Next.</h3>
            <p class="text-white/80 max-w-lg mx-auto mb-2">Thousands of learners begin with uncertainty.</p>
            <p class="text-white/80 max-w-lg mx-auto mb-6">They leave with confidence, practical skills and new opportunities. Take the first step today.</p>
            <a href="{{ url('/register') }}" class="inline-flex items-center gap-2 bg-white text-navy-900 rounded-xl px-5 py-2.5 text-sm font-semibold hover:bg-gray-100 transition-colors">
                Enroll Now <x-core::icon name="arrow-right" class="w-4 h-4" />
            </a>
        </div>
    </div>
</div>
