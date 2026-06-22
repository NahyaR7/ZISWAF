<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisHarta extends Model
{
    protected $table = 'jenis_harta';
    
    protected $guarded = ['id'];

    // Relasi: Satu Jenis Harta (misal: Sapi) bisa memiliki riwayat banyak Harga Harta
    public function hargaHarta()
    {
        return $this->hasMany(HargaHarta::class);
    }

    // Relasi: Satu Jenis Harta bisa memiliki banyak Transaksi Harta (nasabah A, nasabah B)
    public function transaksiHarta()
    {
        return $this->hasMany(TransaksiHarta::class);
    }
}