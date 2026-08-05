<?php

namespace Modules\Booking\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\Academy\Contracts\ProgramLookupContract;
use Modules\Booking\Enums\BookingStatus;
use Modules\Booking\Models\Booking;

#[Layout('core::components.layouts.public', ['title' => 'My Dashboard'])]
class StudentDashboard extends Component
{
    public function render(ProgramLookupContract $programs)
    {
        $user = Auth::user();

        $rawBookings = Booking::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $programsById = $programs->findMany($rawBookings->pluck('program_id')->all());

        $bookings = $rawBookings
            ->map(function (Booking $booking) use ($programsById) {
                $program = $programsById[$booking->program_id] ?? null;

                return [
                    'id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'status' => $booking->status->value,
                    'payment_status' => $booking->payment_status->value,
                    'total_amount' => $booking->total_amount,
                    'currency' => $booking->currency,
                    'created_at' => $booking->created_at,
                    'rejection_reason' => $booking->rejection_reason,
                    'program_title' => $program['title'] ?? 'Program',
                ];
            });

        $active = $bookings->whereIn('status', [
            BookingStatus::Approved->value,
            BookingStatus::PendingApproval->value,
            BookingStatus::Paid->value,
            BookingStatus::AwaitingPayment->value,
        ]);
        $completed = $bookings->where('status', BookingStatus::Completed->value);

        return view('booking::livewire.student-dashboard', [
            'user' => $user,
            'bookings' => $bookings,
            'activeCount' => $active->count(),
            'completedCount' => $completed->count(),
        ]);
    }
}
