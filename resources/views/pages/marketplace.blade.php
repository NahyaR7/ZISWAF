@extends('layouts.app')

@section('title', 'Marketplace Donasi - ZISWAF BMT')
@section('page-title', 'Marketplace Donasi')
@section('page-subtitle', 'Salurkan donasi Anda ke program terpercaya')

@section('content')
<div style="padding: 32px 36px;">
    @if(session('success'))
    <div class="alert alert-success mb-24" style="animation: fadeIn 0.5s ease;">
        <span style="font-size:20px">✅</span>
        <div><strong>Berhasil!</strong> {{ session('success') }}</div>
    </div>
    @endif

    <div class="hero-banner mb-24">
      <h2>🕌 Marketplace Donasi ZISWAF</h2>
      <p>Salurkan zakat, infaq, sedekah, dan wakaf Anda ke program-program terpercaya dari lembaga mitra resmi BMT Pondok Hijau</p>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
        <div class="filter-bar" style="margin:0">
          <button class="filter-btn active" onclick="filterProgram('semua',this)">🌟 Semua</button>
          <button class="filter-btn" onclick="filterProgram('zakat',this)">🕌 Zakat</button>
          <button class="filter-btn" onclick="filterProgram('infaq',this)">💛 Infaq & Sedekah</button>
          <button class="filter-btn" onclick="filterProgram('wakaf',this)">🌿 Wakaf</button>
          <button class="filter-btn" onclick="filterProgram('sosial',this)">🤲 Sosial</button>
        </div>
        @if(auth()->user()->role === 'admin')
        <button class="btn btn-primary btn-sm" onclick="openModal('modal-tambah-program')">+ Tambah Program</button>
        @endif
    </div>

    <div class="program-grid" id="program-grid">
        @forelse($programs as $p)
            @php
                $collected = $p->collected();
                $pct = $p->target > 0 ? min(($collected / $p->target) * 100, 100) : 0;
                $bg = match($p->tipe) { 'zakat' => 'prog-zakat', 'wakaf' => 'prog-wakaf', 'sosial' => 'prog-sosial', default => 'prog-infaq' };
            @endphp
            <div class="program-card" data-tipe="{{ $p->tipe }}">
              <div class="program-img {{ $bg }}" style="position:relative">
                <span>{{ $p->icon }}</span>
                @if(auth()->user()->role === 'admin')
                <form action="{{ route('program.destroy', $p->id) }}" method="POST" style="position:absolute; top:10px; right:10px; margin:0;" onsubmit="return confirm('Hapus program ini?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-ghost btn-sm" style="background:rgba(255,255,255,0.85)" title="Hapus">🗑</button>
                </form>
                @endif
              </div>
              <div class="program-body">
                <div class="program-name">{{ $p->nama }}</div>
                <div class="program-org">🏛 {{ $p->organisasi }}</div>
                <div class="progress-bar-wrap"><div class="progress-fill {{ $p->tipe === 'infaq' || $p->tipe === 'wakaf' ? 'gold' : '' }}" style="width:{{ $pct }}%"></div></div>
                <div class="prog-amounts">
                  <div><span class="prog-collected">Rp {{ number_format($collected / 1000000, 1, ',', '.') }} Jt</span> terkumpul</div>
                  <div>Target Rp {{ number_format($p->target / 1000000, 0, ',', '.') }} Jt</div>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center">
                  <span style="font-size:12px;color:var(--muted)">{{ round($pct) }}% tercapai</span>
                  <button class="btn btn-primary btn-sm" onclick="openModalDonasi({{ $p->id }}, '{{ $p->nama }}', '{{ $p->organisasi }}')">Donasi ➤</button>
                </div>
              </div>
            </div>
        @empty
            <div class="card" style="grid-column:1/-1">
                <div class="card-body" style="text-align:center; padding:56px 20px;">
                    <div style="font-size:48px; margin-bottom:12px; opacity:0.6">🕌</div>
                    <h4 style="font-family:'Playfair Display',serif; font-size:18px; font-weight:700; color:var(--g2); margin-bottom:6px;">Belum Ada Program Donasi</h4>
                    <p style="font-size:13px; color:var(--muted);">
                        @if(auth()->user()->role === 'admin')
                            Klik "+ Tambah Program" untuk menambahkan program donasi baru.
                        @else
                            Program donasi akan segera hadir.
                        @endif
                    </p>
                </div>
            </div>
        @endforelse
    </div>
</div>

@if(auth()->user()->role === 'admin')
<div class="modal-overlay" id="modal-tambah-program">
  <div class="modal">
    <div class="modal-header">
        <h3>Tambah Program Donasi</h3>
        <button class="modal-close" type="button" onclick="closeModal('modal-tambah-program')">✕</button>
    </div>
    <form action="{{ route('program.store') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="input-group"><label class="input-label">Nama Program</label><input type="text" name="nama" class="input-field" required></div>
            <div class="input-group"><label class="input-label">Lembaga / Organisasi</label><input type="text" name="organisasi" class="input-field" required></div>
            <div class="input-group">
                <label class="input-label">Tipe</label>
                <select name="tipe" class="input-field" required>
                    <option value="zakat">Zakat</option>
                    <option value="infaq">Infaq & Sedekah</option>
                    <option value="wakaf">Wakaf</option>
                    <option value="sosial">Sosial</option>
                </select>
            </div>
            <div class="input-group"><label class="input-label">Icon (emoji)</label><input type="text" name="icon" class="input-field" value="🕌" maxlength="4"></div>
            <div class="input-group"><label class="input-label">Target Donasi (Rp)</label><input type="number" name="target" class="input-field" min="0" step="0.01" required></div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Simpan</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="closeModal('modal-tambah-program')">Batal</button>
        </div>
    </form>
  </div>
</div>
@endif
@endsection
