<?php

namespace Modules\Audit\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Audit\Models\AuditLog;
use Modules\User\Contracts\UserLookupContract;

#[Layout('admin::components.layouts.admin', ['title' => 'Audit Log'])]
class AuditLogsIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $action = '';

    public ?string $expandedId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingAction(): void
    {
        $this->resetPage();
    }

    public function toggleExpand(string $id): void
    {
        $this->expandedId = $this->expandedId === $id ? null : $id;
    }

    public function render(UserLookupContract $users)
    {
        $logs = AuditLog::query()
            ->when($this->action, fn ($q) => $q->where('action', $this->action))
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('resource_type', 'like', "%{$this->search}%")
                        ->orWhere('resource_id', 'like', "%{$this->search}%")
                        ->orWhere('ip_address', 'like', "%{$this->search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(25);

        $userIds = $logs->getCollection()->pluck('user_id')->filter()->unique()->values()->all();
        $userMap = $userIds ? $users->findMany($userIds) : [];

        $actions = AuditLog::query()->distinct()->orderBy('action')->pluck('action');

        return view('audit::livewire.admin.audit-logs-index', [
            'logs' => $logs,
            'userMap' => $userMap,
            'actions' => $actions,
        ]);
    }
}
