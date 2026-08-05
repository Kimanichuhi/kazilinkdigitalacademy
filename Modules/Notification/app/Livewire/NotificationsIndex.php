<?php

namespace Modules\Notification\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\Notification\Models\Notification;

#[Layout('core::components.layouts.public', ['title' => 'Notifications'])]
class NotificationsIndex extends Component
{
    public function markRead(string $id): void
    {
        $notification = Notification::where('id', $id)->where('user_id', Auth::id())->first();
        $notification?->update(['is_read' => true]);
    }

    public function markAllRead(): void
    {
        Notification::where('user_id', Auth::id())->where('is_read', false)->update(['is_read' => true]);
    }

    public function render()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('notification::livewire.notifications-index', [
            'notifications' => $notifications,
            'unreadCount' => $notifications->where('is_read', false)->count(),
        ]);
    }
}
