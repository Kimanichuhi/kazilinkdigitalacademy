<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black">Programs</h1>
            <p class="text-sm text-muted-foreground">{{ $programs->total() }} {{ $showTrashed ? 'deleted' : '' }} programs</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="$toggle('showTrashed')" class="inline-flex items-center gap-2 border border-border rounded-xl px-4 py-2 text-sm font-medium hover:bg-muted transition-colors">
                <x-core::icon name="trash" class="w-4 h-4" /> {{ $showTrashed ? 'Back to Active' : 'Deleted Programs' }}
            </button>
            @if (! $showTrashed)
                <button wire:click="openCreate" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">
                    <x-core::icon name="plus" class="w-4 h-4" /> Add Program
                </button>
            @endif
        </div>
    </div>

    <div class="relative max-w-sm">
        <x-core::icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search programs..." class="w-full pl-9 pr-4 py-2 text-sm bg-card border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
    </div>

    <div class="bg-card border border-border rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border bg-muted/30">
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Program</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground hidden md:table-cell">Category</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Price</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Status</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($programs as $program)
                    <tr wire:key="program-{{ $program->id }}" class="border-b border-border hover:bg-muted/20 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if ($program->thumbnail_url)
                                    <img src="{{ $program->thumbnail_url }}" alt="" class="w-10 h-8 rounded-lg object-cover">
                                @endif
                                <div>
                                    <p class="font-medium">{{ $program->title }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $program->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 hidden md:table-cell text-muted-foreground text-xs">{{ $program->category?->name ?? '—' }}</td>
                        <td class="px-5 py-3 font-medium">{{ $program->price ? $program->currency.' '.number_format($program->price) : '—' }}</td>
                        <td class="px-5 py-3">
                            <div class="flex gap-1.5">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $program->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $program->is_published ? 'Published' : 'Draft' }}
                                </span>
                                @if ($program->is_featured)
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-orange-100 text-orange-600">Featured</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex gap-1.5">
                                @if ($program->trashed())
                                    <button wire:click="restore('{{ $program->id }}')" wire:confirm="Restore this program?" class="p-1.5 hover:bg-green-50 rounded-lg transition-colors text-muted-foreground hover:text-green-600" title="Restore">
                                        <x-core::icon name="refresh-cw" class="w-3.5 h-3.5" />
                                    </button>
                                    @if ($canForceDelete)
                                        <button wire:click="forceDelete('{{ $program->id }}')" wire:confirm="Permanently delete this program? This cannot be undone." class="p-1.5 hover:bg-red-50 rounded-lg transition-colors text-muted-foreground hover:text-red-600" title="Delete Permanently">
                                            <x-core::icon name="x" class="w-3.5 h-3.5" />
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ url('/programs/'.$program->slug) }}" target="_blank" class="p-1.5 hover:bg-muted rounded-lg transition-colors text-muted-foreground hover:text-foreground" title="View">
                                        <x-core::icon name="eye" class="w-3.5 h-3.5" />
                                    </a>
                                    <button wire:click="openEdit('{{ $program->id }}')" class="p-1.5 hover:bg-muted rounded-lg transition-colors text-muted-foreground hover:text-foreground" title="Edit">
                                        <x-core::icon name="edit" class="w-3.5 h-3.5" />
                                    </button>
                                    <button wire:click="togglePublish('{{ $program->id }}')" class="p-1.5 hover:bg-muted rounded-lg transition-colors text-muted-foreground hover:text-foreground" title="{{ $program->is_published ? 'Unpublish' : 'Publish' }}">
                                        <x-core::icon name="check-circle" class="w-4 h-4 {{ $program->is_published ? 'text-green-600' : '' }}" />
                                    </button>
                                    <button wire:click="delete('{{ $program->id }}')" wire:confirm="Delete this program?" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors text-muted-foreground hover:text-red-600" title="Delete">
                                        <x-core::icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-muted-foreground">No programs found</td></tr>
                @endforelse
            </tbody>
        </table>

        @if ($programs->hasPages())
            <div class="px-5 py-3 border-t border-border">
                {{ $programs->links() }}
            </div>
        @endif
    </div>

    @if ($showForm)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
            <div class="bg-background border border-border rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border">
                    <h2 class="font-bold text-lg">{{ $editingId ? 'Edit Program' : 'New Program' }}</h2>
                    <button wire:click="$set('showForm', false)" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">Title *</label>
                            <input wire:model="formData.title" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @error('formData.title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Slug *</label>
                            <input wire:model="formData.slug" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium mb-1 block">Subtitle</label>
                            <input wire:model="formData.subtitle" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium mb-1 block">Short Description</label>
                            <textarea wire:model="formData.short_description" rows="2" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium mb-1 block">Description</label>
                            <textarea wire:model="formData.description" rows="4" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <livewire:core::image-upload wire:model="formData.thumbnail_url" folder="programs" label="Thumbnail" :key="'thumb-'.($editingId ?? 'new')" />
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Category</label>
                            <select wire:model="formData.category_id" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="">No category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}{{ ! $category->is_active ? ' (inactive)' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Level</label>
                            <select wire:model="formData.level" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                                <option value="all_levels">All Levels</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Price (KES)</label>
                            <input type="number" wire:model="formData.price" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Original Price</label>
                            <input type="number" wire:model="formData.original_price" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Duration Label</label>
                            <input wire:model="formData.duration_label" placeholder="6 Weeks" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Delivery Mode</label>
                            <select wire:model="formData.delivery_mode" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="online">Online</option>
                                <option value="in_person">In-Person</option>
                                <option value="hybrid">Hybrid</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="formData.is_published" class="w-4 h-4 rounded">
                                <span class="text-sm">Published</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="formData.is_featured" class="w-4 h-4 rounded">
                                <span class="text-sm">Featured</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-border">
                        <button wire:click="$set('showForm', false)" class="border border-border rounded-xl px-4 py-2 text-sm font-medium hover:bg-muted transition-colors">Cancel</button>
                        <button wire:click="save" class="bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">
                            {{ $editingId ? 'Update Program' : 'Create Program' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
