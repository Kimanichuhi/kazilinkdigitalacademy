@php
    $statusColors = [
        'draft' => 'bg-gray-100 text-gray-600',
        'awaiting_payment' => 'bg-yellow-100 text-yellow-700',
        'paid' => 'bg-blue-100 text-blue-700',
        'pending_approval' => 'bg-orange-100 text-orange-700',
        'approved' => 'bg-green-100 text-green-700',
        'rejected' => 'bg-red-100 text-red-700',
        'cancelled' => 'bg-gray-100 text-gray-500',
        'completed' => 'bg-emerald-100 text-emerald-700',
    ];
@endphp
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black">Bookings</h1>
            <p class="text-sm text-muted-foreground">{{ $bookings->total() }} total bookings</p>
        </div>
        <button class="inline-flex items-center gap-2 border border-border rounded-xl px-3 py-2 text-sm font-medium hover:bg-muted transition-colors">
            <x-core::icon name="download" class="w-4 h-4" /> Export
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-card border border-border rounded-2xl p-4 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <x-core::icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by name, email, or booking number..."
                class="w-full pl-9 pr-4 py-2.5 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500"
            >
        </div>
        <select wire:model.live="status" class="px-3 py-2.5 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
            <option value="">All Statuses</option>
            @foreach ($statuses as $s)
                <option value="{{ $s->value }}">{{ $s->label() }}</option>
            @endforeach
        </select>
    </div>

    <div class="bg-card border border-border rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border text-left text-xs text-muted-foreground uppercase tracking-wider">
                        <th class="px-5 py-3 font-medium">Booking #</th>
                        <th class="px-5 py-3 font-medium">Student</th>
                        <th class="px-5 py-3 font-medium hidden md:table-cell">Phone</th>
                        <th class="px-5 py-3 font-medium hidden lg:table-cell">Location</th>
                        <th class="px-5 py-3 font-medium hidden lg:table-cell">Amount</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium hidden xl:table-cell">Date</th>
                        <th class="px-5 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr wire:key="booking-{{ $booking->id }}" class="border-b border-border hover:bg-muted/20 transition-colors">
                            <td class="px-5 py-3 font-mono text-xs text-brand-600 whitespace-nowrap">{{ $booking->booking_number }}</td>
                            <td class="px-5 py-3">
                                <p class="font-medium whitespace-nowrap">{{ $booking->full_name }}</p>
                                <p class="text-xs text-muted-foreground">{{ $booking->email }}</p>
                            </td>
                            <td class="px-5 py-3 hidden md:table-cell text-muted-foreground">{{ $booking->phone }}</td>
                            <td class="px-5 py-3 hidden lg:table-cell text-xs text-muted-foreground whitespace-nowrap">{{ $booking->county ?: '—' }}{{ $booking->constituency ? ' / '.$booking->constituency : '' }}</td>
                            <td class="px-5 py-3 hidden lg:table-cell">
                                <p class="font-medium">{{ $booking->currency }} {{ $booking->total_amount ? number_format($booking->total_amount) : '—' }}</p>
                                <p class="text-xs text-muted-foreground capitalize">{{ $booking->payment_method ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full whitespace-nowrap capitalize {{ $statusColors[$booking->status->value] ?? '' }}">
                                    {{ str_replace('_', ' ', $booking->status->value) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 hidden xl:table-cell text-xs text-muted-foreground whitespace-nowrap">{{ $booking->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-3">
                                <div class="flex gap-1">
                                    @if ($canUpdateStatus && $booking->status->value === 'awaiting_payment' && $booking->payment_reference)
                                        <button wire:click="confirmPayment('{{ $booking->id }}')" wire:confirm="Confirm this payment reference and mark the booking as paid?" class="p-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors" title="Confirm Payment">
                                            <x-core::icon name="check-circle" class="w-3.5 h-3.5" />
                                        </button>
                                    @endif
                                    @if ($canUpdateStatus && $booking->status->value === 'pending_approval')
                                        <button wire:click="updateStatus('{{ $booking->id }}', 'approved')" class="p-1.5 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg transition-colors" title="Approve">
                                            <x-core::icon name="check-circle" class="w-3.5 h-3.5" />
                                        </button>
                                        <button wire:click="updateStatus('{{ $booking->id }}', 'rejected')" class="p-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors" title="Reject">
                                            <x-core::icon name="x" class="w-3.5 h-3.5" />
                                        </button>
                                    @endif
                                    @if ($canUpdateStatus && $booking->status->value === 'paid')
                                        <button wire:click="updateStatus('{{ $booking->id }}', 'pending_approval')" class="p-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-lg transition-colors" title="Move to Pending Approval">
                                            <x-core::icon name="clock" class="w-3.5 h-3.5" />
                                        </button>
                                    @endif
                                    <button wire:click="view('{{ $booking->id }}')" class="p-1.5 bg-brand-100 hover:bg-brand-200 text-brand-700 rounded-lg transition-colors" title="View Details">
                                        <x-core::icon name="eye" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-12 text-center text-muted-foreground">No bookings found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bookings->hasPages())
            <div class="px-5 py-3 border-t border-border">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>

    @if ($viewingBooking)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="closeView">
            <div class="bg-background border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border">
                    <h2 class="font-bold text-lg">Booking Details</h2>
                    <button wire:click="closeView" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-muted-foreground">Booking #</span><span class="font-mono">{{ $viewingBooking->booking_number }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Name</span><span class="font-medium">{{ $viewingBooking->full_name }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Email</span><span data-clarity-mask="true">{{ $viewingBooking->email }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Phone</span><span data-clarity-mask="true">{{ $viewingBooking->phone }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">County</span><span>{{ $viewingBooking->county ?: '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Constituency</span><span>{{ $viewingBooking->constituency ?: '—' }}</span></div>
                    <div class="pt-3 border-t border-border flex justify-between"><span class="text-muted-foreground">Payment Method</span><span class="capitalize">{{ $viewingBooking->payment_method ?: '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Payment Status</span><span class="capitalize">{{ $viewingBooking->payment_status->value }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Payment Reference</span><span class="font-mono" data-clarity-mask="true">{{ $viewingBooking->payment_reference ?: '—' }}</span></div>

                    <div class="pt-3 border-t border-border flex items-center justify-between">
                        <span class="text-muted-foreground">National ID</span>
                        <span class="font-mono" data-clarity-mask="true">
                            {{ $revealedIdNumber !== null && $viewingId === $viewingBooking->id ? $revealedIdNumber : ($viewingBooking->maskedIdNumber() ?? '—') }}
                        </span>
                    </div>
                    @if ($canRevealId && $viewingBooking->id_number && $revealedIdNumber === null)
                        <div class="flex justify-end">
                            <button wire:click="revealId('{{ $viewingBooking->id }}')" wire:confirm="Revealing this ID number will be logged in the audit trail. Continue?" class="text-xs font-medium text-brand-600 hover:underline">
                                Reveal ID
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
