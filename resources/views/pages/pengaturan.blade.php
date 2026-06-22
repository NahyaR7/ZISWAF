@extends('layouts.app')

@section('title', 'Pengaturan - ZISWAF BMT')
@section('page-title', 'Pengaturan Sistem')
@section('page-subtitle', 'Konfigurasi sistem, akun, dan data referensi ZISWAF')

@section('content')
<div style="padding: 32px 36px;">
    <div class="grid-2">
      <div>
        <div class="card mb-20">
          <div class="card-header"><div class="card-title">⚙️ Pengaturan Tampilan & Sistem</div></div>
          <div class="card-body">
            <div class="setting-row">
              <div class="setting-info"><h4>Mode Gelap (Dark Mode)</h4><p>Ubah tampilan ke mode gelap</p></div>
              <button class="toggle" id="dark-toggle" onclick="toggleTheme();this.classList.toggle('on')"></button>
            </div>
            <div class="setting-row">
              <div class="setting-info"><h4>Auto-Update Harga Emas</h4><p>Sinkronisasi dengan API harga emas setiap hari</p></div>
              <button class="toggle on" onclick="this.classList.toggle('on')"></button>
            </div>
          </div>
        </div>

        <div class="card mb-20">
          <div class="card-header">
              <div class="card-title">💳 Rekening Penampung BMT</div>
              <button class="btn btn-outline btn-sm">+ Tambah</button>
          </div>
          <div class="card-body" style="padding:0">
            <table style="width:100%; text-align:left; border-collapse:collapse;">
                <thead style="background:var(--g8); border-bottom:1px solid var(--border);">
                    <tr><th style="padding:12px 16px; font-size:12px;">Bank</th><th style="padding:12px 16px; font-size:12px;">No. Rekening</th><th style="padding:12px 16px; font-size:12px;">Atas Nama</th></tr>
                </thead>
                <tbody>
                    @forelse($rekeningBMT as $rek)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 16px; font-weight:600; font-size:13px;">{{ $rek->nama_bank }}</td>
                        <td style="padding:12px 16px; font-family:monospace; font-size:14px; color:var(--g3)">{{ $rek->no_rekening }}</td>
                        <td style="padding:12px 16px; font-size:12px; color:var(--muted)">{{ $rek->atas_nama }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="padding:16px; text-align:center;">Belum ada rekening tujuan.</td></tr>
                    @endforelse
                </tbody>
            </table>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
              <div class="card-title">🏷️ Kategori & Persentase Zakat</div>
              <button class="btn btn-outline btn-sm">+ Tambah</button>
          </div>
          <div class="card-body" style="padding:0">
            <table style="width:100%; text-align:left; border-collapse:collapse;">
                <thead style="background:var(--g8); border-bottom:1px solid var(--border);">
                    <tr><th style="padding:12px 16px; font-size:12px;">Nama Kategori</th><th style="padding:12px 16px; font-size:12px;">Nisab (Rp)</th><th style="padding:12px 16px; font-size:12px;">Tarif (%)</th></tr>
                </thead>
                <tbody>
                    @forelse($kategoriZakat as $kat)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 16px; font-weight:600; font-size:13px;">{{ $kat->nama_kategori }}</td>
                        <td style="padding:12px 16px; font-size:13px; color:var(--g2)">{{ $kat->nisab ? number_format($kat->nisab, 0, ',', '.') : '-' }}</td>
                        <td style="padding:12px 16px;"><span class="badge badge-gold">{{ $kat->persentase }}%</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="padding:16px; text-align:center;">Belum ada kategori zakat.</td></tr>
                    @endforelse
                </tbody>
            </table>
          </div>
        </div>
      </div>
      
      <div>
        <div class="card mb-20">
          <div class="card-header"><div class="card-title">🔐 Keamanan Akun</div></div>
          <div class="card-body">
            <div class="input-group"><label class="input-label">Password Lama</label><input type="password" class="input-field" placeholder="••••••••"></div>
            <div class="input-group"><label class="input-label">Password Baru</label><input type="password" class="input-field" placeholder="••••••••"></div>
            <div class="input-group"><label class="input-label">Konfirmasi Password</label><input type="password" class="input-field" placeholder="••••••••"></div>
            <button class="btn btn-primary btn-sm" onclick="showToast('🔐 Password berhasil diperbarui!')">Perbarui Password</button>
          </div>
        </div>

        <div class="card mb-20">
          <div class="card-header"><div class="card-title">🏛 Pengaturan Lembaga</div></div>
          <div class="card-body">
            <div class="input-group"><label class="input-label">Nama Lembaga</label><input type="text" class="input-field" value="BMT Pondok Hijau"></div>
            <div class="input-group"><label class="input-label">Alamat</label><input type="text" class="input-field" value="Jl. Pondok Hijau No. 10, Depok"></div>
            <div class="input-group"><label class="input-label">Email Resmi</label><input type="email" class="input-field" value="admin@bmtpondokhijau.id"></div>
            <button class="btn btn-primary btn-sm" onclick="showToast('💾 Profil lembaga disimpan!')">Simpan Profil</button>
          </div>
        </div>
        
        <div class="card">
          <div class="card-header"><div class="card-title">📊 Info Sistem</div></div>
          <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:10px">
              <div class="setting-row" style="padding:8px 0"><div class="setting-info"><h4>Versi Sistem</h4></div><span class="badge badge-green">v2.1.0 Laravel</span></div>
              <div class="setting-row" style="padding:8px 0"><div class="setting-info"><h4>Status API Emas</h4></div><span class="badge badge-green">Terhubung</span></div>
              <div class="setting-row" style="padding:8px 0"><div class="setting-info"><h4>Status Payment Gateway</h4></div><span class="badge badge-green">Aktif</span></div>
              <div class="setting-row" style="padding:8px 0"><div class="setting-info"><h4>Database</h4></div><span class="badge badge-blue">Online</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection