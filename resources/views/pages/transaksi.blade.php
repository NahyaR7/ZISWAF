@extends('layouts.app')

@section('title', 'Riwayat Transaksi - ZISWAF BMT')
@section('page-title', 'Riwayat Transaksi')
@section('page-subtitle', 'Semua transaksi ZISWAF tercatat di sini')

@section('content')
<div style="padding: 32px 36px;">
    @if(session('success'))
    <div class="alert alert-success mb-24" style="animation: fadeIn 0.5s ease;">
        <span style="font-size:20px">✅</span>
        <div><strong>Berhasil!</strong> {{ session('success') }}</div>
    </div>
    @endif

    <div class="card mb-24">
      <div class="card-header"><div class="card-title">Filter Transaksi</div></div>
      <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
        <div style="flex:1;min-width:160px"><label class="input-label">Jenis</label>
          <select class="input-field"><option>Semua</option><option>Zakat</option><option>Infaq</option><option>Wakaf</option></select>
        </div>
        <div style="flex:1;min-width:160px"><label class="input-label">Status</label>
          <select class="input-field"><option>Semua</option><option>Tersalur</option><option>Diproses</option><option>Gagal</option></select>
        </div>
        <div style="flex:1;min-width:160px"><label class="input-label">Periode</label>
          <select class="input-field"><option>Mei 2026</option><option>April 2026</option><option>All Time</option></select>
        </div>
        <button class="btn btn-primary btn-sm" onclick="showToast('🔍 Filter diterapkan')">Terapkan</button>
      </div>
    </div>
    
    <div class="card">
      <div class="card-header">
        <div class="card-title">Riwayat Semua Transaksi ZISWAF</div>
        <button class="btn btn-outline btn-sm" onclick="showToast('📥 Export PDF / Excel diproses...')">📥 Export Laporan</button>
      </div>
      <div class="table-wrap">
        <table>
            <thead>
                <tr><th>ID</th><th>Anggota</th><th>Jenis</th><th>Nominal</th><th>Metode</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr>
            </thead>
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
                        <td style="font-weight:600">{{ $namaMember }}</td>
                        <td><span class="badge {{ $typeBadge }}">{{ $jenis }}</span></td>
                        <td style="font-weight:700;color:var(--g2)">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td style="font-size:12px;color:var(--muted)">{{ $metode }}</td>
                        <td style="font-size:12px;color:var(--muted)">{{ $trx->created_at->format('d M Y') }}</td>
                        <td><span class="badge {{ $statusBadge }}">{{ $trx->status }}</span></td>
                        <td style="display:flex; gap:6px;">
                            <button class="btn btn-outline btn-sm" onclick="viewKwitansi('{{ $trx->kwitansi_number ?? '-' }}', '{{ $trx->transaction_code }}', '{{ $trx->created_at->format('d M Y') }}', '{{ $jenis }}', '{{ $namaMember }}', '{{ $trx->organization }}', '{{ $metode }}', 'Rp {{ number_format($trx->amount, 0, ',', '.') }}')" title="Lihat Kwitansi">🧾</button>
                            
                            @if(auth()->user()->role === 'admin' && $trx->status === 'Diproses')
                                <form action="{{ route('transaksi.verifikasi', $trx->id) }}" method="POST" style="margin:0" onsubmit="return confirm('Verifikasi bukti transfer ini?');">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm" title="Verifikasi Transaksi">✅ Verifikasi</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div style="text-align:center; padding: 56px 20px;">
                                <div style="font-size:56px; margin-bottom:12px; opacity:0.6">📭</div>
                                <h4 style="font-family:'Playfair Display',serif; font-size:18px; font-weight:700; color:var(--g2); margin-bottom:6px;">Belum Ada Transaksi</h4>
                                <p style="font-size:13px; color:var(--muted);">Tidak ada riwayat transaksi ZISWAF yang ditemukan.</p>
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