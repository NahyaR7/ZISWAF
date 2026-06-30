<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kwitansi extends Model
{
    protected $table = 'kwitansi';
    
    protected $guarded = ['id'];

    // Relasi: Kwitansi ini diterbitkan untuk Transaksi yang mana?
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}