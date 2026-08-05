<?php

namespace Modules\Payment\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Thin client for Safaricom's Daraja API (STK push / "Lipa na M-Pesa
 * Online"). Every call is wrapped by callers in try/catch per ISOLATION
 * RULES — a Daraja outage or missing credentials must never fail a booking.
 */
class DarajaClient
{
    public function configured(): bool
    {
        return filled(config('payment.mpesa.consumer_key'))
            && filled(config('payment.mpesa.consumer_secret'))
            && filled(config('payment.mpesa.shortcode'))
            && filled(config('payment.mpesa.passkey'));
    }

    /**
     * @return array{merchant_request_id: ?string, checkout_request_id: ?string, response_code: ?string, response_description: ?string}
     */
    public function stkPush(string $phone, string $amount, string $accountReference, string $description, string $callbackUrl): array
    {
        $shortcode = (string) config('payment.mpesa.shortcode');
        $timestamp = now()->format('YmdHis');

        $response = Http::withToken($this->accessToken())
            ->post($this->baseUrl().'/mpesa/stkpush/v1/processrequest', [
                'BusinessShortCode' => $shortcode,
                'Password' => $this->password($shortcode, $timestamp),
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => max(1, (int) round((float) $amount)),
                'PartyA' => $this->normalizePhone($phone),
                'PartyB' => $shortcode,
                'PhoneNumber' => $this->normalizePhone($phone),
                'CallBackURL' => $callbackUrl,
                'AccountReference' => $accountReference,
                'TransactionDesc' => $description,
            ])
            ->throw();

        return [
            'merchant_request_id' => $response->json('MerchantRequestID'),
            'checkout_request_id' => $response->json('CheckoutRequestID'),
            'response_code' => $response->json('ResponseCode'),
            'response_description' => $response->json('ResponseDescription'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function stkQuery(string $checkoutRequestId): array
    {
        $shortcode = (string) config('payment.mpesa.shortcode');
        $timestamp = now()->format('YmdHis');

        $response = Http::withToken($this->accessToken())
            ->post($this->baseUrl().'/mpesa/stkpushquery/v1/query', [
                'BusinessShortCode' => $shortcode,
                'Password' => $this->password($shortcode, $timestamp),
                'Timestamp' => $timestamp,
                'CheckoutRequestID' => $checkoutRequestId,
            ])
            ->throw();

        return $response->json() ?? [];
    }

    protected function baseUrl(): string
    {
        return config('payment.mpesa.env') === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    protected function password(string $shortcode, string $timestamp): string
    {
        return base64_encode($shortcode.config('payment.mpesa.passkey').$timestamp);
    }

    protected function accessToken(): string
    {
        return Cache::remember('mpesa:access_token:'.config('payment.mpesa.env'), 3500, function () {
            $response = Http::withBasicAuth(
                (string) config('payment.mpesa.consumer_key'),
                (string) config('payment.mpesa.consumer_secret'),
            )->get($this->baseUrl().'/oauth/v1/generate', ['grant_type' => 'client_credentials'])
                ->throw();

            return (string) $response->json('access_token');
        });
    }

    protected function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            return '254'.substr($digits, 1);
        }

        if (str_starts_with($digits, '7') || str_starts_with($digits, '1')) {
            return '254'.$digits;
        }

        return $digits;
    }
}
