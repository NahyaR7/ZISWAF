<!DOCTYPE html>
<html>
<head>
    <title>Kwitansi ZISWAF - {{ $transaction->transaction_code }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 14px; }
        .header { text-align: center; border-bottom: 2px solid #2e7d32; padding-bottom: 10px; margin-bottom: 20px; }
        .logo-text { font-size: 24px; font-weight: bold; color: #2e7d32; margin-bottom: 5px; }
        .sub-text { font-size: 12px; color: #666; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 25px; letter-spacing: 1px; }
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-data td { padding: 10px; border-bottom: 1px solid #ddd; }
        .label { font-weight: bold; width: 35%; color: #555; }
        .total-box { background-color: #f1f8e9; padding: 15px; text-align: right; border: 1px solid #c5e1a5; font-size: 18px; font-weight: bold; color: #2e7d32; border-radius: 5px; }
        .footer { text-align: center; margin-top: 60px; font-size: 12px; color: #888; border-top: 1px dashed #ccc; padding-top: 20px; }
        .badge-status { color: #2e7d32; font-weight: bold; border: 1px solid #2e7d32; padding: 3px 8px; border-radius: 3px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-text">BMT Pondok Hijau</div>
        <div class="sub-text">Koperasi Syariah — Sistem ZISWAF Digital</div>
        <div class="sub-text">Jl. Pondok Hijau No. 10, Depok | Email: admin@bmtpondokhijau.id</div>
    </div>
    
    <div class="title">KWITANSI DIGITAL ZISWAF</div>

    <table class="table-data">
        <tr>
            <td class="label">No. Kwitansi</td>
            <td style="font-family: monospace; font-size: 16px;">{{ $transaction->kwitansi_number ?? 'Menunggu Verifikasi' }}</td>
        </tr>
        <tr>
            <td class="label">ID Transaksi</td>
            <td style="font-family: monospace;">{{ $transaction->transaction_code }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Diterbitkan</td>
            <td>{{ $transaction->created_at->format('d F Y - H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="label">Nama Muzakki / Donatur</td>
            <td>{{ $transaction->user->name ?? 'Hamba Allah' }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Transaksi</td>
            <td>{{ $transaction->jenisZiswaf->nama_jenis ?? 'Zakat' }}</td>
        </tr>
        <tr>
            <td class="label">Lembaga Penyalur</td>
            <td>{{ $transaction->organization }}</td>
        </tr>
        <tr>
            <td class="label">Metode Pembayaran</td>
            <td>{{ $transaction->payment->metode ?? 'Transfer Bank' }}</td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td><span class="badge-status">{{ strtoupper($transaction->status) }}</span></td>
        </tr>
    </table>

    <div class="total-box">
        TOTAL DIBAYAR: Rp {{ number_format($transaction->amount, 0, ',', '.') }}
    </div>

    <div class="footer">
        <h3 style="color: #c9a84c; font-family: serif; margin-bottom: 5px;">جَزَاكَ اللهُ خَيْرًا كَثِيرًا</h3>
        <p style="margin: 5px 0;">Terima kasih atas ZISWAF yang Anda tunaikan. Semoga menjadi amal jariyah dan membawa keberkahan.</p>
        <p style="font-size: 10px; margin-top: 15px;"><i>Kwitansi ini adalah bukti pembayaran digital yang sah dan diterbitkan otomatis oleh sistem ZISWAF BMT Pondok Hijau. Tidak memerlukan tanda tangan basah.</i></p>
    </div>
</body>
</html>