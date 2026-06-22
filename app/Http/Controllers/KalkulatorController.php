<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HargaEmas;
use App\Models\KategoriZakat;

class KalkulatorController extends Controller
{
    public function index()
    {
        $hargaEmasTerkini = HargaEmas::latest('tanggal')->first();
        // Fallback diubah menjadi standar 2026 yang akurat
        $hargaPerGram = $hargaEmasTerkini ? $hargaEmasTerkini->harga_emas_per_gram : 2673000;
        $nisabEmas = 85 * $hargaPerGram;

        // Hitung harga perak dan nisabnya secara dinamis (asumsi perak ~1.97% dari emas)
        $hargaPerak = $hargaPerGram * 0.0197;
        $nisabPerak = 595 * $hargaPerak;

        return view('pages.kalkulator', compact('hargaPerGram', 'nisabEmas', 'hargaPerak', 'nisabPerak'));
    }

    public function hitung(Request $request)
    {
        $saldo = floatval($request->saldo ?? 0);
        $hutang = floatval($request->hutang ?? 0);
        $haul = $request->haul; 
        $type = $request->type; 

        $hartaBersih = $saldo - $hutang;

        $hargaEmas = HargaEmas::latest('tanggal')->first();
        // Fallback diubah menjadi standar 2026 yang akurat
        $hargaPerGram = $hargaEmas ? $hargaEmas->harga_emas_per_gram : 2673000;
        
        $nisabEmas = 85 * $hargaPerGram;
        // Hitung dinamis juga untuk perak
        $hargaPerak = $hargaPerGram * 0.0197;
        $nisabPerak = 595 * $hargaPerak; 

        $nisab = ($type === 'penghasilan') ? $nisabPerak : $nisabEmas;
        $wajib = ($hartaBersih >= $nisab && $haul === 'ya');
        
        $kategori = KategoriZakat::where('nama_kategori', 'like', '%Emas%')->first();
        $persentase = $kategori ? ($kategori->persentase / 100) : 0.025; 

        $zakat = $wajib ? ($hartaBersih * $persentase) : 0;

        return response()->json([
            'bersih' => $hartaBersih,
            'nisab'  => $nisab,
            'wajib'  => $wajib,
            'zakat'  => $zakat,
            'haul'   => $haul
        ]);
    }

    // FUNGSI Endpoint khusus untuk AJAX Polling Live Data
    public function liveHarga()
    {
        $hargaEmasTerkini = HargaEmas::latest('tanggal')->first();
        // Fallback diubah menjadi standar 2026 yang akurat
        $hargaEmas = $hargaEmasTerkini ? $hargaEmasTerkini->harga_emas_per_gram : 2673000;
        
        $hargaPerak = $hargaEmas * 0.0197; 
        $nisabEmas = 85 * $hargaEmas;
        $nisabPerak = 595 * $hargaPerak;

        return response()->json([
            'emas_per_gram' => $hargaEmas,
            'perak_per_gram' => $hargaPerak,
            'nisab_emas' => $nisabEmas,
            'nisab_perak' => $nisabPerak
        ]);
    }
}