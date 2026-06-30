<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Laporan;
use App\Models\RekeningBMT;
use App\Models\KategoriZakat;
use App\Models\HargaHarta;
use App\Notifications\ZiswafNotification;

// Tambahan untuk Export
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function verifikasiTransaksi($id)
    {
        $trx = Transaction::findOrFail($id);
        
        $trx->update([
            'status' => 'Tersalur',
            'kwitansi_number' => 'KW-' . date('Y') . '-' . rand(1000, 9999),
            'verified_by' => auth()->id(),
            'verified_at' => now()
        ]);
        
        if($trx->payment) {
            $trx->payment->update(['status_payment' => 'Success']);
        }

        $trx->user->notify(new ZiswafNotification("Pembayaran {$trx->jenisZiswaf->nama_jenis} Rp " . number_format($trx->amount, 0, ',', '.') . " telah diverifikasi. Kwitansi: " . $trx->kwitansi_number));

        return redirect()->back()->with('success', 'Transaksi berhasil diverifikasi dan kwitansi diterbitkan!');
    }

    public function laporan()
    {
        $transactions = Transaction::latest()->get();
        $riwayatLaporan = Laporan::with('admin')->latest()->get();

        $totalPenerimaan = Transaction::where('status', 'Tersalur')->sum('amount');
        $totalZakat = Transaction::whereHas('jenisZiswaf', function($q) { $q->where('nama_jenis', 'Zakat'); })->where('status', 'Tersalur')->sum('amount');
        $totalInfaq = Transaction::whereHas('jenisZiswaf', function($q) { $q->where('nama_jenis', 'Infaq')->orWhere('nama_jenis', 'Sedekah'); })->where('status', 'Tersalur')->sum('amount');
        $totalWakaf = Transaction::whereHas('jenisZiswaf', function($q) { $q->where('nama_jenis', 'Wakaf'); })->where('status', 'Tersalur')->sum('amount');

        $pctZakat = $totalPenerimaan > 0 ? round(($totalZakat / $totalPenerimaan) * 100) : 0;
        $pctInfaq = $totalPenerimaan > 0 ? round(($totalInfaq / $totalPenerimaan) * 100) : 0;
        $pctWakaf = $totalPenerimaan > 0 ? round(($totalWakaf / $totalPenerimaan) * 100) : 0;

        return view('pages.laporan', compact(
            'transactions', 'riwayatLaporan', 
            'totalPenerimaan', 'totalZakat', 'totalInfaq', 'totalWakaf', 'pctZakat', 'pctInfaq', 'pctWakaf'
        ));
    }

    public function generateLaporan()
    {
        $bulanIni = now()->translatedFormat('F Y');
        $totalTransaksiBulanIni = Transaction::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->where('status', 'Tersalur')->sum('amount');

        $cekLaporan = Laporan::where('periode', $bulanIni)->first();

        if ($cekLaporan) {
            $cekLaporan->update([
                'tanggal_generate' => now(),
                'total_transaksi' => $totalTransaksiBulanIni,
                'generated_by' => auth()->id()
            ]);
            $pesan = 'Laporan bulan ' . $bulanIni . ' berhasil diperbarui!';
        } else {
            Laporan::create([
                'periode' => $bulanIni,
                'tanggal_generate' => now(),
                'total_transaksi' => $totalTransaksiBulanIni,
                'generated_by' => auth()->id()
            ]);
            $pesan = 'Laporan bulan ' . $bulanIni . ' berhasil di-generate!';
        }

        return redirect()->back()->with('success', $pesan);
    }

    // ==========================================
    // FUNGSI BARU: EXPORT EXCEL & PDF
    // ==========================================
    public function exportExcel()
    {
        $namaFile = 'Laporan_ZISWAF_' . date('Y_m') . '.xlsx';
        return Excel::download(new LaporanExport, $namaFile);
    }

    public function exportPDF()
    {
        // Hanya ambil yang Tersalur
        $transactions = Transaction::with(['user', 'jenisZiswaf', 'payment'])
                        ->where('status', 'Tersalur')
                        ->orderBy('created_at', 'asc')
                        ->get();

        $pdf = Pdf::loadView('pdf.laporan', compact('transactions'));
        return $pdf->download('Laporan_ZISWAF_' . date('Y_m') . '.pdf');
    }

    public function pengaturan()
    {
        $rekeningBMT = RekeningBMT::all();
        $kategoriZakat = KategoriZakat::all();
        $hargaHarta = HargaHarta::with('jenisHarta')->latest('tanggal')->get();
        return view('pages.pengaturan', compact('rekeningBMT', 'kategoriZakat', 'hargaHarta'));
    }

    public function storeRekening(Request $request) {
        RekeningBMT::create(['nama_bank' => $request->nama_bank, 'no_rekening' => $request->no_rekening, 'atas_nama' => $request->atas_nama]);
        return redirect()->back()->with('success', 'Rekening BMT berhasil ditambahkan!');
    }

    public function destroyRekening($id) {
        RekeningBMT::destroy($id);
        return redirect()->back()->with('success', 'Rekening BMT berhasil dihapus!');
    }

    public function storeKategori(Request $request) {
        KategoriZakat::create(['nama_kategori' => $request->nama_kategori, 'nisab' => $request->nisab, 'persentase' => $request->persentase]);
        return redirect()->back()->with('success', 'Kategori Zakat berhasil ditambahkan!');
    }

    public function destroyKategori($id) {
        KategoriZakat::destroy($id);
        return redirect()->back()->with('success', 'Kategori Zakat berhasil dihapus!');
    }
<<<<<<< HEAD

    // ==========================================
    // UNGGAH BUKTI PENYALURAN (FOTO/VIDEO)
    // ==========================================
    public function uploadPenyaluran(Request $request, $id)
    {
        $request->validate([
            'bukti_penyaluran' => 'required|file|mimes:jpg,jpeg,png,mp4|max:10240', // Max 10MB
            'keterangan_penyaluran' => 'required|string'
        ]);

        $trx = Transaction::findOrFail($id);

        if ($request->hasFile('bukti_penyaluran')) {
            $file = $request->file('bukti_penyaluran');
            $filename = time() . '_' . $file->getClientOriginalName();
            // Menyimpan file ke folder public/uploads/penyaluran
            $file->move(public_path('uploads/penyaluran'), $filename);
            
            $trx->bukti_penyaluran = 'uploads/penyaluran/' . $filename;
        }

        $trx->keterangan_penyaluran = $request->keterangan_penyaluran;
        $trx->tanggal_penyaluran = now();
        $trx->save();

        return redirect()->back()->with('success', 'Bukti penyaluran berhasil diunggah dan diteruskan ke Nasabah!');
    }
=======
>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
}