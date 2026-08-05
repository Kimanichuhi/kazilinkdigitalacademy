<?php

namespace Modules\Payment\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Payment\Contracts\MpesaPaymentContract;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function status(string $checkoutRequestId, MpesaPaymentContract $mpesa): JsonResponse
    {
        $status = $mpesa->status($checkoutRequestId);

        abort_if(! $status, Response::HTTP_NOT_FOUND);

        return response()->json(['data' => $status]);
    }
}
