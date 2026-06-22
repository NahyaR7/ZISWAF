<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ZiswafNotification extends Notification
{
    use Queueable;

    protected $pesan;

    public function __construct($pesan)
    {
        $this->pesan = $pesan;
    }

    // Menyimpan notifikasi ke dalam database
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->pesan
        ];
    }
}