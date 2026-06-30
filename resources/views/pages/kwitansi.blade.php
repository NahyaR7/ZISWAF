@extends('layouts.app')

@section('title', 'Kwitansi Digital - ZISWAF BMT')
@section('page-title', 'Kwitansi Digital')
@section('page-subtitle', 'Lihat dan unduh kwitansi digital (UC 7)')

@section('content')
<div style="padding: 32px 36px;">
    <div class="hero-banner mb-24">
      <h2>🧾 Kwitansi Digital ZISWAF</h2>
      <p>Lihat dan unduh kwitansi digital transaksi ZISWAF Anda — diterbitkan otomatis setiap transaksi berhasil</p>
    </div>
    
    <div class="card mb-20">
      <div class="card-header"><div class="card-title">Cari Kwitansi</div></div>
      <div class="card-body" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
        <div style="flex:1;min-width:200px">
            <label class="input-label">No. Kwitansi / ID Transaksi</label>
            <input type="text" class="input-field" placeholder="e.g. KW-2026-0001" id="kwitansi-search">
        </div>
        <button class="btn btn-primary btn-sm" onclick="cariKwitansi()">🔍 Cari</button>
      </div>
    </div>
    
    <div id="kwitansi-result">
      <div class="card">
        <div class="card-header"><div class="card-title">Kwitansi Terbaru Anda</div></div>
        <div class="table-wrap">
          <table>
            <thead>
                <tr><th>No. Kwitansi</th><th>Jenis</th><th>Nominal</th><th>Lembaga</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($transactions as $trx)
                    @php
                        $jenis = $trx->jenisZiswaf->nama_jenis ?? 'Zakat';
                        $namaMember = $trx->user->name ?? 'Hamba Allah';
                        $metode = $trx->payment->metode ?? 'Transfer Bank';
                        $typeBadge = $jenis === 'Zakat' ? 'badge-green' : ($jenis === 'Wakaf' ? 'badge-blue' : 'badge-gold');
                    @endphp
                    <tr>
                        <td style="font-family:monospace">{{ $trx->kwitansi_number }}</td>
                        <td><span class="badge {{ $typeBadge }}">{{ $jenis }}</span></td>
                        <td style="font-weight:700">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td>{{ $trx->organization }}</td>
                        <td>{{ $trx->created_at->format('d M Y') }}</td>
                        <td><span class="badge badge-green">Tersalur</span></td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="viewKwitansi('{{ $trx->kwitansi_number }}', '{{ $trx->transaction_code }}', '{{ $trx->created_at->format('d M Y') }}', '{{ $jenis }}', '{{ $namaMember }}', '{{ $trx->organization }}', '{{ $metode }}', 'Rp {{ number_format($trx->amount, 0, ',', '.') }}')">👁 Lihat</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align: center; color: var(--muted);">Belum ada kwitansi tersedia</td></tr>
                @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
    <div id="kwitansi-detail-view" style="display:none;margin-top:20px">
      <div class="kwitansi-card">
        <div class="kwitansi-header">
          <div>
            <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:700">BMT Pondok Hijau</div>
            <div style="font-size:12px;opacity:0.7;margin-top:2px">Koperasi Syariah — Sistem ZISWAF Digital</div>
          </div>
          <div style="text-align:right">
            <div class="badge badge-green" style="font-size:13px">✅ LUNAS</div>
            <div style="font-size:12px;opacity:0.65;margin-top:6px" id="kw-detail-id"></div>
          </div>
        </div>
        <div style="text-align:center;padding:12px;font-family:'Amiri',serif;font-size:18px;color:var(--gold);background:var(--g8);border-bottom:1px solid var(--border)">بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ</div>
        <div class="kwitansi-body" id="kwitansi-detail-body"></div>
        <div style="padding:0 24px 24px;display:flex;gap:10px;flex-wrap:wrap">
          <button class="btn btn-primary" onclick="showToast('📥 PDF diunduh')">📥 Unduh PDF</button>
          <button class="btn btn-outline" onclick="showToast('📨 Dikirim ke email')">📨 Email</button>
          <button class="btn btn-ghost" onclick="document.getElementById('kwitansi-detail-view').style.display='none'">✕ Tutup</button>
        </div>
      </div>
    </div>
</div>
@endsection