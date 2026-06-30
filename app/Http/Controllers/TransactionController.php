<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\JenisZiswaf;
use App\Models\KategoriZakat;
use App\Notifications\ZiswafNotification;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf; // Pustaka PDF

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $jenis = JenisZiswaf::firstOrCreate(['nama_jenis' => $request->jenis_ziswaf]);
        $kategoriId = null;
        if ($request->jenis_ziswaf === 'Zakat' && $request->filled('kategori_harta')) {
            $kategori = KategoriZakat::firstOrCreate(['nama_kategori' => $request->kategori_harta]);
            $kategoriId = $kategori->id;
        }

        $buktiPath = null;
        if ($request->hasFile('bukti_bayar')) {
            $buktiPath = $request->file('bukti_bayar')->store('bukti_transfer', 'public');
        }

        $payment = Payment::create([
            'metode' => $request->metode,
            'status_payment' => ($request->metode === 'Transfer Bank') ? 'Pending' : 'Success',
            'tanggal_payment' => now(),
        ]);

        $trxCode = 'TRX-' . date('ym') . '-' . strtoupper(Str::random(4));
        $status = ($request->metode === 'Transfer Bank') ? 'Diproses' : 'Tersalur';
        $kwitansiNumber = ($status === 'Tersalur') ? 'KW-' . date('Y') . '-' . rand(1000, 9999) : null;

        $transaction = Transaction::create([
            'transaction_code' => $trxCode,
            'user_id' => auth()->id(),
            'jenis_ziswaf_id' => $jenis->id,
            'kategori_zakat_id' => $kategoriId,
            'payment_id' => $payment->id,
            'amount' => $request->nominal,
            'organization' => $request->lembaga,
            'status' => $status,
            'kwitansi_number' => $kwitansiNumber,
            'bukti_bayar' => $buktiPath,
        ]);

        if ($status === 'Tersalur') {
            auth()->user()->notify(new ZiswafNotification("Pembayaran instan {$jenis->nama_jenis} Rp " . number_format($request->nominal, 0, ',', '.') . " berhasil. Kwitansi: " . $kwitansiNumber));
        }

        return response()->json([
            'success' => true,
            'transaction_code' => $transaction->transaction_code,
            'kwitansi_number' => $transaction->kwitansi_number,
            'status' => $transaction->status,
            'date' => $transaction->created_at->format('d M Y')
        ]);
    }

    // FUNGSI BARU: Generate dan Download PDF
    public function downloadPDF($transaction_code)
    {
        // Cari transaksi berdasarkan kode
        $transaction = Transaction::with(['user', 'jenisZiswaf', 'payment'])
            ->where('transaction_code', $transaction_code)
            ->firstOrFail();

        // Merender view PDF
        $pdf = Pdf::loadView('pdf.kwitansi', compact('transaction'));
        
        // Mengirimkan file ke browser pengguna
        return $pdf->download('Kwitansi_ZISWAF_' . $transaction->transaction_code . '.pdf');
    }
}