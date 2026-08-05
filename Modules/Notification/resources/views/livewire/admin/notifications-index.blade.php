@php
    $typeColors = [
        'info' => 'bg-blue-100 text-blue-700',
        'success' => 'bg-green-100 text-green-700',
        'warning' => 'bg-amber-100 text-amber-700',
        'error' => 'bg-red-100 text-red-700',
    ];
@endphp
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black">Notifications</h1>
            <p class="text-sm text-muted-foreground">{{ $notifications->total() }} sent</p>
        </div>
        <button wire:click="openCreate" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">
            <x-core::icon name="plus" class="w-4 h-4" /> New Notification
        </button>
    </div>

    <div class="bg-card border border-border rounded-2xl p-4">
        <div class="relative max-w-sm">
            <x-core::icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search title or message..."
                class="w-full pl-9 pr-4 py-2.5 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
            >
        </div>
    </div>

    <div class="bg-card border border-border rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border bg-muted/30">
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Title</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Type</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground hidden md:table-cell">Recipient</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Sent</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notifications as $notification)
                    <tr wire:key="notification-{{ $notification->id }}" class="border-b border-border hover:bg-muted/20 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium">{{ $notification->title }}</p>
                            <p class="text-xs text-muted-foreground line-clamp-1 mt-0.5">{{ $notification->message }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full capitalize {{ $typeColors[$notification->type] ?? 'bg-gray-100 text-gray-600' }}">{{ $notification->type }}</span>
                        </td>
                        <td class="px-5 py-3 hidden md:table-cell text-xs text-muted-foreground">
                            {{ $notification->user_id ? ($userMap[$notification->user_id] ?? 'Unknown user') : '—' }}
                        </td>
                        <td class="px-5 py-3 text-xs text-muted-foreground">{{ $notification->created_at?->diffForHumans() }}</td>
                        <td class="px-5 py-3">
                            <button wire:click="delete('{{ $notification->id }}')" wire:confirm="Delete this notification?" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors text-muted-foreground hover:text-red-600" title="Delete">
                                <x-core::icon name="trash" class="w-3.5 h-3.5" />
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-muted-foreground">No notifications yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $notifications->links() }}</div>

    @if ($showForm)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
            <div class="bg-background border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border">
                    <h2 class="font-bold text-lg">New Notification</h2>
                    <button wire:click="$set('showForm', false)" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="text-xs font-medium mb-1 block">Title *</label>
                        <input wire:model="formData.title" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        @error('formData.title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium mb-1 block">Message *</label>
                        <textarea wire:model="formData.message" rows="4" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                        @error('formData.message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">Type</label>
                            <select wire:model="formData.type" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                @foreach ($types as $type)
                                    <option value="{{ $type->value }}" class="capitalize">{{ ucfirst($type->value) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Link (optional)</label>
                            <input wire:model="formData.link" placeholder="https://..." class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @error('formData.link') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium mb-1.5 block">Send to</label>
                        <div class="flex items-center gap-4 mb-3">
                            <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                                <input type="radio" wire:model.live="formData.audience" value="user"> Specific user
                            </label>
                            <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                                <input type="radio" wire:model.live="formData.audience" value="role"> By role
                            </label>
                            <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                                <input type="radio" wire:model.live="formData.audience" value="all"> All users
                            </label>
                        </div>

                        @if ($formData['audience'] === 'user')
                            <select wire:model="formData.user_id" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="">Select a user...</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name ?: $user->email }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            @error('formData.user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @elseif ($formData['audience'] === 'role')
                            <select wire:model="formData.role" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="">Select a role...</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->value }}">{{ $role->label() }}</option>
                                @endforeach
                            </select>
                            @error('formData.role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @else
                            <p class="text-xs text-muted-foreground">Every registered user will receive this notification.</p>
                        @endif
                        @error('formData.audience') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-border">
                        <button wire:click="$set('showForm', false)" class="border border-border rounded-xl px-4 py-2 text-sm font-medium hover:bg-muted transition-colors">Cancel</button>
                        <button wire:click="save" class="bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">Send</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
