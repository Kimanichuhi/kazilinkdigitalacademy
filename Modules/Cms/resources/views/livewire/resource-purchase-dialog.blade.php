<div>
    @if ($hasAccess)
        <a href="{{ url('/resources/'.$resource->id.'/download') }}" class="inline-flex items-center gap-1.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors">
            <x-core::icon name="download" class="w-3.5 h-3.5" /> Download
        </a>
    @else
        <button type="button" wire:click="openDialog" class="inline-flex items-center gap-1.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors">
            <x-core::icon name="shield" class="w-3.5 h-3.5" /> Buy &middot; {{ $resource->currency }} {{ number_format($resource->price, 0) }}
        </button>
    @endif

    @if ($open)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="closeDialog">
            <div class="bg-background border border-border rounded-2xl w-full max-w-md shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border">
                    <h2 class="font-bold text-lg">Buy Resource</h2>
                    <button wire:click="closeDialog" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="font-semibold text-sm">{{ $resource->title }}</p>
                        <p class="text-2xl font-black mt-1">{{ $resource->currency }} {{ number_format($resource->price, 0) }}</p>
                    </div>

                    @auth
                        @if ($mpesaCheckoutRequestId)
                            <livewire:payment::mpesa-status-poller :checkout-request-id="$mpesaCheckoutRequestId" :key="'purchase-poll-'.$mpesaCheckoutRequestId" />
                        @else
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">Safaricom Phone Number</label>
                                <input wire:model="mpesaPhone" placeholder="07XXXXXXXX" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                                @error('mpesaPhone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            @if ($mpesaError)
                                <p class="text-red-500 text-xs flex items-center gap-1"><x-core::icon name="x" class="w-3.5 h-3.5" />{{ $mpesaError }}</p>
                            @endif

                            <button
                                type="button"
                                wire:click="payNow"
                                wire:loading.attr="disabled"
                                wire:target="payNow"
                                class="w-full inline-flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="payNow">Pay Now</span>
                                <span wire:loading wire:target="payNow" class="inline-flex items-center gap-2">
                                    <span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                    Waiting for STK Push...
                                </span>
                            </button>
                        @endif
                    @else
                        <p class="text-sm text-muted-foreground">Please <a href="{{ route('login') }}" class="text-brand-600 hover:underline">log in</a> to purchase this resource.</p>
                    @endauth
                </div>
            </div>
        </div>
    @endif
</div>
