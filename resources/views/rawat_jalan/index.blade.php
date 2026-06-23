@extends('layout.app')

@section('title', 'Rawat Jalan')
@section('page-title', 'Rawat Jalan')
@section('breadcrumb', 'Registered Pasien')

@push('styles')
<style>
/* Date range picker row */
.date-range-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}
.date-range-row .date-label {
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  white-space: nowrap;
}
.date-range-row .form-control {
  font-size: 13px;
}
.date-range-separator {
  color: #94a3b8;
  font-size: 13px;
  font-weight: 500;
}
.date-range-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  background: #eff6ff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
  white-space: nowrap;
}
.shortcut-btns {
  display: flex;
  gap: 4px;
  flex-wrap: wrap;
}
.shortcut-btns .btn-shortcut {
  font-size: 11px;
  padding: 3px 8px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  background: #f8fafc;
  color: #475569;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.15s;
  white-space: nowrap;
}
.shortcut-btns .btn-shortcut:hover,
.shortcut-btns .btn-shortcut.active {
  background: #2563eb;
  color: #fff;
  border-color: #2563eb;
}
</style>
@endpush

@section('content')

<div class="row align-items-center mb-3">
  <div class="col">
    <h2 class="h3 mb-0">Rawat Jalan</h2>
    @if($tgl_dari === $tgl_sampai)
      <p class="text-muted mb-0">Tanggal: <strong>{{ \Carbon\Carbon::parse($tgl_dari)->translatedFormat('d F Y') }}</strong></p>
    @else
      <p class="text-muted mb-0">
        Periode: <strong>{{ \Carbon\Carbon::parse($tgl_dari)->translatedFormat('d F Y') }}</strong>
        &mdash;
        <strong>{{ \Carbon\Carbon::parse($tgl_sampai)->translatedFormat('d F Y') }}</strong>
      </p>
    @endif
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
  <div class="d-flex"><div>{{ session('success') }}</div></div>
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
  <div class="card-header" style="flex-direction:column;align-items:stretch;gap:10px;">

    {{-- Date Range Filter --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
      <form method="GET" action="{{ url('/rawat-jalan') }}" class="date-range-row" id="form-filter">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span class="date-label">Dari</span>
        <input type="date" name="tgl_dari" id="tgl_dari" value="{{ $tgl_dari }}"
               class="form-control form-control-sm" style="width:150px;"
               onchange="autoSubmitIfBothSet()">
        <span class="date-range-separator">s/d</span>
        <span class="date-label">Sampai</span>
        <input type="date" name="tgl_sampai" id="tgl_sampai" value="{{ $tgl_sampai }}"
               class="form-control form-control-sm" style="width:150px;"
               onchange="autoSubmitIfBothSet()">
        <button type="submit" class="btn btn-sm btn-primary">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;"><polyline points="22 2 15 22 11 13 2 9 22 2"/></svg>
          Tampilkan
        </button>

        {{-- Info range aktif --}}
        @if($tgl_dari !== $tgl_sampai || $tgl_dari !== date('Y-m-d'))
          <span class="date-range-badge">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $antrian->count() }} data
          </span>
        @endif
      </form>

      {{-- Shortcut buttons --}}
      <div class="shortcut-btns">
        @php
          $today     = date('Y-m-d');
          $yesterday = date('Y-m-d', strtotime('-1 day'));
          $w_start   = date('Y-m-d', strtotime('monday this week'));
          $w_end     = date('Y-m-d', strtotime('sunday this week'));
          $m_start   = date('Y-m-01');
          $m_end     = date('Y-m-d');
        @endphp
        <a href="{{ url('/rawat-jalan') }}?tgl_dari={{ $today }}&tgl_sampai={{ $today }}"
           class="btn-shortcut {{ ($tgl_dari === $today && $tgl_sampai === $today) ? 'active' : '' }}">
          Hari Ini
        </a>
        <a href="{{ url('/rawat-jalan') }}?tgl_dari={{ $yesterday }}&tgl_sampai={{ $yesterday }}"
           class="btn-shortcut {{ ($tgl_dari === $yesterday && $tgl_sampai === $yesterday) ? 'active' : '' }}">
          Kemarin
        </a>
        <a href="{{ url('/rawat-jalan') }}?tgl_dari={{ $w_start }}&tgl_sampai={{ $w_end }}"
           class="btn-shortcut {{ ($tgl_dari === $w_start && $tgl_sampai === $w_end) ? 'active' : '' }}">
          Minggu Ini
        </a>
        <a href="{{ url('/rawat-jalan') }}?tgl_dari={{ $m_start }}&tgl_sampai={{ $m_end }}"
           class="btn-shortcut {{ ($tgl_dari === $m_start && $tgl_sampai === $m_end) ? 'active' : '' }}">
          Bulan Ini
        </a>
      </div>
    </div>

    {{-- Search + count & Per Page Filter --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
      <div style="display:flex;align-items:center;gap:10px;">
        <div class="input-group input-group-sm" style="width:240px;">
          <span class="input-group-text">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          </span>
          <input type="text" class="form-control" id="tableSearch" placeholder="Cari nama/no. rawat…">
        </div>
        <span class="text-muted small">
          <strong>{{ number_format($totalKunjungan) }}</strong> kunjungan
          @if($tgl_dari !== $tgl_sampai)
            <span class="ms-1 text-primary">({{ $tgl_dari }} s/d {{ $tgl_sampai }})</span>
          @endif
        </span>
      </div>

      <div>
        <form method="GET" action="{{ url('/rawat-jalan') }}" class="d-flex align-items-center gap-2">
          <input type="hidden" name="tgl_dari" value="{{ $tgl_dari }}">
          <input type="hidden" name="tgl_sampai" value="{{ $tgl_sampai }}">
          <label class="form-label mb-0 text-muted" style="font-size: 13px; white-space: nowrap;">Tampilkan:</label>
          <select name="per_page" class="form-select form-select-sm" style="width: auto; min-width: 80px;" onchange="this.form.submit()">
              <option value="10" {{ ($perPage ?? 20) == 10 ? 'selected' : '' }}>10</option>
              <option value="25" {{ ($perPage ?? 20) == 25 ? 'selected' : '' }}>25</option>
              <option value="50" {{ ($perPage ?? 20) == 50 ? 'selected' : '' }}>50</option>
              <option value="100" {{ ($perPage ?? 20) == 100 ? 'selected' : '' }}>100</option>
          </select>
        </form>
      </div>
    </div>
  </div>

  {{-- Tab Nav --}}
  <div class="card-header" style="border-top: 1px solid var(--tblr-border-color); padding-top: 0; padding-bottom: 0;">
    <ul class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a class="nav-link active" href="#tab-semua" data-bs-toggle="tab">Semua
          <span class="badge bg-secondary ms-1">{{ $totalKunjungan }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#tab-menunggu" data-bs-toggle="tab">Menunggu
          <span class="badge bg-warning text-dark ms-1">{{ $totalBelum }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#tab-selesai" data-bs-toggle="tab">Selesai
          <span class="badge bg-success ms-1">{{ $totalSudah }}</span>
        </a>
      </li>
    </ul>
  </div>

  <div class="tab-content">
    @foreach(['semua' => null, 'menunggu' => 'Belum', 'selesai' => 'Sudah'] as $tabKey => $statusFilter)
    <div class="tab-pane {{ $tabKey === 'semua' ? 'active show' : '' }}" id="tab-{{ $tabKey }}">
      <div class="table-wrapper mt-3">
        <table class="table table-vcenter table-hover card-table table-nowrap">
          <thead>
            <tr>
              <th>No. Rawat</th>
              <th>No. Antri</th>
              <th>No. RM</th>
              <th>Nama Pasien</th>
              <th>Poliklinik</th>
              <th>Dokter</th>
              <th>Jaminan</th>
              @if($tgl_dari !== $tgl_sampai)
              <th>Tgl. Registrasi</th>
              @endif
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
              <td><span class="badge bg-blue-lt" style="max-width:160px;white-space:normal;text-align:left;line-height:1.3;">{{ \Str::limit($r->jaminan, 30) }}</span></td>
              @if($tgl_dari !== $tgl_sampai)
              <td class="text-muted small">
                {{ \Carbon\Carbon::parse($r->tgl_registrasi)->translatedFormat('d M Y') }}
              </td>
              @endif
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
              <td colspan="11" class="text-center text-muted py-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2 text-muted" width="40" height="40" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="9" y1="10" x2="9.01" y2="10"/><line x1="15" y1="10" x2="15.01" y2="10"/><path d="M9.5 15.25a3.5 3.5 0 0 1 5 0"/></svg>
                @if($tgl_dari === $tgl_sampai)
                  <div>Tidak ada data kunjungan pada <strong>{{ \Carbon\Carbon::parse($tgl_dari)->translatedFormat('d F Y') }}</strong></div>
                @else
                  <div>Tidak ada data kunjungan periode <strong>{{ \Carbon\Carbon::parse($tgl_dari)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($tgl_sampai)->translatedFormat('d F Y') }}</strong></div>
                @endif
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Footer dengan pagination dan info -->
      <div class="card-footer bg-light mt-3" style="border-top: 1px solid var(--tblr-border-color); border-radius: 0 0 var(--tblr-card-border-radius) var(--tblr-card-border-radius); padding: 12px 20px;">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="text-muted" style="font-size: 13px;">
              Menampilkan <strong>{{ $antrian->firstItem() ?? 0 }}</strong> - <strong>{{ $antrian->lastItem() ?? 0 }}</strong> 
              dari <strong>{{ number_format($antrian->total()) }}</strong> data
            </div>
          </div>
          <div class="col-md-6 d-flex justify-content-end">
            {{ $antrian->links('pagination::bootstrap-4') }}
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</div>

@push('scripts')
<script>
// Auto-submit saat tanggal sampai diubah (jika dari sudah terisi)
function autoSubmitIfBothSet() {
  const dari    = document.getElementById('tgl_dari').value;
  const sampai  = document.getElementById('tgl_sampai').value;
  if (dari && sampai && dari <= sampai) {
    document.getElementById('form-filter').submit();
  }
}

// Validasi: dari tidak boleh lebih dari sampai
document.getElementById('tgl_dari').addEventListener('change', function() {
  const sampai = document.getElementById('tgl_sampai');
  if (this.value > sampai.value) {
    sampai.value = this.value;
  }
  autoSubmitIfBothSet();
});

document.getElementById('tgl_sampai').addEventListener('change', function() {
  const dari = document.getElementById('tgl_dari');
  if (this.value < dari.value) {
    dari.value = this.value;
  }
  autoSubmitIfBothSet();
});
</script>
@endpush

@endsection
