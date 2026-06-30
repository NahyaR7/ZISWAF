@extends('layouts.app')

@section('title', 'Dashboard - ZISWAF BMT')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan penerimaan dan penyaluran ZISWAF')

@section('content')
<div style="padding: 32px 36px;">
    <div class="stats-grid">
      <div class="stat-card" data-icon="🕌">
        <div class="stat-label">Total Zakat Terkumpul</div>
        <div class="stat-value">Rp 48,7 Jt</div>
        <div class="stat-change">↑ 12% dari bulan lalu</div>
      </div>
      <div class="stat-card gold" data-icon="💛">
        <div class="stat-label">Infaq & Sedekah</div>
        <div class="stat-value">Rp 23,1 Jt</div>
        <div class="stat-change">↑ 8% dari bulan lalu</div>
      </div>
      <div class="stat-card blue" data-icon="🌱">
        <div class="stat-label">Dana Wakaf</div>
        <div class="stat-value">Rp 15,3 Jt</div>
        <div class="stat-change">↑ 5% dari bulan lalu</div>
      </div>
      <div class="stat-card purple" data-icon="👥">
        <div class="stat-label">Anggota Aktif ZISWAF</div>
        <div class="stat-value">342</div>
        <div class="stat-change">↑ 18 anggota baru</div>
      </div>
    </div>

    <div class="card" style="margin-top: 24px;">
      <div class="card-header">
        <div class="card-title">Transaksi Terbaru</div>
        <button class="btn btn-outline btn-sm">Lihat Semua →</button>
      </div>
      <div class="table-wrap">
        <table>
            <thead>
                <tr><th>ID</th><th>Anggota</th><th>Jenis</th><th>Nominal</th><th>Penyaluran</th><th>Tanggal</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse ($recentTransactions as $trx)
                    @php
                        $jenis = $trx->jenisZiswaf->nama_jenis ?? 'Zakat';
                        $namaMember = $trx->user->name ?? 'Hamba Allah';
                        $typeBadge = $jenis === 'Zakat' ? 'badge-green' : ($jenis === 'Wakaf' ? 'badge-blue' : 'badge-gold');
                        $statusBadge = $trx->status === 'Tersalur' ? 'badge-green' : ($trx->status === 'Diproses' ? 'badge-gold' : 'badge-red');
                    @endphp
                    <tr>
                        <td style="font-family:monospace;font-size:11.5px">{{ $trx->transaction_code }}</td>
                        <td style="font-weight:600">{{ $namaMember }}</td>
                        <td><span class="badge {{ $typeBadge }}">{{ $jenis }}</span></td>
                        <td style="font-weight:700;color:var(--g2)">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td style="font-size:12px;color:var(--muted)">{{ $trx->organization }}</td>
                        <td style="font-size:12px;color:var(--muted)">{{ $trx->created_at->format('d M Y') }}</td>
                        <td><span class="badge {{ $statusBadge }}">{{ $trx->status }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px; color: var(--muted);">Belum ada data transaksi di database.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
      </div>
    </div>
</div>
@endsection