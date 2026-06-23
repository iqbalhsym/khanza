@extends('layout.app')

@section('title', 'Data Poliklinik - Master Data')
@section('page-title', 'Data Poliklinik')
@section('breadcrumb', 'Master Data')

@section('content')

<div class="page-header">
  <div class="page-header-left">
    <h1>Data Poliklinik</h1>
    <p>Kelola data poliklinik dan unit pelayanan</p>
  </div>
  <div class="page-header-right">
    <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 13px;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
        <polyline points="9,22 9,12 15,12 15,22"></polyline>
      </svg>
      Total: {{ $data->total() }} poliklinik
    </div>
  </div>
</div>

    <div class="card">
        <!-- Search & Filter Header -->
        <div class="card-header bg-light">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <form method="GET" action="{{ url('/master/poli') }}" class="d-flex align-items-center gap-2">
                        <div class="input-group" style="max-width: 400px;">
                            <span class="input-group-text bg-white border-end-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control border-start-0" 
                                   placeholder="Cari kode atau nama poliklinik..." 
                                   value="{{ $search ?? '' }}">
                            @if($search ?? '')
                            <a href="{{ url('/master/poli') }}" class="btn btn-outline-secondary" title="Hapus pencarian">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </a>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <span class="d-none d-sm-inline ms-1">Cari</span>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="GET" action="{{ url('/master/poli') }}" class="d-flex align-items-center justify-content-end gap-2">
                        @if($search ?? '')
                        <input type="hidden" name="search" value="{{ $search }}">
                        @endif
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
                <a href="{{ url('/master/poli') }}" class="btn btn-sm btn-outline-primary">
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
                        <th>Kode Poli</th>
                        <th>Nama Poliklinik</th>
                        <th>Registrasi</th>
                        <th>Registrasi Lama</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if($data->count() > 0)
                        @foreach($data as $d)
                        <tr>
                            <td><strong class="text-primary">{{ $d->kd_poli }}</strong></td>
                            <td style="font-weight:600;">{{ $d->nm_poli }}</td>
                            <td class="text-end">Rp {{ number_format($d->registrasi ?? 0, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($d->registrasilama ?? 0, 0, ',', '.') }}</td>
                            <td>
                                @if($d->status == '1')
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-gray">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                @if($search ?? '')
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="mb-2">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                        <p class="mb-1">Tidak ditemukan data untuk "{{ $search }}"</p>
                                        <small>Coba dengan kata kunci lain atau <a href="{{ url('/master/poli') }}">tampilkan semua data</a></small>
                                    </div>
                                @else
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="mb-2">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                            <polyline points="9,22 9,12 15,12 15,22"></polyline>
                                        </svg>
                                        <p class="mb-1">Tidak ada data poliklinik</p>
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