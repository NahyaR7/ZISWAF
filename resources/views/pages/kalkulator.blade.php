@extends('layouts.app')

@section('title', 'Kalkulator Nisab - ZISWAF BMT')
@section('page-title', 'Kalkulator Zakat')
@section('page-subtitle', 'Hitung kewajiban zakat Anda secara akurat')

@section('content')
<div style="padding: 32px 36px;">
    <div class="hero-banner">
      <h2>⚖️ Kalkulator Zakat Otomatis</h2>
      <p>Hitung kewajiban zakat Anda secara akurat berdasarkan data pasar terkini</p>
    </div>
    
    <div class="nisab-grid">
      <div class="card">
        <div class="card-header"><div class="card-title">Input Data Zakat</div></div>
        <div class="card-body">
          <div class="input-group">
            <label class="input-label">Jenis Zakat</label>
            <select class="input-field" id="zakat-type">
              <option value="mal">Zakat Mal (Harta)</option>
              <option value="tabungan">Zakat Tabungan</option>
              <option value="penghasilan">Zakat Penghasilan</option>
            </select>
          </div>
          
          <div class="input-group">
              <label class="input-label">Kategori Harta Spesifik</label>
              <select class="input-field" id="kategori-harta" onchange="toggleGramInput()">
                  <option value="Emas">Tabungan / Emas (2.5%)</option>
                  <option value="Perak">Perak (2.5%)</option>
                  <option value="Ternak">Ternak Sapi / Kambing</option>
                  <option value="Kebun">Hasil Pertanian (5% / 10%)</option>
              </select>
          </div>

          <div class="input-group" id="wrap-jumlah-gram" style="display: block; animation: fadeIn 0.3s ease;">
            <label class="input-label">Jumlah Berat Emas/Perak</label>
            <div class="input-prefix">
                <span>Gr</span>
                <input type="number" class="input-field" id="gram-input" placeholder="0" value="0" step="0.01" oninput="hitungTotalHarta()">
            </div>
          </div>

          <div class="input-group" id="wrap-harta-tunai">
            <label class="input-label">Harta Tunai / Tabungan (Rp)</label>
            <div class="input-prefix">
                <span>Rp</span>
                <input type="number" class="input-field" id="tunai-input" placeholder="0" value="70000000" oninput="hitungTotalHarta()">
            </div>
          </div>

          <div class="input-group">
            <label class="input-label">Total Nilai Harta Keseluruhan (Rp)</label>
            <div class="input-prefix" style="background: var(--g8);">
                <span>Rp</span>
                <input type="number" class="input-field" id="saldo-input" placeholder="0" value="70000000" readonly style="background: transparent; font-weight: bold; color: var(--g2); cursor: not-allowed;">
            </div>
          </div>

          <div class="input-group">
            <label class="input-label">Hutang yang Harus Dibayar (Rp)</label>
            <div class="input-prefix">
                <span>Rp</span>
                <input type="number" class="input-field" id="hutang-input" placeholder="0" value="0">
            </div>
          </div>
          
          <div class="input-group">
            <label class="input-label">Sudah Mencapai Haul (1 Tahun)?</label>
            <select class="input-field" id="haul-input">
              <option value="ya">Ya, sudah mencapai haul</option>
              <option value="tidak">Belum mencapai haul</option>
            </select>
          </div>
          <button class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px" onclick="hitungZakat()">⚡ Hitung Zakat Sekarang</button>
        </div>
      </div>
      
      <div>
        <div class="card mb-20">
          <div class="card-header">
            <div class="card-title">Harga Pasar Terkini</div>
            <div id="live-badge" style="display:flex; align-items:center; gap:6px; font-size:11px; font-weight:700; color:var(--red);">
                <div style="width:8px; height:8px; background:var(--red); border-radius:50%; animation: pulse 1.5s infinite;"></div>
                LIVE
            </div>
          </div>
          <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:10px;margin-top:4px;">
              <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:var(--g8);border-radius:10px;border:1px solid var(--border)">
                <div>
                    <div style="font-size:11px;color:var(--muted)">Harga Emas / gram</div>
                    <div style="font-weight:800;color:var(--g2)" id="teks-harga-emas">Rp {{ number_format($hargaPerGram, 0, ',', '.') }}</div>
                </div>
                <div style="text-align:right">
                    <div style="font-size:11px;color:var(--muted)">Nisab (85 gr)</div>
                    <div style="font-weight:800;color:var(--g3)" id="teks-nisab-emas">Rp {{ number_format($nisabEmas, 0, ',', '.') }}</div>
                </div>
              </div>
              <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:var(--gold3);border-radius:10px;border:1px solid rgba(201,168,76,0.25)">
                <div>
                    <div style="font-size:11px;color:var(--muted)">Harga Perak / gram</div>
                    <div style="font-weight:800;color:var(--g2)" id="teks-harga-perak">Rp {{ number_format($hargaPerak, 0, ',', '.') }}</div>
                </div>
                <div style="text-align:right">
                    <div style="font-size:11px;color:var(--muted)">Nisab (595 gr)</div>
                    <div style="font-weight:800;color:#a07828" id="teks-nisab-perak">Rp {{ number_format($nisabPerak, 0, ',', '.') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="card">
          <div class="card-header"><div class="card-title">Hasil Perhitungan</div></div>
          <div class="card-body" id="kalkulator-result">
            <div style="text-align:center;color:var(--muted);padding:20px 0">
                <div style="font-size:40px;margin-bottom:8px;opacity:0.5">📊</div>
                <div>Masukkan data dan tekan tombol <br><strong style="color:var(--g3)">"Hitung Zakat Sekarang"</strong><br> untuk melihat hasil.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection