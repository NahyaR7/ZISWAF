@extends('layouts.app')

@section('title', 'Pembayaran ZISWAF - BMT Pondok Hijau')
@section('page-title', 'Pembayaran ZISWAF')
@section('page-subtitle', 'Tunaikan kewajiban ZISWAF Anda')

@section('content')
<div style="padding: 32px 36px;">
    <div class="hero-banner" style="margin-bottom:28px">
      <h2>💳 Pembayaran ZISWAF</h2>
      <p>Tunaikan zakat, infaq, sedekah, dan wakaf Anda dengan mudah dan aman</p>
    </div>
    
    <!-- Progress Tracker -->
    <div class="payment-steps mb-24" id="payment-steps">
      <div class="step active" id="step1"><div class="step-circle">1</div><div class="step-label">Pilih Jenis</div></div>
      <div class="step" id="step2"><div class="step-circle">2</div><div class="step-label">Isi Data</div></div>
      <div class="step" id="step3"><div class="step-circle">3</div><div class="step-label">Pembayaran</div></div>
      <div class="step" id="step4"><div class="step-circle">4</div><div class="step-label">Verifikasi</div></div>
      <div class="step" id="step5"><div class="step-circle">5</div><div class="step-label">Kwitansi</div></div>
    </div>

    <!-- Step 1: Pilih Jenis -->
    <div id="bayar-step1" style="animation: fadeIn 0.4s ease;">
      <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:600;color:var(--g2);margin-bottom:20px">Pilih Jenis Transaksi ZISWAF</div>
      <div class="grid-2">
        <div class="program-card" style="cursor:pointer" onclick="selectZiswafType('Zakat')">
          <div class="program-img prog-zakat"><span>🕌</span></div>
          <div class="program-body">
            <div class="program-name">Zakat</div>
            <div class="program-org">Zakat Mal, Profesi, Ternak, Kebun</div>
            <div class="badge badge-green">Wajib Sesuai Nisab</div>
          </div>
        </div>
        <div class="program-card" style="cursor:pointer" onclick="selectZiswafType('Infaq')">
          <div class="program-img prog-infaq"><span>💛</span></div>
          <div class="program-body">
            <div class="program-name">Infaq & Sedekah</div>
            <div class="program-org">Donasi sukarela untuk kebaikan</div>
            <div class="badge badge-gold">Sunnah · Bebas nominal</div>
          </div>
        </div>
        <div class="program-card" style="cursor:pointer" onclick="selectZiswafType('Wakaf')">
          <div class="program-img prog-wakaf"><span>🌿</span></div>
          <div class="program-body">
            <div class="program-name">Wakaf</div>
            <div class="program-org">Wakaf produktif dan wakaf tunai</div>
            <div class="badge badge-blue">Jariyah · Manfaat abadi</div>
          </div>
        </div>
        <div class="program-card" style="cursor:pointer" onclick="selectZiswafType('Fidyah')">
          <div class="program-img prog-sosial"><span>🤲</span></div>
          <div class="program-body">
            <div class="program-name">Fidyah</div>
            <div class="program-org">Tebusan untuk yang tidak berpuasa</div>
            <div class="badge badge-purple">Per hari · Rp 50.000</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Step 2: Isi Data -->
    <div id="bayar-step2" style="display:none">
      <div class="card">
        <div class="card-header">
          <div class="card-title" id="bayar-type-title">Pembayaran Zakat</div>
          <button class="btn btn-ghost btn-sm" onclick="goBackStep(1)">← Kembali</button>
        </div>
        <div class="card-body">
          <div class="grid-2">
            <div>
              <div class="input-group">
                  <label class="input-label">Nama Muzakki / Donatur</label>
                  <input type="text" class="input-field" id="bayar-nama" placeholder="Nama lengkap Anda" value="{{ auth()->user()->name ?? 'Fathur Rahman' }}">
              </div>
              
              <!-- Modul Kategori Harta -->
              <div class="input-group" id="wrap-jenis-harta" style="display:none;">
                  <label class="input-label">Pilih Kategori Harta (Zakat)</label>
                  <select class="input-field" id="bayar-jenis-harta">
                      <option value="Tabungan/Emas">Tabungan / Emas (2.5%)</option>
                      <option value="Profesi">Profesi / Penghasilan (2.5%)</option>
                      <option value="Ternak">Ternak (Sapi/Kambing)</option>
                      <option value="Pertanian">Hasil Pertanian (5% / 10%)</option>
                  </select>
              </div>

              <div class="input-group">
                  <label class="input-label">Nominal (Rp)</label>
                  <div class="input-prefix"><span>Rp</span><input type="number" class="input-field" id="bayar-nominal" placeholder="0"></div>
              </div>
              <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">
                <button class="btn btn-ghost btn-sm" onclick="setBayarNominal(50000)">50 rb</button>
                <button class="btn btn-ghost btn-sm" onclick="setBayarNominal(100000)">100 rb</button>
                <button class="btn btn-ghost btn-sm" onclick="setBayarNominal(500000)">500 rb</button>
                <button class="btn btn-ghost btn-sm" onclick="setBayarNominal(1000000)">1 Jt</button>
              </div>
            </div>
            <div>
              <div class="input-group">
                  <label class="input-label">Lembaga Penyaluran</label>
                  <select class="input-field" id="bayar-lembaga">
                      <option>BAZNAS Pusat</option><option>LAZ Dompet Dhuafa</option><option>Rumah Zakat</option><option>BMT Pondok Hijau Langsung</option>
                  </select>
              </div>
              <div class="input-group">
                  <label class="input-label">Metode Pembayaran</label>
                  <select class="input-field" id="bayar-metode" onchange="toggleBuktiTransfer()">
                      <option value="Transfer Bank">Transfer Bank (Manual)</option>
                      <option value="Virtual Account">Virtual Account</option>
                      <option value="Potong Tabungan">Potong Tabungan (Auto-Debit)</option>
                      <option value="QRIS">QRIS</option>
                  </select>
              </div>

              {{-- Upload Bukti Transfer dinonaktifkan: pembayaran kini ditangani Midtrans (QRIS) --}}
              {{-- <div class="input-group" id="wrap-bukti-transfer" style="display:block;">
                  <label class="input-label">Upload Bukti Transfer <span style="color:var(--g4)">*</span></label>
                  <input type="file" class="input-field" id="bayar-bukti" accept="image/*,.pdf" style="padding: 9px 16px;">
                  <div style="font-size:11px;color:var(--muted);margin-top:4px">Format JPG, PNG, PDF. Wajib dilampirkan untuk metode Transfer Manual.</div>
              </div> --}}

              <div class="input-group">
                  <label class="input-label">Catatan (opsional)</label>
                  <input type="text" class="input-field" id="bayar-catatan" placeholder="Atas nama / pesan">
              </div>
            </div>
          </div>
          <button class="btn btn-primary" onclick="goStep3()" style="margin-top:4px">Lanjut ke Pembayaran →</button>
        </div>
      </div>
    </div>

    <!-- Step 3: Konfirmasi -->
    <div id="bayar-step3" style="display:none">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Konfirmasi Pembayaran</div>
          <button class="btn btn-ghost btn-sm" onclick="goBackStep(2)">← Kembali</button>
        </div>
        <div class="card-body">
          <div class="alert alert-info mb-20" id="bayar-confirm-info"></div>
          <div class="kwitansi-card mb-20">
            <div class="kwitansi-header">
              <div><div style="font-size:12px;opacity:0.7;font-weight:600;text-transform:uppercase;letter-spacing:1px">Ringkasan Transaksi</div></div>
              <div class="badge badge-gold">Menunggu Konfirmasi</div>
            </div>
            <div class="kwitansi-body" id="confirm-detail"></div>
          </div>
          <div class="grid-2" style="gap:12px;margin-bottom:16px;display:none" id="info-pembayaran-instan">
            <div style="background:var(--g8);border-radius:12px;padding:14px;border:1px solid var(--border);text-align:center">
              <div style="font-size:28px;margin-bottom:4px">📱</div>
              <div style="font-size:12px;font-weight:700;color:var(--g3)">QRIS</div>
              <div style="font-size:11px;color:var(--muted)">Scan & bayar instan</div>
            </div>
            <div style="background:var(--g8);border-radius:12px;padding:14px;border:1px solid var(--border);text-align:center">
              <div style="font-size:28px;margin-bottom:4px">🏦</div>
              <div style="font-size:12px;font-weight:700;color:var(--g3)">Virtual Account</div>
              <div style="font-family:monospace;font-size:14px;margin-top:2px">8808 1234 5678</div>
            </div>
          </div>
          <button class="btn btn-primary" onclick="prosesPembayaran()" style="width:100%;justify-content:center;padding:15px">✅ Konfirmasi & Bayar Sekarang</button>
        </div>
      </div>
    </div>

    <!-- Step QRIS: Scan & Bayar (Midtrans) -->
    <div id="bayar-step-qris" style="display:none">
      <div class="card">
        <div class="card-header">
          <div class="card-title">📱 Scan QRIS untuk Membayar</div>
          <button class="btn btn-ghost btn-sm" onclick="batalkanQris()">← Kembali</button>
        </div>
        <div class="card-body" style="text-align:center; padding:32px 36px">
          <div class="alert alert-info mb-20" style="text-align:left">
            <span style="font-size:18px">📲</span>
            <div>Buka aplikasi <strong>GoPay, DANA, OVO, ShopeePay, atau m-Banking</strong> Anda, lalu scan kode QR di bawah ini. Status akan otomatis diperbarui setelah pembayaran berhasil.</div>
          </div>

          <div style="display:inline-block; background:#fff; padding:18px; border-radius:18px; border:1px solid var(--border); box-shadow:0 8px 24px rgba(0,0,0,0.06)">
            <img id="qris-image" src="" alt="QRIS Code" style="width:260px; height:260px; object-fit:contain; display:block" />
          </div>

          <div style="margin-top:18px">
            <div style="font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; font-weight:600">Total Pembayaran</div>
            <div id="qris-amount" style="font-family:'Playfair Display',serif; font-size:30px; font-weight:700; color:var(--g2)">Rp 0</div>
            <div style="font-size:12px; color:var(--muted); margin-top:4px">Kode Transaksi: <span id="qris-order-id" style="font-family:monospace">-</span></div>
          </div>

          <div id="qris-status-box" style="margin-top:22px; display:flex; align-items:center; justify-content:center; gap:10px; font-size:14px; color:var(--gold); font-weight:600">
            <span class="qris-spinner" style="width:16px; height:16px; border:3px solid var(--border); border-top-color:var(--gold); border-radius:50%; display:inline-block; animation:spin 0.8s linear infinite"></span>
            <span id="qris-status-text">Menunggu pembayaran...</span>
          </div>

          <div style="margin-top:18px; font-size:12px; color:var(--muted)">
            🔒 Pembayaran diproses aman oleh <strong>Midtrans</strong> · Mode Sandbox (Testing)
          </div>
        </div>
      </div>
    </div>

    <!-- Step 4 & 5 (Loading & Success) -->
    <div id="bayar-step4" style="display:none; text-align:center;">
        <div class="card">
            <div class="card-body" style="padding:48px 36px">
                <div style="font-size:64px;margin-bottom:16px;animation:pulse 1.5s infinite" id="icon-verify">⚙️</div>
                <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--g2);margin-bottom:8px" id="title-verify">Memverifikasi Pembayaran</div>
                <div style="font-size:14px;color:var(--muted);margin-bottom:24px" id="desc-verify">Sistem sedang memverifikasi transaksi Anda...</div>
            </div>
        </div>
    </div>
    
    <div id="bayar-step5" style="display:none">
        <div class="card">
            <div class="card-header"><div class="card-title">🎉 Pembayaran Berhasil!</div></div>
            <div class="card-body">
                <div class="alert alert-success mb-20"><span style="font-size:20px">🤲</span><div><strong>Jazakallahu Khairan!</strong> Pembayaran ZISWAF Anda telah berhasil diproses.</div></div>
                <div class="kwitansi-card" id="final-kwitansi">
                    <div class="kwitansi-header">
                        <div>
                            <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700">BMT Pondok Hijau</div>
                            <div style="font-size:12px;opacity:0.7;margin-top:2px">Kwitansi Digital ZISWAF</div>
                        </div>
                        <div style="text-align:right">
                            <div class="badge badge-green" style="font-size:12px">✅ LUNAS</div>
                            <div style="font-size:11px;opacity:0.65;margin-top:6px" id="kw-id">No. KW-2026-0001</div>
                        </div>
                    </div>
                    <div class="kwitansi-body" id="kwitansi-final-body"></div>
                    <div style="padding:0 24px 24px;display:flex;gap:10px;flex-wrap:wrap">
                        <a href="{{ route('dashboard-user') }}" class="btn btn-ghost">🏠 Kembali ke Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection