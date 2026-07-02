<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Laporan;
use App\Models\RekeningBMT;
use App\Models\KategoriZakat;
use App\Models\HargaHarta;
use App\Models\HargaEmas;
use App\Models\JenisHarta;
use App\Models\JenisZiswaf;
use App\Models\LembagaSetting;
use App\Models\User;
use App\Notifications\ZiswafNotification;
use Illuminate\Support\Facades\Hash;

// Tambahan untuk Export
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use App\Exports\AnggotaExport;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function verifikasiTransaksi($id)
    {
        $trx = Transaction::findOrFail($id);

        $trx->update([
            'status' => 'Menunggu Penyaluran',
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

    public function simpanPenyaluran(Request $request, $id)
    {
        $request->validate([
            'bukti_penyaluran' => 'required|file|mimes:jpg,jpeg,png,mp4|max:15360',
            'keterangan_penyaluran' => 'nullable|string'
        ]);

        $trx = Transaction::findOrFail($id);

        if ($request->hasFile('bukti_penyaluran')) {
            $file = $request->file('bukti_penyaluran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/penyaluran'), $filename);
            $trx->bukti_penyaluran = 'uploads/penyaluran/' . $filename;
        }

        $trx->keterangan_penyaluran = $request->keterangan_penyaluran;
        $trx->tanggal_penyaluran = now();
        $trx->status = 'Tersalur';
        $trx->save();

        return redirect()->back()->with('success', 'Bukti penyaluran berhasil diunggah dan diteruskan ke Nasabah!');
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

    public function exportExcel()
    {
        $namaFile = 'Laporan_ZISWAF_' . date('Y_m') . '.xlsx';
        return Excel::download(new LaporanExport, $namaFile);
    }

    public function exportPDF()
    {
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
        $jenisHarta = JenisHarta::all();
        $hargaHarta = HargaHarta::with('jenisHarta')->latest('tanggal')->get();
        $lembaga = LembagaSetting::first() ?? new LembagaSetting(['nama_lembaga' => 'BMT Pondok Hijau']);
        return view('pages.pengaturan', compact('rekeningBMT', 'kategoriZakat', 'jenisHarta', 'hargaHarta', 'lembaga'));
    }

    public function updateLembaga(Request $request)
    {
        $request->validate([
            'nama_lembaga' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $lembaga = LembagaSetting::first() ?? new LembagaSetting();
        $lembaga->fill($request->only('nama_lembaga', 'alamat', 'email'));
        $lembaga->save();

        return redirect()->back()->with('success', 'Profil lembaga berhasil disimpan!');
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

    public function storeJenisHarta(Request $request) {
        $request->validate([
            'nama_jenis_harta' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);
        JenisHarta::create($request->only('nama_jenis_harta', 'deskripsi'));
        return redirect()->back()->with('success', 'Jenis Harta berhasil ditambahkan!');
    }

    public function destroyJenisHarta($id) {
        JenisHarta::destroy($id);
        return redirect()->back()->with('success', 'Jenis Harta berhasil dihapus!');
    }

    public function storeHargaHarta(Request $request) {
        $request->validate([
            'jenis_harta_id' => 'required|exists:jenis_harta,id',
            'tanggal' => 'required|date',
            'harga' => 'required|numeric|min:0',
        ]);
        HargaHarta::create($request->only('jenis_harta_id', 'tanggal', 'harga'));
        return redirect()->back()->with('success', 'Harga Harta berhasil ditambahkan!');
    }

    public function destroyHargaHarta($id) {
        HargaHarta::destroy($id);
        return redirect()->back()->with('success', 'Harga Harta berhasil dihapus!');
    }

    // ==========================================
    // Data Anggota
    // ==========================================
    public function anggota(Request $request)
    {
        $query = User::where('role', 'nasabah')
            ->withCount('transactions')
            ->withSum(['transactions as total_disetor' => function ($q) {
                $q->where('status', 'Tersalur');
            }], 'amount');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $anggota = $query->latest()->get();
        $nisabPerak = $this->nisabPerak();

        return view('pages.anggota', compact('anggota', 'nisabPerak'));
    }

    public function storeAnggota(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'no_hp' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'role' => 'nasabah',
        ]);

        return redirect()->back()->with('success', 'Anggota baru berhasil ditambahkan!');
    }

    public function updateAnggota(Request $request, $id)
    {
        $request->validate([
            'saldo' => 'nullable|numeric|min:0',
            'haul_terpenuhi' => 'nullable|boolean',
        ]);

        $anggota = User::where('role', 'nasabah')->findOrFail($id);
        $anggota->update([
            'saldo' => $request->saldo,
            'haul_terpenuhi' => $request->boolean('haul_terpenuhi'),
        ]);

        return redirect()->back()->with('success', 'Data anggota berhasil diperbarui!');
    }

    public function exportAnggota()
    {
        return Excel::download(new AnggotaExport, 'Data_Anggota_ZISWAF_' . date('Y_m_d') . '.xlsx');
    }

    // ==========================================
    // Auto-Deduction
    // ==========================================
    public function autoDeduction()
    {
        $nisabPerak = $this->nisabPerak();
        $persentase = $this->zakatPersentase();

        $anggotaWajib = User::where('role', 'nasabah')
            ->whereNotNull('saldo')
            ->where('saldo', '>=', $nisabPerak)
            ->where('haul_terpenuhi', true)
            ->get();

        return view('pages.auto-deduction', compact('anggotaWajib', 'nisabPerak', 'persentase'));
    }

    public function runAutoDeduction()
    {
        $persentase = $this->zakatPersentase();
        $nisabPerak = $this->nisabPerak();

        $anggotaWajib = User::where('role', 'nasabah')
            ->whereNotNull('saldo')
            ->where('saldo', '>=', $nisabPerak)
            ->where('haul_terpenuhi', true)
            ->get();

        foreach ($anggotaWajib as $anggota) {
            $this->buatTransaksiZakatOtomatis($anggota, $anggota->saldo * $persentase);
        }

        return redirect()->back()->with('success', "Auto-Deduction berhasil dijalankan untuk {$anggotaWajib->count()} anggota!");
    }

    public function potongZakat($userId)
    {
        $anggota = User::where('role', 'nasabah')->findOrFail($userId);
        $persentase = $this->zakatPersentase();

        $this->buatTransaksiZakatOtomatis($anggota, ($anggota->saldo ?? 0) * $persentase);

        return redirect()->back()->with('success', "Zakat {$anggota->name} berhasil dipotong!");
    }

    private function buatTransaksiZakatOtomatis(User $user, float $amount): void
    {
        $jenis = JenisZiswaf::firstOrCreate(['nama_jenis' => 'Zakat']);

        $payment = \App\Models\Payment::create([
            'metode' => 'Potong Tabungan',
            'status_payment' => 'Success',
            'tanggal_payment' => now(),
        ]);

        $trxCode = 'TRX-' . date('ym') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
        $kwitansiNumber = 'KW-' . date('Y') . '-' . rand(1000, 9999);

        Transaction::create([
            'transaction_code' => $trxCode,
            'user_id' => $user->id,
            'jenis_ziswaf_id' => $jenis->id,
            'payment_id' => $payment->id,
            'amount' => $amount,
            'organization' => 'BMT Pondok Hijau',
            'status' => 'Tersalur',
            'kwitansi_number' => $kwitansiNumber,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $user->notify(new ZiswafNotification(
            "Zakat Anda sebesar Rp " . number_format($amount, 0, ',', '.') . " berhasil dipotong otomatis dari saldo tabungan. Kwitansi: " . $kwitansiNumber
        ));
    }

    private function nisabPerak(): float
    {
        $hargaEmasTerkini = HargaEmas::latest('tanggal')->first();
        $hargaPerGram = $hargaEmasTerkini ? $hargaEmasTerkini->harga_emas_per_gram : 2673000;
        $hargaPerak = $hargaPerGram * 0.0197;

        return 595 * $hargaPerak;
    }

    private function zakatPersentase(): float
    {
        $kategori = KategoriZakat::where('nama_kategori', 'like', '%Emas%')->first();

        return $kategori ? ($kategori->persentase / 100) : 0.025;
    }
}
