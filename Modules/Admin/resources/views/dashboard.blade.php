<x-admin::layouts.admin title="Dashboard">
    <div class="p-6 space-y-6">
        <div>
            <h1 class="text-2xl font-black">Admin Dashboard</h1>
            <p class="text-sm text-muted-foreground mt-1">Signed in as {{ $user->name }} ({{ $user->getRoleNames()->implode(', ') }})</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($stats as $stat)
                <div class="bg-card border border-border rounded-2xl p-5">
                    <p class="text-3xl font-black text-brand-600">{{ $stat['value'] }}</p>
                    <p class="text-sm text-muted-foreground mt-0.5">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>

        @if ($topResources->isNotEmpty())
            <div class="bg-card border border-border rounded-2xl p-5">
                <h2 class="font-bold mb-3">Most Downloaded Resources</h2>
                <div class="divide-y divide-border">
                    @foreach ($topResources as $resource)
                        <div class="flex items-center justify-between py-2 text-sm">
                            <span class="font-medium">{{ $resource->title }}</span>
                            <span class="text-muted-foreground">{{ number_format($resource->download_count) }} downloads</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="bg-card border border-border rounded-2xl p-5 flex items-center justify-between">
            <p class="text-sm text-muted-foreground">
                This dashboard shows live counts pulled through each module's Contract.
            </p>
            <div class="flex items-center gap-4 flex-shrink-0">
                <a href="{{ url('/admin/reports') }}" class="text-sm font-medium text-brand-600 hover:underline flex items-center gap-1">
                    View Reports <x-core::icon name="arrow-right" class="w-3.5 h-3.5" />
                </a>
                <a href="{{ url('/admin/analytics') }}" class="text-sm font-medium text-brand-600 hover:underline flex items-center gap-1">
                    View Analytics <x-core::icon name="arrow-right" class="w-3.5 h-3.5" />
                </a>
            </div>
        </div>
    </div>
</x-admin::layouts.admin>
