@php
    $hero = $blocks['hero'][0]['content'] ?? null;
    $mvv = collect($blocks['mission_vision_value'] ?? []);
    $coreValues = collect($blocks['core_value'] ?? []);
    $story = $blocks['story'][0]['content'] ?? null;
    $timeline = collect($blocks['timeline_item'] ?? []);
@endphp
<x-core::layouts.public title="About Us">
    <div class="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">About Us</span>
            <h1 class="text-4xl sm:text-5xl font-black text-white mb-4">{{ $hero['heading'] ?? 'Empowering Kenyans to Earn Online' }}</h1>
            <p class="text-gray-400 text-lg">{{ $hero['subtitle'] ?? 'We equip students with practical, income-generating digital skills.' }}</p>
        </div>
    </div>

    @if ($mvv->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid sm:grid-cols-2 gap-5">
                @foreach ($mvv as $item)
                    <div class="bg-card border border-border rounded-2xl p-6 text-center">
                        <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center mx-auto mb-4">
                            <x-core::icon :name="$item['content']['meta']['icon'] ?? 'award'" class="w-5 h-5 text-brand-600" />
                        </div>
                        <h3 class="font-bold text-lg mb-2">{{ $item['content']['heading'] }}</h3>
                        <p class="text-sm text-muted-foreground">{{ $item['content']['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($coreValues->isNotEmpty())
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 text-center">
            <h2 class="font-black text-2xl mb-6">Core Values</h2>
            <div class="flex flex-wrap justify-center gap-3">
                @foreach ($coreValues as $item)
                    <span class="inline-flex items-center gap-1.5 bg-brand-50 text-brand-700 text-sm font-semibold px-4 py-2 rounded-full">
                        <x-core::icon name="check-circle" class="w-3.5 h-3.5" />
                        {{ $item['content']['heading'] }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    @if ($story)
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h2 class="font-black text-2xl mb-4">{{ $story['heading'] ?? 'Our Story' }}</h2>
            <div class="text-sm text-muted-foreground leading-relaxed space-y-4 whitespace-pre-line">{{ $story['body'] }}</div>
        </div>
    @endif

    @if ($timeline->isNotEmpty())
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h2 class="font-black text-2xl mb-6 text-center">Our Journey</h2>
            <div class="space-y-6 border-l-2 border-brand-200 pl-6">
                @foreach ($timeline as $item)
                    <div>
                        <span class="text-xs font-bold text-brand-600">{{ $item['content']['subtitle'] }}</span>
                        <h3 class="font-semibold">{{ $item['content']['heading'] }}</h3>
                        <p class="text-sm text-muted-foreground">{{ $item['content']['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if (count($trainers))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="font-black text-2xl mb-6 text-center">Meet Our Trainers</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach ($trainers as $trainer)
                    <div class="bg-card border border-border rounded-2xl p-5 text-center">
                        <img src="{{ $trainer['avatar_url'] ?? 'https://ui-avatars.com/api/?name='.urlencode($trainer['full_name'] ?? 'T') }}" loading="lazy" class="w-16 h-16 rounded-full object-cover mx-auto mb-3" alt="">
                        <p class="font-semibold text-sm">{{ $trainer['full_name'] ?? '' }}</p>
                        <p class="text-xs text-muted-foreground">{{ $trainer['title'] ?? $trainer['specialty'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($teamMembers->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="font-black text-2xl mb-6 text-center">Leadership Team</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach ($teamMembers as $member)
                    <div class="bg-card border border-border rounded-2xl p-5 text-center">
                        <img src="{{ $member->avatar_url ?: 'https://ui-avatars.com/api/?name='.urlencode($member->full_name) }}" loading="lazy" class="w-16 h-16 rounded-full object-cover mx-auto mb-3" alt="">
                        <p class="font-semibold text-sm">{{ $member->full_name }}</p>
                        <p class="text-xs text-muted-foreground">{{ $member->title }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if (count($testimonials))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="font-black text-2xl mb-6 text-center">What Our Students Say</h2>
            <div class="grid sm:grid-cols-3 gap-5">
                @foreach ($testimonials as $testimonial)
                    <div class="bg-card border border-border rounded-2xl p-5">
                        <x-core::icon name="quote" class="w-5 h-5 text-brand-300 mb-2" />
                        <p class="text-sm text-muted-foreground mb-3">{{ $testimonial['content'] }}</p>
                        <p class="font-semibold text-sm">{{ $testimonial['student_name'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-core::layouts.public>
