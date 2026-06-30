<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penerimaan ZISWAF</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #2e7d32; padding-bottom: 10px; margin-bottom: 20px; }
        .logo-text { font-size: 22px; font-weight: bold; color: #2e7d32; }
        .sub-text { font-size: 12px; color: #666; }
        .title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 20px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f1f8e9; color: #2e7d32; font-weight: bold; font-size: 11px; }
        td { font-size: 11px; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #e8f5e9; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-text">BMT Pondok Hijau</div>
        <div class="sub-text">Laporan Resmi Penerimaan ZISWAF</div>
        <div class="sub-text">Dicetak pada: {{ now()->format('d F Y H:i') }} WIB</div>
    </div>

    <div class="title">Daftar Transaksi ZISWAF (Tersalurkan)</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>ID Transaksi</th>
                <th>Muzakki</th>
                <th>Jenis</th>
                <th>Metode</th>
                <th class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @forelse($transactions as $index => $trx)
                @php $total += $trx->amount; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $trx->created_at->format('d/m/Y') }}</td>
                    <td style="font-family:monospace">{{ $trx->transaction_code }}</td>
                    <td>{{ $trx->user->name ?? 'Hamba Allah' }}</td>
                    <td>{{ $trx->jenisZiswaf->nama_jenis ?? 'Zakat' }}</td>
                    <td>{{ $trx->payment->metode ?? 'Manual' }}</td>
                    <td class="text-right">{{ number_format($trx->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center">Belum ada transaksi tersalurkan.</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL PENERIMAAN:</td>
                <td class="text-right">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>