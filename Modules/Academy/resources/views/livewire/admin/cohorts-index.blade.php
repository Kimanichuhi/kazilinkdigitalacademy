@php
    $statusColors = [
        'upcoming' => 'bg-blue-100 text-blue-700',
        'open' => 'bg-green-100 text-green-700',
        'full' => 'bg-red-100 text-red-700',
        'in_progress' => 'bg-orange-100 text-orange-700',
        'completed' => 'bg-gray-100 text-gray-600',
        'cancelled' => 'bg-gray-100 text-gray-500',
    ];
    $noAvailablePrograms = $programs->count() > 0 && $availablePrograms->count() === 0;
@endphp
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div><h1 class="text-2xl font-black">Cohorts</h1><p class="text-sm text-muted-foreground">{{ $cohorts->total() }} cohorts</p></div>
        <button
            wire:click="openCreate"
            @if ($noAvailablePrograms) disabled title="Publish at least one active program before creating a cohort" @endif
            class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <x-core::icon name="plus" class="w-4 h-4" /> New Cohort
        </button>
    </div>

    @if ($noAvailablePrograms)
        <div class="flex items-start gap-2.5 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-3.5 text-sm">
            <x-core::icon name="help-circle" class="w-4 h-4 flex-shrink-0 mt-0.5" />
            <span>No published, active programs available. Cohorts need a program to attach to — publish one on the <span class="font-medium">Programs</span> page first.</span>
        </div>
    @endif

    <div class="bg-card border border-border rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border bg-muted/30">
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Cohort</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground hidden md:table-cell">Program</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Dates</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Seats</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Status</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cohorts as $cohort)
                    <tr wire:key="cohort-{{ $cohort->id }}" class="border-b border-border hover:bg-muted/20 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium">{{ $cohort->name }}</p>
                            @if ($cohort->schedule_details)
                                <p class="text-xs text-muted-foreground">{{ $cohort->schedule_details }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 hidden md:table-cell text-xs text-muted-foreground">{{ $cohort->program?->title }}</td>
                        <td class="px-5 py-3 text-xs text-muted-foreground">{{ $cohort->start_date->format('d M Y') }}</td>
                        <td class="px-5 py-3"><span class="text-sm">{{ $cohort->booked_seats }}/{{ $cohort->total_seats }}</span></td>
                        <td class="px-5 py-3"><span class="text-xs font-medium px-2 py-0.5 rounded-full capitalize {{ $statusColors[$cohort->status] ?? '' }}">{{ $cohort->status }}</span></td>
                        <td class="px-5 py-3">
                            <div class="flex gap-1.5">
                                <button wire:click="openEdit('{{ $cohort->id }}')" class="p-1.5 hover:bg-muted rounded-lg"><x-core::icon name="edit" class="w-3.5 h-3.5 text-muted-foreground" /></button>
                                <button wire:click="delete('{{ $cohort->id }}')" wire:confirm="Delete cohort?" class="p-1.5 hover:bg-red-50 rounded-lg"><x-core::icon name="trash" class="w-3.5 h-3.5 text-muted-foreground" /></button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-muted-foreground">No cohorts found</td></tr>
                @endforelse
            </tbody>
        </table>

        @if ($cohorts->hasPages())
            <div class="px-5 py-3 border-t border-border">
                {{ $cohorts->links() }}
            </div>
        @endif
    </div>

    @if ($showForm)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
            <div class="bg-background border border-border rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border">
                    <h2 class="font-bold">{{ $editingId ? 'Edit' : 'New' }} Cohort</h2>
                    <button wire:click="$set('showForm', false)" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">Program *</label>
                            <select wire:model="formData.program_id" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="">Select...</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->title }}{{ (! $program->is_published || ! $program->is_active) ? ' (unavailable)' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Trainer</label>
                            <select wire:model="formData.trainer_id" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="">Select...</option>
                                @foreach ($trainers as $trainer)
                                    <option value="{{ $trainer->id }}">{{ $trainer->full_name }}{{ ! $trainer->is_active ? ' (inactive)' : '' }}</option>
                                @endforeach
                            </select>
                            @if ($activeTrainers->isEmpty())
                                <p class="text-xs text-amber-600 mt-1">No active trainers yet — you can leave this unassigned or add one in Trainers.</p>
                            @endif
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Cohort Name *</label>
                            <input wire:model="formData.name" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Status</label>
                            <select wire:model="formData.status" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                @foreach (['upcoming', 'open', 'full', 'in_progress', 'completed', 'cancelled'] as $s)
                                    <option value="{{ $s }}">{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Start Date *</label>
                            <input type="date" wire:model="formData.start_date" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">End Date</label>
                            <input type="date" wire:model="formData.end_date" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Registration Deadline</label>
                            <input type="date" wire:model="formData.registration_deadline" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Total Seats</label>
                            <input type="number" wire:model="formData.total_seats" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Price (override)</label>
                            <input type="number" wire:model="formData.price" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Venue</label>
                            <input wire:model="formData.venue" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Online Meeting Link</label>
                            <input type="url" wire:model="formData.online_link" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Online Platform</label>
                            <input wire:model="formData.online_platform" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium mb-1 block">Schedule Details</label>
                            <input wire:model="formData.schedule_details" placeholder="Mon, Wed, Fri | 7:00 PM – 9:00 PM EAT" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2 border-t border-border">
                        <button wire:click="$set('showForm', false)" class="border border-border rounded-xl px-4 py-2 text-sm font-medium hover:bg-muted transition-colors">Cancel</button>
                        <button wire:click="save" class="bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">{{ $editingId ? 'Update' : 'Create' }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
