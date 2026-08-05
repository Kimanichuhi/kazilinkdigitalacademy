<div wire:poll.3s="poll" class="bg-card border border-border rounded-xl p-5 text-center space-y-3">
    @if ($status->value === 'pending' && $justSent)
        <div class="w-10 h-10 border-2 border-brand-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
        <p class="font-semibold text-sm">Payment Request Sent</p>
        <p class="text-xs text-muted-foreground">Check your phone for the M-Pesa PIN prompt.</p>
    @elseif ($status->value === 'pending')
        <div class="w-10 h-10 border-2 border-brand-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
        <p class="font-semibold text-sm">Waiting for Confirmation</p>
        <p class="text-xs text-muted-foreground">Enter your M-Pesa PIN on your phone to complete payment.</p>
    @elseif ($status->value === 'success')
        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mx-auto">
            <x-core::icon name="check-circle" class="w-6 h-6 text-green-600" />
        </div>
        <p class="font-semibold text-sm text-green-700">Payment Successful</p>
    @elseif ($status->value === 'cancelled')
        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
            <x-core::icon name="x" class="w-6 h-6 text-gray-500" />
        </div>
        <p class="font-semibold text-sm text-gray-600">Cancelled by User</p>
    @else
        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mx-auto">
            <x-core::icon name="x" class="w-6 h-6 text-red-600" />
        </div>
        <p class="font-semibold text-sm text-red-600">Payment Failed</p>
    @endif
</div>
