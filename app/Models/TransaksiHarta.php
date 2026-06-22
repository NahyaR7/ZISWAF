<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiHarta extends Model
{
    protected $table = 'transaksi_harta';
    
    protected $guarded = ['id'];

    // Relasi: Transaksi Harta ini dilakukan oleh siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Objek hartanya apa (Sapi/Kambing/Emas)?
    public function jenisHarta()
    {
        return $this->belongsTo(JenisHarta::class);
    }

    // Relasi: Mengacu pada harga referensi yang mana saat itu?
    public function hargaHarta()
    {
        return $this->belongsTo(HargaHarta::class);
    }
}