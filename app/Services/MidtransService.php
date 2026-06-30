<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Pembungkus tipis Core API Midtrans khusus untuk pembayaran QRIS.
 *
 * Menggunakan HTTP Client bawaan Laravel (tanpa SDK tambahan) sehingga
 * mudah dipakai untuk pembuktian/dummy di lingkungan Sandbox.
 *
 * Dokumentasi: https://docs.midtrans.com/reference/qris
 */
class MidtransService
{
    public function __construct()
    {
        if (empty($this->serverKey())) {
            throw new RuntimeException(
                'MIDTRANS_SERVER_KEY belum diisi. Tambahkan kredensial Sandbox Midtrans di file .env.'
            );
        }
    }

    private function serverKey(): ?string
    {
        return config('services.midtrans.server_key');
    }

    private function isProduction(): bool
    {
        return (bool) config('services.midtrans.is_production', false);
    }

    /**
     * Base URL Core API. Sandbox untuk testing, Production untuk live.
     */
    private function baseUrl(): string
    {
        return $this->isProduction()
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    /**
     * Header autentikasi Basic Auth: base64(server_key + ':').
     */
    private function request()
    {
        return Http::withBasicAuth($this->serverKey(), '')
            ->acceptJson()
            ->asJson()
            ->timeout(30);
    }

    /**
     * Membuat transaksi QRIS baru di Midtrans.
     *
     * @param  string  $orderId      Kode unik transaksi (= transaction_code kita).
     * @param  int     $grossAmount  Nominal dalam Rupiah (bilangan bulat).
     * @param  array   $customer     Data opsional: name, email, phone.
     * @return array{transaction_id:string,order_id:string,qr_url:?string,qr_string:?string,expiry_time:?string,transaction_status:string,raw:array}
     */
    public function chargeQris(string $orderId, int $grossAmount, array $customer = []): array
    {
        $payload = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'qris' => [
                'acquirer' => config('services.midtrans.qris_acquirer', 'gopay'),
            ],
        ];

        if (! empty($customer)) {
            $payload['customer_details'] = array_filter([
                'first_name' => $customer['name'] ?? null,
                'email' => $customer['email'] ?? null,
                'phone' => $customer['phone'] ?? null,
            ]);
        }

        $response = $this->request()->post($this->baseUrl() . '/v2/charge', $payload);

        $data = $response->json() ?? [];

        // status_code Midtrans "201" = transaksi berhasil dibuat (pending menunggu scan).
        if ($response->failed() || ! in_array(($data['status_code'] ?? null), ['200', '201'], true)) {
            Log::error('Midtrans chargeQris gagal', ['order_id' => $orderId, 'response' => $data]);
            throw new RuntimeException(
                $data['status_message'] ?? 'Gagal membuat transaksi QRIS di Midtrans.'
            );
        }

        return [
            'transaction_id' => $data['transaction_id'] ?? '',
            'order_id' => $data['order_id'] ?? $orderId,
            'qr_url' => $this->extractQrUrl($data),
            'qr_string' => $data['qr_string'] ?? null,
            'expiry_time' => $data['expiry_time'] ?? null,
            'transaction_status' => $data['transaction_status'] ?? 'pending',
            'raw' => $data,
        ];
    }

    /**
     * Mengecek status terbaru sebuah transaksi di Midtrans.
     *
     * @return array{transaction_status:string,fraud_status:?string,raw:array}
     */
    public function getStatus(string $orderId): array
    {
        $response = $this->request()->get($this->baseUrl() . '/v2/' . $orderId . '/status');
        $data = $response->json() ?? [];

        return [
            'transaction_status' => $data['transaction_status'] ?? 'unknown',
            'fraud_status' => $data['fraud_status'] ?? null,
            'raw' => $data,
        ];
    }

    /**
     * URL gambar QR Code ada di dalam array "actions" (name = generate-qr-code).
     */
    private function extractQrUrl(array $data): ?string
    {
        foreach ($data['actions'] ?? [] as $action) {
            if (($action['name'] ?? '') === 'generate-qr-code') {
                return $action['url'] ?? null;
            }
        }

        return null;
    }
}
