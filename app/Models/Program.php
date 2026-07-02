<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $guarded = ['id'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function collected(): float
    {
        return (float) $this->transactions()->where('status', 'Tersalur')->sum('amount');
    }
}
