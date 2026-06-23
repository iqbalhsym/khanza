@extends('layout.app')

@section('title', 'Rawat Inap')
@section('page-title', 'Rawat Inap')
@section('breadcrumb', 'Pasien Dirawat')

@section('content')

<div class="page-header">
  <div class="page-header-left">
    <h1>Rawat Inap</h1>
    <p>Manajemen pasien yang sedang dirawat</p>
  </div>
  <button class="btn btn-primary btn-sm">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Admisi Pasien Baru
  </button>
</div>

<!-- Stats Kamar -->
<div class="stat-grid" style="grid-template-columns:repeat(5,1fr);margin-bottom:20px;">
  @php $bedStats = [
    ['label'=>'Total TT','val'=>90,'icon'=>'orange','color'=>'warning'],
    ['label'=>'Terisi','val'=>53,'icon'=>'red','color'=>'danger'],
    ['label'=>'Kosong','val'=>37,'icon'=>'green','color'=>'success'],
    ['label'=>'Reservasi','val'=>5,'icon'=>'purple','color'=>'info'],
    ['label'=>'Perbaikan','val'=>3,'icon'=>'blue','color'=>'primary'],
  ]; @endphp
  @foreach($bedStats as $bs)
  <div class="stat-card {{ $bs['icon'] }}" style="padding:14px;">
    <div class="stat-info" style="text-align:center;width:100%;">
      <div class="stat-label" style="text-align:center;">{{ $bs['label'] }}</div>
      <div class="stat-value" style="text-align:center;">{{ $bs['val'] }}</div>
    </div>
  </div>
  @endforeach
</div>

<!-- Tabs -->
<div class="tabs mb-20">
    <a href="{{ url('/rawat-inap') }}" class="tab-btn {{ Request::is('rawat-inap') ? 'active' : '' }}">
        Pasien Dirawat
    </a>
    <a href="{{ url('/rawat-inap/kamar') }}" class="tab-btn {{ Request::is('rawat-inap/kamar') ? 'active' : '' }}">
        Peta Kamar
    </a>
</div>

{{-- Alerts --}}
@if(session('success'))
<div style="margin-bottom:16px;padding:12px 18px;background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.4);border-radius:10px;color:#15803d;">
    {{ session('success') }}
</div>
@endif

<div id="tab-pasien" class="tab-content active">
    <div class="filter-bar">
        <form action="{{ url('/rawat-inap') }}" method="GET" class="input-group search-box">
            <span class="input-group-text"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input type="text" name="search" class="form-control" placeholder="Cari nama / no. kamar…" value="{{ request('search') }}">
        </form>
    </div>
    <div class="card">
        <div class="table-wrapper">
            <table class="table-nowrap">
                <thead>
                    <tr>
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>Kamar/TT</th>
                        <th>Bangsal</th>
                        <th>Dokter DPJP</th>
                        <th>Tgl Masuk</th>
                        <th>Hari</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $r)
                    @php
                        $masuk = \Carbon\Carbon::parse($r->tgl_masuk);
                        $los = $masuk->diffInDays(\Carbon\Carbon::now()) ?: 1;
                    @endphp
                    <tr data-search-row>
                        <td class="fw-semibold text-primary">{{ $r->no_rkm_medis }}</td>
                        <td>
                            <div class="fw-semibold">{{ $r->nm_pasien }}</div>
                            <div class="text-xs text-muted">{{ $r->no_rawat }}</div>
                        </td>
                        <td><span class="badge badge-blue">{{ $r->kd_kamar }}</span></td>
                        <td><span class="badge badge-gray">{{ $r->nm_bangsal }}</span></td>
                        <td class="text-sm">{{ $r->dpjp }}</td>
                        <td class="text-muted text-sm">{{ $r->tgl_masuk }}</td>
                        <td><span class="badge badge-orange">{{ $los }} Hari</span></td>
                        <td>
                            <div class="btn-group" style="gap:4px;">
                                <a href="{{ url('/rawat-jalan/pemeriksaan/' . urlencode($r->no_rawat)) }}" class="btn btn-primary btn-sm" title="Pemeriksaan SOAP">SOAP</a>
                                <a href="{{ url('/laboratorium/request/' . urlencode($r->no_rawat)) }}" class="btn btn-blue btn-sm" title="Rujuk Lab">Lab</a>
                                <a href="{{ url('/radiologi/request/' . urlencode($r->no_rawat)) }}" class="btn btn-orange btn-sm" title="Rujuk Rad">Rad</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-40 text-muted">Belum ada pasien yang sedang dirawat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($data->hasPages())
        <div class="card-footer" style="display:flex; justify-content:center; padding:20px;">
            {{ $data->links() }}
        </div>
        @endif
    </div>
</div>

  <!-- Tab Peta Kamar -->
  <div id="tab-kamar" class="tab-content">
    @php $bangsal2 = ['VIP','Kelas I','Kelas II','Kelas III','ICU']; @endphp
    @foreach($bangsal2 as $b)
    <div class="card mb-16">
      <div class="card-header">
        <h3>Bangsal {{ $b }}</h3>
        <div style="display:flex;gap:12px;font-size:11px;align-items:center;">
          <span style="display:flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;background:#34D399;border-radius:2px;display:inline-block;"></span>Kosong</span>
          <span style="display:flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;background:#FCA5A5;border-radius:2px;display:inline-block;"></span>Terisi</span>
          <span style="display:flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;background:#FCD34D;border-radius:2px;display:inline-block;"></span>Reservasi</span>
        </div>
      </div>
      <div class="card-body">
        <div class="bed-grid">
          @for($i=1; $i<=10; $i++)
          @php
            $states = ['available','occupied','occupied','available','reserved','occupied','available','available','occupied','available'];
            $state = $states[($i-1) % count($states)];
            $labels = ['Kosong','Terisi','Terisi','Kosong','Reservasi','Terisi','Kosong','Kosong','Terisi','Kosong'];
            $label = $labels[($i-1) % count($labels)];
          @endphp
          <div class="bed-card {{ $state }}">
            <div class="bed-icon">🛏️</div>
            <div class="bed-num">{{ str_pad($i, 3, '0', STR_PAD_LEFT) }}</div>
            <div class="bed-class text-xs" style="margin-top:3px;">
              <span class="badge @if($state==='available') badge-green @elseif($state==='occupied') badge-red @else badge-orange @endif" style="font-size:10px;padding:2px 6px;">{{ $label }}</span>
            </div>
          </div>
          @endfor
        </div>
      </div>
    </div>
    @endforeach
  </div>
</div>

@endsection
