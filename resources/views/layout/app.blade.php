<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') – SIMRS RSP UI</title>

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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
  <header class="navbar navbar-expand-md navbar-dark d-print-none sticky-top" style="background: linear-gradient(135deg, #1d4ed8, #0e5aa7); border-bottom: 1px solid rgba(255,255,255,0.12); box-shadow: 0 4px 25px rgba(29, 78, 216, 0.18);">
    <div class="container-fluid">

      <!-- Brand / Logo -->
      <a href="{{ url('/dashboard') }}" class="navbar-brand d-flex align-items-center gap-2" style="color: #fff; font-weight: 700; text-decoration: none;">
        <img src="{{ asset('images/logo.png') }}" alt="Logo RSUI Khanza" style="height: 34px; width: 34px; object-fit: contain; filter: brightness(0) invert(1);">
        <span class="d-none d-md-inline" style="font-size: 15px; letter-spacing: 0.5px;">SIMRS RSP UI</span>
      </a>

      <!-- Mobile toggle -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
        <span class="navbar-toggler-icon"></span>
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

          @if(\App\Helpers\PermissionHelper::has('pendaftaran'))
          <!-- Pendaftaran -->
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle @if(request()->is('pendaftaran*')) active @endif"
               data-bs-toggle="dropdown"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('pendaftaran*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Pendaftaran
            </a>
            <ul class="dropdown-menu shadow-sm border-0" style="min-width: 180px; margin-top: 8px;">
              <li><a class="dropdown-item" href="{{ url('/pendaftaran') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Antrian
              </a></li>
              <li><a class="dropdown-item" href="{{ url('/pendaftaran/pasien-baru') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                Pasien Baru
              </a></li>
              <li><a class="dropdown-item" href="{{ url('/pendaftaran/pasien-lama') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
                Pasien Lama
              </a></li>
            </ul>
          </li>
          @endif

          @if(\App\Helpers\PermissionHelper::has('rawat_jalan'))
          <!-- Rawat Jalan -->
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle @if(request()->is('rawat-jalan*')) active @endif"
               data-bs-toggle="dropdown"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('rawat-jalan*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Rawat Jalan
            </a>
            <ul class="dropdown-menu shadow-sm border-0" style="min-width: 180px; margin-top: 8px;">
              <li><a class="dropdown-item" href="{{ url('/rawat-jalan') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
                Registered Pasien
              </a></li>
              <li><a class="dropdown-item" href="{{ url('/rawat-jalan/pemeriksaan') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                Pemeriksaan
              </a></li>
            </ul>
          </li>
          @endif

          @if(\App\Helpers\PermissionHelper::has('rawat_inap'))
          <!-- Rawat Inap -->
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle @if(request()->is('rawat-inap*')) active @endif"
               data-bs-toggle="dropdown"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('rawat-inap*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Rawat Inap
            </a>
            <ul class="dropdown-menu shadow-sm border-0" style="min-width: 180px; margin-top: 8px;">
              <li><a class="dropdown-item" href="{{ url('/rawat-inap') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><path d="M2 4v16M2 11h20M22 8v12M6 8a2 2 0 1 0-4 0"/></svg>
                Pasien Dirawat
              </a></li>
              <li><a class="dropdown-item" href="{{ url('/rawat-inap/kamar') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="16"/><path d="M9 16h6v6"/><line x1="12" y1="5" x2="12" y2="11"/><line x1="9" y1="8" x2="15" y2="8"/></svg>
                Kamar &amp; Bangsal
              </a></li>
            </ul>
          </li>
          @endif

          @if(\App\Helpers\PermissionHelper::has('laboratorium'))
          <!-- Laboratorium -->
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle @if(request()->is('laboratorium*')) active @endif"
               data-bs-toggle="dropdown"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('laboratorium*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Laboratorium
            </a>
            <ul class="dropdown-menu shadow-sm border-0" style="min-width: 180px; margin-top: 8px;">
              <li><a class="dropdown-item" href="{{ url('/laboratorium') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Antrian
              </a></li>
              <li><a class="dropdown-item" href="{{ url('/laboratorium/hasil') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
                Input Hasil
              </a></li>
            </ul>
          </li>
          @endif

          @if(\App\Helpers\PermissionHelper::has('radiologi'))
          <!-- Radiologi -->
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle @if(request()->is('radiologi*')) active @endif"
               data-bs-toggle="dropdown"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('radiologi*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Radiologi
            </a>
            <ul class="dropdown-menu shadow-sm border-0" style="min-width: 180px; margin-top: 8px;">
              <li><a class="dropdown-item" href="{{ url('/radiologi') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Antrian
              </a></li>
            </ul>
          </li>
          @endif

          @if(\App\Helpers\PermissionHelper::has('farmasi'))
          <!-- Farmasi -->
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle @if(request()->is('farmasi*')) active @endif"
               data-bs-toggle="dropdown"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('farmasi*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Farmasi
            </a>
            <ul class="dropdown-menu shadow-sm border-0" style="min-width: 180px; margin-top: 8px;">
              <li><a class="dropdown-item" href="{{ url('/farmasi') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
                Antrian Resep
              </a></li>
              <li><a class="dropdown-item" href="{{ url('/farmasi/stok') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v6c0 1.66 4 3 9 3s9-1.34 9-3V5"/><path d="M3 11v6c0 1.66 4 3 9 3s9-1.34 9-3v-6"/></svg>
                Stok Obat
              </a></li>
            </ul>
          </li>
          @endif

          @if(\App\Helpers\PermissionHelper::has('billing'))
          <!-- Billing -->
          <li class="nav-item">
            <a href="{{ url('/billing') }}" class="nav-link @if(request()->is('billing*')) active @endif"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('billing*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Billing
            </a>
          </li>
          @endif

          @if(\App\Helpers\PermissionHelper::has('master_data'))
          <!-- Master Data -->
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle @if(request()->is('master*')) active @endif"
               data-bs-toggle="dropdown"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('master*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Master Data
            </a>
            <ul class="dropdown-menu shadow-sm border-0" style="min-width: 180px; margin-top: 8px;">
              <li><a class="dropdown-item" href="{{ url('/master/pasien') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Data Pasien
              </a></li>
              <li><a class="dropdown-item" href="{{ url('/master/dokter') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                Data Dokter
              </a></li>
              <li><a class="dropdown-item" href="{{ url('/master/poli') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="16"/><path d="M9 16h6v6"/><line x1="12" y1="5" x2="12" y2="11"/><line x1="9" y1="8" x2="15" y2="8"/></svg>
                Poliklinik
              </a></li>
              <li><a class="dropdown-item" href="{{ url('/master/obat') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/><path d="m8.5 8.5 7 7"/></svg>
                Data Obat
              </a></li>
              <li><a class="dropdown-item" href="{{ url('/master/kamar') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><path d="M2 4v16M2 11h20M22 8v12M6 8a2 2 0 1 0-4 0"/></svg>
                Kamar / Kelas
              </a></li>
              <li><a class="dropdown-item" href="{{ url('/master/tarif') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><line x1="12" y1="4" x2="12" y2="20"/><line x1="2" y1="12" x2="22" y2="12"/></svg>
                Tarif Layanan
              </a></li>
              <li><a class="dropdown-item" href="{{ url('/master/aset') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                Aset & Alat
              </a></li>
            </ul>
          </li>
          @endif

          @if(\App\Helpers\PermissionHelper::has('laporan'))
          <!-- Laporan -->
          <li class="nav-item">
            <a href="{{ url('/laporan') }}" class="nav-link @if(request()->is('laporan*')) active fw-bold @endif"
               style="color: rgba(255,255,255,0.85); font-size: 13px; padding: 6px 10px; border-radius: 6px; @if(request()->is('laporan*')) background: rgba(255,255,255,0.2); color:#fff; @endif">
              Laporan
            </a>
          </li>
          @endif

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
              <span class="avatar avatar-sm" style="background: rgba(255,255,255,0.25); color: #fff; font-weight: 700;">
                {{ substr(session('user')->nama ?? 'A', 0, 1) }}
              </span>
              <span class="d-none d-md-inline">{{ session('user')->nama ?? 'Administrator' }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="margin-top: 8px;">
              <li>
                <div class="dropdown-header py-2" style="border-bottom: 1px solid #f1f5f9; min-width: 180px;">
                  <div class="fw-bold text-dark text-truncate" style="max-width: 160px;">{{ session('user')->nama ?? 'Guest' }}</div>
                  <div class="text-muted small mt-1">Role: <span class="badge bg-blue-lt fw-semibold ms-1" style="font-size: 9px; padding: 1px 4px;">{{ session('user')->role_name ?? 'Staf' }}</span></div>
                </div>
              </li>
              @if(\App\Helpers\PermissionHelper::has('pengaturan'))
              <li><a class="dropdown-item" href="{{ url('/pengaturan') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted me-2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Pengaturan Sistem
              </a></li>
              @endif
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
    <div class="page-header d-print-none" style="padding: 4px 0 2px;">
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
      <div class="container-fluid" style="padding-top: 8px; padding-bottom: 24px;">
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
