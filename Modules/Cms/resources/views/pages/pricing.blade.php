@php
    $hero = $blocks['hero'][0]['content'] ?? null;
    $installments = collect($blocks['installment_option'] ?? []);
    $scholarships = collect($blocks['scholarship'] ?? []);
    $addons = collect($blocks['addon'] ?? []);
@endphp
<x-core::layouts.public title="Pricing">
    <div class="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Pricing</span>
            <h1 class="text-4xl sm:text-5xl font-black text-white mb-4">{{ $hero['heading'] ?? 'Simple, Transparent Pricing' }}</h1>
            <p class="text-gray-400 text-lg">{{ $hero['subtitle'] ?? 'Invest in skills that pay for themselves.' }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid sm:grid-cols-3 gap-6">
            @forelse ($plans as $plan)
                <div class="bg-card border {{ $plan['is_highlighted'] ? 'border-brand-500 ring-2 ring-brand-500' : 'border-border' }} rounded-2xl p-6 flex flex-col">
                    @if ($plan['tag'])
                        <span class="text-xs font-bold text-brand-600 uppercase mb-2">{{ $plan['tag'] }}</span>
                    @endif
                    <h3 class="font-black text-xl mb-1">{{ $plan['name'] }}</h3>
                    <p class="text-3xl font-black mb-1">{{ $plan['currency'] }} {{ number_format($plan['price']) }}<span class="text-sm font-normal text-muted-foreground">{{ $plan['period'] ? '/'.$plan['period'] : '' }}</span></p>
                    <p class="text-sm text-muted-foreground mb-4">{{ $plan['description'] }}</p>
                    <ul class="space-y-2 mb-6 flex-1">
                        @foreach ($plan['features'] ?? [] as $feature)
                            <li class="flex items-start gap-2 text-sm">
                                <x-core::icon name="check-circle" class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" />
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ url($plan['cta_link'] ?: '/booking') }}" class="text-center rounded-xl px-4 py-2.5 text-sm font-semibold transition-colors {{ $plan['is_highlighted'] ? 'bg-brand-500 hover:bg-brand-600 text-white' : 'border border-border hover:bg-muted' }}">
                        {{ $plan['cta_text'] }}
                    </a>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-muted-foreground">Pricing plans coming soon.</div>
            @endforelse
        </div>

        @if ($installments->isNotEmpty())
            <div class="mt-16">
                <h2 class="font-black text-2xl mb-6 text-center">Flexible Payment Options</h2>
                <div class="grid sm:grid-cols-3 gap-5">
                    @foreach ($installments as $option)
                        <div class="bg-card border border-border rounded-2xl p-5">
                            <h3 class="font-semibold mb-1">{{ $option['content']['heading'] }}</h3>
                            <p class="text-sm text-muted-foreground">{{ $option['content']['body'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($scholarships->isNotEmpty())
            <div class="mt-16">
                <h2 class="font-black text-2xl mb-6 text-center">Scholarships &amp; Discounts</h2>
                <div class="grid sm:grid-cols-2 gap-5">
                    @foreach ($scholarships as $item)
                        <div class="bg-card border border-border rounded-2xl p-5">
                            <h3 class="font-semibold mb-1">{{ $item['content']['heading'] }}</h3>
                            <p class="text-sm text-muted-foreground">{{ $item['content']['body'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($addons->isNotEmpty())
            <div class="mt-16">
                <h2 class="font-black text-2xl mb-6 text-center">Add-ons</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($addons as $addon)
                        <div class="bg-card border border-border rounded-2xl p-4 flex items-center justify-between">
                            <span class="text-sm font-medium">{{ $addon['content']['heading'] }}</span>
                            <span class="text-sm font-bold text-brand-600">KES {{ number_format($addon['content']['meta']['price'] ?? 0) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-core::layouts.public>
