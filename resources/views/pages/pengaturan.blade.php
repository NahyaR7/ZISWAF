@extends('layouts.app')

@section('title', 'Pengaturan - ZISWAF BMT')
@section('page-title', 'Pengaturan Sistem')
@section('page-subtitle', 'Konfigurasi sistem, akun, dan data referensi ZISWAF')

@section('content')
<div style="padding: 32px 36px;">
    @if(session('success'))
    <div class="alert alert-success mb-24" style="animation: fadeIn 0.5s ease;">
        <span style="font-size:20px">✅</span>
        <div><strong>Berhasil!</strong> {{ session('success') }}</div>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger mb-24" style="animation: fadeIn 0.5s ease;">
        <span style="font-size:20px">⚠️</span>
        <div>{{ $errors->first() }}</div>
    </div>
    @endif

    <div class="grid-2">
      <div>
        @if(auth()->user()->role === 'admin')
        <div class="card mb-20">
          <div class="card-header">
              <div class="card-title">💳 Rekening Penampung BMT</div>
              <button class="btn btn-outline btn-sm" onclick="openModal('modal-tambah-rekening')">+ Tambah</button>
          </div>
          <div class="card-body" style="padding:0">
            <table style="width:100%; text-align:left; border-collapse:collapse;">
                <thead style="background:var(--g8); border-bottom:1px solid var(--border);">
                    <tr><th style="padding:12px 16px; font-size:12px;">Bank</th><th style="padding:12px 16px; font-size:12px;">No. Rekening</th><th style="padding:12px 16px; font-size:12px;">Atas Nama</th><th style="padding:12px 16px; font-size:12px;"></th></tr>
                </thead>
                <tbody>
                    @forelse($rekeningBMT as $rek)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 16px; font-weight:600; font-size:13px;">{{ $rek->nama_bank }}</td>
                        <td style="padding:12px 16px; font-family:monospace; font-size:14px; color:var(--g3)">{{ $rek->no_rekening }}</td>
                        <td style="padding:12px 16px; font-size:12px; color:var(--muted)">{{ $rek->atas_nama }}</td>
                        <td style="padding:12px 16px;">
                            <form action="{{ route('rekening.destroy', $rek->id) }}" method="POST" style="margin:0" onsubmit="return confirm('Hapus rekening ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" title="Hapus">🗑</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding:16px; text-align:center;">Belum ada rekening tujuan.</td></tr>
                    @endforelse
                </tbody>
            </table>
          </div>
        </div>

        <div class="card mb-20">
          <div class="card-header">
              <div class="card-title">🏷️ Kategori & Persentase Zakat</div>
              <button class="btn btn-outline btn-sm" onclick="openModal('modal-tambah-kategori')">+ Tambah</button>
          </div>
          <div class="card-body" style="padding:0">
            <table style="width:100%; text-align:left; border-collapse:collapse;">
                <thead style="background:var(--g8); border-bottom:1px solid var(--border);">
                    <tr><th style="padding:12px 16px; font-size:12px;">Nama Kategori</th><th style="padding:12px 16px; font-size:12px;">Nisab (Rp)</th><th style="padding:12px 16px; font-size:12px;">Tarif (%)</th><th style="padding:12px 16px; font-size:12px;"></th></tr>
                </thead>
                <tbody>
                    @forelse($kategoriZakat as $kat)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 16px; font-weight:600; font-size:13px;">{{ $kat->nama_kategori }}</td>
                        <td style="padding:12px 16px; font-size:13px; color:var(--g2)">{{ $kat->nisab ? number_format($kat->nisab, 0, ',', '.') : '-' }}</td>
                        <td style="padding:12px 16px;"><span class="badge badge-gold">{{ $kat->persentase }}%</span></td>
                        <td style="padding:12px 16px;">
                            <form action="{{ route('kategori.destroy', $kat->id) }}" method="POST" style="margin:0" onsubmit="return confirm('Hapus kategori ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" title="Hapus">🗑</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding:16px; text-align:center;">Belum ada kategori zakat.</td></tr>
                    @endforelse
                </tbody>
            </table>
          </div>
        </div>

        <div class="card mb-20">
          <div class="card-header">
              <div class="card-title">🐄 Jenis Harta Zakat</div>
              <button class="btn btn-outline btn-sm" onclick="openModal('modal-tambah-jenis-harta')">+ Tambah</button>
          </div>
          <div class="card-body" style="padding:0">
            <table style="width:100%; text-align:left; border-collapse:collapse;">
                <thead style="background:var(--g8); border-bottom:1px solid var(--border);">
                    <tr><th style="padding:12px 16px; font-size:12px;">Nama Jenis Harta</th><th style="padding:12px 16px; font-size:12px;">Deskripsi</th><th style="padding:12px 16px; font-size:12px;"></th></tr>
                </thead>
                <tbody>
                    @forelse($jenisHarta as $jh)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 16px; font-weight:600; font-size:13px;">{{ $jh->nama_jenis_harta }}</td>
                        <td style="padding:12px 16px; font-size:12px; color:var(--muted)">{{ $jh->deskripsi ?? '-' }}</td>
                        <td style="padding:12px 16px;">
                            <form action="{{ route('jenis_harta.destroy', $jh->id) }}" method="POST" style="margin:0" onsubmit="return confirm('Hapus jenis harta ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" title="Hapus">🗑</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="padding:16px; text-align:center;">Belum ada jenis harta.</td></tr>
                    @endforelse
                </tbody>
            </table>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
              <div class="card-title">💰 Harga Harta</div>
              <button class="btn btn-outline btn-sm" onclick="openModal('modal-tambah-harga-harta')">+ Tambah</button>
          </div>
          <div class="card-body" style="padding:0">
            <table style="width:100%; text-align:left; border-collapse:collapse;">
                <thead style="background:var(--g8); border-bottom:1px solid var(--border);">
                    <tr><th style="padding:12px 16px; font-size:12px;">Jenis Harta</th><th style="padding:12px 16px; font-size:12px;">Tanggal</th><th style="padding:12px 16px; font-size:12px;">Harga (Rp)</th><th style="padding:12px 16px; font-size:12px;"></th></tr>
                </thead>
                <tbody>
                    @forelse($hargaHarta as $hh)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:12px 16px; font-weight:600; font-size:13px;">{{ $hh->jenisHarta->nama_jenis_harta ?? '-' }}</td>
                        <td style="padding:12px 16px; font-size:12px; color:var(--muted)">{{ \Carbon\Carbon::parse($hh->tanggal)->format('d M Y') }}</td>
                        <td style="padding:12px 16px; font-size:13px; color:var(--g2)">{{ number_format($hh->harga, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px;">
                            <form action="{{ route('harga_harta.destroy', $hh->id) }}" method="POST" style="margin:0" onsubmit="return confirm('Hapus harga ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" title="Hapus">🗑</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding:16px; text-align:center;">Belum ada data harga harta.</td></tr>
                    @endforelse
                </tbody>
            </table>
          </div>
        </div>
        @endif
      </div>

      <div>
        <div class="card mb-20">
          <div class="card-header"><div class="card-title">🔐 Keamanan Akun</div></div>
          <div class="card-body">
            <form action="{{ route('account.password') }}" method="POST">
                @csrf
                <div class="input-group"><label class="input-label">Password Lama</label><input type="password" name="password_lama" class="input-field" placeholder="••••••••" required></div>
                <div class="input-group"><label class="input-label">Password Baru</label><input type="password" name="password_baru" class="input-field" placeholder="••••••••" minlength="8" required></div>
                <div class="input-group"><label class="input-label">Konfirmasi Password</label><input type="password" name="password_baru_confirmation" class="input-field" placeholder="••••••••" minlength="8" required></div>
                <button type="submit" class="btn btn-primary btn-sm">Perbarui Password</button>
            </form>
          </div>
        </div>

        @if(auth()->user()->role === 'admin')
        <div class="card mb-20">
          <div class="card-header"><div class="card-title">🏛 Pengaturan Lembaga</div></div>
          <div class="card-body">
            <form action="{{ route('lembaga.update') }}" method="POST">
                @csrf
                <div class="input-group"><label class="input-label">Nama Lembaga</label><input type="text" name="nama_lembaga" class="input-field" value="{{ $lembaga->nama_lembaga }}" required></div>
                <div class="input-group"><label class="input-label">Alamat</label><input type="text" name="alamat" class="input-field" value="{{ $lembaga->alamat }}"></div>
                <div class="input-group"><label class="input-label">Email Resmi</label><input type="email" name="email" class="input-field" value="{{ $lembaga->email }}"></div>
                <button type="submit" class="btn btn-primary btn-sm">Simpan Profil</button>
            </form>
          </div>
        </div>
        @endif

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

@if(auth()->user()->role === 'admin')
<div class="modal-overlay" id="modal-tambah-rekening">
  <div class="modal">
    <div class="modal-header">
        <h3>Tambah Rekening BMT</h3>
        <button class="modal-close" type="button" onclick="closeModal('modal-tambah-rekening')">✕</button>
    </div>
    <form action="{{ route('rekening.store') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="input-group"><label class="input-label">Nama Bank</label><input type="text" name="nama_bank" class="input-field" required></div>
            <div class="input-group"><label class="input-label">No. Rekening</label><input type="text" name="no_rekening" class="input-field" required></div>
            <div class="input-group"><label class="input-label">Atas Nama</label><input type="text" name="atas_nama" class="input-field" required></div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Simpan</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="closeModal('modal-tambah-rekening')">Batal</button>
        </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="modal-tambah-kategori">
  <div class="modal">
    <div class="modal-header">
        <h3>Tambah Kategori Zakat</h3>
        <button class="modal-close" type="button" onclick="closeModal('modal-tambah-kategori')">✕</button>
    </div>
    <form action="{{ route('kategori.store') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="input-group"><label class="input-label">Nama Kategori</label><input type="text" name="nama_kategori" class="input-field" required></div>
            <div class="input-group"><label class="input-label">Nisab (Rp)</label><input type="number" name="nisab" class="input-field" min="0" step="0.01"></div>
            <div class="input-group"><label class="input-label">Tarif (%)</label><input type="number" name="persentase" class="input-field" value="2.5" min="0" max="100" step="0.01" required></div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Simpan</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="closeModal('modal-tambah-kategori')">Batal</button>
        </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="modal-tambah-jenis-harta">
  <div class="modal">
    <div class="modal-header">
        <h3>Tambah Jenis Harta</h3>
        <button class="modal-close" type="button" onclick="closeModal('modal-tambah-jenis-harta')">✕</button>
    </div>
    <form action="{{ route('jenis_harta.store') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="input-group"><label class="input-label">Nama Jenis Harta</label><input type="text" name="nama_jenis_harta" class="input-field" placeholder="Sapi, Kambing, Gabah, dll" required></div>
            <div class="input-group"><label class="input-label">Deskripsi</label><textarea name="deskripsi" class="input-field" rows="3"></textarea></div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Simpan</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="closeModal('modal-tambah-jenis-harta')">Batal</button>
        </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="modal-tambah-harga-harta">
  <div class="modal">
    <div class="modal-header">
        <h3>Tambah Harga Harta</h3>
        <button class="modal-close" type="button" onclick="closeModal('modal-tambah-harga-harta')">✕</button>
    </div>
    <form action="{{ route('harga_harta.store') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="input-group">
                <label class="input-label">Jenis Harta</label>
                <select name="jenis_harta_id" class="input-field" required>
                    <option value="">-- Pilih Jenis Harta --</option>
                    @foreach($jenisHarta as $jh)
                        <option value="{{ $jh->id }}">{{ $jh->nama_jenis_harta }}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group"><label class="input-label">Tanggal</label><input type="date" name="tanggal" class="input-field" value="{{ date('Y-m-d') }}" required></div>
            <div class="input-group"><label class="input-label">Harga (Rp)</label><input type="number" name="harga" class="input-field" min="0" step="0.01" required></div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Simpan</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="closeModal('modal-tambah-harga-harta')">Batal</button>
        </div>
    </form>
  </div>
</div>
@endif
@endsection
