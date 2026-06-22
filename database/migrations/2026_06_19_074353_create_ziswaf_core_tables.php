<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Lengkapi Tabel Users (Aktor: Nasabah & Admin)
        Schema::table('users', function (Blueprint $table) {
            $table->string('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('pekerjaan')->nullable();
        });

        // 2. Tabel JenisZISWAF
        Schema::create('jenis_ziswaf', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis'); // Zakat, Infaq, Sedekah, Wakaf, Fidyah
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 3. Tabel KategoriZakat
        Schema::create('kategori_zakat', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori'); // Zakat Maal, Zakat Profesi, dll
            $table->decimal('nisab', 15, 2)->nullable();
            $table->decimal('persentase', 5, 2)->default(2.50);
            $table->timestamps();
        });

        // 4. Tabel HargaEmas
        Schema::create('harga_emas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->decimal('harga_emas_per_gram', 15, 2);
            $table->timestamps();
        });

        // 5. Tabel JenisHarta & 6. HargaHarta
        Schema::create('jenis_harta', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis_harta'); // Sapi, Kambing, Gabah, dll
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('harga_harta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_harta_id')->constrained('jenis_harta')->cascadeOnDelete();
            $table->date('tanggal');
            $table->decimal('harga', 15, 2);
            $table->timestamps();
        });

        // 7. Tabel RekeningBMT
        Schema::create('rekening_bmt', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank');
            $table->string('no_rekening');
            $table->string('atas_nama');
            $table->timestamps();
        });

        // 8. Tabel Payment
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('metode');
            $table->string('status_payment')->default('Pending');
            $table->date('tanggal_payment')->nullable();
            $table->string('reference_id')->nullable();
            $table->foreignId('rekening_bmt_id')->nullable()->constrained('rekening_bmt')->nullOnDelete();
            $table->timestamps();
        });

        // Hapus tabel transaksi lama (jika ada) untuk diganti dengan yang Relasional
        Schema::dropIfExists('transactions');

        // 9. Tabel Transaksi Utama
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('jenis_ziswaf_id')->constrained('jenis_ziswaf')->cascadeOnDelete();
            $table->foreignId('kategori_zakat_id')->nullable()->constrained('kategori_zakat')->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            
            $table->decimal('amount', 15, 2);
            $table->string('organization')->default('BMT Pondok Hijau');
            $table->string('status')->default('Diproses');
            $table->string('kwitansi_number')->nullable();
            $table->text('bukti_bayar')->nullable();
            $table->timestamps();
        });

        // 10. Tabel Kwitansi
        Schema::create('kwitansi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->string('nomor_kwitansi')->unique();
            $table->date('tanggal');
            $table->string('file_kwitansi')->nullable();
            $table->timestamps();
        });

        // 11. Tabel TransaksiHarta
        Schema::create('transaksi_harta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('jenis_harta_id')->constrained('jenis_harta')->cascadeOnDelete();
            $table->foreignId('harga_harta_id')->nullable()->constrained('harga_harta')->nullOnDelete();
            $table->integer('jumlah_harta');
            $table->string('status')->default('Belum Dihitung');
            $table->date('tanggal');
            $table->timestamps();
        });

        // 12. Tabel Laporan
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->string('periode');
            $table->date('tanggal_generate');
            $table->decimal('total_transaksi', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Drop tables in reverse order to avoid foreign key constraints errors
        Schema::dropIfExists('laporan');
        Schema::dropIfExists('transaksi_harta');
        Schema::dropIfExists('kwitansi');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('rekening_bmt');
        Schema::dropIfExists('harga_harta');
        Schema::dropIfExists('jenis_harta');
        Schema::dropIfExists('harga_emas');
        Schema::dropIfExists('kategori_zakat');
        Schema::dropIfExists('jenis_ziswaf');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['alamat', 'no_hp', 'pekerjaan']);
        });
    }
};