<?php

namespace Modules\Academy\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Academy\Contracts\ProgramCategoryLookupContract;
use Modules\Academy\Contracts\ProgramLookupContract;

#[Layout('core::components.layouts.public', ['title' => 'Programs'])]
class ProgramsIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $selectedCategory = '';

    public string $selectedLevel = '';

    public string $selectedMode = '';

    public string $sortBy = 'order_index';

    public bool $filtersOpen = false;

    public function clearFilters(): void
    {
        $this->reset(['search', 'selectedCategory', 'selectedLevel', 'selectedMode', 'sortBy']);
        $this->resetPage();
    }

    public function toggleCategory(string $categoryId): void
    {
        $this->selectedCategory = $this->selectedCategory === $categoryId ? '' : $categoryId;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedLevel(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedMode(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function getHasFiltersProperty(): bool
    {
        return $this->search !== '' || $this->selectedCategory !== '' || $this->selectedLevel !== '' || $this->selectedMode !== '';
    }

    public function render(ProgramLookupContract $programs, ProgramCategoryLookupContract $categories)
    {
        $result = $programs->listPublishedPaginated([
            'category_id' => $this->selectedCategory ?: null,
            'level' => $this->selectedLevel ?: null,
            'delivery_mode' => $this->selectedMode ?: null,
            'search' => $this->search ?: null,
            'sort' => $this->sortBy,
        ], 12);

        return view('academy::livewire.programs-index', [
            'programs' => new LengthAwarePaginator(
                $result['data'],
                $result['meta']['total'],
                $result['meta']['per_page'],
                $result['meta']['current_page'],
            ),
            'categories' => $categories->listActive(),
        ]);
    }
}
