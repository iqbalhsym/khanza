@extends('layout.app')

@section('title', 'Data Obat - Master Data')
@section('page-title', 'Data Obat')
@section('breadcrumb', 'Master Data')

@section('content')

<div class="page-header">
  <div class="page-header-left">
    <h1>Data Obat</h1>
    <p>Kelola data seluruh obat dan alat kesehatan yang aktif</p>
  </div>
  <div class="page-header-right">
    <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 13px;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/>
        <path d="m8.5 8.5 7 7"/>
      </svg>
      Total: {{ $data->total() }} obat
    </div>
  </div>
</div>

    <div class="card">
        <!-- Search & Filter Header -->
        <div class="card-header bg-light">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <form method="GET" action="{{ url('/master/obat') }}" class="d-flex align-items-center gap-2">
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
                                   placeholder="Cari kode/nama obat atau satuan..." 
                                   value="{{ $search ?? '' }}">
                            @if($search ?? '')
                            <a href="{{ url('/master/obat') }}" class="btn btn-outline-secondary" title="Hapus pencarian">
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
                    <form method="GET" action="{{ url('/master/obat') }}" class="d-flex align-items-center justify-content-end gap-2">
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
                <a href="{{ url('/master/obat') }}" class="btn btn-sm btn-outline-primary">
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
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th style="text-align:right">Harga Ralan</th>
                        <th style="text-align:right">Kelas 1</th>
                        <th style="text-align:right">Utama/VIP</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if($data->count() > 0)
                        @foreach($data as $d)
                        <tr>
                            <td><strong class="text-primary">{{ $d->kode_brng }}</strong></td>
                            <td style="font-weight:600;">{{ $d->nama_brng }}</td>
                            <td><span class="badge badge-gray">{{ $d->satuan }}</span></td>
                            <td style="text-align:right; font-weight:600; color:#2e7d32;">Rp {{ number_format($d->ralan, 0, ',', '.') }}</td>
                            <td style="text-align:right; color:#455a64;">Rp {{ number_format($d->kelas1, 0, ',', '.') }}</td>
                            <td style="text-align:right; color:#37474f;">Rp {{ number_format($d->vip, 0, ',', '.') }}</td>
                            <td>
                                @if($d->status == '1')
                                    <span class="badge badge-green">Aktif</span>
                                @else
                                    <span class="badge badge-red">Non-Aktif</span>
                                @endif
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
                                        <small>Coba dengan kata kunci lain atau <a href="{{ url('/master/obat') }}">tampilkan semua data</a></small>
                                    </div>
                                @else
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="mb-2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                            <polyline points="10 9 9 9 8 9"></polyline>
                                        </svg>
                                        <p class="mb-1">Tidak ada data obat</p>
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
    </div>
@endsection
