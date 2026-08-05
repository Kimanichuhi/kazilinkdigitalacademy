<?php

namespace Modules\Core\Livewire;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Modelable;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Core\Support\ImageOptimizer;

/**
 * Reusable admin image uploader — matches components/admin/ImageUpload.tsx:
 * 5MB cap, image mimes only, stored on the `public` disk under $folder,
 * replacing Supabase Storage's `media` bucket.
 */
class ImageUpload extends Component
{
    use WithFileUploads;

    #[Modelable]
    public string $value = '';

    public string $folder = 'uploads';

    public string $label = 'Image';

    public string $shape = 'square';

    public $file = null;

    public function updatedFile(): void
    {
        // Explicit mimes whitelist rather than the generic 'image' rule —
        // that rule accepts SVG, which can carry embedded scripts.
        $this->validate([
            'file' => 'mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $path = $this->file->store($this->folder, 'public');

        // Cap dimensions and re-encode so admin uploads (up to the 5MB cap
        // above) aren't served at full upload size regardless of how small
        // they're actually displayed — see ImageOptimizer for why animated
        // GIFs are deliberately skipped.
        ImageOptimizer::optimize(Storage::disk('public')->path($path));

        $this->value = asset('storage/'.$path);
        $this->file = null;
    }

    public function remove(): void
    {
        $this->value = '';
    }

    public function render()
    {
        return view('core::livewire.image-upload');
    }
}
