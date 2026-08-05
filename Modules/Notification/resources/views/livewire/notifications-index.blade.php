@php
    $typeConfig = [
        'info' => ['icon' => 'bell', 'color' => 'bg-brand-50 text-brand-600'],
        'success' => ['icon' => 'check-circle', 'color' => 'bg-green-50 text-green-600'],
        'warning' => ['icon' => 'x', 'color' => 'bg-yellow-50 text-yellow-600'],
        'error' => ['icon' => 'x', 'color' => 'bg-red-50 text-red-600'],
    ];
@endphp
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black">Notifications</h1>
            <p class="text-muted-foreground mt-1">
                {{ $unreadCount > 0 ? $unreadCount.' unread' : 'You\'re all caught up' }}
            </p>
        </div>
        @if ($unreadCount > 0)
            <button wire:click="markAllRead" class="text-sm font-medium text-brand-600 hover:underline flex-shrink-0">
                Mark all as read
            </button>
        @endif
    </div>

    <div class="space-y-3">
        @forelse ($notifications as $notification)
            @php $type = $typeConfig[$notification->type] ?? $typeConfig['info']; @endphp
            <div wire:key="notification-{{ $notification->id }}" class="bg-card border border-border rounded-2xl p-5 flex items-start gap-4 {{ ! $notification->is_read ? 'border-brand-200 bg-brand-50/30' : '' }}">
                <div class="w-10 h-10 rounded-xl {{ $type['color'] }} flex items-center justify-center flex-shrink-0">
                    <x-core::icon :name="$type['icon']" class="w-5 h-5" />
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-semibold text-sm">{{ $notification->title }}</p>
                        @if (! $notification->is_read)
                            <span class="w-2 h-2 rounded-full bg-brand-500 flex-shrink-0 mt-1.5"></span>
                        @endif
                    </div>
                    <p class="text-sm text-muted-foreground mt-0.5">{{ $notification->message }}</p>
                    <div class="flex items-center gap-3 mt-3">
                        <span class="text-xs text-muted-foreground">{{ $notification->created_at->diffForHumans() }}</span>
                        @if ($notification->link)
                            <a href="{{ url($notification->link) }}" wire:click="markRead('{{ $notification->id }}')" class="text-xs font-medium text-brand-600 hover:underline">
                                View
                            </a>
                        @endif
                        @if (! $notification->is_read)
                            <button wire:click="markRead('{{ $notification->id }}')" class="text-xs font-medium text-muted-foreground hover:text-foreground">
                                Mark as read
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-card border border-border rounded-2xl p-12 text-center">
                <x-core::icon name="bell" class="w-12 h-12 text-muted-foreground/30 mx-auto mb-4" />
                <h3 class="font-semibold text-muted-foreground mb-1">No notifications yet</h3>
                <p class="text-sm text-muted-foreground">We'll let you know when something needs your attention.</p>
            </div>
        @endforelse
    </div>
</div>
