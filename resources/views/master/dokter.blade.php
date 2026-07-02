@extends('layout.app')

@section('title', 'Data Dokter - Master Data')
@section('page-title', 'Data Dokter')
@section('breadcrumb', 'Master Data')

@section('content')

<div class="page-header">
  <div class="page-header-left">
    <h1>Data Dokter</h1>
    <p>Kelola data dokter dan spesialisasi klinik</p>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4">
    <div class="card border-0 shadow-sm p-3 d-flex align-items-center justify-content-between flex-row" style="background: #fff; border-radius: 8px;">
      <div>
        <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Total Dokter</div>
        <div class="h2 mb-0 fw-bold text-dark" style="font-size: 24px; margin-top: 4px;">{{ number_format($stats->total) }}</div>
      </div>
      <div class="d-flex align-items-center justify-content-center bg-blue-lt rounded" style="width: 44px; height: 44px; background: rgba(32, 107, 196, 0.1); color: #206bc4;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm p-3 d-flex align-items-center justify-content-between flex-row" style="background: #fff; border-radius: 8px;">
      <div>
        <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Dokter Aktif</div>
        <div class="h2 mb-0 fw-bold text-success" style="font-size: 24px; margin-top: 4px;">{{ number_format($stats->aktif) }}</div>
      </div>
      <div class="d-flex align-items-center justify-content-center bg-success-lt rounded" style="width: 44px; height: 44px; background: rgba(47, 179, 68, 0.1); color: #2fb344;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm p-3 d-flex align-items-center justify-content-between flex-row" style="background: #fff; border-radius: 8px;">
      <div>
        <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Dokter Tidak Aktif</div>
        <div class="h2 mb-0 fw-bold text-secondary" style="font-size: 24px; margin-top: 4px;">{{ number_format($stats->tidak_aktif) }}</div>
      </div>
      <div class="d-flex align-items-center justify-content-center bg-secondary-lt rounded" style="width: 44px; height: 44px; background: rgba(100, 116, 139, 0.1); color: #64748b;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      </div>
    </div>
  </div>
</div>

    <div class="card">
        <!-- Search & Filter Header -->
        <div class="card-header bg-light">
            <div class="row align-items-center g-3">
                <div class="col-md-9">
                    <form method="GET" action="{{ url('/master/dokter') }}" class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="input-group" style="max-width: 320px;">
                            <span class="input-group-text bg-white border-end-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control border-start-0" 
                                   placeholder="Cari kode, nama, spesialisasi..." 
                                   value="{{ $search ?? '' }}">
                            @if($search ?? '')
                            <a href="{{ url('/master/dokter?status=' . ($statusFilter ?? 'all')) }}" class="btn btn-outline-secondary" title="Hapus pencarian">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </a>
                            @endif
                        </div>

                        <!-- Dropdown Status Filter -->
                        <div style="min-width: 140px;">
                            <select name="status" class="form-select border-1" onchange="this.form.submit()">
                                <option value="all" {{ ($statusFilter ?? 'all') == 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="active" {{ ($statusFilter ?? 'all') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ ($statusFilter ?? 'all') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <span class="d-none d-sm-inline ms-1">Cari</span>
                        </button>
                    </form>
                </div>
                <div class="col-md-3">
                    <form method="GET" action="{{ url('/master/dokter') }}" class="d-flex align-items-center justify-content-end gap-2">
                        @if($search ?? '')
                        <input type="hidden" name="search" value="{{ $search }}">
                        @endif
                        <input type="hidden" name="status" value="{{ $statusFilter ?? 'all' }}">
                        <label class="form-label mb-0 text-muted" style="font-size: 13px; white-space: nowrap;">Tampilkan:</label>
                        <select name="per_page" class="form-select form-select-sm" style="width: auto; min-width: 80px;" onchange="this.form.submit()">
                            <option value="10" {{ ($perPage ?? 20) == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ ($perPage ?? 20) == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ ($perPage ?? 20) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ($perPage ?? 20) == 100 ? 'selected' : '' }}>100</option>
                            <option value="200" {{ ($perPage ?? 20) == 200 ? 'selected' : '' }}>200</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search Results Info -->
        @if($search ?? '')
        <div class="alert alert-info border-0 rounded-0 mb-0" style="background-color: #e3f2fd; border-bottom: 1px solid #e9ecef !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                    <span>Hasil pencarian untuk: <strong>"{{ $search }}"</strong></span>
                    <span class="badge bg-primary">{{ $data->total() }} data</span>
                </div>
                <a href="{{ url('/master/dokter') }}" class="btn btn-sm btn-outline-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                    </svg>
                    Tampilkan Semua
                </a>
            </div>
        </div>
        @endif
        
        <div class="table-wrapper">
            <table class="table table-nowrap">
                <thead>
                    <tr>
                        <th>Kode Dokter</th>
                        <th>Nama Dokter</th>
                        <th>Spesialisasi</th>
                        <th>No. Ijin Praktek</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th width="80" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($data->count() > 0)
                        @foreach($data as $d)
                        <tr>
                            <td><strong class="text-primary">{{ $d->kd_dokter }}</strong></td>
                            <td style="font-weight:600;">{{ $d->nm_dokter }}</td>
                            <td><span class="badge badge-blue">{{ $d->nm_sps ?? '-' }}</span></td>
                            <td class="text-muted">{{ $d->no_ijn_praktek ?? '-' }}</td>
                            <td class="text-sm">{{ Str::limit($d->almt_tgl ?? '-', 40) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($d->status == '1')
                                        <span class="badge badge-success" style="min-width: 80px; display: inline-block; text-align: center;">Aktif</span>
                                        <form action="{{ url('/master/dokter/toggle-status/' . $d->kd_dokter) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan dokter ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size: 11px; padding: 2px 6px;">Nonaktifkan</button>
                                        </form>
                                    @else
                                        <span class="badge badge-gray" style="min-width: 80px; display: inline-block; text-align: center;">Tidak Aktif</span>
                                        <form action="{{ url('/master/dokter/toggle-status/' . $d->kd_dokter) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin mengaktifkan dokter ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" style="font-size: 11px; padding: 2px 6px;">Aktifkan</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-ghost-secondary btn-icon btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#edit-dokter-{{ $d->kd_dokter }}" 
                                        title="Edit Dokter">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>

                                <!-- Modal Edit Dokter -->
                                <div class="modal modal-blur fade" id="edit-dokter-{{ $d->kd_dokter }}" tabindex="-1" role="dialog" aria-hidden="true" style="text-align: left;">
                                  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                                    <div class="modal-content">
                                      <form action="{{ url('/master/dokter/' . $d->kd_dokter) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                          <h5 class="modal-title fw-bold">Edit Data Dokter</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                          <div class="mb-3">
                                            <label class="form-label required">Nama Dokter</label>
                                            <input type="text" class="form-control" name="nm_dokter" value="{{ $d->nm_dokter }}" required>
                                          </div>
                                          <div class="row g-2 mb-3">
                                            <div class="col-6">
                                              <label class="form-label required">Jenis Kelamin</label>
                                              <select class="form-select" name="jk" required>
                                                <option value="L" {{ $d->jk == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                                <option value="P" {{ $d->jk == 'P' ? 'selected' : '' }}>Perempuan</option>
                                              </select>
                                            </div>
                                            <div class="col-6">
                                              <label class="form-label">No. HP / Telp</label>
                                              <input type="text" class="form-control" name="no_telp" value="{{ $d->no_telp }}">
                                            </div>
                                          </div>
                                          <div class="row g-2 mb-3">
                                            <div class="col-6">
                                              <label class="form-label">Tempat Lahir</label>
                                              <input type="text" class="form-control" name="tmp_lahir" value="{{ $d->tmp_lahir }}">
                                            </div>
                                            <div class="col-6">
                                              <label class="form-label">Tanggal Lahir</label>
                                              <input type="date" class="form-control" name="tgl_lahir" value="{{ $d->tgl_lahir }}">
                                            </div>
                                          </div>
                                          <div class="mb-3">
                                            <label class="form-label">Status Keaktifan</label>
                                            <select class="form-select" name="status" required>
                                              <option value="1" {{ $d->status == '1' ? 'selected' : '' }}>Aktif</option>
                                              <option value="0" {{ $d->status != '1' ? 'selected' : '' }}>Tidak Aktif</option>
                                            </select>
                                          </div>
                                          <div class="row g-2 mb-0">
                                            <div class="col-6">
                                              <label class="form-label">Tanggal Mulai Aktif</label>
                                              <input type="date" class="form-control" name="status_aktif_tgl" value="{{ $d->status_aktif_tgl }}">
                                            </div>
                                            <div class="col-6">
                                              <label class="form-label">Tanggal Non-Aktif (Selesai)</label>
                                              <input type="date" class="form-control" name="status_nonaktif_tgl" value="{{ $d->status_nonaktif_tgl }}">
                                            </div>
                                          </div>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                                          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                @if($search ?? '')
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="mb-2">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                        <p class="mb-1">Tidak ditemukan data untuk "{{ $search }}"</p>
                                        <small>Coba dengan kata kunci lain atau <a href="{{ url('/master/dokter') }}">tampilkan semua data</a></small>
                                    </div>
                                @else
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="mb-2">
                                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                                        </svg>
                                        <p class="mb-1">Tidak ada data dokter</p>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Footer dengan pagination dan info -->
        <div class="card-footer bg-light">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="text-muted" style="font-size: 13px;">
                        Menampilkan <strong>{{ $data->firstItem() ?? 0 }}</strong> - <strong>{{ $data->lastItem() ?? 0 }}</strong> 
                        dari <strong>{{ number_format($data->total()) }}</strong> data
                        @if($search ?? '')
                        <span class="ms-2 text-primary">• Pencarian: "{{ $search }}"</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    {{ $data->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection