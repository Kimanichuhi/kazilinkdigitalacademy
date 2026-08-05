<div class="p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-black">Contact Messages</h1>
        <p class="text-sm text-muted-foreground">{{ $submissions->total() }} submissions from the /contact form</p>
    </div>

    <div class="space-y-3">
        @forelse ($submissions as $submission)
            <div wire:key="submission-{{ $submission->id }}" class="bg-card border border-border rounded-2xl p-4 flex items-start justify-between gap-4">
                <button wire:click="view('{{ $submission->id }}')" class="min-w-0 text-left flex-1">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <p class="font-semibold text-sm">{{ $submission->full_name }}</p>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full capitalize
                            {{ $submission->status === 'new' ? 'bg-brand-50 text-brand-600' : ($submission->status === 'resolved' ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500') }}">
                            {{ $submission->status }}
                        </span>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 capitalize">{{ $submission->subject }}</span>
                    </div>
                    <p class="text-xs text-muted-foreground">{{ $submission->email }}@if($submission->phone) &middot; {{ $submission->phone }}@endif</p>
                    <p class="text-xs text-muted-foreground line-clamp-1 mt-0.5">{{ $submission->message }}</p>
                </button>
                <div class="flex gap-1.5 flex-shrink-0 items-center">
                    <span class="text-xs text-muted-foreground">{{ $submission->created_at->diffForHumans() }}</span>
                    <button wire:click="delete('{{ $submission->id }}')" wire:confirm="Delete this message?" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors text-muted-foreground hover:text-red-600" title="Delete">
                        <x-core::icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-muted-foreground">No messages yet</div>
        @endforelse
    </div>

    @if ($submissions->hasPages())
        <div>{{ $submissions->links() }}</div>
    @endif

    @if ($viewing)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="close">
            <div class="bg-background border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border">
                    <h2 class="font-bold text-lg">{{ $viewing->full_name }}</h2>
                    <button wire:click="close" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-4">
                    <div class="text-sm space-y-1">
                        <p><span class="text-muted-foreground">Email:</span> {{ $viewing->email }}</p>
                        @if ($viewing->phone)
                            <p><span class="text-muted-foreground">Phone:</span> {{ $viewing->phone }}</p>
                        @endif
                        <p><span class="text-muted-foreground">Subject:</span> <span class="capitalize">{{ $viewing->subject }}</span></p>
                        <p><span class="text-muted-foreground">Received:</span> {{ $viewing->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <div class="bg-muted rounded-xl p-4 text-sm whitespace-pre-line">{{ $viewing->message }}</div>

                    <div>
                        <label class="text-xs font-medium mb-1 block">Status</label>
                        <div class="flex gap-2">
                            @foreach (['new', 'read', 'resolved'] as $status)
                                <button
                                    wire:click="updateStatus('{{ $viewing->id }}', '{{ $status }}')"
                                    class="text-xs font-medium px-3 py-1.5 rounded-full capitalize transition-colors {{ $viewing->status === $status ? 'bg-brand-500 text-white' : 'bg-muted text-muted-foreground hover:bg-border' }}"
                                >{{ $status }}</button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium mb-1 block">Internal Notes</label>
                        <textarea wire:model="notes" rows="3" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-border">
                        <button wire:click="close" class="border border-border rounded-xl px-4 py-2 text-sm font-medium hover:bg-muted transition-colors">Close</button>
                        <button wire:click="saveNotes" class="bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">Save Notes</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
