<?php

namespace Modules\Academy\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Academy\Contracts\CohortLookupContract;

#[Layout('core::components.layouts.public', ['title' => 'Upcoming Cohorts'])]
class CohortsIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(CohortLookupContract $cohorts)
    {
        $result = $cohorts->listPublicPaginated(['search' => $this->search ?: null], 12);

        return view('academy::livewire.cohorts-index', [
            'cohorts' => new LengthAwarePaginator(
                $result['data'],
                $result['meta']['total'],
                $result['meta']['per_page'],
                $result['meta']['current_page'],
            ),
        ]);
    }
}
