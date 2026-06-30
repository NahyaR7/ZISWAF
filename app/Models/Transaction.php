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

<<<<<<< HEAD
    public function tandaiBerhasil(?int $verifiedBy = null): void
    {
        // Cegah proses ganda
        if (in_array($this->status, ['Menunggu Penyaluran', 'Tersalur'])) {
=======
    /**
     * Menandai transaksi sebagai berhasil/lunas: terbitkan kwitansi,
     * update status payment, dan kirim notifikasi ke nasabah.
     *
     * Dipakai bersama oleh Webhook Midtrans dan pengecekan status QRIS
     * agar logika penyelesaian transaksi tidak terduplikasi.
     *
     * @param  int|null  $verifiedBy  Null = diverifikasi otomatis oleh sistem/API.
     */
    public function tandaiBerhasil(?int $verifiedBy = null): void
    {
        // Idempotent: jangan proses ganda jika sudah Tersalur.
        if ($this->status === 'Tersalur') {
>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
            return;
        }

        $kwitansiNumber = $this->kwitansi_number ?: 'KW-' . date('Y') . '-' . rand(1000, 9999);

        $this->update([
<<<<<<< HEAD
            'status' => 'Menunggu Penyaluran', // Status berubah menjadi Menunggu Penyaluran
=======
            'status' => 'Tersalur',
>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
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
<<<<<<< HEAD
}
=======
}
>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
