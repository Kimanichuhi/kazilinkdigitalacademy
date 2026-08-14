<?php

namespace Modules\Booking\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Modules\Academy\Contracts\CohortLookupContract;
use Modules\Academy\Contracts\ProgramLookupContract;
use Modules\Booking\Models\Booking;
use Modules\Booking\Services\BookingCreationService;
use Modules\Booking\Support\KenyaCounties;
use Modules\Payment\Contracts\MpesaPaymentContract;

/**
 * The 4-step public booking wizard (Program -> Details -> Payment ->
 * Review), matching app/booking/page.tsx field-for-field. No login
 * required — mirrors the source's anon-insert flow.
 *
 * The booking row is created as soon as Details is complete (status
 * `awaiting_payment`), not at final submit. M-Pesa currently collects the
 * customer-entered transaction code for admin confirmation while the STK
 * Push path is prepared for a later production rollout.
 */
#[Layout('core::components.layouts.public', ['title' => 'Book Your Training'])]
class BookingWizard extends Component
{
    public int $step = 1;

    #[Url]
    public ?string $program = null;

    #[Url]
    public ?string $cohort = null;

    public ?array $selectedProgram = null;

    public ?array $selectedCohort = null;

    // Step 2 — personal details (field names match the source zod schema exactly)
    public string $full_name = '';

    public string $email = '';

    public string $phone = '';

    public string $id_number = '';

    public string $date_of_birth = '';

    public string $gender = '';

    public string $nationality = '';

    public string $address = '';

    public string $county = '';

    public string $constituency = '';

    public string $current_occupation = '';

    public string $employer = '';

    public string $education_level = '';

    public string $referral_source = '';

    public string $emergency_contact_name = '';

    public string $emergency_contact_phone = '';

    public string $special_requirements = '';

    public bool $consent_given = false;

    // Step 3 — payment
    public string $paymentMethod = 'mpesa';

    public string $paymentReference = '';

    public bool $submitting = false;

    public ?string $bookingNumber = null;

    /**
     * The booking row, created once Details is complete rather than at
     * final submit. Never bound to a form input — locked so a crafted
     * client update can't repoint later steps (payNow/submit) at a
     * different, unrelated booking.
     */
    #[Locked]
    public ?string $bookingId = null;

    public string $mpesaPhone = '';

    public bool $mpesaPushing = false;

    public ?string $mpesaError = null;

    public ?string $mpesaCheckoutRequestId = null;

    public function mount(ProgramLookupContract $programs, CohortLookupContract $cohorts): void
    {
        if ($this->program) {
            $this->selectedProgram = $programs->find($this->program);
        }

        if ($this->cohort) {
            $found = $cohorts->find($this->cohort);
            if ($found) {
                $this->selectedCohort = $found;
                $this->selectedProgram = $found['program'] ?? $this->selectedProgram;
                $this->program = $this->selectedProgram['id'] ?? null;
                $this->step = 2;
            }
        }
    }

    public function selectProgram(string $programId, ProgramLookupContract $programs): void
    {
        $this->selectedProgram = $programs->find($programId);
        $this->program = $programId;
        $this->selectedCohort = null;
        $this->cohort = null;
    }

    public function selectCohort(?string $cohortId, CohortLookupContract $cohorts): void
    {
        if ($cohortId === null || ($this->selectedCohort['id'] ?? null) === $cohortId) {
            $this->selectedCohort = null;
            $this->cohort = null;

            return;
        }

        $this->selectedCohort = $cohorts->find($cohortId);
        $this->cohort = $cohortId;
    }

    public function goToStep(int $step): void
    {
        $this->step = $step;
    }

    public function updatedCounty(): void
    {
        $this->constituency = '';
    }

    public function constituencyOptions(): array
    {
        return KenyaCounties::all()[$this->county] ?? [];
    }

    public function continueFromProgram(): void
    {
        if (! $this->selectedProgram) {
            return;
        }

        $this->step = 2;
    }

    public function continueFromDetails(BookingCreationService $bookings): void
    {
        $this->validate($this->detailsRules(), $this->detailsMessages());

        if (! $this->selectedProgram) {
            return;
        }

        if ($this->bookingId) {
            Booking::find($this->bookingId)?->update($this->bookingFields());
        } else {
            $booking = $bookings->create($this->bookingFields() + [
                'payment_method' => $this->paymentMethod,
            ]);
            $this->bookingId = $booking->id;
        }

        $this->mpesaPhone = $this->mpesaPhone ?: $this->phone;
        $this->step = 3;
    }

    public function continueFromPayment(): void
    {
        if ($this->paymentMethod === 'mpesa') {
            $this->paymentReference = strtoupper(preg_replace('/\s+/', '', $this->paymentReference));

            $this->validate([
                'paymentReference' => ['required', 'string', 'min:6', 'max:12', 'regex:/^[A-Z0-9]+$/'],
            ], [
                'paymentReference.required' => 'Enter your M-Pesa confirmation code',
                'paymentReference.min' => 'Enter a valid M-Pesa confirmation code',
                'paymentReference.max' => 'Enter a valid M-Pesa confirmation code',
                'paymentReference.regex' => 'Use only letters and numbers from the M-Pesa message',
            ]);
        }

        $this->step = 4;
    }

    public function payNow(MpesaPaymentContract $mpesa): void
    {
        $this->validate([
            'mpesaPhone' => 'required|string|min:9',
        ], [
            'mpesaPhone.required' => 'Valid M-Pesa phone number required',
            'mpesaPhone.min' => 'Valid M-Pesa phone number required',
        ]);

        if (! $this->bookingId) {
            return;
        }

        $booking = Booking::find($this->bookingId);

        if (! $booking) {
            return;
        }

        $booking->update(['payment_method' => 'mpesa']);

        $this->mpesaError = null;
        $this->mpesaPushing = true;

        $amount = $this->selectedCohort['price'] ?? $this->selectedProgram['price'] ?? 0;

        $result = $mpesa->initiateForBooking(
            bookingId: $booking->id,
            phone: $this->mpesaPhone,
            amount: (string) $amount,
            accountReference: $booking->booking_number,
            description: 'Kazilink Academy Booking '.$booking->booking_number,
        );

        $this->mpesaCheckoutRequestId = $result['checkout_request_id'];
        $this->mpesaError = $result['error'];
        $this->mpesaPushing = false;
    }

    #[On('mpesa-payment-succeeded')]
    public function onMpesaPaymentSucceeded(): void
    {
        $this->step = 4;
    }

    #[On('mpesa-payment-failed')]
    public function onMpesaPaymentFailed(?string $reason = null): void
    {
        $this->mpesaError = $reason ?: 'Payment failed. Please try again.';
        $this->mpesaCheckoutRequestId = null;
    }

    public function submit(BookingCreationService $bookings): void
    {
        $this->validate([
            'consent_given' => 'accepted',
        ], [
            'consent_given.accepted' => 'You must agree to the terms',
        ]);

        if (! $this->selectedProgram || ! $this->bookingId) {
            return;
        }

        $this->submitting = true;

        $booking = Booking::find($this->bookingId);

        if (! $booking) {
            $this->submitting = false;

            return;
        }

        $booking = $bookings->finalize($booking, $this->bookingFields() + [
            'payment_method' => $this->paymentMethod,
            'payment_reference' => $this->paymentReference ?: null,
        ]);

        $this->bookingNumber = $booking->booking_number;
        $this->step = 5;
        $this->submitting = false;
    }

    /**
     * Fields shared by the initial (Details-step) booking creation and the
     * final submit-step update, so both stay in sync as the user edits and
     * revisits earlier steps.
     */
    protected function bookingFields(): array
    {
        return [
            'program_id' => $this->selectedProgram['id'] ?? null,
            'cohort_id' => $this->selectedCohort['id'] ?? null,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'id_number' => $this->id_number ?: null,
            'date_of_birth' => $this->date_of_birth ?: null,
            'gender' => $this->gender ?: null,
            'nationality' => $this->nationality ?: null,
            'address' => $this->address ?: null,
            'county' => $this->county,
            'constituency' => $this->constituency,
            'current_occupation' => $this->current_occupation ?: null,
            'employer' => $this->employer ?: null,
            'education_level' => $this->education_level ?: null,
            'referral_source' => $this->referral_source ?: null,
            'emergency_contact_name' => $this->emergency_contact_name ?: null,
            'emergency_contact_phone' => $this->emergency_contact_phone ?: null,
            'special_requirements' => $this->special_requirements ?: null,
            'consent_given' => $this->consent_given,
            'user_id' => auth()->id(),
            'total_amount' => $this->selectedCohort['price'] ?? $this->selectedProgram['price'] ?? null,
            'currency' => $this->selectedProgram['currency'] ?? 'KES',
        ];
    }

    protected function detailsRules(): array
    {
        return [
            'full_name' => 'required|string|min:2',
            'email' => 'required|email',
            'phone' => 'required|string|min:9',
            'county' => 'required|string',
            'constituency' => 'required|string',
        ];
    }

    protected function detailsMessages(): array
    {
        return [
            'full_name.required' => 'Full name required',
            'full_name.min' => 'Full name required',
            'email.required' => 'Valid email required',
            'email.email' => 'Valid email required',
            'phone.required' => 'Valid phone required',
            'phone.min' => 'Valid phone required',
            'county.required' => 'County is required',
            'constituency.required' => 'Constituency is required',
        ];
    }

    public function render(ProgramLookupContract $programs, CohortLookupContract $cohorts)
    {
        return view('booking::livewire.booking-wizard', [
            'programs' => $programs->listPublished(),
            'cohorts' => $this->selectedProgram ? $cohorts->openForProgram($this->selectedProgram['id']) : [],
            'counties' => array_keys(KenyaCounties::all()),
        ]);
    }
}
