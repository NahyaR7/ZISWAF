<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporan';
    
    protected $guarded = ['id'];

    // Relasi untuk Audit Trail: Laporan ini di-generate oleh siapa?
    public function admin()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}