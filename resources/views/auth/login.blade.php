<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — BMT Pondok Hijau</title>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Nunito:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div id="login-page">
  <div class="login-bg-ornament">
    <div class="orb orb1"></div>
    <div class="orb orb2"></div>
    <div class="login-geo"></div>
  </div>
  
  <div class="login-box">
    <div class="login-logo-wrap">
      <div class="login-star">🕌</div>
      <div class="login-brand">BMT Pondok Hijau</div>
      <div class="login-sub">Sistem ZISWAF Digital</div>
      <div class="login-arabic">بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ</div>
    </div>
    <div class="ornament-divider">✦ ❖ ✦</div>
    
    <div style="margin-top:20px">
      <div class="login-form-title">Masuk ke Sistem</div>
      
      <!-- Menampilkan Error jika salah password -->
      @if(session('error'))
        <div class="login-error show">{{ session('error') }}</div>
      @endif

      <!-- Form Login Asli -->
      <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="login-field">
          <label>Username</label>
          <input type="text" name="username" placeholder="Masukkan username Anda" autocomplete="username" required>
        </div>
        <div class="login-field">
          <label>Password</label>
          <input type="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
        </div>
        <div class="login-field">
          <label>Login Sebagai (Visual)</label>
          <select>
            <option value="nasabah">Nasabah / Muzakki</option>
            <option value="admin">Admin / Petugas ZISWAF</option>
          </select>
        </div>
        <button type="submit" class="btn-login">Masuk ke Sistem ➤</button>
      </form>
      
      <div class="login-demo">
        Gunakan: admin/admin123 &nbsp;|&nbsp; nasabah/nasabah123
      </div>
    </div>
  </div>
</div>

</body>
</html>