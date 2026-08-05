<?php

namespace Modules\Cms\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Cms\Models\Testimonial;
use Modules\Core\Support\OptionalContract;
use Modules\Marketing\Contracts\StatisticLookupContract;

#[Layout('core::components.layouts.public', ['title' => 'Success Stories'])]
class SuccessStoriesPage extends Component
{
    use WithPagination;

    public string $minRating = '';

    public function updatingMinRating(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $testimonials = Testimonial::query()
            ->where('is_published', true)
            ->when($this->minRating, fn ($query) => $query->where('rating', '>=', $this->minRating))
            ->orderBy('order_index')
            ->paginate(9);

        $statistics = OptionalContract::resolve(StatisticLookupContract::class);

        return view('cms::livewire.success-stories-page', [
            'testimonials' => $testimonials,
            'stats' => collect($statistics?->listActive() ?? [])->keyBy('key'),
        ]);
    }
}
