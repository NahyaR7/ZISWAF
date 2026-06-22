@extends('layouts.app')

@section('title', 'Dashboard Saya - ZISWAF BMT')
@section('page-title', 'Dashboard Saya')
@section('page-subtitle', 'Selamat datang, pantau ZISWAF Anda di sini')

@section('content')
<div style="padding: 32px 36px;">
    <div class="alert alert-success mb-20">
      <span style="font-size:20px">🌙</span>
      <div>
        <strong>Assalamu'alaikum, {{ auth()->user()->name }}!</strong><br>
        <span style="font-size:13px;opacity:0.8">Semoga amal ibadah Anda senantiasa diterima oleh Allah SWT. Berikut ringkasan ZISWAF Anda.</span>
      </div>
    </div>
    
    <div class="stats-grid" style="grid-template-columns:repeat(3,1fr)">
      <div class="stat-card" data-icon="📊">
        <div class="stat-label">Total Zakat Dibayar</div>
        <div class="stat-value">Rp 3,75 Jt</div>
        <div class="stat-change">Tahun ini</div>
      </div>
      <div class="stat-card gold" data-icon="💛">
        <div class="stat-label">Infaq & Sedekah</div>
        <div class="stat-value">Rp 1,2 Jt</div>
        <div class="stat-change">Total donasi</div>
      </div>
      <div class="stat-card blue" data-icon="🔔">
        <div class="stat-label">Status Nisab</div>
        <div class="stat-value" style="font-size:18px">Wajib Zakat</div>
        <div class="stat-change" style="color:var(--g4)">✅ Saldo mencapai nisab</div>
      </div>
    </div>

    <div class="dashboard-grid">
      <div class="card">
        <div class="card-header"><div class="card-title">Riwayat Transaksi Saya</div></div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>ID</th><th>Jenis</th><th>Nominal</th><th>Lembaga</th><th>Tanggal</th><th>Status</th><th>Kwitansi</th></tr></thead>
            <tbody>
                @forelse ($transactions as $trx)
                    @php
                        $jenis = $trx->jenisZiswaf->nama_jenis ?? 'Zakat';
                        $namaMember = $trx->user->name ?? 'Hamba Allah';
                        $metode = $trx->payment->metode ?? 'Transfer Bank';
                        $typeBadge = $jenis === 'Zakat' ? 'badge-green' : ($jenis === 'Wakaf' ? 'badge-blue' : 'badge-gold');
                        $statusBadge = $trx->status === 'Tersalur' ? 'badge-green' : ($trx->status === 'Diproses' ? 'badge-gold' : 'badge-red');
                    @endphp
                    <tr>
                        <td style="font-family:monospace;font-size:11.5px">{{ $trx->transaction_code }}</td>
                        <td><span class="badge {{ $typeBadge }}">{{ $jenis }}</span></td>
                        <td style="font-weight:700;color:var(--g2)">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td style="font-size:12px;color:var(--muted)">{{ $trx->organization }}</td>
                        <td style="font-size:12px;color:var(--muted)">{{ $trx->created_at->format('d M Y') }}</td>
                        <td><span class="badge {{ $statusBadge }}">{{ $trx->status }}</span></td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="viewKwitansi('{{ $trx->kwitansi_number ?? '-' }}', '{{ $trx->transaction_code }}', '{{ $trx->created_at->format('d M Y') }}', '{{ $jenis }}', '{{ $namaMember }}', '{{ $trx->organization }}', '{{ $metode }}', 'Rp {{ number_format($trx->amount, 0, ',', '.') }}')">🧾</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align: center; color: var(--muted);">Belum ada transaksi</td></tr>
                @endforelse
            </tbody>
          </table>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title">Status Auto-Deduction Saya</div></div>
        <div class="card-body">
          <div style="background:var(--g8);border-radius:12px;padding:16px;border:1px solid var(--border);margin-bottom:16px">
            <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px">Saldo Tabungan</div>
            <div style="font-family:'Playfair Display',serif;font-size:24px;font-weight:700;color:var(--g2)">Rp 150.000.000</div>
            <div style="font-size:12px;color:var(--g4);margin-top:4px">✅ Mencapai nisab emas</div>
          </div>
          <div class="setting-row">
            <div class="setting-info"><h4>Auto-Deduction Zakat</h4><p>Pemotongan otomatis setiap tahun</p></div>
            <button class="toggle on" onclick="this.classList.toggle('on');showToast('⚡ Pengaturan auto-deduction diperbarui')"></button>
          </div>
          <div style="margin-top:14px">
            <a href="{{ route('kalkulator') }}" class="btn btn-primary" style="width:100%;justify-content:center;text-decoration:none;">⚖️ Hitung Zakat Saya Sekarang</a>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection