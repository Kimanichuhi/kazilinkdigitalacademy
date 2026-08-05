@php
    $roleColors = [
        'student' => 'bg-blue-100 text-blue-700', 'trainer' => 'bg-green-100 text-green-700',
        'admin' => 'bg-orange-100 text-orange-700', 'super_admin' => 'bg-red-100 text-red-700',
        'content_manager' => 'bg-purple-100 text-purple-700', 'marketing' => 'bg-pink-100 text-pink-700',
        'finance' => 'bg-yellow-100 text-yellow-700', 'support' => 'bg-teal-100 text-teal-700',
    ];
@endphp
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black">Students</h1>
            <p class="text-sm text-muted-foreground">{{ $students->total() }} registered students</p>
        </div>
    </div>

    <div class="relative max-w-sm">
        <x-core::icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search students..." class="w-full pl-9 pr-4 py-2 text-sm bg-card border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
    </div>

    <div class="bg-card border border-border rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border bg-muted/30">
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Student</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground hidden md:table-cell">Phone</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Role</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground hidden lg:table-cell">Joined</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $student)
                    <tr wire:key="student-{{ $student->id }}" class="border-b border-border hover:bg-muted/20 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($student->name ?: $student->email, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium">{{ $student->name ?: 'No name' }}</p>
                                    <p class="text-xs text-muted-foreground" data-clarity-mask="true">{{ $student->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 hidden md:table-cell text-muted-foreground text-xs" data-clarity-mask="true">{{ $student->phone ?: '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full capitalize {{ $roleColors['student'] }}">student</span>
                        </td>
                        <td class="px-5 py-3 hidden lg:table-cell text-xs text-muted-foreground">{{ $student->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ $student->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-muted-foreground">No students found</td></tr>
                @endforelse
            </tbody>
        </table>

        @if ($students->hasPages())
            <div class="px-5 py-3 border-t border-border">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
