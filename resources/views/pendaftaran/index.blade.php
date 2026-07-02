@extends('layout.app')

@section('title', 'Pendaftaran')
@section('page-title', 'Pendaftaran')
@section('breadcrumb', 'Antrian Pendaftaran')

@section('content')

{{-- Alert sukses --}}
@if(session('success'))
<div style="margin-bottom:16px;padding:14px 18px;background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.4);border-radius:10px;color:#15803d;display:flex;align-items:center;gap:10px;">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
  {{ session('success') }}
</div>
@endif

{{-- Alert error --}}
@if(session('error'))
<div style="margin-bottom:16px;padding:14px 18px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.4);border-radius:10px;color:#b91c1c;display:flex;align-items:center;gap:10px;">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
  {{ session('error') }}
</div>
@endif

<div class="page-header">

  <div class="page-header-left">
    <h1>Antrian Pendaftaran</h1>
    <p>Kelola antrian dan pendaftaran pasien</p>
  </div>
  <div class="btn-group">
    <a href="{{ url('/pendaftaran/pasien-lama') }}" class="btn btn-outline">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      Pasien Lama
    </a>
    <a href="{{ url('/pendaftaran/pasien-baru') }}" class="btn btn-primary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Pasien Baru
    </a>
  </div>
</div>

<!-- Stats Summary -->
<div class="stat-grid" style="margin-bottom:20px;">
  <div class="stat-card blue" style="padding:16px;">
    <div class="stat-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
    <div class="stat-info"><div class="stat-label">Total Antrian</div><div class="stat-value">28</div></div>
  </div>
  <div class="stat-card green" style="padding:16px;">
    <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><polyline points="20 6 9 17 4 12"/></svg></div>
    <div class="stat-info"><div class="stat-label">Selesai</div><div class="stat-value">14</div></div>
  </div>
  <div class="stat-card orange" style="padding:16px;">
    <div class="stat-icon orange"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
    <div class="stat-info"><div class="stat-label">Menunggu</div><div class="stat-value">11</div></div>
  </div>
  <div class="stat-card accent" style="padding:16px;--accent-color:var(--accent);">
    <div class="stat-icon" style="background:rgba(0,180,216,0.1);color:var(--accent);">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    </div>
    <div class="stat-info"><div class="stat-label">Dalam Layanan</div><div class="stat-value">3</div></div>
  </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
  <form method="GET" action="{{ url()->current() }}" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center; width:100%; margin:0;">
    <div class="input-group search-box" style="max-width:350px; flex:1;">
      <span class="input-group-text">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </span>
      <input type="text" class="form-control" name="search" id="tableSearch" placeholder="Cari nama pasien, nomor rm, NIK, alamat…" value="{{ $search ?? '' }}">
      @if($search ?? '')
        <a href="{{ url()->current() }}" class="btn btn-ghost btn-sm" style="display:flex; align-items:center; padding: 0 8px; border:none; background:transparent;" title="Reset Pencarian">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </a>
      @endif
    </div>

    <div style="display:flex; align-items:center; gap:8px; margin-left:auto;">
      <label class="text-sm text-muted" style="white-space:nowrap; margin-bottom:0;">Tampilkan:</label>
      <select name="per_page" class="form-control" style="width:85px; padding: 6px 10px;" onchange="this.form.submit()">
        <option value="10" {{ ($perPage ?? 20) == 10 ? 'selected' : '' }}>10</option>
        <option value="20" {{ ($perPage ?? 20) == 20 ? 'selected' : '' }}>20</option>
        <option value="50" {{ ($perPage ?? 20) == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ ($perPage ?? 20) == 100 ? 'selected' : '' }}>100</option>
        <option value="200" {{ ($perPage ?? 20) == 200 ? 'selected' : '' }}>200</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">
      Cari
    </button>
  </form>
</div>

<!-- Table -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
  <div class="card-header border-bottom bg-white d-flex align-items-center justify-content-between py-3">
    <h3 class="card-title fw-bold text-dark m-0">Daftar Antrian Hari Ini — <span id="liveDate" class="fw-normal text-muted"></span></h3>
    <button class="btn btn-ghost-secondary btn-sm d-flex align-items-center gap-1" onclick="window.location.reload()">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
      Refresh
    </button>
  </div>
  <div class="table-responsive" style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
    <table class="table table-vcenter card-table table-hover mb-0" style="white-space: nowrap; min-width: 2500px;">
      <thead>
        <tr>
          <th width="40"><input type="checkbox" id="selectAll"></th>
          <th>No. RM</th>
          <th>Nama Pasien</th>
          <th>Data Pendukung</th>
          <th>No. KTP</th>
          <th>JK</th>
          <th>Tempat Lahir</th>
          <th>Tgl. Lahir</th>
          <th>Umur</th>
          <th>Gol. Darah</th>
          <th>Nama Ibu</th>
          <th>Alamat</th>
          <th>No. Tlp</th>
          <th>Agama</th>
          <th>Status Nikah</th>
          <th>Pendidikan</th>
          <th>Pekerjaan</th>
          <th>Keluarga</th>
          <th>Nama Keluarga</th>
          <th>Jaminan / KD PJ</th>
          <th>No. Peserta</th>
          <th>Perusahaan</th>
          <th>Email</th>
          <th>NIP</th>
          <th>KD Kel</th>
          <th>KD Kec</th>
          <th>KD Kab</th>
          <th>KD Prop</th>
          <th>Pekerjaan PJ</th>
          <th>Alamat PJ</th>
          <th>Kelurahan PJ</th>
          <th>Kecamatan PJ</th>
          <th>Kabupaten PJ</th>
          <th>Provinsi PJ</th>
          <th>Suku Bangsa</th>
          <th>Bahasa</th>
          <th>Cacat Fisik</th>
          <th>Tgl. Daftar</th>
          <th width="80" class="text-center sticky-right" style="position: sticky; right: 0; background: #f8fafc; z-index: 10; border-left: 1px solid var(--border);">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pasien as $p)
        <tr data-search-row>
          <td><input type="checkbox" class="row-check"></td>
          <td><span class="fw-bold text-primary">{{ $p->no_rkm_medis }}</span></td>
          <td class="fw-semibold">{{ $p->nm_pasien }}</td>
          <td>
            @if(isset($p->data_pendukung) && $p->data_pendukung)
              <a href="{{ asset('uploads/data_pendukung/' . $p->data_pendukung) }}" target="_blank" class="badge bg-primary text-white" style="text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:4px 8px;border-radius:4px;" onclick="event.stopPropagation()">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Unduh
              </a>
            @else
              <span class="text-muted small">-</span>
            @endif
          </td>
          <td>{{ $p->no_ktp }}</td>
          <td><span class="badge badge-gray">{{ $p->jk }}</span></td>
          <td>{{ $p->tmp_lahir }}</td>
          <td class="text-muted text-sm">{{ $p->tgl_lahir }}</td>
          <td>{{ $p->umur }}</td>
          <td>{{ $p->gol_darah }}</td>
          <td class="text-sm font-italic">{{ $p->nm_ibu }}</td>
          <td class="text-sm" style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $p->alamat }}">{{ $p->alamat }}</td>
          <td>{{ $p->no_tlp }}</td>
          <td>{{ $p->agama }}</td>
          <td>{{ $p->stts_nikah }}</td>
          <td>{{ $p->pnd }}</td>
          <td>{{ $p->pekerjaan }}</td>
          <td>{{ $p->keluarga }}</td>
          <td>{{ $p->namakeluarga }}</td>
          <td>
            <span class="badge {{ $p->kd_pj==='BPJ' ? 'badge-green' : ($p->kd_pj==='ASU' ? 'badge-purple' : 'badge-gray') }}">
              {{ $p->kd_pj }}
            </span>
          </td>
          <td>{{ $p->no_peserta }}</td>
          <td>{{ $p->perusahaan_pasien }}</td>
          <td>{{ $p->email }}</td>
          <td>{{ $p->nip }}</td>
          <td>{{ $p->kd_kel }}</td>
          <td>{{ $p->kd_kec }}</td>
          <td>{{ $p->kd_kab }}</td>
          <td>{{ $p->kd_prop }}</td>
          <td>{{ $p->pekerjaanpj }}</td>
          <td>{{ $p->alamatpj }}</td>
          <td>{{ $p->kelurahanpj }}</td>
          <td>{{ $p->kecamatanpj }}</td>
          <td>{{ $p->kabupatenpj }}</td>
          <td>{{ $p->propinsipj }}</td>
          <td>{{ $p->suku_bangsa }}</td>
          <td>{{ $p->bahasa_pasien }}</td>
          <td>{{ $p->cacat_fisik }}</td>
          <td class="text-sm text-muted">{{ $p->tgl_daftar }}</td>
          <td class="text-center sticky-right" style="position: sticky; right: 0; background: #ffffff; border-left: 1px solid var(--border);">
            <div class="btn-group" style="justify-content:center;">
              <a href="{{ url('/rawat-jalan/daftar?no_rkm_medis=' . $p->no_rkm_medis) }}" class="btn btn-ghost btn-icon btn-sm text-primary" title="Daftar Berobat">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                  <line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
                </svg>
              </a>
              <a href="{{ url('/pendaftaran/edit/'.$p->no_rkm_medis) }}" class="btn btn-ghost btn-icon btn-sm" title="Edit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <form action="{{ url('/pendaftaran/delete/'.$p->no_rkm_medis) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data pasien {{ $p->nm_pasien }}?');" style="display:inline-block; margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-ghost btn-icon btn-sm" title="Hapus" style="color:var(--danger)">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="38" class="text-center text-muted" style="padding: 40px;">Tidak ada data pasien ditemukan.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer bg-light" style="display:flex; align-items:center; justify-content:space-between; padding:16px;">
    <div class="text-muted" style="font-size: 13px;">
      Menampilkan <strong>{{ $pasien->firstItem() ?? 0 }}</strong> - <strong>{{ $pasien->lastItem() ?? 0 }}</strong> 
      dari <strong>{{ number_format($pasien->total()) }}</strong> data pasien
      @if($search ?? '')
        <span class="ms-2 text-primary">• Pencarian: "{{ $search }}"</span>
      @endif
    </div>
    <div class="d-flex justify-content-end">
      {{ $pasien->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
    </div>
  </div>
</div>

@endsection
