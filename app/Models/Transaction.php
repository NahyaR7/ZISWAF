<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = ['id'];

    public function user() { 
        return $this->belongsTo(User::class); 
    }
    
    public function jenisZiswaf() { 
        return $this->belongsTo(JenisZiswaf::class); 
    }

    public function payment() { 
        return $this->belongsTo(Payment::class); 
    }
}