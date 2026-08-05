<?php

namespace Modules\Marketing\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Marketing\Models\Cta;

#[Layout('admin::components.layouts.admin', ['title' => 'CTAs'])]
class CtasIndex extends Component
{
    use WithPagination;

    public bool $showForm = false;

    public ?string $editingId = null;

    public array $formData = [];

    public const PLACEMENTS = ['homepage', 'about', 'programs', 'pricing', 'booking', 'blog', 'contact'];

    protected function defaultForm(): array
    {
        return [
            'name' => '', 'title' => '', 'subtitle' => '',
            'button_one_text' => '', 'button_one_link' => '',
            'button_two_text' => '', 'button_two_link' => '',
            'priority' => '0', 'description' => '', 'background_color' => '#0f172a',
            'placement' => ['homepage'], 'is_active' => true,
        ];
    }

    public function mount(): void
    {
        $this->formData = $this->defaultForm();
    }

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->formData = $this->defaultForm();
        $this->showForm = true;
    }

    public function openEdit(string $id): void
    {
        $cta = Cta::findOrFail($id);
        $this->editingId = $id;
        $this->formData = [
            'name' => $cta->name,
            'title' => $cta->title,
            'subtitle' => $cta->subtitle ?? '',
            'button_one_text' => $cta->button_one_text ?? '',
            'button_one_link' => $cta->button_one_link ?? '',
            'button_two_text' => $cta->button_two_text ?? '',
            'button_two_link' => $cta->button_two_link ?? '',
            'priority' => (string) $cta->priority,
            'description' => $cta->description ?? '',
            'background_color' => $cta->background_color ?? '#0f172a',
            'placement' => $cta->placement ?: ['homepage'],
            'is_active' => $cta->is_active,
        ];
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'formData.name' => 'required|string',
            'formData.title' => 'required|string',
        ], [
            'formData.name.required' => 'Name and title required',
            'formData.title.required' => 'Name and title required',
        ]);

        $payload = [
            'name' => $this->formData['name'],
            'title' => $this->formData['title'],
            'subtitle' => $this->formData['subtitle'] ?: null,
            'button_one_text' => $this->formData['button_one_text'] ?: null,
            'button_one_link' => $this->formData['button_one_link'] ?: null,
            'button_two_text' => $this->formData['button_two_text'] ?: null,
            'button_two_link' => $this->formData['button_two_link'] ?: null,
            'priority' => (int) $this->formData['priority'],
            'description' => $this->formData['description'] ?: null,
            'background_color' => $this->formData['background_color'] ?: null,
            'placement' => $this->formData['placement'] ?: ['homepage'],
            'is_active' => $this->formData['is_active'],
        ];

        if ($this->editingId) {
            Cta::findOrFail($this->editingId)->update($payload);
        } else {
            $payload['button_one_style'] = 'primary';
            $payload['button_two_style'] = 'outline';
            Cta::create($payload);
        }

        $this->showForm = false;
    }

    public function delete(string $id): void
    {
        Cta::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('marketing::livewire.admin.ctas-index', [
            'ctas' => Cta::orderByDesc('priority')->paginate(20),
            'placements' => self::PLACEMENTS,
        ]);
    }
}
