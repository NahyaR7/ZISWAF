@extends('layouts.app')

@section('title', 'Auto-Deduction - ZISWAF BMT')
@section('page-title', 'Auto-Deduction')
@section('page-subtitle', 'Kelola pemotongan zakat otomatis')

@section('content')
<div style="padding: 32px 36px;">
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
            <div class="input-group"><label class="input-label">Acuan Nisab</label>
              <select class="input-field"><option>Emas (85 gram) — Rp 114.325.000</option><option>Perak (595 gram) — Rp 15.819.860</option></select>
            </div>
            <div class="input-group"><label class="input-label">Notifikasi Email/SMS</label>
              <select class="input-field"><option>Aktifkan Keduanya</option><option>Email Saja</option><option>SMS Saja</option><option>Nonaktifkan</option></select>
            </div>
          </div>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap">
          <button class="btn btn-primary" onclick="jalankanAutoDeduction()">⚡ Jalankan Auto-Deduction Sekarang</button>
          <button class="btn btn-outline" onclick="showToast('💾 Pengaturan disimpan!')">💾 Simpan Pengaturan</button>
        </div>
      </div>
    </div>
    
    <div class="card">
      <div class="card-header">
        <div class="card-title">Anggota Wajib Zakat (Siap Potong)</div>
        <span class="badge badge-gold" id="auto-deduction-count">5 Anggota</span>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
              <tr><th>ID</th><th>Nama</th><th>Saldo</th><th>Harta Bersih</th><th>Status Nisab</th><th>Zakat (2,5%)</th><th>Aksi</th></tr>
          </thead>
          <tbody id="auto-deduction-table">
              </tbody>
        </table>
      </div>
    </div>
</div>
@endsection