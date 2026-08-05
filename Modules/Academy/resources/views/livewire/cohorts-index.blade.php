<div>
    <div class="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Enrollment Open</span>
            <h1 class="text-4xl sm:text-5xl font-black text-white mb-4">Upcoming Cohorts</h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                {{ $cohorts->total() }} cohort{{ $cohorts->total() === 1 ? '' : 's' }} open for enrollment right now.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
        <div class="bg-card border border-border rounded-2xl p-4">
            <div class="relative">
                <x-core::icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by program or cohort name..."
                    class="w-full pl-9 pr-4 py-2.5 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                >
            </div>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($cohorts as $cohort)
                <div wire:key="cohort-{{ $cohort['id'] }}" class="bg-card border border-border rounded-2xl p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-brand-50 text-brand-600 capitalize">{{ str_replace('_', ' ', $cohort['status']) }}</span>
                        <span class="text-xs text-muted-foreground">{{ $cohort['seats_left'] }} seats left</span>
                    </div>
                    <div>
                        <h3 class="font-bold">{{ $cohort['program']['title'] ?? $cohort['name'] }}</h3>
                        <p class="text-sm text-muted-foreground">{{ $cohort['name'] }}</p>
                    </div>
                    <div class="text-sm space-y-1 text-muted-foreground">
                        <p class="flex items-center gap-2"><x-core::icon name="calendar" class="w-4 h-4" /> Starts {{ \Illuminate\Support\Carbon::parse($cohort['start_date'])->format('M j, Y') }}</p>
                        @if ($cohort['trainer']['full_name'] ?? null)
                            <p class="flex items-center gap-2"><x-core::icon name="user" class="w-4 h-4" /> {{ $cohort['trainer']['full_name'] }}</p>
                        @endif
                        @if ($cohort['venue'] ?? null)
                            <p class="flex items-center gap-2"><x-core::icon name="map-pin" class="w-4 h-4" /> {{ $cohort['venue'] }}</p>
                        @endif
                    </div>
                    <a href="{{ url('/programs/'.($cohort['program']['slug'] ?? '')) }}" class="text-center border border-border rounded-xl px-4 py-2 text-sm font-semibold hover:bg-muted transition-colors mt-auto">
                        View Program
                    </a>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-muted-foreground">No cohorts match your search.</div>
            @endforelse
        </div>

        @if ($cohorts->hasPages())
            <div>{{ $cohorts->links() }}</div>
        @endif
    </div>
</div>
