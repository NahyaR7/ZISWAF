@extends('layouts.app')

@section('title', 'Laporan ZISWAF - BMT Pondok Hijau')
@section('page-title', 'Laporan ZISWAF')
@section('page-subtitle', 'Generate dan kelola laporan')

@section('content')
<div style="padding: 32px 36px;">
    @if(session('success'))
    <div class="alert alert-success mb-24" style="animation: fadeIn 0.5s ease;">
        <span style="font-size:20px">✅</span>
        <div><strong>Berhasil!</strong> {{ session('success') }}</div>
    </div>
    @endif

    <div class="card mb-24">
      <div class="card-header">
        <div class="card-title">📊 Kelola & Generate Laporan</div>
        <span class="badge badge-gold">UC 8 & UC 9</span>
      </div>
      <div class="card-body">
        <div class="grid-2" style="gap:14px;margin-bottom:16px">
          <div class="input-group" style="margin:0"><label class="input-label">Periode Laporan</label>
            <select class="input-field" id="laporan-period">
                <option>{{ now()->translatedFormat('F Y') }}</option>
                <option>{{ now()->subMonth()->translatedFormat('F Y') }}</option>
            </select>
          </div>
          <div class="input-group" style="margin:0"><label class="input-label">Jenis Laporan</label>
            <select class="input-field" id="laporan-type">
                <option>Laporan Penerimaan ZISWAF</option>
                <option>Laporan Penyaluran</option>
            </select>
          </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
          <button class="btn btn-primary" onclick="showToast('📊 Menampilkan laporan untuk periode terpilih')">📊 Tampilkan Laporan</button>
          
          <form action="{{ route('laporan.generate') }}" method="POST" style="margin:0;">
              @csrf
              <button type="submit" class="btn btn-gold">📅 Generate Bulanan (UC 9)</button>
          </form>

          <a href="{{ route('laporan.export.pdf') }}" class="btn btn-outline" style="text-decoration:none;" onclick="showToast('📥 Mengunduh PDF...')">📥 Export PDF</a>
          <a href="{{ route('laporan.export.excel') }}" class="btn btn-outline" style="text-decoration:none;" onclick="showToast('📊 Mengunduh Excel...')">📊 Export Excel</a>
        </div>
      </div>
    </div>

    <div class="grid-2 mb-24">
      <div class="card">
        <div class="card-header"><div class="card-title">Distribusi ZISWAF (All Time)</div></div>
        <div class="card-body">
          <div class="donut-wrap">
            <svg viewBox="0 0 140 140" width="140" height="140">
              <circle cx="70" cy="70" r="55" fill="none" stroke="var(--border)" stroke-width="20"/>
              <circle cx="70" cy="70" r="55" fill="none" stroke="var(--g4)" stroke-width="20" stroke-dasharray="199 346" stroke-dashoffset="-86" stroke-linecap="round"/>
              <circle cx="70" cy="70" r="55" fill="none" stroke="var(--gold)" stroke-width="20" stroke-dasharray="100 346" stroke-dashoffset="-285" stroke-linecap="round"/>
            </svg>
            <div class="donut-center">
                <div class="donut-total">Rp {{ number_format($totalPenerimaan / 1000000, 1, ',', '.') }}</div>
                <div class="donut-label">Juta Total</div>
            </div>
          </div>
          <div class="breakdown-list">
            <div class="breakdown-item"><div class="bd-dot" style="background:var(--g4)"></div><div class="bd-label">Zakat</div><div><div class="bd-amount">Rp {{ number_format($totalZakat / 1000000, 1, ',', '.') }} Jt</div><div class="bd-pct">{{ $pctZakat }}%</div></div></div>
            <div class="breakdown-item"><div class="bd-dot" style="background:var(--gold)"></div><div class="bd-label">Infaq</div><div><div class="bd-amount">Rp {{ number_format($totalInfaq / 1000000, 1, ',', '.') }} Jt</div><div class="bd-pct">{{ $pctInfaq }}%</div></div></div>
            <div class="breakdown-item"><div class="bd-dot" style="background:#7c5cbf"></div><div class="bd-label">Wakaf</div><div><div class="bd-amount">Rp {{ number_format($totalWakaf / 1000000, 1, ',', '.') }} Jt</div><div class="bd-pct">{{ $pctWakaf }}%</div></div></div>
          </div>
        </div>
      </div>
      
      <div class="card">
        <div class="card-header"><div class="card-title">Histori Generate Laporan & Audit Trail</div></div>
        <div class="card-body" style="padding:0; overflow-x:auto;">
            <table style="width:100%; text-align:left; border-collapse:collapse;">
                <thead style="background:var(--g8); border-bottom:1px solid var(--border);">
                    <tr>
                        <th style="padding:12px 16px; font-size:12px;">Periode</th>
                        <th style="padding:12px 16px; font-size:12px;">Tgl Generate</th>
                        <th style="padding:12px 16px; font-size:12px;">Total Transaksi</th>
                        <th style="padding:12px 16px; font-size:12px;">Dibuat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatLaporan as $lap)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 16px; font-weight:600;">{{ $lap->periode }}</td>
                        <td style="padding:12px 16px; font-size:12px; color:var(--muted)">{{ \Carbon\Carbon::parse($lap->tanggal_generate)->format('d M Y') }}</td>
                        <td style="padding:12px 16px; font-weight:700; color:var(--g2)">Rp {{ number_format($lap->total_transaksi, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px; font-size:12px;"><span class="badge badge-blue">{{ $lap->admin->name ?? 'Sistem' }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding:24px; text-align:center; color:var(--muted); font-size:13px;">Belum ada laporan yang di-generate.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <div class="card-title" id="laporan-table-title">Daftar Penerimaan ZISWAF Bulan Ini</div>
      </div>
      <div class="table-wrap">
        <table>
            <thead>
                <tr><th>No.</th><th>ID Transaksi</th><th>Nama</th><th>Jenis</th><th>Nominal</th><th>Lembaga</th><th>Tanggal</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse ($transactions as $index => $trx)
                    @php
                        $jenis = $trx->jenisZiswaf->nama_jenis ?? 'Zakat';
                        $namaMember = $trx->user->name ?? 'Hamba Allah';
                        $typeBadge = $jenis === 'Zakat' ? 'badge-green' : ($jenis === 'Wakaf' ? 'badge-blue' : 'badge-gold');
                        $statusBadge = $trx->status === 'Tersalur' ? 'badge-green' : ($trx->status === 'Diproses' ? 'badge-gold' : 'badge-red');
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
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
                        <td colspan="8">
                            <div style="text-align:center; padding: 56px 20px;">
                                <div style="font-size:56px; margin-bottom:12px; opacity:0.6">📭</div>
                                <h4 style="font-family:'Playfair Display',serif; font-size:18px; font-weight:700; color:var(--g2); margin-bottom:6px;">Laporan Kosong</h4>
                                <p style="font-size:13px; color:var(--muted);">Tidak ada transaksi yang tercatat pada periode ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
      </div>
    </div>
</div>
@endsection