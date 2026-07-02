<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KalkulatorController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\QrisPaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MarketplaceController;
use App\Models\Transaction;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');

Route::middleware(['auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'indexAdmin'])->name('dashboard');

    Route::get('/dashboard-user', [DashboardController::class, 'indexUser'])->name('dashboard-user');

    Route::get('/kalkulator', [KalkulatorController::class, 'index'])->name('kalkulator');
    Route::get('/kalkulator/hitung', [KalkulatorController::class, 'hitung'])->name('kalkulator.hitung');
    Route::get('/kalkulator/live-harga', [KalkulatorController::class, 'liveHarga'])->name('kalkulator.live');

    Route::get('/kwitansi', function () {
        $query = Transaction::whereNotNull('kwitansi_number')->latest();
        if (auth()->user()->role !== 'admin') $query->where('user_id', auth()->id());
        $transactions = $query->get();
        return view('pages.kwitansi', compact('transactions'));
    })->name('kwitansi');

    Route::get('/kwitansi/download/{transaction_code}', [TransactionController::class, 'downloadPDF'])->name('kwitansi.download');

    Route::get('/notifikasi/read', function() {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifikasi.read');

    // Nasabah
    Route::get('/bayar', function () { return view('pages.bayar'); })->name('bayar');
    Route::post('/transaksi/store', [TransactionController::class, 'store'])->name('transaksi.store');
    Route::post('/qris/create', [QrisPaymentController::class, 'createQris'])->name('qris.create');
    Route::get('/qris/status/{orderId}', [QrisPaymentController::class, 'checkStatus'])->name('qris.status');

    Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace');
    Route::post('/marketplace/donasi', [MarketplaceController::class, 'donasi'])->name('marketplace.donasi');

    Route::get('/penyaluran', function () {
        $transactions = Transaction::where('user_id', auth()->id())->latest()->get();
        return view('pages.penyaluran', compact('transactions'));
    })->name('penyaluran');

    // Pengaturan: dapat diakses semua role, tapi hanya bagian akun sendiri
    Route::get('/pengaturan', [AdminController::class, 'pengaturan'])->name('pengaturan');
    Route::post('/pengaturan/password', [AccountController::class, 'updatePassword'])->name('account.password');

    // ==========================================
    // Khusus Admin
    // ==========================================
    Route::middleware(['admin'])->group(function () {
        Route::get('/auto-deduction', [AdminController::class, 'autoDeduction'])->name('auto-deduction');
        Route::post('/auto-deduction/run', [AdminController::class, 'runAutoDeduction'])->name('auto-deduction.run');
        Route::post('/auto-deduction/potong/{userId}', [AdminController::class, 'potongZakat'])->name('auto-deduction.potong');

        Route::get('/anggota', [AdminController::class, 'anggota'])->name('anggota');
        Route::get('/anggota/export', [AdminController::class, 'exportAnggota'])->name('anggota.export');
        Route::post('/anggota', [AdminController::class, 'storeAnggota'])->name('anggota.store');
        Route::put('/anggota/{id}', [AdminController::class, 'updateAnggota'])->name('anggota.update');

        Route::get('/transaksi', function () {
            $transactions = Transaction::latest()->get();
            return view('pages.transaksi', compact('transactions'));
        })->name('transaksi');

        Route::post('/transaksi/{id}/verifikasi', [AdminController::class, 'verifikasiTransaksi'])->name('transaksi.verifikasi');
        Route::post('/transaksi/{id}/penyaluran', [AdminController::class, 'simpanPenyaluran'])->name('transaksi.penyaluran');

        Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
        Route::post('/laporan/generate', [AdminController::class, 'generateLaporan'])->name('laporan.generate');
        Route::get('/laporan/export/excel', [AdminController::class, 'exportExcel'])->name('laporan.export.excel');
        Route::get('/laporan/export/pdf', [AdminController::class, 'exportPDF'])->name('laporan.export.pdf');

        Route::post('/pengaturan/lembaga', [AdminController::class, 'updateLembaga'])->name('lembaga.update');
        Route::post('/pengaturan/rekening', [AdminController::class, 'storeRekening'])->name('rekening.store');
        Route::delete('/pengaturan/rekening/{id}', [AdminController::class, 'destroyRekening'])->name('rekening.destroy');
        Route::post('/pengaturan/kategori', [AdminController::class, 'storeKategori'])->name('kategori.store');
        Route::delete('/pengaturan/kategori/{id}', [AdminController::class, 'destroyKategori'])->name('kategori.destroy');
        Route::post('/pengaturan/jenis-harta', [AdminController::class, 'storeJenisHarta'])->name('jenis_harta.store');
        Route::delete('/pengaturan/jenis-harta/{id}', [AdminController::class, 'destroyJenisHarta'])->name('jenis_harta.destroy');
        Route::post('/pengaturan/harga-harta', [AdminController::class, 'storeHargaHarta'])->name('harga_harta.store');
        Route::delete('/pengaturan/harga-harta/{id}', [AdminController::class, 'destroyHargaHarta'])->name('harga_harta.destroy');

        Route::post('/marketplace/program', [MarketplaceController::class, 'storeProgram'])->name('program.store');
        Route::delete('/marketplace/program/{id}', [MarketplaceController::class, 'destroyProgram'])->name('program.destroy');
    });
});
