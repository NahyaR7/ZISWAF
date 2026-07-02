<div class="modal-overlay" id="modal-donasi">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modal-donasi-title">Program Donasi</h3>
      <p id="modal-donasi-org">Lembaga Mitra</p>
      <button class="modal-close" onclick="closeModal('modal-donasi')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="donasi-program-id" value="">
      <div class="input-group">
          <label class="input-label">Nama Donatur</label>
          <input type="text" class="input-field" id="donasi-nama" placeholder="Nama Anda" value="{{ auth()->user()->name ?? '' }}">
      </div>
      <div class="input-group">
          <label class="input-label">Nominal Donasi (Rp)</label>
          <div class="input-prefix"><span>Rp</span><input type="number" class="input-field" id="donasi-nominal" placeholder="50000"></div>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
        <button type="button" class="btn btn-ghost btn-sm" onclick="setNominal(50000)">50 rb</button>
        <button type="button" class="btn btn-ghost btn-sm" onclick="setNominal(100000)">100 rb</button>
        <button type="button" class="btn btn-ghost btn-sm" onclick="setNominal(250000)">250 rb</button>
        <button type="button" class="btn btn-ghost btn-sm" onclick="setNominal(500000)">500 rb</button>
      </div>
      <div class="input-group">
          <label class="input-label">Metode Pembayaran</label>
          <select class="input-field" id="donasi-metode"><option>Transfer Bank</option><option>QRIS</option><option>Potong Tabungan</option></select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary" style="flex:1;justify-content:center" onclick="konfirmasiDonasi()">✅ Donasikan Sekarang</button>
      <button class="btn btn-ghost btn-sm" onclick="closeModal('modal-donasi')">Batal</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modal-kwitansi">
  <div class="modal" style="max-width:560px">
    <div class="modal-header">
      <h3>🧾 Kwitansi Digital</h3>
      <p id="modal-kw-id">No. KW-2026-0047</p>
      <button class="modal-close" onclick="closeModal('modal-kwitansi')">✕</button>
    </div>
    <div class="modal-body">
      <div style="text-align:center;padding:8px;font-family:'Amiri',serif;font-size:18px;color:var(--gold);margin-bottom:16px">بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ</div>
      <div id="modal-kw-body"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary" onclick="showToast('📥 PDF diunduh');closeModal('modal-kwitansi')">📥 Unduh PDF</button>
      <button class="btn btn-outline" onclick="showToast('📨 Dikirim ke email');closeModal('modal-kwitansi')">📨 Email</button>
      <button class="btn btn-ghost btn-sm" onclick="closeModal('modal-kwitansi')">Tutup</button>
    </div>
  </div>
</div>