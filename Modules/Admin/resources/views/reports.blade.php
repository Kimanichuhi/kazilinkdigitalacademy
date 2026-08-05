<x-admin::layouts.admin title="Reports">
    <div class="p-6 space-y-6">
        <div>
            <h1 class="text-2xl font-black">Reports</h1>
            <p class="text-sm text-muted-foreground mt-1">Enrollment and revenue breakdowns.</p>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div class="bg-card border border-border rounded-2xl p-5">
                <p class="text-3xl font-black text-brand-600">KES {{ number_format($bookingRevenue) }}</p>
                <p class="text-sm text-muted-foreground mt-0.5">Booking Revenue</p>
            </div>
            <div class="bg-card border border-border rounded-2xl p-5">
                <p class="text-3xl font-black text-brand-600">KES {{ number_format($resourceRevenue) }}</p>
                <p class="text-sm text-muted-foreground mt-0.5">Resource Sales Revenue</p>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            <div class="bg-card border border-border rounded-2xl p-5">
                <h2 class="font-bold mb-3">Enrollment by County</h2>
                <div class="divide-y divide-border max-h-96 overflow-y-auto">
                    @forelse ($byCounty as $row)
                        <div class="flex items-center justify-between py-2 text-sm">
                            <span>{{ $row->county }}</span>
                            <span class="font-medium">{{ $row->total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-muted-foreground py-4">No data yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-card border border-border rounded-2xl p-5">
                <h2 class="font-bold mb-3">Enrollment by Constituency</h2>
                <div class="divide-y divide-border max-h-96 overflow-y-auto">
                    @forelse ($byConstituency as $row)
                        <div class="flex items-center justify-between py-2 text-sm">
                            <span>{{ $row->constituency }}</span>
                            <span class="font-medium">{{ $row->total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-muted-foreground py-4">No data yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-card border border-border rounded-2xl p-5">
                <h2 class="font-bold mb-3">Enrollment by Program</h2>
                <div class="divide-y divide-border max-h-96 overflow-y-auto">
                    @forelse ($byProgram as $row)
                        <div class="flex items-center justify-between py-2 text-sm">
                            <span>{{ $row->title }}</span>
                            <span class="font-medium">{{ $row->total }} &middot; KES {{ number_format((float) $row->revenue) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-muted-foreground py-4">No data yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-card border border-border rounded-2xl p-5">
                <h2 class="font-bold mb-3">Resource Sales</h2>
                <div class="divide-y divide-border max-h-96 overflow-y-auto">
                    @forelse ($resourceSales as $row)
                        <div class="flex items-center justify-between py-2 text-sm">
                            <span>{{ $row->title }}</span>
                            <span class="font-medium">{{ $row->total }} &middot; KES {{ number_format((float) $row->revenue) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-muted-foreground py-4">No data yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin::layouts.admin>
