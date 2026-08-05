<div class="max-w-2xl mx-auto px-4 sm:px-6 py-10">
    <h1 class="text-3xl font-black mb-8">Edit Profile</h1>
    <div class="bg-card border border-border rounded-2xl p-6">
        @if ($saved)
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 mb-5">Profile updated!</div>
        @endif
        <form wire:submit="save" class="space-y-5">
            <div>
                <livewire:core::image-upload wire:model="avatarUrl" folder="avatars" label="Profile Photo" shape="circle" />
            </div>
            <div>
                <label class="text-sm font-medium mb-1.5 block">Full Name</label>
                <input wire:model="name" class="w-full px-3 py-2.5 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium mb-1.5 block">Email</label>
                <input value="{{ $email }}" disabled class="w-full px-3 py-2.5 border border-border rounded-xl text-sm bg-muted text-muted-foreground cursor-not-allowed">
                <p class="text-xs text-muted-foreground mt-1">Email cannot be changed here</p>
            </div>
            <div>
                <label class="text-sm font-medium mb-1.5 block">Phone Number</label>
                <input wire:model="phone" data-clarity-mask="true" class="w-full px-3 py-2.5 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
            </div>
            <div>
                <label class="text-sm font-medium mb-1.5 block">Bio</label>
                <textarea wire:model="bio" rows="3" placeholder="Tell us a bit about yourself..." class="w-full px-3 py-2.5 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background resize-none"></textarea>
            </div>
            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2.5 text-sm font-semibold transition-colors disabled:opacity-50">
                <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-2"><x-core::icon name="check-circle" class="w-4 h-4" /> Save Profile</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </form>
    </div>

    <div class="mt-4">
        <a href="{{ route('student.sessions') }}" class="inline-flex items-center gap-2 text-sm font-medium text-brand-600 hover:text-brand-700">
            <x-core::icon name="shield" class="w-4 h-4" /> Manage active sessions
        </a>
    </div>
</div>
