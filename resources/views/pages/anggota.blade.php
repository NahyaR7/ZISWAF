@extends('layouts.app')

@section('title', 'Data Anggota - ZISWAF BMT')
@section('page-title', 'Data Anggota')
@section('page-subtitle', 'Kelola data anggota ZISWAF')

@section('content')
<div style="padding: 32px 36px;">
    @if(session('success'))
    <div class="alert alert-success mb-24" style="animation: fadeIn 0.5s ease;">
        <span style="font-size:20px">✅</span>
        <div><strong>Berhasil!</strong> {{ session('success') }}</div>
    </div>
    @endif

    <div class="anggota-top">
      <input type="text" class="search-input" placeholder="🔍  Cari nama atau email anggota..." oninput="filterAnggota(this.value)">
      <button class="btn btn-primary btn-sm" onclick="openModal('modal-tambah-anggota')">➕ Tambah Anggota</button>
      <a href="{{ route('anggota.export') }}" class="btn btn-outline btn-sm" style="text-decoration:none;">📥 Export</a>
    </div>

    <div class="card">
      <div class="card-header">
        <div class="card-title">Daftar Anggota ZISWAF</div>
        <div style="display:flex;gap:8px;align-items:center;font-size:12px;color:var(--muted)">
          <div style="display:flex;align-items:center;gap:4px"><div style="width:8px;height:8px;border-radius:50%;background:var(--g4)"></div>Wajib Zakat</div>
          <div style="display:flex;align-items:center;gap:4px"><div style="width:8px;height:8px;border-radius:50%;background:var(--gold)"></div>Belum Haul</div>
          <div style="display:flex;align-items:center;gap:4px"><div style="width:8px;height:8px;border-radius:50%;background:#e74c3c"></div>Belum Nisab</div>
        </div>
      </div>
      <div class="table-wrap">
        <table>
            <thead>
                <tr><th>ID</th><th>Nama</th><th>Saldo</th><th>Status Nisab</th><th>Auto-Deduct</th><th>Total Disetor</th><th>Aksi</th></tr>
            </thead>
            <tbody id="anggota-table">
                @forelse($anggota as $a)
                    @php
                        $saldo = $a->saldo ?? 0;
                        $nisabTercapai = $saldo >= $nisabPerak;
                        $wajib = $nisabTercapai && $a->haul_terpenuhi;
                    @endphp
                    <tr data-name="{{ strtolower($a->name) }}" data-email="{{ strtolower($a->email) }}">
                        <td style="font-family:monospace;font-size:11.5px">BMT-{{ str_pad($a->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td style="font-weight:600">{{ $a->name }}</td>
                        <td style="font-weight:700">Rp {{ number_format($saldo / 1000000, 1, ',', '.') }} Jt</td>
                        <td><span class="badge {{ $wajib ? 'badge-green' : ($nisabTercapai ? 'badge-gold' : 'badge-red') }}">{{ $wajib ? '✅ Wajib Zakat' : ($nisabTercapai ? '⏳ Belum Haul' : '❌ Belum Nisab') }}</span></td>
                        <td><span class="badge {{ $a->haul_terpenuhi ? 'badge-green' : 'badge-red' }}">{{ $a->haul_terpenuhi ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td style="font-weight:700;color:var(--g3)">Rp {{ number_format(($a->total_disetor ?? 0) / 1000000, 1, ',', '.') }} Jt</td>
                        <td>
                            <button class="btn btn-outline btn-sm" onclick="bukaDetailAnggota({{ $a->id }}, '{{ $a->name }}', {{ $saldo }}, {{ $a->haul_terpenuhi ? 'true' : 'false' }}, {{ $a->transactions_count }})">Detail</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center; padding:24px; color:var(--muted);">Belum ada anggota terdaftar.</td></tr>
                @endforelse
            </tbody>
        </table>
      </div>
    </div>
</div>

<div class="modal-overlay" id="modal-tambah-anggota">
  <div class="modal">
    <div class="modal-header">
        <h3>Tambah Anggota Baru</h3>
        <button class="modal-close" type="button" onclick="closeModal('modal-tambah-anggota')">✕</button>
    </div>
    <form action="{{ route('anggota.store') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="input-group"><label class="input-label">Nama Lengkap</label><input type="text" name="name" class="input-field" required></div>
            <div class="input-group"><label class="input-label">Username</label><input type="text" name="username" class="input-field" required></div>
            <div class="input-group"><label class="input-label">Email</label><input type="email" name="email" class="input-field" required></div>
            <div class="input-group"><label class="input-label">No. HP</label><input type="text" name="no_hp" class="input-field"></div>
            <div class="input-group"><label class="input-label">Password Awal</label><input type="password" name="password" class="input-field" minlength="8" required></div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Simpan</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="closeModal('modal-tambah-anggota')">Batal</button>
        </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="modal-detail-anggota">
  <div class="modal">
    <div class="modal-header">
        <h3 id="detail-anggota-nama">Detail Anggota</h3>
        <p id="detail-anggota-info"></p>
        <button class="modal-close" type="button" onclick="closeModal('modal-detail-anggota')">✕</button>
    </div>
    <form id="form-detail-anggota" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
            <div class="input-group">
                <label class="input-label">Saldo Tabungan (Rp)</label>
                <input type="number" name="saldo" id="detail-anggota-saldo" class="input-field" min="0" step="0.01">
                <small style="color:var(--muted); font-size:11px;">Dipakai sebagai acuan nisab & Auto-Deduction zakat.</small>
            </div>
            <div class="setting-row" style="padding:8px 0">
                <div class="setting-info"><h4>Haul Terpenuhi</h4><p>Tandai jika saldo sudah mengendap 1 tahun</p></div>
                <label style="display:flex;align-items:center;gap:6px">
                    <input type="checkbox" name="haul_terpenuhi" id="detail-anggota-haul" value="1">
                </label>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Simpan Perubahan</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="closeModal('modal-detail-anggota')">Tutup</button>
        </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function filterAnggota(value) {
    const q = value.trim().toLowerCase();
    document.querySelectorAll('#anggota-table tr[data-name]').forEach(row => {
        const match = row.dataset.name.includes(q) || row.dataset.email.includes(q);
        row.style.display = match ? '' : 'none';
    });
}

function bukaDetailAnggota(id, nama, saldo, haul, jumlahTransaksi) {
    document.getElementById('detail-anggota-nama').textContent = nama;
    document.getElementById('detail-anggota-info').textContent = jumlahTransaksi + ' transaksi tercatat';
    document.getElementById('detail-anggota-saldo').value = saldo;
    document.getElementById('detail-anggota-haul').checked = haul;
    document.getElementById('form-detail-anggota').action = '/anggota/' + id;
    openModal('modal-detail-anggota');
}
</script>
@endpush
@endsection
