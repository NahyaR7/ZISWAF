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
<<<<<<< HEAD
          <select class="input-field"><option>Semua</option><option>Tersalur</option><option>Menunggu Penyaluran</option><option>Diproses</option></select>
=======
          <select class="input-field"><option>Semua</option><option>Tersalur</option><option>Diproses</option><option>Gagal</option></select>
>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
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
<<<<<<< HEAD
                        
                        if ($trx->status === 'Tersalur') $statusBadge = 'badge-green';
                        elseif ($trx->status === 'Menunggu Penyaluran') $statusBadge = 'badge-blue';
                        else $statusBadge = 'badge-gold';
=======
                        $statusBadge = $trx->status === 'Tersalur' ? 'badge-green' : ($trx->status === 'Diproses' ? 'badge-gold' : 'badge-red');
>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
                    @endphp
                    <tr>
                        <td style="font-family:monospace;font-size:11.5px">{{ $trx->transaction_code }}</td>
                        <td style="font-weight:600">{{ $namaMember }}</td>
                        <td><span class="badge {{ $typeBadge }}">{{ $jenis }}</span></td>
                        <td style="font-weight:700;color:var(--g2)">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td style="font-size:12px;color:var(--muted)">{{ $metode }}</td>
                        <td style="font-size:12px;color:var(--muted)">{{ $trx->created_at->format('d M Y') }}</td>
                        <td><span class="badge {{ $statusBadge }}">{{ $trx->status }}</span></td>
<<<<<<< HEAD
                        <td style="display:flex; gap:6px; align-items:center;">
                            <button class="btn btn-outline btn-sm" onclick="viewKwitansi('{{ $trx->kwitansi_number ?? '-' }}', '{{ $trx->transaction_code }}', '{{ $trx->created_at->format('d M Y') }}', '{{ $jenis }}', '{{ $namaMember }}', '{{ $trx->organization }}', '{{ $metode }}', 'Rp {{ number_format($trx->amount, 0, ',', '.') }}')" title="Lihat Kwitansi">🧾</button>
                            
                            @if(auth()->user()->role === 'admin')
                                @if($trx->status === 'Diproses')
                                    <form action="{{ route('transaksi.verifikasi', $trx->id) }}" method="POST" style="margin:0" onsubmit="return confirm('Verifikasi bukti transfer ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm" title="Verifikasi Uang Masuk">✅ Verifikasi</button>
                                    </form>
                                @elseif($trx->status === 'Menunggu Penyaluran')
                                    <button class="btn btn-gold btn-sm" onclick="document.getElementById('modal-penyaluran-{{ $trx->id }}').classList.add('open')" title="Sematkan Dokumentasi Penyaluran">📦 Upload Bukti</button>
                                @elseif($trx->status === 'Tersalur')
                                    <span class="badge badge-green" style="cursor:default;" title="Dokumentasi penyaluran telah terbit">📸 Selesai</span>
                                @endif
                            @endif
                        </td>
                    </tr>

                    @if(auth()->user()->role === 'admin' && $trx->status === 'Menunggu Penyaluran')
                    <div class="modal-overlay" id="modal-penyaluran-{{ $trx->id }}">
                        <div class="modal">
                            <div class="modal-header">
                                <h3>Sematkan Bukti Penyaluran</h3>
                                <p>ID: {{ $trx->transaction_code }} &bull; Muzakki: {{ $namaMember }}</p>
                                <button class="modal-close" type="button" onclick="document.getElementById('modal-penyaluran-{{ $trx->id }}').classList.remove('open')">✕</button>
                            </div>
                            <form action="{{ route('transaksi.penyaluran', $trx->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <div class="input-group">
                                        <label class="input-label">Berkas Bukti (Foto / Video)</label>
                                        <input type="file" name="bukti_penyaluran" class="input-field" accept="image/*,video/mp4" required>
                                        <small style="color:var(--muted); font-size:11px; display:block; margin-top:4px;">Batas Maks: 15MB.</small>
                                    </div>
                                    <div class="input-group">
                                        <label class="input-label">Deskripsi Penyaluran</label>
                                        <textarea name="keterangan_penyaluran" class="input-field" rows="4" style="resize:vertical;" placeholder="Laporan detail penyaluran dana..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">Terbitkan Laporan</button>
                                    <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('modal-penyaluran-{{ $trx->id }}').classList.remove('open')">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

=======
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
>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
                @empty
                    <tr>
                        <td colspan="8">
                            <div style="text-align:center; padding: 56px 20px;">
                                <div style="font-size:56px; margin-bottom:12px; opacity:0.6">📭</div>
                                <h4 style="font-family:'Playfair Display',serif; font-size:18px; font-weight:700; color:var(--g2); margin-bottom:6px;">Belum Ada Transaksi</h4>
<<<<<<< HEAD
=======
                                <p style="font-size:13px; color:var(--muted);">Tidak ada riwayat transaksi ZISWAF yang ditemukan.</p>
>>>>>>> 43eb9314b80869898d386f72920947b2fe795e46
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