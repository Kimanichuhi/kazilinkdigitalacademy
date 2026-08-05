@props(['cohort'])
@php
    $statusConfig = [
        'upcoming' => ['label' => 'Upcoming', 'class' => 'bg-blue-100 text-blue-700'],
        'open' => ['label' => 'Open for Registration', 'class' => 'bg-green-100 text-green-700'],
        'full' => ['label' => 'Fully Booked', 'class' => 'bg-red-100 text-red-700'],
        'in_progress' => ['label' => 'In Progress', 'class' => 'bg-orange-100 text-orange-700'],
        'completed' => ['label' => 'Completed', 'class' => 'bg-gray-100 text-gray-700'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-gray-100 text-gray-500'],
    ];
    $status = $statusConfig[$cohort['status']] ?? $statusConfig['upcoming'];
    $seatsLeft = $cohort['seats_left'] ?? max(0, $cohort['total_seats'] - $cohort['booked_seats']);
    $fillPercent = $cohort['total_seats'] > 0 ? round(($cohort['booked_seats'] / $cohort['total_seats']) * 100) : 0;
    $program = $cohort['program'] ?? null;
    $trainer = $cohort['trainer'] ?? null;
@endphp

<div {{ $attributes->merge(['class' => 'bg-card border border-border rounded-2xl p-5 card-hover flex flex-col gap-4']) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="flex-1 min-w-0">
            <h3 class="font-bold text-foreground text-base line-clamp-2 mb-1">{{ $cohort['name'] }}</h3>
            @if ($program)
                <p class="text-sm text-brand-600 font-medium">{{ $program['title'] }}</p>
            @endif
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap flex-shrink-0 {{ $status['class'] }}">
            {{ $status['label'] }}
        </span>
    </div>

    <div class="space-y-2 text-sm text-muted-foreground">
        <div class="flex items-center gap-2">
            <x-core::icon name="calendar" class="w-4 h-4 flex-shrink-0 text-brand-500" />
            <span>
                {{ $cohort['start_date'] ? \Illuminate\Support\Carbon::parse($cohort['start_date'])->format('d M Y') : 'TBD' }}
                @if ($cohort['end_date'])
                    &ndash; {{ \Illuminate\Support\Carbon::parse($cohort['end_date'])->format('d M Y') }}
                @endif
            </span>
        </div>
        @if (! empty($cohort['schedule_details']))
            <div class="flex items-center gap-2">
                <x-core::icon name="clock" class="w-4 h-4 flex-shrink-0 text-brand-500" />
                <span>{{ $cohort['schedule_details'] }}</span>
            </div>
        @endif
        @if (! empty($cohort['venue']))
            <div class="flex items-center gap-2">
                <x-core::icon name="map-pin" class="w-4 h-4 flex-shrink-0 text-brand-500" />
                <span>{{ $cohort['venue'] }}</span>
            </div>
        @elseif (! empty($cohort['online_platform']))
            <div class="flex items-center gap-2">
                <x-core::icon name="wifi" class="w-4 h-4 flex-shrink-0 text-brand-500" />
                <span>Online via {{ $cohort['online_platform'] }}</span>
            </div>
        @endif
        @if ($trainer)
            <div class="flex items-center gap-2">
                @if (! empty($trainer['avatar_url']))
                    <img src="{{ $trainer['avatar_url'] }}" alt="{{ $trainer['full_name'] }}" loading="lazy" class="w-5 h-5 rounded-full object-cover border border-border">
                @else
                    <div class="w-5 h-5 rounded-full bg-brand-500 flex items-center justify-center text-white text-[10px] font-bold">
                        {{ strtoupper(substr($trainer['full_name'], 0, 1)) }}
                    </div>
                @endif
                <span>Trainer: <span class="font-medium text-foreground">{{ $trainer['full_name'] }}</span></span>
            </div>
        @endif
    </div>

    <div>
        <div class="flex justify-between text-xs mb-1.5">
            <span class="text-muted-foreground"><x-core::icon name="users" class="w-3.5 h-3.5 inline mr-1" />{{ $cohort['booked_seats'] }}/{{ $cohort['total_seats'] }} seats</span>
            <span class="font-semibold {{ $seatsLeft <= 5 ? 'text-red-500' : 'text-green-600' }}">
                {{ $seatsLeft > 0 ? "{$seatsLeft} seats left" : 'Fully booked' }}
            </span>
        </div>
        <div class="h-1.5 bg-muted rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all {{ $fillPercent >= 90 ? 'bg-red-500' : ($fillPercent >= 60 ? 'bg-orange-500' : 'bg-green-500') }}" style="width: {{ $fillPercent }}%"></div>
        </div>
    </div>

    <div class="flex items-center justify-between pt-1 border-t border-border">
        <div>
            @if (! empty($cohort['price']))
                <span class="text-lg font-bold">{{ $cohort['currency'] }} {{ number_format($cohort['price']) }}</span>
            @elseif ($program && ! empty($program['price']))
                <span class="text-lg font-bold">{{ $cohort['currency'] }} {{ number_format($program['price']) }}</span>
            @endif
        </div>
        @if (in_array($cohort['status'], ['open', 'upcoming']))
            <a href="{{ url('/booking?cohort='.$cohort['id']) }}" class="flex items-center gap-1 bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                Book Now <x-core::icon name="arrow-right" class="w-3.5 h-3.5" />
            </a>
        @else
            <span class="text-sm text-muted-foreground">Not available</span>
        @endif
    </div>
</div>
