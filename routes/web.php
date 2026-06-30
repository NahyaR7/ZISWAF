<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KalkulatorController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\QrisPaymentController;
use App\Http\Controllers\AdminController;
use App\Models\Transaction;

<<<<<<< HEAD
=======
// ==========================================
// RUTE AUTENTIKASI (LOGIN & REGISTER)
// ==========================================
>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');

<<<<<<< HEAD
Route::middleware(['auth'])->group(function () {
    
=======

// ==========================================
// RUTE DENGAN PROTEKSI (HARUS LOGIN)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // 1. SMART DASHBOARD REDIRECTOR (Mencegah Nasabah nyasar ke Admin)
>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
    Route::get('/', function () {
        if (auth()->user()->role === 'admin') {
            return app(DashboardController::class)->indexAdmin();
        }
        return redirect()->route('dashboard-user');
    })->name('dashboard');

    Route::get('/dashboard-user', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('dashboard');
        }
        $transactions = Transaction::where('user_id', auth()->id())->latest()->take(4)->get();
        return view('pages.dashboard-user', compact('transactions'));
    })->name('dashboard-user');

<<<<<<< HEAD
=======

    // 2. RUTE GLOBAL (Bisa diakses Admin & Nasabah)
>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
    Route::get('/kalkulator', [KalkulatorController::class, 'index'])->name('kalkulator');
    Route::get('/kalkulator/hitung', [KalkulatorController::class, 'hitung'])->name('kalkulator.hitung');
    Route::get('/kalkulator/live-harga', [KalkulatorController::class, 'liveHarga'])->name('kalkulator.live');
    
    Route::get('/kwitansi', function () {
        $query = Transaction::whereNotNull('kwitansi_number')->latest();
<<<<<<< HEAD
        if (auth()->user()->role !== 'admin') $query->where('user_id', auth()->id());
        $transactions = $query->get();
        return view('pages.kwitansi', compact('transactions'));
    })->name('kwitansi');
    
    Route::get('/kwitansi/download/{transaction_code}', [TransactionController::class, 'downloadPDF'])->name('kwitansi.download');
=======
        // Jika nasabah, hanya lihat kwitansinya sendiri
        if (auth()->user()->role !== 'admin') $query->where('user_id', auth()->id());
        
        $transactions = $query->get();
        return view('pages.kwitansi', compact('transactions'));
    })->name('kwitansi');
    Route::get('/kwitansi/download/{transaction_code}', [TransactionController::class, 'downloadPDF'])->name('kwitansi.download');

>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
    Route::get('/notifikasi/read', function() { 
        auth()->user()->unreadNotifications->markAsRead(); 
        return response()->json(['success' => true]); 
    })->name('notifikasi.read');

<<<<<<< HEAD
    // RUTE NASABAH
    Route::get('/bayar', function () { return view('pages.bayar'); })->name('bayar');
    Route::post('/transaksi/store', [TransactionController::class, 'store'])->name('transaksi.store');
=======

    // 3. RUTE KHUSUS NASABAH
    Route::get('/bayar', function () { return view('pages.bayar'); })->name('bayar');
    Route::post('/transaksi/store', [TransactionController::class, 'store'])->name('transaksi.store');

    // Pembayaran QRIS via Midtrans (buat QR + polling status)
>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
    Route::post('/qris/create', [QrisPaymentController::class, 'createQris'])->name('qris.create');
    Route::get('/qris/status/{orderId}', [QrisPaymentController::class, 'checkStatus'])->name('qris.status');
    Route::get('/marketplace', function () { return view('pages.marketplace'); })->name('marketplace');

<<<<<<< HEAD
    Route::get('/penyaluran', function () {
        // Tampilkan SEMUA transaksi nasabah agar bisa dilacak
        $transactions = Transaction::where('user_id', auth()->id())->latest()->get();
        return view('pages.penyaluran', compact('transactions'));
    })->name('penyaluran');

    // RUTE ADMIN
    Route::get('/auto-deduction', function () { return view('pages.auto-deduction'); })->name('auto-deduction');
    Route::get('/anggota', function () { return view('pages.anggota'); })->name('anggota');
=======

    // 4. RUTE KHUSUS ADMIN
    Route::get('/auto-deduction', function () { return view('pages.auto-deduction'); })->name('auto-deduction');
    Route::get('/anggota', function () { return view('pages.anggota'); })->name('anggota');
    
>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
    Route::get('/transaksi', function () {
        $transactions = Transaction::latest()->get();
        return view('pages.transaksi', compact('transactions'));
    })->name('transaksi');
<<<<<<< HEAD
    
    Route::post('/transaksi/{id}/verifikasi', [AdminController::class, 'verifikasiTransaksi'])->name('transaksi.verifikasi');
    Route::post('/transaksi/{id}/penyaluran', [AdminController::class, 'simpanPenyaluran'])->name('transaksi.penyaluran');
=======
    Route::post('/transaksi/{id}/verifikasi', [AdminController::class, 'verifikasiTransaksi'])->name('transaksi.verifikasi');
>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
    
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
    Route::post('/laporan/generate', [AdminController::class, 'generateLaporan'])->name('laporan.generate');
    Route::get('/laporan/export/excel', [AdminController::class, 'exportExcel'])->name('laporan.export.excel');
    Route::get('/laporan/export/pdf', [AdminController::class, 'exportPDF'])->name('laporan.export.pdf');
    
    Route::get('/pengaturan', [AdminController::class, 'pengaturan'])->name('pengaturan');
    Route::post('/pengaturan/rekening', [AdminController::class, 'storeRekening'])->name('rekening.store');
    Route::delete('/pengaturan/rekening/{id}', [AdminController::class, 'destroyRekening'])->name('rekening.destroy');
    Route::post('/pengaturan/kategori', [AdminController::class, 'storeKategori'])->name('kategori.store');
    Route::delete('/pengaturan/kategori/{id}', [AdminController::class, 'destroyKategori'])->name('kategori.destroy');
    Route::post('/pengaturan/jenis-harta', [AdminController::class, 'storeJenisHarta'])->name('jenis_harta.store');
    Route::delete('/pengaturan/jenis-harta/{id}', [AdminController::class, 'destroyJenisHarta'])->name('jenis_harta.destroy');
    Route::post('/pengaturan/harga-harta', [AdminController::class, 'storeHargaHarta'])->name('harga_harta.store');
    Route::delete('/pengaturan/harga-harta/{id}', [AdminController::class, 'destroyHargaHarta'])->name('harga_harta.destroy');
<<<<<<< HEAD
=======

>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
});