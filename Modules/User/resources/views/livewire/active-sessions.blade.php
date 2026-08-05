<div class="max-w-2xl mx-auto px-4 sm:px-6 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black">Active Sessions</h1>
            <p class="text-sm text-muted-foreground mt-1">Everywhere you're currently signed in.</p>
        </div>
        @if ($sessions->filter(fn ($s) => ! $s->is_current_device)->isNotEmpty())
            <button wire:click="revokeOthers" wire:confirm="Sign out of every other session? You'll stay signed in here." class="text-sm font-medium text-red-600 hover:underline flex-shrink-0">
                Sign out other devices
            </button>
        @endif
    </div>

    <div class="space-y-3">
        @foreach ($sessions as $session)
            <div wire:key="session-{{ $session->id }}" class="bg-card border border-border rounded-2xl p-4 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="font-semibold text-sm">{{ $session->device }}</p>
                        @if ($session->is_current_device)
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-green-100 text-green-700">This device</span>
                        @endif
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">{{ $session->ip_address ?? 'Unknown IP' }} &middot; Active {{ $session->last_active }}</p>
                </div>
                @unless ($session->is_current_device)
                    <button wire:click="revoke('{{ $session->id }}')" wire:confirm="Sign out this session?" class="text-xs font-medium text-red-600 hover:underline flex-shrink-0">
                        Revoke
                    </button>
                @endunless
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        <a href="{{ route('student.profile') }}" class="inline-flex items-center gap-2 text-sm font-medium text-brand-600 hover:text-brand-700">
            <x-core::icon name="chevron-left" class="w-4 h-4" /> Back to Profile
        </a>
    </div>
</div>
