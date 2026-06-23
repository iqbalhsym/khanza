@extends('layout.app')

@section('title', 'Tarif Layanan - Master Data')
@section('page-title', 'Tarif Layanan')
@section('breadcrumb', 'Master Data')

@section('content')

<div class="page-header">
  <div class="page-header-left">
    <h1>Tarif Layanan & Tindakan</h1>
    <p>Kelola tarif dan biaya tindakan medis di rumah sakit</p>
  </div>
  <div class="page-header-right">
    <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 13px;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="2" y="4" width="20" height="16" rx="2" ry="2"/>
        <line x1="12" y1="4" x2="12" y2="20"/>
        <line x1="2" y1="12" x2="22" y2="12"/>
      </svg>
      Total: {{ $data->total() }} tarif
    </div>
  </div>
</div>

    <div class="card">
        <!-- Search & Filter Header -->
        <div class="card-header bg-light">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <form method="GET" action="{{ url('/master/tarif') }}" class="d-flex align-items-center gap-2">
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
                                   placeholder="Cari Kode, Nama Tindakan, Poli, Penjamin..." 
                                   value="{{ $search ?? '' }}">
                            @if($search ?? '')
                            <a href="{{ url('/master/tarif') }}" class="btn btn-outline-secondary" title="Hapus pencarian">
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
                    <form method="GET" action="{{ url('/master/tarif') }}" class="d-flex align-items-center justify-content-end gap-2">
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
                <a href="{{ url('/master/tarif') }}" class="btn btn-sm btn-outline-primary">
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
                        <th>Kode</th>
                        <th>Nama Perawatan / Tindakan</th>
                        <th>Poliklinik</th>
                        <th>Penanggung Jawab</th>
                        <th class="text-end">Tarif RS</th>
                        <th class="text-end">Jasa Dr.</th>
                        <th class="text-end">Jasa Pr.</th>
                        <th class="text-end">BHP / KSO</th>
                        <th class="text-end">Total Tarif</th>
                    </tr>
                </thead>
                <tbody>
                    @if($data->count() > 0)
                        @foreach($data as $d)
                        <tr>
                            <td><strong class="text-primary">{{ $d->kd_jenis_prw }}</strong></td>
                            <td style="font-weight:600;">{{ $d->nm_perawatan }}</td>
                            <td>
                                <span class="badge badge-blue">{{ $d->nm_poli ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="badge badge-gray">{{ $d->png_jawab ?? '-' }}</span>
                            </td>
                            <td class="text-end text-muted">Rp {{ number_format($d->material, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($d->tarif_tindakandr, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($d->tarif_tindakanpr, 0, ',', '.') }}</td>
                            <td class="text-end text-muted">Rp {{ number_format($d->bhp + $d->kso, 0, ',', '.') }}</td>
                            <td class="text-endfw-bold text-primary" style="font-weight: 700;">
                                Rp {{ number_format($d->material + $d->tarif_tindakandr + $d->tarif_tindakanpr + $d->bhp + $d->kso + $d->menejemen, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                @if($search ?? '')
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="mb-2">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                        <p class="mb-1">Tidak ditemukan data tarif untuk "{{ $search }}"</p>
                                        <small>Coba dengan kata kunci lain atau <a href="{{ url('/master/tarif') }}">tampilkan semua data</a></small>
                                    </div>
                                @else
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="mb-2">
                                            <rect x="2" y="4" width="20" height="16" rx="2" ry="2"/>
                                            <line x1="12" y1="4" x2="12" y2="20"/>
                                            <line x1="2" y1="12" x2="22" y2="12"/>
                                        </svg>
                                        <p class="mb-1">Tidak ada data tarif layanan</p>
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
