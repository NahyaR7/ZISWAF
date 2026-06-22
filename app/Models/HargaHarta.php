<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HargaHarta extends Model
{
    protected $table = 'harga_harta';
    
    protected $guarded = ['id'];

    // Relasi: Harga Harta ini milik Jenis Harta apa?
    public function jenisHarta()
    {
        return $this->belongsTo(JenisHarta::class);
    }

    // Relasi: Harga ini digunakan di transaksi mana saja?
    public function transaksiHarta()
    {
        return $this->hasMany(TransaksiHarta::class);
    }
}