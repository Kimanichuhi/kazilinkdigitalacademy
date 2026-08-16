<?php

namespace Modules\Booking\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Academy\Contracts\CohortLookupContract;
use Modules\Academy\Contracts\ProgramLookupContract;
use Modules\Booking\Models\Booking;
use Modules\Booking\Services\BookingCreationService;
use Modules\Booking\Support\KenyaCounties;
use Modules\Booking\Support\OlKalouOffer;
use Modules\Core\Support\ImageOptimizer;
use Modules\Payment\Contracts\MpesaPaymentContract;

/**
 * The 4-step public booking wizard (Program -> Details -> Payment ->
 * Review), matching app/booking/page.tsx field-for-field. No login
 * required — mirrors the source's anon-insert flow.
 *
 * The booking row is created as soon as Details is complete (status
 * `awaiting_payment`), not at final submit — this lets the M-Pesa method
 * gate progress on a real STK Push confirmation before enrollment is
 * considered final, matching the payment flow requirements. Other payment
 * methods (bank) are unaffected and proceed exactly as before.
 */
#[Layout('core::components.layouts.public', ['title' => 'Book Your Training'])]
class BookingWizard extends Component
{
    use WithFileUploads;

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

    /**
     * Ol Kalou always has a special offer running (see the Ol Kalou Special
     * Offer Notice) — when the applicant's constituency is Ol Kalou, the
     * Details step offers an optional ID-photo upload for eligibility
     * verification. Optional and skippable: not providing it never blocks
     * booking, matching the notice's "What happens if you do not provide
     * your ID?" section.
     */
    public $idDocumentPhoto = null;

    public ?string $idDocumentPath = null;

    public bool $olKalouIdConsent = false;

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
        $this->clearOlKalouDocument();
    }

    public function updatedConstituency(): void
    {
        if (! $this->isOlKalouConstituency()) {
            $this->clearOlKalouDocument();
        }
    }

    public function constituencyOptions(): array
    {
        return KenyaCounties::all()[$this->county] ?? [];
    }

    public function isOlKalouConstituency(): bool
    {
        return OlKalouOffer::isEligible($this->constituency);
    }

    public function basePrice(): ?float
    {
        $price = $this->selectedCohort['price'] ?? $this->selectedProgram['price'] ?? null;

        return $price !== null ? (float) $price : null;
    }

    public function amountDue(): ?float
    {
        return OlKalouOffer::apply($this->basePrice(), $this->constituency);
    }

    public function updatedIdDocumentPhoto(): void
    {
        // Same whitelist/size cap as Core\Livewire\ImageUpload — explicit
        // mimes rather than the generic 'image' rule, which accepts SVG
        // (can carry embedded scripts).
        $this->validate([
            'idDocumentPhoto' => 'mimes:jpg,jpeg,png,webp|max:5120',
        ], [], ['idDocumentPhoto' => 'ID document photo']);

        $this->deleteStoredIdDocument();

        $path = $this->idDocumentPhoto->store('booking-id-documents', 'local');
        ImageOptimizer::optimize(Storage::disk('local')->path($path));

        $this->idDocumentPath = $path;
        $this->idDocumentPhoto = null;
    }

    public function removeIdDocumentPhoto(): void
    {
        $this->clearOlKalouDocument();
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

        $amount = $this->amountDue() ?? 0;

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
            'payment_reference' => $this->paymentMethod !== 'mpesa' ? ($this->paymentReference ?: null) : null,
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
            'documents_urls' => $this->isOlKalouConstituency() && $this->idDocumentPath && $this->olKalouIdConsent
                ? ['ol_kalou_id_document_path' => $this->idDocumentPath]
                : null,
            'consent_given' => $this->consent_given,
            'user_id' => auth()->id(),
            'total_amount' => $this->amountDue(),
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
            'olKalouIdConsent' => $this->idDocumentPath ? 'accepted' : 'nullable',
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
            'olKalouIdConsent.accepted' => 'Confirm consent before uploading your ID for the Ol Kalou offer',
        ];
    }

    protected function clearOlKalouDocument(): void
    {
        $this->deleteStoredIdDocument();
        $this->idDocumentPath = null;
        $this->idDocumentPhoto = null;
        $this->olKalouIdConsent = false;
    }

    protected function deleteStoredIdDocument(): void
    {
        if ($this->idDocumentPath) {
            Storage::disk('local')->delete($this->idDocumentPath);
        }
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
