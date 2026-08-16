<?php

namespace Modules\Booking\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Booking\Models\Booking;
use Symfony\Component\HttpFoundation\Response;

class BookingDocumentController extends Controller
{
    public function show(Booking $booking, string $document)
    {
        abort_unless(auth()->user()?->can('view', $booking), Response::HTTP_FORBIDDEN);

        $path = $booking->documents_urls[$document.'_path'] ?? null;

        abort_if(! $path || ! str_starts_with($path, 'booking-id-documents/'), Response::HTTP_NOT_FOUND);
        abort_unless(Storage::disk('local')->exists($path), Response::HTTP_NOT_FOUND);

        return Storage::disk('local')->response($path);
    }
}
