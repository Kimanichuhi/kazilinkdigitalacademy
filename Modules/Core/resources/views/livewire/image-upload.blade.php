@php
    $previewClass = $shape === 'circle' ? 'w-16 h-16 rounded-full' : 'w-24 h-16 rounded-xl';
@endphp
<div>
    <label class="text-xs font-medium mb-1 block">{{ $label }}</label>
    <div class="flex items-center gap-3">
        @if ($value)
            <img src="{{ $value }}" alt="" class="{{ $previewClass }} object-cover flex-shrink-0 border border-border">
        @else
            <div class="{{ $previewClass }} bg-muted flex items-center justify-center text-muted-foreground text-[10px] text-center flex-shrink-0">No image</div>
        @endif
        <div class="flex items-center gap-2">
            <label class="cursor-pointer text-sm px-3 py-1.5 border border-border rounded-xl hover:bg-muted inline-flex items-center gap-2">
                <span wire:loading.remove wire:target="file">
                    <x-core::icon name="download" class="w-3.5 h-3.5" />
                </span>
                <span wire:loading wire:target="file">Uploading...</span>
                <span wire:loading.remove wire:target="file">{{ $value ? 'Replace' : 'Upload' }}</span>
                <input type="file" wire:model="file" accept="image/*" class="hidden">
            </label>
            @if ($value)
                <button type="button" wire:click="remove" class="text-xs text-red-500 hover:underline inline-flex items-center gap-0.5">
                    <x-core::icon name="x" class="w-3 h-3" /> Remove
                </button>
            @endif
        </div>
    </div>
    @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>
