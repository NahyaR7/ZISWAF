<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function indexAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard-user');
        }

        $recentTransactions = Transaction::latest()->take(5)->get();

        $totalZakat = $this->sumByJenis(['Zakat']);
        $totalInfaq = $this->sumByJenis(['Infaq', 'Sedekah']);
        $totalWakaf = $this->sumByJenis(['Wakaf']);

        $totalAnggota = User::where('role', 'nasabah')->count();
        $anggotaBaruBulanIni = User::where('role', 'nasabah')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('pages.dashboard', [
            'recentTransactions' => $recentTransactions,
            'totalZakat' => $totalZakat,
            'totalInfaq' => $totalInfaq,
            'totalWakaf' => $totalWakaf,
            'totalAnggota' => $totalAnggota,
            'anggotaBaruBulanIni' => $anggotaBaruBulanIni,
            'perubahanZakat' => $this->perubahanBulanan(['Zakat']),
            'perubahanInfaq' => $this->perubahanBulanan(['Infaq', 'Sedekah']),
            'perubahanWakaf' => $this->perubahanBulanan(['Wakaf']),
        ]);
    }

    public function indexUser()
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('dashboard');
        }

        $userId = auth()->id();

        $transactions = Transaction::where('user_id', $userId)->latest()->take(4)->get();

        $totalZakatSaya = Transaction::where('user_id', $userId)
            ->whereHas('jenisZiswaf', fn ($q) => $q->where('nama_jenis', 'Zakat'))
            ->where('status', 'Tersalur')
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $totalInfaqSaya = Transaction::where('user_id', $userId)
            ->whereHas('jenisZiswaf', fn ($q) => $q->whereIn('nama_jenis', ['Infaq', 'Sedekah']))
            ->where('status', 'Tersalur')
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $totalTransaksiSaya = Transaction::where('user_id', $userId)->count();
        $transaksiTerakhir = Transaction::where('user_id', $userId)->latest()->first();

        return view('pages.dashboard-user', compact(
            'transactions', 'totalZakatSaya', 'totalInfaqSaya', 'totalTransaksiSaya', 'transaksiTerakhir'
        ));
    }

    private function sumByJenis(array $namaJenis): float
    {
        return (float) Transaction::whereHas('jenisZiswaf', fn ($q) => $q->whereIn('nama_jenis', $namaJenis))
            ->where('status', 'Tersalur')
            ->sum('amount');
    }

    private function perubahanBulanan(array $namaJenis): ?float
    {
        $bulanIni = Transaction::whereHas('jenisZiswaf', fn ($q) => $q->whereIn('nama_jenis', $namaJenis))
            ->where('status', 'Tersalur')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $bulanLalu = Transaction::whereHas('jenisZiswaf', fn ($q) => $q->whereIn('nama_jenis', $namaJenis))
            ->where('status', 'Tersalur')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');

        if ($bulanLalu <= 0) {
            return null;
        }

        return round((($bulanIni - $bulanLalu) / $bulanLalu) * 100);
    }
}
