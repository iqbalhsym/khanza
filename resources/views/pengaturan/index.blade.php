@extends('layout.app')

@section('title', 'Manajemen Akun & Role – SIMRS RSP UI')
@section('page-title', 'Pengaturan Sistem')
@section('breadcrumb', 'Pengaturan')

@section('content')

<!-- Alerts -->
@if(session('success'))
<div class="alert alert-important alert-success alert-dismissible shadow-sm border-0 mb-4" role="alert">
  <div class="d-flex">
    <div>
      <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
    </div>
    <div>{{ session('success') }}</div>
  </div>
  <button class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-important alert-danger alert-dismissible shadow-sm border-0 mb-4" role="alert">
  <div class="d-flex">
    <div>
      <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg>
    </div>
    <div>{{ session('error') }}</div>
  </div>
  <button class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></button>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger alert-dismissible shadow-sm border-0 mb-4" role="alert">
  <div class="d-flex">
    <div>
      <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg>
    </div>
    <div>
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  </div>
  <button class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
</div>
@endif

<div class="card shadow-sm border-0" style="border-radius: 12px;">
  <!-- Tab Header -->
  <div class="card-header border-bottom bg-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist" style="border-bottom: 0;">
      <li class="nav-item" role="presentation">
        <a href="#tabs-users" class="nav-link active d-flex align-items-center gap-2 fw-semibold" data-bs-toggle="tab" aria-selected="true" role="tab" style="padding: 12px 16px;">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="9" cy="7" r="4" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 11l2 2l4 -4" /></svg>
          Manajemen Akun
        </a>
      </li>
      <li class="nav-item" role="presentation">
        <a href="#tabs-roles" class="nav-link d-flex align-items-center gap-2 fw-semibold" data-bs-toggle="tab" aria-selected="false" tabindex="-1" role="tab" style="padding: 12px 16px;">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" /><circle cx="12" cy="11" r="3" /><path d="M12 14v7" /></svg>
          Role & Hak Akses
        </a>
      </li>
    </ul>
  </div>

  <div class="tab-content">
    <!-- ==============================================
         TAB 1: USER MANAGEMENT
         ============================================== -->
    <div class="tab-pane active show" id="tabs-users" role="tabpanel">
      <div class="card-body p-0">
        <!-- Sub-header & Action -->
        <div class="p-3 bg-light d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <span class="text-muted" style="font-size: 13px;">Kelola daftar pengguna sistem baik via LDAP Active Directory maupun akun lokal.</span>
          </div>
          <button class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-add-user">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
            Tambah Pengguna
          </button>
        </div>

        <!-- Table Users -->
        <div class="table-responsive">
          <table class="table table-vcenter card-table table-hover mb-0">
            <thead>
              <tr class="bg-light text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                <th>Nama Lengkap</th>
                <th>Username / LDAP</th>
                <th>Email</th>
                <th>Role</th>
                <th>Dokter Terkait</th>
                <th class="text-center">Status</th>
                <th class="w-1">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $user)
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-sm rounded-circle bg-blue-lt fw-bold" style="background: rgba(32, 107, 196, 0.1); color: #206bc4;">
                      {{ substr($user->name, 0, 1) }}
                    </span>
                    <div>
                      <div class="font-weight-medium text-dark">{{ $user->name }}</div>
                      @if(empty($user->password))
                        <span class="badge bg-purple-lt" style="font-size: 9px; padding: 2px 4px;">SSO/LDAP Only</span>
                      @else
                        <span class="badge bg-blue-lt" style="font-size: 9px; padding: 2px 4px;">Lokal + LDAP</span>
                      @endif
                    </div>
                  </div>
                </td>
                <td class="text-muted font-monospace">{{ $user->username }}</td>
                <td class="text-muted">{{ $user->email ?: '-' }}</td>
                <td>
                  <span class="badge bg-cyan-lt fw-semibold" style="font-size: 11px;">
                    {{ $user->role ? $user->role->name : 'Tanpa Role' }}
                  </span>
                </td>
                <td class="text-muted">
                  @if($user->kd_dokter)
                    <span class="text-dark fw-medium" title="Kode Dokter: {{ $user->kd_dokter }}">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted d-inline me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8l0 4" /><path d="M12 16l.01 0" /></svg>
                      {{ $user->kd_dokter }}
                    </span>
                  @else
                    <span class="text-muted" style="font-size: 12px; font-style: italic;">Tidak terkait</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($user->is_active)
                    <span class="badge bg-success" style="padding: 4px 8px; border-radius: 12px;">Aktif</span>
                  @else
                    <span class="badge bg-secondary" style="padding: 4px 8px; border-radius: 12px;">Nonaktif</span>
                  @endif
                </td>
                <td>
                  <div class="btn-list flex-nowrap">
                    <button class="btn btn-white btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modal-edit-user-{{ $user->id }}">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" /><path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" /><line x1="16" y1="5" x2="19" y2="8" /></svg>
                      Edit
                    </button>
                    @if(session('user') && session('user')->id != $user->id)
                    <form action="{{ url('/pengaturan/users/' . $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                        Hapus
                      </button>
                    </form>
                    @endif
                  </div>
                </td>
              </tr>

              <!-- MODAL EDIT USER -->
              <div class="modal modal-blur fade" id="modal-edit-user-{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content border-0 shadow-lg">
                    <form action="{{ url('/pengaturan/users/' . $user->id) }}" method="POST">
                      @csrf
                      @method('PUT')
                      <div class="modal-header bg-light border-0">
                        <h5 class="modal-title fw-bold text-dark">Edit Pengguna: {{ $user->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <label class="form-label required">Nama Lengkap</label>
                          <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label required">Username / AD Account</label>
                          <input type="text" class="form-control" name="username" value="{{ old('username', $user->username) }}" required>
                          <small class="form-hint text-muted">Sesuaikan dengan sAMAccountName di Active Directory jika menggunakan SSO.</small>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Alamat Email</label>
                          <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}">
                        </div>
                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <label class="form-label required">Role Pengguna</label>
                            <select class="form-select" name="role_id" required>
                              @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                              @endforeach
                            </select>
                          </div>
                          <div class="col-md-6 mb-3">
                            <label class="form-label">Dokter Terkait</label>
                            <select class="form-select select-dokter font-monospace" name="kd_dokter">
                              <option value="">-- Tidak Terkait --</option>
                              @foreach($dokters as $doc)
                                <option value="{{ $doc->kd_dokter }}" {{ $user->kd_dokter == $doc->kd_dokter ? 'selected' : '' }}>
                                  {{ $doc->nm_dokter }} ({{ $doc->kd_dokter }})
                                </option>
                              @endforeach
                            </select>
                            <small class="form-hint">Harus diisi jika rolenya adalah Dokter.</small>
                          </div>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Password Lokal Baru (Opsional)</label>
                          <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak ingin diubah">
                          <small class="form-hint text-muted">Hanya untuk login darurat / lokal jika koneksi LDAP mati.</small>
                        </div>
                        <div class="mb-0">
                          <label class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" {{ $user->is_active ? 'checked' : '' }}>
                            <span class="form-check-label text-dark fw-medium">Akun Aktif</span>
                          </label>
                        </div>
                      </div>
                      <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary shadow-sm">Simpan Perubahan</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              @empty
              <tr>
                <td colspan="7" class="text-center p-4">
                  <div class="text-muted">Tidak ada data pengguna terdaftar.</div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ==============================================
         TAB 2: ROLE & PERMISSION MANAGEMENT
         ============================================== -->
    <div class="tab-pane" id="tabs-roles" role="tabpanel">
      <div class="card-body p-0">
        <div class="p-3 bg-light">
          <span class="text-muted" style="font-size: 13px;">Tentukan menu navigasi dan hak akses fitur per-modul yang dapat dibuka oleh masing-masing Role.</span>
        </div>

        <!-- Table Roles -->
        <div class="table-responsive">
          <table class="table table-vcenter card-table table-hover mb-0">
            <thead>
              <tr class="bg-light text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                <th>Nama Role</th>
                <th>Daftar Modul yang Diizinkan</th>
                <th class="w-1 text-nowrap">Hak Akses</th>
              </tr>
            </thead>
            <tbody>
              @foreach($roles as $role)
              <tr>
                <td>
                  <div class="font-weight-medium text-dark d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" /></svg>
                    {{ $role->name }}
                  </div>
                </td>
                <td>
                  <div class="d-flex flex-wrap gap-1">
                    @if($role->name === 'Super Admin')
                      <span class="badge bg-white" style="padding: 4px 8px; border-radius: 6px;">SEMUA AKSES</span>
                    @else
                      @forelse($role->permissions ?? [] as $perm)
                        <span class="badge bg-blue-lt" style="padding: 2px 6px; font-size: 10px;">
                          {{ $allPermissions[$perm] ?? $perm }}
                        </span>
                      @empty
                        <span class="text-muted" style="font-size: 12px; font-style: italic;">Tidak ada modul</span>
                      @endforelse
                    @endif
                  </div>
                </td>
                <td>
                  @if($role->name === 'Super Admin')
                    <button class="btn btn-light btn-sm disabled" disabled title="Akses Super Admin bersifat permanen">
                      Terkunci
                    </button>
                  @else
                    <button class="btn btn-white btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modal-edit-role-{{ $role->id }}">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                      Kelola Hak Akses
                    </button>
                  @endif
                </td>
              </tr>

              <!-- MODAL EDIT ROLE PERMISSIONS -->
              @if($role->name !== 'Super Admin')
              <div class="modal modal-blur fade" id="modal-edit-role-{{ $role->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                  <div class="modal-content border-0 shadow-lg">
                    <form action="{{ url('/pengaturan/roles/' . $role->id) }}" method="POST">
                      @csrf
                      @method('PUT')
                      <div class="modal-header bg-light border-0">
                        <h5 class="modal-title fw-bold text-dark">Kelola Hak Akses Role: {{ $role->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="text-muted mb-3">Centang modul yang diizinkan untuk diakses oleh role <strong>{{ $role->name }}</strong>:</div>
                        <div class="row">
                          @foreach($allPermissions as $key => $label)
                          <div class="col-md-6 mb-2">
                            <label class="form-check form-check-inline m-0">
                              <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $key }}"
                                {{ in_array($key, $role->permissions ?? []) ? 'checked' : '' }}>
                              <span class="form-check-label text-dark">{{ $label }}</span>
                            </label>
                          </div>
                          @endforeach
                        </div>
                      </div>
                      <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary shadow-sm">Simpan Hak Akses</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              @endif
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ==============================================
     MODAL ADD USER (GLOBAL)
     ============================================== -->
<div class="modal modal-blur fade" id="modal-add-user" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg">
      <form action="{{ url('/pengaturan/users') }}" method="POST">
        @csrf
        <div class="modal-header bg-light border-0">
          <h5 class="modal-title fw-bold text-dark">Tambah Pengguna Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">Nama Lengkap</label>
            <input type="text" class="form-control" name="name" placeholder="Masukkan nama lengkap user" value="{{ old('name') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label required">Username / AD Account</label>
            <input type="text" class="form-control" name="username" placeholder="Masukkan username AD atau lokal" value="{{ old('username') }}" required>
            <small class="form-hint text-muted">Sesuaikan dengan sAMAccountName di Active Directory jika menggunakan SSO.</small>
          </div>
          <div class="mb-3">
            <label class="form-label">Alamat Email</label>
            <input type="email" class="form-control" name="email" placeholder="contoh@rs.ui.ac.id" value="{{ old('email') }}">
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label required">Role Pengguna</label>
              <select class="form-select" name="role_id" required>
                <option value="" disabled selected>-- Pilih Role --</option>
                @foreach($roles as $role)
                  <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Dokter Terkait</label>
              <select class="form-select select-dokter font-monospace" name="kd_dokter">
                <option value="" selected>-- Tidak Terkait --</option>
                @foreach($dokters as $doc)
                  <option value="{{ $doc->kd_dokter }}" {{ old('kd_dokter') == $doc->kd_dokter ? 'selected' : '' }}>
                    {{ $doc->nm_dokter }} ({{ $doc->kd_dokter }})
                  </option>
                @endforeach
              </select>
              <small class="form-hint">Harus diisi jika rolenya adalah Dokter.</small>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Password Lokal (Opsional)</label>
            <input type="password" class="form-control" name="password" placeholder="Masukkan password jika akun lokal">
            <small class="form-hint text-muted">Hanya untuk login darurat / lokal jika koneksi LDAP mati.</small>
          </div>
          <div class="mb-0">
            <label class="form-check form-switch mt-2">
              <input class="form-check-input" type="checkbox" name="is_active" checked>
              <span class="form-check-label text-dark fw-medium">Akun Aktif</span>
            </label>
          </div>
        </div>
        <div class="modal-footer bg-light border-0">
          <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary shadow-sm">Simpan Pengguna</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('styles')
<!-- Tom Select CSS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
  .ts-wrapper.form-select {
    padding: 0 !important;
    border: none !important;
    background: none !important;
  }
  .ts-control {
    border-radius: 4px !important;
    padding: 8px 12px !important;
    border: 1px solid #ced4da !important;
    font-family: var(--tblr-font-monospace, monospace);
  }
  .ts-dropdown {
    font-family: var(--tblr-font-monospace, monospace);
  }
</style>
@endpush

@push('scripts')
<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Fungsi inisialisasi Tom Select pada select-dokter di modal tertentu
    function initTomSelectInModal(modal) {
        modal.querySelectorAll('.select-dokter').forEach(function (select) {
            if (!select.tomselect) {
                new TomSelect(select, {
                    create: false,
                    placeholder: "Ketik nama / ID dokter...",
                    maxOptions: 1000, // Batasan maksimum render opsi agar responsif
                    searchField: ['text', 'value'],
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
            }
        });
    }

    // Inisialisasi saat modal ditampilkan (shown.bs.modal)
    // Menghindari bug dimensi lebar 0-width saat modal masih tersembunyi
    document.querySelectorAll('.modal').forEach(function (modal) {
        modal.addEventListener('shown.bs.modal', function () {
            initTomSelectInModal(modal);
        });
    });
});
</script>
@endpush

@endsection
