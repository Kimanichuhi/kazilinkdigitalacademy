@props(['program'])
@php
    $levelColors = [
        'beginner' => 'bg-green-100 text-green-700',
        'intermediate' => 'bg-blue-100 text-blue-700',
        'advanced' => 'bg-orange-100 text-orange-700',
        'all_levels' => 'bg-gray-100 text-gray-700',
    ];
    $levelLabels = [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        'all_levels' => 'All Levels',
    ];
    $level = $program['level'] ?? 'beginner';
    $hasDiscount = ! empty($program['original_price']) && ! empty($program['price']) && $program['original_price'] > $program['price'];
@endphp

<a href="{{ url('/programs/'.$program['slug']) }}" {{ $attributes->merge(['class' => 'group block']) }}>
    <div class="bg-card border border-border rounded-2xl overflow-hidden card-hover h-full flex flex-col">
        <div class="relative overflow-hidden">
            <div class="aspect-[16/9] bg-muted overflow-hidden">
                @if (! empty($program['thumbnail_url']))
                    <img src="{{ $program['thumbnail_url'] }}" alt="{{ $program['title'] }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-brand-400 to-navy-600 flex items-center justify-center">
                        <x-core::icon name="trending-up" class="w-12 h-12 text-white/50" />
                    </div>
                @endif
            </div>
            @if ($program['is_featured'] ?? false)
                <div class="absolute top-3 left-3">
                    <span class="bg-orange-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">Featured</span>
                </div>
            @endif
            @if ($hasDiscount)
                <div class="absolute top-3 right-3">
                    <span class="bg-green-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                        {{ round((1 - $program['price'] / $program['original_price']) * 100) }}% OFF
                    </span>
                </div>
            @endif
        </div>

        <div class="p-5 flex flex-col flex-1">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $levelColors[$level] ?? $levelColors['beginner'] }}">
                    {{ $levelLabels[$level] ?? $level }}
                </span>
                <span class="text-xs text-muted-foreground capitalize">{{ str_replace('_', ' ', $program['delivery_mode'] ?? '') }}</span>
            </div>

            <h3 class="font-bold text-foreground text-base mb-2 line-clamp-2 group-hover:text-brand-600 transition-colors">
                {{ $program['title'] }}
            </h3>

            @if (! empty($program['short_description']))
                <p class="text-sm text-muted-foreground line-clamp-2 mb-4 flex-1">{{ $program['short_description'] }}</p>
            @endif

            <div class="flex items-center gap-4 text-xs text-muted-foreground mb-4">
                @if (! empty($program['duration_label']))
                    <span class="flex items-center gap-1"><x-core::icon name="clock" class="w-3.5 h-3.5" />{{ $program['duration_label'] }}</span>
                @endif
                @if (($program['enrollment_count'] ?? 0) > 0)
                    <span class="flex items-center gap-1"><x-core::icon name="users" class="w-3.5 h-3.5" />{{ number_format($program['enrollment_count']) }} enrolled</span>
                @endif
                @if (($program['rating'] ?? 0) > 0)
                    <span class="flex items-center gap-1 text-amber-500">
                        <x-core::icon name="star" class="w-3.5 h-3.5 fill-current" />
                        {{ number_format($program['rating'], 1) }}
                        <span class="text-muted-foreground">({{ $program['review_count'] }})</span>
                    </span>
                @endif
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-border">
                <div>
                    @if (! empty($program['price']))
                        <div class="flex items-baseline gap-2">
                            <span class="text-lg font-bold text-foreground">{{ $program['currency'] }} {{ number_format($program['price']) }}</span>
                            @if ($hasDiscount)
                                <span class="text-sm text-muted-foreground line-through">{{ number_format($program['original_price']) }}</span>
                            @endif
                        </div>
                    @else
                        <span class="text-lg font-bold text-green-600">Free</span>
                    @endif
                </div>
                <span class="flex items-center gap-1 text-brand-600 text-sm font-medium group-hover:gap-2 transition-all">
                    Enroll <x-core::icon name="arrow-right" class="w-3.5 h-3.5" />
                </span>
            </div>
        </div>
    </div>
</a>
