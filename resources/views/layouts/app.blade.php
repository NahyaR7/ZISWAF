<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ZISWAF BMT Pondok Hijau')</title>
    <!-- Font Arab & Font Utama -->
    <link href="https://fonts.googleapis.com/css2?family=Amiri&family=Nunito:wght@400;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <!-- CSS Utama -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Toast Notifikasi Global -->
    <div id="toast" class="toast"></div>

    <!-- ===================== SIDEBAR KIRI ===================== -->
    <div class="sidebar">
        <!-- Logo -->
        <div class="sidebar-logo">
            <div class="logo-emblem">
                <div class="logo-icon">🕌</div>
                <div class="logo-text">
                    <div class="brand">Pondok Hijau</div>
                    <div class="sub">ZISWAF System</div>
                </div>
            </div>
            <div class="bismillah">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم</div>
        </div>

        <!-- Daftar Menu (Dinamis Berdasarkan Role) -->
        <div style="overflow-y: auto; flex: 1;">
            
            @if(auth()->user()->role === 'admin')
                <!-- MENU KHUSUS ADMIN -->
                <div class="nav-section">
                    <div class="nav-label">Main Menu</div>
                    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" style="text-decoration: none;">
                        <div class="nav-icon">📊</div> Dashboard Admin
                    </a>
                    <a href="{{ route('anggota') }}" class="nav-item {{ request()->routeIs('anggota') ? 'active' : '' }}" style="text-decoration: none;">
                        <div class="nav-icon">👥</div> Data Anggota
                    </a>
                    <a href="{{ route('transaksi') }}" class="nav-item {{ request()->routeIs('transaksi') ? 'active' : '' }}" style="text-decoration: none;">
                        <div class="nav-icon">💸</div> Validasi Transaksi
                    </a>
                    <a href="{{ route('laporan') }}" class="nav-item {{ request()->routeIs('laporan') ? 'active' : '' }}" style="text-decoration: none;">
                        <div class="nav-icon">📑</div> Laporan & Audit
                    </a>
                </div>
                <div class="nav-section">
                    <div class="nav-label">Sistem</div>
                    <a href="{{ route('kalkulator') }}" class="nav-item {{ request()->routeIs('kalkulator') ? 'active' : '' }}" style="text-decoration: none;">
                        <div class="nav-icon">⚖️</div> Kalkulator Zakat
                    </a>
                    <a href="{{ route('pengaturan') }}" class="nav-item {{ request()->routeIs('pengaturan') ? 'active' : '' }}" style="text-decoration: none;">
                        <div class="nav-icon">⚙️</div> Pengaturan
                    </a>
                </div>

            @else
                <!-- MENU KHUSUS NASABAH -->
                <div class="nav-section">
                    <div class="nav-label">Layanan Utama</div>
                    <a href="{{ route('dashboard-user') }}" class="nav-item {{ request()->routeIs('dashboard-user') ? 'active' : '' }}" style="text-decoration: none;">
                        <div class="nav-icon">🏠</div> Dashboard Saya
                    </a>
                    <a href="{{ route('bayar') }}" class="nav-item {{ request()->routeIs('bayar') ? 'active' : '' }}" style="text-decoration: none;">
                        <div class="nav-icon">💳</div> Bayar ZISWAF
                    </a>
                    <a href="{{ route('marketplace') }}" class="nav-item {{ request()->routeIs('marketplace') ? 'active' : '' }}" style="text-decoration: none;">
                        <div class="nav-icon">🕌</div> Program Donasi
                    </a>
                </div>
                <div class="nav-section">
                    <div class="nav-label">Pusat Bantuan</div>
                    <a href="{{ route('kalkulator') }}" class="nav-item {{ request()->routeIs('kalkulator') ? 'active' : '' }}" style="text-decoration: none;">
                        <div class="nav-icon">⚖️</div> Kalkulator Zakat
                    </a>
                    <a href="{{ route('kwitansi') }}" class="nav-item {{ request()->routeIs('kwitansi') ? 'active' : '' }}" style="text-decoration: none;">
                        <div class="nav-icon">🧾</div> Riwayat Transaksi
                    </a>
                </div>
            @endif

        </div>

        <!-- Profil Pengguna Bawah Sidebar (Dinamis) -->
        <div class="sidebar-footer">
            <div class="user-info-sidebar">
                <!-- Mengambil huruf pertama dari nama -->
                <div class="avatar-sb">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <!-- Menampilkan Nama Asli Pengguna -->
                    <div class="sb-name" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px;">
                        {{ auth()->user()->name }}
                    </div>
                    <!-- Menampilkan Role Pengguna (Nasabah / Admin) -->
                    <div class="sb-role">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
                <!-- Tombol Logout -->
                <form action="{{ route('logout') }}" method="POST" style="margin-left:auto;">
                    @csrf
                    <button type="submit" class="sb-logout" title="Keluar dari Sistem">⏻</button>
                </form>
            </div>
        </div>
    </div>

    <!-- ===================== KONTEN UTAMA ===================== -->
    <div class="main">
        <!-- Topbar Atas -->
        <div class="topbar">
            <div>
                <div class="page-title">@yield('page-title', 'Dashboard')</div>
                <div class="page-sub">@yield('page-subtitle', 'Sistem Informasi Manajemen ZISWAF')</div>
            </div>
            <div class="topbar-right">
                <div class="date-badge" id="current-date"></div>
                <button class="theme-toggle" id="theme-btn" onclick="toggleTheme()">🌙</button>
                
                <!-- Profil Pengguna Topbar (Dinamis) -->
                <div class="user-chip">
                    <div class="uc-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div>
                        <!-- Menampilkan Kata Pertama dari Nama -->
                        <div class="uc-name">{{ explode(' ', auth()->user()->name)[0] }}</div>
                        <div class="uc-role">{{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Render Halaman -->
        <div class="content">
            @yield('content')
        </div>
    </div>

    <!-- Script Utama Aplikasi -->
    <script src="{{ asset('js/main.js') }}"></script>
    
    <!-- Script Tambahan (Jika Ada) -->
    @stack('scripts')
</body>
</html>