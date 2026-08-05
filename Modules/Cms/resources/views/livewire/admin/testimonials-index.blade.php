<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black">Testimonials</h1>
            <p class="text-sm text-muted-foreground">{{ $testimonials->total() }} testimonials</p>
        </div>
        <button wire:click="openCreate" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">
            <x-core::icon name="plus" class="w-4 h-4" /> Add Testimonial
        </button>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($testimonials as $testimonial)
            <div wire:key="testimonial-{{ $testimonial->id }}" class="relative bg-card border border-border rounded-2xl p-5">
                <div class="absolute top-3 right-3 flex gap-1">
                    <button wire:click="openEdit('{{ $testimonial->id }}')" class="p-1.5 hover:bg-muted rounded-lg transition-colors text-muted-foreground hover:text-foreground" title="Edit">
                        <x-core::icon name="edit" class="w-3.5 h-3.5" />
                    </button>
                    <button wire:click="delete('{{ $testimonial->id }}')" wire:confirm="Delete?" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors text-muted-foreground hover:text-red-600" title="Delete">
                        <x-core::icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
                <div class="flex items-center gap-3 mb-3 pr-14">
                    @if ($testimonial->student_avatar_url)
                        <img src="{{ $testimonial->student_avatar_url }}" alt="" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                    @else
                        <div class="w-12 h-12 rounded-full bg-brand-500 flex items-center justify-center text-white font-bold flex-shrink-0">
                            {{ strtoupper(substr($testimonial->student_name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="font-semibold text-sm truncate">{{ $testimonial->student_name }}</p>
                        @if ($testimonial->student_title)
                            <p class="text-xs text-muted-foreground truncate">{{ $testimonial->student_title }}</p>
                        @endif
                    </div>
                </div>
                <p class="text-sm text-muted-foreground line-clamp-3 mb-3">{{ $testimonial->content }}</p>
                <div class="flex items-center justify-between">
                    <div class="flex gap-0.5">
                        @for ($i = 1; $i <= 5; $i++)
                            <x-core::icon name="star" class="w-3.5 h-3.5 {{ $i <= (int) $testimonial->rating ? 'text-amber-400 fill-amber-400' : 'text-gray-300' }}" />
                        @endfor
                    </div>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $testimonial->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $testimonial->is_published ? 'Published' : 'Draft' }}
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-muted-foreground">No testimonials yet</div>
        @endforelse
    </div>

    @if ($testimonials->hasPages())
        <div>{{ $testimonials->links() }}</div>
    @endif

    @if ($showForm)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" wire:click.self="$set('showForm', false)">
            <div class="bg-background border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="flex items-center justify-between p-5 border-b border-border">
                    <h2 class="font-bold text-lg">{{ $editingId ? 'Edit Testimonial' : 'Add Testimonial' }}</h2>
                    <button wire:click="$set('showForm', false)" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-4">
                    <livewire:core::image-upload wire:model="formData.student_avatar_url" folder="testimonials" label="Avatar Photo" shape="circle" :key="'avatar-'.($editingId ?? 'new')" />

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">Student Name *</label>
                            <input wire:model="formData.student_name" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @error('formData.student_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Title / Role</label>
                            <input wire:model="formData.student_title" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Income Before</label>
                            <input wire:model="formData.income_before" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Income After</label>
                            <input wire:model="formData.income_after" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium mb-1 block">Testimonial *</label>
                        <textarea wire:model="formData.content" rows="4" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                        @error('formData.content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">Rating</label>
                            <select wire:model="formData.rating" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                @foreach ([5, 4, 3, 2, 1] as $star)
                                    <option value="{{ $star }}">{{ $star }} Stars</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-4">
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
