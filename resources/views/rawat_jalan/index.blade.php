@extends('layout.app')

@section('title', 'Rawat Jalan')
@section('page-title', 'Rawat Jalan')
@section('breadcrumb', 'Registered Pasien')

@section('content')

<div class="row align-items-center mb-3">
  <div class="col">
    <h2 class="h3 mb-0">Rawat Jalan</h2>
    <p class="text-muted mb-0">Tanggal: <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</strong></p>
  </div>
  <div class="col-auto">
    <a href="{{ url('/rawat-jalan/daftar') }}" class="btn btn-primary">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Tambah Kunjungan
    </a>
  </div>
</div>

{{-- Alerts --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
  <div class="d-flex">
    <div>{{ session('success') }}</div>
  </div>
  <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible" role="alert">
  {{ session('error') }}
  <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

<div class="card">
  <div class="card-header">
    {{-- Date Filter --}}
    <form method="GET" action="{{ url('/rawat-jalan') }}" class="d-flex align-items-center gap-2 flex-wrap me-auto">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control form-control-sm" style="width: 165px;">
      <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
      @if($tanggal !== date('Y-m-d'))
        <a href="{{ url('/rawat-jalan') }}" class="btn btn-sm btn-outline-secondary">Hari Ini</a>
      @endif
    </form>
    {{-- Search --}}
    <div class="input-group input-group-sm" style="width: 220px;">
      <span class="input-group-text">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </span>
      <input type="text" class="form-control" id="tableSearch" placeholder="Cari pasien…">
    </div>
    <span class="text-muted small ms-2">{{ $antrian->count() }} pasien</span>
  </div>

  {{-- Tab Nav --}}
  <div class="card-header" style="border-top: 1px solid var(--tblr-border-color); padding-top: 0; padding-bottom: 0;">
    <ul class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a class="nav-link active" href="#tab-semua" data-bs-toggle="tab">Semua
          <span class="badge bg-secondary ms-1">{{ $antrian->count() }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#tab-menunggu" data-bs-toggle="tab">Menunggu
          <span class="badge bg-warning text-dark ms-1">{{ $antrian->where('stts','Belum')->count() }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#tab-selesai" data-bs-toggle="tab">Selesai
          <span class="badge bg-success ms-1">{{ $antrian->where('stts','Sudah')->count() }}</span>
        </a>
      </li>
    </ul>
  </div>

  <div class="tab-content">
    @foreach(['semua' => null, 'menunggu' => 'Belum', 'selesai' => 'Sudah'] as $tabKey => $statusFilter)
    <div class="tab-pane {{ $tabKey === 'semua' ? 'active show' : '' }}" id="tab-{{ $tabKey }}">
      <div class="table-responsive mt-3">
        <table class="table table-vcenter table-hover card-table">
          <thead>
            <tr>
              <th>No. Rawat</th>
              <th>No. Antri</th>
              <th>No. RM</th>
              <th>Nama Pasien</th>
              <th>Poliklinik</th>
              <th>Dokter</th>
              <th>Jaminan</th>
              <th>Jam Reg</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @php
              $filtered = $statusFilter ? $antrian->where('stts', $statusFilter) : $antrian;
            @endphp
            @forelse($filtered as $r)
            @php
              $stts_color = $r->stts == 'Belum' ? 'warning' : ($r->stts == 'Sudah' ? 'success' : 'secondary');
            @endphp
            <tr data-search-row>
              <td>
                <a href="{{ url('/rawat-jalan/registered/' . urlencode($r->no_rawat)) }}" class="fw-semibold text-primary" style="text-decoration:none;">
                  {{ $r->no_rawat }}
                </a>
              </td>
              <td class="fw-bold fs-5">{{ $r->no_reg }}</td>
              <td class="text-muted">{{ $r->no_rkm_medis }}</td>
              <td>
                <a href="{{ url('/rawat-jalan/registered/' . urlencode($r->no_rawat)) }}" class="fw-semibold text-reset" style="text-decoration:none;">
                  {{ $r->nm_pasien }}
                </a>
                <div class="text-muted small">{{ $r->jk }}</div>
              </td>
              <td>
                <span class="badge bg-azure-lt">{{ $r->nm_poli }}</span>
              </td>
              <td class="text-muted">{{ $r->nm_dokter }}</td>
              <td><span class="badge bg-blue-lt">{{ $r->jaminan }}</span></td>
              <td class="text-muted small">{{ $r->jam_reg }}</td>
              <td><span class="badge bg-{{ $stts_color }}-lt">{{ $r->stts }}</span></td>
              <td>
                <div class="btn-list flex-nowrap">
                  <a href="{{ url('/rawat-jalan/registered/' . urlencode($r->no_rawat)) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                  <a href="{{ url('/rawat-jalan/pemeriksaan/' . urlencode($r->no_rawat)) }}?ref=index" class="btn btn-sm btn-primary">SOAP</a>
                  <a href="{{ url('/rawat-jalan/resep/' . urlencode($r->no_rawat)) }}?ref=index" class="btn btn-sm btn-outline-success">Resep</a>
                  <a href="{{ url('/laboratorium/request/' . urlencode($r->no_rawat)) }}?ref=index" class="btn btn-sm btn-outline-info">Lab</a>
                  <a href="{{ url('/radiologi/request/' . urlencode($r->no_rawat)) }}?ref=index" class="btn btn-sm btn-outline-warning">Rad</a>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="10" class="text-center text-muted py-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2 text-muted" width="40" height="40" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="9" y1="10" x2="9.01" y2="10"/><line x1="15" y1="10" x2="15.01" y2="10"/><path d="M9.5 15.25a3.5 3.5 0 0 1 5 0"/></svg>
                <div>Tidak ada data pasien pada tanggal <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</strong></div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @endforeach
  </div>
</div>

@endsection
