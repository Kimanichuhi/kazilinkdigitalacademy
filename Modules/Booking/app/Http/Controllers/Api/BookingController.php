<?php

namespace Modules\Booking\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academy\Contracts\CohortLookupContract;
use Modules\Academy\Contracts\ProgramLookupContract;
use Modules\Booking\Http\Resources\BookingResource;
use Modules\Booking\Models\Booking;
use Modules\Booking\Services\BookingCreationService;
use Modules\Payment\Contracts\MpesaPaymentContract;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auth-required throughout — unlike the public web wizard, the API does not
 * support guest/anonymous bookings (a deliberate scope boundary: mobile
 * clients are expected to be logged in).
 */
class BookingController extends Controller
{
    public function index(Request $request, ProgramLookupContract $programs): JsonResponse
    {
        $paginated = Booking::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(min((int) $request->query('per_page', 20), 50));

        $programsById = $programs->findMany(
            $paginated->getCollection()->pluck('program_id')->filter()->unique()->all()
        );

        return response()->json([
            'data' => $paginated->getCollection()
                ->map(fn (Booking $booking) => (new BookingResource($booking, $programsById))->resolve())
                ->all(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, string $id, ProgramLookupContract $programs): JsonResponse
    {
        $booking = $this->ownedBooking($request, $id);

        $program = $booking->program_id ? $programs->find($booking->program_id) : null;

        return response()->json([
            'data' => (new BookingResource($booking, $program ? [$program['id'] => $program] : []))->resolve(),
        ]);
    }

    public function store(
        Request $request,
        BookingCreationService $bookings,
        ProgramLookupContract $programs,
        CohortLookupContract $cohorts,
    ): JsonResponse {
        $data = $request->validate([
            'program_id' => 'required|string',
            'cohort_id' => 'nullable|string',
            'full_name' => 'required|string|min:2',
            'email' => 'required|email',
            'phone' => 'required|string|min:9',
            'county' => 'required|string',
            'constituency' => 'required|string',
            'payment_method' => 'required|string|in:mpesa,bank',
            'id_number' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string',
            'nationality' => 'nullable|string',
            'address' => 'nullable|string',
            'current_occupation' => 'nullable|string',
            'employer' => 'nullable|string',
            'education_level' => 'nullable|string',
            'referral_source' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'special_requirements' => 'nullable|string',
        ]);

        $program = $programs->find($data['program_id']);

        abort_if(! $program || ! $program['is_published'] || ! $program['is_active'], Response::HTTP_UNPROCESSABLE_ENTITY, 'Program is not available for booking.');

        $cohort = null;

        if (! empty($data['cohort_id'])) {
            $cohort = $cohorts->find($data['cohort_id']);
            abort_if(! $cohort || $cohort['program_id'] !== $program['id'], Response::HTTP_UNPROCESSABLE_ENTITY, 'Cohort does not belong to this program.');
        }

        $booking = $bookings->create([
            'program_id' => $program['id'],
            'cohort_id' => $cohort['id'] ?? null,
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'id_number' => $data['id_number'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'gender' => $data['gender'] ?? null,
            'nationality' => $data['nationality'] ?? null,
            'address' => $data['address'] ?? null,
            'county' => $data['county'],
            'constituency' => $data['constituency'],
            'current_occupation' => $data['current_occupation'] ?? null,
            'employer' => $data['employer'] ?? null,
            'education_level' => $data['education_level'] ?? null,
            'referral_source' => $data['referral_source'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            'special_requirements' => $data['special_requirements'] ?? null,
            'consent_given' => false,
            'user_id' => $request->user()->id,
            'total_amount' => $cohort['price'] ?? $program['price'] ?? null,
            'currency' => $program['currency'] ?? 'KES',
            'payment_method' => $data['payment_method'],
        ]);

        return response()->json(['data' => (new BookingResource($booking))->resolve()], Response::HTTP_CREATED);
    }

    public function pay(Request $request, string $id, MpesaPaymentContract $mpesa): JsonResponse
    {
        $booking = $this->ownedBooking($request, $id);

        $data = $request->validate([
            'phone' => 'required|string|min:9',
        ]);

        $booking->update(['payment_method' => 'mpesa']);

        $result = $mpesa->initiateForBooking(
            bookingId: $booking->id,
            phone: $data['phone'],
            amount: (string) ($booking->total_amount ?? 0),
            accountReference: $booking->booking_number,
            description: 'Kazilink Academy Booking '.$booking->booking_number,
        );

        return response()->json(['data' => $result]);
    }

    public function confirm(Request $request, string $id, BookingCreationService $bookings): JsonResponse
    {
        $booking = $this->ownedBooking($request, $id);

        $data = $request->validate([
            'consent_given' => 'accepted',
            'payment_reference' => [
                $booking->payment_method === 'mpesa' ? 'required' : 'nullable',
                'string',
                'min:6',
                'max:12',
                'regex:/^[A-Za-z0-9\s]+$/',
            ],
        ]);

        $booking = $bookings->finalize($booking, [
            'consent_given' => true,
            'payment_reference' => isset($data['payment_reference'])
                ? strtoupper(preg_replace('/\s+/', '', $data['payment_reference']))
                : null,
        ]);

        return response()->json(['data' => (new BookingResource($booking))->resolve()]);
    }

    /**
     * 404 (not 403) for someone else's booking — doesn't confirm to a
     * caller whether the id exists at all.
     */
    private function ownedBooking(Request $request, string $id): Booking
    {
        return Booking::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
    }
}
