<div>
    <div class="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Support</span>
            <h1 class="text-4xl sm:text-5xl font-black text-white mb-4">Frequently Asked Questions</h1>
            <p class="text-gray-400 text-lg">Answers to the most common questions about our programs, payments and bookings.</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
        <div class="bg-card border border-border rounded-2xl p-4 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <x-core::icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search questions..."
                    class="w-full pl-9 pr-4 py-2.5 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                >
            </div>
            <select wire:model.live="category" class="px-3 py-2.5 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
        </div>

        @if ($popular->isNotEmpty() && ! $search && ! $category)
            <div class="space-y-3">
                <h2 class="font-bold text-lg">Popular Questions</h2>
                <div class="space-y-2" x-data="{ open: null }">
                    @foreach ($popular as $faq)
                        <div wire:key="popular-faq-{{ $faq->id }}" class="bg-card border border-border rounded-2xl overflow-hidden">
                            <button @click="open = open === '{{ $faq->id }}' ? null : '{{ $faq->id }}'" class="w-full flex items-center justify-between gap-4 p-4 text-left">
                                <span class="font-semibold text-sm">{{ $faq->question }}</span>
                                <x-core::icon name="chevron-down" class="w-4 h-4 flex-shrink-0 transition-transform" x-bind:class="open === '{{ $faq->id }}' ? 'rotate-180' : ''" />
                            </button>
                            <div x-show="open === '{{ $faq->id }}'" x-collapse class="px-4 pb-4 text-sm text-muted-foreground">
                                {{ $faq->answer }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @forelse ($grouped as $categoryName => $faqs)
            <div class="space-y-3">
                <h2 class="font-bold text-lg capitalize">{{ $categoryName }}</h2>
                <div class="space-y-2" x-data="{ open: null }">
                    @foreach ($faqs as $faq)
                        <div wire:key="faq-{{ $faq->id }}" class="bg-card border border-border rounded-2xl overflow-hidden">
                            <button @click="open = open === {{ $loop->parent->index }}_{{ $loop->index }} ? null : '{{ $loop->parent->index }}_{{ $loop->index }}'" class="w-full flex items-center justify-between gap-4 p-4 text-left">
                                <span class="font-semibold text-sm">{{ $faq->question }}</span>
                                <x-core::icon name="chevron-down" class="w-4 h-4 flex-shrink-0 transition-transform" x-bind:class="open === '{{ $loop->parent->index }}_{{ $loop->index }}' ? 'rotate-180' : ''" />
                            </button>
                            <div x-show="open === '{{ $loop->parent->index }}_{{ $loop->index }}'" x-collapse class="px-4 pb-4 text-sm text-muted-foreground">
                                {{ $faq->answer }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="py-16 text-center text-muted-foreground">No FAQs match your search.</div>
        @endforelse

        <div class="bg-gradient-to-br from-brand-500 to-navy-600 rounded-2xl p-8 text-center text-white mt-10">
            <h3 class="font-bold text-xl mb-2">Didn't find what you were looking for?</h3>
            <p class="text-white/80 mb-4">Send us a message below.</p>
            <a href="{{ url('/contact') }}" class="inline-flex items-center gap-2 bg-white text-navy-900 rounded-xl px-5 py-2.5 text-sm font-semibold hover:bg-gray-100 transition-colors">
                Contact Us <x-core::icon name="arrow-right" class="w-4 h-4" />
            </a>
        </div>
    </div>
</div>
