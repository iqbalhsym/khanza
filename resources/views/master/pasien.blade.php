@extends('layout.app')

@section('title', 'Data Pasien - Master Data')
@section('page-title', 'Data Pasien')
@section('breadcrumb', 'Master Data')

@push('styles')
<style>
/* Modern Modal Overlay Styles */
.modal-overlay {
  display: none !important;
  position: fixed !important;
  inset: 0 !important;
  background: rgba(15, 23, 42, 0.6) !important;
  backdrop-filter: blur(4px) !important;
  z-index: 9999 !important;
  justify-content: center !important;
  align-items: center !important;
  padding: 20px !important;
  opacity: 0 !important;
  visibility: hidden !important;
  transition: opacity 0.2s ease, visibility 0.2s ease !important;
}

.modal-overlay.open {
  display: flex !important;
  opacity: 1 !important;
  visibility: visible !important;
}

/* Modal Content Card */
.modal-overlay .modal-card {
  background: #ffffff !important;
  border-radius: 16px !important;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
  border: 1px solid #e2e8f0 !important;
  width: 100% !important;
  max-width: 800px !important;
  max-height: 85vh !important;
  display: flex !important;
  flex-direction: column !important;
  overflow: hidden !important;
  transform: scale(0.95) !important;
  transition: transform 0.2s ease !important;
  margin: auto !important;
  position: relative !important;
}

.modal-overlay.open .modal-card {
  transform: scale(1) !important;
}

/* Modal Header styling */
.modal-overlay .modal-header {
  background: #206bc4 !important;
  color: #ffffff !important;
  padding: 16px 24px !important;
  border-bottom: 1px solid #1e5bb0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
}

/* Modal Body */
.modal-overlay .modal-body {
  padding: 24px !important;
  background: #ffffff !important;
  overflow-y: auto !important;
}

/* Modern Tab buttons */
.modal-overlay .tabs {
  margin-bottom: 20px !important;
  border-bottom: 2px solid #f1f5f9 !important;
  display: flex !important;
  gap: 16px !important;
  padding-bottom: 0 !important;
}

.modal-overlay .tab-btn {
  padding: 10px 4px !important;
  border: none !important;
  background: transparent !important;
  font-size: 14px !important;
  font-weight: 600 !important;
  color: #64748b !important;
  cursor: pointer !important;
  border-bottom: 2px solid transparent !important;
  transition: all 0.2s ease !important;
  margin-bottom: -2px !important;
}

.modal-overlay .tab-btn:hover {
  color: #206bc4 !important;
}

.modal-overlay .tab-btn.active {
  color: #206bc4 !important;
  border-bottom: 2px solid #206bc4 !important;
}

/* Tab content logic */
.modal-overlay .tab-content {
  display: none !important;
}

.modal-overlay .tab-content.active {
  display: block !important;
}
</style>
@endpush

@section('content')

<div class="page-header">
  <div class="page-header-left">
    <h1>Data Pasien</h1>
    <p>Kelola data seluruh pasien yang terdaftar di sistem</p>
  </div>
  <div class="page-header-right">
    <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 13px;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
        <circle cx="12" cy="7" r="4"></circle>
      </svg>
      Total: {{ $data->total() }} pasien
    </div>
  </div>
</div>

    <div class="card">
        <!-- Search & Filter Header -->
        <div class="card-header bg-light">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <form method="GET" action="{{ url('/master/pasien') }}" class="d-flex align-items-center gap-2">
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
                                   placeholder="Cari No. RM, nama, NIK, atau alamat..." 
                                   value="{{ $search ?? '' }}">
                            @if($search ?? '')
                            <a href="{{ url('/master/pasien') }}" class="btn btn-outline-secondary" title="Hapus pencarian">
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
                    <form method="GET" action="{{ url('/master/pasien') }}" class="d-flex align-items-center justify-content-end gap-2">
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
                <a href="{{ url('/master/pasien') }}" class="btn btn-sm btn-outline-primary">
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
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>Data Pendukung</th>
                        <th>NIK</th>
                        <th>Jenis Kelamin</th>
                        <th>Tgl. Lahir</th>
                        <th>Umur</th>
                        <th>Alamat</th>
                        <th>No. Telepon</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if($data->count() > 0)
                        @foreach($data as $d)
                        <tr class="clickable-row" style="cursor: pointer;"
                            data-no-rm="{{ $d->no_rkm_medis }}"
                            data-nama="{{ $d->nm_pasien }}"
                            data-ktp="{{ $d->no_ktp }}"
                            data-jk="{{ $d->jk }}"
                            data-tmp-lahir="{{ $d->tmp_lahir }}"
                            data-tgl-lahir="{{ $d->tgl_lahir }}"
                            data-umur="{{ $d->umur }}"
                            data-gol-darah="{{ $d->gol_darah }}"
                            data-ibu="{{ $d->nm_ibu }}"
                            data-alamat="{{ $d->alamat }}"
                            data-telp="{{ $d->no_tlp }}"
                            data-agama="{{ $d->agama }}"
                            data-nikah="{{ $d->stts_nikah }}"
                            data-pnd="{{ $d->pnd }}"
                            data-pekerjaan="{{ $d->pekerjaan }}"
                            data-keluarga="{{ $d->keluarga }}"
                            data-nm-keluarga="{{ $d->namakeluarga }}"
                            data-jaminan="{{ $d->kd_pj }}"
                            data-no-peserta="{{ $d->no_peserta }}"
                            data-perusahaan="{{ $d->perusahaan_pasien }}"
                            data-email="{{ $d->email }}"
                            data-tgl-daftar="{{ $d->tgl_daftar }}"
                            data-suku="{{ $d->suku_bangsa }}"
                            data-bahasa="{{ $d->bahasa_pasien }}"
                            data-cacat="{{ $d->cacat_fisik }}"
                            data-nip="{{ $d->nip }}"
                            data-pekerjaanpj="{{ $d->pekerjaanpj }}"
                            data-alamatpj="{{ $d->alamatpj }}"
                            data-data-pendukung="{{ $d->data_pendukung ?? '' }}">
                            <td><strong class="text-primary">{{ $d->no_rkm_medis }}</strong></td>
                            <td style="font-weight:600;">{{ $d->nm_pasien }}</td>
                            <td>
                                @if($d->data_pendukung)
                                    <a href="{{ asset('uploads/data_pendukung/' . $d->data_pendukung) }}" target="_blank" class="badge bg-primary text-white" style="text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:4px 8px;border-radius:4px;" onclick="event.stopPropagation()">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                        Unduh
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $d->no_ktp ?? '-' }}</td>
                            <td>
                                @if($d->jk == 'L')
                                    <span class="badge badge-blue">Laki-laki</span>
                                @else
                                    <span class="badge badge-orange">Perempuan</span>
                                @endif
                            </td>
                            <td>{{ $d->tgl_lahir }}</td>
                            <td>{{ $d->umur }}</td>
                            <td class="text-sm">{{ Str::limit($d->alamat, 30) }}</td>
                            <td class="text-muted">{{ $d->no_tlp ?? '-' }}</td>
                            <td>
                                <span class="badge badge-success">Terdaftar</span>
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
                                        <p class="mb-1">Tidak ditemukan data untuk "{{ $search }}"</p>
                                        <small>Coba dengan kata kunci lain atau <a href="{{ url('/master/pasien') }}">tampilkan semua data</a></small>
                                    </div>
                                @else
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="mb-2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        <p class="mb-1">Tidak ada data pasien</p>
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
</div> <!-- Close card -->

<!-- Modal Detail Pasien -->
<div class="modal-overlay" id="modalDetailPasien">
  <div class="modal-card" style="max-width: 800px; width: 90%; height: auto; max-height: 85vh; border-radius: 16px; display: flex; flex-direction: column; margin: auto; position: relative;">
    <div class="modal-header" style="background: #206bc4; color: #fff; padding: 16px 24px;">
      <div>
        <h2 style="margin: 0; font-size: 18px; font-weight: 700; color: #fff;">Detail Informasi Pasien</h2>
        <p style="margin: 4px 0 0 0; font-size: 12px; opacity: 0.85; color: #fff;" id="modal-subtitle-rm"></p>
      </div>
      <button class="modal-close" style="color: #fff; background: transparent; border: none; font-size: 24px; cursor: pointer; transition: all 0.2s;" onclick="document.getElementById('modalDetailPasien').classList.remove('open')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" style="color:#fff;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body" style="padding: 24px; overflow-y: auto;" data-tabs>
      <!-- Tabs Nav -->
      <div class="tabs" style="margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; display: flex; gap: 8px;">
        <button class="tab-btn active" data-tab="detail-demografi">Demografi</button>
        <button class="tab-btn" data-tab="detail-kontak">Kontak & Penanggung Jawab</button>
        <button class="tab-btn" data-tab="detail-sistem">Suku & Sistem</button>
      </div>

      <!-- Tab 1: Demografi -->
      <div class="tab-content active" id="tab-detail-demografi">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">No. Rekam Medis</label>
            <div id="m-no-rm" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">NIK / No. KTP</label>
            <div id="m-ktp" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div style="grid-column: span 2;">
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Nama Lengkap Pasien</label>
            <div id="m-nama" style="font-size: 15px; font-weight: 700; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Jenis Kelamin</label>
            <div id="m-jk" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Umur</label>
            <div id="m-umur" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Tempat Lahir</label>
            <div id="m-tmp-lahir" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Tanggal Lahir</label>
            <div id="m-tgl-lahir" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Agama</label>
            <div id="m-agama" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Golongan Darah</label>
            <div id="m-gol-darah" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Status Pernikahan</label>
            <div id="m-nikah" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Pendidikan</label>
            <div id="m-pnd" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Pekerjaan</label>
            <div id="m-pekerjaan" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Cacat Fisik</label>
            <div id="m-cacat" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Data Pendukung</label>
            <div id="m-data-pendukung" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
        </div>
      </div>

      <!-- Tab 2: Kontak & PJ -->
      <div class="tab-content" id="tab-detail-kontak">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
          <div style="grid-column: span 2;">
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Alamat Lengkap</label>
            <div id="m-alamat" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0; line-height: 1.5;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">No. Telepon / HP</label>
            <div id="m-telp" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Email</label>
            <div id="m-email" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Nama Ibu Kandung</label>
            <div id="m-ibu" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Hubungan Keluarga PJ</label>
            <div id="m-keluarga" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Nama Penanggung Jawab</label>
            <div id="m-nm-keluarga" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Pekerjaan PJ</label>
            <div id="m-pekerjaanpj" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div style="grid-column: span 2;">
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Alamat Penanggung Jawab</label>
            <div id="m-alamatpj" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0; line-height: 1.5;">-</div>
          </div>
        </div>
      </div>

      <!-- Tab 3: Suku & Sistem -->
      <div class="tab-content" id="tab-detail-sistem">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Jaminan / Jenis Bayar</label>
            <div id="m-jaminan" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">No. Kartu Jaminan</label>
            <div id="m-no-peserta" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Instansi / Perusahaan</label>
            <div id="m-perusahaan" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Suku Bangsa</label>
            <div id="m-suku" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Bahasa</label>
            <div id="m-bahasa" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">Tanggal Daftar</label>
            <div id="m-tgl-daftar" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
          <div>
            <label style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 4px;">NIP / No. Pegawai</label>
            <div id="m-nip" style="font-size: 14px; font-weight: 600; color: #0f172a; padding: 6px 0;">-</div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: flex-end;">
      <button type="button" class="btn btn-primary" onclick="document.getElementById('modalDetailPasien').classList.remove('open')">Tutup</button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const clickableRows = document.querySelectorAll('.clickable-row');
  clickableRows.forEach(row => {
    row.addEventListener('click', function(e) {
      // Exclude clicks on interactive elements (links, buttons, checkboxes, etc)
      if (e.target.closest('a, button, form, input, label, select, textarea')) {
        return;
      }
      
      const modal = document.getElementById('modalDetailPasien');
      if (!modal) return;
      
      // Populate fields
      document.getElementById('modal-subtitle-rm').textContent = 'No. Rekam Medis: ' + (this.dataset.noRm || '-');
      document.getElementById('m-no-rm').textContent = this.dataset.noRm || '-';
      document.getElementById('m-ktp').textContent = this.dataset.ktp || '-';
      document.getElementById('m-nama').textContent = this.dataset.nama || '-';
      
      let jkText = '-';
      if (this.dataset.jk === 'L') jkText = 'Laki-laki';
      else if (this.dataset.jk === 'P') jkText = 'Perempuan';
      else if (this.dataset.jk) jkText = this.dataset.jk;
      
      document.getElementById('m-jk').textContent = jkText;
      document.getElementById('m-umur').textContent = this.dataset.umur || '-';
      document.getElementById('m-tmp-lahir').textContent = this.dataset.tmpLahir || '-';
      document.getElementById('m-tgl-lahir').textContent = this.dataset.tglLahir || '-';
      document.getElementById('m-agama').textContent = this.dataset.agama || '-';
      document.getElementById('m-gol-darah').textContent = this.dataset.golDarah || '-';
      document.getElementById('m-nikah').textContent = this.dataset.nikah || '-';
      document.getElementById('m-pnd').textContent = this.dataset.pnd || '-';
      document.getElementById('m-pekerjaan').textContent = this.dataset.pekerjaan || '-';
      document.getElementById('m-cacat').textContent = this.dataset.cacat || '-';
      
      const mDataPendukung = document.getElementById('m-data-pendukung');
      if (mDataPendukung) {
        if (this.dataset.dataPendukung) {
          mDataPendukung.innerHTML = `<a href="/uploads/data_pendukung/${this.dataset.dataPendukung}" target="_blank" style="color: #206bc4; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Unduh Dokumen
          </a>`;
        } else {
          mDataPendukung.textContent = '-';
        }
      }
      
      document.getElementById('m-alamat').textContent = this.dataset.alamat || '-';
      document.getElementById('m-telp').textContent = this.dataset.telp || '-';
      document.getElementById('m-email').textContent = this.dataset.email || '-';
      document.getElementById('m-ibu').textContent = this.dataset.ibu || '-';
      document.getElementById('m-keluarga').textContent = this.dataset.keluarga || '-';
      document.getElementById('m-nm-keluarga').textContent = this.dataset.nmKeluarga || '-';
      document.getElementById('m-pekerjaanpj').textContent = this.dataset.pekerjaanpj || '-';
      document.getElementById('m-alamatpj').textContent = this.dataset.alamatpj || '-';
      
      document.getElementById('m-jaminan').textContent = this.dataset.jaminan || '-';
      document.getElementById('m-no-peserta').textContent = this.dataset.noPeserta || '-';
      document.getElementById('m-perusahaan').textContent = this.dataset.perusahaan || '-';
      document.getElementById('m-suku').textContent = this.dataset.suku || '-';
      document.getElementById('m-bahasa').textContent = this.dataset.bahasa || '-';
      document.getElementById('m-tgl-daftar').textContent = this.dataset.tglDaftar || '-';
      document.getElementById('m-nip').textContent = this.dataset.nip || '-';
      
      // Reset active tab to Demografi
      modal.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      modal.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
      modal.querySelector('[data-tab="detail-demografi"]').classList.add('active');
      modal.querySelector('#tab-detail-demografi').classList.add('active');
      
      // Open Modal
      modal.classList.add('open');
    });
  });

  // Handle tab switching inside detail modal
  const modal = document.getElementById('modalDetailPasien');
  if (modal) {
    const tabButtons = modal.querySelectorAll('.tab-btn');
    tabButtons.forEach(btn => {
      btn.addEventListener('click', function() {
        tabButtons.forEach(b => b.classList.remove('active'));
        modal.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        this.classList.add('active');
        const targetTab = this.getAttribute('data-tab');
        const targetContent = modal.querySelector('#tab-' + targetTab);
        if (targetContent) {
          targetContent.classList.add('active');
        }
      });
    });
  }
});
</script>
@endsection