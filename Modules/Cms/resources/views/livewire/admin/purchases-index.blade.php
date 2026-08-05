@php
    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-700',
        'paid' => 'bg-green-100 text-green-700',
        'failed' => 'bg-red-100 text-red-700',
    ];
@endphp
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black">Purchases</h1>
            <p class="text-sm text-muted-foreground">{{ $purchases->total() }} purchases &middot; KES {{ number_format($totalRevenue, 2) }} revenue</p>
        </div>
        <select wire:model.live="status" class="px-3 py-2.5 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
            <option value="failed">Failed</option>
        </select>
    </div>

    <div class="bg-card border border-border rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border text-left text-xs text-muted-foreground uppercase tracking-wider">
                        <th class="px-5 py-3 font-medium">Resource</th>
                        <th class="px-5 py-3 font-medium">Buyer</th>
                        <th class="px-5 py-3 font-medium">Amount</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Purchased</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr wire:key="purchase-{{ $purchase->id }}" class="border-b border-border hover:bg-muted/20 transition-colors">
                            <td class="px-5 py-3 font-medium">{{ $purchase->resource->title ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <p class="font-medium">{{ $purchase->user->name ?? '—' }}</p>
                                <p class="text-xs text-muted-foreground">{{ $purchase->user->email ?? '' }}</p>
                            </td>
                            <td class="px-5 py-3">{{ $purchase->currency }} {{ number_format($purchase->amount, 2) }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full capitalize {{ $statusColors[$purchase->status] ?? '' }}">{{ $purchase->status }}</span>
                            </td>
                            <td class="px-5 py-3 text-xs text-muted-foreground whitespace-nowrap">{{ $purchase->purchased_at?->format('d M Y H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-muted-foreground">No purchases yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($purchases->hasPages())
            <div class="px-5 py-3 border-t border-border">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>
</div>
