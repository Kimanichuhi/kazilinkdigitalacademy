<?php

namespace Modules\Cms\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Cms\Contracts\ResourceLookupContract;

#[Layout('core::components.layouts.public', ['title' => 'Free Resources'])]
class ResourcesPage extends Component
{
    use WithPagination;

    public string $search = '';

    public string $type = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function render(ResourceLookupContract $resources)
    {
        $result = $resources->listPublishedPaginated([
            'type' => $this->type ?: null,
            'search' => $this->search ?: null,
        ], 12);

        return view('cms::livewire.resources-page', [
            'resources' => new LengthAwarePaginator(
                $result['data'],
                $result['meta']['total'],
                $result['meta']['per_page'],
                $result['meta']['current_page'],
            ),
        ]);
    }
}
