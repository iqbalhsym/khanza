<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') – SIMRS RSP UI</title>

  <!-- Tabler CSS CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css">
  <!-- Bridge: maps old custom classes → Tabler/Bootstrap equivalents -->
  <link rel="stylesheet" href="{{ asset('css/bridge.css') }}">
  @stack('styles')
</head>
<body class="antialiased">
<div class="wrapper">

  <!-- ============================
       TOP NAVBAR (Tabler)
       ============================ -->
  <header class="navbar navbar-expand-md navbar-light d-print-none sticky-top" style="background: #206bc4; border-bottom: 1px solid #1a5ba8;">
    <div class="container-fluid">

      <!-- Brand / Logo -->
      <a href="{{ url('/dashboard') }}" class="navbar-brand d-flex align-items-center gap-2" style="color: #fff; font-weight: 700; text-decoration: none;">
        <img src="{{ asset('images/logo.png') }}" alt="Logo RSUI Khanza" style="height: 34px; width: 34px; object-fit: contain; filter: brightness(0) invert(1);">
        <span class="d-none d-md-inline" style="font-size: 15px; letter-spacing: 0.5px;">SIMRS RSP UI</span>
      </a>

      <!-- Mobile toggle -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
        <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
      </button>

      <!-- Nav Links -->
      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav me-auto ms-3 align-items-md-center" style="gap: 2px;">

          <!-- Dashboard -->
          <li class="nav-item">
            <a href="{{ url('/dashboard') }}" class="nav-link @if(request()->is('dashboard')) active fw-bold @endif"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('dashboard')) background: rgba(255,255,255,0.2); color: #fff; @endif">
              Dashboard
            </a>
          </li>

          <!-- Pendaftaran -->
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle @if(request()->is('pendaftaran*')) active @endif"
               data-bs-toggle="dropdown"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('pendaftaran*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Pendaftaran
            </a>
            <ul class="dropdown-menu shadow-sm border-0" style="min-width: 180px; margin-top: 8px;">
              <li><a class="dropdown-item" href="{{ url('/pendaftaran') }}">Antrian</a></li>
              <li><a class="dropdown-item" href="{{ url('/pendaftaran/pasien-baru') }}">Pasien Baru</a></li>
              <li><a class="dropdown-item" href="{{ url('/pendaftaran/pasien-lama') }}">Pasien Lama</a></li>
            </ul>
          </li>

          <!-- Rawat Jalan -->
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle @if(request()->is('rawat-jalan*')) active @endif"
               data-bs-toggle="dropdown"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('rawat-jalan*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Rawat Jalan
            </a>
            <ul class="dropdown-menu shadow-sm border-0" style="min-width: 180px; margin-top: 8px;">
              <li><a class="dropdown-item" href="{{ url('/rawat-jalan') }}">Registered Pasien</a></li>
              <li><a class="dropdown-item" href="{{ url('/rawat-jalan/pemeriksaan') }}">Pemeriksaan</a></li>
            </ul>
          </li>

          <!-- Rawat Inap -->
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle @if(request()->is('rawat-inap*')) active @endif"
               data-bs-toggle="dropdown"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('rawat-inap*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Rawat Inap
            </a>
            <ul class="dropdown-menu shadow-sm border-0" style="min-width: 180px; margin-top: 8px;">
              <li><a class="dropdown-item" href="{{ url('/rawat-inap') }}">Pasien Dirawat</a></li>
              <li><a class="dropdown-item" href="{{ url('/rawat-inap/kamar') }}">Kamar &amp; Bangsal</a></li>
            </ul>
          </li>

          <!-- Laboratorium -->
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle @if(request()->is('laboratorium*')) active @endif"
               data-bs-toggle="dropdown"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('laboratorium*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Laboratorium
            </a>
            <ul class="dropdown-menu shadow-sm border-0" style="min-width: 180px; margin-top: 8px;">
              <li><a class="dropdown-item" href="{{ url('/laboratorium') }}">Antrian</a></li>
              <li><a class="dropdown-item" href="{{ url('/laboratorium/hasil') }}">Input Hasil</a></li>
            </ul>
          </li>

          <!-- Radiologi -->
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle @if(request()->is('radiologi*')) active @endif"
               data-bs-toggle="dropdown"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('radiologi*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Radiologi
            </a>
            <ul class="dropdown-menu shadow-sm border-0" style="min-width: 180px; margin-top: 8px;">
              <li><a class="dropdown-item" href="{{ url('/radiologi') }}">Antrian</a></li>
            </ul>
          </li>

          <!-- Farmasi -->
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle @if(request()->is('farmasi*')) active @endif"
               data-bs-toggle="dropdown"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('farmasi*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Farmasi
            </a>
            <ul class="dropdown-menu shadow-sm border-0" style="min-width: 180px; margin-top: 8px;">
              <li><a class="dropdown-item" href="{{ url('/farmasi') }}">Antrian Resep</a></li>
              <li><a class="dropdown-item" href="{{ url('/farmasi/stok') }}">Stok Obat</a></li>
            </ul>
          </li>

          <!-- Billing -->
          <li class="nav-item">
            <a href="{{ url('/billing') }}" class="nav-link @if(request()->is('billing*')) active @endif"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('billing*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Billing
            </a>
          </li>

          <!-- Master -->
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle @if(request()->is('master*')) active @endif"
               data-bs-toggle="dropdown"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('master*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Master
            </a>
            <ul class="dropdown-menu shadow-sm border-0" style="min-width: 180px; margin-top: 8px;">
              <li><a class="dropdown-item" href="{{ url('/master/dokter') }}">Data Dokter</a></li>
              <li><a class="dropdown-item" href="{{ url('/master/poli') }}">Poliklinik</a></li>
              <li><a class="dropdown-item" href="{{ url('/master/obat') }}">Data Obat</a></li>
              <li><a class="dropdown-item" href="{{ url('/master/kamar') }}">Kamar / Kelas</a></li>
            </ul>
          </li>

        </ul>

        <!-- Right Side: Clock + User -->
        <div class="d-flex align-items-center gap-3 ms-auto">
          <div class="d-none d-lg-block text-end" style="color: rgba(255,255,255,0.8); font-size: 11px; line-height: 1.3;">
            <div id="liveClock" style="font-size: 15px; font-weight: 700; color: #fff; font-family: monospace;">--:--:--</div>
            <div id="liveDate">--</div>
          </div>

          <!-- Notification -->
          <a href="#" class="btn btn-ghost-light btn-icon" title="Notifikasi" style="color: rgba(255,255,255,0.85);">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
          </a>

          <!-- User Dropdown -->
          <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown"
               style="color: rgba(255,255,255,0.85); font-size: 13px;">
              <span class="avatar avatar-sm" style="background: rgba(255,255,255,0.25); color: #fff; font-weight: 700;">A</span>
              <span class="d-none d-md-inline">Administrator</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="margin-top: 8px;">
              <li><h6 class="dropdown-header">Super Admin</h6></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="{{ url('/logout') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Keluar
              </a></li>
            </ul>
          </div>
        </div>
      </div><!-- end collapse -->
    </div>
  </header>
  <!-- END NAVBAR -->

  <!-- ============================
       PAGE WRAPPER
       ============================ -->
  <div class="page-wrapper">
    <!-- Page Header (breadcrumb area) -->
    <div class="page-header d-print-none" style="padding: 12px 0 0;">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col">
            <h2 class="page-title" style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0;">
              @yield('page-title', 'Dashboard')
            </h2>
            @hasSection('breadcrumb')
            <ol class="breadcrumb" style="margin: 2px 0 0; font-size: 12px;">
              <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" style="color: #64748b;">Home</a></li>
              <li class="breadcrumb-item active text-muted">@yield('breadcrumb')</li>
            </ol>
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="page-body">
      <div class="container-fluid" style="padding-top: 16px; padding-bottom: 24px;">
        @yield('content')
      </div>
    </div>
  </div>
  <!-- END PAGE WRAPPER -->

</div><!-- end wrapper -->

<!-- Tabler JS CDN (includes Bootstrap 5) -->
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
<!-- Legacy interaction helpers (tabs, table search, etc.) -->
<script src="{{ asset('js/simrs.js') }}"></script>

<script>
// Live Clock
(function clock() {
  const tick = () => {
    const now = new Date();
    const el = document.getElementById('liveClock');
    const de = document.getElementById('liveDate');
    if (el) el.textContent = now.toLocaleTimeString('id-ID');
    if (de) de.textContent = now.toLocaleDateString('id-ID', { weekday:'long', day:'2-digit', month:'long', year:'numeric' });
  };
  tick();
  setInterval(tick, 1000);
})();
</script>
@stack('scripts')
</body>
</html>
