<?php

namespace Modules\Cms\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Cms\Models\PricingPlan;

#[Layout('admin::components.layouts.admin', ['title' => 'Pricing Plans'])]
class PricingPlansIndex extends Component
{
    use WithPagination;

    public bool $showForm = false;

    public ?string $editingId = null;

    public array $formData = [];

    protected function defaultForm(): array
    {
        return [
            'name' => '', 'tag' => '', 'price' => '', 'currency' => 'KES', 'period' => '',
            'description' => '', 'features' => '', 'cta_text' => 'Get Started', 'cta_link' => '/booking',
            'is_highlighted' => false, 'is_published' => true,
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
        $plan = PricingPlan::findOrFail($id);
        $this->editingId = $id;
        $this->formData = [
            'name' => $plan->name,
            'tag' => $plan->tag ?? '',
            'price' => (string) $plan->price,
            'currency' => $plan->currency,
            'period' => $plan->period ?? '',
            'description' => $plan->description ?? '',
            'features' => $plan->features ? implode("\n", $plan->features) : '',
            'cta_text' => $plan->cta_text,
            'cta_link' => $plan->cta_link ?? '',
            'is_highlighted' => $plan->is_highlighted,
            'is_published' => $plan->is_published,
        ];
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'formData.name' => 'required|string',
            'formData.price' => 'required|numeric',
        ], [
            'formData.name.required' => 'Plan name required',
            'formData.price.required' => 'Price required',
            'formData.price.numeric' => 'Price must be a number',
        ]);

        $features = collect(explode("\n", $this->formData['features']))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        $payload = [
            'name' => $this->formData['name'],
            'tag' => $this->formData['tag'] ?: null,
            'price' => $this->formData['price'],
            'currency' => $this->formData['currency'] ?: 'KES',
            'period' => $this->formData['period'] ?: null,
            'description' => $this->formData['description'] ?: null,
            'features' => $features ?: null,
            'cta_text' => $this->formData['cta_text'] ?: 'Get Started',
            'cta_link' => $this->formData['cta_link'] ?: null,
            'is_highlighted' => $this->formData['is_highlighted'],
            'is_published' => $this->formData['is_published'],
        ];

        if ($this->editingId) {
            PricingPlan::findOrFail($this->editingId)->update($payload);
        } else {
            $payload['order_index'] = PricingPlan::count() + 1;
            PricingPlan::create($payload);
        }

        $this->showForm = false;
    }

    public function delete(string $id): void
    {
        PricingPlan::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('cms::livewire.admin.pricing-plans-index', [
            'plans' => PricingPlan::orderBy('order_index')->paginate(25),
        ]);
    }
}
