@php
    $statusConfig = [
        'draft' => ['label' => 'Draft', 'color' => 'bg-gray-100 text-gray-600', 'icon' => 'clock'],
        'awaiting_payment' => ['label' => 'Awaiting Payment', 'color' => 'bg-yellow-100 text-yellow-700', 'icon' => 'clock'],
        'paid' => ['label' => 'Payment Received', 'color' => 'bg-blue-100 text-blue-700', 'icon' => 'check-circle'],
        'pending_approval' => ['label' => 'Pending Approval', 'color' => 'bg-orange-100 text-orange-700', 'icon' => 'clock'],
        'approved' => ['label' => 'Approved', 'color' => 'bg-green-100 text-green-700', 'icon' => 'check-circle'],
        'rejected' => ['label' => 'Rejected', 'color' => 'bg-red-100 text-red-700', 'icon' => 'x'],
        'cancelled' => ['label' => 'Cancelled', 'color' => 'bg-gray-100 text-gray-500', 'icon' => 'x'],
        'completed' => ['label' => 'Completed', 'color' => 'bg-emerald-100 text-emerald-700', 'icon' => 'award'],
    ];
@endphp
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-black">My Dashboard</h1>
        <p class="text-muted-foreground mt-1">Welcome back, {{ $user->name }}</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @foreach ([
            ['label' => 'Total Bookings', 'value' => $bookings->count(), 'color' => 'text-brand-600'],
            ['label' => 'Active Programs', 'value' => $activeCount, 'color' => 'text-green-600'],
            ['label' => 'Completed', 'value' => $completedCount, 'color' => 'text-emerald-600'],
            ['label' => 'Certificates', 'value' => $completedCount, 'color' => 'text-orange-600'],
        ] as $stat)
            <div class="rounded-2xl p-5 border border-border bg-card">
                <p class="text-3xl font-black {{ $stat['color'] }}">{{ $stat['value'] }}</p>
                <p class="text-sm text-muted-foreground mt-0.5">{{ $stat['label'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Bookings -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-bold">My Bookings</h2>
                <a href="{{ url('/booking') }}" class="text-sm text-brand-600 hover:underline flex items-center gap-1">
                    + New Booking <x-core::icon name="arrow-right" class="w-3.5 h-3.5" />
                </a>
            </div>

            @forelse ($bookings as $booking)
                @php $status = $statusConfig[$booking['status']] ?? $statusConfig['draft']; @endphp
                <div class="bg-card border border-border rounded-2xl p-5">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold truncate">{{ $booking['program_title'] }}</h3>
                            <p class="text-xs text-muted-foreground mt-0.5 font-mono">{{ $booking['booking_number'] }}</p>
                        </div>
                        <span class="flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full flex-shrink-0 {{ $status['color'] }}">
                            <x-core::icon :name="$status['icon']" class="w-3.5 h-3.5" /> {{ $status['label'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs text-muted-foreground">
                        <div><span class="font-medium text-foreground">Booked: </span>{{ $booking['created_at']->format('d M Y') }}</div>
                        <div><span class="font-medium text-foreground">Amount: </span>{{ $booking['currency'] }} {{ $booking['total_amount'] ? number_format($booking['total_amount']) : '—' }}</div>
                        <div><span class="font-medium text-foreground">Payment: </span><span class="capitalize">{{ $booking['payment_status'] }}</span></div>
                    </div>

                    @if ($booking['status'] === 'awaiting_payment')
                        <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-xl text-xs text-yellow-800">
                            <x-core::icon name="x" class="w-3.5 h-3.5 inline mr-1" />
                            Payment pending. Please complete your M-Pesa or bank transfer to proceed.
                        </div>
                    @endif

                    @if ($booking['rejection_reason'])
                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-800">
                            <x-core::icon name="x" class="w-3.5 h-3.5 inline mr-1" />
                            {{ $booking['rejection_reason'] }}
                        </div>
                    @endif

                    @if ($booking['status'] === 'completed')
                        <div class="mt-3">
                            <button class="flex items-center gap-2 text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                                <x-core::icon name="award" class="w-3.5 h-3.5" /> Download Certificate
                            </button>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-card border border-border rounded-2xl p-10 text-center">
                    <x-core::icon name="book-open" class="w-12 h-12 text-muted-foreground/30 mx-auto mb-4" />
                    <h3 class="font-semibold text-muted-foreground mb-2">No bookings yet</h3>
                    <p class="text-sm text-muted-foreground mb-4">Explore our programs and book your first training.</p>
                    <a href="{{ url('/programs') }}" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                        Browse Programs <x-core::icon name="arrow-right" class="w-3.5 h-3.5" />
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Sidebar -->
        <div class="space-y-5">
            <!-- Profile card -->
            <div class="bg-card border border-border rounded-2xl p-5">
                <h2 class="font-bold mb-4">My Profile</h2>
                <div class="flex flex-col items-center text-center mb-4">
                    <div class="w-16 h-16 rounded-full bg-brand-500 flex items-center justify-center text-white text-2xl font-bold mb-3">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <p class="font-bold">{{ $user->name }}</p>
                    <p class="text-sm text-muted-foreground">{{ $user->email }}</p>
                    @if ($user->phone)
                        <p class="text-xs text-muted-foreground">{{ $user->phone }}</p>
                    @endif
                </div>
                <a href="{{ url('/student/profile') }}" class="block w-full text-center border border-border hover:border-brand-500 hover:text-brand-600 text-sm font-medium py-2 rounded-xl transition-colors">
                    Edit Profile
                </a>
            </div>

            <!-- Quick links -->
            <div class="bg-card border border-border rounded-2xl p-5">
                <h2 class="font-bold mb-3">Quick Links</h2>
                <div class="space-y-1">
                    @foreach ([
                        ['label' => 'Browse Programs', 'href' => '/programs'],
                        ['label' => 'Book New Training', 'href' => '/booking'],
                        ['label' => 'Download Resources', 'href' => '/resources'],
                        ['label' => 'Read Blog', 'href' => '/blog'],
                        ['label' => 'Contact Support', 'href' => '/contact'],
                    ] as $link)
                        <a href="{{ url($link['href']) }}" class="flex items-center justify-between px-3 py-2 text-sm rounded-lg hover:bg-muted transition-colors text-muted-foreground hover:text-foreground">
                            {{ $link['label'] }}
                            <x-core::icon name="arrow-right" class="w-3.5 h-3.5" />
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
