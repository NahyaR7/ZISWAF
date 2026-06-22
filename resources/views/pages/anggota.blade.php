@extends('layouts.app')

@section('title', 'Data Anggota - ZISWAF BMT')
@section('page-title', 'Data Anggota')
@section('page-subtitle', 'Kelola data anggota ZISWAF')

@section('content')
<div style="padding: 32px 36px;">
    <div class="anggota-top">
      <input type="text" class="search-input" placeholder="🔍  Cari nama atau ID anggota..." oninput="filterAnggota(this.value)">
      <button class="btn btn-primary btn-sm" onclick="showToast('➕ Form tambah anggota dibuka')">➕ Tambah Anggota</button>
      <button class="btn btn-outline btn-sm" onclick="showToast('📥 Export Excel sedang diproses')">📥 Export</button>
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
                </tbody>
        </table>
      </div>
    </div>
</div>
@endsection