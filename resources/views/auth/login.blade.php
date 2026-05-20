<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login – SIMRS RSP UI</title>
  <link rel="stylesheet" href="{{ asset('css/simrs.css') }}">
</head>
<body style="background: var(--bg);">

<div class="login-page">

  <div class="login-left">
    <div class="login-brand">
      <div class="logo-wrap" style="width:80px;height:80px;background:transparent;box-shadow:none;border:none;">
        <img src="{{ asset('images/logo.png') }}" style="width:100%;height:100%;object-fit:contain;" alt="Logo RSUI Khanza">
      </div>
      <h1>SIMRS RSP UI</h1>
      <p>Sistem Informasi Manajemen<br>Rumah Sakit Terintegrasi</p>

      <div class="login-feature-list">
        <div class="login-feature">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          Manajemen Pasien & Pendaftaran
        </div>
        <div class="login-feature">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          Rawat Jalan & Rawat Inap
        </div>
        <div class="login-feature">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          Farmasi, Lab & Radiologi
        </div>
        <div class="login-feature">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          Laporan & Analitik Real-time
        </div>
      </div>
    </div>
  </div>

  <div class="login-right">
    <div class="login-form-wrap">
      <h2>Selamat Datang</h2>
      <p>Masuk ke akun Anda untuk melanjutkan</p>

      @if(session('error'))
        <div class="alert alert-danger">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          {{ session('error') }}
        </div>
      @endif

      <form class="login-form" action="{{ url('/login') }}" method="POST">
        @csrf
        <div class="form-group">
          <label class="form-label" for="username">Username</label>
          <div class="input-group">
            <span class="input-group-text">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
              </svg>
            </span>
            <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-group">
            <span class="input-group-text">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
              </svg>
            </span>
            <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
            
            <span class="input-group-text" id="togglePassword" style="cursor: pointer; background: transparent;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </span>
            </div>
        </div>

        <div class="form-group" style="display:flex; align-items:center; justify-content:space-between;">
          <label style="display:flex; align-items:center; gap:8px; font-size:13px; cursor:pointer;">
            <input type="checkbox" name="remember"> Ingat saya
          </label>
        </div>

        <div class="form-group">
          <label class="form-label" for="captcha">Verifikasi Keamanan</label>
          <div style="display: flex; gap: 10px; margin-bottom: 8px;">
            <div style="border: 1px solid #ced4da; border-radius: 4px; overflow: hidden; background: #fff; display: flex; align-items: center; justify-content: center;">
              <img src="/captcha" id="captcha-img" alt="Captcha" style="display:block; height: 38px;">
            </div>
            <button type="button" class="btn btn-outline-secondary" id="reload-captcha" style="padding: 0 12px; display: flex; align-items: center; justify-content: center;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"></path>
              </svg>
            </button>
          </div>
          <div class="input-group">
            <span class="input-group-text">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
              </svg>
            </span>
            <input type="number" id="captcha" name="captcha" class="form-control" placeholder="Berapa hasil hitungan di atas?" required>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width:100%; justify-content:center; margin-top:8px;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
            <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
          </svg>
          Masuk ke Sistem
        </button>
      </form>

      <div class="login-footer">
        <p>Lupa password? Hubungi Administrator</p>
        <p style="margin-top:16px;">© {{ date('Y') }} SIMRS RSP UI Development By SIMRS & TI – Hak Cipta Dilindungi</p>
      </div>
    </div>
  </div>

</div>

<script src="{{ asset('js/simrs.js') }}"></script>

<script>
  document.addEventListener("DOMContentLoaded", function() {
      const togglePassword = document.querySelector('#togglePassword');
      const password = document.querySelector('#password');

      togglePassword.addEventListener('click', function () {
          // Ubah tipe input dari password ke text atau sebaliknya
          const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
          password.setAttribute('type', type);
          
          // Ubah warna ikon saat diklik agar terlihat perbedaannya
          this.style.color = type === 'password' ? '#6c757d' : '#0d6efd';
      });

      const reloadCaptchaBtn = document.querySelector('#reload-captcha');
      const captchaImg = document.querySelector('#captcha-img');
      if (reloadCaptchaBtn && captchaImg) {
          reloadCaptchaBtn.addEventListener('click', function() {
              captchaImg.src = '/captcha?_=' + new Date().getTime();
          });
      }
  });
</script>
</body>
</html>