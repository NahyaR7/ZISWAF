<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\JenisZiswaf;
use App\Models\KategoriZakat;
use App\Models\HargaEmas;
use App\Models\RekeningBMT;
use App\Models\Transaction;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Users
        $admin = User::create([
            'name' => "Ahmad Syafi'i", 'username' => 'admin', 'email' => 'admin@bmtpondokhijau.id', 
            'password' => Hash::make('admin123'), 'role' => 'admin', 'no_hp' => '081234567890'
        ]);

        $nasabah = User::create([
            'name' => 'Fathur Rahman', 'username' => 'nasabah', 'email' => 'fathur@contoh.com', 
            'password' => Hash::make('nasabah123'), 'role' => 'nasabah', 'no_hp' => '089876543210'
        ]);

        // 2. Buat Jenis ZISWAF
        $jenisZakat = JenisZiswaf::create(['nama_jenis' => 'Zakat', 'deskripsi' => 'Kewajiban harta']);
        $jenisInfaq = JenisZiswaf::create(['nama_jenis' => 'Infaq', 'deskripsi' => 'Sedekah sukarela']);
        $jenisWakaf = JenisZiswaf::create(['nama_jenis' => 'Wakaf', 'deskripsi' => 'Harta yang ditahan untuk umat']);

        // 3. Buat Kategori Zakat
        KategoriZakat::create(['nama_kategori' => 'Zakat Tabungan/Emas', 'nisab' => 114325000, 'persentase' => 2.5]);
        KategoriZakat::create(['nama_kategori' => 'Zakat Profesi', 'nisab' => 15820000, 'persentase' => 2.5]);

        // 4. Harga Emas Terkini
        HargaEmas::create(['tanggal' => now(), 'harga_emas_per_gram' => 1345000]);

        // 5. Rekening Tujuan BMT
        RekeningBMT::create(['nama_bank' => 'BSI (Bank Syariah Indonesia)', 'no_rekening' => '7123456789', 'atas_nama' => 'BMT Pondok Hijau']);
        RekeningBMT::create(['nama_bank' => 'Bank Muamalat', 'no_rekening' => '3012345678', 'atas_nama' => 'BMT Pondok Hijau ZISWAF']);

        // 6. Dummy Transaksi (Relasional)
        Transaction::create([
            'transaction_code' => 'TRX-2605-001',
            'user_id' => $nasabah->id,
            'jenis_ziswaf_id' => $jenisZakat->id,
            'amount' => 3750000,
            'status' => 'Tersalur',
            'kwitansi_number' => 'KW-2026-0047'
        ]);

        Transaction::create([
            'transaction_code' => 'TRX-2605-002',
            'user_id' => $nasabah->id,
            'jenis_ziswaf_id' => $jenisInfaq->id,
            'amount' => 200000,
            'status' => 'Tersalur',
            'kwitansi_number' => 'KW-2026-0046'
        ]);
    }
}