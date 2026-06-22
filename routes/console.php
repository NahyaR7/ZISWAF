<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // Tambahkan facade Schedule

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ==========================================
// JOB SCHEDULER ZISWAF BMT
// ==========================================

// Menjalankan update harga emas setiap hari jam 00:00 WIB
Schedule::command('app:update-harga-emas')->daily()->timezone('Asia/Jakarta');