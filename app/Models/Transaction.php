<?php

namespace App\Models;

use App\Notifications\ZiswafNotification;
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

    public function program() {
        return $this->belongsTo(Program::class);
    }

    public function tandaiBerhasil(?int $verifiedBy = null): void
    {
        // Cegah proses ganda
        if (in_array($this->status, ['Menunggu Penyaluran', 'Tersalur'])) {
            return;
        }

        $kwitansiNumber = $this->kwitansi_number ?: 'KW-' . date('Y') . '-' . rand(1000, 9999);

        $this->update([
            'status' => 'Menunggu Penyaluran',
            'kwitansi_number' => $kwitansiNumber,
            'verified_at' => now(),
            'verified_by' => $verifiedBy,
        ]);

        if ($this->payment) {
            $this->payment->update(['status_payment' => 'Success']);
        }

        if ($this->user) {
            $jenis = $this->jenisZiswaf->nama_jenis ?? 'ZISWAF';
            $this->user->notify(new ZiswafNotification(
                "Pembayaran {$jenis} Rp " . number_format($this->amount, 0, ',', '.') .
                " BERHASIL divalidasi sistem. Kwitansi: " . $kwitansiNumber
            ));
        }
    }
}
