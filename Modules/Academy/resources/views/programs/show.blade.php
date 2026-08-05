@php
    $curriculum = is_array($program['curriculum'] ?? null) ? $program['curriculum'] : [];
@endphp
<x-core::layouts.public :title="$program['title']">
    <div x-data="{ activeTab: 'overview', openFaq: null }">
        <!-- Hero -->
        <div class="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-12 sm:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-10">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-brand-500/20 text-brand-400 capitalize">
                                {{ str_replace('_', ' ', $program['level'] ?? '') }}
                            </span>
                            <span class="text-gray-400 text-xs capitalize">{{ str_replace('_', ' ', $program['delivery_mode'] ?? '') }}</span>
                        </div>
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-4 leading-tight">{{ $program['title'] }}</h1>
                        @if (! empty($program['subtitle']))
                            <p class="text-lg text-gray-300 mb-5">{{ $program['subtitle'] }}</p>
                        @endif

                        <div class="flex flex-wrap gap-5 text-sm text-gray-400 mb-6">
                            @if (! empty($program['duration_label']))
                                <span class="flex items-center gap-2"><x-core::icon name="clock" class="w-4 h-4 text-brand-400" />{{ $program['duration_label'] }}</span>
                            @endif
                            @if (($program['enrollment_count'] ?? 0) > 0)
                                <span class="flex items-center gap-2"><x-core::icon name="users" class="w-4 h-4 text-brand-400" />{{ number_format($program['enrollment_count']) }} enrolled</span>
                            @endif
                            @if (($program['rating'] ?? 0) > 0)
                                <span class="flex items-center gap-2 text-amber-400">
                                    <x-core::icon name="star" class="w-4 h-4 fill-current" />{{ number_format($program['rating'], 1) }} ({{ $program['review_count'] }} reviews)
                                </span>
                            @endif
                        </div>

                        @if (! empty($program['outcomes']))
                            <div class="space-y-2">
                                <p class="text-white font-semibold text-sm mb-3">What you'll achieve:</p>
                                <div class="grid sm:grid-cols-2 gap-2">
                                    @foreach (array_slice($program['outcomes'], 0, 4) as $outcome)
                                        <div class="flex items-start gap-2 text-sm text-gray-300">
                                            <x-core::icon name="check-circle" class="w-4 h-4 text-green-400 flex-shrink-0 mt-0.5" />
                                            {{ $outcome }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sticky enrollment card -->
                    <div class="lg:w-80 xl:w-96">
                        <div class="bg-card border border-border rounded-2xl overflow-hidden shadow-2xl">
                            @if (! empty($program['thumbnail_url']))
                                <img src="{{ $program['thumbnail_url'] }}" alt="{{ $program['title'] }}" class="w-full h-48 object-cover">
                            @endif
                            <div class="p-5">
                                <div class="flex items-baseline gap-3 mb-4">
                                    <span class="text-3xl font-black text-foreground">{{ $program['currency'] }} {{ number_format($program['price'] ?? 0) }}</span>
                                    @if (! empty($program['original_price']) && $program['original_price'] > ($program['price'] ?? 0))
                                        <span class="text-muted-foreground line-through text-sm">{{ number_format($program['original_price']) }}</span>
                                    @endif
                                </div>

                                <a href="{{ url('/booking?program='.$program['id']) }}" class="w-full flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold py-3.5 rounded-xl transition-colors mb-3 shadow-lg shadow-brand-500/25">
                                    Enroll Now <x-core::icon name="arrow-right" class="w-4 h-4" />
                                </a>
                                <a href="{{ url('/contact') }}" class="w-full flex items-center justify-center gap-2 border-2 border-border text-foreground hover:border-brand-500 hover:text-brand-600 font-semibold py-3.5 rounded-xl transition-colors text-sm">
                                    Book Free Consultation
                                </a>

                                <div class="mt-4 pt-4 border-t border-border space-y-2 text-sm text-muted-foreground">
                                    @if (! empty($program['duration_label']))
                                        <div class="flex justify-between"><span>Duration</span><span class="font-medium text-foreground">{{ $program['duration_label'] }}</span></div>
                                    @endif
                                    <div class="flex justify-between"><span>Format</span><span class="font-medium text-foreground capitalize">{{ str_replace('_', ' ', $program['delivery_mode'] ?? '') }}</span></div>
                                    <div class="flex justify-between"><span>Level</span><span class="font-medium text-foreground capitalize">{{ str_replace('_', ' ', $program['level'] ?? '') }}</span></div>
                                    <div class="flex justify-between"><span>Certificate</span><span class="font-medium text-green-600 flex items-center gap-1"><x-core::icon name="award" class="w-3.5 h-3.5" />Included</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Tabs -->
            <div class="flex gap-1 bg-muted rounded-xl p-1 mb-8 w-fit overflow-x-auto">
                @foreach (['overview', 'curriculum', 'trainer', 'reviews'] as $tab)
                    <button
                        @click="activeTab = '{{ $tab }}'"
                        :class="activeTab === '{{ $tab }}' ? 'bg-white text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-colors capitalize whitespace-nowrap"
                    >{{ $tab }}</button>
                @endforeach
            </div>

            <div class="grid lg:grid-cols-3 gap-10">
                <div class="lg:col-span-2 space-y-10">
                    <!-- Overview -->
                    <div x-show="activeTab === 'overview'" class="space-y-10">
                        @if (! empty($program['description']))
                            <div>
                                <h2 class="text-2xl font-bold mb-4">About This Program</h2>
                                <p class="text-muted-foreground leading-relaxed">{{ $program['description'] }}</p>
                            </div>
                        @endif
                        @if (! empty($program['outcomes']))
                            <div>
                                <h2 class="text-2xl font-bold mb-4">What You'll Learn</h2>
                                <div class="grid sm:grid-cols-2 gap-3">
                                    @foreach ($program['outcomes'] as $outcome)
                                        <div class="flex items-start gap-2">
                                            <x-core::icon name="check-circle" class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" />
                                            <span class="text-sm text-foreground">{{ $outcome }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if (! empty($program['requirements']))
                            <div>
                                <h2 class="text-2xl font-bold mb-4">Requirements</h2>
                                <ul class="space-y-2">
                                    @foreach ($program['requirements'] as $requirement)
                                        <li class="flex items-start gap-2 text-sm text-muted-foreground">
                                            <div class="w-1.5 h-1.5 rounded-full bg-brand-500 mt-2 flex-shrink-0"></div>
                                            {{ $requirement }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <!-- Curriculum -->
                    <div x-show="activeTab === 'curriculum'" x-cloak>
                        <h2 class="text-2xl font-bold mb-6">Program Curriculum</h2>
                        @if (count($curriculum) > 0)
                            <div class="space-y-3">
                                @foreach ($curriculum as $i => $module)
                                    <div class="border border-border rounded-xl overflow-hidden">
                                        <div class="flex items-center justify-between px-5 py-4 bg-muted/50 font-semibold text-sm">
                                            <span>Week {{ $i + 1 }}: {{ $module['title'] ?? '' }}</span>
                                            <span class="text-xs text-muted-foreground">{{ count($module['lessons'] ?? []) }} lessons</span>
                                        </div>
                                        @if (! empty($module['lessons']))
                                            <div class="px-5 py-3 space-y-2">
                                                @foreach ($module['lessons'] as $lesson)
                                                    <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                                        <x-core::icon name="book-open" class="w-3.5 h-3.5 text-brand-500" />{{ $lesson }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted-foreground">Detailed curriculum available upon enrollment. Contact us for more information.</p>
                        @endif
                    </div>

                    <!-- Trainer tab -->
                    <div x-show="activeTab === 'trainer'" x-cloak>
                        <h2 class="text-2xl font-bold mb-6">Your Trainer</h2>
                        <p class="text-muted-foreground">Trainer information is available for specific cohorts. Check the upcoming cohorts section below.</p>
                    </div>

                    <!-- Reviews -->
                    <div x-show="activeTab === 'reviews'" x-cloak>
                        <h2 class="text-2xl font-bold mb-6">Student Reviews</h2>
                        @if (count($testimonials) > 0)
                            <div class="space-y-4">
                                @foreach ($testimonials as $testimonial)
                                    <x-cms::testimonial-card :testimonial="$testimonial" />
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted-foreground">No reviews yet for this program.</p>
                        @endif
                    </div>

                    <!-- Upcoming Cohorts -->
                    @if (count($cohorts) > 0)
                        <div>
                            <h2 class="text-2xl font-bold mb-5">Upcoming Cohorts</h2>
                            <div class="grid sm:grid-cols-2 gap-4">
                                @foreach ($cohorts as $cohort)
                                    <x-academy::cohort-card :cohort="$cohort" />
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- FAQs -->
                    @if (count($faqs) > 0)
                        <div>
                            <h2 class="text-2xl font-bold mb-5">Frequently Asked Questions</h2>
                            <div class="space-y-2">
                                @foreach (array_slice($faqs, 0, 6) as $faq)
                                    <div class="border border-border rounded-xl overflow-hidden">
                                        <button
                                            @click="openFaq = openFaq === '{{ $faq['id'] }}' ? null : '{{ $faq['id'] }}'"
                                            class="w-full flex items-center justify-between px-5 py-4 text-sm font-medium text-left hover:bg-muted/50 transition-colors"
                                        >
                                            {{ $faq['question'] }}
                                            <x-core::icon name="chevron-down" x-show="openFaq !== '{{ $faq['id'] }}'" class="w-4 h-4 flex-shrink-0" />
                                            <x-core::icon name="chevron-up" x-show="openFaq === '{{ $faq['id'] }}'" x-cloak class="w-4 h-4 flex-shrink-0" />
                                        </button>
                                        <div x-show="openFaq === '{{ $faq['id'] }}'" x-cloak class="px-5 pb-4 text-sm text-muted-foreground leading-relaxed border-t border-border pt-3">
                                            {{ $faq['answer'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    @if (count($related) > 0)
                        <div>
                            <h3 class="font-bold text-lg mb-4">Related Programs</h3>
                            <div class="space-y-4">
                                @foreach ($related as $relatedProgram)
                                    <x-academy::program-card :program="$relatedProgram" />
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-core::layouts.public>
