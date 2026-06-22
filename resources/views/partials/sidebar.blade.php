@php
    $role = auth()->user()->role;
    $name = auth()->user()->name;
    // Mengambil inisial huruf depan
    $initial = strtoupper(substr($name, 0, 1)); 
@endphp

<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-emblem">
      <div class="logo-icon">🕌</div>
      <div class="logo-text">
        <div class="brand">BMT Pondok Hijau</div>
        <div class="sub">Sistem ZISWAF Digital</div>
      </div>
    </div>
    <div class="bismillah">بِسْمِ اللهِ</div>
  </div>

  <nav id="sidebar-nav">
    @if($role === 'nasabah')
        <div class="nav-section"><div class="nav-label">Utama</div>
          <a href="{{ route('dashboard-user') }}" class="nav-item {{ request()->routeIs('dashboard-user') ? 'active' : '' }}" style="text-decoration:none;"><span class="nav-icon">🏠</span> Dashboard Saya</a>
        </div>
        <div class="nav-section"><div class="nav-label">ZISWAF Saya</div>
          <a href="{{ route('kalkulator') }}" class="nav-item {{ request()->routeIs('kalkulator') ? 'active' : '' }}" style="text-decoration:none;"><span class="nav-icon">⚖️</span> Hitung Zakat</a>
          <a href="{{ route('bayar') }}" class="nav-item {{ request()->routeIs('bayar') ? 'active' : '' }}" style="text-decoration:none;"><span class="nav-icon">💳</span> Bayar ZISWAF <span class="nav-badge">NEW</span></a>
          <a href="{{ route('marketplace') }}" class="nav-item {{ request()->routeIs('marketplace') ? 'active' : '' }}" style="text-decoration:none;"><span class="nav-icon">🕌</span> Program Donasi</a>
        </div>
        <div class="nav-section"><div class="nav-label">Riwayat</div>
          <a href="{{ route('transaksi') }}" class="nav-item {{ request()->routeIs('transaksi') ? 'active' : '' }}" style="text-decoration:none;"><span class="nav-icon">📋</span> Riwayat Transaksi</a>
          <a href="{{ route('kwitansi') }}" class="nav-item {{ request()->routeIs('kwitansi') ? 'active' : '' }}" style="text-decoration:none;"><span class="nav-icon">🧾</span> Kwitansi Saya</a>
        </div>
    @else
        <div class="nav-section"><div class="nav-label">Utama</div>
          <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" style="text-decoration:none;"><span class="nav-icon">🏠</span> Dashboard</a>
        </div>
        <div class="nav-section"><div class="nav-label">Pengelolaan ZISWAF</div>
          <a href="{{ route('kalkulator') }}" class="nav-item {{ request()->routeIs('kalkulator') ? 'active' : '' }}" style="text-decoration:none;"><span class="nav-icon">⚖️</span> Kalkulator Nisab</a>
          <a href="{{ route('auto-deduction') }}" class="nav-item {{ request()->routeIs('auto-deduction') ? 'active' : '' }}" style="text-decoration:none;"><span class="nav-icon">⚡</span> Auto-Deduction</a>
          <a href="{{ route('marketplace') }}" class="nav-item {{ request()->routeIs('marketplace') ? 'active' : '' }}" style="text-decoration:none;"><span class="nav-icon">🕌</span> Marketplace Donasi</a>
        </div>
        <div class="nav-section"><div class="nav-label">Administrasi</div>
          <a href="{{ route('anggota') }}" class="nav-item {{ request()->routeIs('anggota') ? 'active' : '' }}" style="text-decoration:none;"><span class="nav-icon">👥</span> Data Anggota</a>
          <a href="{{ route('laporan') }}" class="nav-item {{ request()->routeIs('laporan') ? 'active' : '' }}" style="text-decoration:none;"><span class="nav-icon">📊</span> Laporan ZISWAF</a>
          <a href="{{ route('transaksi') }}" class="nav-item {{ request()->routeIs('transaksi') ? 'active' : '' }}" style="text-decoration:none;"><span class="nav-icon">📋</span> Riwayat Transaksi</a>
          <a href="{{ route('kwitansi') }}" class="nav-item {{ request()->routeIs('kwitansi') ? 'active' : '' }}" style="text-decoration:none;"><span class="nav-icon">🧾</span> Kwitansi Digital</a>
        </div>
    @endif
    <div class="nav-section"><div class="nav-label">Sistem</div>
        <a href="{{ route('pengaturan') }}" class="nav-item {{ request()->routeIs('pengaturan') ? 'active' : '' }}" style="text-decoration:none;"><span class="nav-icon">⚙️</span> Pengaturan</a>
    </div>
  </nav>

  <div class="sidebar-footer">
    <div class="user-info-sidebar">
      <div class="avatar-sb">{{ $initial }}</div>
      <div>
        <div class="sb-name">{{ $name }}</div>
        <div class="sb-role">{{ $role === 'nasabah' ? 'Nasabah/Muzakki' : 'Admin ZISWAF' }}</div>
      </div>
      <!-- Form Logout -->
      <form method="POST" action="{{ route('logout') }}" style="margin-left:auto;">
          @csrf
          <button type="submit" class="sb-logout" title="Keluar">⏻</button>
      </form>
    </div>
  </div>
</aside>