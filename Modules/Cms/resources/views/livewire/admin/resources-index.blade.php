<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black">Resources</h1>
            <p class="text-sm text-muted-foreground">{{ $resources->total() }} resources</p>
        </div>
        <button wire:click="openCreate" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">
            <x-core::icon name="plus" class="w-4 h-4" /> Add Resource
        </button>
    </div>

    <div class="bg-card border border-border rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border bg-muted/30">
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Resource</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Type</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Downloads</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Status</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($resources as $resource)
                    <tr wire:key="resource-{{ $resource->id }}" class="border-b border-border hover:bg-muted/20 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium">{{ $resource->title }}</p>
                            <p class="text-xs text-muted-foreground line-clamp-1">{{ $resource->description }}</p>
                        </td>
                        <td class="px-5 py-3 text-muted-foreground text-xs capitalize">{{ $resource->type }}</td>
                        <td class="px-5 py-3 text-muted-foreground text-xs">{{ number_format($resource->download_count) }}</td>
                        <td class="px-5 py-3">
                            <div class="flex flex-wrap gap-1">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $resource->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $resource->is_published ? 'Published' : 'Draft' }}
                                </span>
                                @if ($resource->is_paid)
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-brand-50 text-brand-600">{{ $resource->currency }} {{ number_format($resource->price, 0) }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex gap-1.5">
                                <button wire:click="openEdit('{{ $resource->id }}')" class="p-1.5 hover:bg-muted rounded-lg transition-colors text-muted-foreground hover:text-foreground" title="Edit">
                                    <x-core::icon name="edit" class="w-3.5 h-3.5" />
                                </button>
                                <button wire:click="delete('{{ $resource->id }}')" wire:confirm="Delete?" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors text-muted-foreground hover:text-red-600" title="Delete">
                                    <x-core::icon name="trash" class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-muted-foreground">No resources yet</td></tr>
                @endforelse
            </tbody>
        </table>

        @if ($resources->hasPages())
            <div class="px-5 py-3 border-t border-border">
                {{ $resources->links() }}
            </div>
        @endif
    </div>

    @if ($showForm)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
            <div class="bg-background border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border">
                    <h2 class="font-bold text-lg">{{ $editingId ? 'Edit Resource' : 'Add Resource' }}</h2>
                    <button wire:click="$set('showForm', false)" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="text-xs font-medium mb-1 block">Title *</label>
                        <input wire:model="formData.title" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        @error('formData.title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium mb-1 block">Description</label>
                        <textarea wire:model="formData.description" rows="2" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">Type</label>
                            <select wire:model="formData.type" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                @foreach (['pdf', 'video', 'template', 'guide', 'spreadsheet', 'ebook', 'audio', 'other'] as $type)
                                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Tags (comma-separated)</label>
                            <input wire:model="formData.tags" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-medium mb-1 block">File URL</label>
                        <input wire:model="formData.file_url" placeholder="https://..." class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="formData.is_free" class="w-4 h-4 rounded">
                            <span class="text-sm">Free</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="formData.is_published" class="w-4 h-4 rounded">
                            <span class="text-sm">Published</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="formData.is_paid" class="w-4 h-4 rounded">
                            <span class="text-sm">Premium (paid)</span>
                        </label>
                    </div>

                    @if ($formData['is_paid'])
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium mb-1 block">Price (KES)</label>
                                <input type="number" step="0.01" wire:model="formData.price" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="text-xs font-medium mb-1 block">Download Limit (optional)</label>
                                <input type="number" wire:model="formData.download_limit" placeholder="Unlimited" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-2 border-t border-border">
                        <button wire:click="$set('showForm', false)" class="border border-border rounded-xl px-4 py-2 text-sm font-medium hover:bg-muted transition-colors">Cancel</button>
                        <button wire:click="save" class="bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">
                            {{ $editingId ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
