// ===================== DATA MOCKUP =====================
const NISAB_EMAS = 114325000;
const NISAB_PERAK = 15820000;

const CHART_DATA = [
  {m:'Jan',z:80,i:40,w:25},{m:'Feb',z:95,i:50,w:30},{m:'Mar',z:110,i:55,w:28},
  {m:'Apr',z:100,i:45,w:35},{m:'Mei',z:130,i:65,w:40},{m:'Jun',z:120,i:60,w:38}
];

const ANGGOTA = [
  {id:'BMT-0021',name:'Fathur Rahman',saldo:150000000,haul:true,auto:true,total:11250000},
  {id:'BMT-0034',name:'Siti Nurazizah',saldo:120000000,haul:true,auto:false,total:9000000},
  {id:'BMT-0056',name:'Hendra Saputra',saldo:200000000,haul:true,auto:true,total:15000000}
];

const PROGRAMS = [
  {name:'Zakat Fitrah 1444H',org:'BAZNAS Pusat',type:'zakat',icon:'🕌',bg:'prog-zakat',collected:84000000,target:100000000},
  {name:'Beasiswa Santri Berprestasi',org:'Dompet Dhuafa',type:'infaq',icon:'📚',bg:'prog-infaq',collected:37500000,target:50000000},
  {name:'Sumur Wakaf Pelosok',org:'BMT Pondok Hijau',type:'wakaf',icon:'💧',bg:'prog-wakaf',collected:12000000,target:25000000}
];

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

// ===================== RENDER FUNGSI (CHART, TABLE, DLL) =====================
function renderChart() {
  const max = 160;
  const bar = document.getElementById('chart-bar');
  if(!bar) return; 
  bar.innerHTML = CHART_DATA.map(d => `
    <div class="bar-group">
      <div class="bar-wrap">
        <div class="bar zakat" style="height:${(d.z/max*100)}%" title="Zakat Rp ${d.z} rb"></div>
        <div class="bar infaq" style="height:${(d.i/max*100)}%" title="Infaq Rp ${d.i} rb"></div>
        <div class="bar wakaf" style="height:${(d.w/max*100)}%" title="Wakaf Rp ${d.w} rb"></div>
      </div>
      <div class="bar-month">${d.m}</div>
    </div>`).join('');
}

function renderAnggota(data) {
  const el = document.getElementById('anggota-table');
  if(!el) return;
  el.innerHTML = data.map(a => {
    const nisab = a.saldo >= NISAB_PERAK;
    const wajib = nisab && a.haul;
    return `<tr>
      <td style="font-family:monospace;font-size:11.5px">${a.id}</td>
      <td style="font-weight:600">${a.name}</td>
      <td style="font-weight:700">Rp ${(a.saldo/1000000).toFixed(1)} Jt</td>
      <td><span class="badge ${wajib?'badge-green':nisab?'badge-gold':'badge-red'}">${wajib?'✅ Wajib Zakat':nisab?'⏳ Belum Haul':'❌ Belum Nisab'}</span></td>
      <td><span class="badge ${a.auto?'badge-green':'badge-red'}">${a.auto?'Aktif':'Nonaktif'}</span></td>
      <td style="font-weight:700;color:var(--g3)">Rp ${(a.total/1000000).toFixed(1)} Jt</td>
      <td><button class="btn btn-outline btn-sm" onclick="showToast('📋 Detail ${a.name} dibuka')">Detail</button></td>
    </tr>`;
  }).join('');
}

function renderAutoDeduction() {
  const el = document.getElementById('auto-deduction-table');
  if(!el) return;
  const wajib = ANGGOTA.filter(a => a.saldo >= NISAB_PERAK && a.haul);
  const countBadge = document.getElementById('auto-deduction-count');
  if(countBadge) countBadge.textContent = wajib.length + ' Anggota';
  
  el.innerHTML = wajib.map(a => {
    const z = a.saldo * 0.025;
    return `<tr>
      <td style="font-family:monospace;font-size:11.5px">${a.id}</td>
      <td style="font-weight:600">${a.name}</td>
      <td>Rp ${(a.saldo/1000000).toFixed(1)} Jt</td>
      <td>Rp ${(a.saldo/1000000).toFixed(1)} Jt</td>
      <td><span class="badge badge-green">✅ Terpenuhi</span></td>
      <td style="font-weight:800;color:var(--g3)">Rp ${(z/1000).toLocaleString('id-ID')}</td>
      <td><button class="btn btn-primary btn-sm" onclick="showToast('⚡ Zakat ${a.name} berhasil dipotong!')">⚡ Potong</button></td>
    </tr>`;
  }).join('');
}

function renderPrograms(filter='semua') {
  const grid = document.getElementById('program-grid');
  if(!grid) return;
  const data = filter==='semua' ? PROGRAMS : PROGRAMS.filter(p=>p.type===filter);
  grid.innerHTML = data.map(p => {
    const pct = Math.min((p.collected/p.target)*100,100).toFixed(0);
    return `<div class="program-card">
      <div class="program-img ${p.bg}">${p.icon}</div>
      <div class="program-body">
        <div class="program-name">${p.name}</div>
        <div class="program-org">🏛 ${p.org}</div>
        <div class="progress-bar-wrap"><div class="progress-fill ${p.type==='infaq'||p.type==='wakaf'?'gold':''}" style="width:${pct}%"></div></div>
        <div class="prog-amounts">
          <div><span class="prog-collected">Rp ${(p.collected/1000000).toFixed(1)} Jt</span> terkumpul</div>
          <div>Target Rp ${(p.target/1000000).toFixed(0)} Jt</div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center">
          <span style="font-size:12px;color:var(--muted)">${pct}% tercapai</span>
          <button class="btn btn-primary btn-sm" onclick="openModalDonasi('${p.name}','${p.org}')">Donasi ➤</button>
        </div>
      </div>
    </div>`;
  }).join('');
}

// ===================== FUNGSI SINKRONISASI GRAM + TUNAI =====================
function toggleGramInput() {
  const kategori = document.getElementById('kategori-harta').value;
  const wrapGram = document.getElementById('wrap-jumlah-gram');
  const gramInput = document.getElementById('gram-input');
  
  if (!wrapGram) return;

  if (kategori === 'Emas' || kategori === 'Perak') {
    wrapGram.style.display = 'block';
    wrapGram.style.animation = 'fadeIn 0.3s ease';
  } else {
    // Jika pilih Ternak/Kebun, sembunyikan input gram dan reset isinya
    wrapGram.style.display = 'none';
    if(gramInput) gramInput.value = 0;
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
  
    setTimeout(() => {
        const elEmas = document.getElementById('teks-harga-emas');
        const elPerak = document.getElementById('teks-harga-perak');
        
        const hargaEmas = elEmas ? parseFloat(elEmas.innerText.replace(/[^0-9]/g, '')) : 2673000;
        const hargaPerak = elPerak ? parseFloat(elPerak.innerText.replace(/[^0-9]/g, '')) : 52658;
        
        const saldo = parseFloat(saldoInput.value) || 0;
        const hutang = parseFloat(document.getElementById('hutang-input').value) || 0;
        const haul = document.getElementById('haul-input').value;
        
        const bersih = saldo - hutang;
        
        let nisab = 0;
        if (type === 'penghasilan') {
            nisab = 15820000; 
        } else {
            nisab = (kategori === 'Perak') ? (595 * hargaPerak) : (85 * hargaEmas);
        }
        
        const wajib = (bersih >= nisab && haul === 'ya');
        const zakat = wajib ? (bersih * 0.025) : 0;
        
        const fmt = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');
        
        r.innerHTML = `
          <div class="result-box" style="animation: fadeIn 0.4s ease;">
            <div class="result-label">Harta Bersih (Saldo − Hutang)</div>
            <div class="result-value">${fmt(bersih)}</div>
            <div class="result-note">Nisab ${type === 'penghasilan' ? 'Profesi' : kategori}: ${fmt(nisab)}</div>
          </div>
          <div class="nisab-status ${wajib?'wajib':'belum'}" style="animation: fadeIn 0.4s ease; padding: 12px; border-radius: 8px; margin-top: 10px; background: ${wajib ? 'var(--gold3)' : 'var(--g8)'};">
            <div style="display:flex; align-items:center; gap:8px;">
                <div class="status-icon" style="font-size:24px">${wajib?'✅':'⏳'}</div>
                <div style="font-weight:700; color:var(--g3)">${wajib?'Wajib Zakat':'Belum Mencapai Nisab/Haul'}</div>
            </div>
          </div>
          ${wajib?`
          <div class="result-box" style="margin-top:14px;background:var(--gold3);border-color:rgba(201,168,76,0.3); animation: fadeIn 0.4s ease;">
            <div class="result-label" style="color:#a07828">Jumlah Zakat Anda</div>
            <div class="result-value" style="color:var(--g1)">${fmt(zakat)}</div>
          </div>
          <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap; animation: fadeIn 0.4s ease;">
            <a href="/bayar?nominal=${Math.round(zakat)}" class="btn btn-primary">💳 Bayar Zakat Sekarang</a>
            <a href="/marketplace" class="btn btn-outline">🕌 Pilih Lembaga</a>
          </div>`:''}
        `;
    }, 400); 
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
    if (!result.success) return;

    if (result.paid) {
      hentikanPollingQris();
      document.getElementById('qris-status-text').textContent = '✅ Pembayaran diterima!';
      showToast('🎉 Pembayaran QRIS berhasil! Kwitansi terbit.');
      setTimeout(() => tampilkanSuksesQris(result), 800);
    } else if (result.status === 'Batal') {
      hentikanPollingQris();
      document.getElementById('qris-status-text').textContent = '⌛ QRIS kadaluarsa / dibatalkan.';
      showToast('⚠️ Pembayaran QRIS kadaluarsa atau dibatalkan.');
    }
  } catch (e) {
    // Abaikan error sementara; polling berikutnya akan mencoba lagi.
  }
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

  renderChart();
  renderAnggota(ANGGOTA);
  renderAutoDeduction();
  renderPrograms();
  
  // Menginisialisasi input awal saat halaman baru dibuka
  toggleGramInput();

  setInterval(fetchLiveHarga, 5000);

  document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if(e.target===m) m.classList.remove('open'); });
  });
}

document.addEventListener('DOMContentLoaded', init);