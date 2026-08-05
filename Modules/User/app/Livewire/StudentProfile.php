<?php

namespace Modules\User\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\User\Events\UserProfileUpdated;

#[Layout('core::components.layouts.public', ['title' => 'Edit Profile'])]
class StudentProfile extends Component
{
    public string $name = '';

    public string $phone = '';

    public string $bio = '';

    public string $avatarUrl = '';

    public bool $saved = false;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->phone = $user->phone ?? '';
        $this->bio = $user->bio ?? '';
        $this->avatarUrl = $user->avatar_url ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|min:2',
            'phone' => 'nullable|string',
            'bio' => 'nullable|string',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $oldValues = $user->only(['name', 'phone', 'bio', 'avatar_url']);

        $user->update([
            'name' => $this->name,
            'phone' => $this->phone ?: null,
            'bio' => $this->bio ?: null,
            'avatar_url' => $this->avatarUrl ?: null,
        ]);

        UserProfileUpdated::dispatch(
            $user->id,
            $oldValues,
            $user->only(['name', 'phone', 'bio', 'avatar_url']),
            request()->ip(),
            request()->userAgent(),
        );

        $this->saved = true;
    }

    public function render()
    {
        return view('user::livewire.student-profile', [
            'email' => Auth::user()->email,
        ]);
    }
}
