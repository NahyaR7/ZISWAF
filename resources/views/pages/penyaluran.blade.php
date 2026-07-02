@extends('layouts.app')

@section('title', 'Transparansi Penyaluran - BMT Pondok Hijau')
@section('page-title', 'Tracking Penyaluran ZISWAF')
@section('page-subtitle', 'Pantau proses verifikasi dan laporan pendistribusian dana amanah Anda')

@section('content')
<div style="padding: 32px 36px;">
    
    <div class="alert alert-info" style="margin-bottom: 24px; display:flex; gap:12px; align-items:center;">
        <span style="font-size: 24px;">💡</span>
        <div><strong>Prinsip Akuntabilitas Syariah:</strong> Halaman ini memuat pelacakan (tracking) riil penyaluran dana yang telah Anda tunaikan, dari mulai verifikasi administrasi hingga dokumentasi pendistribusian kepada asnaf.</div>
    </div>

    @if($transactions->isEmpty())
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 64px 20px;">
                <div style="font-size: 64px; margin-bottom: 16px; opacity: 0.5;">📸</div>
                <h3 style="color: var(--g2); font-family: 'Playfair Display', serif; font-size:20px; font-weight:700;">Belum Ada Riwayat Transaksi</h3>
                <p style="color: var(--muted); font-size: 13px; max-width:450px; margin: 8px auto 0;">Silakan tunaikan ZISWAF Anda terlebih dahulu melalui menu Bayar ZISWAF.</p>
            </div>
        </div>
    @else
        <div class="grid-2">
            @foreach($transactions as $trx)
                @php
                    $jenis = $trx->jenisZiswaf->nama_jenis ?? 'ZISWAF';
                    $badgeColor = $jenis === 'Zakat' ? 'badge-green' : ($jenis === 'Wakaf' ? 'badge-blue' : 'badge-gold');
                @endphp
                <div class="card" style="display:flex; flex-direction:column; background:var(--white);">
                    
                    <!-- BAGIAN ATAS: MEDIA / STATUS TRACKING -->
                    @if($trx->status === 'Diproses')
                        <div style="background:var(--g8); height:240px; display:flex; flex-direction:column; align-items:center; justify-content:center; border-bottom:1px solid var(--border); position:relative;">
                            <div style="font-size:48px; margin-bottom:12px;">⏳</div>
                            <h4 style="color:var(--g2); font-weight:700; margin-bottom:4px;">Menunggu Verifikasi</h4>
                            <p style="font-size:12px; color:var(--muted); max-width:80%; text-align:center; margin:0;">Amil sedang memvalidasi dana masuk dari Anda.</p>
                            <div style="position:absolute; top:14px; left:14px;"><span class="badge {{ $badgeColor }}">{{ $jenis }}</span></div>
                        </div>
                    
                    @elseif($trx->status === 'Menunggu Penyaluran')
                        <div style="background:var(--g8); height:240px; display:flex; flex-direction:column; align-items:center; justify-content:center; border-bottom:1px solid var(--border); position:relative;">
                            <div style="font-size:48px; margin-bottom:12px;">🚚</div>
                            <h4 style="color:var(--g3); font-weight:700; margin-bottom:4px;">Menunggu Penyaluran</h4>
                            <p style="font-size:12px; color:var(--muted); max-width:80%; text-align:center; margin:0;">Pembayaran berhasil divalidasi. Dana ZISWAF Anda sedang dalam antrean pendistribusian.</p>
                            <div style="position:absolute; top:14px; left:14px;"><span class="badge {{ $badgeColor }}">{{ $jenis }}</span></div>
                        </div>

                    @elseif($trx->status === 'Tersalur')
                        @php $isMovementMedia = \Illuminate\Support\Str::endsWith($trx->bukti_penyaluran, ['.mp4', '.mov', '.webm']); @endphp
                        <div style="background:#000; height:240px; position:relative; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                            @if($isMovementMedia)
                                <video controls style="width:100%; height:100%; object-fit:cover;">
                                    <source src="{{ asset($trx->bukti_penyaluran) }}" type="video/mp4">
                                    Browser Anda tidak mendukung tag video.
                                </video>
                            @else
                                <img src="{{ asset($trx->bukti_penyaluran) }}" alt="Dokumentasi Penyaluran BMT" style="width:100%; height:100%; object-fit:cover;">
                            @endif
                            <div style="position:absolute; top:14px; left:14px; display:flex; gap:6px;">
                                <span class="badge {{ $badgeColor }}">{{ $jenis }}</span>
                                <span class="badge badge-green">✓ Telah Disalurkan</span>
                            </div>
                        </div>
                    @endif

                    <!-- BAGIAN BAWAH: DESKRIPSI & INFO NOMINAL -->
                    <div class="card-body" style="padding:20px 24px; flex:1; display:flex; flex-direction:column; justify-content:space-between;">
                        <div style="margin-bottom: 20px;">
                            @if($trx->status === 'Tersalur')
                                <div style="font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">
                                    TANGGAL DISTRIBUSI: {{ \Carbon\Carbon::parse($trx->tanggal_penyaluran)->translatedFormat('d F Y') }}
                                </div>
                                <h4 style="font-family:'Playfair Display',serif; font-size:18px; font-weight:700; color:var(--g2); margin:0 0 10px 0;">
                                    Penyaluran Dana {{ $jenis }} Anda
                                </h4>
                                <p style="font-size:13px; color:var(--text); line-height:1.6; text-align:justify; margin:0;">
                                    {{ $trx->keterangan_penyaluran }}
                                </p>
                            @else
                                <h4 style="font-family:'Playfair Display',serif; font-size:18px; font-weight:700; color:var(--g2); margin:0 0 10px 0;">
                                    Pembayaran Dana {{ $jenis }}
                                </h4>
                                <div style="background:var(--bg); border: 1px dashed var(--border); padding: 12px; border-radius: 8px; text-align:center;">
                                    <p style="font-size:12px; color:var(--muted); margin:0;">
                                        Dokumentasi penyaluran akan otomatis muncul di sini setelah Amil kami selesai mendistribusikan dana Anda di lapangan.
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div style="padding-top:14px; border-top:1px dashed var(--border); display:flex; justify-content:space-between; align-items:center; background:var(--white);">
                            <div>
                                <span style="font-size:10px; font-weight:700; color:var(--muted); text-transform:uppercase; display:block; letter-spacing:0.5px;">Nominal ZISWAF</span>
                                <span style="font-size:15px; font-weight:700; color:var(--g3);">Rp {{ number_format($trx->amount, 0, ',', '.') }}</span>
                            </div>
                            <div style="text-align:right;">
                                <span style="font-size:10px; font-weight:700; color:var(--muted); text-transform:uppercase; display:block; letter-spacing:0.5px;">Nomor Kwitansi</span>
                                <span style="font-size:12px; font-family:monospace; font-weight:600; color:var(--text)">
                                    {{ $trx->kwitansi_number ?? 'Belum Terbit' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection