<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    // Tabel 'payments' sudah bahasa Inggris jamak, jadi tidak perlu deklarasi $table
    
    protected $guarded = ['id'];

    // Relasi: Payment ini ditransfer ke Rekening BMT mana?
    public function rekeningBMT()
    {
        return $this->belongsTo(RekeningBMT::class);
    }

    // Relasi: Payment ini digunakan untuk Transaksi utama yang mana?
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}