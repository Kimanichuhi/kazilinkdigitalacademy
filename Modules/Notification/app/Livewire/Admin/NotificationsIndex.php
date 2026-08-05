<?php

namespace Modules\Notification\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Notification\Enums\NotificationType;
use Modules\Notification\Models\Notification;
use Modules\User\Enums\UserRole;

#[Layout('admin::components.layouts.admin', ['title' => 'Notifications'])]
class NotificationsIndex extends Component
{
    use WithPagination;

    public bool $showForm = false;

    public array $formData = [];

    public string $search = '';

    protected function defaultForm(): array
    {
        return [
            'title' => '',
            'message' => '',
            'type' => NotificationType::Info->value,
            'link' => '',
            'audience' => 'user',
            'user_id' => '',
            'role' => '',
        ];
    }

    public function mount(): void
    {
        $this->formData = $this->defaultForm();
    }

    public function openCreate(): void
    {
        $this->formData = $this->defaultForm();
        $this->resetErrorBag();
        $this->showForm = true;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function save(): void
    {
        $this->validate([
            'formData.title' => 'required|string|max:255',
            'formData.message' => 'required|string',
            'formData.type' => 'required|in:'.implode(',', array_column(NotificationType::cases(), 'value')),
            'formData.link' => 'nullable|url',
            'formData.audience' => 'required|in:user,role,all',
            'formData.user_id' => 'required_if:formData.audience,user',
            'formData.role' => 'required_if:formData.audience,role',
        ]);

        $recipientIds = match ($this->formData['audience']) {
            'user' => array_filter([$this->formData['user_id']]),
            'role' => User::role($this->formData['role'])->pluck('id')->all(),
            'all' => User::pluck('id')->all(),
        };

        if (empty($recipientIds)) {
            $this->addError('formData.audience', 'No matching users found for this audience.');

            return;
        }

        foreach ($recipientIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'title' => $this->formData['title'],
                'message' => $this->formData['message'],
                'type' => $this->formData['type'],
                'link' => $this->formData['link'] ?: null,
                'is_read' => false,
            ]);
        }

        $this->showForm = false;
        $this->resetPage();
    }

    public function delete(string $id): void
    {
        Notification::findOrFail($id)->delete();
    }

    public function render()
    {
        $notifications = Notification::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('message', 'like', "%{$this->search}%");
            }))
            ->orderByDesc('created_at')
            ->paginate(20);

        $userIds = $notifications->getCollection()->pluck('user_id')->filter()->unique()->values()->all();
        $userMap = $userIds ? User::whereIn('id', $userIds)->pluck('name', 'id') : collect();

        return view('notification::livewire.admin.notifications-index', [
            'notifications' => $notifications,
            'userMap' => $userMap,
            'types' => NotificationType::cases(),
            'roles' => UserRole::cases(),
            // Short TTL: this dropdown rarely needs to reflect a
            // brand-new user within seconds, and it otherwise re-runs
            // on every render (every search keystroke / pagination click).
            'users' => Cache::remember(
                'admin.notification-recipients',
                now()->addMinutes(5),
                fn () => User::orderBy('name')->limit(200)->get(['id', 'name', 'email']),
            ),
        ]);
    }
}
