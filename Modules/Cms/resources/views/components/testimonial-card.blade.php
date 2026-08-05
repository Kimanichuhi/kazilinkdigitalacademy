@props(['testimonial'])

<div {{ $attributes->merge(['class' => 'bg-card border border-border rounded-2xl p-6 flex flex-col gap-4']) }}>
    <div class="flex items-start justify-between">
        <x-core::icon name="quote" class="w-8 h-8 text-brand-500/30" />
        @if (! empty($testimonial['rating']))
            <div class="flex gap-0.5">
                @for ($i = 0; $i < (int) $testimonial['rating']; $i++)
                    <x-core::icon name="star" class="w-4 h-4 fill-amber-400 text-amber-400" />
                @endfor
            </div>
        @endif
    </div>

    <p class="text-sm text-muted-foreground leading-relaxed line-clamp-4 flex-1">&ldquo;{{ $testimonial['content'] }}&rdquo;</p>

    @if (! empty($testimonial['income_before']) || ! empty($testimonial['income_after']))
        <div class="flex gap-3 p-3 bg-green-50 rounded-xl border border-green-100">
            @if (! empty($testimonial['income_before']))
                <div class="text-center flex-1">
                    <p class="text-xs text-muted-foreground">Before</p>
                    <p class="text-sm font-semibold text-red-600">{{ $testimonial['income_before'] }}</p>
                </div>
            @endif
            @if (! empty($testimonial['income_before']) && ! empty($testimonial['income_after']))
                <div class="w-px bg-border"></div>
            @endif
            @if (! empty($testimonial['income_after']))
                <div class="text-center flex-1">
                    <p class="text-xs text-muted-foreground">After</p>
                    <p class="text-sm font-semibold text-green-600">{{ $testimonial['income_after'] }}</p>
                </div>
            @endif
        </div>
    @endif

    <div class="flex items-center gap-3 pt-2 border-t border-border">
        @if (! empty($testimonial['student_avatar_url']))
            <img src="{{ $testimonial['student_avatar_url'] }}" alt="{{ $testimonial['student_name'] }}" loading="lazy" class="w-10 h-10 rounded-full object-cover">
        @else
            <div class="w-10 h-10 rounded-full bg-brand-500 flex items-center justify-center text-white font-bold text-sm">
                {{ strtoupper(substr($testimonial['student_name'], 0, 1)) }}
            </div>
        @endif
        <div>
            <p class="text-sm font-semibold">{{ $testimonial['student_name'] }}</p>
            @if (! empty($testimonial['student_title']))
                <p class="text-xs text-muted-foreground">{{ $testimonial['student_title'] }}</p>
            @endif
        </div>
    </div>
</div>
