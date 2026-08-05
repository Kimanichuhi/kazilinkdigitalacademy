<?php

namespace Modules\Marketing\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Marketing\Models\Statistic;

#[Layout('admin::components.layouts.admin', ['title' => 'Stats'])]
class StatisticsIndex extends Component
{
    use WithPagination;

    public bool $showForm = false;

    public ?string $editingId = null;

    public array $formData = [];

    public const ICONS = ['Users', 'BookOpen', 'TrendingUp', 'Globe', 'Award', 'DollarSign', 'Zap', 'Star'];

    protected function defaultForm(): array
    {
        return [
            'label' => '', 'value' => '', 'icon' => 'TrendingUp', 'order_index' => '0',
            'key' => '', 'description' => '', 'is_active' => true,
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
        $stat = Statistic::findOrFail($id);
        $this->editingId = $id;
        $this->formData = [
            'label' => $stat->label,
            'value' => $stat->value,
            'icon' => $stat->icon ?? 'TrendingUp',
            'order_index' => (string) $stat->order_index,
            'key' => $stat->key ?? '',
            'description' => $stat->description ?? '',
            'is_active' => $stat->is_active,
        ];
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'formData.label' => 'required|string',
            'formData.value' => 'required|string',
        ], [
            'formData.label.required' => 'Label and value required',
            'formData.value.required' => 'Label and value required',
        ]);

        $payload = [
            'label' => $this->formData['label'],
            'value' => $this->formData['value'],
            'icon' => $this->formData['icon'] ?: 'TrendingUp',
            'order_index' => (int) $this->formData['order_index'],
            'key' => $this->formData['key'] ?: null,
            'description' => $this->formData['description'] ?: null,
            'is_active' => $this->formData['is_active'],
        ];

        if ($this->editingId) {
            Statistic::findOrFail($this->editingId)->update($payload);
        } else {
            Statistic::create($payload);
        }

        $this->showForm = false;
    }

    public function delete(string $id): void
    {
        Statistic::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('marketing::livewire.admin.statistics-index', [
            'stats' => Statistic::orderBy('order_index')->paginate(25),
            'icons' => self::ICONS,
        ]);
    }
}
