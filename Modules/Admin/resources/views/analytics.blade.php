<x-admin::layouts.admin title="Analytics">
    @vite('resources/js/admin-charts.js')
    <div
        class="p-6 space-y-6"
        x-data="{
            monthly: @js($monthly),
            programPopularity: @js($programPopularity),
            bookingsChart: null,
            revenueChart: null,
            pieChart: null,
            init() {
                this.bookingsChart = new ApexCharts(this.$refs.bookingsChart, {
                    chart: { type: 'area', height: 260, toolbar: { show: false } },
                    series: [{ name: 'Bookings', data: this.monthly.map(m => m.bookings) }],
                    xaxis: { categories: this.monthly.map(m => m.month) },
                    colors: ['#4CAF50'],
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2 },
                });
                this.bookingsChart.render();

                this.revenueChart = new ApexCharts(this.$refs.revenueChart, {
                    chart: { type: 'bar', height: 260, toolbar: { show: false } },
                    series: [{ name: 'Revenue', data: this.monthly.map(m => m.revenue) }],
                    xaxis: { categories: this.monthly.map(m => m.month) },
                    yaxis: { labels: { formatter: (v) => (v / 1000) + 'K' } },
                    colors: ['#10b981'],
                    plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                    dataLabels: { enabled: false },
                });
                this.revenueChart.render();

                this.pieChart = new ApexCharts(this.$refs.pieChart, {
                    chart: { type: 'donut', height: 260 },
                    series: this.programPopularity.map(p => p.value),
                    labels: this.programPopularity.map(p => p.name),
                    colors: this.programPopularity.map(p => p.color),
                    plotOptions: { pie: { donut: { size: '55%' } } },
                    legend: { position: 'bottom' },
                    dataLabels: { enabled: false },
                });
                this.pieChart.render();
            },
        }"
    >
        <div>
            <h1 class="text-2xl font-black">Analytics</h1>
            <p class="text-sm text-muted-foreground mt-1">Booking and revenue overview</p>
        </div>

        <div class="grid sm:grid-cols-3 gap-4">
            <div class="bg-card border border-border rounded-2xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                    <x-core::icon name="users" class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-2xl font-black">{{ number_format($totalBookings) }}</p>
                    <p class="text-xs text-muted-foreground">Total Bookings</p>
                </div>
            </div>
            <div class="bg-card border border-border rounded-2xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center flex-shrink-0">
                    <x-core::icon name="dollar-sign" class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-2xl font-black">KES {{ number_format($totalRevenue / 1000, 0) }}K</p>
                    <p class="text-xs text-muted-foreground">Total Revenue</p>
                </div>
            </div>
            <div class="bg-card border border-border rounded-2xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0">
                    <x-core::icon name="trending-up" class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-2xl font-black">KES {{ number_format($avgValue) }}</p>
                    <p class="text-xs text-muted-foreground">Avg Booking Value</p>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            <div class="bg-card border border-border rounded-2xl p-5">
                <h2 class="font-bold mb-4">Monthly Bookings</h2>
                <div x-ref="bookingsChart"></div>
            </div>
            <div class="bg-card border border-border rounded-2xl p-5">
                <h2 class="font-bold mb-4">Revenue Trend (KES)</h2>
                <div x-ref="revenueChart"></div>
            </div>
            <div class="bg-card border border-border rounded-2xl p-5">
                <h2 class="font-bold mb-4">Program Popularity</h2>
                <div x-ref="pieChart"></div>
            </div>
            <div class="bg-card border border-border rounded-2xl p-5">
                <h2 class="font-bold mb-4">Key Metrics</h2>
                <div class="space-y-4">
                    @foreach ($keyMetrics as $metric)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium">{{ $metric['label'] }}</p>
                                <p class="text-xs text-muted-foreground">{{ $metric['description'] }}</p>
                            </div>
                            <p class="text-xl font-black text-brand-600">{{ $metric['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-admin::layouts.admin>
