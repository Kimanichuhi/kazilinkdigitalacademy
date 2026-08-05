<?php

namespace Modules\Cms\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Cms\Models\Purchase;
use Modules\Core\Support\RoleGroups;

#[Layout('admin::components.layouts.admin', ['title' => 'Purchases'])]
class PurchasesIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    // Revenue/purchase data is financially sensitive — narrower than the
    // blanket admin-family gate this screen otherwise sits behind.
    public function mount(): void
    {
        abort_unless(auth()->user()?->hasAnyRole([
            RoleGroups::SUPER_ADMIN, RoleGroups::ADMIN, RoleGroups::FINANCE,
        ]), 403);
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $purchases = Purchase::query()
            ->with(['resource', 'user'])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('cms::livewire.admin.purchases-index', [
            'purchases' => $purchases,
            'totalRevenue' => Purchase::where('status', 'paid')->sum('amount'),
        ]);
    }
}
