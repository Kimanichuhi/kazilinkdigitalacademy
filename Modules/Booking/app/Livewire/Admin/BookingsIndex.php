<?php

namespace Modules\Booking\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Booking\Enums\BookingStatus;
use Modules\Booking\Events\NationalIdRevealed;
use Modules\Booking\Models\Booking;
use Modules\Booking\Services\BookingLifecycle;
use Modules\Core\Support\RoleGroups;

#[Layout('admin::components.layouts.admin', ['title' => 'Bookings'])]
class BookingsIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    public ?string $viewingId = null;

    public ?string $revealedIdNumber = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updateStatus(string $id, string $newStatus, BookingLifecycle $lifecycle): void
    {
        abort_unless(auth()->user()?->hasRole(RoleGroups::ADMIN), 403);

        $booking = Booking::findOrFail($id);
        $lifecycle->transition($booking, BookingStatus::from($newStatus));
    }

    public function view(string $id): void
    {
        $this->viewingId = $id;
        $this->revealedIdNumber = null;
    }

    public function closeView(): void
    {
        $this->viewingId = null;
        $this->revealedIdNumber = null;
    }

    public function revealId(string $id): void
    {
        abort_unless(auth()->user()?->hasRole(RoleGroups::ADMIN), 403);

        $booking = Booking::findOrFail($id);
        $this->revealedIdNumber = $booking->id_number;

        NationalIdRevealed::dispatch(
            $booking->id,
            $booking->booking_number,
            auth()->id(),
            request()->ip(),
            request()->userAgent(),
        );
    }

    public function render()
    {
        $bookings = Booking::query()
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('full_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('booking_number', 'like', "%{$this->search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('booking::livewire.admin.bookings-index', [
            'bookings' => $bookings,
            'statuses' => BookingStatus::cases(),
            'viewingBooking' => $this->viewingId ? Booking::find($this->viewingId) : null,
            'canRevealId' => (bool) auth()->user()?->hasRole(RoleGroups::ADMIN),
            'canUpdateStatus' => (bool) auth()->user()?->hasRole(RoleGroups::ADMIN),
        ]);
    }
}
