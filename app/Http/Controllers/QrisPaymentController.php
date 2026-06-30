<?php

namespace App\Http\Controllers;

use App\Models\JenisZiswaf;
use App\Models\KategoriZakat;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Mengelola pembayaran ZISWAF via QRIS Midtrans (mode Sandbox untuk testing).
 *
 * Alur:
 *  1. createQris()  -> buat transaksi lokal (status Diproses) + minta QR ke Midtrans.
 *  2. Frontend menampilkan QR, lalu polling checkStatus() tiap beberapa detik.
 *  3. checkStatus() -> tanya status ke Midtrans; jika lunas, terbitkan kwitansi.
 *
 * Catatan: Webhook (PaymentWebhookController) tetap menjadi sumber kebenaran
 * di produksi. Polling di sini berguna saat berjalan di localhost yang tidak
 * bisa dijangkau notifikasi Midtrans.
 */
class QrisPaymentController extends Controller
{
    /**
     * Buat transaksi QRIS baru dan kembalikan data QR Code untuk ditampilkan.
     */
    public function createQris(Request $request)
    {
        $validated = $request->validate([
            'jenis_ziswaf' => ['required', 'string'],
            'nominal' => ['required', 'integer', 'min:10000'],
            'lembaga' => ['required', 'string'],
            'kategori_harta' => ['nullable', 'string'],
        ]);

        $jenis = JenisZiswaf::firstOrCreate(['nama_jenis' => $validated['jenis_ziswaf']]);

        $kategoriId = null;
        if ($validated['jenis_ziswaf'] === 'Zakat' && ! empty($validated['kategori_harta'])) {
            $kategoriId = KategoriZakat::firstOrCreate(['nama_kategori' => $validated['kategori_harta']])->id;
        }

        $payment = Payment::create([
            'metode' => 'QRIS',
            'status_payment' => 'Pending',
            'tanggal_payment' => now(),
        ]);

        $trxCode = 'TRX-' . date('ym') . '-' . strtoupper(Str::random(4));

        $transaction = Transaction::create([
            'transaction_code' => $trxCode,
            'user_id' => auth()->id(),
            'jenis_ziswaf_id' => $jenis->id,
            'kategori_zakat_id' => $kategoriId,
            'payment_id' => $payment->id,
            'amount' => $validated['nominal'],
            'organization' => $validated['lembaga'],
            'status' => 'Diproses',
        ]);

        try {
            $qris = app(MidtransService::class)->chargeQris(
                $trxCode,
                (int) $validated['nominal'],
                [
                    'name' => auth()->user()->name ?? null,
                    'email' => auth()->user()->email ?? null,
                    'phone' => auth()->user()->no_hp ?? null,
                ]
            );
        } catch (Throwable $e) {
            // Bersihkan transaksi gagal agar tidak menyisakan data "Diproses" tanpa QR.
            $transaction->delete();
            $payment->delete();

            Log::error('Gagal membuat QRIS Midtrans', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        // Simpan ID transaksi Midtrans sebagai referensi.
        $payment->update(['reference_id' => $qris['transaction_id']]);

        return response()->json([
            'success' => true,
            'order_id' => $trxCode,
            'qr_url' => $qris['qr_url'],
            'qr_string' => $qris['qr_string'],
            'expiry_time' => $qris['expiry_time'],
            'amount' => (int) $validated['nominal'],
            'transaction_status' => $qris['transaction_status'],
        ]);
    }

    /**
     * Cek status pembayaran QRIS ke Midtrans dan sinkronkan ke database lokal.
     * Dipanggil berulang (polling) oleh frontend hingga lunas/kadaluarsa.
     */
    public function checkStatus(string $orderId)
    {
        $transaction = Transaction::with(['payment', 'user', 'jenisZiswaf'])
            ->where('transaction_code', $orderId)
            ->first();

        if (! $transaction) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        // Jika sudah selesai sebelumnya, langsung kembalikan tanpa memanggil API lagi.
        if ($transaction->status === 'Tersalur') {
            return $this->statusResponse($transaction, 'settlement');
        }

        try {
            $status = app(MidtransService::class)->getStatus($orderId);
        } catch (Throwable $e) {
            Log::error('Gagal cek status QRIS', ['order_id' => $orderId, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi Midtrans',
            ], 502);
        }

        $trxStatus = $status['transaction_status'];
        $fraud = $status['fraud_status'] ?? 'accept';

        if (in_array($trxStatus, ['capture', 'settlement'], true) && $fraud === 'accept') {
            $transaction->tandaiBerhasil(); // terbitkan kwitansi + notifikasi
            $transaction->refresh();
        } elseif (in_array($trxStatus, ['expire', 'cancel', 'deny'], true)) {
            $transaction->update(['status' => 'Batal']);
            $transaction->payment?->update(['status_payment' => 'Failed']);
        }

        return $this->statusResponse($transaction, $trxStatus);
    }

    private function statusResponse(Transaction $transaction, string $midtransStatus)
    {
        return response()->json([
            'success' => true,
            'transaction_status' => $midtransStatus,
            'order_id' => $transaction->transaction_code,
            'status' => $transaction->status, // Diproses | Tersalur | Batal
            'paid' => $transaction->status === 'Tersalur',
            'kwitansi_number' => $transaction->kwitansi_number,
            'date' => $transaction->created_at->format('d M Y'),
        ]);
    }
}
