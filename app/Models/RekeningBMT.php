<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekeningBMT extends Model
{
    protected $table = 'rekening_bmt';
    
    protected $guarded = ['id'];

    // Relasi: Rekening BMT ini menampung pembayaran apa saja?
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}