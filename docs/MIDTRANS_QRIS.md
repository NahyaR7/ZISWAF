# Integrasi QRIS Midtrans (Dummy / Sandbox)

Dokumen ini menjelaskan cara mengaktifkan & menguji pembayaran ZISWAF via **QRIS**
menggunakan **Midtrans Core API** mode Sandbox.

## 1. Dapatkan Kredensial Sandbox

1. Daftar / login di **https://dashboard.sandbox.midtrans.com**.
2. Buka **Settings → Access Keys**.
3. Salin **Server Key** dan **Client Key** (yang berawalan `SB-Mid-...`).

## 2. Isi `.env`

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_QRIS_ACQUIRER=gopay
```

> `MIDTRANS_IS_PRODUCTION=false` memakai endpoint `api.sandbox.midtrans.com`.
> Untuk live, ganti `true` dan pakai kunci Production.

Bersihkan cache config setelah mengubah `.env`:

```bash
php artisan config:clear
```

## 3. Alur Teknis

| Langkah | Endpoint | Keterangan |
|--------|----------|------------|
| Buat QR | `POST /qris/create` | Membuat transaksi lokal (status `Diproses`) lalu `POST /v2/charge` ke Midtrans dengan `payment_type: qris`. Mengembalikan URL gambar QR. |
| Cek status | `GET /qris/status/{orderId}` | Polling dari frontend tiap 3 detik. Memanggil `GET /v2/{order_id}/status` Midtrans, lalu menerbitkan kwitansi bila `settlement`. |
| Webhook | `POST /api/midtrans/webhook` | Notifikasi resmi Midtrans (sumber kebenaran di produksi), dengan validasi signature SHA512. |

`order_id` Midtrans = `transaction_code` di database (`TRX-yymm-XXXX`).

Logika penyelesaian transaksi dipusatkan di `Transaction::tandaiBerhasil()` sehingga
jalur **webhook** dan **polling** menghasilkan kwitansi + notifikasi yang konsisten.

## 4. Cara Menguji (Proof of Concept)

1. Jalankan aplikasi: `composer dev` (atau `php artisan serve`).
2. Login sebagai nasabah, buka menu **Bayar**.
3. Pilih jenis ZISWAF → isi nominal → pilih **Metode Pembayaran: QRIS** → lanjut → **Konfirmasi & Bayar**.
4. Layar QRIS muncul menampilkan QR Code dari Midtrans.
5. **Simulasikan pembayaran** di **Simulator Sandbox**:
   **https://simulator.sandbox.midtrans.com/qris/index** — scan / tempel `qr_string`
   atau masukkan order id, lalu klik **Pay**.
6. Polling otomatis mendeteksi `settlement` → halaman berpindah ke kwitansi sukses,
   status transaksi menjadi `Tersalur`, dan kwitansi otomatis terbit.

## 5. Catatan

- Di `localhost`, server Midtrans tidak bisa menembak webhook, jadi **polling status**
  adalah jalur utama pembuktian. Untuk menguji webhook, ekspos lokal via `ngrok`
  dan set URL-nya di **Settings → Configuration → Payment Notification URL**.
- Acquirer QRIS bisa `gopay` atau `airpay shopee` (atur via `MIDTRANS_QRIS_ACQUIRER`).
