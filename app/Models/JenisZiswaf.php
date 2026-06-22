<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisZiswaf extends Model
{
    // Menyesuaikan dengan nama tabel di migration
    protected $table = 'jenis_ziswaf';
    
    protected $guarded = ['id'];

    // Relasi: Satu Jenis ZISWAF memiliki banyak Transaksi
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}