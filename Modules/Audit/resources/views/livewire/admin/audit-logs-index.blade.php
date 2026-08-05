<div class="p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-black">Audit Log</h1>
        <p class="text-sm text-muted-foreground">{{ $logs->total() }} recorded actions — written automatically by event listeners across every module</p>
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1 max-w-sm">
            <x-core::icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search resource type, ID, IP..." class="w-full pl-9 pr-4 py-2 text-sm bg-card border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
        </div>
        <select wire:model.live="action" class="px-3 py-2 text-sm bg-card border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
            <option value="">All actions</option>
            @foreach ($actions as $actionOption)
                <option value="{{ $actionOption }}">{{ $actionOption }}</option>
            @endforeach
        </select>
    </div>

    <div class="bg-card border border-border rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border bg-muted/30">
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Action</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Resource</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground hidden md:table-cell">User</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground hidden lg:table-cell">IP Address</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">When</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr wire:key="log-{{ $log->id }}" class="border-b border-border hover:bg-muted/20 transition-colors cursor-pointer" wire:click="toggleExpand('{{ $log->id }}')">
                        <td class="px-5 py-3">
                            <span class="text-xs font-mono px-2 py-0.5 rounded-full bg-brand-50 text-brand-600">{{ $log->action }}</span>
                        </td>
                        <td class="px-5 py-3 text-xs">
                            <span class="font-medium">{{ $log->resource_type }}</span>
                            @if ($log->resource_id)
                                <span class="text-muted-foreground font-mono block">{{ \Illuminate\Support\Str::limit($log->resource_id, 12) }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 hidden md:table-cell text-xs text-muted-foreground">
                            {{ $log->user_id ? ($userMap[$log->user_id]['name'] ?? 'Unknown user') : 'System' }}
                        </td>
                        <td class="px-5 py-3 hidden lg:table-cell text-xs text-muted-foreground font-mono">{{ $log->ip_address ?? '—' }}</td>
                        <td class="px-5 py-3 text-xs text-muted-foreground">{{ $log->created_at->diffForHumans() }}</td>
                        <td class="px-5 py-3">
                            <x-core::icon :name="$expandedId === $log->id ? 'chevron-up' : 'chevron-down'" class="w-4 h-4 text-muted-foreground" />
                        </td>
                    </tr>
                    @if ($expandedId === $log->id)
                        <tr class="border-b border-border bg-muted/10">
                            <td colspan="6" class="px-5 py-4">
                                <div class="grid sm:grid-cols-2 gap-4 text-xs">
                                    <div>
                                        <p class="font-semibold mb-1 text-muted-foreground">Old Values</p>
                                        <pre class="bg-background border border-border rounded-lg p-3 overflow-x-auto">{{ $log->old_values ? json_encode($log->old_values, JSON_PRETTY_PRINT) : '—' }}</pre>
                                    </div>
                                    <div>
                                        <p class="font-semibold mb-1 text-muted-foreground">New Values</p>
                                        <pre class="bg-background border border-border rounded-lg p-3 overflow-x-auto">{{ $log->new_values ? json_encode($log->new_values, JSON_PRETTY_PRINT) : '—' }}</pre>
                                    </div>
                                </div>
                                @if ($log->user_agent)
                                    <p class="text-xs text-muted-foreground mt-3"><span class="font-semibold">User agent:</span> {{ $log->user_agent }}</p>
                                @endif
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-muted-foreground">No audit entries yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $logs->links() }}</div>
</div>
