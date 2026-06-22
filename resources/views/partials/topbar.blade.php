@php
    // Mengambil notifikasi yang belum dibaca dari Database
    $unreadNotifs = auth()->user()->unreadNotifications;
    $pesanNotif = $unreadNotifs->count() > 0 ? $unreadNotifs->first()->data['message'] : 'Tidak ada notifikasi baru.';
@endphp

<div class="topbar">
  <div>
    <div class="page-title">@yield('page-title', 'Dashboard')</div>
    <div class="page-sub">@yield('page-subtitle', 'Selamat datang di Sistem ZISWAF BMT Pondok Hijau')</div>
  </div>
  <div class="topbar-right">
    <button class="theme-toggle" onclick="toggleTheme()" title="Ganti Tema" id="theme-btn">🌙</button>
    
    <button class="btn-notif" onclick="bacaNotifikasi()">🔔
        @if($unreadNotifs->count() > 0)
            <span class="notif-dot"></span>
        @endif
    </button>
    
    <div class="date-badge" id="current-date"></div>
    <div class="user-chip">
      <div class="uc-avatar" id="uc-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
      <div>
        <div class="uc-name" id="uc-name">{{ auth()->user()->name }}</div>
        <div class="uc-role" id="uc-role">{{ auth()->user()->role === 'admin' ? 'Admin' : 'Nasabah' }}</div>
      </div>
    </div>
  </div>
</div>

<script>
// Fungsi JS untuk menampilkan notifikasi dari DB dan menandainya sebagai "Telah Dibaca"
function bacaNotifikasi() {
    let pesan = "{{ $pesanNotif }}";
    let jumlah = {{ $unreadNotifs->count() }};
    
    if(jumlah > 0) {
        showToast('📬 ' + pesan);
        // Memanggil API latar belakang untuk menghapus badge merah
        fetch("{{ route('notifikasi.read') }}")
            .then(() => {
                let dot = document.querySelector('.notif-dot');
                if(dot) dot.style.display = 'none';
            });
    } else {
        showToast('📭 ' + pesan);
    }
}
</script>