<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Notifications\ZiswafNotification;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Endpoint ini akan ditembak secara rahasia oleh server Midtrans
     * setiap kali ada perubahan status pembayaran (berhasil/gagal/pending).
     */
    public function handleMidtrans(Request $request)
    {
        // 1. Ambil payload (data JSON) yang dikirim oleh Midtrans
        $payload = $request->all();

        // Mencatat log untuk keperluan audit jejak digital
        Log::info('Midtrans Webhook Diterima:', $payload);

        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $signatureKey = $payload['signature_key'] ?? '';

        // ==========================================
        // 2. VALIDASI KEAMANAN MUTLAK (SHA512)
        // ==========================================
        // Mencocokkan kunci rahasia agar endpoint tidak bisa ditembak oleh hacker
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($expectedSignature !== $signatureKey) {
            Log::error('Keamanan Webhook Gagal: Signature tidak cocok!');
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
        }

        // ==========================================
        // 3. CARI DATA TRANSAKSI DI DATABASE
        // ==========================================
        // order_id dari Midtrans sama dengan transaction_code di database kita
        $transaction = Transaction::with('payment', 'user', 'jenisZiswaf')->where('transaction_code', $orderId)->first();

        if (!$transaction) {
            Log::error('Webhook Error: Transaksi ' . $orderId . ' tidak ditemukan.');
            return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
        }

        // ==========================================
        // 4. UPDATE STATUS BERDASARKAN RESPON SERVER
        // ==========================================
        $transactionStatus = $payload['transaction_status'];
        $fraudStatus = $payload['fraud_status'] ?? 'accept';

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($fraudStatus == 'accept') {
                $this->prosesTransaksiBerhasil($transaction);
            }
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            // Jika nasabah batal bayar / waktu VA habis
            $transaction->update(['status' => 'Batal']);
            $transaction->payment->update(['status_payment' => 'Failed']);
        } else if ($transactionStatus == 'pending') {
            // Jika nasabah baru mendapat kode VA tapi belum transfer
            $transaction->update(['status' => 'Diproses']);
            $transaction->payment->update(['status_payment' => 'Pending']);
        }

        return response()->json(['status' => 'success', 'message' => 'Webhook berhasil diproses']);
    }

    /**
     * Fungsi Privat untuk mengeksekusi penerbitan Kwitansi secara otomatis
     */
    private function prosesTransaksiBerhasil($transaction)
    {
        // Hindari proses ganda jika transaksi sudah berstatus Tersalur
        if ($transaction->status === 'Tersalur') return;

        // Generate Nomor Kwitansi Otomatis
        $kwitansiNumber = 'KW-' . date('Y') . '-' . rand(1000, 9999);

        // Update ke Database
        $transaction->update([
            'status' => 'Tersalur',
            'kwitansi_number' => $kwitansiNumber,
            'verified_at' => now(),
            'verified_by' => null // Null menandakan diverifikasi oleh Sistem/API
        ]);

        $transaction->payment->update(['status_payment' => 'Success']);

        // Kirim Notifikasi Real-time ke Nasabah
        if ($transaction->user) {
            $transaction->user->notify(new ZiswafNotification(
                "Pembayaran instan {$transaction->jenisZiswaf->nama_jenis} Rp " . number_format($transaction->amount, 0, ',', '.') . " BERHASIL divalidasi sistem. Kwitansi: " . $kwitansiNumber
            ));
        }
    }
}