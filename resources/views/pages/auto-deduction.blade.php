@extends('layouts.app')

@section('title', 'Auto-Deduction - ZISWAF BMT')
@section('page-title', 'Auto-Deduction')
@section('page-subtitle', 'Kelola pemotongan zakat otomatis')

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
        <div class="card-title">⚡ Pengaturan Auto-Deduction Zakat</div>
        <span class="badge badge-green">Sistem Aktif</span>
      </div>
      <div class="card-body">
        <div class="grid-2">
          <div>
            <div class="input-group"><label class="input-label">Mode Pemotongan</label>
              <select class="input-field">
                <option>Bulanan (setiap tanggal 1)</option><option>Tahunan (saat haul terpenuhi)</option><option>Manual (notifikasi saja)</option>
              </select>
            </div>
            <div class="input-group"><label class="input-label">Lembaga Penerima Default</label>
              <select class="input-field"><option>BAZNAS Pusat</option><option>LAZ Dompet Dhuafa</option><option>BMT Pondok Hijau Langsung</option></select>
            </div>
          </div>
          <div>
            <div class="input-group"><label class="input-label">Acuan Nisab (Perak, 595 gram)</label>
              <div class="input-field" style="background:var(--g8); display:flex; align-items:center;">Rp {{ number_format($nisabPerak, 0, ',', '.') }}</div>
            </div>
            <div class="input-group"><label class="input-label">Tarif Zakat</label>
              <div class="input-field" style="background:var(--g8); display:flex; align-items:center;">{{ $persentase * 100 }}% dari saldo (mengikuti Kategori Zakat "Emas" di Pengaturan)</div>
            </div>
          </div>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap">
          <form action="{{ route('auto-deduction.run') }}" method="POST" onsubmit="return confirm('Jalankan pemotongan zakat otomatis untuk semua anggota yang memenuhi nisab & haul?');">
              @csrf
              <button type="submit" class="btn btn-primary">⚡ Jalankan Auto-Deduction Sekarang</button>
          </form>
          <button class="btn btn-outline" onclick="showToast('💾 Pengaturan disimpan!')">💾 Simpan Pengaturan</button>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <div class="card-title">Anggota Wajib Zakat (Siap Potong)</div>
        <span class="badge badge-gold" id="auto-deduction-count">{{ $anggotaWajib->count() }} Anggota</span>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
              <tr><th>ID</th><th>Nama</th><th>Saldo</th><th>Harta Bersih</th><th>Status Nisab</th><th>Zakat ({{ $persentase * 100 }}%)</th><th>Aksi</th></tr>
          </thead>
          <tbody id="auto-deduction-table">
              @forelse($anggotaWajib as $a)
                  @php $zakat = $a->saldo * $persentase; @endphp
                  <tr>
                      <td style="font-family:monospace;font-size:11.5px">BMT-{{ str_pad($a->id, 4, '0', STR_PAD_LEFT) }}</td>
                      <td style="font-weight:600">{{ $a->name }}</td>
                      <td>Rp {{ number_format($a->saldo / 1000000, 1, ',', '.') }} Jt</td>
                      <td>Rp {{ number_format($a->saldo / 1000000, 1, ',', '.') }} Jt</td>
                      <td><span class="badge badge-green">✅ Terpenuhi</span></td>
                      <td style="font-weight:800;color:var(--g3)">Rp {{ number_format($zakat, 0, ',', '.') }}</td>
                      <td>
                          <form action="{{ route('auto-deduction.potong', $a->id) }}" method="POST" style="margin:0" onsubmit="return confirm('Potong zakat {{ $a->name }} sebesar Rp ' + Number({{ $zakat }}).toLocaleString('id-ID') + '?');">
                              @csrf
                              <button type="submit" class="btn btn-primary btn-sm">⚡ Potong</button>
                          </form>
                      </td>
                  </tr>
              @empty
                  <tr><td colspan="7" style="text-align:center; padding:24px; color:var(--muted);">Belum ada anggota yang memenuhi nisab & haul. Atur saldo &amp; status haul anggota di halaman Data Anggota.</td></tr>
              @endforelse
          </tbody>
        </table>
      </div>
    </div>
</div>
@endsection
