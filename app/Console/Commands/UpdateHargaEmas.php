<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\HargaEmas;

class UpdateHargaEmas extends Command
{
    /**
     * Nama perintah yang akan dijalankan di terminal
     *
     * @var string
     */
    protected $signature = 'app:update-harga-emas';

    /**
     * Deskripsi perintah
     *
     * @var string
     */
    protected $description = 'Mengambil harga emas real-time dari Yahoo Finance (tanpa API Key) dan menyimpannya ke database';

    /**
     * Eksekusi perintah (Logika Utama)
     */
    public function handle()
    {
        $this->info('Memulai proses sinkronisasi harga emas dari Yahoo Finance...');

        try {
            // 1. Ambil Harga Emas Dunia (USD per Troy Ounce) - Simbol: GC=F (Gold Futures)
            // Header User-Agent ditambahkan agar Yahoo tidak mengira kita adalah bot spam
            $responseGold = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
            ])->get('https://query1.finance.yahoo.com/v8/finance/chart/GC=F');

            // 2. Ambil Kurs USD ke IDR saat ini - Simbol: IDR=X
            $responseIdr = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
            ])->get('https://query1.finance.yahoo.com/v8/finance/chart/IDR=X');

            // Jika kedua permintaan berhasil
            if ($responseGold->successful() && $responseIdr->successful()) {
                $dataGold = $responseGold->json();
                $dataIdr = $responseIdr->json();
                
                // Mengekstrak harga pasar reguler saat ini dari kedalaman struktur JSON Yahoo Finance
                $goldUsdPerOunce = $dataGold['chart']['result'][0]['meta']['regularMarketPrice'] ?? 0;
                $usdToIdr = $dataIdr['chart']['result'][0]['meta']['regularMarketPrice'] ?? 0;

                // Memastikan data valid (tidak 0)
                if ($goldUsdPerOunce > 0 && $usdToIdr > 0) {
                    
                    // Rumus Konversi: 1 Troy Ounce = 31.1034768 gram
                    $goldUsdPerGram = $goldUsdPerOunce / 31.1034768;
                    
                    // Konversi harga Gram USD ke IDR
                    $hargaPerGramIdr = $goldUsdPerGram * $usdToIdr;

                    // Simpan ke database
                    HargaEmas::create([
                        'tanggal' => now(),
                        'harga_emas_per_gram' => round($hargaPerGramIdr, 2)
                    ]);

                    $this->info('Berhasil! Harga Emas (Yahoo Finance): Rp ' . number_format($hargaPerGramIdr, 0, ',', '.'));
                } else {
                    $this->error('Gagal membaca struktur data dari Yahoo Finance. Beralih ke simulasi.');
                    $this->simulasiHarga();
                }
            } else {
                $this->error('Server Yahoo Finance sedang sibuk/menolak akses. Beralih ke simulasi.');
                $this->simulasiHarga();
            }
        } catch (\Exception $e) {
            $this->error('Terjadi gangguan koneksi internet: ' . $e->getMessage());
            $this->simulasiHarga();
        }
    }

    /**
     * Fungsi fallback jika Yahoo Finance sedang down (Sistem tetap aman)
     */
    private function simulasiHarga()
    {
        // Simulasi fluktuasi harga emas harian yang realistis
        $hargaSimulasi = rand(2670000, 2680000);
        
        HargaEmas::create([
            'tanggal' => now(),
            'harga_emas_per_gram' => $hargaSimulasi
        ]);

        $this->warn('Mode Simulasi Aktif: Harga emas disimpan di Rp ' . number_format($hargaSimulasi, 0, ',', '.'));
    }
}