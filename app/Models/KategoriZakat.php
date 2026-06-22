<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriZakat extends Model
{
    protected $table = 'kategori_zakat';
    
    protected $guarded = ['id'];

    // Relasi: Satu Kategori Zakat memiliki banyak Transaksi
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}