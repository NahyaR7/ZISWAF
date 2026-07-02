let currentPayStep = 1;
let selectedZiswafType = '';

// ===================== NAVIGATION & UI =====================
function showPage(id) {
  if(id === 'dashboard') window.location.href = '/';
  else window.location.href = '/' + id;
}

function showToast(msg) {
  const t = document.getElementById('toast');
  if(!t) return;
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}

function toggleTheme() {
  const h = document.documentElement;
  const isDark = h.getAttribute('data-theme') === 'dark';
  h.setAttribute('data-theme', isDark ? 'light' : 'dark');
  document.getElementById('theme-btn').textContent = isDark ? '🌙' : '☀️';
}

function openModal(id) { 
  const modal = document.getElementById(id);
  if(modal) modal.classList.add('open');
}
function closeModal(id) { 
  const modal = document.getElementById(id);
  if(modal) modal.classList.remove('open');
}

// ===================== FUNGSI SINKRONISASI GRAM + TUNAI =====================
function toggleGramInput() {
  const kategoriEl = document.getElementById('kategori-harta');
  const wrapGram = document.getElementById('wrap-jumlah-gram');
  const wrapTunai = document.getElementById('wrap-harta-tunai');
  const gramInput = document.getElementById('gram-input');
  const tunaiInput = document.getElementById('tunai-input');

  if (!kategoriEl || !wrapGram) return;
  const kategori = kategoriEl.value;

  if (kategori === 'Emas' || kategori === 'Perak') {
    // Emas/Perak: tampilkan input gram, sembunyikan harta tunai
    wrapGram.style.display = 'block';
    wrapGram.style.animation = 'fadeIn 0.3s ease';
    if (wrapTunai) wrapTunai.style.display = 'none';
    if (tunaiInput) tunaiInput.value = 0;
  } else {
    // Selain emas/perak: tampilkan harta tunai, sembunyikan input gram
    wrapGram.style.display = 'none';
    if (gramInput) gramInput.value = 0;
    if (wrapTunai) {
      wrapTunai.style.display = 'block';
      wrapTunai.style.animation = 'fadeIn 0.3s ease';
    }
  }

  // Hitung ulang total harta
  hitungTotalHarta();
}

function hitungTotalHarta() {
  const kategori = document.getElementById('kategori-harta').value;
  const gramInput = parseFloat(document.getElementById('gram-input').value) || 0;
  const tunaiInput = parseFloat(document.getElementById('tunai-input').value) || 0;
  
  const elEmas = document.getElementById('teks-harga-emas');
  const elPerak = document.getElementById('teks-harga-perak');
  
  const hargaEmas = elEmas ? parseFloat(elEmas.innerText.replace(/[^0-9]/g, '')) : 2673000;
  const hargaPerak = elPerak ? parseFloat(elPerak.innerText.replace(/[^0-9]/g, '')) : 52658;
  
  let nilaiEmasPerak = 0;
  if (kategori === 'Emas' || kategori === 'Perak') {
      let hargaAcuan = (kategori === 'Perak') ? hargaPerak : hargaEmas;
      nilaiEmasPerak = gramInput * hargaAcuan;
  }
  
  // Total Harta = (Berat Emas/Perak x Harga Live) + Harta Tunai
  const totalEquiv = nilaiEmasPerak + tunaiInput;
  
  const saldoInput = document.getElementById('saldo-input');
  if (saldoInput) {
      saldoInput.value = Math.round(totalEquiv);
  }
}

// ===================== FUNGSI KALKULATOR =====================
async function hitungZakat() {
    const saldoInput = document.getElementById('saldo-input');
    const kategoriEl = document.getElementById('kategori-harta');
    const kategori = kategoriEl ? kategoriEl.value : 'Emas';
    const type = document.getElementById('zakat-type').value;

    if(!saldoInput) return;

    const r = document.getElementById('kalkulator-result');
    if(!r) return;

    r.innerHTML = `<div style="text-align:center;padding:20px"><div style="font-size:32px;animation:pulse 1s infinite">⚙️</div><div style="margin-top:10px;color:var(--g3);font-weight:bold;">Sistem sedang menghitung...</div></div>`;

    const saldo = parseFloat(saldoInput.value) || 0;
    const hutang = parseFloat(document.getElementById('hutang-input').value) || 0;
    const haul = document.getElementById('haul-input').value;

    const params = new URLSearchParams({ saldo, hutang, haul, type, kategori });

    try {
        const response = await fetch('/kalkulator/hitung?' + params.toString());
        const data = await response.json();

        const fmt = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');

        r.innerHTML = `
          <div class="result-box" style="animation: fadeIn 0.4s ease;">
            <div class="result-label">Harta Bersih (Saldo − Hutang)</div>
            <div class="result-value">${fmt(data.bersih)}</div>
            <div class="result-note">Nisab ${type === 'penghasilan' ? 'Profesi' : kategori}: ${fmt(data.nisab)}</div>
          </div>
          <div class="nisab-status ${data.wajib?'wajib':'belum'}" style="animation: fadeIn 0.4s ease; padding: 12px; border-radius: 8px; margin-top: 10px; background: ${data.wajib ? 'var(--gold3)' : 'var(--g8)'};">
            <div style="display:flex; align-items:center; gap:8px;">
                <div class="status-icon" style="font-size:24px">${data.wajib?'✅':'⏳'}</div>
                <div style="font-weight:700; color:var(--g3)">${data.wajib?'Wajib Zakat':'Belum Mencapai Nisab/Haul'}</div>
            </div>
          </div>
          ${data.wajib?`
          <div class="result-box" style="margin-top:14px;background:var(--gold3);border-color:rgba(201,168,76,0.3); animation: fadeIn 0.4s ease;">
            <div class="result-label" style="color:#a07828">Jumlah Zakat Anda (${data.persentase}%)</div>
            <div class="result-value" style="color:var(--g1)">${fmt(data.zakat)}</div>
          </div>
          <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap; animation: fadeIn 0.4s ease;">
            <a href="/bayar?nominal=${Math.round(data.zakat)}" class="btn btn-primary">💳 Bayar Zakat Sekarang</a>
            <a href="/marketplace" class="btn btn-outline">🕌 Pilih Lembaga</a>
          </div>`:''}
        `;
    } catch (e) {
        r.innerHTML = `<div style="text-align:center;padding:20px;color:var(--muted)">❌ Gagal menghitung zakat. Coba lagi.</div>`;
    }
}

// Logika Tarik Data (Polling) Live Harga setiap 5 detik
async function fetchLiveHarga() {
    if(!document.getElementById('teks-harga-emas')) return;

    try {
        const response = await fetch('/kalkulator/live-harga');
        const data = await response.json();
        
        const fmt = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');
        
        const elEmas = document.getElementById('teks-harga-emas');
        const elNisabEmas = document.getElementById('teks-nisab-emas');
        const elPerak = document.getElementById('teks-harga-perak');
        const elNisabPerak = document.getElementById('teks-nisab-perak');
        const elBadge = document.getElementById('live-badge');
        
        if (elEmas.innerText !== fmt(data.emas_per_gram)) {
            elEmas.innerText = fmt(data.emas_per_gram);
            elNisabEmas.innerText = fmt(data.nisab_emas);
            
            elEmas.style.color = '#2e7d32'; 
            if(elBadge) elBadge.innerHTML = '<div style="width:8px; height:8px; background:var(--green); border-radius:50%; animation: pulse 1.5s infinite;"></div> UPDATE BARU';
            
            setTimeout(() => { 
                elEmas.style.color = 'var(--g2)'; 
                if(elBadge) elBadge.innerHTML = '<div style="width:8px; height:8px; background:var(--red); border-radius:50%; animation: pulse 1.5s infinite;"></div> LIVE';
            }, 3000);
            
            // Otomatis re-kalkulasi Total Harta saat harga live berganti!
            hitungTotalHarta();
        }
        
        if (elPerak.innerText !== fmt(data.perak_per_gram)) {
            elPerak.innerText = fmt(data.perak_per_gram);
            elNisabPerak.innerText = fmt(data.nisab_perak);
            hitungTotalHarta();
        }
    } catch (error) {
        console.log('Sinkronisasi live background tertunda.');
    }
}

// ===================== MARKETPLACE DONASI =====================
function filterProgram(type, btn) {
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');

  document.querySelectorAll('#program-grid [data-tipe]').forEach(card => {
    card.style.display = (type === 'semua' || card.dataset.tipe === type) ? '' : 'none';
  });
}

function openModalDonasi(programId, programName, programOrg) {
  document.getElementById('donasi-program-id').value = programId;
  document.getElementById('modal-donasi-title').textContent = programName;
  document.getElementById('modal-donasi-org').textContent = '🏛 ' + programOrg;
  openModal('modal-donasi');
}

function setNominal(v) {
  const input = document.getElementById('donasi-nominal');
  if (input) input.value = v;
}

async function konfirmasiDonasi() {
  const programId = document.getElementById('donasi-program-id').value;
  const nama = document.getElementById('donasi-nama').value;
  const nominal = parseInt(document.getElementById('donasi-nominal').value) || 0;
  const metode = document.getElementById('donasi-metode').value;

  if (!nominal || nominal < 10000) { showToast('⚠️ Minimal donasi adalah Rp 10.000'); return; }

  try {
    const response = await fetch('/marketplace/donasi', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ program_id: programId, nama, nominal, metode }),
    });
    const result = await response.json();

    if (result.success) {
      closeModal('modal-donasi');
      const isManual = result.status === 'Diproses';
      showToast(isManual ? '🕒 Donasi dicatat. Menunggu verifikasi admin.' : '🎉 Donasi berhasil! Kwitansi terbit: ' + result.kwitansi_number);
    } else {
      showToast('❌ ' + (result.message || 'Gagal memproses donasi.'));
    }
  } catch (e) {
    showToast('❌ Terjadi kesalahan jaringan saat memproses donasi');
  }
}

// ===================== FUNGSI PEMBAYARAN & API =====================
function selectZiswafType(type) {
  if(!type) { showToast('⚠️ Silakan pilih jenis transaksi terlebih dahulu!'); return; }
  selectedZiswafType = type;
  document.getElementById('bayar-step1').style.display = 'none';
  
  const step2 = document.getElementById('bayar-step2');
  step2.style.display = 'block';
  step2.style.animation = 'fadeIn 0.4s ease';

  document.getElementById('bayar-type-title').textContent = `Pembayaran ${type}`;

  const wrapHarta = document.getElementById('wrap-jenis-harta');
  if (wrapHarta) {
    wrapHarta.style.display = (type === 'Zakat') ? 'block' : 'none';
    if(type === 'Zakat') wrapHarta.style.animation = 'fadeIn 0.4s ease';
  }

  if (type === 'Zakat') {
      const urlParams = new URLSearchParams(window.location.search);
      const nominalParam = urlParams.get('nominal');
      if (nominalParam) {
          const inputNominal = document.getElementById('bayar-nominal');
          if(inputNominal) {
              inputNominal.value = nominalParam;
              showToast('✅ Nominal otomatis terisi dari Kalkulator');
          }
      }
  }
  setStep(2);
}

function toggleBuktiTransfer() {
  const metode = document.getElementById('bayar-metode').value;
  const wrapBukti = document.getElementById('wrap-bukti-transfer');
  const infoInstan = document.getElementById('info-pembayaran-instan');

  if (wrapBukti) {
    wrapBukti.style.display = (metode === 'Transfer Bank') ? 'block' : 'none';
    if(metode === 'Transfer Bank') wrapBukti.style.animation = 'fadeIn 0.4s ease';
  }

  if (infoInstan) {
    infoInstan.style.display = (metode === 'Transfer Bank') ? 'none' : 'grid';
    if(metode !== 'Transfer Bank') infoInstan.style.animation = 'fadeIn 0.4s ease';
  }
}

function setBayarNominal(v) { 
  const input = document.getElementById('bayar-nominal');
  if(input) input.value = v; 
}

function goStep3() {
  const nama = document.getElementById('bayar-nama').value;
  const nominal = parseInt(document.getElementById('bayar-nominal').value) || 0;
  const metode = document.getElementById('bayar-metode').value;
  const lembaga = document.getElementById('bayar-lembaga').value;
  
  if(!nominal || nominal < 10000) { showToast('⚠️ Minimal nominal transaksi adalah Rp 10.000'); return; }

  // Validasi upload bukti transfer dinonaktifkan: pembayaran ditangani Midtrans.
  // if (metode === 'Transfer Bank') {
  //     const fileInput = document.getElementById('bayar-bukti');
  //     if (fileInput && fileInput.files.length === 0) {
  //         showToast('⚠️ Mohon upload bukti transfer terlebih dahulu!');
  //         return;
  //     }
  // }

  document.getElementById('bayar-step2').style.display = 'none';
  const step3 = document.getElementById('bayar-step3');
  step3.style.display = 'block';
  step3.style.animation = 'fadeIn 0.4s ease';
  
  const fmt = n => 'Rp ' + n.toLocaleString('id-ID');
  document.getElementById('bayar-confirm-info').innerHTML = `<span style="font-size:18px">ℹ️</span><div>Harap periksa kembali detail transaksi sebelum melakukan pembayaran. Pastikan nominal dan lembaga penerima sudah benar.</div>`;
  
  let detailHTML = `<div class="kwitansi-row"><span class="kw-label">Jenis</span><span class="kw-value">${selectedZiswafType}</span></div>`;

  if (selectedZiswafType === 'Zakat') {
      const elmHarta = document.getElementById('bayar-jenis-harta');
      if (elmHarta) detailHTML += `<div class="kwitansi-row"><span class="kw-label">Kategori Harta</span><span class="kw-value">${elmHarta.options[elmHarta.selectedIndex].text}</span></div>`;
  }

  detailHTML += `
    <div class="kwitansi-row"><span class="kw-label">Muzakki</span><span class="kw-value">${nama}</span></div>
    <div class="kwitansi-row"><span class="kw-label">Nominal</span><span class="kw-value" style="color:var(--g3);font-size:18px">${fmt(nominal)}</span></div>
    <div class="kwitansi-row"><span class="kw-label">Lembaga</span><span class="kw-value">${lembaga}</span></div>
    <div class="kwitansi-row"><span class="kw-label">Metode</span><span class="kw-value">${metode}</span></div>`;

  document.getElementById('confirm-detail').innerHTML = detailHTML;
  setStep(3);
}

async function prosesPembayaran() {
  // Metode QRIS memakai integrasi Midtrans (QR Code + polling status).
  if (document.getElementById('bayar-metode').value === 'QRIS') {
    return bayarQris();
  }

  document.getElementById('bayar-step3').style.display = 'none';

  const step4 = document.getElementById('bayar-step4');
  step4.style.display = 'block';
  step4.style.animation = 'fadeIn 0.4s ease';
  setStep(4);
  
  const formData = new FormData();
  formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
  formData.append('jenis_ziswaf', selectedZiswafType);
  formData.append('nominal', document.getElementById('bayar-nominal').value);
  formData.append('metode', document.getElementById('bayar-metode').value);
  formData.append('lembaga', document.getElementById('bayar-lembaga').value);
  
  if (selectedZiswafType === 'Zakat') {
      const elmHarta = document.getElementById('bayar-jenis-harta');
      if (elmHarta) formData.append('kategori_harta', elmHarta.options[elmHarta.selectedIndex].text);
  }
  
  // Upload bukti transfer dinonaktifkan: pembayaran ditangani Midtrans.
  // const fileInput = document.getElementById('bayar-bukti');
  // if (fileInput && fileInput.files.length > 0) {
  //     formData.append('bukti_bayar', fileInput.files[0]);
  // }

  try {
      const response = await fetch('/transaksi/store', {
          method: 'POST',
          body: formData
      });
      const result = await response.json();

      if(result.success) {
          const bayarStep4 = document.getElementById('bayar-step4');
          if(bayarStep4) {
            bayarStep4.innerHTML = `
            <div class="card"><div class="card-body" style="padding:48px 36px">
            <div style="font-size:64px; margin-bottom:16px; animation:fadeIn 0.5s ease;">✅</div>
            <h3 style="color:var(--g4)">Pembayaran Berhasil Tersimpan!</h3>
            </div></div>`;
          }

          setTimeout(() => {
            document.getElementById('bayar-step4').style.display = 'none';
            const step5 = document.getElementById('bayar-step5');
            step5.style.display = 'block';
            step5.style.animation = 'fadeIn 0.5s ease';
            setStep(5);
            
            const nama = document.getElementById('bayar-nama').value;
            const fmt = n => 'Rp ' + parseInt(n).toLocaleString('id-ID');
            const isManual = result.status === 'Diproses';
            const kwIdDisplay = result.kwitansi_number ? result.kwitansi_number : 'Menunggu Verifikasi Admin';
            
            const kwitansiBody = document.getElementById('kwitansi-final-body');
            if(kwitansiBody) {
              kwitansiBody.innerHTML = `
                <div class="kwitansi-row"><span class="kw-label">Kode Transaksi</span><span class="kw-value" style="font-family:monospace">${result.transaction_code}</span></div>
                <div class="kwitansi-row"><span class="kw-label">No. Kwitansi</span><span class="kw-value" style="font-family:monospace; color:${isManual ? 'var(--gold)' : 'var(--g2)'}">${kwIdDisplay}</span></div>
                <div class="kwitansi-row"><span class="kw-label">Tanggal</span><span class="kw-value">${result.date}</span></div>
                <div class="kwitansi-row"><span class="kw-label">Jenis</span><span class="kw-value">${selectedZiswafType}</span></div>
                <div class="kwitansi-row"><span class="kw-label">Muzakki</span><span class="kw-value">${nama}</span></div>
                <div class="kwitansi-row"><span class="kw-label">Metode</span><span class="kw-value">${document.getElementById('bayar-metode').value}</span></div>
                <div class="kwitansi-row"><span class="kw-label">Lembaga</span><span class="kw-value">${document.getElementById('bayar-lembaga').value}</span></div>
                <div class="kwitansi-total"><span style="font-weight:700;font-size:14px;color:var(--muted)">TOTAL DIBAYAR</span><span style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--g2)">${fmt(document.getElementById('bayar-nominal').value)}</span></div>
                <div style="margin-top:16px;text-align:center;font-family:'Amiri',serif;font-size:15px;color:var(--gold);opacity:0.75">جَزَاكَ اللهُ خَيْرًا كَثِيرًا</div>`;
            }
            
            if (isManual) {
                showToast('🕒 Transaksi dicatat. Menunggu verifikasi admin.');
            } else {
                showToast('🎉 Pembayaran instan berhasil! Kwitansi terbit.');
            }
          }, 1500);
      }
  } catch(e) {
      showToast('❌ Terjadi kesalahan jaringan saat menyimpan data');
  }
}

// ==========================================
// PEMBAYARAN QRIS (MIDTRANS)
// ==========================================
let qrisPollTimer = null;
let qrisOrderId = null;

async function bayarQris() {
  const nominal = parseInt(document.getElementById('bayar-nominal').value) || 0;
  const lembaga = document.getElementById('bayar-lembaga').value;

  const payload = {
    jenis_ziswaf: selectedZiswafType,
    nominal: nominal,
    lembaga: lembaga,
  };
  if (selectedZiswafType === 'Zakat') {
    const elmHarta = document.getElementById('bayar-jenis-harta');
    if (elmHarta) payload.kategori_harta = elmHarta.options[elmHarta.selectedIndex].text;
  }

  showToast('⏳ Membuat kode QRIS...');

  try {
    const response = await fetch('/qris/create', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify(payload),
    });
    const result = await response.json();

    if (!result.success) {
      showToast('❌ ' + (result.message || 'Gagal membuat QRIS. Cek konfigurasi Midtrans.'));
      return;
    }

    // Tampilkan layar QRIS
    document.getElementById('bayar-step3').style.display = 'none';
    const stepQris = document.getElementById('bayar-step-qris');
    stepQris.style.display = 'block';
    stepQris.style.animation = 'fadeIn 0.4s ease';

    document.getElementById('qris-image').src = result.qr_url;
    document.getElementById('qris-amount').textContent = 'Rp ' + result.amount.toLocaleString('id-ID');
    document.getElementById('qris-order-id').textContent = result.order_id;
    document.getElementById('qris-status-text').textContent = 'Menunggu pembayaran...';

    qrisOrderId = result.order_id;
    mulaiPollingQris(result.order_id);
  } catch (e) {
    showToast('❌ Terjadi kesalahan jaringan saat membuat QRIS');
  }
}

function mulaiPollingQris(orderId) {
  hentikanPollingQris();
  // Cek status tiap 3 detik hingga lunas / kadaluarsa.
  qrisPollTimer = setInterval(() => cekStatusQris(orderId), 3000);
}

function hentikanPollingQris() {
  if (qrisPollTimer) {
    clearInterval(qrisPollTimer);
    qrisPollTimer = null;
  }
}

async function cekStatusQris(orderId) {
  try {
    const response = await fetch('/qris/status/' + encodeURIComponent(orderId), {
      headers: { 'Accept': 'application/json' },
    });
    const result = await response.json();
    if (!result.success) return 'error';

    if (result.paid) {
      hentikanPollingQris();
      document.getElementById('qris-status-text').textContent = '✅ Pembayaran diterima!';
      showToast('🎉 Pembayaran QRIS berhasil! Kwitansi terbit.');
      setTimeout(() => tampilkanSuksesQris(result), 800);
      return 'paid';
    } else if (result.status === 'Batal') {
      hentikanPollingQris();
      document.getElementById('qris-status-text').textContent = '⌛ QRIS kadaluarsa / dibatalkan.';
      showToast('⚠️ Pembayaran QRIS kadaluarsa atau dibatalkan.');
      return 'batal';
    }
    return 'pending';
  } catch (e) {
    // Abaikan error sementara; polling berikutnya akan mencoba lagi.
    return 'error';
  }
}

async function cekStatusQrisManual() {
  if (!qrisOrderId) return;

  const btn = document.getElementById('qris-refresh-btn');
  if (btn) { btn.disabled = true; btn.textContent = '🔄 Memeriksa...'; }

  const hasil = await cekStatusQris(qrisOrderId);

  if (hasil === 'pending') {
    showToast('⏳ Belum ada pembayaran masuk. Coba lagi setelah simulasi bayar di Midtrans.');
  } else if (hasil === 'error') {
    showToast('❌ Gagal menghubungi server untuk cek status.');
  }

  if (btn) { btn.disabled = false; btn.textContent = '🔄 Cek Status Sekarang'; }
}

function tampilkanSuksesQris(result) {
  document.getElementById('bayar-step-qris').style.display = 'none';
  const step5 = document.getElementById('bayar-step5');
  step5.style.display = 'block';
  step5.style.animation = 'fadeIn 0.5s ease';
  setStep(5);

  const nama = document.getElementById('bayar-nama').value;
  const fmt = n => 'Rp ' + parseInt(n).toLocaleString('id-ID');

  const kwId = document.getElementById('kw-id');
  if (kwId && result.kwitansi_number) kwId.textContent = 'No. ' + result.kwitansi_number;

  const kwitansiBody = document.getElementById('kwitansi-final-body');
  if (kwitansiBody) {
    kwitansiBody.innerHTML = `
      <div class="kwitansi-row"><span class="kw-label">Kode Transaksi</span><span class="kw-value" style="font-family:monospace">${result.order_id}</span></div>
      <div class="kwitansi-row"><span class="kw-label">No. Kwitansi</span><span class="kw-value" style="font-family:monospace">${result.kwitansi_number || '-'}</span></div>
      <div class="kwitansi-row"><span class="kw-label">Tanggal</span><span class="kw-value">${result.date}</span></div>
      <div class="kwitansi-row"><span class="kw-label">Jenis</span><span class="kw-value">${selectedZiswafType}</span></div>
      <div class="kwitansi-row"><span class="kw-label">Muzakki</span><span class="kw-value">${nama}</span></div>
      <div class="kwitansi-row"><span class="kw-label">Metode</span><span class="kw-value">QRIS (Midtrans)</span></div>
      <div class="kwitansi-row"><span class="kw-label">Lembaga</span><span class="kw-value">${document.getElementById('bayar-lembaga').value}</span></div>
      <div class="kwitansi-total"><span style="font-weight:700;font-size:14px;color:var(--muted)">TOTAL DIBAYAR</span><span style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--g2)">${fmt(document.getElementById('bayar-nominal').value)}</span></div>
      <div style="margin-top:16px;text-align:center;font-family:'Amiri',serif;font-size:15px;color:var(--gold);opacity:0.75">جَزَاكَ اللهُ خَيْرًا كَثِيرًا</div>`;
  }
}

function batalkanQris() {
  hentikanPollingQris();
  document.getElementById('bayar-step-qris').style.display = 'none';
  const s3 = document.getElementById('bayar-step3');
  s3.style.display = 'block';
  s3.style.animation = 'fadeIn 0.3s ease';
  setStep(3);
}

function goBackStep(step) {
  if(step === 1) {
    document.getElementById('bayar-step2').style.display = 'none';
    const s1 = document.getElementById('bayar-step1');
    s1.style.display = 'block';
    s1.style.animation = 'fadeIn 0.3s ease';
    setStep(1);
  } else if(step === 2) {
    document.getElementById('bayar-step3').style.display = 'none';
    const s2 = document.getElementById('bayar-step2');
    s2.style.display = 'block';
    s2.style.animation = 'fadeIn 0.3s ease';
    setStep(2);
  }
}

function setStep(n) {
  for(let i=1;i<=5;i++) {
    const s = document.getElementById('step'+i);
    if(!s) continue;
    s.classList.remove('active','done');
    if(i < n) s.classList.add('done');
    else if(i === n) s.classList.add('active');
  }
  currentPayStep = n;
}

// ===================== KWITANSI & PDF =====================
function cariKwitansi() {
  const q = document.getElementById('kwitansi-search');
  if(q && q.value) showToast('🔍 Mencari kwitansi: ' + q.value);
}

function viewKwitansi(kwId, trxId, date, type, name, org, method, amount) {
  if(kwId === '-' || !kwId) { showToast('❌ Kwitansi belum tersedia. Menunggu Verifikasi'); return; }
  
  const body = `
    <div class="kwitansi-row"><span class="kw-label">No. Kwitansi</span><span class="kw-value" style="font-family:monospace">${kwId}</span></div>
    <div class="kwitansi-row"><span class="kw-label">ID Transaksi</span><span class="kw-value" style="font-family:monospace">${trxId}</span></div>
    <div class="kwitansi-row"><span class="kw-label">Tanggal</span><span class="kw-value">${date}</span></div>
    <div class="kwitansi-row"><span class="kw-label">Jenis</span><span class="kw-value">${type}</span></div>
    <div class="kwitansi-row"><span class="kw-label">Muzakki</span><span class="kw-value">${name}</span></div>
    <div class="kwitansi-row"><span class="kw-label">Lembaga Penerima</span><span class="kw-value">${org}</span></div>
    <div class="kwitansi-row"><span class="kw-label">Metode</span><span class="kw-value">${method}</span></div>
    <div class="kwitansi-total"><span style="font-weight:700;font-size:14px;color:var(--muted)">TOTAL</span><span style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--g2)">${amount}</span></div>
    <div style="margin-top:14px;text-align:center;font-family:'Amiri',serif;font-size:15px;color:var(--gold);opacity:0.75">جَزَاكَ اللهُ خَيْرًا كَثِيرًا</div>`;

  const detailView = document.getElementById('kwitansi-detail-view');
  
  if (detailView && window.location.pathname.includes('kwitansi')) {
    const idEl = document.getElementById('kw-detail-id');
    const bodyEl = document.getElementById('kwitansi-detail-body');
    if(idEl) idEl.textContent = 'No. ' + kwId;
    if(bodyEl) bodyEl.innerHTML = body;
    detailView.style.display = 'block';
    detailView.scrollIntoView({behavior:'smooth'});
    
    const btnUnduh = detailView.querySelector('.btn-primary'); 
    if(btnUnduh) {
        btnUnduh.onclick = () => {
            showToast('📥 Mengunduh PDF Kwitansi...');
            window.location.href = `/kwitansi/download/${trxId}`; 
        };
    }
  } else {
    const idModal = document.getElementById('modal-kw-id');
    const bodyModal = document.getElementById('modal-kw-body');
    if(idModal) idModal.textContent = 'No. ' + kwId;
    if(bodyModal) bodyModal.innerHTML = body;
    
    const modalBtnUnduh = document.querySelector('#modal-kwitansi .btn-primary');
    if(modalBtnUnduh) {
        modalBtnUnduh.onclick = () => {
            showToast('📥 Mengunduh PDF Kwitansi...');
            window.location.href = `/kwitansi/download/${trxId}`; 
            closeModal('modal-kwitansi');
        };
    }
    openModal('modal-kwitansi');
  }
}

function init() {
  const now = new Date();
  const el = document.getElementById('current-date');
  if(el) el.textContent = now.toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'});

  // Menginisialisasi input awal saat halaman baru dibuka
  toggleGramInput();

  setInterval(fetchLiveHarga, 5000);

  document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if(e.target===m) m.classList.remove('open'); });
  });
}

document.addEventListener('DOMContentLoaded', init);