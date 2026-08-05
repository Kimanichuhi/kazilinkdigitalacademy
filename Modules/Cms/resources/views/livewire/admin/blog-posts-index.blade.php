<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black">Blog</h1>
            <p class="text-sm text-muted-foreground">{{ $posts->total() }} posts</p>
        </div>
        <button wire:click="openCreate" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">
            <x-core::icon name="plus" class="w-4 h-4" /> New Post
        </button>
    </div>

    <div class="bg-card border border-border rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border bg-muted/30">
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Title</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground hidden md:table-cell">Category</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Status</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground hidden lg:table-cell">Date</th>
                    <th class="text-left px-5 py-3 font-medium text-muted-foreground">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr wire:key="post-{{ $post->id }}" class="border-b border-border hover:bg-muted/20 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if ($post->thumbnail_url)
                                    <img src="{{ $post->thumbnail_url }}" alt="" class="w-10 h-8 rounded-lg object-cover flex-shrink-0">
                                @endif
                                <div class="min-w-0">
                                    <p class="font-medium truncate">{{ $post->title }}</p>
                                    <p class="text-xs text-muted-foreground truncate">{{ $post->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 hidden md:table-cell text-muted-foreground text-xs">{{ $post->category?->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <div class="flex gap-1.5">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $post->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $post->is_published ? 'Published' : 'Draft' }}
                                </span>
                                @if ($post->is_featured)
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-orange-100 text-orange-600">Featured</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3 hidden lg:table-cell text-muted-foreground text-xs">{{ $post->published_at?->format('d M Y') ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <div class="flex gap-1.5">
                                <button wire:click="openEdit('{{ $post->id }}')" class="p-1.5 hover:bg-muted rounded-lg transition-colors text-muted-foreground hover:text-foreground" title="Edit">
                                    <x-core::icon name="edit" class="w-3.5 h-3.5" />
                                </button>
                                <button wire:click="togglePublish('{{ $post->id }}')" class="p-1.5 hover:bg-muted rounded-lg transition-colors" title="{{ $post->is_published ? 'Unpublish' : 'Publish' }}">
                                    <x-core::icon :name="$post->is_published ? 'toggle-right' : 'toggle-left'" class="w-4 h-4 {{ $post->is_published ? 'text-green-600' : 'text-muted-foreground' }}" />
                                </button>
                                <button wire:click="delete('{{ $post->id }}')" wire:confirm="Delete?" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors text-muted-foreground hover:text-red-600" title="Delete">
                                    <x-core::icon name="trash" class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-muted-foreground">No posts yet</td></tr>
                @endforelse
            </tbody>
        </table>

        @if ($posts->hasPages())
            <div class="px-5 py-3 border-t border-border">
                {{ $posts->links() }}
            </div>
        @endif
    </div>

    @if ($showForm)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
            <div class="bg-background border border-border rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border">
                    <h2 class="font-bold text-lg">{{ $editingId ? 'Edit Post' : 'New Post' }}</h2>
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
                            <label class="text-xs font-medium mb-1 block">Category</label>
                            <select wire:model="formData.category_id" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="">No category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @disabled(! $category->is_active && $formData['category_id'] !== $category->id)>
                                        {{ $category->name }}{{ ! $category->is_active ? ' (inactive)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium mb-1 block">Excerpt</label>
                            <textarea wire:model="formData.excerpt" rows="2" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium mb-1 block">Content</label>
                            <textarea wire:model="formData.content" rows="8" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <livewire:core::image-upload wire:model="formData.thumbnail_url" folder="blog" label="Thumbnail" :key="'thumb-'.($editingId ?? 'new')" />
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Tags (comma-separated)</label>
                            <input wire:model="formData.tags" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Read Time (minutes)</label>
                            <input type="number" wire:model="formData.read_time_minutes" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div class="flex items-center gap-4 sm:col-span-2">
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
                            {{ $editingId ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
