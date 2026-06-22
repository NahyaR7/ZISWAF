<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BMT Pondok Hijau</title>
    <link href="https://fonts.googleapis.com/css2?family=Amiri&family=Nunito:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        /* Palet Warna Persis Sesuai style.css Anda */
        :root {
            --g1:#0d2818; --g2:#1a3d2b; --g3:#2d6a4f; --g4:#40916c;
            --g8:#f0faf3; --gold:#c9a84c; --gold2:#f0d080;
            --text:#1e2d25; --muted:#6b8070; --border:#d8e4db;
            --bg:#f5faf6;
        }

        body, html {
            margin: 0; padding: 0;
            font-family: 'Nunito', sans-serif;
            background-color: var(--bg);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 960px;
            min-height: 560px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 12px 48px rgba(13,40,24,0.15);
            overflow: hidden;
            margin: 20px;
        }

        /* BAGIAN KIRI (Gaya BMT Pondok Hijau) */
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, var(--g1) 0%, var(--g2) 100%);
            /* Ornamen Bintang Khas */
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 5l2.5 8.5h9l-7.5 5.5 2.5 8.5-7.5-5.5-7.5 5.5 2.5-8.5-7.5-5.5h9z' fill='rgba(64,145,108,0.1)' fill-rule='evenodd'/%3E%3C/svg%3E"), linear-gradient(135deg, var(--g1) 0%, var(--g2) 100%);
            color: white;
            padding: 40px;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            text-align: center; position: relative;
        }

        .left-icon { font-size: 56px; margin-bottom: 12px; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3)); }
        .left-title { font-family: 'Playfair Display', serif; font-size: 30px; font-weight: 700; margin: 0 0 8px 0; color: white; letter-spacing: 0.5px; }
        .left-arabic { font-family: 'Amiri', serif; font-size: 26px; color: var(--gold2); margin-bottom: 24px; }
        .left-subtitle { font-size: 13px; line-height: 1.6; color: rgba(255,255,255,0.8); max-width: 85%; margin-bottom: 40px; }
        
        .left-badges {
            border: 1px solid rgba(255,255,255,0.15);
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            color: var(--g8);
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }

        /* BAGIAN KANAN (Formulir Login) */
        .login-right {
            flex: 1.1;
            padding: 40px 50px;
            display: flex; flex-direction: column; justify-content: center;
            background: white; position: relative;
        }

        .right-header { margin-bottom: 28px; }
        .right-title { font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 700; color: var(--g1); margin: 0 0 6px 0; }
        .right-subtitle { font-size: 13px; color: var(--muted); margin: 0; }

        .form-group { margin-bottom: 18px; }
        .form-label-wrap { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .form-label { font-size: 11px; font-weight: 700; color: var(--g3); text-transform: uppercase; letter-spacing: 0.5px; }
        .forgot-link { font-size: 11px; color: var(--muted); text-decoration: none; font-weight: 600; transition: color 0.2s; }
        .forgot-link:hover { color: var(--g4); }

        .input-wrapper { position: relative; display: flex; align-items: center; }
        .input-icon { position: absolute; left: 16px; color: var(--muted); }
        .input-eye { position: absolute; right: 16px; color: var(--muted); cursor: pointer; transition: color 0.2s; }
        .input-eye:hover { color: var(--g4); }

        .form-control {
            width: 100%;
            padding: 13px 40px 13px 44px;
            background: var(--g8); /* Sesuai variabel CSS Anda */
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-size: 13px; font-family: inherit; color: var(--text);
            transition: all 0.2s ease; box-sizing: border-box; outline: none;
        }
        .form-control::placeholder { color: #9ca3af; font-weight: 500; }
        .form-control:focus { border-color: var(--g4); background: white; box-shadow: 0 0 0 3px rgba(64,145,108,0.1); }

        .btn-submit {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, var(--g3), var(--g4));
            color: white; border: none; border-radius: 12px;
            font-family: inherit; font-size: 14px; font-weight: 700;
            cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;
            display: flex; justify-content: center; align-items: center; gap: 8px;
            margin-top: 10px;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(64,145,108,0.3); }

        .btn-google {
            width: 100%; padding: 12px;
            background: white; color: var(--text);
            border: 1.5px solid var(--border); border-radius: 12px;
            font-family: inherit; font-size: 13px; font-weight: 700;
            cursor: pointer; transition: all 0.2s;
            display: flex; justify-content: center; align-items: center; gap: 10px;
            margin-top: 16px; text-decoration: none;
        }
        .btn-google:hover { background: var(--g8); border-color: var(--g4); }

        .divider { display: flex; align-items: center; text-align: center; margin: 20px 0; color: var(--muted); font-size: 11px; font-weight: 700; letter-spacing: 1px; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid var(--border); }
        .divider:not(:empty)::before { margin-right: 1em; }
        .divider:not(:empty)::after { margin-left: 1em; }

        .register-link { text-align: center; font-size: 13px; color: var(--muted); margin-top: 20px; }
        .register-link a { color: var(--g4); font-weight: 700; text-decoration: none; }

        .footer-text { text-align: center; font-size: 10px; color: var(--muted); letter-spacing: 1px; margin-top: 24px; text-transform: uppercase; font-weight: 700; }

        .alert-error {
            background: rgba(231,76,60,0.1); color: #c0392b;
            padding: 12px 16px; border-radius: 10px; font-size: 12px;
            margin-bottom: 20px; border: 1px solid rgba(231,76,60,0.3);
            display: flex; align-items: center; gap: 8px; font-weight: 600;
        }

        @media (max-width: 768px) {
            .login-wrapper { flex-direction: column; min-height: auto; margin: 16px; border-radius: 20px;}
            .login-left { padding: 40px 20px; }
            .login-right { padding: 30px 24px; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-left">
        <div class="left-icon">🕌</div>
        <h1 class="left-title">BMT Pondok Hijau</h1>
        <div class="left-arabic">بَيْتُ المَالِ وَالتَّمْوِيلِ</div>
        <p class="left-subtitle">Platform Digital ZISWAF Terintegrasi untuk kemudahan pembayaran Zakat, Infaq, Sedekah, dan Wakaf secara transparan dan aman.</p>
        <div class="left-badges">
            Amanah &bull; Profesional &bull; Syariah
        </div>
    </div>

    <div class="login-right">
        <div class="right-header">
            <h2 class="right-title">Selamat Datang Kembali</h2>
            <p class="right-subtitle">Silakan masuk untuk mengakses layanan</p>
        </div>

        @if ($errors->any())
            <div class="alert-error">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ $errors->first('email') }}</span>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <div class="form-label-wrap"><label class="form-label">Email / Username</label></div>
                <div class="input-wrapper">
                    <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <input type="email" name="email" class="form-control" placeholder="Contoh: xxxxx@gmail.com" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <div class="form-label-wrap">
                    <label class="form-label">Password</label>
                    <a href="#" class="forgot-link">Lupa Password?</a>
                </div>
                <div class="input-wrapper">
                    <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan Kata sandi Anda" required>
                    <svg class="input-eye" id="togglePassword" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
            </div>

            <button type="submit" class="btn-submit">Masuk Sekarang &rarr;</button>
        </form>

        <div class="divider">ATAU</div>

        <a href="{{ route('google.login') }}" class="btn-google">
            <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
            Masuk dengan Google
        </a>

        <div class="register-link">Belum punya akun? <a href="{{ route('register') }}">Daftar Akun</a></div>
        <div class="footer-text">&bull; ------------- &bull;</div>
    </div>
</div>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    togglePassword.addEventListener('click', function (e) {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.style.color = type === 'text' ? 'var(--g4)' : 'var(--muted)';
    });
</script>
</body>
</html>